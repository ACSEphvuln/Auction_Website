<?php // signup.php
include_once 'db_connect.php';
include_once 'common.php';
include_once 'accesscontrol.php';

if(isset($_SESSION['id_user']))
  header("Location: index.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

  if (!isset($_POST['email'], $_POST['fname'] ,$_POST['lname'], $_POST['psw'],$_POST['psw-repeat'],$_POST['CNP'],$_POST['Address'])){
    error('One or more required fields were left blank.\n Please fill them in and try again.');
  }

  $email = trim($_POST['email']);
  $lname = trim($_POST['lname']);
  $fname = trim($_POST['fname']);
  $password = trim($_POST['psw']);
  $CNP = trim($_POST['CNP']);
  $address = trim($_POST['Address']);

  foreach (array($email,$lname,$fname,$password,$CNP,$address ) as $v)
    if(empty($v)){
      error('One or more required fields were left blank.\n Please fill them in and try again.');
    }

  // Move to frontend!!!!!!!!!!!!!!!!!!!!!!!!!!
  if (!($_POST['psw']==$_POST['psw-repeat'])) {
  error('Passwords does not match.\n Please fill them in and try again.');
  }

  $email=filter_var($email, FILTER_VALIDATE_EMAIL);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    error("Please provide a valid email address.");

  $lname=filter_var($lname, FILTER_SANITIZE_STRING);
  if (!filter_var($lname, FILTER_SANITIZE_STRING)) 
    error("Please provide a valid name.");

  $fname=filter_var($fname, FILTER_SANITIZE_STRING);
  if (!filter_var($fname, FILTER_SANITIZE_STRING)) 
    error("Please provide a valid name.");

  if(strlen($CNP) != 13)
    error("Please provide a valid CNP.");

  if(!is_numeric($CNP))
     error("Please provide a valid CNP.");


  $address=filter_var($address, FILTER_SANITIZE_STRING);
  if (!filter_var($address, FILTER_SANITIZE_STRING)) 
    error("Please provide a valid address.");

  if(strlen($lname)>50 || strlen($fname) >50)
    error("Name too long!");

  if(strlen($email)>50)
    error("Email too long!");

  if(strlen($password)>50)
    error("Passowrd too long!");

  if(strlen($address)>80)
    error("Address too long!");


  $query= 'SELECT IDUtilizator FROM Utilizator WHERE Email = ?';
  if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        error('There is already a registered account with your email.\n Please check agian your email or reset your password.');
    }
    $stmt->close();
  }

  $query="INSERT INTO Utilizator (Email, Parola) VALUES(?,?)"; 

  if ($stmt = $conn->prepare($query)) {
    $pass_hash=hash('sha256', 'BD'.$password);
    $stmt->bind_param("ss",$email,$pass_hash);
    $stmt->execute();
    $stmt->close();
  } else error("Internal server error.");


  $sql = "SELECT IDUtilizator FROM Utilizator WHERE email = \"".$email."\"";
  $result = $conn->query($sql);

  if ($result->num_rows > 0){
  $row = $result->fetch_assoc();
  $id_user=$row["IDUtilizator"];
  } else error("Internal server error.");


  $query="INSERT INTO Persoana (IDUtilizator, Nume, Prenume, CNP, Adresa) VALUES(?,?,?,?,?)"; 

  if ($stmt = $conn->prepare($query)) {
    $pass_hash=hash('sha256', 'BD'.$password);
    $stmt->bind_param("sssss",$id_user,$lname,$fname,$CNP,$address);
    $stmt->execute();
    $stmt->close();
  } else error("Internal server error.");


?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <title> Registration Complete </title>
    <meta http-equiv="Content-Type"
    content="text/html; charset=iso-8859-1" />
    </head>
    <body>
    <center><p><strong>Registration successful for user <?php echo $name; ?>!</strong></p></center>
     <center>To log in,
    click <a href="login.php">here</a> to return to the login
    page, and enter your user and password.</p></center>
    </body>
    </html>
<?php

} else {?>

<!DOCTYPE html>
<!--
  ustora by freshdesignweb.com
  Twitter: https://twitter.com/freshdesignweb
  URL: https://www.freshdesignweb.com/ustora/
-->
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout Page - E-Comerce</title>
    
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,700,600' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,100' rel='stylesheet' type='text/css'>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <object id="menuBar"></object>
    
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Login</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">                
                <div class="col-md-12">
                    <div class="product-content-right">
                        <div class="woocommerce">


                            <form id="login-form-wrap" method="post">

                                <p>Please enter the information requested below:</p>


                                <p class="form-row">
                                    <label for="lname">Last name:<span class="required">*</span>
                                    </label>
                                    <input type="text" id="lname" name="lname" class="input-text">
                                </p>
                                <div class="clear"></div>



                                <p class="form-row">
                                    <label for="fname">First name:<span class="required">*</span>
                                    </label>
                                    <input type="text" id="fname" name="fname" class="input-text">
                                </p>
                                <div class="clear"></div>


                                <p class="form-row">
                                    <label for="email">Email <span class="required">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" class="input-text">
                                </p>
                                <p class="form-row">
                                    <label for="psw">Password <span class="required">*</span>
                                    </label>
                                    <input type="password" id="psw" name="psw" class="input-text">
                                </p>
                                <div class="clear"></div>

                                <p class="form-row">
                                    <label for="psw-repeat">Confirm Password <span class="required">*</span>
                                    </label>
                                    <input type="password" id="psw-repeat" name="psw-repeat" class="input-text">
                                </p>
                                <div class="clear"></div>


                                <p class="form-row">
                                    <label for="CNP">CNP <span class="required">*</span>
                                    </label>
                                    <input type="text" id="CNP" name="CNP" class="input-text">
                                </p>
                                <div class="clear"></div>

                                <p class="form-row">
                                    <label for="Address">Address <span class="required">*</span>
                                    </label>
                                    <input type="text" id="Address" name="Address" class="input-text">
                                </p>
                                <div class="clear"></div>



                                <p class="form-row">
                                    <input type="submit" value="Login" name="login" class="button">
                                </p>
                                <p class="lost_password">
                                    <a href="#">Lost your password?</a>
                                </p>
                            </form>


                        </div>                       
                    </div>                    
                </div>
            </div>
        </div>
    </div>


    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="copyright">
                        <p>Project's Opens Source Template: &copy; 2015 E-Commerce. todos los derechso reservados. <a href="https://jairandresdiazp.blogspot.com.co/" target="_blank">https://jairandresdiazp.blogspot.com.co/</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End footer bottom area -->
   
   
    <!-- Latest jQuery form server -->
    <script src="https://code.jquery.com/jquery.min.js"></script>
    
    <!-- Bootstrap JS form CDN -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    
    <!-- jQuery sticky menu -->
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    
    <!-- jQuery easing -->
    <script src="js/jquery.easing.1.3.min.js"></script>
    
    <!-- Main Script -->
    <script src="js/main.js"></script>
    <script type="text/javascript" src="js/menu.js"></script>

  </body>
</html>

<?php }?>