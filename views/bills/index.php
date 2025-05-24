<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill Management</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Manage Bills</h2>

<div class="top-controls">
    <div class="left-controls">
        <input type="text" id="searchInput" placeholder="Search ">
    </div>
    <div class="right-controls">
        <button onclick="archiveSelected()">Archive</button>
        <button onclick="location.href='index.php?route=create'">Add New Bill</button>
        <button onclick="location.href='index.php?route=archived'">View Archived Items</button>
        <button onclick="$('#massUpdateModal').show()">Mass Update</button>
    </div>
</div>
<div class="show-items">
    <label>Show:
        <input type="number" id="itemsPerPage" value="5" min="1" style="width: 60px; height: 28px; font-size: 13px; padding: 2px 6px;"> Items
    </label>
</div>
<table>
    <thead>
    <tr>
        <th><input type="checkbox" id="selectAll"></th>
        <th>Account</th>
        <th>Bill ID</th>
        <th>Service</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Category</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody class="billTableBody">
    </tbody>
</table>

<div id="pagination" class="pagination"></div>
<div id="massUpdateModal" style="display:none; position:fixed; top:20%; left:30%; background:#fff; padding:20px; border:1px solid #ccc; z-index:999;">
    <h3>Mass Update</h3>
    <label>New Status:</label>
    <select id="massStatus">
        <option value="">--Select--</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
        <option value="Canceled">Canceled</option>
        <option value="Refunded">Refunded</option>
    </select><br>

    <label>New Service:</label>
    <select id="massService">
        <option value="">--Select--</option>
        <option value="Internet Ser">Internet Ser</option>
        <option value="Rent House">Rent House</option>
        <option value="Buy Mobile">Buy Mobile</option>
        <option value="Maintenance">Maintenance</option>
        <option value="Package">Package</option>
    </select><br>

    <label>New Category:</label>
    <select id="massCategory">
        <option value="">--Select--</option>
        <option value="Company">Company</option>
        <option value="Hospital">Hospital</option>
        <option value="Personal customer">Personal customer</option>
    </select><br>

    <label>New Account Number:</label>
    <select id="massAccount">
        <option value="">--Select--</option>
        <option value="AN00100">AN00100</option>
        <option value="AN00101">AN00101</option>
        <option value="AN00102">AN00102</option>
    </select><br>

    <label>New Bill ID:</label>
    <input type="text" id="massID"><br>

    <label>New Amount:</label>
    <input type="number" id="massAmount"><br><br>


    <button onclick="submitMassUpdate()">Update</button>
    <button onclick="$('#massUpdateModal').hide()">Cancel</button>
</div>


<script src="/assets/js/script1.js"></script>


</body>
</html>
