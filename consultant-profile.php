<?php
$page_title = "My Profile | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get consultant information
$consultant_query = "SELECT * FROM consultants WHERE id = ?";
$consultant_result = executeQuery($consultant_query, [$consultant_id]);
$consultant = mysqli_fetch_assoc($consultant_result);
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>My Profile</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">View and manage your consultant profile</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Consultant Profile Card -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; margin-bottom: 30px;">
            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                <!-- Profile Image -->
                <div style="flex: 0 0 150px;">
                    <?php if (!empty($consultant['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($consultant['profile_image']); ?>" alt="Profile" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;">
                    <?php else: ?>
                        <div style="width: 150px; height: 150px; background-color: #f0f0f0; border-radius: 10px; display: flex; justify-content: center; align-items: center;">
                            <i class="fas fa-user" style="font-size: 50px; color: #999;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Profile Information -->
                <div style="flex: 1; min-width: 300px;">
                    <h2 style="margin: 0 0 10px; color: var(--color-burgundy);"><?php echo htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']); ?></h2>
                    
                    <?php if (!empty($consultant['rcic_number'])): ?>
                        <p style="margin: 0 0 15px; color: #666;">RCIC #: <?php echo htmlspecialchars($consultant['rcic_number']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($consultant['bio'])): ?>
                        <p style="margin: 0 0 15px;"><?php echo htmlspecialchars($consultant['bio']); ?></p>
                    <?php else: ?>
                        <p style="margin: 0 0 15px; color: #999;">No bio information added yet.</p>
                    <?php endif; ?>
                    
                    <a href="consultant-profile-edit.php" style="display: inline-block; padding: 8px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem; margin-top: 15px;">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Additional Profile Sections -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Membership Plan -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px;">
                <h3 style="margin: 0 0 15px; color: var(--color-burgundy); font-size: 1.2rem;">
                    <i class="fas fa-crown" style="margin-right: 10px;"></i> Membership Plan
                </h3>
                
                <?php 
                $plan_colors = [
                    'bronze' => '#CD7F32',
                    'silver' => '#C0C0C0',
                    'gold' => '#FFD700'
                ];
                $current_plan = $consultant['membership_plan'] ?? 'bronze';
                $plan_color = $plan_colors[$current_plan] ?? $plan_colors['bronze'];
                ?>
                
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: <?php echo $plan_color; ?>; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-crown" style="color: <?php echo $current_plan === 'gold' ? '#333' : 'white'; ?>;"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--color-dark);"><?php echo ucfirst($current_plan); ?> Plan</div>
                        <?php if ($current_plan !== 'gold'): ?>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">
                                <a href="consultant-membership.php" style="color: var(--color-burgundy); text-decoration: none;">
                                    Upgrade your plan <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="font-size: 0.9rem; color: #666; margin-top: 5px;">You are on our highest plan</div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php
                // Define plan features based on membership level
                $features = [
                    'bronze' => [
                        'Basic profile listing',
                        '5 client bookings per month',
                        'Email support'
                    ],
                    'silver' => [
                        'Enhanced profile listing',
                        '20 client bookings per month',
                        'Priority email support',
                        'Featured profile listing'
                    ],
                    'gold' => [
                        'Premium profile listing',
                        'Unlimited client bookings',
                        '24/7 priority support',
                        'Top featured profile listing',
                        'Client messaging system'
                    ]
                ];
                ?>
                
                <div>
                    <div style="font-weight: 500; margin-bottom: 10px; color: #666;">Plan features:</div>
                    <ul style="margin: 0; padding-left: 25px; color: #666;">
                        <?php foreach ($features[$current_plan] as $feature): ?>
                            <li style="margin-bottom: 5px;"><?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Specializations -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px;">
                <h3 style="margin: 0 0 15px; color: var(--color-burgundy); font-size: 1.2rem;">
                    <i class="fas fa-star" style="margin-right: 10px;"></i> Specializations
                </h3>
                
                <?php if (!empty($consultant['specialization'])): ?>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach (explode(',', $consultant['specialization']) as $specialization): ?>
                            <span style="display: inline-block; padding: 6px 12px; background-color: #f7f7f7; border-radius: 20px; font-size: 0.9rem;">
                                <?php echo htmlspecialchars(trim($specialization)); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #999;">No specializations added yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- Languages -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px;">
                <h3 style="margin: 0 0 15px; color: var(--color-burgundy); font-size: 1.2rem;">
                    <i class="fas fa-language" style="margin-right: 10px;"></i> Languages
                </h3>
                
                <?php if (!empty($consultant['languages'])): ?>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach (explode(',', $consultant['languages']) as $language): ?>
                            <span style="display: inline-block; padding: 6px 12px; background-color: #f7f7f7; border-radius: 20px; font-size: 0.9rem;">
                                <?php echo htmlspecialchars(trim($language)); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #999;">No languages added yet.</p>
                <?php endif; ?>
            </div>
            
            <!-- Consultation Fees -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px;">
                <h3 style="margin: 0 0 15px; color: var(--color-burgundy); font-size: 1.2rem;">
                    <i class="fas fa-dollar-sign" style="margin-right: 10px;"></i> Consultation Fees
                </h3>
                
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 500; margin-bottom: 5px;">Video Consultation</div>
                    <div><?php echo !empty($consultant['video_consultation_fee']) ? '$' . number_format($consultant['video_consultation_fee'], 2) : 'Not set'; ?></div>
                </div>
                
                <div style="margin-bottom: 10px;">
                    <div style="font-weight: 500; margin-bottom: 5px;">Phone Consultation</div>
                    <div><?php echo !empty($consultant['phone_consultation_fee']) ? '$' . number_format($consultant['phone_consultation_fee'], 2) : 'Not set'; ?></div>
                </div>
                
                <div>
                    <div style="font-weight: 500; margin-bottom: 5px;">In-Person Consultation</div>
                    <div><?php echo !empty($consultant['in_person_consultation_fee']) ? '$' . number_format($consultant['in_person_consultation_fee'], 2) : 'Not set'; ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?>

