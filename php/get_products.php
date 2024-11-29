<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "productdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// SQL query using prepared statements
$sql = "SELECT name, price, category, image_path FROM products WHERE 1=1";

if ($category) {
    $sql .= " AND category = ?";
}

if ($search) {
    $sql .= " AND name LIKE ?";
}

$stmt = $conn->prepare($sql);

if ($category && $search) {
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("ss", $category, $searchParam);
} elseif ($category) {
    $stmt->bind_param("s", $category);
} elseif ($search) {
    $searchParam = "%" . $search . "%";
    $stmt->bind_param("s", $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();

// Close MySQL connection
$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>
