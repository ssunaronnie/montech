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


// Handle stock update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $new_stock_quantity = mysqli_real_escape_string($conn, $_POST['new_stock_quantity']);

    // Update the stock quantity in the products table
    $update_stock_sql = "UPDATE products SET quantity_in_stock = $new_stock_quantity WHERE product_id = $product_id";
    if (mysqli_query($conn, $update_stock_sql)) {
        echo "<script>alert('Stock updated successfully!'); window.location.href='stock.php';</script>";
    } else {
        echo "Error updating stock: " . mysqli_error($conn);
    }
}

// Fetch all products
$products_sql = "SELECT * FROM products ORDER BY quantity_in_stock ASC";
$products_result = mysqli_query($conn, $products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Monitoring - MON-NITE</title>
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
            color: #ffdd00;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #003399; /* Darker blue */
        }
        
        h1 {
            margin-top: 0; /* Remove default margin */
            color: #004aad; /* Match header color */
        }
                
        /* Footer */
        .footer {
            background: #004aad;
            color: #fff;
            text-align: center;
            padding: 15px 0;
        }
        
        /* Main Content */
        main {
            padding-top: 90px; /* Space for fixed navbar */
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
            color: #ffdd00;
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
                <a style="color: #ffdd00;" href="stock.php">Stock</a>
                <a href="reports.php">Reports</a>
                <a href="settings.php">Settings</a>
            </nav>
            <div class="hamburger-menu" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <main>
        <section>
            <div class="container">
                <h1>Stock Monitoring</h1>
                
                <!-- Stock Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Stock Quantity</th>
                            <th>Update Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($products_result) > 0): ?>
                            <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                                <tr <?php if ($product['quantity_in_stock'] < 10) echo 'style="background-color: #fff;"'; ?>>
                                    <td><?php echo $product['product_name']; ?></td>
                                    <td><?php echo $product['quantity_in_stock']; ?></td>
                                    <td>
                                        <form action="stock.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                            <input type="number" name="new_stock_quantity" min="0" placeholder="New stock" required>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
    </script>
</body>
</html>
