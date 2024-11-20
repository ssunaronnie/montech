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

// Handle date range filter if submitted
$start_date = '';
$end_date = '';
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
}

// Fetch Total Sales
$total_sales_sql = "SELECT SUM(total_sale) AS total_sales FROM sales";
if ($start_date && $end_date) {
    $total_sales_sql .= " WHERE sale_date BETWEEN '$start_date' AND '$end_date'";
}
$total_sales_result = mysqli_query($conn, $total_sales_sql);
$total_sales = mysqli_fetch_assoc($total_sales_result)['total_sales'];

// Fetch Top-Selling Products
$top_selling_sql = "SELECT products.product_name, SUM(sales.quantity_sold) AS total_sold
                    FROM sales
                    JOIN products ON sales.product_id = products.product_id
                    GROUP BY sales.product_id
                    ORDER BY total_sold DESC
                    LIMIT 5";
$top_selling_result = mysqli_query($conn, $top_selling_sql);

// Fetch Inventory Value
$inventory_value_sql = "SELECT SUM(products.quantity_in_stock * products.price) AS inventory_value FROM products";
$inventory_value_result = mysqli_query($conn, $inventory_value_sql);
$inventory_value = mysqli_fetch_assoc($inventory_value_result)['inventory_value'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - MON-NITE</title>
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
        
        h2 {
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
            <a href="index.html" class="logo">MON-NITE PHONES</a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="sales.php">Sales</a>
                <a href="stock.php">Stock</a>
                <a style="color: #ffdd00;" href="reports.php">Reports</a>
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
                <h1>Reports</h1>

                <!-- Date Range Filter Form -->
                <form action="reports.php" method="POST" style="margin-bottom: 20px;">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </form>

                <!-- Total Sales -->
                <div class="report-section">
                    <h2>Total Sales</h2>
                    <p>UGX <?php echo number_format($total_sales, 0); ?></p>
                </div>

                <!-- Top-Selling Products -->
                <div class="report-section">
                    <h2>Top-Selling Products</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($top_selling_result) > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($top_selling_result)): ?>
                                    <tr>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td><?php echo $product['total_sold']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">No sales data available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Inventory Value -->
                <div class="report-section">
                    <h2>Inventory Value</h2>
                    <p>UGX <?php echo number_format($inventory_value, 0); ?></p>
                </div>
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
