<?php
// backend/admin/run_ai_batch.php

// 1. SETUP
require_once(__DIR__ . "/../db.php");
// Increase time limit (AI takes time, and we are sleeping 4s per record)
set_time_limit(600); 

echo "<body style='font-family: sans-serif; background: #111; color: #ddd; padding: 20px;'>";
echo "<h1>🚀 Starting AI Backfill (Safety Mode)</h1>";
echo "<p><em>Note: We are pausing 4 seconds between records to respect Google's Free Tier limits.</em></p>";
echo "<div style='background: #222; padding: 15px; border-radius: 8px; font-family: monospace;'>";

// 2. CONFIGURATION
$scriptPath = __DIR__ . "/../ai/triage_processor.py"; 
require_once(__DIR__ . "/../../config/config.php"); 
global $PYTHON_PATH;
$pythonExec = isset($PYTHON_PATH) ? $PYTHON_PATH : 'python';

// Helper to find JSON in noisy output
function extractJson($string) {
    $start = strpos($string, '{');
    $end = strrpos($string, '}');
    if ($start !== false && $end !== false) {
        return json_decode(substr($string, $start, $end - $start + 1), true);
    }
    return null;
}

// 3. FETCH BROKEN RECORDS
$sql = "SELECT Checkin_ID, Notes FROM Checkin 
        WHERE AI_reasoning IS NULL 
           OR AI_reasoning LIKE '%AI Retry%' 
           OR AI_reasoning LIKE '%Manual Review%'
           OR AI_reasoning LIKE '%AI Error%'";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<h3 style='color: #4ade80'>✅ All caught up! No records need updating.</h3>";
    die();
}

echo "Found <strong>" . $result->num_rows . "</strong> records to process.<br><br>";

// 4. LOOP
$count = 1;
while ($row = $result->fetch_assoc()) {
    $id = $row['Checkin_ID'];
    $notes = $row['Notes'];
    
    echo "[$count] Processing ID #$id... ";
    flush(); 

    if ($notes === 'None' || empty($notes)) {
        $severity = 5;
        $reasoning = "No symptoms provided.";
        echo "<span style='color: #888'>(Skipped - Empty)</span><br>";
    } else {
        $safe_notes = escapeshellarg($notes);
        
        // Command construction
        $command = "\"$pythonExec\" -W ignore \"$scriptPath\" $safe_notes 2>&1";
        $output = shell_exec($command);
        
        $ai_result = extractJson($output);

        if ($ai_result && isset($ai_result['severity_score'])) {
            $severity = (int)$ai_result['severity_score'];
            $reasoning = $conn->real_escape_string($ai_result['medical_reasoning']);
            echo "<span style='color: #4ade80'>SUCCESS</span> (Sev: $severity)<br>";
            
            // UPDATE DB
            $updateSql = "UPDATE Checkin SET Severity = $severity, AI_reasoning = '$reasoning' WHERE Checkin_ID = $id";
            $conn->query($updateSql);

        } else {
            // FAILURE DEBUGGING
            echo "<span style='color: #f87171'>FAILED</span><br>";
            echo "<div style='border-left: 3px solid red; padding-left: 10px; margin: 5px 0; font-size: 0.9em; color: #fca5a5'>";
            echo "<strong>Output:</strong> " . htmlspecialchars(substr($output, 0, 150)) . "...<br>";
            echo "<strong>Try this in Terminal:</strong><br>$command";
            echo "</div><br>";
        }
    }
    
    // CRITICAL: Sleep 4 seconds to avoid 429/400 Rate Limit Errors
    if ($count < $result->num_rows) {
        echo "<span style='color: #555'>...sleeping 4s...</span><br>";
        flush();
        sleep(4); 
    }
    $count++;
}

echo "<br><h3 style='color: #4ade80'>✨ Batch Complete!</h3>";
echo "</div></body>";
$conn->close();
?>