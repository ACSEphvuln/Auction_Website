<?php
include_once 'db_connect.php';
include_once 'accesscontrol.php';
include_once 'common.php';


if(!isset($_SESSION['idU'])){
    header("Location: index.php");
}

global $conn;


if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (!isset($_POST['owner'],$_POST['details'],$_POST['exp'],$_POST['ccv']))
      error('One or more required fields were left blank.\n Please fill them in and try again.');
    if(strlen($owner)>50 || strlen($details)>50 || strlen($ccv)>50)
      error("Too long fields!");
    if (!date_parse_from_format("Y-m-d", $date))
      error("Bad date!");

    $owner = trim($_POST['owner']);
    $details = trim($_POST['details']);
    $ccv = trim($_POST['ccv']);
    $date = trim($_POST['exp']);

    foreach (array($owner, $details, $ccv) as $v)
    if(empty($v)){
      error('One or more required fields were left blank.\n Please fill them in and try again.');
    }


    $query="INSERT INTO Card (Propietar, Exp, Detalii,CCV) VALUES(?,?,?,?)"; 
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssss",$owner,$date,$details,$ccv);
        $stmt->execute();
        $stmt->close();
      } else error("Internal server error at Insert.");




    $sql = "SELECT IDCard FROM Card WHERE CCV = \"".$ccv."\" AND  Detalii=\"".$details."\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $idcard=$row["IDCard"];
    } else error("Internal server error at CardIdentification.");

    $query="UPDATE Persoana SET IDCard = ? WHERE IDUtilizator=?"; 
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ss",$idcard,$_SESSION['idU']);
        $stmt->execute();
        $stmt->close();
      } else error("Internal server error at Insert.");



}







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
                        <h2>Account Info</h2>
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

<?php


$sql = "SELECT U.Email, P.Nume, P.Prenume, P.CNP, P.Adresa, P.IDCard FROM Utilizator U INNER JOIN Persoana P ON  U.IDUtilizator = P.IDUtilizator Where U.IDUtilizator = \"" .$_SESSION['idU']. "\"";
$result = $conn->query($sql);

$card=True;

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

    ?>
    <h3> Full name: <?php echo $row["Nume"]." "; echo $row["Prenume"]?></h3>
    <h3>Email: <?php echo $row["Email"]?></h3>
    <h3>CNP: <?php echo $row["CNP"]?></h3>
    <h3>Address:</h3> <h4> <?php echo $row["Adresa"]?></h4>
    <?php
    $card=$row["IDCard"];
    if(!$card)
        $card=False;

  }
}


if($card){
    $sql = "SELECT Propietar, Exp, CCV FROM Card Where IDCard = \"" .$card. "\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {

        ?>
        <h3> Propietar card: <?php echo $row["Propietar"] ?></h3>
        <h3>Exp: <?php echo $row["Exp"]?></h3>
        <h3>CCV:</h3> <h4> <?php echo $row["CCV"]?></h4>
        <?php
      }
    }


}else{
?>



                            <div class="woocommerce-info">Ready to puchase? <a class="showlogin" data-toggle="collapse" href="#login-form-wrap" aria-expanded="false" aria-controls="login-form-wrap">Click here to add credit card details</a>
                            </div>

                            <form id="login-form-wrap" class="login collapse" method="post">


                                <p class="form-row">
                                    <label for="owner">Owner full name: <span class="required">*</span>
                                    </label>
                                    <input type="text" id="owner" name="owner" class="input-text">
                                </p>
                                <p class="form-row">
                                    <label for="details">Card Details: <span class="required">*</span>
                                    </label>
                                    <input type="text" id="details" name="details" class="input-text">
                                </p>
                                <p class="form-row">
                                    <label for="ccv">CCV: <span class="required">*</span>
                                    </label>
                                    <input type="text" id="ccv" name="ccv" class="input-text">
                                </p>
                                <p class="form-row">
                                    <label for="exp">Expiration date (YYYY-MM-DD): <span class="required">*</span>
                                    </label>
                                    <input type="text" id="exp" name="exp" class="input-text">
                                </p>
                                <p class="form-row">
                                    <input type="submit" value="Add Card" name="login" class="button">
                                </p>

                            </form>
                        </div>                       
                    </div>                    
                </div>
            </div>
        </div>
    </div>
<?php }?>

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

