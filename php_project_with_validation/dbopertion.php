<?php
require './vendor/autoload.php';
use Respect\Validation\Validator;

   session_start();
include_once 'includes/conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php';


function sendmail_verify($email,$otp)
{
  $mail = new PHPMailer(true);


$mail->SMTPDebug = 2;

  // Server settings
  $mail->isSMTP();                                            // Send using SMTP
  $mail->Host       = 'smtp.gmx.com';                       // Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
  $mail->Username   = 'otpverify@gmx.com';             // SMTP username
  $mail->Password   = '7770028552';                          // SMTP password
  // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
  $mail->Port       = 587;                                    // TCP port to connect to 587
  
  $mail->SMTPSecure = false;
  $mail->SMTPAutoTLS = false;


  // Prepare and send the email
  $mail->addAddress($email);     // Add a recipient
  $mail->isHTML(true);                   // Set email format to HTML
  $mail->Subject = 'Email Verification OTP';
  $mail_template = "<h2> You have registered with Help needy people website <h2> <br/>
                    <h5> Verify by clicking below <h5>
                    <br/><br/>
                    <a href ='http://localhost/ca2swd/verify_otp.php?otp=$otp'> CLICK HERE </a>  
  ";
  $mail->Body = $mail_template;
  $mail->send();
  echo 'message not send ';
}

if(isset($_POST['rubtn'])){
    $role=$_POST['role'];
    $fname=$_POST['fname'];
    $lname=$_POST['lname'];
    $address=$_POST['address'];
    $mobile=$_POST['mobile'];
    $email=$_POST['email'];
    $password=$_POST['epass'];
    $rpassword=$_POST['cpass'];
    $otp = rand(100000, 999999); // Generate a 6-digit OTP

    $photo=$_FILES['photo']['name'];
		$upload="../upload/".$photo;
        $path="upload/".$photo;
    move_uploaded_file($_FILES['photo']['tmp_name'], $path);

   

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
      $query="select * from user where email='$email'";
      $result=mysqli_query($conn,$query);
      if(mysqli_num_rows($result)==1)
      {
          echo "<script>alert('the emailid is already registrate do you already register then login')</script>";
          echo "<script>window.open('index.php','_self')</script>";
      
      }
      else
      {
          
          // Hash the password
          $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
          $query="insert into user(role,fname,lname,photo,address,mobile,email,pass,otp)values('$role','$fname','$lname','$upload','$address','$mobile','$email','$hashed_pwd','$otp')";
          $result=mysqli_query($conn,$query);
         if($result)
         {
          // sendmail_verify("$email","$otp");
           echo "<script>alert('user Register Sucessfully.')</script>";
             echo "<script>window.open('login.php','_self')</script>";
         }
        
      }
  }
}
if(isset($_POST['luser'])){
    $role=$_POST['role'];
	  $username=$_POST['email'];
	  $password=$_POST['pass'];
      
        $query="select * from user where email='$username' and role='$role'";
        $result=mysqli_query($conn,$query);
        
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
        if (password_verify($password, $row['pass'])) {
        if('Recipient'==$role && $_SESSION['email']==$username )
          {
            $_SESSION['file']='reciept/reciept.php';
            echo "<script>window.open('reciept/reciept.php','_self')</script>";
          }
        if('Donor'==$_SESSION['role'] && $_SESSION['email']==$username )
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
            echo "<script>window.open('login.php','_self')</script>";
          }
        }else {
            // Passwords do not match
            echo "<script>alert('you enter wrong pass credential.')</script>";
            echo "<script>window.open('login.php','_self')</script>";
            // echo "<script> console.log('Password: " . $password . "')</script>";
            // echo "<script> console.log('Hashed Password: " . $row['pass']  . "')</script>";
            // echo "hash not match";
        }
}


