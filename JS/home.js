// Slider functionality
document.querySelectorAll('.slider').forEach(slider => {
    let index = 0;
    const slides = slider.querySelectorAll('.slide');
    setInterval(() => {
        slides.forEach(slide => slide.style.display = 'none');
        slides[index].style.display = 'block';
        index = (index + 1) % slides.length;
    }, 3000);
});

// Click redirect functionality
const isLoggedIn = false; // Set this based on session or server-side logic

// Handle Canteens Slider Click
document.querySelector('#canteens-slider .slider').addEventListener('click', () => {
    if (!isLoggedIn) {
        window.location.href = 'canteen.php';
    }
});

// Handle Special Items Slider Click
document.querySelector('#special-items-slider .slider').addEventListener('click', () => {
    if (!isLoggedIn) {
        window.location.href = 'specialitem.php';
    }
});

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
