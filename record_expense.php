<?php
// Include the database connection
include('db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $expense_type = $_POST['expense_type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = date('Y-m-d H:i:s');
    $recorded_by = 'Admin'; // Change this to dynamic user login

    // Insert expense into the database
    $insert_sql = "INSERT INTO expenses (expense_type, amount, description, date, recorded_by) 
                   VALUES ('$expense_type', $amount, '$description', '$date', '$recorded_by')";
    if (mysqli_query($conn, $insert_sql)) {
        $message = "Expense recorded successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Expense - MON-NITE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Record Expense</h1>
        <?php if (isset($message)) { ?>
            <div class="alert"><?= $message ?></div>
        <?php } ?>
        <form action="record_expense.php" method="POST">
            <div class="form-group">
                <label for="expense_type">Expense Type</label>
                <select id="expense_type" name="expense_type" required>
                    <option value="">Select an expense type</option>
                    <option value="Rent">Rent</option>
                    <option value="Utilities">Utilities</option>
                    <option value="Salary">Salary</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <button type="submit">Record Expense</button>
        </form>
    </div>
</body>
</html>
