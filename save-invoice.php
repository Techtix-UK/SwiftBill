<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// 1. Sanitize Basic Input
$client_id = $_POST['client_id'] ?? null;
$type = $_POST['type'] ?? 'invoice';
$due_date = $_POST['due_date'] ?? null;
$total_amount = $_POST['total_amount'] ?? 0;
$issue_date = date('Y-m-d');

// Generate a random unique invoice number (e.g., INV-8F3A)
$invoice_number = strtoupper($type === 'invoice' ? 'INV-' : 'QTE-') . strtoupper(substr(uniqid(), -6));

if (!$client_id || empty($_POST['description'])) {
    die("Client and at least one line item are required.");
}

try {
    // Start Database Transaction
    $pdo->beginTransaction();

    // 2. Insert the Parent Invoice/Quote record
    $stmt = $pdo->prepare("
        INSERT INTO invoices (client_id, invoice_number, status, type, issue_date, due_date, total_amount) 
        VALUES (?, ?, 'draft', ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $client_id, 
        $invoice_number, 
        $type, 
        $issue_date, 
        $due_date, 
        $total_amount
    ]);

    // Grab the ID of the invoice we just created
    $invoice_id = $pdo->lastInsertId();

    // 3. Loop through arrays and insert Line Items
    $descriptions = $_POST['description'];
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['unit_price'];

    $itemStmt = $pdo->prepare("
        INSERT INTO invoice_items (invoice_id, product_id, description, quantity, unit_price, subtotal) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < count($descriptions); $i++) {
        // Skip empty rows
        if (empty(trim($descriptions[$i]))) continue;

        $p_id = !empty($product_ids[$i]) ? $product_ids[$i] : null;
        $qty = (int)$quantities[$i];
        $price = (float)$prices[$i];
        $subtotal = $qty * $price;

        $itemStmt->execute([
            $invoice_id,
            $p_id,
            trim($descriptions[$i]),
            $qty,
            $price,
            $subtotal
        ]);
    }

    // 4. Commit Transaction
    $pdo->commit();

    // Redirect back to dashboard upon success
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    // If anything fails, rollback to protect the database
    $pdo->rollBack();
    die("Error saving document: " . $e->getMessage());
}