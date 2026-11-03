function addToCartDB(productId) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) { window.location.assign('/login'); return; }
    fetch('/cart/add', {
        method: 'POST',
        headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token},
        body: JSON.stringify({product_id: productId})
    }).then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Tidak dapat menambahkan produk.');
        alert(data.message);
    }).catch(error => alert(error.message));
}