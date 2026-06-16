<?php
// 1. Auto-redirect to installer if the system isn't set up yet
if (!file_exists('db.php')) {
    header("Location: install.php");
    exit;
}

require_once 'db.php';

try {
    // 2. Fetch Dashboard Stats
    $stats = $pdo->query("
        SELECT 
            COALESCE(SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END), 0) as total_paid,
            COALESCE(SUM(CASE WHEN status != 'paid' THEN total_amount ELSE 0 END), 0) as total_outstanding,
            COUNT(id) as total_invoices
        FROM invoices
    ")->fetch();

    // 3. Fetch Recent Invoices
    $stmt = $pdo->query("
        SELECT i.*, c.name as client_name 
        FROM invoices i 
        JOIN clients c ON i.client_id = c.id 
        ORDER BY i.created_at DESC 
        LIMIT 15
    ");
    $invoices = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Dashboard error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwiftBill Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-white p-6 flex flex-col">
            <div class="text-2xl font-bold tracking-tight mb-8">⚡ SwiftBill</div>
            <nav class="space-y-2 flex-1">
                <a href="index.php" class="block py-2.5 px-4 rounded-lg bg-indigo-600 font-medium shadow-sm">Dashboard</a>
                <a href="#" class="block py-2.5 px-4 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Invoices</a>
                <a href="#" class="block py-2.5 px-4 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Clients</a>
                <a href="#" class="block py-2.5 px-4 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">Products</a>
            </nav>
            <div class="pt-4 border-t border-slate-800 text-sm text-slate-500">
                Logged in as Admin
            </div>
        </aside>

        <main class="flex-1 p-10 overflow-y-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-950">Overview</h1>
                    <p class="text-slate-500 mt-1">Here is what's happening with your billing today.</p>
                </div>
                <a href="create-invoice.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition flex items-center gap-2">
                    <span class="text-xl leading-none">+</span> New Invoice
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200">
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Paid</p>
                    <p class="text-3xl font-bold text-emerald-600">$<?= number_format($stats['total_paid'], 2) ?></p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200">
                    <p class="text-sm font-medium text-slate-500 mb-1">Outstanding</p>
                    <p class="text-3xl font-bold text-amber-500">$<?= number_format($stats['total_outstanding'], 2) ?></p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200">
                    <p class="text-sm font-medium text-slate-500 mb-1">Total Documents</p>
                    <p class="text-3xl font-bold text-slate-900"><?= number_format($stats['total_invoices']) ?></p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="font-bold text-slate-800">Recent Invoices & Quotes</h2>
                </div>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500 bg-white">
                            <th class="p-4 px-6">Document #</th>
                            <th class="p-4">Client</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="6" class="p-10 text-center text-slate-500">
                                    <div class="mb-2 text-4xl">📄</div>
                                    No documents found. Create your first invoice!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr class="hover:bg-slate-50 transition group">
                                    <td class="p-4 px-6 font-medium text-slate-900"><?= htmlspecialchars($invoice['invoice_number']) ?></td>
                                    <td class="p-4 text-slate-600"><?= htmlspecialchars($invoice['client_name']) ?></td>
                                    <td class="p-4 text-slate-500"><?= htmlspecialchars($invoice['issue_date']) ?></td>
                                    <td class="p-4 font-semibold text-slate-900">$<?= number_format($invoice['total_amount'], 2) ?></td>
                                    <td class="p-4">
                                        <?php
                                            $statusColors = [
                                                'paid'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                'sent'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'void'  => 'bg-red-50 text-red-700 border-red-200'
                                            ];
                                            $colorClass = $statusColors[$invoice['status']] ?? $statusColors['draft'];
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border <?= $colorClass ?>">
                                            <?= ucfirst($invoice['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 px-6 text-right space-x-3 opacity-0 group-hover:opacity-100 transition">
                                        <?php if ($invoice['status'] !== 'paid'): ?>
                                            <a href="pay.php?id=<?= $invoice['id'] ?>" class="text-indigo-600 hover:text-indigo-900 font-medium">Pay Link</a>
                                        <?php endif; ?>
                                        <a href="#" class="text-slate-400 hover:text-slate-600 font-medium">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>