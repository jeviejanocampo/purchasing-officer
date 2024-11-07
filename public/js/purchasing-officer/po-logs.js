document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const logsTable = document.getElementById('logs-table');
    const clearButton = document.getElementById('clear-button');

    function filterLogs() {
        const searchValue = searchInput.value.toLowerCase();
        const startDateValue = startDate.value ? new Date(startDate.value) : null;
        const endDateValue = endDate.value ? new Date(endDate.value) : null;

        const rows = document.querySelectorAll('.log-row');
        rows.forEach(row => {
            const logData = row.cells[1].textContent.toLowerCase();
            const createdAt = new Date(row.cells[2].textContent);

            let showRow = true;

            if (searchValue && !logData.includes(searchValue)) {
                showRow = false;
            }

            if (startDateValue && createdAt < startDateValue) {
                showRow = false;
            }

            if (endDateValue && createdAt > endDateValue) {
                showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Add event listeners for automatic filtering
    searchInput.addEventListener('input', filterLogs);
    startDate.addEventListener('change', filterLogs);
    endDate.addEventListener('change', filterLogs);

    // Clear button functionality
    clearButton.addEventListener('click', () => {
        searchInput.value = '';
        startDate.value = '';
        endDate.value = '';
        filterLogs(); // Reset the filter
    });
});
