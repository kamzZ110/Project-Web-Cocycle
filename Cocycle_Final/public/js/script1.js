function toggleFAQ(element) {
    const item = element.closest('.faq-item');
    if (item) item.classList.toggle('active');
}
function searchContent() {
    const input = document.getElementById('searchBar');
    if (!input) return;
    document.querySelectorAll('.faq-item').forEach(item => {
        item.hidden = !item.textContent.toLowerCase().includes(input.value.toLowerCase());
    });
}
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', event => {
        const href = anchor.getAttribute('href');
        if (!href || href === '#') return;
        const target = document.getElementById(href.slice(1));
        if (target) { event.preventDefault(); target.scrollIntoView({behavior: 'smooth'}); }
    });
});
document.querySelectorAll('.counter').forEach(counter => {
    counter.textContent = Number(counter.dataset.target).toLocaleString('en-US');
});