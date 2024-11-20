<?php
// Include database connection
include('db_connection.php');

// Initialize variables for filters
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$where_clause = '';

// Build query conditionally based on filters
if (!empty($start_date) && !empty($end_date)) {
    $where_clause = "WHERE date BETWEEN '$start_date' AND '$end_date'";
}

// Fetch data from cash_flow table
$query = "SELECT * FROM cash_flow $where_clause ORDER BY date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Flow Report</title>
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

        .income {
            color: green;
            font-weight: bold;
        }
        .expense {
            color: red;
            font-weight: bold;
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
                <h1>Cash Flow Report</h1>
        
                <!-- Filter Form -->
                <form method="POST" class="filter-form">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" required>
        
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" required>
        
                    <button class="btn btn-primary" type="submit">Filter</button>
                </form>
        
                <!-- Cash Flow Table -->
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($row['date'])); ?></td>
                                    <td class="<?php echo strtolower($row['type']); ?>">
                                        <?php echo ucfirst($row['type']); ?>
                                    </td>
                                    <td><?php echo number_format($row['amount'], 0); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No records found for the selected date range.</td>
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
