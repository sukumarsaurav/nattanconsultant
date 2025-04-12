<?php
$page_title = "Consultant Dashboard | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Function to get record count
function getRecordCount($table, $condition = '1=1') {
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $condition";
    $result = executeQuery($sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
    
    return 0;
}

// Get consultant information
$consultant_query = "SELECT * FROM consultants WHERE id = ?";
$consultant_result = executeQuery($consultant_query, [$consultant_id]);
$consultant = mysqli_fetch_assoc($consultant_result);

// Get dashboard statistics
$total_appointments = getRecordCount('appointments', "1=1");
$upcoming_appointments = getRecordCount('appointments', "appointment_datetime >= NOW() AND status IN ('pending', 'confirmed')");
$today_appointments = getRecordCount('appointments', "DATE(appointment_datetime) = CURDATE()");
$completed_appointments = getRecordCount('appointments', "status = 'completed'");

// Get recent appointments
$recent_sql = "SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5";
$recent_result = executeQuery($recent_sql);

// Get upcoming appointments for today and tomorrow
$upcoming_sql = "SELECT * FROM appointments 
                WHERE appointment_datetime >= NOW() 
                AND appointment_datetime <= DATE_ADD(NOW(), INTERVAL 2 DAY)
                AND status IN ('pending', 'confirmed')
                ORDER BY appointment_datetime ASC 
                LIMIT 5";
$upcoming_result = executeQuery($upcoming_sql);
?>

<!-- Consultant Dashboard Page -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1 data-aos="fade-up">Consultant Dashboard</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 600px; margin: 20px auto 0;">Manage your consultation bookings and client appointments</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 40px;">
            <!-- Welcome Section -->
            <div style="flex: 1; min-width: 300px;">
                <h2 style="color: var(--color-burgundy); margin-bottom: 10px;">Welcome, <?php echo $consultant['first_name']; ?>!</h2>
                <p style="color: #666; margin-bottom: 20px;">Here's an overview of your consultation bookings and upcoming appointments.</p>
            </div>
            
            <!-- Profile Completion Section -->
            <div style="flex: 0 0 300px; background-color: var(--color-gold); padding: 20px; border-radius: 10px; display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background-color: var(--color-burgundy); border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-user" style="color: white; font-size: 20px;"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 5px; font-size: 1rem; color: var(--color-burgundy);">Profile Completion</h3>
                    <div style="width: 180px; height: 10px; background-color: #e0e0e0; border-radius: 5px; overflow: hidden;">
                        <?php
                        // Calculate profile completion percentage based on fields
                        $fields = ['profile_image', 'bio', 'specialization', 'languages', 'consultation_fee', 'office_address', 'office_hours'];
                        $filled = 0;
                        foreach ($fields as $field) {
                            if (!empty($consultant[$field])) $filled++;
                        }
                        $percentage = ceil(($filled / count($fields)) * 100);
                        ?>
                        <div style="width: <?php echo $percentage; ?>%; height: 100%; background-color: var(--color-burgundy);"></div>
                    </div>
                    <div style="font-size: 0.8rem; margin-top: 5px;">
                        <a href="consultant-profile-edit.php" style="color: var(--color-burgundy); text-decoration: none;">Update Profile</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <!-- Total Appointments Card -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; display: flex; align-items: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 10px; display: flex; justify-content: center; align-items: center; margin-right: 20px;">
                    <i class="fas fa-calendar-check" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.8rem; margin: 0 0 5px; color: var(--color-dark);"><?php echo $total_appointments; ?></h3>
                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Total Appointments</p>
                </div>
            </div>
            
            <!-- Today's Appointments Card -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; display: flex; align-items: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 10px; display: flex; justify-content: center; align-items: center; margin-right: 20px;">
                    <i class="fas fa-calendar-day" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.8rem; margin: 0 0 5px; color: var(--color-dark);"><?php echo $today_appointments; ?></h3>
                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Today's Appointments</p>
                </div>
            </div>
            
            <!-- Upcoming Appointments Card -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; display: flex; align-items: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 10px; display: flex; justify-content: center; align-items: center; margin-right: 20px;">
                    <i class="fas fa-calendar-alt" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.8rem; margin: 0 0 5px; color: var(--color-dark);"><?php echo $upcoming_appointments; ?></h3>
                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Upcoming Appointments</p>
                </div>
            </div>
            
            <!-- Completed Appointments Card -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; display: flex; align-items: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 10px; display: flex; justify-content: center; align-items: center; margin-right: 20px;">
                    <i class="fas fa-check-circle" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.8rem; margin: 0 0 5px; color: var(--color-dark);"><?php echo $completed_appointments; ?></h3>
                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Completed Consultations</p>
                </div>
            </div>
        </div>
        
        <!-- Main Dashboard Content -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Recent Bookings Section -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 0; overflow: hidden;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #f0f0f0;">
                    <h3 style="margin: 0; color: var(--color-burgundy); font-size: 1.2rem;">Recent Bookings</h3>
                    <a href="consultant-bookings.php" style="color: var(--color-burgundy); text-decoration: none; font-size: 0.9rem; font-weight: 500;">View All</a>
                </div>
                <div style="padding: 0 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <th style="text-align: left; padding: 15px 10px; color: #666; font-weight: 500; font-size: 0.9rem;">Client</th>
                                <th style="text-align: left; padding: 15px 10px; color: #666; font-weight: 500; font-size: 0.9rem;">Type</th>
                                <th style="text-align: left; padding: 15px 10px; color: #666; font-weight: 500; font-size: 0.9rem;">Date & Time</th>
                                <th style="text-align: left; padding: 15px 10px; color: #666; font-weight: 500; font-size: 0.9rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($recent_result && mysqli_num_rows($recent_result) > 0): ?>
                                <?php while($booking = mysqli_fetch_assoc($recent_result)): ?>
                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                        <td style="padding: 15px 10px;">
                                            <div>
                                                <div style="font-weight: 500; color: var(--color-dark);"><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></div>
                                                <div style="font-size: 0.8rem; color: #666;"><?php echo $booking['email']; ?></div>
                                            </div>
                                        </td>
                                        <td style="padding: 15px 10px;"><?php echo $booking['consultation_type']; ?></td>
                                        <td style="padding: 15px 10px;">
                                            <div style="font-weight: 500; color: var(--color-dark);"><?php echo date('M j, Y', strtotime($booking['appointment_datetime'])); ?></div>
                                            <div style="font-size: 0.8rem; color: #666;"><?php echo date('g:i A', strtotime($booking['appointment_datetime'])); ?></div>
                                        </td>
                                        <td style="padding: 15px 10px;">
                                            <?php 
                                                $status_color = '';
                                                switch(strtolower($booking['status'])) {
                                                    case 'pending': $status_color = '#f5a623'; break;
                                                    case 'confirmed': $status_color = '#4caf50'; break;
                                                    case 'completed': $status_color = '#2196f3'; break;
                                                    case 'cancelled': $status_color = '#f44336'; break;
                                                    case 'no-show': $status_color = '#9e9e9e'; break;
                                                    default: $status_color = '#9e9e9e';
                                                }
                                            ?>
                                            <span style="display: inline-block; padding: 5px 10px; background-color: <?php echo $status_color; ?>; color: white; border-radius: 20px; font-size: 0.8rem;">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 30px; color: #666;">No recent bookings found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Upcoming Appointments Section -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 0; overflow: hidden;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #f0f0f0;">
                    <h3 style="margin: 0; color: var(--color-burgundy); font-size: 1.2rem;">Upcoming Consultations</h3>
                    <a href="consultant-bookings.php?filter=upcoming" style="color: var(--color-burgundy); text-decoration: none; font-size: 0.9rem; font-weight: 500;">View All</a>
                </div>
                <div style="padding: 20px;">
                    <?php if($upcoming_result && mysqli_num_rows($upcoming_result) > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php while($upcoming = mysqli_fetch_assoc($upcoming_result)): 
                                $is_today = date('Y-m-d', strtotime($upcoming['appointment_datetime'])) === date('Y-m-d');
                                $date_label = $is_today ? 'Today' : 'Tomorrow';
                            ?>
                                <div style="display: flex; align-items: center; background-color: #f9f9f9; border-radius: 8px; padding: 15px; gap: 15px;">
                                    <div style="text-align: center; padding-right: 15px; border-right: 1px solid #eee;">
                                        <div style="font-size: 0.8rem; color: #666;"><?php echo $date_label; ?></div>
                                        <div style="font-weight: 600; color: var(--color-burgundy); font-size: 1.1rem;"><?php echo date('g:i A', strtotime($upcoming['appointment_datetime'])); ?></div>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <div style="font-weight: 500; color: var(--color-dark); margin-bottom: 3px;"><?php echo $upcoming['first_name'] . ' ' . $upcoming['last_name']; ?></div>
                                        <div style="font-size: 0.9rem; color: #666;"><?php echo $upcoming['consultation_type']; ?></div>
                                    </div>
                                    <div>
                                        <?php 
                                            $status_color = '';
                                            switch(strtolower($upcoming['status'])) {
                                                case 'pending': $status_color = '#f5a623'; break;
                                                case 'confirmed': $status_color = '#4caf50'; break;
                                                default: $status_color = '#9e9e9e';
                                            }
                                        ?>
                                        <span style="display: inline-block; padding: 5px 10px; background-color: <?php echo $status_color; ?>; color: white; border-radius: 20px; font-size: 0.8rem;">
                                            <?php echo ucfirst($upcoming['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 30px; color: #666;">
                            <p>No upcoming appointments for today or tomorrow.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Mini Calendar -->
        <div style="margin-top: 30px; background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 0; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #f0f0f0;">
                <h3 style="margin: 0; color: var(--color-burgundy); font-size: 1.2rem;">Monthly Calendar</h3>
                <div>
                    <button id="prev-month" style="background: none; border: none; cursor: pointer; color: #666;"><i class="fas fa-chevron-left"></i></button>
                    <span id="current-month" style="margin: 0 10px; font-weight: 500;"><?php echo date('F Y'); ?></span>
                    <button id="next-month" style="background: none; border: none; cursor: pointer; color: #666;"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div style="padding: 20px;">
                <div id="consultant-calendar" style="width: 100%;">
                    <!-- Calendar will be rendered by JavaScript -->
                </div>
            </div>
        </div>
        
        <!-- Quick Links Section -->
        <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 15px;">
                    <i class="fas fa-user-edit" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <h3 style="margin: 0 0 10px; color: var(--color-dark); font-size: 1.1rem;">Update Profile</h3>
                <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem;">Keep your profile information up to date to attract more clients</p>
                <a href="consultant-profile-edit.php" style="display: inline-block; padding: 8px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">Edit Profile</a>
            </div>
            
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 15px;">
                    <i class="fas fa-calendar-plus" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <h3 style="margin: 0 0 10px; color: var(--color-dark); font-size: 1.1rem;">View All Bookings</h3>
                <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem;">Access and manage all your consultation bookings</p>
                <a href="consultant-bookings.php" style="display: inline-block; padding: 8px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">View Bookings</a>
            </div>
            
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="width: 60px; height: 60px; background-color: rgba(var(--color-burgundy-rgb), 0.1); border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 15px;">
                    <i class="fas fa-cog" style="color: var(--color-burgundy); font-size: 24px;"></i>
                </div>
                <h3 style="margin: 0 0 10px; color: var(--color-dark); font-size: 1.1rem;">Account Settings</h3>
                <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem;">Update your account settings and preferences</p>
                <a href="consultant-settings.php" style="display: inline-block; padding: 8px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">Settings</a>
            </div>
        </div>
    </div>
</section>

<!-- Calendar JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple calendar implementation
    const calendarContainer = document.getElementById('consultant-calendar');
    const currentMonthElement = document.getElementById('current-month');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    
    // Current date tracking
    let currentDate = new Date();
    
    // Functions to navigate between months
    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        
        // Update the month/year display
        currentMonthElement.textContent = new Date(year, month, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        
        // Get the first day of the month
        const firstDay = new Date(year, month, 1).getDay();
        
        // Get the number of days in the month
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Create calendar HTML
        let calendarHTML = `
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
        `;
        
        let day = 1;
        const rows = Math.ceil((firstDay + daysInMonth) / 7);
        
        for (let i = 0; i < rows; i++) {
            calendarHTML += '<tr>';
            
            for (let j = 0; j < 7; j++) {
                if ((i === 0 && j < firstDay) || day > daysInMonth) {
                    calendarHTML += '<td style="padding: 10px; text-align: center;"></td>';
                } else {
                    const isToday = day === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear();
                    const dayStyle = isToday ? 
                        'padding: 10px; text-align: center; background-color: var(--color-burgundy); color: white; border-radius: 50%;' :
                        'padding: 10px; text-align: center;';
                    
                    calendarHTML += `<td style="${dayStyle}">${day}</td>`;
                    day++;
                }
            }
            
            calendarHTML += '</tr>';
        }
        
        calendarHTML += `
                </tbody>
            </table>
        `;
        
        calendarContainer.innerHTML = calendarHTML;
    }
    
    // Initialize calendar
    renderCalendar(currentDate);
    
    // Add event listeners for month navigation
    prevMonthButton.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    
    nextMonthButton.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
});
</script>

<?php include('includes/footer.php'); ?> 