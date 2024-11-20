<?php
// Database connection parameters
$host = "localhost";
$username = "cglugcom_srm";
$password = ",Adgjmptw1";
$database = "cglugcom_mon_nite";

// Connect to the database
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $quantity_sold = mysqli_real_escape_string($conn, $_POST['quantity_sold']);
    $sale_date = date('Y-m-d H:i:s');

    // Fetch product details for price and stock check
    $product_sql = "SELECT * FROM products WHERE product_id = $product_id";
    $product_result = mysqli_query($conn, $product_sql);
    $product = mysqli_fetch_assoc($product_result);

    if ($product) {
        $price = $product['price'];
        $quantity_in_stock = $product['quantity_in_stock'];
        
        // Check if stock is sufficient
        if ($quantity_sold <= $quantity_in_stock) {
            $total_sale = $price * $quantity_sold;

            // Insert the sale into the sales table
            $insert_sale_sql = "INSERT INTO sales (product_id, quantity_sold, total_sale, sale_date) VALUES ($product_id, $quantity_sold, $total_sale, '$sale_date')";
            if (mysqli_query($conn, $insert_sale_sql)) {
                // Update the stock in the products table
                $new_stock = $quantity_in_stock - $quantity_sold;
                $update_stock_sql = "UPDATE products SET quantity_in_stock = $new_stock WHERE product_id = $product_id";
                mysqli_query($conn, $update_stock_sql);

                echo "<script>alert('Sale recorded successfully!'); window.location.href='add_sale.php';</script>";
            } else {
                echo "Error recording sale: " . mysqli_error($conn);
            }
        } else {
            echo "<script>alert('Insufficient stock available.');</script>";
        }
    } else {
        echo "<script>alert('Product not found.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sale - MON-NITE</title>
    <style>
       /* General Reset */
        * {
            margin: 3;
            padding: 3;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #f4f4f4;
        }
        
        /* Navbar */
        .navbar {
            background: #004aad; /* Blue */
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color: #ffdd00;
        }
        
        .navbar .nav-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            transition: color 0.3s;
        }
        
        .navbar .nav-links a:hover {
            color: #ffdd00; /* Yellow */
        }
        
        /* Hamburger Menu (Mobile View) */
        .hamburger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        
        .hamburger-menu span {
            background: #fff;
            height: 3px;
            width: 25px;
            margin: 4px;
            transition: all 0.3s;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
        
            .hamburger-menu {
                display: flex;
            }
        
            .nav-links.active {
                display: flex;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                background: #004aad;
                width: 100%;
                padding: 10px 0;
            }
        }
        
        /* Button Styles */
        .btn {
            background-color: #004aad; /* Blue */
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #003399; /* Darker blue */
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%; /* Full width for mobile */
            max-width: 500px; /* Max width for larger screens */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .modal-content h2 {
            margin-bottom: 20px;
        }
        .modal-header {
            background-color: #004aad; /* Bootstrap primary color */
            color: white;
            padding: 10px; /* Add some padding */
            border-top-left-radius: 10px; /* Round the top corners */
            border-top-right-radius: 10px; /* Round the top corners */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: #000;
        }
        
        /* Main Content */
        main {
            padding-top: 100px; /* Space for fixed navbar */
            min-height: calc(100vh - 70px); /* Ensure main is at least full height minus navbar */
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background: #004aad; /* Blue */
            color: #fff;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        /* Footer */
        .footer {
            background: #004aad;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        
        /* Report Section */
        .report-section {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, ) 
        }
    </style>
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
                <h1>Record a Sale</h1>
                <form action="add_sales.php" method="POST">
                    <label for="product_id">Product</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">Select a Product</option>
                        <?php
                        // Fetch products for dropdown
                        $product_sql = "SELECT * FROM products WHERE quantity_in_stock > 0";
                        $product_result = mysqli_query($conn, $product_sql);
                        while ($product = mysqli_fetch_assoc($product_result)) {
                            echo "<option value='{$product['product_id']}'>{$product['product_name']} - {$product['quantity_in_stock']} in stock</option>";
                        }
                        ?>
                    </select>

                    <label for="quantity_sold">Quantity Sold</label>
                    <input type="number" id="quantity_sold" name="quantity_sold" min="1" required>

                    <button type="submit" class="btn btn-primary">Record Sale</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
