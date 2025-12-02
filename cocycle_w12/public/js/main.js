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
document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // BAGIAN A: LOGIKA AUTH (Khusus Halaman Sign Up)
    // ============================================================
    const profileInput = document.getElementById('profilePicInput');
    const profilePreview = document.getElementById('profilePicPreview');

    if (profileInput && profilePreview) {
        profileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
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

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sessionStorage.removeItem("user");
            localStorage.removeItem("user");
            
            // Cek kita lagi dimana untuk nentuin arah logout
            const currentPath = window.location.pathname;
            if (currentPath.includes('/backend/')) {
                window.location.href = "logout.php";
            } else {
                window.location.href = "backend/logout.php";
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
            if(imgEl) imgEl.src = data.image;
            
            const titleEl = popupView.querySelector('.info h3');
            if(titleEl) titleEl.textContent = data.name;

            // ... isi sisa data (aman kalau elemen ga ketemu ga error)
            const catEl = popupView.querySelector('.info .category'); if(catEl) catEl.textContent = data.category;
            const resEl = popupView.querySelector('.info .restaurant'); if(resEl) resEl.textContent = data.restaurant;
            const ratEl = popupView.querySelector('.info .rating'); if(ratEl) ratEl.textContent = `⭐ ${data.rating}`;
            const disEl = popupView.querySelector('.info .distance'); if(disEl) disEl.textContent = `📍 ${data.distance}`;
            const priEl = popupView.querySelector('.info .price'); if(priEl) priEl.textContent = data.price;
            const desEl = popupView.querySelector('.info .popup-description'); if(desEl) desEl.textContent = data.description;

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