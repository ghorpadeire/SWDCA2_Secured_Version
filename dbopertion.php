<?php

//session start
session_start();
include_once 'includes/conn.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php';
use Respect\Validation\Validator;


// Check if the CSRF token is set in the session, if not, create one
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
function sendmail_verify($email, $otp) {
  $mail = new PHPMailer(true);
  try {
      // Server settings
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com'; // Your SMTP server
      $mail->SMTPAuth = true;
      $mail->Username   = 'pragcodeverify@gmail.com'; // Your SMTP username
      $mail->Password   = 'srrxkrhcxyizkwdq';  // Your SMTP password
      // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Recipient
      $mail->addAddress($email);
      $mail->isHTML(true);
      $mail->Subject = 'Email Verification OTP';
      // Email body...
      $mail_template = "<h2>You have registered with the Help Needy People website</h2><br/>
                  <h5>Verify by entering the OTP provided below</h5><br/><br/>
                  <h3>YOUR OTP is: $otp</h3>
                  <a href='http://localhost/ca2swd/verify_otp.php'>CLICK HERE TO VERIFY OTP</a>";
            // 
      $mail->Body = $mail_template;
      $mail->send();
  } catch (Exception $e) {
      echo 'Mailer Error: ' . $mail->ErrorInfo;
  }
}
if(isset($_POST['rubtn']))
{ 
    // Sanitize role, first name, last name, and address
    $role = trim(htmlspecialchars(strip_tags($_POST['role'])));
    $fname = trim(htmlspecialchars(strip_tags($_POST['fname'])));
    $lname = trim(htmlspecialchars(strip_tags($_POST['lname'])));
    $address = trim(htmlspecialchars(strip_tags($_POST['address'])));
    // Sanitize and validate mobile number
    $mobile = filter_var(trim(htmlspecialchars($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT)));
      if (false === filter_var($mobile, FILTER_VALIDATE_INT)) {
        echo "<script>alert('Invalid mobile number.')</script>";
        echo "<script>window.open('register.php','_self')</script>";
      }
    // Sanitize and validate email
    $email = filter_var(trim(htmlspecialchars($_POST['email'], FILTER_SANITIZE_EMAIL)));
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Invalid email address.</script>";
        echo "<script>window.open('register.php','_self')</script>"; // Stop further execution if the email is invalid
      }


    //Sanitize and validate file
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) 
    {
      // Check if the file type is allowed
      $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // Example types
      $fileType = mime_content_type($_FILES['photo']['tmp_name']);
  
      if (!in_array($fileType, $allowedTypes)) {
          echo "<script>alert('Invalid file type.')</script>";
          echo "<script>window.open('register.php','_self')</script>";
      }
  
      // Check file size
      $maxSize = 5 * 1024 * 1024; // 5 MB
      if ($_FILES['photo']['size'] > $maxSize) {
          echo "<script>alert('File is too large.')</script>";
          echo "<script>window.open('register.php','_self')</script>";
      }
  
      // Sanitize and create a unique file name
      $originalName = basename($_FILES['photo']['name']);
      $sanitizedName = preg_replace("/[^a-zA-Z0-9.]/", "_", $originalName);
      $uniqueName = uniqid() . '_' . $sanitizedName;
  
      // Move the file to a safe location
      $uploadDir = '../upload/'; // Ensure this directory exists and is writable
      $uploadPath = $uploadDir . $uniqueName;
  
      if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
          echo "<script>alert('File uploaded successfully.')</script>";
        } else {
            echo "<script>alert('File upload failed.')</script>";
            echo "<script>window.open('register.php','_self')</script>";
        }
    }
    // else {
    //   echo "<script>alert('No file uploaded or upload error.')</script>";
    //   echo "<script>window.open('register.php','_self')</script>";
    // }

  
  
  // Passwords - consider hashing before storing in database
    $password=trim(htmlspecialchars($_POST['epass']));
    $rpassword=trim(htmlspecialchars($_POST['cpass']));
    $otp = rand(100000, 999999);        // Generate a 6-digit OTP    
  
    if($password != $rpassword){
      echo "<script>alert('Enter Password And Reenter Password Are not same')</script>";
      echo "<script>window.open('register.php','_self')</script>";
    }
    if ($password < 8 ){
    echo "<script>alert('Password is weak.')</script>";
    echo "<script>window.open('register.php','_self')</script>";
    }
  
  else
  {
      // $query="select * from user where email='$email'";
      $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      // TODO: Converted this query to a p statement for security
      if(mysqli_num_rows($result)==1)
      {
          echo "<script>alert('the emailid is already registrated, if you already register then login')</script>";
          echo "<script>window.open('index.php','_self')</script>";
      
      }

      // Hash the password
      else
      {
        // After generating the OTP and before inserting user data into the user table
        $stmt = $conn->prepare("INSERT INTO verifyotp (otp, is_email_verified, email) VALUES (?, 0, ?)");
        $stmt->bind_param("is", $otp, $email);
        $stmt->execute();
         // Check for errors
         if ($stmt->error) {
          // Handle error
          echo "Error: " . $stmt->error;
      }
        $stmt->close();
        // $stmt = $conn->prepare("UPDATE verifyotp SET is_email_verified = 1 WHERE email = ?");
        // $stmt->bind_param("s", $email);
        // $stmt->execute();
        sendmail_verify("$email","$otp");
        echo "<script>alert('verify email')</script>";
        
        echo "<script>window. close();</script>"; 
        
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO user (role, fname, lname, photo, address, mobile, email, pass, otp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssbsissi", $role, $fname, $lname, $upload, $address, $mobile, $email, $hashed_pwd, $otp); // Bind parameters to the statement
        $stmt->execute();
        $stmt->close();
        echo "<script>window.open('index.php','_self')</script>";
      
      }
  }
}
if(isset($_POST['luser']))
{
  $role=trim(htmlspecialchars($_POST['role']));
  $username=trim(htmlspecialchars($_POST['email']));
  $password=trim(htmlspecialchars($_POST['pass']));

  // Prepare the statement with placeholders
  $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND role = ?");  
  $stmt->bind_param("ss", $username, $role);// Bind parameters to the placeholders. AND  "ss" indicates that both parameters are strings
  $stmt->execute();// Execute the statement  
  $result = $stmt->get_result();  // Fetch the results  
  $stmt->close();// Close the statement
  // TODO: Converted this query to a p statement for security
        
      foreach($result as $row)
      {
          $_SESSION['id']=$row['id'];
          $_SESSION['fname']=$row['fname'];
          $_SESSION['lname']=$row['lname'];
          $_SESSION['photo']=$row['photo'];
          $_SESSION['address']=$row['address'];
          $_SESSION['mobile']=$row['mobile'];
          $_SESSION['email']=$row['email'];
          $_SESSION['password']=$row['pass'];
          $_SESSION['role']=$row['role'];
      }
      // echo ;cho ;
      if (password_verify($password, $row['pass'])) {
        echo $username;
        echo $_SESSION['email'];
      if(strtolower(substr('Recipient', 0, 1)) ==strtolower(substr($role, 0, 1)) && $_SESSION['email']==$username )
        {
          $_SESSION['file']='reciept/reciept.php';
          echo "<script>window.open('reciept/reciept.php','_self')</script>";
        }
      if('Donor'==$role && $_SESSION['email']==$username )
      {
          $_SESSION['file']='donor/donor.php';
          echo "<script>window.open('donor/donor.php','_self')</script>";
      }
      if('Admin'==$_SESSION['role'] && $_SESSION['email']==$username )
      {
          $_SESSION['file']='admin/admin.php';
          echo "<script>window.open('admin/admin.php','_self')</script>";
      }
        else
        {
          echo "<script>alert('you enter wrong pass credential.')</script>";
          // echo "<script>window.open('login.php','_self')</script>";
        }
      }else {
          // Passwords do not match
          echo "<script>alert('you enter wrong pass 1credential.')</script>";
          echo "<script>window.open('login.php','_self')</script>";
      }
}
if(isset($_POST['rpass']))
{
    $role=trim(htmlspecialchars($_POST['role']));
    $email=trim(htmlspecialchars($_POST['email']));
    $password=trim(htmlspecialchars($_POST['epass']));
    $rpassword=trim(htmlspecialchars($_POST['cpass']));
 
    //  $query="select * from user where email='$email'";
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
  // TODO: Converted this query to a p statement for security
     if(mysqli_num_rows($result)==1)
      {
         if($password!= $rpassword)
           {
              echo "<script>alert('Enter Password And Reenter Password Are not same')</script>";
              echo "<script>window.open('login.php','_self')</script>";
            }
            else
            {
                // $query="UPDATE user SET pass = '$password' WHERE email ='$email'";

                // Prepare the statement with placeholders
                $stmt = $conn->prepare("UPDATE user SET pass = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                $stmt->execute();// Execute the statement
                $result = $stmt->get_result();
                $stmt->close();// Close the statement
                // TODO: Converted this query to a p statement for security
                if($result)
                  {
                      echo "<script>alert('pasword change successfully.')</script>";
                      echo "<script>window.open('login.php','_self')</script>";
                  }

            }
       }
       else
       {
         echo "<script>alert('the emailid is not registrate enter right emaid ya create new account')</script>";
         echo "<script>window.open('register.php','_self')</script>";
       }
}
if(isset($_POST['upbtn']))
{
    $id=trim(htmlspecialchars($_POST['id']));
    $role=trim(htmlspecialchars($_POST['role']));
    $fname=trim(htmlspecialchars($_POST['fname']));
    $lname=trim(htmlspecialchars($_POST['lname']));
    $address=trim(htmlspecialchars($_POST['address']));
    $mobile=trim(htmlspecialchars($_POST['mobile']));
    $email=trim(htmlspecialchars($_POST['email']));
    $photo=$_FILES['photo']['name'];
    if($address=="")
    {
      $address= $_SESSION['address'];
    }
    if($photo=="")
    {
      $photo= $_SESSION['photo'];
      $upload=$photo;
      move_uploaded_file($_FILES['photo']['tmp_name'], $upload);
    }
    else{
      $upload="../upload/".$photo;
      move_uploaded_file($_FILES['photo']['tmp_name'], $upload);
    }
		
    $query = "UPDATE `user` SET `fname` = ?, `lname` = ?, `photo` = ?, `address` = ?, `mobile` = ?, `email` = ? WHERE `id` = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $fname, $lname, $upload, $address, $mobile, $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();// Close the prepare statement
      if($result) {
        // echo "";
        echo "<script>alert('User updated successfully.')</script>";
        if($role=='Admin')
        {
          echo "<script>window.open('admin/aprofile.php','_self')</script>";
        }
        if($role =='Donor')
        {
          echo "<script>window.open('donor/dprofile.php','_self')</script>";
        }
        if($role =='Recipient')
        {
          echo "<script>window.open('reciept/rprofile.php','_self')</script>";
        }
    } else {
        echo "<script>alert('User update failed.')</script>";
    }
    $stmt->close();// Close the statement
}
if(isset($_POST['antbtn']))
{
  $type = trim(htmlspecialchars($_POST['type']));
  $query = "INSERT INTO needtype(type) VALUES (?)";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $type);
  if($stmt->execute())
  {
    echo "<script>alert('Need type Added Sucessfully.')</script>";
    echo "<script>window.open('admin/aaddneed.php','_self')</script>";
  }
  $result = $stmt->get_result();
  $stmt->close();// Close the prepare statement
          //  if($result)
           
}
if(isset($_POST['anbtn']))
{
  $rid=trim(htmlspecialchars($_POST['rid']));
  $ntype=trim(htmlspecialchars($_POST['ntype']));
  $nname=trim(htmlspecialchars($_POST['nname']));
  $ndetail=trim(htmlspecialchars($_POST['ndetail']));
  $ramount=trim(htmlspecialchars($_POST['ramount']));
  $ldate=trim(htmlspecialchars($_POST['ldate']));
  $photo=$_FILES['nphoto']['name'];
  $upload="../upload/".$photo;
      $path="upload/".$photo;
  move_uploaded_file($_FILES['nphoto']['tmp_name'], $path);

    
    //  $query="insert into need(rid,ntype,nphoto,nname,ndetails,ramount,ldate,status)values('$rid','$ntype','$upload','$nname','$ndetail','$ramount','$ldate','1')";
     
    // Prepare the statement with placeholders
    $stmt = $conn->prepare("INSERT INTO need (rid, ntype, nphoto, nname, ndetails, ramount, ldate, status) VALUES (?, ?, ?, ?, ?, ?, ?, '1')");
    if ($stmt) {
        $stmt->bind_param("sssssis", $rid, $ntype, $upload, $nname, $ndetail, $ramount, $ldate);
        if ($stmt->execute()) {
            echo "<script>alert('Need Added Sucessfully.')</script>";
            echo "<script>window.open('reciept/addhelp.php','_self')</script>";
        } else {
            echo "<script>alert('Error adding need.')</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement.')</script>";
    }
    
}
if(isset($_POST['snrbtn']))
{
    $nid=trim(htmlspecialchars($_POST['nid']));
    $rid=trim(htmlspecialchars($_POST['rid']));
    $_SESSION['nid']=$nid;
    $_SESSION['rid']=$rid;
    echo "<script>window.open('admin/aaprovev.php','_self')</script>";
}
if(isset($_POST['drnbtn']))
{
    $nid=trim(htmlspecialchars($_POST['nid']));
    // $query="DELETE FROM need WHERE id=$nid";
    $stmt = $conn->prepare("DELETE FROM need WHERE id = ?"); // Prepare the delete statement
    $stmt->bind_param("i", $nid); // Bind the $nid variable to the placeholder
    $stmt->execute(); // Execute the delete operation
    $result = $stmt->get_result();
    $stmt->close();// Close the prepare statement
    // TODO: Convert this query to a p statement for security
        if($result)
        {
            echo "<script>alert('Delete need suceessfuly.')</script>";
            echo "<script>window.open('admin/aaprove.php','_self')</script>";
        }
}
if(isset($_POST['arnbtn']))
{
  $nid=trim(htmlspecialchars($_POST['nid']));
  // $query="UPDATE `need` SET `status` ='0' WHERE `id` ='$nid'";
  $stmt = $conn->prepare("UPDATE `need` SET `status` = '0' WHERE `id` = ?");
  $stmt->bind_param("i", $nid);// 'i' indicates the type of the parameter is integer
  if($stmt->execute()) // Execute the prepare statement
  {
    echo "<script>alert('Approve Recipient suceessfuly.')</script>";
    echo "<script>window.open('admin/aaprove.php','_self')</script>";
  }
  // $result = $stmt->get_result();
  $stmt->close();// Close the prepare statement
  // TODO: Convert this query to a p statement for security
  // if($result)
  
}
if(isset($_POST['smubtn']))
{
    $uid=trim(htmlspecialchars($_POST['uid']));
    $_SESSION['uid']=$uid;
    echo "<script>window.open('admin/amanageuserv.php','_self')</script>";
}
if(isset($_POST['dmubtn']))
{
    $uid=trim(htmlspecialchars($_POST['uid']));
    $query="DELETE FROM user WHERE id=$uid";
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?"); // Prepare the delete statement
    $stmt->bind_param("i", $uid); // Bind the $nid variable to the placeholder
    $stmt->execute(); // Execute the delete operation
    $result = $stmt->get_result();
    $stmt->close(); // Close the statement to free up resources
    // TODO: Convert this query to a p statement for security

        if($result)
        {
            echo "<script>alert('Delete user suceessfuly.')</script>";
            echo "<script>window.open('admin/amanageuser.php','_self')</script>";
        }
}
if(isset($_POST['shdbtn']))
{
    $nid=trim(htmlspecialchars($_POST['nid']));
    $rid=trim(htmlspecialchars($_POST['rid']));
    $_SESSION['nid']=$nid;
    $_SESSION['rid']=$rid;
    echo "<script>window.open('donor/dhelpv.php','_self')</script>";
}
if(isset($_POST['ah']))
{
  $nid=trim(htmlspecialchars($_POST['nid']));
  $rid=trim(htmlspecialchars($_POST['rid']));
  $did=trim(htmlspecialchars($_POST['did']));
  $hamount=trim(htmlspecialchars($_POST['hamount']));
  // $query="insert into help(nid,did,rid,hamount,status)values('$nid','$did','$rid','$hamount','1')";
  $stmt = $conn->prepare("INSERT INTO help (nid, did, rid, hamount, status) VALUES (?, ?, ?, ?, '1')"); // Prepare the insert statement
  $stmt->bind_param("iiii", $nid, $did, $rid, $hamount); // Bind the $nid, $did, $rid, $hamount variables to the placeholders
  $stmt->execute(); // Execute the insert operation
  $result = $stmt->get_result();
  $stmt->close(); // Close the statement to free up resources
// TODO: Convert this query to a p statement for security
           if($result)
           {
             echo "<script>alert('help Added Sucessfully.')</script>";
               echo "<script>window.open('donor/dhelp.php','_self')</script>";
           }
}
if(isset($_POST['srbtn']))
{
    $uid=trim(htmlspecialchars($_POST['did']));
    $_SESSION['uid']=$uid;
    echo "<script>window.open('reciept/snotifictionv.php','_self')</script>";
}
if(isset($_POST['ddn']))
{
  $hid=trim(htmlspecialchars($_POST['hid']));
  // $query="UPDATE `help` SET `status` ='2' WHERE `id` ='$hid'";
  $stmt = $conn->prepare("UPDATE `help` SET `status` = '2' WHERE `id` = ?"); // Prepare the update statement
  $stmt->bind_param("i", $hid); // Bind the $hid variable to the placeholder
  $stmt->execute(); // Execute the update operation
  $result = $stmt->get_result();
  $stmt->close(); // Close the statement to free up resources
// TODO: Convert this query to a p statement for security
  if($result)
  {
    echo "<script>alert('Delete notifiction suceessfuly.')</script>";
    echo "<script>window.open('donor/dnotifiction.php','_self')</script>";
  }
}

if(isset($_POST['srtd']))
{
  $hid=trim(htmlspecialchars($_POST['hid']));
  $message=trim(htmlspecialchars($_POST['message']));
  // $query="UPDATE `help` SET `status` ='0',`message` ='$message' WHERE `id` ='$hid'";
  $stmt = $conn->prepare("UPDATE `help` SET `status` = '0', `message` = ? WHERE `id` = ?"); // Prepare the update statement
  $stmt->bind_param("si", $message, $hid); // Bind the $message and $hid variables to the placeholders
  $stmt->execute(); // Execute the update operation
  $result = $stmt->get_result();
  $stmt->close(); // Close the statement to free up resources
// TODO: Converted this query to a p statement for security
  if($result)
  {
    echo "<script>alert('Send Replay suceessfuly.')</script>";
    echo "<script>window.open('reciept/snotifiction.php','_self')</script>";
  }
}
if(isset($_POST['sdnbtn']))
{
    $nid=trim(htmlspecialchars($_POST['nid']));
    $rid=trim(htmlspecialchars($_POST['rid']));
    $_SESSION['nid']=$nid;
    $_SESSION['rid']=$rid;
    echo "<script>window.open('donor/dnotifictionv.php','_self')</script>";
}
?>