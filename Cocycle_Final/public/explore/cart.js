function addToCartDB(name, price, image) {

    let cleanPrice = price.toString().replace(/[^0-9]/g, '');

    $.ajax({
        url: 'cart.php',
        type: 'POST',
        data: {
            action: 'add',
            name: name,
            price: cleanPrice,
            image: image
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 'success') {
                alert("✅ " + response.message);
            } else {
                alert("❌ Gagal: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
            if (xhr.status === 401 || xhr.responseText.includes('Login')) {
                alert("⚠️ Anda harus login dulu untuk belanja!");
                window.location.href = '../backend/login.php';
            } else {
                alert("Terjadi kesalahan koneksi.");
            }
        }
    });
}
