<?php
$page_title = "Membership Upgrade | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get consultant information
$consultant_query = "SELECT * FROM consultants WHERE id = ?";
$consultant_result = executeQuery($consultant_query, [$consultant_id]);
$consultant = mysqli_fetch_assoc($consultant_result);

// Handle membership upgrade form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upgrade_membership'])) {
    $new_plan = sanitizeInput($_POST['new_plan']);
    
    // Validate the plan
    if (!in_array($new_plan, ['bronze', 'silver', 'gold'])) {
        $message = 'Invalid membership plan selected.';
        $message_type = 'error';
    } else if ($new_plan === $consultant['membership_plan']) {
        $message = 'You are already on this membership plan.';
        $message_type = 'error';
    } else {
        // In a real application, you would integrate with a payment gateway here
        // For demonstration, we'll just update the membership plan
        
        // Process payment (simulated)
        $payment_successful = true; // In a real app, this would be set based on payment gateway response
        
        if ($payment_successful) {
            // Update consultant membership plan
            $update_query = "UPDATE consultants SET membership_plan = ? WHERE id = ?";
            $update_result = executeQuery($update_query, [$new_plan, $consultant_id]);
            
            if ($update_result) {
                $message = 'Your membership has been successfully upgraded to ' . ucfirst($new_plan) . '!';
                $message_type = 'success';
                
                // Refresh consultant data
                $consultant_result = executeQuery($consultant_query, [$consultant_id]);
                $consultant = mysqli_fetch_assoc($consultant_result);
            } else {
                $message = 'There was an error updating your membership. Please try again.';
                $message_type = 'error';
            }
        } else {
            $message = 'Payment processing failed. Please try again or contact support.';
            $message_type = 'error';
        }
    }
}

// Define membership plan details
$membership_plans = [
    'bronze' => [
        'name' => 'Bronze',
        'price' => 49,
        'color' => '#CD7F32',
        'features' => [
            'Basic profile listing',
            '5 client bookings per month',
            'Email support'
        ],
        'not_included' => [
            'Featured profile listing',
            'Client messaging system'
        ]
    ],
    'silver' => [
        'name' => 'Silver',
        'price' => 99,
        'color' => '#C0C0C0',
        'features' => [
            'Enhanced profile listing',
            '20 client bookings per month',
            'Priority email support',
            'Featured profile listing'
        ],
        'not_included' => [
            'Client messaging system'
        ]
    ],
    'gold' => [
        'name' => 'Gold',
        'price' => 149,
        'color' => '#FFD700',
        'features' => [
            'Premium profile listing',
            'Unlimited client bookings',
            '24/7 priority support',
            'Top featured profile listing',
            'Client messaging system'
        ],
        'not_included' => []
    ]
];
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center; background-color: var(--color-burgundy);">
    <div class="container">
        <h1>Membership Upgrade</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">Enhance your consultant services with our premium plans</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <?php if (!empty($message)): ?>
            <div style="margin-bottom: 30px; padding: 15px; border-radius: 5px; background-color: <?php echo $message_type === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $message_type === 'success' ? '#155724' : '#721c24'; ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Current Membership Status -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 40px;">
            <h2 style="margin: 0 0 20px; color: var(--color-dark); font-size: 1.5rem;">Current Membership</h2>
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background-color: <?php echo $membership_plans[$consultant['membership_plan']]['color']; ?>; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-crown" style="font-size: 2rem; color: white;"></i>
                </div>
                <div>
                    <h3 style="margin: 0 0 5px; font-size: 1.3rem; color: var(--color-dark);"><?php echo ucfirst($consultant['membership_plan']); ?> Plan</h3>
                    <p style="margin: 0; color: #666;">$<?php echo $membership_plans[$consultant['membership_plan']]['price']; ?>/month</p>
                </div>
            </div>
            
            <div>
                <h4 style="margin: 0 0 15px; font-size: 1.1rem; color: var(--color-dark);">Your Plan Features:</h4>
                <ul style="margin: 0; padding-left: 20px; color: #555;">
                    <?php foreach ($membership_plans[$consultant['membership_plan']]['features'] as $feature): ?>
                        <li style="margin-bottom: 10px;"><?php echo $feature; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <!-- Upgrade Options -->
        <h2 style="margin: 0 0 30px; color: var(--color-dark); font-size: 1.5rem; text-align: center;">Upgrade Your Membership</h2>
        
        <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            <?php foreach ($membership_plans as $plan_id => $plan): ?>
                <?php 
                // Skip current plan or plans lower than current
                $current_plan_level = array_search($consultant['membership_plan'], array_keys($membership_plans));
                $this_plan_level = array_search($plan_id, array_keys($membership_plans));
                if ($this_plan_level <= $current_plan_level) continue;
                ?>
                
                <div style="flex: 1; min-width: 280px; max-width: 350px; background-color: var(--color-light); border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                    <div style="background-color: <?php echo $plan['color']; ?>; color: <?php echo $plan_id === 'gold' ? '#333' : 'white'; ?>; padding: 20px; text-align: center;">
                        <h3 style="margin-bottom: 10px; font-size: 1.5rem;"><?php echo $plan['name']; ?></h3>
                        <div style="font-size: 2rem; font-weight: 700;">$<?php echo $plan['price']; ?><span style="font-size: 1rem; font-weight: 400;">/month</span></div>
                    </div>
                    <div style="padding: 30px;">
                        <ul style="margin-bottom: 25px; color: #555; padding-left: 0; list-style: none;">
                            <?php foreach ($plan['features'] as $feature): ?>
                                <li style="margin-bottom: 12px; padding-left: 30px; position: relative;">
                                    <i class="fas fa-check" style="color: #3CB371; position: absolute; left: 0; top: 4px;"></i>
                                    <?php echo $feature; ?>
                                </li>
                            <?php endforeach; ?>
                            
                            <?php foreach ($plan['not_included'] as $feature): ?>
                                <li style="margin-bottom: 12px; padding-left: 30px; position: relative; color: #999;">
                                    <i class="fas fa-times" style="color: #DC3545; position: absolute; left: 0; top: 4px;"></i>
                                    <?php echo $feature; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <form action="" method="POST" style="text-align: center;">
                            <input type="hidden" name="new_plan" value="<?php echo $plan_id; ?>">
                            <button type="submit" name="upgrade_membership" class="btn btn-primary" style="width: 100%; padding: 12px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                                Upgrade Now
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Payment Information (for a real app) -->
        <div style="margin-top: 40px; background-color: #f8f9fa; border-radius: 10px; padding: 20px; text-align: center;">
            <h3 style="margin: 0 0 15px; font-size: 1.2rem; color: var(--color-dark);">Payment Information</h3>
            <p style="margin: 0; color: #666;">
                All memberships are billed monthly. You can upgrade at any time, and the new rate will be prorated for the remainder of the current billing cycle.
                <br>For any questions about billing or membership features, please contact our support team.
            </p>
        </div>
    </div>
