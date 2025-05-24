<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Bills</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Archived Bills</h2>

<div class="top-controls">
    <div class="left-controls">
        <input type="text" id="searchInput" placeholder="Search">
    </div>
    <div class="right-controls">
        <button onclick="location.href='index.php'">&larr; Back to Active Bills</button>
        <button id="unarchiveBtn">Unarchive Selected</button>
    </div>
</div>

<div class="show-items">
    <label>Show:
        <input type="number" id="itemsPerPage" value="5" min="1" style="width: 60px;"> Items
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
    </tr>
    </thead>
    <tbody class="billTableBody"></tbody>
</table>

<div id="pagination" class="pagination"></div>

<script>
    let currentPage = 1;
    let itemsPerPage = 5;

    function fetchData() {
        const search = $('#searchInput').val();
        $.get('index.php?route=fetch&archived=1', {
            page: currentPage,
            itemsPerPage: itemsPerPage,
            search: search
        }, function (res) {
            const { data, totalPages, currentPage: page } = JSON.parse(res);
            renderTable(data);
            renderPagination(totalPages, page);
        });
    }

    function renderTable(data) {
        const tbody = $('.billTableBody');
        tbody.empty();
        data.forEach(row => {
            tbody.append(`
                <tr>
                    <td><input type="checkbox" class="bill-checkbox" value="${row.id}"></td>
                    <td>${row.account_number}</td>
                    <td>${row.bill_id}</td>
                    <td>${row.service}</td>
                    <td>$${parseFloat(row.amount).toFixed(2)}</td>
                    <td>${row.payment_status}</td>
                    <td>${row.category}</td>
                </tr>
            `);
        });
    }

    function renderPagination(totalPages, current) {
        const pagination = $('#pagination');
        pagination.empty();
        if (totalPages <= 1) return;

        pagination.append(`<button ${current === 1 ? 'disabled' : ''} onclick="changePage(${current - 1})">&larr; Prev</button>`);
        for (let i = 1; i <= totalPages; i++) {
            pagination.append(`<button ${i === current ? 'disabled' : ''} onclick="changePage(${i})">${i}</button>`);
        }
        pagination.append(`<button ${current === totalPages ? 'disabled' : ''} onclick="changePage(${current + 1})">Next &rarr;</button>`);
    }

    function changePage(page) {
        currentPage = page;
        fetchData();
    }

    function updateSelectAllState() {
        const all = $('.bill-checkbox').length;
        const checked = $('.bill-checkbox:checked').length;
        $('#selectAll').prop('checked', all > 0 && all === checked);
    }

    $(document).on('change', '.bill-checkbox', updateSelectAllState);

    $('#selectAll').on('change', function () {
        $('.bill-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#unarchiveBtn').on('click', function () {
        const ids = $('.bill-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (ids.length === 0) {
            alert("Please select at least one bill to unarchive.");
            return;
        }

        if (!confirm("Unarchive selected bills?")) return;

        $.post('index.php?route=unarchive', { ids: ids }, function (res) {
            if (res.trim() === 'success') {
                alert("Unarchived successfully.");
                fetchData();
            } else {
                alert("Unarchive failed.");
            }
        });
    });

    $('#searchInput').on('input', function () {
        currentPage = 1;
        fetchData();
    });

    $('#itemsPerPage').on('change', function () {
        itemsPerPage = parseInt($(this).val());
        currentPage = 1;
        fetchData();
    });

    $(document).ready(fetchData);
</script>

</body>
</html>
