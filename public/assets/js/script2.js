// function toggleAllCheckboxes(source) {
//     $('.bill-checkbox').prop('checked', source.checked);
// }
//
// $(document).on('change', '.bill-checkbox', function () {
//     let all = $('.bill-checkbox').length;
//     let checked = $('.bill-checkbox:checked').length;
//     $('#selectAll').prop('checked', all === checked);
// });

function unarchiveSelected() {
    let selectedIds = [];
    $('.bill-checkbox:checked').each(function () {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        alert("Please select at least one bill to unarchive.");
        return;
    }

    if (!confirm("Unarchive selected bills?")) return;

    $.ajax({
        url: '/routes/web.php?route=unarchive',
        method: 'POST',
        data: { ids: selectedIds },
        success: function (response) {
            if (response === "success") {
                alert("Unarchived successfully.");
                location.reload();
            } else {
                alert("Error unarchiving bills.");
            }
        }
    });
}
// function toggleAllCheckboxes(source) {
//     $('.bill-checkbox').prop('checked', source.checked);
// }
//
// $(document).on('change', '.bill-checkbox', function () {
//     let all = $('.bill-checkbox').length;
//     let checked = $('.bill-checkbox:checked').length;
//     $('#selectAll').prop('checked', all === checked);
// });