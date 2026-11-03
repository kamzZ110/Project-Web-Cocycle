document.addEventListener('DOMContentLoaded', () => {
  // Guard login dipindah ke sisi server (PHP).
  // JS tidak perlu memblokir akses berdasarkan localStorage/sessionStorage.

  var dropdown = document.getElementsByClassName("category-drop");
  var i;

  // search //
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

    // 'no results' kalo ga ketemu //
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

  for (i = 0; i < dropdown.length; i++) {
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

  const searchContainer = document.querySelector('.search-container');
  const searchBtn = document.querySelector('.search-btn');
  const searchInput = document.querySelector('.search-input');

  if (searchContainer && searchBtn && searchInput) {
    const formEl = searchContainer.closest('form');

    searchContainer.addEventListener('click', (e) => {
      e.stopPropagation();
    });

    // Klik pertama: buka input; klik berikutnya: submit ke server (GET)
    searchBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (!searchContainer.classList.contains('active')) {
        e.preventDefault();
        searchContainer.classList.add('active');
        searchInput.focus();
        return;
      }
      e.preventDefault();
      const q = (searchInput.value || '').trim();
      const action = (formEl && formEl.getAttribute('action')) ? formEl.getAttribute('action') : 'katalog.php';
      const url = action + '?search=' + encodeURIComponent(q);
      window.location.href = url;
    });

    // Tekan Enter: submit form agar backend memproses pencarian
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const q = (searchInput.value || '').trim();
        const action = (formEl && formEl.getAttribute('action')) ? formEl.getAttribute('action') : 'katalog.php';
        const url = action + '?search=' + encodeURIComponent(q);
        window.location.href = url;
      }
    });

    // Jangan tutup ketika klik di input
    searchInput.addEventListener('click', (e) => e.stopPropagation());
    searchInput.addEventListener('mousedown', (e) => e.stopPropagation());

    // hide search //
    document.addEventListener('click', () => {
      if (searchContainer.classList.contains('active')) {
        searchContainer.classList.remove('active');
      }
    });

    // hide search pake esc //
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && searchContainer.classList.contains('active')) {
        searchContainer.classList.remove('active');
      }
    });
  }

  //!-- pop up item deh pokoknya -- //
    const popupView = document.querySelector('.popup-view');
    const viewButtons = document.querySelectorAll('.view-btn');
    const closeButton = document.querySelector('.close-btn');

    // menambilkan popup dengan dinamis //
    const showPopup = function (e) {
      e.preventDefault();

      const card = e.currentTarget.closest('.product-card');
      if (!card) return;

      // ambil data dari card //
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

      // masukin data ke popout (buat detail) //
      popupView.querySelector('.product-img img').src = data.image;
      popupView.querySelector('.info h3').textContent = data.name;
      popupView.querySelector('.info .category').textContent = data.category;
      popupView.querySelector('.info .restaurant').textContent = data.restaurant;
      popupView.querySelector('.info .rating').textContent = `⭐ ${data.rating}`;
      popupView.querySelector('.info .distance').textContent = `📍 ${data.distance}`;
      popupView.querySelector('.info .price').textContent = data.price;
      popupView.querySelector('.info .popup-description').textContent = data.description;

      // tampilkan popup //
      popupView.classList.add('active');
    };

    // tutup popout //
    const hidePopup = function () {
      popupView.classList.remove('active');
    };

    // buat liat dan tutup //
    viewButtons.forEach((btn) => {
      btn.addEventListener('click', showPopup);
    });

    closeButton.addEventListener('click', hidePopup);
  });
