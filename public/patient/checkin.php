<?php
require_once(__DIR__ . "/../../config/session.php");
requireRole("patient");

require_once(__DIR__ . "/../../backend/db.php");

// Fetch hospital list
$hospitals = [];
$sql = "SELECT Hospital_ID, Name, Location FROM Hospital ORDER BY Name";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Check-in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col font-sans">

    <header class="flex justify-between items-center p-6 bg-black/40 border-b border-purple-900/50 backdrop-blur-md sticky top-0 z-50">
        <div>
            <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-purple-400">Create New Check-In</h1>
            <div class="text-sm text-gray-400 mt-1">
                Logged in as: <strong class="text-white"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Patient'); ?></strong>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="home.php" class="px-4 py-2 rounded-lg border border-gray-600 hover:bg-gray-800 transition text-sm">Dashboard</a>
            <a href="../../backend/auth/logout.php" class="px-4 py-2 rounded-lg border border-red-900/50 text-red-400 hover:bg-red-900/20 transition text-sm">Logout</a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-6">
        
        <div class="w-full max-w-lg bg-gray-900/80 p-8 rounded-2xl border border-purple-500/20 shadow-2xl backdrop-blur-xl relative overflow-hidden">
            
            <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
                <div class="mb-6 p-4 bg-green-900/30 border border-green-500/50 rounded-xl text-green-300 text-center text-sm font-bold">
                    ✅ Check-in created successfully!
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="mb-6 p-4 bg-red-900/30 border border-red-500/50 rounded-xl text-red-300 text-center text-sm font-bold">
                    ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="../../backend/patient/create_checkin.php" method="post" class="flex flex-col gap-6">
                
                <div class="grid grid-cols-2 gap-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-purple-300 uppercase tracking-wider ml-1">
                            Age <span class="text-gray-500 normal-case">(Optional)</span>
                        </label>
                        <input type="number" name="age" min="1" max="120" 
                            class="w-full p-3 bg-black/50 border border-purple-500/30 rounded-xl text-white placeholder-gray-600 focus:border-purple-400 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all box-border"
                            placeholder="25">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-purple-300 uppercase tracking-wider ml-1">
                            Sex <span class="text-gray-500 normal-case">(Optional)</span>
                        </label>
                        <select name="sex" 
                                class="w-full p-3 bg-black/50 border border-purple-500/30 rounded-xl text-white appearance-none focus:border-purple-400 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all cursor-pointer box-border"
                                style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23A855F7%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.7em;">
                            <option value="Unknown" class="text-gray-500">Select...</option>
                            <option value="Male" class="text-white">Male</option>
                            <option value="Female" class="text-white">Female</option>
                            <option value="Other" class="text-white">Other</option>
                        </select>
                    </div>
                </div>

                <div class="p-5 bg-purple-900/10 border border-purple-500/20 rounded-xl w-full box-border">
                    <div class="flex justify-between items-center mb-4">
                        <label class="text-xs font-bold text-purple-300 uppercase tracking-wider">Pain Level</label>
                        <span class="bg-purple-600/20 text-purple-300 px-3 py-1 rounded-lg text-sm font-bold border border-purple-500/30">
                            <span id="pain-val">0</span>/10
                        </span>
                    </div>
                    
                    <input type="range" name="pain" min="0" max="10" value="0" step="1"
                        class="w-full h-2 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-purple-500"
                        oninput="document.getElementById('pain-val').innerText = this.value">
                        
                    <div class="flex justify-between text-[10px] text-gray-400 mt-2 font-bold tracking-widest uppercase">
                        <span>No Pain</span>
                        <span>Severe</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-purple-300 uppercase tracking-wider ml-1">Select Hospital</label>
                    <select name="hos_id" id="hos_id" required 
                        class="w-full p-4 bg-black/50 border border-purple-500/30 rounded-xl text-white appearance-none focus:border-purple-400 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all cursor-pointer box-border"
                        style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23A855F7%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1.25rem center; background-size: 0.8em;">
                        <option value="" class="text-gray-500">Choose a location...</option>
                        <?php foreach ($hospitals as $h): ?> 
                            <option value="<?php echo (int)$h['Hospital_ID']; ?>" class="text-white">
                                <?php echo htmlspecialchars($h['Name'] . " - " . $h['Location']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-purple-300 uppercase tracking-wider ml-1">Symptoms</label>
                    <textarea name="notes" id="notes" rows="4" 
                        class="w-full p-4 bg-black/50 border border-purple-500/30 rounded-xl text-white placeholder-gray-600 focus:border-purple-400 focus:ring-1 focus:ring-purple-500 focus:outline-none transition-all resize-none box-border shadow-inner"
                        placeholder="Describe what you are experiencing..."></textarea>
                </div>

                <button type="submit" 
                    class="w-full py-4 mt-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-lg rounded-xl shadow-lg shadow-purple-900/50 transform transition-all active:scale-[0.98] border border-white/10">
                    Submit Check-In
                </button>

            </form>
        </div>
    </main>

</body>
</html>