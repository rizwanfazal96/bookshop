<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookshop";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Define the number of results per page
$results_per_page = 15;

// Determine the total number of rows in the sales table
$sql = "SELECT COUNT(*) AS total FROM sales";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_results = $row["total"];

// Determine the total number of pages
$total_pages = ceil($total_results / $results_per_page);

// Determine the current page
if (!isset($_GET["page"])) {
  $current_page = 1;
} else {
  $current_page = $_GET["page"];
}

// Determine the first result on the current page
$this_page_first_result = ($current_page - 1) * $results_per_page;

// Define the SQL query for the sales table with filters
$sql = "SELECT sales.sale_id, customers.customer_name, products.product_name, products.product_price, sales.sale_date FROM sales JOIN customers ON sales.customer_id = customers.customer_id JOIN products ON sales.product_id = products.product_id";

if (isset($_POST["customer"]) && !empty($_POST["customer"])) {
  $customer = $_POST["customer"];
  $sql .= " WHERE customers.customer_name LIKE '%$customer%'";
}

if (isset($_POST["product"]) && !empty($_POST["product"])) {
  $product = $_POST["product"];
  if (strpos($sql, "WHERE") === false) {
    $sql .= " WHERE products.product_name LIKE '%$product%'";
  } else {
    $sql .= " AND products.product_name LIKE '%$product%'";
  }
}

// if (isset($_POST["price"]) && !empty($_POST["price"])) {
//   $price = round($_POST["price"]); // Round off input price to zero decimals
//   if (strpos($sql, "WHERE") === false) {
//     $sql .= " WHERE FLOOR(products.product_price) <= $price";
//   } else {
//     $sql .= " AND FLOOR(products.product_price <= $price";
//   }
// }

if (isset($_POST["price"]) && !empty($_POST["price"])) {
  $price = round($_POST["price"], 0);
  if (strpos($sql, "WHERE") === false) {
    $sql .= " WHERE ROUND(products.product_price, 0) <= $price";
  } else {
    $sql .= " AND ROUND(products.product_price, 0) <= $price";
  }
}


$sql .= " LIMIT $this_page_first_result, $results_per_page";

// Execute the SQL query
$result = $conn->query($sql);


// Output the filter inputs and search button
echo "<form method='post'>";
echo "<label for='customer'>Customer:</label>";
echo "<input type='text' id='customer' name='customer' value='" . (isset($_POST["customer"]) ? htmlspecialchars($_POST["customer"]) : "") . "'>";
echo "<label for='product'>Product:</label>";
echo "<input type='text' id='product' name='product' value='" . (isset($_POST["product"]) ? htmlspecialchars($_POST["product"]) : "") . "'>";
echo "<label for='price'>Price:</label>";
echo "<input type='number' id='price' name='price' value='" . (isset($_POST["price"]) ? htmlspecialchars($_POST["price"]) : "") . "'>";
echo "<input type='submit' value='Search'>";
echo "</form>";


// Output the filtered results in a table below the filters
echo "<table>";
echo "<tr><th>Sale ID</th><th>Customer Name</th><th>Product Name</th><th>Product Price</th><th>Sale Date</th></tr>";

while ($row = $result->fetch_assoc()) {
  echo "<tr><td>" . $row["sale_id"] . "</td><td>" . $row["customer_name"] . "</td><td>" . $row["product_name"] . "</td><td>" . $row["product_price"] . "</td><td>" . $row["sale_date"] . "</td></tr>";
}

echo "</table>";

// Add a last row for the total price of all filtered entries
$sql = "SELECT SUM(products.product_price) AS total_price FROM sales JOIN customers ON sales.customer_id = customers.customer_id JOIN products ON sales.product_id = products.product_id";

if (isset($_POST["customer"]) && !empty($_POST["customer"])) {
  $customer = $_POST["customer"];
  $sql .= " WHERE customers.customer_name LIKE '%$customer%'";
}

if (isset($_POST["product"]) && !empty($_POST["product"])) {
  $product = $_POST["product"];
  if (strpos($sql, "WHERE") === false) {
    $sql .= " WHERE products.product_name LIKE '%$product%'";
  } else {
    $sql .= " AND products.product_name LIKE '%$product%'";
  }
}

if (isset($_POST["price"]) && !empty($_POST["price"])) {
  $price = $_POST["price"];
  if (strpos($sql, "WHERE") === false) {
    $sql .= " WHERE products.product_price <= $price";
  } else {
    $sql .= " AND products.product_price <= $price";
  }
}

$result = $conn->query($sql);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

$row = $result->fetch_assoc();

$total_price = $row["total_price"];

echo "<p>Total price of all filtered entries: <b>" . $total_price . "</b></p>";

// Generate pagination links
echo "<div>";
for ($page = 1; $page <= $total_pages; $page++) {
  if ($page == $current_page) {
    echo "<span>$page</span>";
  } else {
    echo "<a href=\"?page=$page\">" . $page . "</a>";
  }
}
