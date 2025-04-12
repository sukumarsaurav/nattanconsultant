/**
 * Calendar JavaScript
 * Handles calendar display and appointment scheduling
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize calendar
    initCalendar();
});

// Global variables
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let appointments = [];

/**
 * Initialize calendar
 */
function initCalendar() {
    const calendarContainer = document.getElementById('calendar-container');
    if (!calendarContainer) return;
    
    // Fetch appointments for the current month
    fetchAppointments(currentYear, currentMonth);
    
    // Render initial calendar
    renderCalendar(currentYear, currentMonth);
    
    // Set up navigation buttons
    document.getElementById('prev-month')?.addEventListener('click', () => {
        navigateMonth(-1);
    });
    
    document.getElementById('next-month')?.addEventListener('click', () => {
        navigateMonth(1);
    });
    
    // Listen for appointment selection
    document.addEventListener('click', function(e) {
        if (e.target.closest('.calendar-day')) {
            const day = e.target.closest('.calendar-day');
            
            if (!day.classList.contains('disabled')) {
                const date = day.dataset.date;
                showAppointmentsForDate(date);
            }
        }
    });
}

/**
 * Navigate to previous or next month
 */
function navigateMonth(direction) {
    currentMonth += direction;
    
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    
    // Fetch appointments for the new month
    fetchAppointments(currentYear, currentMonth);
    
    // Re-render calendar
    renderCalendar(currentYear, currentMonth);
}

/**
 * Render calendar for specific month and year
 */
function renderCalendar(year, month) {
    const calendarGrid = document.getElementById('calendar-grid');
    if (!calendarGrid) return;
    
    // Clear existing calendar
    calendarGrid.innerHTML = '';
    
    // Update month/year title
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    document.getElementById('calendar-month').textContent = `${monthNames[month]} ${year}`;
    
    // Add weekday headers
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    weekdays.forEach(day => {
        const dayEl = document.createElement('div');
        dayEl.className = 'calendar-weekday';
        dayEl.textContent = day;
        calendarGrid.appendChild(dayEl);
    });
    
    // Get first day of month
    const firstDay = new Date(year, month, 1);
    const startingDay = firstDay.getDay(); // 0 = Sunday
    
    // Get number of days in month
    const lastDay = new Date(year, month + 1, 0);
    const totalDays = lastDay.getDate();
    
    // Get previous month's last days
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    
    // Current date for highlighting today
    const today = new Date();
    const todayDate = today.getDate();
    const todayMonth = today.getMonth();
    const todayYear = today.getFullYear();
    
    // Add previous month's days
    for (let i = 0; i < startingDay; i++) {
        const dayNum = prevMonthLastDay - startingDay + i + 1;
        addCalendarDay(calendarGrid, dayNum, 'other-month', year, month - 1);
    }
    
    // Add current month's days
    for (let i = 1; i <= totalDays; i++) {
        const isToday = (i === todayDate && month === todayMonth && year === todayYear);
        addCalendarDay(calendarGrid, i, isToday ? 'today' : '', year, month);
    }
    
    // Add next month's days to fill out the grid
    const daysAdded = startingDay + totalDays;
    const nextMonthDays = 42 - daysAdded; // 6 rows of 7 days = 42
    
    for (let i = 1; i <= nextMonthDays; i++) {
        addCalendarDay(calendarGrid, i, 'other-month', year, month + 1);
    }
}

/**
 * Add a day to the calendar grid
 */
function addCalendarDay(grid, dayNum, extraClass, year, month) {
    // Adjust year and month for overflow
    if (month < 0) {
        month = 11;
        year--;
    } else if (month > 11) {
        month = 0;
        year++;
    }
    
    const dayElement = document.createElement('div');
    dayElement.className = `calendar-day ${extraClass}`;
    
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
    dayElement.setAttribute('data-date', dateStr);
    
    const dayNumber = document.createElement('span');
    dayNumber.className = 'calendar-day-number';
    dayNumber.textContent = dayNum;
    
    dayElement.appendChild(dayNumber);
    
    // Add event indicator if there are appointments on this day
    if (hasAppointmentsOnDate(dateStr)) {
        dayElement.classList.add('has-events');
    }
    
    grid.appendChild(dayElement);
}

/**
 * Check if the date has appointments
 */
function hasAppointmentsOnDate(dateStr) {
    return appointments.some(appointment => appointment.date === dateStr);
}

/**
 * Fetch appointments for month
 */
function fetchAppointments(year, month) {
    // Format month for API
    const monthStr = String(month + 1).padStart(2, '0');
    
    // Create form data
    const formData = new FormData();
    formData.append('year', year);
    formData.append('month', monthStr);
    formData.append('action', 'get_appointments');
    
    // Fetch from API
    fetch('appointment-actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            appointments = data.appointments;
            renderCalendar(year, month); // Re-render with appointments
        } else {
            console.error('Failed to load appointments:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

/**
 * Show appointments for a specific date
 */
function showAppointmentsForDate(date) {
    // Highlight selected day
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.classList.remove('selected');
    });
    
    document.querySelector(`.calendar-day[data-date="${date}"]`)?.classList.add('selected');
    
    // Find appointments for this date
    const dateAppointments = appointments.filter(appointment => 
        appointment.date === date
    );
    
    // Get appointments list container
    const appointmentsList = document.getElementById('day-appointments');
    if (!appointmentsList) return;
    
    // Clear previous appointments
    appointmentsList.innerHTML = '';
    
    // Update date display
    const dateObj = new Date(date);
    document.getElementById('selected-date').textContent = formatDate(dateObj);
    
    // Show empty state if no appointments
    if (dateAppointments.length === 0) {
        appointmentsList.innerHTML = `
            <div class="dashboard-card-empty">
                <div class="dashboard-card-empty-icon">
                    <i class="far fa-calendar"></i>
                </div>
                <p>No appointments scheduled for this date</p>
                <p><a href="consultant-schedule.php" class="btn btn-outline">Set Availability</a></p>
            </div>
        `;
        return;
    }
    
    // Sort appointments by time
    dateAppointments.sort((a, b) => {
        return new Date(a.datetime) - new Date(b.datetime);
    });
    
    // Add each appointment to the list
    dateAppointments.forEach(appointment => {
        const appointmentItem = document.createElement('li');
        appointmentItem.className = 'appointment-item';
        
        // Format time
        const appointmentTime = new Date(appointment.datetime);
        const timeFormatted = formatTime(appointmentTime);
        
        // Create status badge
        const statusClass = `badge badge-${appointment.status.toLowerCase()}`;
        
        appointmentItem.innerHTML = `
            <div class="appointment-time">
                <div class="appointment-hour">${timeFormatted}</div>
            </div>
            <div class="appointment-details">
                <div class="appointment-client">${appointment.client_name}</div>
                <div class="appointment-type">${appointment.consultation_type}</div>
            </div>
            <div class="appointment-status ${statusClass}">
                ${appointment.status}
            </div>
            <div class="appointment-actions">
                <a href="consultant-booking-view.php?id=${appointment.id}" class="btn btn-sm btn-outline">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        `;
        
        appointmentsList.appendChild(appointmentItem);
    });
}

/**
 * Format date for display
 */
function formatDate(date) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString(undefined, options);
}

/**
 * Format time for display
 */
function formatTime(date) {
    const options = { hour: 'numeric', minute: '2-digit', hour12: true };
    return date.toLocaleTimeString(undefined, options);
} 