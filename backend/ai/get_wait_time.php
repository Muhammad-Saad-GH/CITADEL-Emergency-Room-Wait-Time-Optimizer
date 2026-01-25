<?php
function getEstimatedWaitTime(array $queueSeverities, int $patientSeverity): int
{
    $queueArg = implode(",", $queueSeverities);

    $python = ' "C:\Users\samee\AppData\Local\Programs\Python\Python313\python.exe" '; // WAMP / Windows

    $script = escapeshellarg(__DIR__ . "/wait_time_predictor.py");
    $queue  = escapeshellarg($queueArg);
    $sev    = escapeshellarg((string)$patientSeverity);

    $cmd = "$python $script $queue $sev";

    $output = shell_exec($cmd);

    // Absolute safety: only accept numeric output
    if ($output === null) {
        return 0;
    }

    $output = trim($output);

    if (!is_numeric($output)) {
        return 0;
    }

    return (int)$output;
}
