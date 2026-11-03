// main.js - Gabungan Semua Logika Javascript

// --- 1. FUNGSI GLOBAL (Bisa dipanggil lewat onclick di HTML) ---
function openDonation() {
    alert("Fitur donasi akan segera hadir!");
}

function openNews(id) {
    const modalElement = document.getElementById('detailModal');
    if (modalElement) {
        const dm = new bootstrap.Modal(modalElement);
        document.getElementById('detailTitle').innerText = 'News & Promo - #' + id;
        document.getElementById('detailBody').innerHTML = '<p>Isi berita lengkap untuk item nomor ' + id + '...</p>';
        dm.show();
    }
}

function openTip(id) {
    const modalElement = document.getElementById('detailModal');
    if (modalElement) {
        const dm = new bootstrap.Modal(modalElement);
        document.getElementById('detailTitle').innerText = 'Food Storage Tip - #' + id;
        document.getElementById('detailBody').innerHTML = '<p>Tips penyimpanan makanan nomor ' + id + '...</p>';
        dm.show();
    }
}

// --- 2. EVENT LISTENER UTAMA (Saat Website Selesai Loading) ---
document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // BAGIAN A: LOGIKA AUTH (Khusus Halaman Sign Up)
    // ============================================================
    const profileInput = document.getElementById('profilePicInput');
    const profilePreview = document.getElementById('profilePicPreview');

    if (profileInput && profilePreview) {
        profileInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    profilePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // ============================================================
    // BAGIAN B: LOGIKA PROFILE & LOGOUT (Homepage/Global)
    // ============================================================
    const profileBtn = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');

    if (profileBtn && profileMenu) {
        let profileOpen = false;
        profileBtn.addEventListener('click', (e) => {
            profileOpen = !profileOpen;
            profileMenu.style.display = profileOpen ? 'block' : 'none';
            profileBtn.setAttribute('aria-expanded', profileOpen);
        });

        document.addEventListener('click', (e) => {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.style.display = 'none';
                profileOpen = false;
                profileBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Bagian profil pic
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');

    if (editProfileBtn && editProfileModal) {
        editProfileBtn.addEventListener('click', (e) => {
            e.preventDefault();

            if (profileMenu) profileMenu.style.display = 'none';

            if (profileMenu) profileMenu.style.display = 'none';

            fetch('/profile/data')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('modalUsername').value = data.username || '';
                        document.getElementById('modalEmail').value = data.email || '';
                        document.getElementById('modalPhone').value = data.phone || '';
                        document.getElementById('modalFullname').value = data.fullname || '';
                        document.getElementById('modalAddress').value = data.address || '';
                    } else {
                        alert('Error loading profile data');
                    }
                })
                .catch(err => {
                    console.error('Error fetching profile:', err);
                    alert('Error loading profile data');
                });

            const modal = new bootstrap.Modal(editProfileModal);
            modal.show();
        });
    }

    const modalEditProfileForm = document.getElementById('modalEditProfileForm');
    if (modalEditProfileForm) {
        modalEditProfileForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('username', document.getElementById('modalUsername').value);
            formData.append('phone', document.getElementById('modalPhone').value);
            formData.append('fullname', document.getElementById('modalFullname').value);
            formData.append('address', document.getElementById('modalAddress').value);

            const messageDiv = document.getElementById('editProfileMessage');
            messageDiv.innerHTML = '<div class="alert alert-info alert-dismissible fade show" role="alert">Saving...</div>';

            try {
                const response = await fetch('/profile/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const text = await response.text();
                const result = JSON.parse(text);

                if (result.status === 'success') {
                    messageDiv.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">Profile updated successfully!</div>';

                    // Update username di header
                    const usernameSpan = document.querySelector('.profile-btn span');
                    if (usernameSpan) usernameSpan.textContent = result.username;

                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' + result.message + '</div>';
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error: ' + error.message + '</div>';
            }
        });
    }

    // Ngirim profil pics
    const modalProfilePicInput = document.getElementById('modalProfilePicInput');
    const modalProfilePicImg = document.getElementById('modalProfilePicImg');

    if (modalProfilePicInput) {
        modalProfilePicInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // Ukuran file
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit');
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Allowed: JPG, PNG, GIF, WebP');
                return;
            }

            // Preview foto
            const reader = new FileReader();
            reader.onload = (e) => {
                modalProfilePicImg.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload foto profil
            const formData = new FormData();
            formData.append('profile_pic', file);

            const messageDiv = document.getElementById('editProfileMessage');
            messageDiv.innerHTML = '<div class="alert alert-info">Uploading...</div>';

            try {
                const response = await fetch('/profile/photo', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const text = await response.text();
                const result = JSON.parse(text);

                if (result.status === 'success') {
                    messageDiv.innerHTML = '<div class="alert alert-success">Picture updated successfully!</div>';
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger">' + result.message + '</div>';
                }
            } catch (error) {
                console.error('Upload error:', error);
                messageDiv.innerHTML = '<div class="alert alert-danger">Upload failed: ' + error.message + '</div>';
            }
        });
    }



    // ============================================================
    // BAGIAN C: LOGIKA SEARCH, DROPDOWN, & POPUP (Fitur Lama Kamu)
    // ============================================================

    // 1. Dropdown Category
    const dropdowns = document.getElementsByClassName("category-drop");
    if (dropdowns.length > 0) {
        for (let i = 0; i < dropdowns.length; i++) {
            dropdowns[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }
            });
        }
    }

    // 2. Search Functionality
    const searchContainer = document.querySelector('.search-container');
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');

    // Hanya jalankan kalau elemen search ada di halaman ini
    if (searchContainer && searchBtn && searchInput) {

        function performSearch(query) {
            const q = String(query || '').trim().toLowerCase();
            const cards = document.querySelectorAll('.product-card');
            if (!cards) return;

            let anyVisible = false;
            cards.forEach(card => {
                const titleEl = card.querySelector('.card-title');
                const textEl = card.querySelector('.card-text');
                const title = titleEl ? titleEl.textContent.toLowerCase() : '';
                const text = textEl ? textEl.textContent.toLowerCase() : '';

                if (q === '' || title.includes(q) || text.includes(q)) {
                    card.style.display = ''; // Tampilkan
                    anyVisible = true;
                } else {
                    card.style.display = 'none'; // Sembunyikan
                }
            });

            // Pesan 'No results'
            let noResults = document.querySelector('.no-results-message');
            if (!anyVisible) {
                if (!noResults) {
                    noResults = document.createElement('div');
                    noResults.className = 'no-results-message text-center text-muted my-4';
                    noResults.textContent = 'No products found.';
                    const main = document.querySelector('main');
                    if (main) main.appendChild(noResults);
                }
            } else if (noResults) {
                noResults.remove();
            }
        }

        searchContainer.addEventListener('click', (e) => e.stopPropagation());

        searchBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (!searchContainer.classList.contains('active')) {
                searchContainer.classList.add('active');
                searchInput.focus();
                return;
            }
            const query = searchInput.value.trim();
            performSearch(query);
            searchInput.focus();
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(searchInput.value.trim());
            }
        });

        // Tutup search kalau klik diluar atau tekan ESC
        document.addEventListener('click', () => {
            if (searchContainer.classList.contains('active')) searchContainer.classList.remove('active');
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchContainer.classList.contains('active')) {
                searchContainer.classList.remove('active');
            }
        });
    }

    // 3. Popup Produk
    const popupView = document.querySelector('.popup-view');
    const viewButtons = document.querySelectorAll('.view-btn');
    const closeButton = document.querySelector('.close-btn');

    if (popupView && closeButton && viewButtons.length > 0) {
        const showPopup = function (e) {
            e.preventDefault();
            const card = e.currentTarget.closest('.product-card');
            if (!card) return;

            const data = {
                name: card.dataset.name || 'Product Name',
                category: card.dataset.category || 'Category',
                price: card.dataset.price || '$0.00',
                rating: card.dataset.rating || '-',
                distance: card.dataset.distance || '-',
                restaurant: card.dataset.restaurant || 'Unknown',
                description: card.dataset.description || 'No description available.',
                image: card.querySelector('img')?.src || ''
            };

            // Masukkan data ke elemen popup
            // (Pastikan class HTML di popup sesuai dengan selector ini)
            const imgEl = popupView.querySelector('.product-img img');
            if (imgEl) imgEl.src = data.image;

            const titleEl = popupView.querySelector('.info h3');
            if (titleEl) titleEl.textContent = data.name;

            // ... isi sisa data (aman kalau elemen ga ketemu ga error)
            const catEl = popupView.querySelector('.info .category'); if (catEl) catEl.textContent = data.category;
            const resEl = popupView.querySelector('.info .restaurant'); if (resEl) resEl.textContent = data.restaurant;
            const ratEl = popupView.querySelector('.info .rating'); if (ratEl) ratEl.textContent = `⭐ ${data.rating}`;
            const disEl = popupView.querySelector('.info .distance'); if (disEl) disEl.textContent = `📍 ${data.distance}`;
            const priEl = popupView.querySelector('.info .price'); if (priEl) priEl.textContent = data.price;
            const desEl = popupView.querySelector('.info .popup-description'); if (desEl) desEl.textContent = data.description;

            popupView.classList.add('active');
        };

        const hidePopup = function () {
            popupView.classList.remove('active');
        };

        viewButtons.forEach((btn) => {
            btn.addEventListener('click', showPopup);
        });

        closeButton.addEventListener('click', hidePopup);
    }

});