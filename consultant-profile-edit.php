<?php
$page_title = "Edit Profile | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get consultant information
$consultant_query = "SELECT * FROM consultants WHERE id = ?";
$consultant_result = executeQuery($consultant_query, [$consultant_id]);
$consultant = mysqli_fetch_assoc($consultant_result);

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Collect form data
    $bio = sanitizeInput($_POST['bio'] ?? null);
    $office_address = sanitizeInput($_POST['office_address'] ?? null);
    $office_hours = sanitizeInput($_POST['office_hours'] ?? null);
    
    // Check if this is a consultation fee update
    if (isset($_POST['video_consultation_fee']) || isset($_POST['phone_consultation_fee']) || isset($_POST['in_person_consultation_fee'])) {
        // Consultation fees form was submitted
        $video_consultation_fee = sanitizeInput($_POST['video_consultation_fee'] ?? $consultant['video_consultation_fee']);
        $phone_consultation_fee = sanitizeInput($_POST['phone_consultation_fee'] ?? $consultant['phone_consultation_fee']);
        $in_person_consultation_fee = sanitizeInput($_POST['in_person_consultation_fee'] ?? $consultant['in_person_consultation_fee']);
        
        $video_consultation_available = isset($_POST['video_consultation_available']) ? 1 : 0;
        $phone_consultation_available = isset($_POST['phone_consultation_available']) ? 1 : 0;
        $in_person_consultation_available = isset($_POST['in_person_consultation_available']) ? 1 : 0;
        
        // Update consultant fees information
        $data = [
            'video_consultation_fee' => $video_consultation_fee,
            'phone_consultation_fee' => $phone_consultation_fee,
            'in_person_consultation_fee' => $in_person_consultation_fee,
            'video_consultation_available' => $video_consultation_available,
            'phone_consultation_available' => $phone_consultation_available,
            'in_person_consultation_available' => $in_person_consultation_available
        ];
        
        $update_result = updateData('consultants', $data, 'id = ?', [$consultant_id]);
        
        if ($update_result) {
            $success_message = 'Consultation fees updated successfully!';
            
            // Refresh consultant data
            $consultant_result = executeQuery($consultant_query, [$consultant_id]);
            $consultant = mysqli_fetch_assoc($consultant_result);
        } else {
            $error_message = 'Error updating consultation fees. Please try again.';
        }
    } else {
        // Profile information form was submitted
        // Update consultant information if no error occurred
        if (empty($error_message)) {
            $data = [
                'bio' => $bio,
                'office_address' => $office_address,
                'office_hours' => $office_hours
            ];
            
            $update_result = updateData('consultants', $data, 'id = ?', [$consultant_id]);
            
            if ($update_result) {
                $success_message = 'Profile updated successfully!';
                
                // Refresh consultant data
                $consultant_result = executeQuery($consultant_query, [$consultant_id]);
                $consultant = mysqli_fetch_assoc($consultant_result);
            } else {
                $error_message = 'Error updating profile. Please try again.';
            }
        }
    }
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $password_query = "SELECT password FROM consultants WHERE id = ?";
    $password_result = executeQuery($password_query, [$consultant_id]);
    $password_row = mysqli_fetch_assoc($password_result);
    
    if (password_verify($current_password, $password_row['password'])) {
        // Verify new passwords match
        if ($new_password === $confirm_password) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_result = updateData('consultants', ['password' => $hashed_password], 'id = ?', [$consultant_id]);
            
            if ($update_result) {
                $success_message = 'Password updated successfully!';
            } else {
                $error_message = 'Error updating password. Please try again.';
            }
        } else {
            $error_message = 'New passwords do not match.';
        }
    } else {
        $error_message = 'Current password is incorrect.';
    }
}

// Available specializations and languages options
$specialization_options = [
    'Express Entry', 'Provincial Nominee Program', 'Family Sponsorship', 'Study Permits', 
    'Work Permits', 'Business Immigration', 'Refugee Claims', 'Citizenship Applications',
    'Humanitarian and Compassionate Applications', 'Appeals and Judicial Reviews'
];

$language_options = [
    'English', 'French', 'Spanish', 'Mandarin', 'Cantonese', 'Hindi', 'Punjabi', 
    'Arabic', 'Urdu', 'Tagalog', 'Portuguese', 'Russian', 'Korean', 'Japanese'
];

// Parse consultant's current specializations and languages into arrays
$current_specializations = !empty($consultant['specialization']) ? explode(',', $consultant['specialization']) : [];
$current_languages = !empty($consultant['languages']) ? explode(',', $consultant['languages']) : [];
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>Edit Profile</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">Update your profile information and settings</p>
    </div>
