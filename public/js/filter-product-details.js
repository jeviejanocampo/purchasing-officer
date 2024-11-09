// C:\xampp\htdocs\purchasing-officer\public\js\filter-product-details.js

document.addEventListener("DOMContentLoaded", function () {
    // Get the search input and date range filters
    const searchInput = document.getElementById("search");
    const startDateInput = document.getElementById("start-date");
    const endDateInput = document.getElementById("end-date");
    const productTable = document.getElementById("product-table");
    const productBody = document.getElementById("product-body");

    // Function to filter the products based on the search input and date range
    function filterProducts() {
        const searchQuery = searchInput.value.toLowerCase();
        const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
        const endDate = endDateInput.value ? new Date(endDateInput.value) : null;

        const rows = productBody.querySelectorAll("tr");

        rows.forEach((row) => {
            const productId = row.querySelector("td:nth-child(1)").textContent.toLowerCase();
            const productName = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
            const createdAt = new Date(row.querySelector("td:nth-child(4)").textContent);

            // Check if the product matches the search query
            const matchesSearch =
                productId.includes(searchQuery) || productName.includes(searchQuery);

            // Check if the product matches the date range
            const matchesDateRange =
                (!startDate || createdAt >= startDate) && (!endDate || createdAt <= endDate);

            // Toggle row visibility based on filters
            if (matchesSearch && matchesDateRange) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Add event listeners for filters
    searchInput.addEventListener("input", filterProducts);
    startDateInput.addEventListener("change", filterProducts);
    endDateInput.addEventListener("change", filterProducts);
});
