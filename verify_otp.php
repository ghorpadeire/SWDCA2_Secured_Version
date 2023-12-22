
<?php
session_start();
include './includes/conn.php'; // Include your database connection file

// Function to validate OTP
function validate_otp($user_otp, $email, $conn) {
    // Query to retrieve the OTP for the given email from the user table
    $sql = "SELECT otp FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_otp = $row['otp'];

        // Check if the user-provided OTP matches the one stored in the database
        if ($user_otp === $stored_otp) {
            return true; // OTP is valid
        }
    }
    
    return false; // OTP is invalid
}

$verification_status = '';

if (isset($_POST['submit'])) {
    $email = $_SESSION['email']; // Retrieve email from session or modify as per your application logic
    $user_otp = $_POST['otp'];

    if (validate_otp($user_otp, $email, $conn)) {
        $verification_status = 'OTP verified successfully.';
        // Perform further actions upon successful verification
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
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <input type="submit" name="submit" value="Verify OTP">
    </form>

    <?php if ($verification_status): ?>
        <p><?php
         echo $verification_status; 
         echo "<script>window.open('login.php','_self')</script>";
        ?></p>
    <?php endif; ?>
</body>
</html>
