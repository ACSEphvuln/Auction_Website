<?php
include_once 'db_connect.php';
include_once 'common.php';
include_once 'accesscontrol.php';

global $conn;

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(!isset($_SESSION['idU']))
        error("Login first!");

    if(!isset($_POST['idtelefon']))
        error("Internal Server Error at POST.");
    
    $query= 'SELECT IDCard FROM Persoana WHERE IDUtilizator = ?';

    if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $_SESSION['idU']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows ===0 ) 
        error("Internal Server Error");
    $row = $result->fetch_assoc();

    if ($row['IDCard'] != NULL) {

        $idt=trim($_POST['idtelefon']);
        $idt=filter_var($idt, FILTER_VALIDATE_INT);
        $val=trim($_POST['valoare']);
        $val=filter_var($val, FILTER_VALIDATE_INT);

        $sql = "SELECT PretInitial FROM Telefon WHERE IDTelefon = ".$idt;
        $result = $conn->query($sql);
        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();

            if($row["PretInitial"] < $val){
                $f = fopen("./auction/".$idt.".csv", "a") or die("Unable to open file!");
                fwrite($f, date("Y-m-d h:i:sa").",".$_SESSION['idU'].",".$val."\n");
                fclose($f);
            } else{
                error("Pret mai mic decat cel minim!");
            }

        } else error("Internal Server Error.");


    }else{
        error("Introduce card details before in section Account Information.");
    }
    $stmt->close();
  }



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
    <title>Product Page - E-Comerce</title>
    
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
                        <h2>Shop</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                    
                <div class="col-md-8">
                    <div class="product-content-right">

<?php 
$id_tel=trim($_GET["t"]);
$id_tel=filter_var($id_tel, FILTER_VALIDATE_INT);
if (!filter_var($id_tel, FILTER_VALIDATE_INT)) 
    error("Internal Server Error.");
$sql = "SELECT T.*, V.NumeFirma FROM Telefon T INNER JOIN Vanzator V ON V.IDUtilizator = T.IDUtilizator INNER JOIN Utilizator U ON  V.IDUtilizator = U.IDUtilizator Where T.IDTelefon = \"".$id_tel."\"";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  $row = $result->fetch_assoc(); ?>
            Vandut de <?php echo $row["NumeFirma"]; ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="product-images">
                                    <div class="product-main-img">
                                        <img src=<?php echo '"'.$row['LocImagine'].'"' ?> alt="">
                                    </div>
                                    
                                    <div class="product-gallery">
                                        <img src=<?php echo '"'.$row['LocImagine'].'"' ?>>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="product-inner">
                                    <h2 class="product-name"><?php echo $row["Nume"].' - '.$row["AnAparitie"]; ?></h2>
                                    <div class="product-inner-price">
                                        <ins>Pret Initial: <?php echo $row["PretInitial"]; ?> lei</ins>
                                    </div>    
                                    
                                    
                                    <?php if($row['Vandut']==True){ ?>
                                    <div class="product-inner-category">
                                        <p>VANDUT</p>
                                    </div> 
                                <?php } else{ ?>
                                    <form action="" class="cart" method="post">
                                        <label for="valoare">Licit (lei):</label> 
                                        <input name="valoare" type="text">
                                        <input name="idtelefon" type="text" value=<?php echo "\"".$_GET["t"]."\"";?> hidden="True" >
                                        <button class="add_to_cart_button" type="submit">Licita</button>
                                    </form>   
                                    <?php }?>
                                    <div role="tabpanel">
                                        <ul class="product-tab" role="tablist">
                                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Description</a></li>
                                            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Licitatii</a></li>

                                        </ul>
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                                <h2>Product Description</h2>  
                                                <?php echo $row["Specificatii"]; ?>
                                            </div>



                                            <div role="tabpanel" class="tab-pane fade" id="profile">
                                                <h2>Licitatii</h2>
                                                <?php
                                                if(isset($_GET['t']))
                                                    $idt=$_GET['t'];
                                                else 
                                                    $idt=$_POST['idtelefon'];
                                                $idt ="./auction/".$idt.".csv";
                                                if(file_exists($idt)){
                                                    $f=fopen($idt, "r");
                                                    if($f !== FALSE){
                                                    while (($data = fgetcsv($f, 1000, ",")) !== FALSE)
                                                        echo $data[0]." - ".$data[2]." lei<br>";
                                                    
                                                    fclose($f);
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
<?php }
else error("Invalid phone id.");
?>
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