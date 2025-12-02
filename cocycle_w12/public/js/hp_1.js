// --- BAGIAN 1: FUNGSI GLOBAL ---
// (Ditaruh di luar DOMContentLoaded supaya bisa dipanggil lewat onclick HTML)

function getLoggedInUser() {
    let userData = sessionStorage.getItem("user");
    if (!userData) {
        userData = localStorage.getItem("user");
    }
    if (userData) {
        return JSON.parse(userData);
    }
    return null;
}

function openDonation() {
    alert("Fitur donasi akan segera hadir!");
}

function openNews(id) {
    const modalElement = document.getElementById('detailModal');
    if (modalElement) {
        // Menggunakan Bootstrap Modal
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

// --- BAGIAN 2: EVENT LISTENERS (Saat Website Selesai Loading) ---
document.addEventListener('DOMContentLoaded', () => {

    // 1. CEK LOGIN (Optional, karena sudah dijaga PHP)
    // const user = getLoggedInUser();
    // if (!user) {
        // console.log("User belum login (JS Check)");
        // alert("Anda harus login untuk mengakses halaman ini.");
        // window.location.href = "backend/login.php"; // Arahkan ke login PHP
        // return;
    // } else {
    //    console.log("Selamat datang,", user.username);
    // }

    // 2. LOGIC PROFILE MENU (BARU)
    const profileBtn = document.getElementById('profileBtn');
    const profileMenu = document.getElementById('profileMenu');
    let profileOpen = false;

    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            profileOpen = !profileOpen;
            profileMenu.style.display = profileOpen ? 'block' : 'none';
            profileBtn.setAttribute('aria-expanded', profileOpen);
        });
    }

    // Tutup profile menu kalau klik di luar
    document.addEventListener('click', (e) => {
        if (profileBtn && profileMenu && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
            profileMenu.style.display = 'none';
            profileOpen = false;
            profileBtn.setAttribute('aria-expanded', 'false');
        }
    });

    // 3. LOGIC LOGOUT (BARU - Mengarah ke PHP)
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            // Hapus storage JS juga biar bersih
            sessionStorage.removeItem("user");
            localStorage.removeItem("user");
            
            // Redirect ke backend logout
            window.location.href = "backend/logout.php";
        });
    }

    // 4. LOGIC DROPDOWN KATEGORI (LAMA)
    var dropdown = document.getElementsByClassName("category-drop");
    for (let i = 0; i < dropdown.length; i++) {
        dropdown[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    }

    // 5. LOGIC SEARCH (LAMA)
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
                card.style.display = '';
                anyVisible = true;
            } else {
                card.style.display = 'none';
            }
        });

        // 'no results' message
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

    const searchContainer = document.querySelector('.search-container');
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');

    if (searchContainer && searchBtn && searchInput) {
        searchContainer.addEventListener('click', (e) => {
            e.stopPropagation();
        });

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
                const query = searchInput.value.trim();
                performSearch(query);
            }
        });

        document.addEventListener('click', () => {
            if (searchContainer.classList.contains('active')) {
                searchContainer.classList.remove('active');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchContainer.classList.contains('active')) {
                searchContainer.classList.remove('active');
            }
        });
    }

    // 6. LOGIC POPUP PRODUK (LAMA)
    const popupView = document.querySelector('.popup-view');
    const viewButtons = document.querySelectorAll('.view-btn');
    const closeButton = document.querySelector('.close-btn');

    // Cek apakah elemen popup ada di halaman ini (biar gak error di homepage)
    if (popupView && closeButton) {
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

            popupView.querySelector('.product-img img').src = data.image;
            popupView.querySelector('.info h3').textContent = data.name;
            popupView.querySelector('.info .category').textContent = data.category;
            popupView.querySelector('.info .restaurant').textContent = data.restaurant;
            popupView.querySelector('.info .rating').textContent = `⭐ ${data.rating}`;
            popupView.querySelector('.info .distance').textContent = `📍 ${data.distance}`;
            popupView.querySelector('.info .price').textContent = data.price;
            popupView.querySelector('.info .popup-description').textContent = data.description;

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