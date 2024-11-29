<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "productdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
    $name = isset($_POST['nameproduct']) ? $conn->real_escape_string($_POST['nameproduct']) : '';
    $price = isset($_POST['priceproduct']) ? $conn->real_escape_string($_POST['priceproduct']) : '';

    // Check if all required fields are filled
    if (empty($category) || empty($name) || empty($price) || empty($_FILES["productImage"]["name"])) {
        echo "Please fill out all fields.";
    } else {
        // Handle file upload
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }

        $target_file = $target_dir . basename($_FILES["productImage"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["productImage"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (optional, e.g., max 5MB)
        if ($_FILES["productImage"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        $allowed_formats = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_formats)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
                // Prepare and bind
                $stmt = $conn->prepare("INSERT INTO products (category, name, price, image_path) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssis", $category, $name, $price, $target_file);

                if ($stmt->execute() === TRUE) {
                    echo "<script>alert('Product added successfully!'); window.location.href='../addproduct.html';</script>";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}

$conn->close();
?>
<script>
    alert('Product added successfully!');
    var currentURL = window.location.href;
    var newURL = currentURL.replace('/php/', '/');
    window.location.href = newURL + 'index.html';
</script>
