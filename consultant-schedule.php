<?php
$page_title = "My Schedule | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get current date for default view
$current_date = isset($_GET['date']) ? new DateTime($_GET['date']) : new DateTime();
$selected_date = $current_date->format('Y-m-d');
$selected_day = strtolower($current_date->format('l')); // e.g., 'monday', 'tuesday', etc.

// Handle form submissions
$success_message = '';
$error_message = '';

// Handle slot availability update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    // Begin transaction
    $conn = getConnection();
    mysqli_begin_transaction($conn);
    
    try {
        // First, delete existing slots for the selected day of week
        $delete_query = "DELETE FROM availability_schedule WHERE admin_user_id = ? AND day_of_week = ?";
        executeQuery($delete_query, [$consultant_id, $selected_day]);
        
        // Then, insert new slots if any are selected
        if (isset($_POST['time_slots']) && !empty($_POST['time_slots'])) {
            foreach ($_POST['time_slots'] as $slot) {
                list($start_time, $end_time, $consultation_type) = explode('|', $slot);
                
                $insert_query = "INSERT INTO availability_schedule (admin_user_id, day_of_week, start_time, end_time, is_available) 
                               VALUES (?, ?, ?, ?, 1)";
                executeQuery($insert_query, [
                    $consultant_id, 
                    $selected_day, 
                    $start_time, 
                    $end_time
                ]);
            }
        }
        
        // Set recurring schedule if requested - not needed with day_of_week schema
        // since we're already setting it for a specific day of the week
        
        // Commit transaction
        mysqli_commit($conn);
        $success_message = 'Schedule updated successfully!';
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $error_message = 'Error updating schedule: ' . $e->getMessage();
    }
}

// Handle date navigation
$prev_date = (clone $current_date)->modify('-1 day')->format('Y-m-d');
$next_date = (clone $current_date)->modify('+1 day')->format('Y-m-d');

// Get existing schedule for selected day of week
$schedule_query = "SELECT * FROM availability_schedule WHERE admin_user_id = ? AND day_of_week = ? ORDER BY start_time";
$schedule_result = executeQuery($schedule_query, [$consultant_id, $selected_day]);
$schedule_slots = [];

while ($row = mysqli_fetch_assoc($schedule_result)) {
    $key = $row['start_time'] . '|' . $row['end_time'] . '|' . (isset($row['consultation_type']) ? $row['consultation_type'] : '');
    $schedule_slots[$key] = $row;
}

// Get booked appointments for the selected date
$booked_query = "SELECT * FROM appointments WHERE DATE(appointment_datetime) = ? ORDER BY appointment_datetime";
$booked_result = executeQuery($booked_query, [$selected_date]);
$booked_slots = [];

while ($row = mysqli_fetch_assoc($booked_result)) {
    $time = date('H:i', strtotime($row['appointment_datetime']));
    // Assuming 30-minute appointments for simplicity
    $end_time = date('H:i', strtotime($row['appointment_datetime']) + 1800); // +30 minutes
    $key = $time . '|' . $end_time . '|' . (isset($row['consultation_type']) ? $row['consultation_type'] : '');
    $booked_slots[$key] = $row;
}

// Get day-specific consultation availability
$day_availability_query = "SELECT * FROM day_consultation_availability WHERE consultant_id = ? AND day_of_week = ?";
$day_availability_result = executeQuery($day_availability_query, [$consultant_id, $selected_day]);
$day_availability = mysqli_fetch_assoc($day_availability_result);

// If no day-specific settings exist, use the consultant's global settings
$video_available = isset($day_availability['video_available']) ? $day_availability['video_available'] : ($consultant['video_consultation_available'] ?? 0);
$phone_available = isset($day_availability['phone_available']) ? $day_availability['phone_available'] : ($consultant['phone_consultation_available'] ?? 0);
$in_person_available = isset($day_availability['in_person_available']) ? $day_availability['in_person_available'] : ($consultant['in_person_consultation_available'] ?? 0);

