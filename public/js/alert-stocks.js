const lowStockWarnings = new Set(); // Set to track already warned products

function checkLowStock(stock, productName) {
    if (stock <= 10 && !lowStockWarnings.has(productName)) {
        lowStockWarnings.add(productName); // Mark this product as warned
        swal({
            title: "Caution!",
            text: "Stocks are low for " + productName + ": " + stock + " remaining.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        });
    }
}
