<?php
include('db_connection.php');

// Fetch all expenses
$expenses_sql = "SELECT * FROM expenses ORDER BY date DESC";
$expenses_result = mysqli_query($conn, $expenses_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses - MON-NITE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>View Expenses</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($expenses_result)) { ?>
                    <tr>
                        <td><?= $row['expense_id'] ?></td>
                        <td><?= $row['expense_type'] ?></td>
                        <td><?= $row['amount'] ?></td>
                        <td><?= $row['description'] ?></td>
                        <td><?= $row['date'] ?></td>
                        <td><?= $row['recorded_by'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