</section>

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
        
        <!-- Profile Navigation Tabs -->
        <div style="display: flex; border-bottom: 1px solid #e0e0e0; margin-bottom: 30px;">
            <a href="#profile-info" class="profile-tab active" style="padding: 12px 20px; font-weight: 500; color: var(--color-burgundy); border-bottom: 2px solid var(--color-burgundy); text-decoration: none;">Profile Information</a>
            <a href="#consultation-fees" class="profile-tab" style="padding: 12px 20px; font-weight: 500; color: #666; text-decoration: none;">Consultation Fees</a>
            <a href="#password" class="profile-tab" style="padding: 12px 20px; font-weight: 500; color: #666; text-decoration: none;">Change Password</a>
        </div>
        
        <!-- Profile Information Form -->
        <div id="profile-info-content" class="tab-content active" style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Profile Information</h2>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <!-- Read-only Personal Information -->
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">First Name</label>
                        <p style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; background-color: #f9f9f9;"><?php echo htmlspecialchars($consultant['first_name']); ?></p>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Last Name</label>
                        <p style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; background-color: #f9f9f9;"><?php echo htmlspecialchars($consultant['last_name']); ?></p>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                        <p style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; background-color: #f9f9f9;"><?php echo htmlspecialchars($consultant['email']); ?></p>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Phone</label>
                        <p style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; background-color: #f9f9f9;"><?php echo htmlspecialchars($consultant['phone']); ?></p>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">RCIC Number</label>
                        <p style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; background-color: #f9f9f9;"><?php echo htmlspecialchars($consultant['rcic_number']); ?></p>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Years of Experience</label>
                        <p style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; background-color: #f9f9f9;"><?php echo htmlspecialchars($consultant['years_experience']); ?></p>
                    </div>
                    
                    <!-- Bio -->
                    <div style="grid-column: 1 / -1;">
                        <label for="bio" style="display: block; margin-bottom: 8px; font-weight: 500;">Professional Bio</label>
                        <textarea name="bio" id="bio" rows="6" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; resize: vertical;"><?php echo htmlspecialchars($consultant['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Office Information -->
                    <div style="grid-column: 1 / -1;">
                        <label for="office_address" style="display: block; margin-bottom: 8px; font-weight: 500;">Office Address</label>
                        <textarea name="office_address" id="office_address" rows="3" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; resize: vertical;"><?php echo htmlspecialchars($consultant['office_address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="grid-column: 1 / -1;">
                        <label for="office_hours" style="display: block; margin-bottom: 8px; font-weight: 500;">Office Hours</label>
                        <textarea name="office_hours" id="office_hours" rows="3" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; resize: vertical;"><?php echo htmlspecialchars($consultant['office_hours'] ?? ''); ?></textarea>
                        <p style="font-size: 0.9rem; color: #666;">Example: Monday - Friday: 9:00 AM - 5:00 PM, Saturday: By appointment, Sunday: Closed</p>
                    </div>
                </div>
                
                <div style="text-align: right;">
                    <button type="submit" name="update_profile" style="padding: 12px 25px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Consultation Fees Form -->
        <div id="consultation-fees-content" class="tab-content" style="display: none; background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Consultation Fees & Availability</h2>
            
            <form action="" method="POST">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <!-- Video Consultation -->
                    <div style="padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h3 style="margin: 0; font-size: 1.1rem;">Video Consultation</h3>
                            <div style="display: flex; align-items: center;">
                                <label class="switch" style="position: relative; display: inline-block; width: 60px; height: 34px;">
                                    <input type="checkbox" name="video_consultation_available" <?php echo $consultant['video_consultation_available'] ? 'checked' : ''; ?> style="opacity: 0; width: 0; height: 0;">
                                    <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" class="slider"></span>
                                </label>
                                <span style="margin-left: 10px; font-size: 0.9rem;"><?php echo $consultant['video_consultation_available'] ? 'Available' : 'Not Available'; ?></span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label for="video_consultation_fee" style="display: block; margin-bottom: 8px; font-weight: 500;">Fee ($)</label>
                            <input type="number" name="video_consultation_fee" id="video_consultation_fee" value="<?php echo htmlspecialchars($consultant['video_consultation_fee']); ?>" min="0" step="0.01" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    
                    <!-- Phone Consultation -->
                    <div style="padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h3 style="margin: 0; font-size: 1.1rem;">Phone Consultation</h3>
                            <div style="display: flex; align-items: center;">
                                <label class="switch" style="position: relative; display: inline-block; width: 60px; height: 34px;">
                                    <input type="checkbox" name="phone_consultation_available" <?php echo $consultant['phone_consultation_available'] ? 'checked' : ''; ?> style="opacity: 0; width: 0; height: 0;">
                                    <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" class="slider"></span>
                                </label>
                                <span style="margin-left: 10px; font-size: 0.9rem;"><?php echo $consultant['phone_consultation_available'] ? 'Available' : 'Not Available'; ?></span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label for="phone_consultation_fee" style="display: block; margin-bottom: 8px; font-weight: 500;">Fee ($)</label>
                            <input type="number" name="phone_consultation_fee" id="phone_consultation_fee" value="<?php echo htmlspecialchars($consultant['phone_consultation_fee']); ?>" min="0" step="0.01" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    
                    <!-- In-Person Consultation -->
                    <div style="padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h3 style="margin: 0; font-size: 1.1rem;">In-Person Consultation</h3>
                            <div style="display: flex; align-items: center;">
                                <label class="switch" style="position: relative; display: inline-block; width: 60px; height: 34px;">
                                    <input type="checkbox" name="in_person_consultation_available" <?php echo $consultant['in_person_consultation_available'] ? 'checked' : ''; ?> style="opacity: 0; width: 0; height: 0;">
                                    <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;" class="slider"></span>
                                </label>
                                <span style="margin-left: 10px; font-size: 0.9rem;"><?php echo $consultant['in_person_consultation_available'] ? 'Available' : 'Not Available'; ?></span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label for="in_person_consultation_fee" style="display: block; margin-bottom: 8px; font-weight: 500;">Fee ($)</label>
                            <input type="number" name="in_person_consultation_fee" id="in_person_consultation_fee" value="<?php echo htmlspecialchars($consultant['in_person_consultation_fee']); ?>" min="0" step="0.01" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                </div>
                
                <div style="text-align: right;">
                    <button type="submit" name="update_profile" style="padding: 12px 25px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Password Change Form -->
        <div id="password-content" class="tab-content" style="display: none; background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Change Password</h2>
            
            <form action="" method="POST">
                <div style="max-width: 500px; margin: 0 auto;">
                    <div style="margin-bottom: 20px;">
                        <label for="current_password" style="display: block; margin-bottom: 8px; font-weight: 500;">Current Password *</label>
                        <input type="password" name="current_password" id="current_password" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="new_password" style="display: block; margin-bottom: 8px; font-weight: 500;">New Password *</label>
                        <input type="password" name="new_password" id="new_password" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 500;">Confirm New Password *</label>
                        <input type="password" name="confirm_password" id="confirm_password" required style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" name="update_password" style="padding: 12px 25px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-key"></i> Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Logout Button -->
        <div style="text-align: center; margin-top: 30px;">
            <a href="logout.php" style="display: inline-block; padding: 12px 25px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</section>

