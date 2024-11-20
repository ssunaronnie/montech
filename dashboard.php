<?php
session_start();

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

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

session_start(); // Ensure session is started

// Initialize message variable
$message = '';

// Fetch user role from session
$role = $_SESSION['role'];

// Fetch Total Sales for the Current Month
$total_sales_sql = "SELECT SUM(total_sale) AS total_sales FROM sales WHERE MONTH(sale_date) = MONTH(CURDATE())";
$total_sales_result = mysqli_query($conn, $total_sales_sql);
$total_sales = mysqli_fetch_assoc($total_sales_result)['total_sales'];

// Fetch Top-Selling Products for the Current Month
$top_selling_sql = "SELECT products.product_name, SUM(sales.quantity) AS total_sold
                    FROM sales
                    JOIN products ON sales.product_id = products.product_id
                    WHERE MONTH(sales.sale_date) = MONTH(CURDATE())
                    GROUP BY sales.product_id
                    ORDER BY total_sold DESC
                    LIMIT 5";
$top_selling_result = mysqli_query($conn, $top_selling_sql);

// Fetch Low Stock Products
$low_stock_sql = "SELECT * FROM products WHERE quantity_in_stock < 10 ORDER BY quantity_in_stock ASC";
$low_stock_result = mysqli_query($conn, $low_stock_sql);

// Fetch Inventory Value
$inventory_value_sql = "SELECT SUM(products.quantity_in_stock * products.price) AS inventory_value FROM products";
$inventory_value_result = mysqli_query($conn, $inventory_value_sql);
$inventory_value = mysqli_fetch_assoc($inventory_value_result)['inventory_value'];

// Fetch Expenses
$total_expenses_sql = "SELECT SUM(amount) AS total_expenses FROM expenses";
$total_expenses_result = mysqli_query($conn, $total_expenses_sql);
$total_expenses = mysqli_fetch_assoc($total_expenses_result)['total_expenses'];

// Fetch products for dropdown including cost price
$product_sql = "SELECT product_id, product_name, cost FROM products WHERE quantity_in_stock > 0";
$product_result = mysqli_query($conn, $product_sql);

// Handle Expense Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['expense_type'])) {
    $expense_type = mysqli_real_escape_string($conn, $_POST['expense_type']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date = date('Y-m-d H:i:s');
    $recorded_by = 'Admin'; // Change this to dynamic user login

    // Insert expense into the expenses table
    $insert_expense_sql = "INSERT INTO expenses (expense_type, amount, description, date, recorded_by) 
                           VALUES ('$expense_type', $amount, '$description', '$date', '$recorded_by')";

    // Check if the expense was successfully recorded
    if (mysqli_query($conn, $insert_expense_sql)) {
        // Insert into the cash_flow table
        $insert_cash_flow_sql = "INSERT INTO cash_flow (type, amount, description, date, recorded_by) 
                                 VALUES ('Expense', $amount, '$description', '$date', '$recorded_by')";
        if (mysqli_query($conn, $insert_cash_flow_sql)) {
            $message = "Expense recorded successfully and added to cash flow!";
        } else {
            $message = "Expense recorded, but failed to update cash flow: " . mysqli_error($conn);
        }
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}


// Handle Sale Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $quantity_sold = (int)$_POST['quantity_sold'];
    $cost_price = mysqli_real_escape_string($conn, $_POST['cost_price']);
    $sale_price = mysqli_real_escape_string($conn, $_POST['sale_price']);
    $total_sale = mysqli_real_escape_string($conn, $_POST['total_sale']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $sale_date = date('Y-m-d H:i:s');
    $salesperson_id = $_SESSION['user_id']; // Assuming you have the salesperson's ID stored in the session

    // Get product details including product name
    $product_query = "SELECT product_name, quantity_in_stock FROM products WHERE product_id = '$product_id'";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);

    if ($product && $product['quantity_in_stock'] >= $quantity_sold) {
        // Retrieve the product name
        $product_name = $product['product_name'];

        // Update stock
        $new_stock = $product['quantity_in_stock'] - $quantity_sold;
        $update_stock_sql = "UPDATE products SET quantity_in_stock = $new_stock WHERE product_id = '$product_id'";
        mysqli_query($conn, $update_stock_sql);

        // Record sale
        $insert_sale_sql = "INSERT INTO sales (product_id, quantity_sold, cost_price, sale_price, total_sale, payment_method, sale_date, salesperson_id) 
                            VALUES ('$product_id', $quantity_sold, $cost_price, $sale_price, $total_sale, '$payment_method', '$sale_date', '$salesperson_id')";
        if (mysqli_query($conn, $insert_sale_sql)) {
            // Insert into cash_flow table
            $insert_cash_flow_sql = "INSERT INTO cash_flow (type, amount, description, date, recorded_by) 
                                     VALUES ('Income', $total_sale, 'Sale of product: $product_name', '$sale_date', '$salesperson_id')";
            if (mysqli_query($conn, $insert_cash_flow_sql)) {
                $message = "Sale recorded successfully and added to cash flow!";
            } else {
                $message = "Sale recorded, but failed to update cash flow: " . mysqli_error($conn);
            }
        } else {
            $message = "Error recording sale: " . mysqli_error($conn);
        }
    } else {
        $message = "Insufficient stock for the selected product.";
    }
}


