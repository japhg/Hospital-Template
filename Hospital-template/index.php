<?php
    require_once "connection.php";
    require_once "function.php";
    
    ob_start();
    session_start();
    $errors = array();


if (isset($_POST['login'])) {
    $ip = getUserIpAdd();
    $time = time() - 30;
    $check_attempt = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as total_attempt FROM attempt_table WHERE time_count>$time AND ip_address = '$ip'"));
    $total_count = $check_attempt['total_attempt'];
    if ($total_count == 3) {
        $errors['student_number'] = "Users are now locked. Please wait for 30 seconds! ";
    } else {
        $username = clean(mysqli_real_escape_string($con, $_POST["user"]));
        $password = clean(mysqli_real_escape_string($con, $_POST['password']));


        $query = "SELECT * FROM user_table WHERE username = '$username'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['valid'] = true;
            $_SESSION['timeout'] = time();
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['password'] = $row['password'];
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                if ($row['user_type'] == "SUPER ADMIN") {
                    mysqli_query($con, "DELETE FROM attempt_table WHERE ip_address = '$ip'");
                    header("location: dashboard.php");
                } else if ($row['user_type'] == "HR ADMIN") {
                    mysqli_query($con, "DELETE FROM attempt_table WHERE ip_address = '$ip'");
                    header("location: subsystem.php");
                    exit(0);
                }
            } else {
                $total_count++;
            $time_remain = 5 - $total_count;
            $time = time();
            if ($time_remain == 0) {
                $errors['student_number'] = "Users are now locked. Please wait for 30 seconds! ";
            } else {
                $errors['username'] = "Username or Password is incorrect." .$time_remain. " attempts  remaining. ";
            }
            mysqli_query($con, "INSERT INTO attempt_table(ip_address,time_count) VALUES('$ip','$time')");
                
            }
        } 
    }
}
function getUserIpAdd(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
    {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip; 
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&family=Poppins&family=Quicksand&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@500&family=Inter:wght@300;400;600;800&family=Poppins&family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">

    <script src="js/sweetalert.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <link rel="stylesheet" href="assets/css/login.css">
    <title>Login</title>
</head>

<body>
    <!--@kirzin-Codepen.io-->
    <div class="container">
    <?php
    if(count($errors) > 0)
    { ?>
         <?php
    foreach($errors as $showerror)
    {
    ?>
    <script> swal({
            title: "Engk!",
            text: "<?php echo $showerror?>",
            icon: "error",
            button: "Retry"
          }) </script>
          <?php
    } 
    
?>
<?php
    }
  ?>
        <div class="card">
            <div class="content">
                <img src="https://imgs.search.brave.com/5EIU675lSA0b-7hP0m8XtE-9rEZmWPa3MXyUVlv02KQ/rs:fit:512:512:1/g:ce/aHR0cHM6Ly9pLnBp/bmltZy5jb20vb3Jp/Z2luYWxzLzU3Lzhh/LzY0LzU3OGE2NDA4/NWU1NDI1YmM3YzY1/M2QwOTAxM2ExNTIw/LnBuZw" alt="" style="width: 60px;">
                <h2>Alegario Cure Hospital</h2>
                <h3>Login</h3>
           
                <div class="form">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
                        <label for="Email">Username</label>
                        <br>
                        <input type="text" name="user" id="user" required>
                        <br>
                        <label for="Password">Password</label>
                        <br>
                        <input type="password" name="password" id="pass" required>
                        <br>
                        <br>
                        <button type="submit" name="login">Login</button>
                        <br>

                    </form>
                    <a href="">Forgot Password</a>
                </div>
                </div>
               
        </div>
        
       
      
</div>
</body>
</html>