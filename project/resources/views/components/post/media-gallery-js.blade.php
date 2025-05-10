@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mediaItems = document.querySelectorAll('.media-item');
            const indicators = document.querySelectorAll('.pagination-indicator');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            let currentIndex = 0;
            const totalItems = mediaItems.length;

            if (totalItems <= 1) return;

            function showMedia(index) {
                mediaItems.forEach(item => item.classList.add('hidden'));

                mediaItems[index].classList.remove('hidden');

                // Update indicators
                indicators.forEach(indicator => indicator.classList.replace('bg-white', 'bg-white/50'));
                indicators[index].classList.replace('bg-white/50', 'bg-white');

                currentIndex = index;
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    const newIndex = (currentIndex + 1) % totalItems;
                    showMedia(newIndex);
                });
            }
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    const newIndex = (currentIndex - 1 + totalItems) % totalItems;
                    showMedia(newIndex);
                });
            }

            // Indicator clicks
            indicators.forEach(indicator => {
                indicator.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    showMedia(index);
                });
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight') {
                    const newIndex = (currentIndex + 1) % totalItems;
                    showMedia(newIndex);
                } else if (e.key === 'ArrowLeft') {
                    const newIndex = (currentIndex - 1 + totalItems) % totalItems;
                    showMedia(newIndex);
                }
            });
        });
    </script>
@endpush
