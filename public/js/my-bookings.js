document.addEventListener('DOMContentLoaded', function () {
    const filters = document.querySelectorAll('.booking-filter');
    const cards = document.querySelectorAll('.my-booking-card');
    const emptyState = document.getElementById('booking-empty');

    filters.forEach(function (filter) {
        filter.addEventListener('click', function () {
            const category = filter.dataset.filter;
            let visibleCount = 0;

            filters.forEach(function (item) {
                const isActive = item === filter;
                item.classList.toggle('active', isActive);
                item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            cards.forEach(function (card) {
                const shouldShow = category === 'all' || card.dataset.status === category;
                card.hidden = !shouldShow;
                if (shouldShow) visibleCount += 1;
            });

            emptyState.hidden = visibleCount > 0;
        });
    });
});
