<?php
include_once 'common.php';
include_once 'db_connect.php';
include_once 'accesscontrol.php';

if(isset($_SESSION['idU']))
  header("Location: index.php");


if($_SERVER["REQUEST_METHOD"] == "POST"):
    if (!isset($_POST['email'],$_POST['psw']))
      error('One or more required fields were left blank.\n Please fill them in and try again.');

    $email = trim($_POST['email']);
    $password = trim($_POST['psw']);

    if(empty($email) || empty($password) )
      error('One or more required fields were left blank.\n Please fill them in and try again.');

    if(strlen($email)>50 || strlen($password)>50)
      error("Wrong email/passowrd combination.");

    $query= 'SELECT Email,Parola FROM Utilizator WHERE email = ? AND parola = ?';
    if ($stmt = $conn->prepare($query)) {
      $pass_hash=hash('sha256', 'BD'.$password);
        $stmt->bind_param("ss", $email,$pass_hash);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
           error('Wrong email/passowrd combination.');
      }
      $stmt->close();
    } else error('Unexpecter error.');

    // login sucessful
    session_regenerate_id(TRUE);
    $_SESSION['email']=$email;

    $sql = "SELECT IDUtilizator FROM Utilizator WHERE email = \"".$email."\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $_SESSION["idU"]=$row["IDUtilizator"];
    } else error("Internal server error.");


    header("Location: index.php");
else:



?>

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

                                <p class="form-row form-row-first">
                                    <label for="email">Email <span class="required">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" class="input-text">
                                </p>
                                <p class="form-row form-row-last">
                                    <label for="psw">Password <span class="required">*</span>
                                    </label>
                                    <input type="password" id="psw" name="psw" class="input-text">
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

<?php endif ?>