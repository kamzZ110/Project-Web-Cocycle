<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Same head as index -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --green-sage: #8A9A5B; --bg-site: #fafafaff; }
        body { background-color: var(--bg-site); font-family: sans-serif; }
        .header-section { background: white; padding: 15px 0; border-bottom: 1px solid #eee; }
        .upload-box { width: 100%; height: 300px; border: 2px dashed #ccc; border-radius: 20px; background: white; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; transition: 0.3s; }
        .upload-box:hover { border-color: var(--green-sage); background: #fcfcfc; }
        .upload-box img { width: 100%; height: 100%; object-fit: cover; position: absolute; display: none; }
        .btn-simpan { background-color: var(--green-sage); color: white; padding: 15px 80px; border-radius: 30px; font-weight: 700; border: none; transition: 0.3s; text-transform: uppercase; }
        .btn-simpan:hover { background-color: #7a8a4f; }
        .input-group-text { background: white; border-right: none; border-radius: 12px 0 0 12px; font-weight: bold; color: var(--green-sage); }
        .price-input { border-left: none; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
</head>
<body>
@include('partials.feedback')

    <div class="header-section">
        <div class="container-fluid px-5 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                 <a href="{{ route('partner.products') }}" class="text-dark me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-shop me-2"></i>{{ strtoupper($nama_toko) }}</h5>
            </div>
            <div class="d-flex align-items-center">
                <span class="me-2 fw-bold small">User</span>
                <i class="bi bi-person-circle fs-3 text-secondary"></i>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 form-container mb-5 mt-4">
        <h3 class="fw-bold mb-4">Tambah Produk</h3>
        
        <form action="{{ route('partner.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            <div class="row g-5">
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-4 text-success">Media Produk</h5>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Foto Produk</label>
                        <div class="upload-box" onclick="document.getElementById('foto_produk').click()">
                            <img id="preview" src="#" alt="Preview">
                            <div class="upload-placeholder" id="placeholder-content">
                                <i class="bi bi-cloud-upload fs-1 text-success"></i>
                                <p class="mb-0 mt-2 fw-bold">Klik untuk Upload</p>
                                <small>Format JPG/PNG</small>
                            </div>
                        </div>
                        <input type="file" name="image" id="foto_produk" hidden accept="image/*" onchange="previewImage(event)">
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Nama Produk *</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Mie Ayam Spesial" required>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Kategori *</label>
                        <select name="category" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Bakery & Pastry">Bakery & Pastry</option>
                            <option value="Vegetables & Fruits">Vegetables & Fruits</option>
                            <option value="Drinks">Drinks</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Main Course">Main Course</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-7">
                    <h5 class="fw-bold mb-4 text-success">Keterangan Detail</h5>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Harga Jual *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" min="0" step="0.01" max="99999999.99" class="form-control price-input" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Stok *</label>
                        <input type="number" name="stock" min="0" max="1000000" class="form-control" placeholder="0" value="0" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Jelaskan keunggulan produkmu di sini..."></textarea>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn-simpan">Simpan Produk</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('preview');
                const placeholder = document.getElementById('placeholder-content');
                output.src = reader.result;
                output.style.display = 'block';
                placeholder.style.display = 'none';
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>
