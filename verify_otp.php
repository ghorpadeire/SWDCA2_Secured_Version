
<?php
session_start();
include './includes/conn.php'; // Include your database connection file

// Function to validate OTP
function validate_otp($user_otp, $email, $conn) {
    // Query to retrieve the OTP for the given email from the user table
    $sql = "SELECT otp FROM verifyotp WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows) {
        $row = $result->fetch_assoc();
        $stored_otp = $row['otp'];
                  
        
        // Check if the user-provided OTP matches the one stored in the database
        if ($user_otp === $stored_otp) {
            
            return true; // OTP is valid  //code run till now
        }
    }
    
    return false; // OTP is invalid 
}

$verification_status = '';

if (isset($_POST['submit'])) 
{
    $email = $_POST['email']; // Retrieve email from session or modify as per your application logic
    $user_otp = $_POST['otp'];
    
    if (validate_otp($user_otp, $email, $conn)) 
    {
        // Update is_email_verified in verifyotp table
        $stmt = $conn->prepare("UPDATE verifyotp SET is_email_verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->error) {
            // Handle error
            echo "Error: " . $stmt->error;
        }
        $verification_status = 'OTP verified';

    } else {
        $verification_status = 'Invalid OTP. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
</head>
<body>
    <h1>OTP Verification</h1>
    <form method="POST" action="verify_otp.php">
    <label for="email">Enter Email:</label>
        <input type="email" id="otp" name="email" required><br>
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required><br>
        <input type="submit" name="submit" value="Verify OTP"><br><br>

        
        
    </form>

    <?php if ($verification_status): ?>
        <p><?php
         echo  "<script>alert($verification_status)</script>"; ; 
         echo "<script>window.open('login.php','_self')</script>";
        ?></p>
    <?php endif; ?>
</body>
</html>
