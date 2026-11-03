<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - CoCycle</title>
    <!-- Styles same as create -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --green-sage: #8A9A5B; --bg-site: #fafafaff; }
        body { background-color: var(--bg-site); font-family: sans-serif; }
        .header-section { background: white; padding: 15px 0; border-bottom: 1px solid #eee; }
        .upload-box { width: 100%; height: 300px; border: 2px dashed #ccc; border-radius: 20px; background: white; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; position: relative; overflow: hidden; transition: 0.3s; }
        .upload-box:hover { border-color: var(--green-sage); background: #fcfcfc; }
        .upload-box img { width: 100%; height: 100%; object-fit: cover; position: absolute; }
        .btn-simpan { background-color: var(--green-sage); color: white; padding: 15px 80px; border-radius: 30px; font-weight: 700; border: none; transition: 0.3s; text-transform: uppercase; }
        .btn-simpan:hover { background-color: #7a8a4f; }
    </style>
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
        <h3 class="fw-bold mb-4">Edit Produk</h3>
        
        <form action="{{ route('partner.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST') <!-- Should be POST in Laravel if not using PUT? Actually PUT is better but POST works if method spoofing. -->
            <!-- Laravel form spoofing -->
            <!-- <input type="hidden" name="_method" value="POST">  Actually updateProduct uses Request, let's keep it simple POST unless we registered PUT route -->

            <div class="row g-5">
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-4 text-success">Media Produk</h5>
                    
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Foto Produk</label>
                        <div class="upload-box" onclick="document.getElementById('foto_produk').click()">
                            @if($product->image)
                                <img id="preview" src="{{ asset($product->image) }}" alt="Preview" style="display:block;">
                            @else
                                <img id="preview" src="#" alt="Preview" style="display:none;">
                            @endif
                            <div class="upload-placeholder" id="placeholder-content" style="{{ $product->image ? 'display:none' : '' }}">
                                <i class="bi bi-cloud-upload fs-1 text-success"></i>
                                <p class="mb-0 mt-2 fw-bold">Klik untuk Ubah</p>
                            </div>
                        </div>
                        <input type="file" name="image" id="foto_produk" hidden accept="image/*" onchange="previewImage(event)">
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Nama Produk *</label>
                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Kategori *</label>
                        <select name="category" class="form-control" required>
                            @foreach(['Bakery & Pastry', 'Vegetables & Fruits', 'Drinks', 'Snacks', 'Main Course'] as $cat)
                                <option value="{{ $cat }}" {{ $product->category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="mb-4">
                        <label class="fw-bold mb-2">Harga Jual *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price" min="0" step="0.01" max="99999999.99" class="form-control price-input" value="{{ $product->price }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Stok *</label>
                        <input type="number" name="stock" min="0" max="1000000" class="form-control" value="{{ $product->stock }}" required>
                    </div>
                    
                     <div class="mb-4">
                        <label class="fw-bold mb-2">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="5">{{ $product->description }}</textarea>
                    </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn-simpan">Update Produk</button>
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
