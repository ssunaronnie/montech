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

// Fetch products with category names
$sql = "
    SELECT products.product_id, products.product_name, products.price, products.quantity_in_stock, 
           categories.category_name 
    FROM products 
    LEFT JOIN categories ON products.category_id = categories.category_id";
$result = mysqli_query($conn, $sql);

// Fetch categories from the database
$categoryQuery = "SELECT category_id, category_name FROM categories";
$categoryResult = mysqli_query($conn, $categoryQuery);
$categories = [];

if ($categoryResult) {
    while ($category = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $category; // Store each category in the categories array
    }
}

// Fetch suppliers from the database
$supplierQuery = "SELECT supplier_id, supplier_name FROM suppliers";
$supplierResult = mysqli_query($conn, $supplierQuery);
$suppliers = [];

if ($supplierResult) {
    while ($supplier = mysqli_fetch_assoc($supplierResult)) {
        $suppliers[] = $supplier; // Store each supplier in the suppliers array
    }
}

// Handle form submission to add a new product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $supplier_id = (int)$_POST['supplier_id']; 
    $price = (float)$_POST['price'];
    $quantity_in_stock = (int)$_POST['quantity_in_stock'];

    // Insert the new product into the products table
    $insertQuery = "INSERT INTO products (product_name, description, category_id, supplier_id, price, quantity_in_stock) 
                    VALUES ('$product_name', '$description', $category_id, $supplier_id, $price, $quantity_in_stock)";

    if (mysqli_query($conn, $insertQuery)) {
        // Set a success message in the session
        $_SESSION['success_message'] = "New product added successfully.";
        // Redirect to the same page to refresh and show the new product
        header("Location: products.php");
        exit; // Ensure no further code is executed after the redirect
    } else {
        echo "Error: " . $insertQuery . "<br>" . mysqli_error($conn);
    }
}

// Display success message if it exists
if (isset($_SESSION['success_message'])) {
    echo "<div class='success'>" . $_SESSION['success_message'] . "</div>";
    // Clear the message after displaying it
    unset($_SESSION['success_message']);
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - MON-NITE</title>
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
        
        /* Report Section */
        .report-section {
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, ) 
        }
        #addProductModal {
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
        
        #addProductModal > div {
            background: #fff; /* White background for the modal */
            margin: auto;
            padding: 20px;
            width: 90%;
            max-width: 400px; /* Maximum width for larger screens */
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
        }
        
        #addProductModal h2 {
            margin-top: 0; /* Remove default margin */
            color: #ffdd00; /* Match header color */
        }
        
        #addProductModal label {
            display: block; /* Block display for labels */
            margin: 10px 0 5px; /* Spacing around labels */
            font-weight: bold; /* Bold labels */
            color: #0059b3; /* Match header color */
        }
        
        #addProductModal input[type="text"],
        #addProductModal input[type="date"],
        #addProductModal input[type="number"],
        #addProductModal textarea,
        #addProductModal select {
            width: 100%; /* Full width */
            padding: 10px; /* Padding for input fields */
            border: 1px solid #ddd; /* Light border */
            border-radius: 3px; /* Rounded corners */
            box-sizing: border-box; /* Include padding in width calculation */
            margin-bottom: 10px; /* Space below input fields */
        }
        
        #addProductModal button {
            padding: 10px 15px; /* Padding for buttons */
            background-color: #0059b3; /* Button background color */
            color: #ffdd00; /* Button text color */
            border: none; /* Remove default border */
            border-radius: 3px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition for hover effect */
        }
        
        #addProductModal button:hover {
            background-color: #004080; /* Darker shade on hover */
        }
        
        #addProductModal button[type="button"] {
            background-color: #ccc; /* Different color for cancel button */
        }
        
        #addProductModal button[type="button"]:hover {
            background-color: #aaa; /* Darker shade on hover for cancel button */
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="#" class="logo">MON-NITE PHONES</a>
            <nav class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a style="color: #ffdd00;" href="products.php">Products</a>
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

    <main>
        <section>
            <div class="container">
                <h1>Product List</h1>
                <button onclick="openModal()" class="btn">Add New Product</button>

                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['product_name']; ?></td>
                                    <td><?php echo $row['category_name']; ?></td> <!-- Show category name -->
                                    <td>UGX <?php echo number_format($row['price'], 0); ?></td>
                                    <td><?php echo $row['quantity_in_stock']; ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-warning">Edit</a>
                                        <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </section>
    </main>

    <!-- Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Add New Product</h2>
            </div>
            <form action="products.php" method="POST">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" required>
                <label for="description">Description</label>
                <input type="text" id="description" name="description" required>
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>">
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="supplier_id">Supplier</label>
                <select id="supplier_id" name="supplier_id" required>
                    <option value="">Select a Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['supplier_id']; ?>">
                            <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" required>
                <label for="quantity_in_stock">Stock Quantity</label>
                <input type="number" id="quantity_in_stock" name="quantity_in_stock" required>
                <button type="submit" class="btn">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        function toggleMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
        }
        
        function openModal() {
            document.getElementById("addProductModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("addProductModal").style.display = "none";
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('addProductModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>