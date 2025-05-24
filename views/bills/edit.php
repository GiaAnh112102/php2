<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Bill</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<h2>Edit Bill</h2>

<form method="POST" action="index.php?route=update&id=<?= $bill['id'] ?>">
    <!-- Account Number -->
    <label>Account Number:</label><br>
    <select name="account_number" required>
        <?php foreach (["AN00100", "AN00101", "AN00102"] as $acc): ?>
            <option value="<?= $acc ?>" <?= $bill['account_number'] === $acc ? 'selected' : '' ?>><?= $acc ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Bill ID:</label><br>
    <input type="text" name="bill_id" value="<?= $bill['bill_id'] ?>" required><br>

    <label>Service:</label><br>
    <select name="service" required>
        <?php foreach (["Internet Ser", "Rent House", "Buy Mobile", "Maintenance", "Package"] as $svc): ?>
            <option value="<?= $svc ?>" <?= $bill['service'] === $svc ? 'selected' : '' ?>><?= $svc ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Amount:</label><br>
    <input type="number" step="0.01" name="amount" value="<?= $bill['amount'] ?>" required><br>

    <label>Status:</label><br>
    <select name="payment_status">
        <?php foreach (["Pending", "Completed", "Canceled", "Refunded"] as $status): ?>
            <option value="<?= $status ?>" <?= $bill['payment_status'] === $status ? 'selected' : '' ?>><?= $status ?></option>
        <?php endforeach; ?>
    </select><br>

    <label>Category:</label><br>
    <select name="category">
        <?php foreach (["Company", "Hospital", "Personal customer"] as $cat): ?>
            <option value="<?= $cat ?>" <?= $bill['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Update</button>
    <button type="button" onclick="location.href='index.php'">Back</button>

</form>

</body>
</html>