<style>
/* Toggle switch styling */
.switch input:checked + .slider {
    background-color: var(--color-burgundy);
}

.switch input:checked + .slider:before {
    transform: translateX(26px);
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
</style>

<script>
// Tab navigation
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.profile-tab');
    const contents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active tab
            tabs.forEach(t => {
                t.classList.remove('active');
                t.style.borderBottom = 'none';
                t.style.color = '#666';
            });
            this.classList.add('active');
            this.style.borderBottom = '2px solid var(--color-burgundy)';
            this.style.color = 'var(--color-burgundy)';
            
            // Show corresponding content
            const target = this.getAttribute('href').substring(1);
            contents.forEach(content => {
                content.style.display = 'none';
            });
            document.getElementById(target + '-content').style.display = 'block';
        });
    });
    
    // Image preview
    const profileInput = document.getElementById('profile_image');
    const profilePreview = document.getElementById('profile-preview');
    
    if (profileInput && profilePreview) {
        profileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (profilePreview.tagName.toLowerCase() === 'img') {
                        profilePreview.src = e.target.result;
                    } else {
                        // Create an image if the preview is currently a div
                        const img = document.createElement('img');
                        img.id = 'profile-preview';
                        img.src = e.target.result;
                        img.style.width = '150px';
                        img.style.height = '150px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '10px';
                        
                        profilePreview.parentNode.replaceChild(img, profilePreview);
                    }
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Toggle switch labels
    const toggles = document.querySelectorAll('input[type="checkbox"]');
    toggles.forEach(toggle => {
        if (toggle.name.includes('consultation_available')) {
            toggle.addEventListener('change', function() {
                const label = this.parentNode.nextElementSibling;
                label.textContent = this.checked ? 'Available' : 'Not Available';
            });
        }
    });
});
</script>

<?php include('includes/footer.php'); ?>