</section>

<!-- Membership Benefits Section -->
<section class="section" style="background-color: #f8f9fa; padding: 60px 0;">
    <div class="container">
        <h2 style="margin: 0 0 40px; color: var(--color-dark); font-size: 1.8rem; text-align: center;">Membership Benefits Comparison</h2>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                <thead>
                    <tr>
                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee;">Feature</th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; background-color: #f1e6d6;">
                            <div style="font-size: 1.2rem; color: #CD7F32;">Bronze</div>
                            <div style="font-size: 0.9rem; color: #666;">$49/month</div>
                        </th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; background-color: #e8e8e8;">
                            <div style="font-size: 1.2rem; color: #808080;">Silver</div>
                            <div style="font-size: 0.9rem; color: #666;">$99/month</div>
                        </th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #eee; background-color: #fff9dd;">
                            <div style="font-size: 1.2rem; color: #DAA520;">Gold</div>
                            <div style="font-size: 0.9rem; color: #666;">$149/month</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">Profile Listing</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Basic</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Enhanced</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Premium</td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">Monthly Bookings</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">5</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">20</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Unlimited</td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">Support</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Email</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Priority Email</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">24/7 Priority</td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">Featured Profile</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;"><i class="fas fa-times" style="color: #DC3545;"></i></td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;"><i class="fas fa-check" style="color: #3CB371;"></i></td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;"><i class="fas fa-check" style="color: #3CB371;"></i> Top Listing</td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">Client Messaging</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;"><i class="fas fa-times" style="color: #DC3545;"></i></td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;"><i class="fas fa-times" style="color: #DC3545;"></i></td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;"><i class="fas fa-check" style="color: #3CB371;"></i></td>
                    </tr>
                    <tr>
                        <td style="padding: 15px; border-bottom: 1px solid #eee;">Analytics & Reports</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Basic</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Detailed</td>
                        <td style="padding: 15px; text-align: center; border-bottom: 1px solid #eee;">Advanced</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?> 