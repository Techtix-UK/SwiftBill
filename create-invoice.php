<?php
require_once 'db.php';

// Fetch clients for the dropdown
$clients = $pdo->query("SELECT id, name, company FROM clients ORDER BY name ASC")->fetchAll();

// Fetch products to populate line items dynamically
$products = $pdo->query("SELECT id, name, price, description FROM products ORDER BY name ASC")->fetchAll();
$products_json = json_encode($products); // Pass to JS
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased p-10">

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="bg-slate-900 p-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white tracking-tight">Create New Invoice or Quote</h1>
            <a href="index.php" class="text-slate-300 hover:text-white">Cancel & Return</a>
        </div>

        <form action="save-invoice.php" method="POST" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Client</label>
                    <select name="client_id" required class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select a Client...</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['name']) ?> (<?= htmlspecialchars($client['company']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Document Type</label>
                    <select name="type" required class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                        <option value="invoice">Invoice</option>
                        <option value="quote">Quote</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Due Date</label>
                    <input type="date" name="due_date" required class="w-full border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <h3 class="text-lg font-bold text-slate-900 border-b pb-2 mb-4">Line Items</h3>
            <div id="items-container" class="space-y-4 mb-4">
                </div>

            <button type="button" onclick="addRow()" class="text-indigo-600 font-medium hover:text-indigo-800 text-sm mb-8">
                + Add Another Item
            </button>

            <div class="flex justify-between items-end border-t pt-6">
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Notes / Terms</label>
                    <textarea name="notes" rows="3" class="w-96 border border-slate-300 rounded-lg p-2.5 bg-slate-50 focus:ring-2 focus:ring-indigo-500" placeholder="Thank you for your business."></textarea>
                </div>
                <div class="text-right">
                    <p class="text-slate-500 mb-2">Total Amount</p>
                    <p class="text-4xl font-bold text-slate-900" id="grand-total">$0.00</p>
                    <input type="hidden" name="total_amount" id="total_amount_input" value="0">
                    <button type="submit" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-sm transition">
                        Save Document
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const products = <?= $products_json ?>;

        function addRow() {
            const container = document.getElementById('items-container');
            const rowId = Date.now();
            
            let options = '<option value="">Custom Item...</option>';
            products.forEach(p => {
                options += `<option value="${p.id}" data-price="${p.price}" data-desc="${p.description}">${p.name}</option>`;
            });

            const row = document.createElement('div');
            row.className = 'flex gap-4 items-start line-item';
            row.innerHTML = `
                <div class="w-1/3">
                    <select name="product_id[]" onchange="autoFill(this)" class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50">
                        ${options}
                    </select>
                </div>
                <div class="w-1/3">
                    <input type="text" name="description[]" placeholder="Description" required class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50">
                </div>
                <div class="w-24">
                    <input type="number" name="quantity[]" value="1" min="1" onchange="calculateTotal()" class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50 item-qty">
                </div>
                <div class="w-32">
                    <input type="number" step="0.01" name="unit_price[]" placeholder="0.00" onchange="calculateTotal()" required class="w-full border border-slate-300 rounded-lg p-2 bg-slate-50 item-price">
                </div>
                <button type="button" onclick="this.parentElement.remove(); calculateTotal();" class="text-red-500 hover:text-red-700 p-2 font-bold">X</button>
            `;
            container.appendChild(row);
        }

        function autoFill(select) {
            const selected = select.options[select.selectedIndex];
            const row = select.closest('.line-item');
            
            if (selected.value !== "") {
                row.querySelector('input[name="description[]"]').value = selected.dataset.desc;
                row.querySelector('input[name="unit_price[]"]').value = selected.dataset.price;
            }
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.line-item').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                total += (qty * price);
            });
            
            document.getElementById('grand-total').innerText = '$' + total.toFixed(2);
            document.getElementById('total_amount_input').value = total.toFixed(2);
        }

        // Initialize with one empty row
        window.onload = addRow;
    </script>
</body>
</html>