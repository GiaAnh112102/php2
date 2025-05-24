<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Bill</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<form method="POST" action="index.php?route=create">
    <label>Account Number:</label><br>
    <select name="account_number" required>
        <option value="AN00100">AN00100</option>
        <option value="AN00101">AN00101</option>
        <option value="AN00102">AN00102</option>
    </select><br>

    <label>Bill ID:</label><br>
    <input type="text" name="bill_id" required><br>

    <label>Service:</label><br>
    <select name="service" required>
        <option value="Internet Ser">Internet Ser</option>
        <option value="Rent House">Rent House</option>
        <option value="Buy Mobile">Buy Mobile</option>
        <option value="Maintenance">Maintenance</option>
        <option value="Package">Package</option>
    </select><br>

    <label>Amount:</label><br>
    <input type="number" step="0.01" name="amount" required><br>

    <label>Status:</label><br>
    <select name="payment_status" required>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <option value="Canceled">Canceled</option>
        <option value="Refunded">Refunded</option>
    </select><br>

    <label>Category:</label><br>
    <select name="category" required>
        <option value="Company">Company</option>
        <option value="Hospital">Hospital</option>
        <option value="Personal customer">Personal customer</option>
    </select><br><br>

    <button type="submit">Save</button>
    <button type="button" onclick="location.href='index.php'">Cancel</button>
</form>
<?php if (!empty($error)): ?>
    <script>
        alert("<?= addslashes($error) ?>");
    </script>
<?php endif; ?>
