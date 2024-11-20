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

// Handle date filters if submitted
$date_filter_start = '';
$date_filter_end = '';
$product_filter = '';

if (isset($_POST['date_start']) && isset($_POST['date_end'])) {
    $date_filter_start = mysqli_real_escape_string($conn, $_POST['date_start']);
    $date_filter_end = mysqli_real_escape_string($conn, $_POST['date_end']);
    $product_filter = mysqli_real_escape_string($conn, $_POST['product_name']);
    $sales_sql = "SELECT sales.*, products.product_name, products.price 
                  FROM sales 
                  JOIN products ON sales.product_id = products.product_id 
                  WHERE DATE(sale_date) BETWEEN '$date_filter_start' AND '$date_filter_end'
                  ORDER BY sale_date DESC";
} else {
    // Default query to show recent sales if no filter is applied
    $sales_sql = "SELECT sales.*, products.product_name, products.price 
                  FROM sales 
                  JOIN products ON sales.product_id = products.product_id 
                  ORDER BY sale_date DESC";
}

$sales_result = mysqli_query($conn, $sales_sql);
$total_sales_amount = 0;

// Calculate total sales amount
while ($sale = mysqli_fetch_assoc($sales_result)) {
    $total_sales_amount += $sale['total_sale'];
}

// Reset the result pointer
mysqli_data_seek($sales_result, 0);

// Fetch product names for the dropdown
$product_sql = "SELECT product_id, product_name FROM products";
$product_result = mysqli_query($conn, $product_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales List - MON-NITE</title>
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
            position: relative;
            bottom: 0;
            width: 100%;
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
        
        /* Report Section */
        .report-section {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        /* Filter Section */
        .filter-section {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                <a style="color: #ffdd00;" href="sales.php">Sales</a>
                <a href="stock.php">Stock</a>
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
                <div class="filter-section">
                    <h1>Sales List</h1>
                    
                    <!-- Date Filter Form -->
                    <form action="sales.php" method="POST">
                        <label for="date_start">Start Date:</label>
                        <input type="date" id="date_start" name="date_start" value="<?php echo $date_filter_start; ?>">
                        <label for="date_end">End Date:</label>
                        <input type="date" id="date_end" name="date_end" value="<?php echo $date_filter_end; ?>"><br>
                        <label for="product_name">Product Name:</label>
                        <select id="product_name" name="product_name">
                            <option value="">Select a product</option>
                            <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
                                <option value="<?php echo $product['product_name']; ?>" <?php echo ($product['product_name'] == $product_filter) ? 'selected' : ''; ?>>
                                    <?php echo $product['product_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select><br>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="sales.php" class="btn btn-secondary">Clear Filter</a>
                    </form>
    
                </div>
                <!-- Sales Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity Sold</th>
                            <th>Sale Amount</th>
                            <th>Sale Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($sales_result) > 0): ?>
                            <?php while ($sale = mysqli_fetch_assoc($sales_result)): ?>
                                <tr>
                                    <td><?php echo $sale['product_name']; ?></td>
                                    <td><?php echo $sale['quantity_sold']; ?></td>
                                    <td>UGX <?php echo number_format($sale['total_sale'], 0); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($sale['sale_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                            <td colspan="1" style="text-align: left;"><strong>Total Sales:</strong></td> 
                            <td colspan="2">UGX <?php echo number_format($total_sales_amount, 0); ?></td>                             </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr> 
                            <td colspan="2" style="text-align: left;"><strong>Total Sales:</strong></td> 
                            <td colspan="4"><strong>UGX <?php echo number_format($total_sales_amount, 0); ?></strong></td> 
                        </tr> 
                    </tfoot>
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