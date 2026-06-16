<?php
// products.php
require_once 'auth.php';
require_once 'db.php';

$success = '';
$error = '';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($name && $price >= 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $description]);
            $success = "Product added successfully.";
        } catch (PDOException $e) {
            $error = "Failed to add product: " . $e->getMessage();
        }
    } else {
        $error = "Name and a valid price are required.";
    }
}

// Fetch products
try {
    $products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - SwiftBill</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-white p-6 flex flex-col">
            <div class="text-2xl font-bold tracking-tight mb-8">⚡ SwiftBill</div>
            <nav class="space-y-2 flex-1">
                <a href="index.php" class="block py-2.5 px-4 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Dashboard</a>
                <a href="#" class="block py-2.5 px-4 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Invoices</a>
                <a href="clients.php" class="block py-2.5 px-4 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Clients</a>
                <a href="products.php" class="block py-2.5 px-4 rounded-lg bg-indigo-600 font-medium shadow-sm">Products</a>
            </nav>
            <div class="pt-4 border-t border-slate-800 text-sm text-slate-500">
                <a href="logout.php" class="hover:text-white transition">Log Out</a>
            </div>
        </aside>

        <main class="flex-1 p-10 overflow-y-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-950">Products & Services</h1>
                <p class="text-slate-500 mt-1">Manage what you bill for.</p>
            </div>

            <?php if ($success): ?>
                <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 p-4 rounded-lg mb-6 font-medium">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-700 border border-red-200 p-4 rounded-lg mb-6 font-medium">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6">
                        <h2 class="font-bold text-slate-800 mb-4">Add New Item</h2>
                        <form method="POST" action="products.php" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-slate-700">Item Name *</label>
                                <input type="text" name="name" required class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-slate-700">Base Price ($) *</label>
                                <input type="number" step="0.01" name="price" required class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1 text-slate-700">Default Description</label>
                                <textarea name="description" rows="3" class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50 focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2.5 px-4 rounded-lg mt-2 transition">
                                Save Product
                            </button>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    <th class="p-4">Item</th>
                                    <th class="p-4">Description</th>
                                    <th class="p-4 text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="3" class="p-8 text-center text-slate-400">No products found. Add your first service!</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="p-4 font-medium text-slate-900"><?= htmlspecialchars($product['name']) ?></td>
                                            <td class="p-4 text-slate-500 truncate max-w-xs"><?= htmlspecialchars($product['description']) ?></td>
                                            <td class="p-4 text-right font-semibold text-slate-900">$<?= number_format($product['price'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>