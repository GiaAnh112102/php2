let currentPage = 1;
let itemsPerPage = 5;
let isGlobalSelectAll = false;
let selectedIds = new Set();

function fetchData() {
    const searchQuery = $('#searchInput').val();
    $.get('index.php?route=fetch', {
        search: searchQuery,
        itemsPerPage: itemsPerPage,
        page: currentPage
    }, function (response) {
        const res = JSON.parse(response);
        renderTable(res.data);
        renderPagination(res.totalPages, res.currentPage);
        updateSelectAllCheckbox();
    });
}




function renderTable(data) {
    let tbody = $('.billTableBody');
    tbody.empty();
    data.forEach(row => {
        const isChecked = isGlobalSelectAll || selectedIds.has(row.id.toString());
        tbody.append(`
            <tr>
                <td><input type="checkbox" class="bill-checkbox" value="${row.id}" ${isChecked ? 'checked' : ''}></td>
                    <td class="editable" data-id="${row.id}" data-field="bill_id">${row.bill_id}</td>
                <td class="editable" data-id="${row.id}" data-field="amount">${row.amount}</td>
                <td class="editable" data-id="${row.id}" data-field="account_number">${row.account_number}</td>
                <td class="editable" data-id="${row.id}" data-field="service">${row.service}</td>
                <td class="editable" data-id="${row.id}" data-field="payment_status">${row.payment_status}</td>
                <td class="editable" data-id="${row.id}" data-field="category">${row.category}</td>
                <td>
                    <a href="index.php?route=edit&id=${row.id}">Edit</a> /
                    <a href="index.php?route=delete&id=${row.id}" onclick="return confirm('Delete?')">Delete</a>

                </td>
            </tr>
        `);
    });
}

$(document).on('change', '.bill-checkbox', function () {
    const id = $(this).val();
    if ($(this).is(':checked')) {
        selectedIds.add(id);
    } else {
        selectedIds.delete(id);
        isGlobalSelectAll = false;
    }
    updateSelectAllCheckbox();
});

$('#selectAll').on('change', function () {
    isGlobalSelectAll = $(this).is(':checked');
    selectedIds.clear();

    if (isGlobalSelectAll) {
        $.get('/routes/web.php?route=get_all_ids', function (response) {
            const ids = JSON.parse(response);
            ids.forEach(id => selectedIds.add(id.toString()));
            fetchData();
        });
    } else {
        fetchData();
    }
});

function updateSelectAllCheckbox() {
    $.get('/routes/web.php?route=get_total_count', function (totalCount) {
        const selectedCount = selectedIds.size;
        const total = parseInt(totalCount);
        $('#selectAll').prop('checked', selectedCount > 0 && selectedCount === total);
    });
}

function renderPagination(totalPages, current) {
    let pagination = $('#pagination');
    pagination.empty();
    if (totalPages <= 0) return;
    pagination.append(`<button class="page-button" ${current === 1 ? 'disabled' : ''} onclick="changePage(${current - 1})">← Prev</button>`);
    for (let i = 1; i <= totalPages; i++) {
        pagination.append(`<button class="page-button" ${i === current ? 'disabled' : ''} onclick="changePage(${i})">${i}</button>`);
    }
    pagination.append(`<button class="page-button" ${current === totalPages ? 'disabled' : ''} onclick="changePage(${current + 1})">Next →</button>`);
}

function changePage(page) {
    currentPage = page;
    fetchData();
}

function archiveSelected() {
    if (!isGlobalSelectAll && selectedIds.size === 0) {
        alert("Please select at least one bill.");
        return;
    }
    if (!confirm("Are you sure you want to archive the selected bills?")) return;

    $.post('/routes/web.php?route=archive', {
        ids: isGlobalSelectAll ? 'ALL' : Array.from(selectedIds)
    }, function (response) {
        if (response === "success") {
            alert("Archived successfully.");
            selectedIds.clear();
            isGlobalSelectAll = false;
            fetchData();
        } else {
            alert("Archive failed.");
        }
    });
}

$('#searchInput').on('input', function () {
    currentPage = 1;
    fetchData();
});

$('#itemsPerPage').on('change', function () {
    itemsPerPage = parseInt($(this).val());
    currentPage = 1;
    fetchData();
});

$(document).ready(function () {
    fetchData();
});
function submitMassUpdate() {
    const status = $('#massStatus').val();
    const service = $('#massService').val();
    const category = $('#massCategory').val();
    const BillID = $('#massID').val();
    const Amount = $('#massAmount').val();
    const AccountNumber = $('#massAccount').val();

    if (!isGlobalSelectAll && selectedIds.size === 0) {
        alert("Please select at least one bill to update.");
        return;
    }

    if (!status && !service && !category && !BillID && !Amount && !AccountNumber) {
        alert("Please select at least one field to update.");
        return;
    }

    let idsToUpdate = isGlobalSelectAll ? 'ALL' : Array.from(selectedIds);

    $.ajax({
        url: 'index.php?route=mass_update',
        type: 'POST',
        data: {
            ids: idsToUpdate,
            payment_status: status,
            service: service,
            account_number: AccountNumber,
            bill_id: BillID,
            amount: Amount,
            category: category
        },
        success: function (response) {
            if (response.trim() === 'success') {
                alert("Mass update successful!");
                selectedIds.clear();
                isGlobalSelectAll = false;
                $('#massUpdateModal').hide();
                fetchData();
            } else {
                alert("Mass update failed: " + response);
            }
        },
        error: function () {
            alert("Error occurred during mass update.");
        }
    });
}

//Inline Editing
$(document).on('click', '.editable', function () {
    const td = $(this);
    const field = td.data('field');
    const id = td.data('id');
    const original = td.text().trim();

    let inputHTML;

    const selectOptions = {
        account_number: ["AN00100", "AN00101", "AN00102"],
        service: ["Internet Ser", "Rent House", "Buy Mobile", "Maintenance", "Package"],
        payment_status: ["Pending", "Completed", "Canceled", "Refunded"],
        category: ["Company", "Hospital", "Personal customer"]
    };

    if (["bill_id", "amount"].includes(field)) {
        inputHTML = `<input type="${field === 'amount' ? 'number' : 'text'}" class="inline-input" value="${original}">`;
    } else {
        inputHTML = `<select class="inline-select">` +
            selectOptions[field].map(opt =>
                `<option value="${opt}" ${opt === original ? 'selected' : ''}>${opt}</option>`
            ).join('') + `</select>`;
    }

    td.html(inputHTML);
    td.find('input, select').focus();
});

$(document).on('blur change', '.inline-input, .inline-select', function () {
    const input = $(this);
    const td = input.closest('td');
    const id = td.data('id');
    const field = td.data('field');
    const value = input.val();

    $.post('index.php?route=inlineUpdate', { id, field, value }, function (res) {
        if (res === 'success') {
            td.html(value);
        } else {
            alert("Update failed.");
            td.html(value); // fallback
        }
    });
});

