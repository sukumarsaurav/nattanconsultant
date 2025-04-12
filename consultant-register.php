<?php
// Include database configuration
require_once 'includes/config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['consultant_id'])) {
    header("Location: consultant-dashboard.php");
    exit();
}

$page_title = "Consultant Registration | CANEXT Immigration";
require_once 'includes/header.php';

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM consultants WHERE email = ?";
        $result = executeQuery($check_query, [$email]);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $error = "Email already registered";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new consultant
            $consultant_data = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashed_password,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $insert_success = insertData('consultants', $consultant_data);
            
            if ($insert_success) {
                $success = "Registration successful! Your account is pending approval.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 80px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1 data-aos="fade-up">Join Our Consultant Network</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 700px; margin: 20px auto 0;">Register as a licensed immigration consultant to connect with clients and grow your practice</p>
    </div>
</section>

<!-- Registration Form -->
<section class="section" style="padding: 60px 0;">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <!-- Steps Indicator -->
            <div class="registration-steps" style="display: flex; justify-content: space-between; margin-bottom: 40px;">
                <div class="step active" style="flex: 1; text-align: center;">
                    <div style="width: 40px; height: 40px; background-color: var(--color-burgundy); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">1</div>
                    <div style="font-weight: 500; color: var(--color-burgundy);">Registration</div>
                </div>
                <div style="flex: 0 0 80px; height: 2px; background-color: #e0e0e0; margin-top: 20px;"></div>
                <div class="step" style="flex: 1; text-align: center;">
                    <div style="width: 40px; height: 40px; background-color: #e0e0e0; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">2</div>
                    <div style="color: #666;">Verification</div>
                </div>
                <div style="flex: 0 0 80px; height: 2px; background-color: #e0e0e0; margin-top: 20px;"></div>
                <div class="step" style="flex: 1; text-align: center;">
                    <div style="width: 40px; height: 40px; background-color: #e0e0e0; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">3</div>
                    <div style="color: #666;">Choose Plan</div>
                </div>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 30px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" style="padding: 15px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 30px;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <p>Thank you for registering as a consultant!</p>
                    <p>Our team will review your application and contact you shortly.</p>
                    <p>You can <a href="consultant-login.php" style="color: var(--color-burgundy); font-weight: 600;">log in</a> once your account is approved.</p>
                </div>
            <?php else: ?>
                <div style="background-color: var(--color-light); border-radius: 10px; padding: 40px; margin-bottom: 30px;">
                    <h2 style="margin-bottom: 30px; color: var(--color-burgundy); font-size: 1.6rem; text-align: center;">Consultant Registration Form</h2>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="consultantRegForm">
                        <!-- Personal Information -->
                        <h3 style="margin-bottom: 20px; color: var(--color-dark); font-size: 1.3rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">Personal Information</h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                            <div class="form-group">
                                <label for="first_name" style="display: block; margin-bottom: 8px; font-weight: 500;">First Name <span style="color: red;">*</span></label>
                                <input type="text" id="first_name" name="first_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Last Name <span style="color: red;">*</span></label>
                                <input type="text" id="last_name" name="last_name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                            <div class="form-group">
                                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 500;">Email Address <span style="color: red;">*</span></label>
                                <input type="email" id="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 500;">Phone Number <span style="color: red;">*</span></label>
                                <input type="tel" id="phone" name="phone" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                            </div>
                        </div>
                        
                        <!-- Professional Information -->
                        <h3 style="margin-bottom: 20px; color: var(--color-dark); font-size: 1.3rem; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 40px;">Professional Information</h3>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="rcic_number" style="display: block; margin-bottom: 8px; font-weight: 500;">RCIC Number <span style="color: red;">*</span></label>
                            <input type="text" id="rcic_number" name="rcic_number" required style="width: 100%; max-width: 250px; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                            <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Your Regulated Canadian Immigration Consultant registration number</p>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Specializations</label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="specialization[]" value="Express Entry">
                                    Express Entry
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="specialization[]" value="Family Sponsorship">
                                    Family Sponsorship
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="specialization[]" value="Study Permits">
                                    Study Permits
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="specialization[]" value="Work Permits">
                                    Work Permits
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="specialization[]" value="Business Immigration">
                                    Business Immigration
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="specialization[]" value="Refugee Claims">
                                    Refugee Claims
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Languages Spoken</label>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="English">
                                    English
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="French">
                                    French
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="Spanish">
                                    Spanish
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="Mandarin">
                                    Mandarin
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="Hindi">
                                    Hindi
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="Punjabi">
                                    Punjabi
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="Arabic">
                                    Arabic
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="languages[]" value="Portuguese">
                                    Portuguese
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 30px;">
                            <label for="bio" style="display: block; margin-bottom: 8px; font-weight: 500;">Professional Bio</label>
                            <textarea id="bio" name="bio" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; resize: vertical;"></textarea>
                            <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Provide a brief description of your professional background and expertise</p>
                        </div>
                        
                        <!-- Account Setup -->
                        <h3 style="margin-bottom: 20px; color: var(--color-dark); font-size: 1.3rem; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 40px;">Account Setup</h3>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500;">Password <span style="color: red;">*</span></label>
                            <input type="password" id="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                            <p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Minimum 8 characters, at least one uppercase letter, one lowercase letter, and one number</p>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 30px;">
                            <label for="confirm_password" style="display: block; margin-bottom: 8px; font-weight: 500;">Confirm Password <span style="color: red;">*</span></label>
                            <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        </div>
                        
                        <!-- Terms and Agreement -->
                        <div class="form-group" style="margin-bottom: 30px;">
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                                <input type="checkbox" id="terms_agree" name="terms_agree" required style="margin-top: 3px;">
                                <span>
                                    I agree to the <a href="#" style="color: var(--color-burgundy);">Terms of Service</a> and <a href="#" style="color: var(--color-burgundy);">Privacy Policy</a>. I understand that my information will be verified before my account is approved.
                                </span>
                            </label>
                        </div>
                        
                        <div style="text-align: center;">
                            <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-size: 1rem;">Register</button>
                        </div>
                    </form>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p>Already registered? <a href="consultant-login.php" style="color: var(--color-burgundy); font-weight: 600;">Log in here</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Membership Plans Section -->
<section class="section" style="background-color: var(--color-gold); padding: 60px 0;">
    <div class="container">
        <h2 class="section-title">Membership Plans</h2>
        <p class="section-subtitle" style="max-width: 700px; margin: 0 auto 50px;">Choose a membership plan that suits your needs after your registration is approved</p>
        
        <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            <!-- Bronze Plan -->
            <div style="flex: 1; min-width: 280px; max-width: 350px; background-color: var(--color-light); border-radius: 10px; overflow: hidden;">
                <div style="background-color: #CD7F32; color: white; padding: 20px; text-align: center;">
                    <h3 style="margin-bottom: 10px; font-size: 1.5rem;">Bronze</h3>
                    <div style="font-size: 2rem; font-weight: 700;">$49<span style="font-size: 1rem; font-weight: 400;">/month</span></div>
                </div>
                <div style="padding: 30px;">
                    <ul style="margin-bottom: 25px; color: #555;">
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Basic profile listing
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            5 client bookings per month
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Email support
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-times" style="color: #DC3545; position: absolute; left: 0; top: 4px;"></i>
                            Featured profile listing
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-times" style="color: #DC3545; position: absolute; left: 0; top: 4px;"></i>
                            Client messaging system
                        </li>
                    </ul>
                    <div style="text-align: center;">
                        <button disabled class="btn btn-primary" style="width: 100%; opacity: 0.6; cursor: not-allowed;">Available After Approval</button>
                    </div>
                </div>
            </div>
            
            <!-- Silver Plan -->
            <div style="flex: 1; min-width: 280px; max-width: 350px; background-color: var(--color-light); border-radius: 10px; overflow: hidden; transform: scale(1.05); z-index: 1; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div style="background-color: #C0C0C0; color: white; padding: 20px; text-align: center; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 10px; right: -30px; background-color: var(--color-burgundy); color: white; transform: rotate(45deg); padding: 5px 40px; font-size: 0.8rem; font-weight: 600;">POPULAR</div>
                    <h3 style="margin-bottom: 10px; font-size: 1.5rem;">Silver</h3>
                    <div style="font-size: 2rem; font-weight: 700;">$99<span style="font-size: 1rem; font-weight: 400;">/month</span></div>
                </div>
                <div style="padding: 30px;">
                    <ul style="margin-bottom: 25px; color: #555;">
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Enhanced profile listing
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            20 client bookings per month
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Priority email support
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Featured profile listing
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-times" style="color: #DC3545; position: absolute; left: 0; top: 4px;"></i>
                            Client messaging system
                        </li>
                    </ul>
                    <div style="text-align: center;">
                        <button disabled class="btn btn-primary" style="width: 100%; opacity: 0.6; cursor: not-allowed;">Available After Approval</button>
                    </div>
                </div>
            </div>
            
            <!-- Gold Plan -->
            <div style="flex: 1; min-width: 280px; max-width: 350px; background-color: var(--color-light); border-radius: 10px; overflow: hidden;">
                <div style="background-color: #FFD700; color: #333; padding: 20px; text-align: center;">
                    <h3 style="margin-bottom: 10px; font-size: 1.5rem;">Gold</h3>
                    <div style="font-size: 2rem; font-weight: 700;">$149<span style="font-size: 1rem; font-weight: 400;">/month</span></div>
                </div>
                <div style="padding: 30px;">
                    <ul style="margin-bottom: 25px; color: #555;">
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Premium profile listing
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Unlimited client bookings
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            24/7 priority support
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Top featured profile listing
                        </li>
                        <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                            <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                            Client messaging system
                        </li>
                    </ul>
                    <div style="text-align: center;">
                        <button disabled class="btn btn-primary" style="width: 100%; opacity: 0.6; cursor: not-allowed;">Available After Approval</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const form = document.getElementById('consultantRegForm');
    
    // Password validation
    passwordInput.addEventListener('input', function() {
        // Password must be at least 8 characters with at least one uppercase, one lowercase, and one number
        const passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        
        if (!passRegex.test(this.value)) {
            this.setCustomValidity('Password must be at least 8 characters and include at least one uppercase letter, one lowercase letter, and one number');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function() {
        if (this.value !== passwordInput.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (!document.getElementById('terms_agree').checked) {
            e.preventDefault();
            alert('You must agree to the Terms of Service and Privacy Policy to continue.');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 