// Outputting message to the HTML
if (!empty($message)) {
    echo "<script>
            alert('$message');
            setTimeout(function() {
                window.location.href = 'dashboard.php';
            }, 2000); // Redirect after 2 seconds
          </script>";
}

// Query to get top selling products
$query = "
    SELECT p.product_name, SUM(s.quantity_sold) AS total_sold
    FROM products p
    JOIN sales s ON p.product_id = s.product_id
    GROUP BY p.product_name
    ORDER BY total_sold DESC
    LIMIT 10"; // Adjust the limit as needed

$top_selling_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MON-NITE</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 5;
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
            background: white; /* Background color remains white */
            border-radius: 10px;
            border: 2px solid #ffdd00; /* Yellow border */
            padding: 20px;
            flex: 1;
            margin: 0 10px;
            text-align: center;
            color: #0059b3;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .stat-box:hover {
            border-color: #ffdd00; /* Ensure border stays yellow on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: scale(1.05);
        }

        
        h1 {
            margin-top: 0; /* Remove default margin */
            color: #ffdd00; /* Match header color */
        }       
        
        h2 {
            margin-top: 0; /* Remove default margin */
            color: #004aad; /* Match header color */
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
            color: #ffdd00;
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
        .container1 { 
            margin-left: auto; 
            padding: 10px 15px; 
            background-color: #0059b3; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            text-align: center; 
            
        } 
          .container1 .post-button { 
            margin-left: auto; 
            padding: 10px 15px; 
            background-color: #ffdd00; 
            color: #0059b3; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            text-align: center; 
            
        } 
        .container1 .post-button:hover { 
            background-color: #004080;
            color: white;
        }
        #postSaleModal,
        #recordExpenseModal {
            display: none; /* Initially hidden */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* Dark semi-transparent background */
            z-index: 1000;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }
        
        #postSaleModal > div,
        #recordExpenseModal > div {
            background: #fff; /* White background for the modal */
            margin: auto;
            padding: 20px;
            width: 90%;
            max-width: 400px; /* Maximum width for larger screens */
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
        }
        
        #postSaleModal h2,
        #recordExpenseModal h2 {
            margin-top: 0; /* Remove default margin */
            color: #ffdd00; /* Match header color */
        }
        
        #postSaleModal label,
        #recordExpenseModal label {
            display: block; /* Block display for labels */
            margin: 10px 0 5px; /* Spacing around labels */
            font-weight: bold; /* Bold labels */
            color: #0059b3; /* Match header color */
        }
        
        #postSaleModal input[type="text"],
        #postSaleModal input[type="date"],
        #postSaleModal input[type="number"],
        #postSaleModal textarea,
        #postSaleModal select,
        #recordExpenseModal input[type="text"],
        #recordExpenseModal input[type="date"],
        #recordExpenseModal input[type="number"],
        #recordExpenseModal textarea,
        #recordExpenseModal select {
            width: 100%; /* Full width */
            padding: 10px; /* Padding for input fields */
            border: 1px solid #ddd; /* Light border */
            border-radius: 3px; /* Rounded corners */
            box-sizing: border-box; /* Include padding in width calculation */
            margin-bottom: 10px; /* Space below input fields */
        }
        
        #postSaleModal button,
        #recordExpenseModal button {
            padding: 10px 15px; /* Padding for buttons */
            background-color: #0059b3; /* Button background color */
            color: #ffdd00; /* Button text color */
            border: none; /* Remove default border */
            border-radius: 3px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }
        
        #postSaleModal button:hover,
        #recordExpenseModal button:hover {
            background-color: #004080; /* Darker shade on hover */
        }
        
        #postSaleModal button[type="button"],
        #recordExpenseModal button[type="button"] {
            background-color: #ccc; /* Different color for cancel button */
        }
        
        #postSaleModal button[type="button"]:hover,
        #recordExpenseModal button[type="button"]:hover {
        background-color: #aaa; /* Darker shade on hover for cancel button */
        }

    </style>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="index.html" class="logo">MON-NITE PHONES</a>
            <nav class="nav-links">
                <a style="color: #ffdd00;" href="dashboard.php">Dashboard</a>
                <a href="products.php">Products</a>
                <a href="sales.php">Sales</a>
                <a href="stock.php">Stock</a>
                <a href="reports.php">Reports</a>
                <a href="cash_flow_report.php">Cash Flow</a>
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

    <main>
        <section>
            <div class="container1">

                <?php
                    // Fetch user role from session
                    $role = $_SESSION['role'];
                    
                    // Show different content based on user role
                    switch ($role) {
                        case 'Admin':
                            echo "<h4>Welcome, Admin!</h4>";
                            break;
                        case 'Manager':
                            echo "<h4>Welcome, Manager!</h4>";
                            break;
                        case 'Salesperson':
                            echo "<h4>Welcome, Salesperson!</h4>";
                            break;
                        case 'Stock Manager':
                            break;
                        default:
                            echo "<h4>Welcome!</h4>";
                    }
                ?>

                <h1>Dashboard</h1><br>
                <button onclick="openModal()" class="post-button">Post Sale</button>
                <button onclick="openModal2()" class="post-button">Record Expense</button>
            </div>

            <div class="container">

                <!-- Overview Stats -->
                <div class="overview-stats">
                    <div class="stat-box">
                        <h3>Total Sales</h3>
                        <p>UGX <?php echo number_format($total_sales, 0); ?></p>
                    </div>
                    <div class="stat-box">
                        <h3>Inventory Value</h3>
                        <p>UGX <?php echo number_format($inventory_value, 0); ?></p>
                    </div>                    
                    <div class="stat-box">
                        <h3>Total Expenses</h3>
                        <p>UGX <?php echo number_format($total_expenses, 0); ?></p>
                    </div>
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
                                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['total_sold']); ?></td>
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

                <!-- Low Stock Alerts -->
                <div class="report-section">
                    <h2>Low Stock Products</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Stock Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($low_stock_result) > 0): ?>
                                <?php while ($product = mysqli_fetch_assoc($low_stock_result)): ?>
                                    <tr>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td><?php echo $product['quantity_in_stock']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" style="text-align: center;">All products are well-stocked!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 MON-NITE PHONES & COMPUTER ACCESSORIES. All Rights Reserved.</p>
    </div>
    
    <!-- Post Sale Modal -->
    <div id="postSaleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Post Sales</h2>
            </div>
            <form action="dashboard.php" method="POST">
                <label for="product_id">Product</label>
                <select id="product_id" name="product_id" required onchange="updateCostPrice()">
                    <option value="">Select a product</option>
                    <?php
                    // Fetch products for dropdown including cost price
                    $product_sql = "SELECT product_id, product_name, price, quantity_in_stock FROM products WHERE quantity_in_stock > 0";
                    $product_result = mysqli_query($conn, $product_sql);
                    while ($product = mysqli_fetch_assoc($product_result)) {
                        // Add the cost price as a data attribute
                        echo "<option value='{$product['product_id']}' data-cost='{$product['price']}'>{$product['product_name']} - {$product['quantity_in_stock']} in stock</option>";
                    }
                    ?>
                </select>
                
                <label for="cost_price">Cost Price</label>
                <input type="number" id="cost_price" name="cost_price" min="1" readonly>
                
                <label for="quantity_sold">Quantity Sold</label>
                <input type="number" id="quantity_sold" name="quantity_sold" min="1" required oninput="calculateTotal()">
    
                <label for="sale_price">Sale Price</label>
                <input type="number" id="sale_price" name="sale_price" min="500" required oninput="calculateTotal()">
    
                <label for="total_sale">Total Sale</label>
                <input type="number" id="total_sale" name="total_sale" min="500" readonly>
    
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="Cash">Cash</option>
                    <option value="Mobile Money">Mobile Money</option>
                </select>
    
                <button type="submit" class="btn">Post Sale</button>
            </form>
        </div>
    </div>
    
    <!-- Record Expense Modal -->
    <div id="recordExpenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal2()">&times;</span>
                <h2>Record Expense</h2>
            </div>
            <form action="dashboard.php" method="POST">
                <label for="expense_type">Expense Type</label>
                <select id="expense_type" name="expense_type" required>
                    <option value="">Select an expense type</option>
                    <option value="Facilitation">Facilitation</option>
                    <option value="Rent">Rent</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Salary">Salary</option>
                </select>
                    
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
    
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
    
                <button type="submit" class="btn">Post Expense</button>
            </form>
        </div>
    </div>
    
    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
        function openModal() {
            document.getElementById("postSaleModal").style.display = "block";
        }
    
        function closeModal() {
            document.getElementById("postSaleModal").style.display = "none";
        }
    
        function openModal2() {
            document.getElementById("recordExpenseModal").style.display = "block";
        }
    
        function closeModal2() {
            document.getElementById("recordExpenseModal").style.display = "none";
        }
    
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal1 = document.getElementById('postSaleModal');
            const modal2 = document.getElementById('recordExpenseModal');
            if (event.target == modal1) {
                closeModal();
            } else if (event.target == modal2) {
                closeModal2();
            }
        }
    
        function calculateTotal() {
            const quantity = document.getElementById('quantity_sold').value;
            const salePrice = document.getElementById('sale_price').value;
            const totalSale = document.getElementById('total_sale');
    
            if (quantity && salePrice) {
                totalSale.value = quantity * salePrice;
            } else {
                totalSale.value = 0;
            }
        }
        
        function updateCostPrice() {
            const productSelect = document.getElementById('product_id');
            const costPriceInput = document.getElementById('cost_price');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
        
            // Get the cost price from the selected option's data attribute
            const costPrice = selectedOption.getAttribute('data-cost');
            costPriceInput.value = costPrice ? costPrice : '';
        }
    </script>
</body>
</html>