<?php
$pdo = new PDO("mysql:host=localhost;dbname=bookshop", "root", "");

// Read JSON data from file
$jsonFilePath = 'saledata.json';
$jsonData = file_get_contents($jsonFilePath);

$data = json_decode($jsonData, true);

foreach ($data as $key => $entry) {
    // Check if the customer already exists
    $stmtCustomerCheck = $pdo->prepare("SELECT customer_id FROM customers WHERE customer_name = ?");
    $stmtCustomerCheck->execute([$entry['customer_name']]);
    $existingCustomer = $stmtCustomerCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingCustomer) {
        // Customer already exists, use existing customer_id
        $customerId = $existingCustomer['customer_id'];
    } else {
        // Insert into customers table
        $stmtCustomer = $pdo->prepare("INSERT INTO customers (customer_name, customer_mail) VALUES (?, ?)");
        $stmtCustomer->execute([$entry['customer_name'], $entry['customer_mail']]);
        
        // Get the auto-incremented customer_id
        $customerId = $pdo->lastInsertId();
    }

    // Check if the product already exists
    $stmtProductCheck = $pdo->prepare("SELECT product_id FROM products WHERE product_name = ?");
    $stmtProductCheck->execute([$entry['product_name']]);
    $existingProduct = $stmtProductCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingProduct) {
        // Product already exists, use existing product_id
        $productId = $existingProduct['product_id'];
    } else {
        // Insert into products table
        $stmtProduct = $pdo->prepare("INSERT INTO products (product_name, product_price) VALUES (?, ?)");
        $stmtProduct->execute([$entry['product_name'], $entry['product_price']]);
        
        // Get the auto-incremented product_id
        $productId = $pdo->lastInsertId();
    }

    // Insert into sales table
    $stmtSales = $pdo->prepare("INSERT INTO sales (customer_id, product_id, sale_date) VALUES (?, ?, ?)");
    $stmtSales->execute([$customerId, $productId, $entry['sale_date']]);
}


echo "Data inserted successfully.";
?>
