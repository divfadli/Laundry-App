function formatRupiah(angka) {
    return 'Rp. ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatNumber(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function animateCounter(element, target, duration = 2000, isCurrency = false) {
    let start = 0;
    const increment = target / (duration / 16);

    function updateCounter() {
        start += increment;
        if (start < target) {
            element.textContent = isCurrency ?
                formatRupiah(Math.floor(start)) :
                formatNumber(Math.floor(start));
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = isCurrency ?
                formatRupiah(target) :
                formatNumber(target);
        }
    }
    updateCounter();
}

const statsSection = document.querySelector('.stats-section');
let statsAnimated = false;

if (statsSection) {
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !statsAnimated) {
                document.querySelectorAll('.stat-number').forEach(stat => {
                    const target = parseInt(stat.getAttribute('data-target'));
                    const isCurrency = stat.getAttribute('data-currency') === 'true';
                    animateCounter(stat, target, 2000, isCurrency);
                });
                statsAnimated = true;
            }
        });
    }, {
        threshold: 0.5
    });

    statsObserver.observe(statsSection);
}