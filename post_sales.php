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

// Fetch products for the dropdown
$product_sql = "SELECT product_id, product_name, stock_quantity FROM products WHERE stock_quantity > 0";
$product_result = mysqli_query($conn, $product_sql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity_sold = (int)$_POST['quantity'];
    $payment_method = $_POST['payment_method'];
    $sale_date = date('Y-m-d H:i:s');

    // Get product stock
    $stock_query = "SELECT stock_quantity FROM products WHERE product_id = '$product_id'";
    $stock_result = mysqli_query($conn, $stock_query);
    $product = mysqli_fetch_assoc($stock_result);

    if ($product && $product['stock_quantity'] >= $quantity_sold) {
        // Update stock
        $new_stock = $product['stock_quantity'] - $quantity_sold;
        $update_stock_sql = "UPDATE products SET stock_quantity = $new_stock WHERE product_id = '$product_id'";
        mysqli_query($conn, $update_stock_sql);

        // Record sale
        $insert_sale_sql = "INSERT INTO sales (product_id, quantity, payment_method, sale_date) 
                            VALUES ('$product_id', $quantity_sold, '$payment_method', '$sale_date')";
        if (mysqli_query($conn, $insert_sale_sql)) {
            $message = "Sale recorded successfully!";
        } else {
            $message = "Error recording sale: " . mysqli_error($conn);
        }
    } else {
        $message = "Insufficient stock for the selected product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Sales - MON-NITE</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
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
        
        /* Cards Section */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
            padding: 20px;
        }
        
        /* Main Content */
        main {
            padding-top: 100px; /* Space for fixed navbar */
            min-height: calc(100vh - 70px); /* Ensure main is at least full height minus navbar */
        }
        
        .card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            border-color: #004aad;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }
        
        /* Overview Stats */
        .overview-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px; /* Adjusted for spacing */
            padding: 20px;
        }
        
        .stat-box {
            background: #ffdd00; /* Yellow */
            border-radius: 10px;
            padding: 20px;
            flex: 1;
            margin: 0 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .stat-box:hover {
            border-color: #f1f1f1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }
        
        /* Report Section */
        .report-section {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
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
        .nav-links .logout-button { 
            margin-left: auto; 
            padding: 10px 15px; 
            background-color: #0059b3; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            text-align: center; 
            
        } 
        .nav-links .logout-button:hover { 
            background-color: #004080; 
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo">MON-NITE PHONES</a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="sales.php">Sales</a>
                <a href="stock.php">Stock</a>
                <a href="reports.php">Reports</a>
                <a href="settings.php">Settings</a>
                <a href="logout.php" class="logout-button">Log Out</a>
            </nav>
            <div class="hamburger-menu" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h1>Post Sales</h1>
    
            <?php if (isset($message)) { ?>
                <div class="alert"><?= $message ?></div>
            <?php } ?>
    
            <form action="post_sales.php" method="POST">
                <div class="form-group">
                    <label for="product_id">Product</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">Select a product</option>
                        <?php
                        // Fetch products for dropdown
                        $product_sql = "SELECT * FROM products WHERE quantity_in_stock > 0";
                        $product_result = mysqli_query($conn, $product_sql);
                        while ($product = mysqli_fetch_assoc($product_result)) {
                            echo "<option value='{$product['product_id']}'>{$product['product_name']} - {$product['quantity_in_stock']} in stock</option>";
                        }
                        ?>
                    </select>
                </div>
    
                <div class="form-group">
                    <label for="cost_price">Cost Price</label>
                    <input type="number" id="cost_price" name="cost_price" min="1" readonly>
                </div>
                
                <div class="form-group">
                    <label for="quantity_sold">Quantity Sold</label>
                    <input type="number" id="quantity_soldquantity_sold" name="quantity_sold" min="1" required>
                </div>

                <div class="form-group">
                    <label for="sale_price">Sale Price</label>
                    <input type="number" id="sale_price" name="sale_price" min="500" required>
                </div>
                
                <div class="form-group">
                    <label for="total_sale">Total Sale</label>
                    <input type="number" id="total_sale" name="total_sale" min="500" readonly>
                </div>
    
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="Cash">Cash</option>
                        <option value="Mobile Money">Mobile Money</option>
                    </select>
                </div>
                
                
                <button type="submit">Post Sale</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 MON-NITE PHONES & COMPUTER ACCESSORIES. All Rights Reserved.</p>
    </div>
    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>
</html>
