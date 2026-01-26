import './bootstrap';

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');
            
            // Toggle icon
            const icons = mobileMenuButton.querySelectorAll('svg');
            icons.forEach(icon => icon.classList.toggle('hidden'));
        });
    }
    
    // Set progress bar widths
    document.querySelectorAll('.progress-bar[data-percentage]').forEach(function(el) {
        const percentage = el.getAttribute('data-percentage');
        if (percentage !== null) {
            el.style.width = percentage + '%';
        }
    });
    
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.bg-green-50, .bg-red-50');
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.transition = 'opacity 0.5s';
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 500);
        }, 5000);
    });
});