if(isset($_POST['rpass']))
{
    $role=$_POST['role'];
    $email=$_POST['email'];
    $password=$_POST['epass'];
    $rpassword=$_POST['cpass'];
 
     $query="select * from user where email='$email'";
     $result=mysqli_query($conn,$query);
     if(mysqli_num_rows($result)==1)
      {
         if($password!= $rpassword)
           {
              echo "<script>alert('Enter Password And Reenter Password Are not same')</script>";
              echo "<script>window.open('login.php','_self')</script>";
            }
            else
            {
                $query="UPDATE user SET pass = '$password' WHERE email ='$email'";
                $result=mysqli_query($conn,$query);
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
    $id=$_POST['id'];
    $role=$_POST['role'];
    $fname=$_POST['fname'];
    $lname=$_POST['lname'];
    $address=$_POST['address'];
    $mobile=$_POST['mobile'];
    $email=$_POST['email'];
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
		$query="UPDATE `user` SET `fname` = '$fname',`lname` = '$lname',`photo` = '$upload',`address` = '$address',`mobile` = '$mobile',`email` = '$email' WHERE `id` ='$id'";
      $result=mysqli_query($conn,$query);
      if($result)
      {
        echo "<script>alert('update profile suceessfuly.')</script>";
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
      }
}

if(isset($_POST['antbtn']))
{
  $type=$_POST['type'];
  $query="insert into needtype(type)values('$type')";
            $result=mysqli_query($conn,$query);
           if($result)
           {
       
             echo "<script>alert('Need type Added Sucessfully.')</script>";
               echo "<script>window.open('admin/aaddneed.php','_self')</script>";
           }
}

if(isset($_POST['anbtn']))
{
  $rid=$_POST['rid'];
  $ntype=$_POST['ntype'];
  $nname=$_POST['nname'];
  $ndetail=$_POST['ndetail'];
  $ramount=$_POST['ramount'];
  $ldate=$_POST['ldate'];
  $photo=$_FILES['nphoto']['name'];
  $upload="../upload/".$photo;
      $path="upload/".$photo;
  move_uploaded_file($_FILES['nphoto']['tmp_name'], $path);

    // // Assuming a column 'otp' and 'is_verified' exists in the users table
    // $query = "UPDATE users SET otp='$otp', is_verified=0 WHERE email='$email'";
    // $result = $conn->query($query);
    

  $query="insert into need(rid,ntype,nphoto,nname,ndetails,ramount,ldate,status)values('$rid','$ntype','$upload','$nname','$ndetail','$ramount','$ldate','1')";
            $result=mysqli_query($conn,$query);
           if($result)
           {
             echo "<script>alert('Need Added Sucessfully.')</script>";
               echo "<script>window.open('reciept/addhelp.php','_self')</script>";
           }
}
if(isset($_POST['snrbtn']))
{
    $nid=$_POST['nid'];
    $rid=$_POST['rid'];
    $_SESSION['nid']=$nid;
    $_SESSION['rid']=$rid;
    echo "<script>window.open('admin/aaprovev.php','_self')</script>";
}
if(isset($_POST['drnbtn']))
{
    $nid=$_POST['nid'];
    $query="DELETE FROM need WHERE id=$nid";
        $result=mysqli_query($conn,$query);
        if($result)
        {
            echo "<script>alert('Delete need suceessfuly.')</script>";
            echo "<script>window.open('admin/aaprove.php','_self')</script>";
        }
}
if(isset($_POST['arnbtn']))
{
  $nid=$_POST['nid'];
  $query="UPDATE `need` SET `status` ='0' WHERE `id` ='$nid'";
  $result=mysqli_query($conn,$query);
  if($result)
  {
    echo "<script>alert('Approve Recipient suceessfuly.')</script>";
    echo "<script>window.open('admin/aaprove.php','_self')</script>";
  }
}
if(isset($_POST['smubtn']))
{
    $uid=$_POST['uid'];
    $_SESSION['uid']=$uid;
    echo "<script>window.open('admin/amanageuserv.php','_self')</script>";
}

if(isset($_POST['dmubtn']))
{
    $uid=$_POST['uid'];
    $query="DELETE FROM user WHERE id=$uid";
        $result=mysqli_query($conn,$query);
        if($result)
        {
            echo "<script>alert('Delete user suceessfuly.')</script>";
            echo "<script>window.open('admin/amanageuser.php','_self')</script>";
        }
}

if(isset($_POST['shdbtn']))
{
    $nid=$_POST['nid'];
    $rid=$_POST['rid'];
    $_SESSION['nid']=$nid;
    $_SESSION['rid']=$rid;
    echo "<script>window.open('donor/dhelpv.php','_self')</script>";
}
if(isset($_POST['ah']))
{
  $nid=$_POST['nid'];
  $rid=$_POST['rid'];
  $did=$_POST['did'];
  $hamount=$_POST['hamount'];
  $query="insert into help(nid,did,rid,hamount,status)values('$nid','$did','$rid','$hamount','1')";
            $result=mysqli_query($conn,$query);
           if($result)
           {
             echo "<script>alert('help Added Sucessfully.')</script>";
               echo "<script>window.open('donor/dhelp.php','_self')</script>";
           }
}
if(isset($_POST['srbtn']))
{
    $uid=$_POST['did'];
    $_SESSION['uid']=$uid;
    echo "<script>window.open('reciept/snotifictionv.php','_self')</script>";
}
if(isset($_POST['ddn']))
{
  $hid=$_POST['hid'];
  $query="UPDATE `help` SET `status` ='2' WHERE `id` ='$hid'";
  $result=mysqli_query($conn,$query);
  if($result)
  {
    echo "<script>alert('Delete notifiction suceessfuly.')</script>";
    echo "<script>window.open('donor/dnotifiction.php','_self')</script>";
  }
}

if(isset($_POST['srtd']))
{
  $hid=$_POST['hid'];
  $message=$_POST['message'];
  $query="UPDATE `help` SET `status` ='0',`message` ='$message' WHERE `id` ='$hid'";
  $result=mysqli_query($conn,$query);
  if($result)
  {
    echo "<script>alert('Send Replay suceessfuly.')</script>";
    echo "<script>window.open('reciept/snotifiction.php','_self')</script>";
  }
}
if(isset($_POST['sdnbtn']))
{
    $nid=$_POST['nid'];
    $rid=$_POST['rid'];
    $_SESSION['nid']=$nid;
    $_SESSION['rid']=$rid;
    echo "<script>window.open('donor/dnotifictionv.php','_self')</script>";
}
?>