// Handle day-specific consultation type availability update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_consultation_types'])) {
    $video_available = isset($_POST['video_available']) ? 1 : 0;
    $phone_available = isset($_POST['phone_available']) ? 1 : 0;
    $in_person_available = isset($_POST['in_person_available']) ? 1 : 0;
    
    // Check if record exists
    $check_query = "SELECT id FROM day_consultation_availability WHERE consultant_id = ? AND day_of_week = ?";
    $check_result = executeQuery($check_query, [$consultant_id, $selected_day]);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing record
        $update_query = "UPDATE day_consultation_availability 
                        SET video_available = ?, phone_available = ?, in_person_available = ? 
                        WHERE consultant_id = ? AND day_of_week = ?";
        executeQuery($update_query, [$video_available, $phone_available, $in_person_available, $consultant_id, $selected_day]);
    } else {
        // Insert new record
        $insert_query = "INSERT INTO day_consultation_availability 
                        (consultant_id, day_of_week, video_available, phone_available, in_person_available) 
                        VALUES (?, ?, ?, ?, ?)";
        executeQuery($insert_query, [$consultant_id, $selected_day, $video_available, $phone_available, $in_person_available]);
    }
    
    $success_message = 'Consultation types updated successfully for ' . ucfirst($selected_day) . '!';
}

// Get consultant information
$consultant_query = "SELECT * FROM consultants WHERE id = ?";
$consultant_result = executeQuery($consultant_query, [$consultant_id]);
$consultant = mysqli_fetch_assoc($consultant_result);

// Define time slots for scheduling (30-minute intervals)
$time_slots = [];
$start = new DateTime('9:00');
$end = new DateTime('18:00');
$interval = new DateInterval('PT30M');

$period = new DatePeriod($start, $interval, $end);
foreach ($period as $time) {
    $start_time = $time->format('H:i');
    $end_time = (clone $time)->add(new DateInterval('PT30M'))->format('H:i');
    $time_slots[] = ['start' => $start_time, 'end' => $end_time];
}

// Consultation types
$consultation_types = [
    'Video Consultation' => $video_available,
    'Phone Consultation' => $phone_available,
    'In-Person Consultation' => $in_person_available
];
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>My Schedule</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">Manage your availability and consultation schedule</p>
    </div>
</section>

<style>
/* Toggle switch styling */
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
}

.switch .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.switch input:checked + .slider {
    background-color: var(--color-burgundy);
}

.switch input:focus + .slider {
    box-shadow: 0 0 1px var(--color-burgundy);
}

.switch input:checked + .slider:before {
    transform: translateX(26px);
}

/* Time slot toggle switch styling */
.time-slot-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    margin-left: 10px;
}

.time-slot-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.time-slot-switch .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
    box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.2);
}

.time-slot-switch .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.time-slot-switch input:checked + .slider {
    background-color: var(--color-burgundy);
}

.time-slot-switch input:disabled + .slider {
    opacity: 0.5;
    cursor: not-allowed;
}

.time-slot-switch input:checked + .slider:before {
    transform: translateX(26px);
}

.time-slot-card {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.2s ease;
}

