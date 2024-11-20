<?php
include('db_connection.php');

// Calculate total sales
$sales_sql = "SELECT SUM(total_amount) AS total_sales FROM sales";
$sales_result = mysqli_query($conn, $sales_sql);
$sales_row = mysqli_fetch_assoc($sales_result);
$total_sales = $sales_row['total_sales'] ?? 0;

// Calculate total expenses
$expenses_sql = "SELECT SUM(amount) AS total_expenses FROM expenses";
$expenses_result = mysqli_query($conn, $expenses_sql);
$expenses_row = mysqli_fetch_assoc($expenses_result);
$total_expenses = $expenses_row['total_expenses'] ?? 0;

// Calculate cash position
$cash_position = $total_sales - $total_expenses;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Position - MON-NITE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Cash Position</h1>
        <p>Total Sales: <strong>$<?= number_format($total_sales, 2) ?></strong></p>
        <p>Total Expenses: <strong>$<?= number_format($total_expenses, 2) ?></strong></p>
        <p>Current Cash Position: <strong>$<?= number_format($cash_position, 2) ?></strong></p>
    </div>
</body>
</html>
