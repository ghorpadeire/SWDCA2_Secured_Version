<?php
  // This page is shown when a user tries to access a resource they are not authorized to.
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Unauthorized Access</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <h1>Unauthorized Access</h1>
  <p>You do not have permission to access this page.</p>
  <p><a href="index.php">Click here to Return to Home</a></p>
</body>
</html>