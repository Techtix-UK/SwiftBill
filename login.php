<?php
// login.php
session_start();

// --- CONFIGURATION ---
// Change this to whatever you want your admin password to be.
$admin_password = 'supersecretpassword123'; 
// ---------------------

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_password = $_POST['password'] ?? '';
    
    if ($submitted_password === $admin_password) {
        // Password is correct, set the session and redirect
        $_SESSION['swiftbill_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = 'Invalid password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SwiftBill</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4 font-sans text-slate-800">

    <div class="max-w-sm w-full bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="bg-slate-900 p-6 text-center">
            <h1 class="text-2xl font-bold text-white tracking-tight">⚡ SwiftBill</h1>
            <p class="text-slate-400 text-sm mt-1">Admin Access</p>
        </div>

        <div class="p-6">
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-700 border border-red-200 p-3 rounded-lg text-sm mb-5 font-medium text-center">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1 text-slate-700">Password</label>
                    <input type="password" name="password" required autofocus class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg mt-4 shadow-sm transition">
                    Log In
                </button>
            </form>
        </div>
    </div>

</body>
</html>