/**
 * Consultant Portal JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initTooltips();
    
    // Initialize appointment status actions
    initAppointmentActions();
    
    // Initialize form validations
    initFormValidations();
    
    // Initialize profile image upload
    initProfileImageUpload();
    
    // Handle mobile navigation
    initMobileNav();
    
    // Initialize notification system
    initNotifications();
});

/**
 * Initialize bootstrap tooltips
 */
function initTooltips() {
    // Check if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Initialize appointment action buttons
 */
function initAppointmentActions() {
    // Appointment status update buttons
    const statusButtons = document.querySelectorAll('.appointment-status-btn');
    
    statusButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const appointmentId = this.dataset.appointmentId;
            const status = this.dataset.status;
            const confirmMessage = `Are you sure you want to mark this appointment as ${status}?`;
            
            if (confirm(confirmMessage)) {
                // Create form data
                const formData = new FormData();
                formData.append('appointment_id', appointmentId);
                formData.append('status', status);
                formData.append('action', 'update_status');
                
                // Send AJAX request
                fetch('appointment-actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        updateAppointmentStatusUI(appointmentId, status);
                        showNotification('Appointment status updated successfully!', 'success');
                    } else {
                        showNotification('Failed to update appointment status: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while updating the appointment.', 'error');
                });
            }
        });
    });
}

/**
 * Update appointment status in UI
 */
function updateAppointmentStatusUI(appointmentId, status) {
    const statusElement = document.querySelector(`.appointment-status[data-appointment-id="${appointmentId}"]`);
    const row = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
    
    if (statusElement) {
        // Remove all status classes
        statusElement.classList.remove('badge-pending', 'badge-confirmed', 'badge-completed', 'badge-cancelled', 'badge-no-show');
        
        // Add new status class
        statusElement.classList.add(`badge-${status.toLowerCase()}`);
        
        // Update text
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    }
    
    // If status is cancelled or completed, update row styling
    if (row) {
        if (status.toLowerCase() === 'cancelled') {
            row.style.opacity = '0.6';
        } else if (status.toLowerCase() === 'completed') {
            row.style.backgroundColor = 'rgba(40, 167, 69, 0.05)';
        }
    }
}

/**
 * Initialize form validations
 */
function initFormValidations() {
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Initialize profile image upload
 */
function initProfileImageUpload() {
    const imageInput = document.getElementById('profile-image-upload');
    const imagePreview = document.getElementById('profile-image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.style.backgroundImage = `url('${e.target.result}')`;
                    imagePreview.classList.add('has-image');
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
}

/**
 * Initialize mobile navigation
 */
function initMobileNav() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const sideNav = document.getElementById('side-navigation');
    
    if (menuToggle && sideNav) {
        menuToggle.addEventListener('click', function() {
            sideNav.classList.toggle('show');
            document.body.classList.toggle('nav-open');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (sideNav.classList.contains('show') && 
                !sideNav.contains(event.target) && 
                event.target !== menuToggle) {
                sideNav.classList.remove('show');
                document.body.classList.remove('nav-open');
            }
        });
    }
}

/**
 * Show notification
 * @param {string} message - The notification message
 * @param {string} type - The notification type (success, error, warning, info)
 */
function showNotification(message, type = 'info') {
    // Check if notifications container exists, if not create it
    let container = document.querySelector('.notifications-container');
    
    if (!container) {
        container = document.createElement('div');
        container.className = 'notifications-container';
        document.body.appendChild(container);
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <div class="notification-message">${message}</div>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Add to container
    container.appendChild(notification);
    
    // Show with animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Set up close button
    const closeButton = notification.querySelector('.notification-close');
    closeButton.addEventListener('click', () => {
        closeNotification(notification);
    });
    
    // Auto-dismiss after 5 seconds for success and info
    if (type === 'success' || type === 'info') {
        setTimeout(() => {
            closeNotification(notification);
        }, 5000);
    }
}

/**
 * Close notification with animation
 */
function closeNotification(notification) {
    notification.classList.remove('show');
    
    // Remove from DOM after animation
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Format time for display
 */
function formatTime(dateString) {
    const options = { hour: 'numeric', minute: '2-digit', hour12: true };
    return new Date(dateString).toLocaleTimeString(undefined, options);
} 