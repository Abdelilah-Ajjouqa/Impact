<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createdTab = document.getElementById('created-tab');
        const savedTab = document.getElementById('saved-tab');
        const createdposts = document.getElementById('created-posts');
        const savedposts = document.getElementById('saved-posts');

        function switchTab(activeTab, inactiveTab, activeContent, inactiveContent) {
            activeTab.classList.add('border-sky-600', 'text-sky-600');
            activeTab.classList.remove('border-transparent', 'text-gray-600');

            inactiveTab.classList.add('border-transparent', 'text-gray-600');
            inactiveTab.classList.remove('border-sky-600', 'text-sky-600');

            activeContent.classList.remove('hidden');
            inactiveContent.classList.add('hidden');
        }

        // Set up click handlers
        createdTab.addEventListener('click', function() {
            switchTab(createdTab, savedTab, createdposts, savedposts);
        });

        savedTab.addEventListener('click', function() {
            switchTab(savedTab, createdTab, savedposts, createdposts);
        });

        // Initialize with created tab active (default state)
        switchTab(createdTab, savedTab, createdposts, savedposts);
    });
</script> 