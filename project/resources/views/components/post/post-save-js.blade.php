@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Save button functionality
            const saveButtons = document.querySelectorAll('.save-btn');

            saveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    const isSaved = this.getAttribute('data-saved') === 'true';
                    const icon = this.querySelector('.bookmark-icon');
                    const text = this.querySelector('.bookmark-text');

                    // Toggle state
                    if (isSaved) {
                        // Remove from saved
                        fetch(`/posts/${postId}/unsave`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    // Update UI
                                    this.setAttribute('data-saved', 'false');
                                    icon.classList.replace('fas', 'far');
                                    text.textContent = 'Save';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    } else {
                        // Add to saved
                        fetch(`/posts/${postId}/save`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    // Update UI
                                    this.setAttribute('data-saved', 'true');
                                    icon.classList.replace('far', 'fas');
                                    text.textContent = 'Saved';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            });
        });
    </script>
@endpush
