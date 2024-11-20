<?php
// Include the database connection
include 'db_connect.php';

// Check if the product ID is provided
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Confirm that the product exists
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        // Delete the product from the database
        $delete_sql = "DELETE FROM products WHERE product_id = $product_id";

        if (mysqli_query($conn, $delete_sql)) {
            echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
        } else {
            echo "Error deleting product: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Product not found.'); window.location.href='products.php';</script>";
    }
} else {
    echo "<script>alert('Invalid product ID.'); window.location.href='products.php';</script>";
}
?>
