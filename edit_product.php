<?php
// Include the database connection
include 'db_connect.php';

// Check if the product ID is provided
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details based on the product ID
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Product not found.'); window.location.href='products.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Invalid product ID.'); window.location.href='products.php';</script>";
    exit;
}

// Handle form submission to update the product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity_in_stock = mysqli_real_escape_string($conn, $_POST['quantity_in_stock']);

    // Update the product in the database
    $update_sql = "UPDATE products SET product_name = '$product_name', category_id = '$category_id', price = '$price', quantity_in_stock = '$quantity_in_stock' WHERE product_id = $product_id";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - MON-NITE</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="#" class="logo">MON-NITE PHONES</a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="sales.php">Sales</a>
                <a href="stock.php">Stock</a>
                <a href="reports.php">Reports</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>

    <main>
        <section>
            <div class="container">
                <h1>Edit Product</h1>
                <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" value="<?php echo $product['product_name']; ?>" required>

                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select a Category</option>
                        <?php
                        // Fetch categories from the categories table
                        $category_result = mysqli_query($conn, "SELECT * FROM categories");
                        while ($category = mysqli_fetch_assoc($category_result)) {
                            $selected = $category['category_id'] == $product['category_id'] ? 'selected' : '';
                            echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                        }
                        ?>
                    </select>

                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>

                    <label for="quantity_in_stock">Stock Quantity</label>
                    <input type="number" id="quantity_in_stock" name="quantity_in_stock" value="<?php echo $product['quantity_in_stock']; ?>" required>

                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