.time-slot-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
</style>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; padding: 12px 20px; margin-bottom: 20px; border-radius: 4px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div style="background-color: #ffebee; border-left: 4px solid #f44336; padding: 12px 20px; margin-bottom: 20px; border-radius: 4px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Date Navigation -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <a href="?date=<?php echo $prev_date; ?>" style="display: inline-flex; align-items: center; text-decoration: none; color: var(--color-burgundy);">
                <i class="fas fa-chevron-left" style="margin-right: 5px;"></i> Previous Day
            </a>
            
            <div style="text-align: center;">
                <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.5rem;"><?php echo $current_date->format('l, F j, Y'); ?></h2>
                <p style="margin: 5px 0 0; color: #666;">Set your availability for <?php echo $current_date->format('l'); ?></p>
            </div>
            
            <a href="?date=<?php echo $next_date; ?>" style="display: inline-flex; align-items: center; text-decoration: none; color: var(--color-burgundy);">
                Next Day <i class="fas fa-chevron-right" style="margin-left: 5px;"></i>
            </a>
        </div>
        
        <!-- Schedule Management Section -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Set Your Availability for <?php echo $current_date->format('l'); ?></h2>
            
            <!-- Day-specific Consultation Types -->
            <div style="margin-bottom: 30px; border: 1px solid #eee; border-radius: 10px; padding: 20px;">
                <h3 style="font-size: 1.1rem; margin: 0 0 15px; color: var(--color-dark);">Consultation Types Available for <?php echo ucfirst($selected_day); ?></h3>
                <p style="margin-bottom: 20px; font-size: 0.9rem; color: #666;">Enable or disable specific consultation types for this day of the week. These settings will override your global profile settings.</p>
                
                <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px;">
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 10px; min-width: 250px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div style="font-weight: 500;">Video Consultation</div>
                            <label class="switch" style="position: relative; display: inline-block; width: 60px; height: 34px;">
                                <input type="checkbox" class="auto-save-toggle" name="video_available" data-type="video" <?php echo $video_available ? 'checked' : ''; ?> style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" class="slider"></span>
                            </label>
                        </div>
                        <div style="font-size: 0.9rem; color: #666;">
                            Fee: $<?php echo number_format($consultant['video_consultation_fee'], 2); ?>
                        </div>
                    </div>
                    
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 10px; min-width: 250px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div style="font-weight: 500;">Phone Consultation</div>
                            <label class="switch" style="position: relative; display: inline-block; width: 60px; height: 34px;">
                                <input type="checkbox" class="auto-save-toggle" name="phone_available" data-type="phone" <?php echo $phone_available ? 'checked' : ''; ?> style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" class="slider"></span>
                            </label>
                        </div>
                        <div style="font-size: 0.9rem; color: #666;">
                            Fee: $<?php echo number_format($consultant['phone_consultation_fee'], 2); ?>
                        </div>
                    </div>
                    
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 10px; min-width: 250px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div style="font-weight: 500;">In-Person Consultation</div>
                            <label class="switch" style="position: relative; display: inline-block; width: 60px; height: 34px;">
                                <input type="checkbox" class="auto-save-toggle" name="in_person_available" data-type="in_person" <?php echo $in_person_available ? 'checked' : ''; ?> style="opacity: 0; width: 0; height: 0;">
                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" class="slider"></span>
                            </label>
                        </div>
                        <div style="font-size: 0.9rem; color: #666;">
                            Fee: $<?php echo number_format($consultant['in_person_consultation_fee'], 2); ?>
                        </div>
                    </div>
                </div>
                
                <div id="toggle-save-status" style="text-align: right; height: 30px;">
                    <!-- Status messages will appear here -->
                </div>
            </div>
            
            <!-- Time Slots Selection -->
            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 1.1rem; margin: 0 0 15px; color: var(--color-dark);">Select Available Time Slots</h3>
                
                <div id="time-slots-status" style="margin-bottom: 15px; text-align: right; height: 30px;">
                    <!-- Status messages will appear here -->
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                    <?php foreach ($time_slots as $slot): ?>
                        <?php
                        // Determine if this is a valid slot for scheduling (not in the past for today)
                        $slot_datetime = new DateTime($selected_date . ' ' . $slot['start']);
                        $now = new DateTime();
                        $is_past = $selected_date == $now->format('Y-m-d') && $slot_datetime < $now;
                        ?>
                        
                        <div class="time-slot-card" style="<?php echo $is_past ? 'opacity: 0.5;' : ''; ?>">
                            <div style="font-weight: 500; margin-bottom: 12px; font-size: 1.05rem; border-bottom: 1px solid #eee; padding-bottom: 8px;">
                                <?php echo date('g:i A', strtotime($slot['start'])); ?> - <?php echo date('g:i A', strtotime($slot['end'])); ?>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <?php 
                                // Loop through all consultation types regardless of day availability
                                $all_types = [
                                    'Video Consultation' => ['available' => $video_available, 'fee' => $consultant['video_consultation_fee']],
                                    'Phone Consultation' => ['available' => $phone_available, 'fee' => $consultant['phone_consultation_fee']],
                                    'In-Person Consultation' => ['available' => $in_person_available, 'fee' => $consultant['in_person_consultation_fee']]
                                ];
                                
                                foreach ($all_types as $type => $settings): 
                                    $slot_key = $slot['start'] . '|' . $slot['end'] . '|' . $type;
                                    $is_scheduled = isset($schedule_slots[$slot_key]);
                                    
                                    // Check if this slot is booked
                                    $is_booked = false;
                                    foreach ($booked_slots as $booked_key => $booked) {
                                        list($booked_start, $booked_end, $booked_type) = explode('|', $booked_key);
                                        if ($slot['start'] === $booked_start && $type === $booked_type) {
                                            $is_booked = true;
                                            break;
                                        }
                                    }
                                    
                                    // Determine styles based on status
                                    $checkbox_disabled = $is_past || $is_booked || !$settings['available'];
                                    $disabled_reason = '';
                                    
                                    if ($is_past) {
                                        $disabled_reason = 'Past time';
                                    } elseif ($is_booked) {
                                        $disabled_reason = 'Booked';
                                    } elseif (!$settings['available']) {
                                        $disabled_reason = 'Disabled for ' . ucfirst($selected_day);
                                    }
                                ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 5px; <?php echo $checkbox_disabled ? 'opacity: 0.7;' : ''; ?>">
                                        <div style="font-size: 0.9rem;">
                                            <div><?php echo $type; ?></div>
                                            <div style="font-size: 0.8rem; color: #666;">Fee: $<?php echo number_format($settings['fee'], 2); ?></div>
                                            <?php if ($checkbox_disabled && $disabled_reason): ?>
                                                <div style="font-size: 0.8rem; color: #f44336;"><?php echo $disabled_reason; ?></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <label class="time-slot-switch">
                                            <input 
                                                type="checkbox" 
                                                class="time-slot-toggle"
                                                data-start="<?php echo $slot['start']; ?>"
                                                data-end="<?php echo $slot['end']; ?>"
                                                data-type="<?php echo $type; ?>"
                                                value="<?php echo $slot_key; ?>" 
                                                <?php echo $is_scheduled ? 'checked' : ''; ?> 
                                                <?php echo $checkbox_disabled ? 'disabled' : ''; ?>
                                            >
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Calendar View -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Weekly Calendar</h2>
            
            <div id="schedule-calendar" style="width: 100%;"></div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle switches auto-save functionality
    const toggleSwitches = document.querySelectorAll('.auto-save-toggle');
    const statusDiv = document.getElementById('toggle-save-status');
    
    toggleSwitches.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const consultationType = this.getAttribute('data-type');
            const isChecked = this.checked ? 1 : 0;
            const selectedDay = '<?php echo $selected_day; ?>';
            
            // Show loading status
            statusDiv.innerHTML = '<div style="color: #666;"><i class="fas fa-spinner fa-spin"></i> Saving changes...</div>';
            
            // Create form data
            const formData = new FormData();
            formData.append('selected_day', selectedDay);
            formData.append('update_consultation_types', '1');
            
            // Add specific consultation type
            if (consultationType === 'video') {
                formData.append('video_available', isChecked);
                formData.append('phone_available', document.querySelector('[name="phone_available"]').checked ? 1 : 0);
                formData.append('in_person_available', document.querySelector('[name="in_person_available"]').checked ? 1 : 0);
            } else if (consultationType === 'phone') {
                formData.append('video_available', document.querySelector('[name="video_available"]').checked ? 1 : 0);
                formData.append('phone_available', isChecked);
                formData.append('in_person_available', document.querySelector('[name="in_person_available"]').checked ? 1 : 0);
            } else if (consultationType === 'in_person') {
                formData.append('video_available', document.querySelector('[name="video_available"]').checked ? 1 : 0);
                formData.append('phone_available', document.querySelector('[name="phone_available"]').checked ? 1 : 0);
                formData.append('in_person_available', isChecked);
            }
            
            // Send AJAX request
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Show success message
                    statusDiv.innerHTML = '<div style="color: #4caf50;"><i class="fas fa-check"></i> Changes saved</div>';
                    
                    // Update the time slots in real-time
                    updateTimeSlots(consultationType, isChecked);
                    
                    // Provide reload option for complete refresh
                    setTimeout(() => {
                        statusDiv.innerHTML = '<div style="color: #4caf50;"><i class="fas fa-check"></i> Changes saved. <a href="javascript:void(0)" id="reload-page" style="color: #0066cc; text-decoration: underline;">Refresh page</a> if changes are not visible.</div>';
                        
                        // Add event listener to the reload link
                        document.getElementById('reload-page').addEventListener('click', function() {
                            window.location.reload();
                        });
                    }, 2000);
                } else {
                    // Show error message
                    statusDiv.innerHTML = '<div style="color: #f44336;"><i class="fas fa-times"></i> Failed to save changes</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<div style="color: #f44336;"><i class="fas fa-times"></i> Failed to save changes</div>';
            });
        });
    });

    // Time slot toggles auto-save functionality
    const timeSlotToggles = document.querySelectorAll('.time-slot-toggle');
    const timeSlotStatusDiv = document.getElementById('time-slots-status');
    
    // Debounce function to reduce number of server requests
    let timeoutId;
    const debounce = (func, delay) => {
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(null, args);
            }, delay);
        };
    };
    
    // Track changes to send in batch
    let pendingChanges = [];
    
    timeSlotToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const startTime = this.getAttribute('data-start');
            const endTime = this.getAttribute('data-end');
            const slotType = this.getAttribute('data-type');
            const isChecked = this.checked;
            
            // Add to pending changes
            pendingChanges.push({
                start: startTime,
                end: endTime,
                type: slotType,
                checked: isChecked
            });
            
            // Show loading status
            timeSlotStatusDiv.innerHTML = '<div style="color: #666;"><i class="fas fa-spinner fa-spin"></i> Saving changes...</div>';
            
            // Debounce the save operation
            debounceTimeSlotSave();
        });
    });
    
    // Debounced save function
    const debounceTimeSlotSave = debounce(() => {
        saveTimeSlots();
    }, 500);
    
    // Function to save time slots
    function saveTimeSlots() {
        if (pendingChanges.length === 0) return;
        
        const selectedDay = '<?php echo $selected_day; ?>';
        const selectedDate = '<?php echo $selected_date; ?>';
        
        // Create form data for AJAX request
        const formData = new FormData();
        formData.append('time_slots_ajax', JSON.stringify(pendingChanges));
        formData.append('selected_day', selectedDay);
        formData.append('selected_date', selectedDate);
        
        // Send AJAX request
        fetch('ajax-save-timeslots.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Show success message
                timeSlotStatusDiv.innerHTML = '<div style="color: #4caf50;"><i class="fas fa-check"></i> Time slots updated</div>';
                
                // Clear pending changes
                pendingChanges = [];
                
                // Clear message after 3 seconds
                setTimeout(() => {
                    timeSlotStatusDiv.innerHTML = '';
                }, 3000);
            } else {
                // Show error message
                timeSlotStatusDiv.innerHTML = '<div style="color: #f44336;"><i class="fas fa-times"></i> Failed to update time slots</div>';
                
                // Show reload option
                setTimeout(() => {
                    timeSlotStatusDiv.innerHTML = '<div style="color: #f44336;"><i class="fas fa-times"></i> Failed to update time slots. <a href="javascript:void(0)" id="reload-page-slots" style="color: #0066cc; text-decoration: underline;">Please refresh the page</a> and try again.</div>';
                    
                    document.getElementById('reload-page-slots')?.addEventListener('click', function() {
                        window.location.reload();
                    });
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            timeSlotStatusDiv.innerHTML = '<div style="color: #f44336;"><i class="fas fa-times"></i> Failed to update time slots</div>';
        });
    }

    // Function to update time slots based on consultation type availability
    function updateTimeSlots(consultationType, isAvailable) {
        // Map data-type attribute values to their corresponding time slot types
        const typeMapping = {
            'video': 'Video Consultation',
            'phone': 'Phone Consultation',
            'in_person': 'In-Person Consultation'
        };
        
        // Get the consultation type string for the time slots
        const slotTypeString = typeMapping[consultationType];
        
        if (!slotTypeString) return;
        
        // Query all time slot checkboxes that contain our consultation type
        document.querySelectorAll('.time-slot-card').forEach(card => {
            // Get all checkboxes and their container divs
            const slots = card.querySelectorAll('input[type="checkbox"][value*="' + slotTypeString + '"]');
            
            slots.forEach(checkbox => {
                const slotContainer = checkbox.closest('div[style*="display: flex"]');
                
                if (slotContainer) {
                    // Check if this is a past time slot (parent card has opacity 0.5)
                    const isPast = card.style.opacity === '0.5';
                    
                    // Check if this slot is already booked
                    const disabledDiv = slotContainer.querySelector('div[style*="color: #f44336"]');
                    const isBooked = disabledDiv && disabledDiv.textContent === 'Booked';
                    
                    if (!isAvailable) {
                        // Disable the slot if consultation type is toggled off
                        checkbox.disabled = true;
                        slotContainer.style.opacity = '0.7';
                        
                        // Add or update the disabled reason text
                        const infoDiv = slotContainer.querySelector('div:first-child');
                        
                        if (infoDiv) {
                            // Check for existing reason div
                            let reasonDiv = Array.from(infoDiv.children).find(child => 
                                child.classList && child.classList.contains('disabled-reason')
                            );
                            
                            if (!reasonDiv) {
                                // Create disabled reason div
                                reasonDiv = document.createElement('div');
                                reasonDiv.classList.add('disabled-reason');
                                reasonDiv.style.fontSize = '0.8rem';
                                reasonDiv.style.color = '#f44336';
                                infoDiv.appendChild(reasonDiv);
                            }
                            
                            reasonDiv.textContent = 'Disabled for <?php echo ucfirst($selected_day); ?>';
                        }
                    } else if (!isPast && !isBooked) {
                        // Enable the checkbox if:
                        // 1. Consultation type is enabled
                        // 2. It's not a past time slot
                        // 3. It's not already booked
                        checkbox.disabled = false;
                        slotContainer.style.opacity = '';
                        
                        // Remove the "Disabled for X day" message if it exists
                        const infoDiv = slotContainer.querySelector('div:first-child');
                        if (infoDiv) {
                            const reasonDivs = infoDiv.querySelectorAll('.disabled-reason');
                            reasonDivs.forEach(div => {
                                if (div.textContent === 'Disabled for <?php echo ucfirst($selected_day); ?>') {
                                    div.remove();
                                }
                            });
                        }
                    }
                }
            });
        });
    }

    // Simple weekly calendar implementation
    const calendarContainer = document.getElementById('schedule-calendar');
    
    // Current date tracking
    let currentDate = new Date('<?php echo $selected_date; ?>');
    
    // Functions to render the calendar
    function renderWeeklyCalendar() {
        // Get the current week's dates
        const currentDay = currentDate.getDay(); // 0 is Sunday, 1 is Monday, etc.
        const sunday = new Date(currentDate);
        sunday.setDate(currentDate.getDate() - currentDay);
        
        const days = [];
        for (let i = 0; i < 7; i++) {
            const day = new Date(sunday);
            day.setDate(sunday.getDate() + i);
            days.push(day);
        }
        
        // Create calendar HTML
        let calendarHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <button id="prev-week" style="background: none; border: none; cursor: pointer; color: var(--color-burgundy);"><i class="fas fa-chevron-left"></i> Previous Week</button>
                <h3 style="margin: 0; font-size: 1.2rem;">Week of ${sunday.toLocaleDateString('en-US', { month: 'long', day: 'numeric' })} - ${days[6].toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</h3>
                <button id="next-week" style="background: none; border: none; cursor: pointer; color: var(--color-burgundy);">Next Week <i class="fas fa-chevron-right"></i></button>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center; padding: 10px; color: #666;">Sun</th>
                        <th style="text-align: center; padding: 10px; color: #666;">Mon</th>
                        <th style="text-align: center; padding: 10px; color: #666;">Tue</th>
                        <th style="text-align: center; padding: 10px; color: #666;">Wed</th>
                        <th style="text-align: center; padding: 10px; color: #666;">Thu</th>
                        <th style="text-align: center; padding: 10px; color: #666;">Fri</th>
                        <th style="text-align: center; padding: 10px; color: #666;">Sat</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
        `;
        
        // Add the dates for each day
        for (let i = 0; i < 7; i++) {
            const day = days[i];
            const isToday = day.toDateString() === new Date().toDateString();
            const isSelected = day.toDateString() === currentDate.toDateString();
            
            let cellStyle = 'padding: 10px; text-align: center;';
            
            if (isSelected) {
                cellStyle += 'background-color: var(--color-burgundy); color: white;';
            } else if (isToday) {
                cellStyle += 'background-color: #f0f0f0;';
            }
            
            const dateUrl = `?date=${day.getFullYear()}-${String(day.getMonth() + 1).padStart(2, '0')}-${String(day.getDate()).padStart(2, '0')}`;
            
            calendarHTML += `
                <td style="${cellStyle}">
                    <a href="${dateUrl}" style="display: block; text-decoration: none; color: ${isSelected ? 'white' : 'inherit'};">
                        <div style="font-weight: 500;">${day.getDate()}</div>
                        <div style="font-size: 0.8rem;">${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>
                    </a>
                </td>
            `;
        }
        
        calendarHTML += `
                    </tr>
                </tbody>
            </table>
        `;
        
        calendarContainer.innerHTML = calendarHTML;
        
        // Add event listeners for week navigation
        document.getElementById('prev-week').addEventListener('click', function() {
            currentDate.setDate(currentDate.getDate() - 7);
            renderWeeklyCalendar();
        });
        
        document.getElementById('next-week').addEventListener('click', function() {
            currentDate.setDate(currentDate.getDate() + 7);
            renderWeeklyCalendar();
        });
    }
    
    // Initialize calendar
    renderWeeklyCalendar();
});
</script>

<?php include('includes/footer.php'); ?> 