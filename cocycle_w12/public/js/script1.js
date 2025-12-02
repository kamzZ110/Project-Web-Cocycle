document.querySelectorAll('.card, .impact-item, .community-item').forEach(item => {
    item.addEventListener('click', () => {
        alert(`You clicked ${item.textContent}!`);
    });
});

function searchContent() {
    let input = document.getElementById('searchBar').value.toLowerCase();
    let contents = document.getElementsByClassName('content');
    for (let i = 0; i < contents.length; i++) {
        let text = contents[i].textContent.toLowerCase();
        contents[i].style.display = text.includes(input) ? '' : 'none';
    }
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('active');
    });
});

function toggleFAQ(element) {
    const item = element.parentElement;
    item.classList.toggle('active');
}

function searchContent() {
    let input = document.getElementById('searchBar').value.toLowerCase();
    let faqQuestions = document.getElementsByClassName('faq-question');
    for (let i = 0; i < faqQuestions.length; i++) {
        let text = faqQuestions[i].textContent.toLowerCase();
        let item = faqQuestions[i].parentElement;
        item.style.display = text.includes(input) ? 'block' : 'none';
    }
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href') || '#faq').scrollIntoView({
            behavior: 'smooth'
        });
    });
});

console.log("🔍 Script1.js loaded!");
