<?php
// Include the database connection
include('db_connection.php');

// Fetch current settings from the database
$sql = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $sql);
$settings = mysqli_fetch_assoc($result);

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $shop_name = $_POST['shop_name'];
    $primary_color = $_POST['primary_color'];
    $secondary_color = $_POST['secondary_color'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];

    // Update settings in the database
    $update_sql = "UPDATE settings SET 
                    shop_name = '$shop_name',
                    primary_color = '$primary_color',
                    secondary_color = '$secondary_color',
                    contact_email = '$contact_email',
                    contact_phone = '$contact_phone',
                    address = '$address'
                    WHERE id = 1";

    if (mysqli_query($conn, $update_sql)) {
        $message = "Settings updated successfully!";
    } else {
        $message = "Error updating settings: " . mysqli_error($conn);
    }
}

// Handle CRUD operations for suppliers
if (isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $contact_details = $_POST['contact_details'];
    $add_supplier_sql = "INSERT INTO suppliers (supplier_name, contact_details) VALUES ('$supplier_name', '$contact_details')";
    mysqli_query($conn, $add_supplier_sql);
}

if (isset($_POST['update_supplier'])) {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $contact_details = $_POST['contact_details'];
    $update_supplier_sql = "UPDATE suppliers SET supplier_name = '$supplier_name', contact_details = '$contact_details' WHERE supplier_id = $supplier_id";
    mysqli_query($conn, $update_supplier_sql);
}

if (isset($_GET['delete_supplier'])) {
    $supplier_id = $_GET['delete_supplier'];
    $delete_supplier_sql = "DELETE FROM suppliers WHERE supplier_id = $supplier_id";
    mysqli_query($conn, $delete_supplier_sql);
}

// Fetch suppliers
$suppliers_sql = "SELECT * FROM suppliers";
$suppliers_result = mysqli_query($conn, $suppliers_sql);

// Handle CRUD operations for categories
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $add_category_sql = "INSERT INTO categories (category_name) VALUES ('$category_name')";
    mysqli_query($conn, $add_category_sql);
}

if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $update_category_sql = "UPDATE categories SET category_name = '$category_name' WHERE category_id = $category_id";
    mysqli_query($conn, $update_category_sql);
}

if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];
    $delete_category_sql = "DELETE FROM categories WHERE category_id = $category_id";
    mysqli_query($conn, $delete_category_sql);
}

// Fetch categories
$categories_sql = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_sql);

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - MON-NITE PHONES & COMPUTER ACCESSORIES</title>
    <style>
        /* General Reset */
        * {
            margin: 3;
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
            box-shadow: 0  2px 5px rgba(0, 0, 0, 0.1);
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

        h2 {
            margin-top: 0; /* Remove default margin */
            color: #004aad; /* Match header color */
        }        
        
        h3 {
            margin-top: 0; /* Remove default margin */
            color: #004aad; /* Match header color */
        }
        
        /* Button Styles */
        .btn {
            background-color: #004aad; /* Blue */
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .btn:hover {
            background-color: #003399; /* Darker blue */
            transform: translateY(-2px);
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background: #004aad; /* Blue */
            color: #ffdd00;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9; /* Light gray for even rows */
        }
        
        tr:hover {
            background-color: #e0e0e0; /* Light gray on hover */
        }
        
        /* Main Content */
        main {
            padding-top: 100px; /* Space for fixed navbar */
            min-height: calc(100vh - 80px); /* Ensure main is at least full height minus navbar */
        }
        
        /* Footer */
        .footer {
            background: #004aad;
            color: #fff;
            text-align: center;
            padding: 10px 0;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                <a style="color: #ffdd00;" href="settings.php">Settings</a>
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
        <section>
            <div class="container">
                <h2>Manage Suppliers</h2>
                <form action="settings.php" method="POST">
                    <input type="hidden" name="add_supplier" value="1">
                        <label for="supplier_name">Supplier Name:</label>
                        <input type="text" id="supplier_name" name="supplier_name" required>

                        <label for="contact_details">Contact Details:</label>
                        <textarea id="contact_details" name="contact_details" required></textarea>

                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </form>
        
                <h3>Existing Suppliers</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>Contact Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($supplier = mysqli_fetch_assoc($suppliers_result)) { ?>
                            <tr>
                                <td><?= $supplier['supplier_name'] ?></td>
                                <td><?= $supplier['contact_details'] ?></td>
                                <td>
                                    <form action="settings.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="supplier_id" value="<?= $supplier['supplier_id'] ?>">
                                        <input type="text" name="supplier_name" value="<?= $supplier['supplier_name'] ?>" required>
                                        <input type="text" name="contact_details" value="<?= $supplier['contact_details'] ?>" required>
                                        <button type="submit" class="btn btn-primary" name="update_supplier">Update</button>
                                    </form>
                                    <a class="btn btn-primary" href="settings.php?delete_supplier=<?= $supplier['supplier_id'] ?>" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        
                <h2>Manage Categories</h2>
                <form action="settings.php" method="POST">
                    <input type="hidden" name="add_category" value="1">
                        <label for="category_name">Category Name: </label>
                        <input type="text" id="category_name" name="category_name" required>

                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
        
                <h3>Existing Categories</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category ID</th>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($category = mysqli_fetch_assoc($categories_result)) { ?>
                            <tr>
                                <td><?= $category['category_id'] ?></td>
                                <td><?= $category['category_name'] ?></td>
                                <td>
                                    <form action="settings.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                        <input type="text" name="category_name" value="<?= $category['category_name'] ?>" required>
                                        <button type="submit" class="btn btn-primary" name="update_category">Update</button>
                                    </form>
                                    <a class="btn btn-primary" href="settings.php?delete_category=<?= $category['category_id'] ?>" onclick="return confirm('Are you sure you want to delete this category? ');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </section>
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