<?php 
/* Common functions used to serve the webapp*/

// Raise an error and go back
function error($msg) {
  echo "<script language=\"JavaScript\">alert(\"${msg}\");history.back();</script>";
  exit;
}

// Filter input
function filter($input,$inputMaxLen,$filterType,$method="POST"){
  $inputName=$input;
  if($method == "POST"){
    $input=$_POST[$input];
    if(isset($_POST[$input]))
      error("Please provide the ".$inputName.".");
  }else{
    $input=$_GET[$input];
    if(!isset($_GET[$input]))
      error("Please provide the ".$inputName.".");
  }

  $input=trim($input);
  if(empty($input))
    error("Please provide the ".$inputName.".");

  if(strlen($input)>$inputMaxLen)
    error(ucfirst($inputName)." too long!");

  $input=filter_var($input, $filterType);
  if (!$input) 
    error("Please provide a valid".$inputName.".");

  return $input;
}

// Generate a new HTML table that will be returned as a string
class FancyTable{
  private $tableHeader; // Array containing strings of table columns names
  private $numcol;      // Number of columns (count($tableHeader))
  private $tableColumns;// Array of Arrays containing each column
  private $numrows=0;   // Number of rows

  public function __construct($tableHeader){
    $this->tableHeader=$tableHeader;
    $this->numcol=count($tableHeader);
    $this->tableColumns=Array();
  }
  public function appendRow($row){
    if(is_array($row))
      if(count($row)==$this->numcol){
        array_push($this->tableColumns,$row);
        $this->numrows++;
      } else error("Invalid number of columns at FancyTable, given".count($row)." expecting ".$this->numcol);
  }

  public function getTableHTML(){
    $table='';
    $table=$table. '<table cellspacing="0" class="shop_table cart"><thead><tr>';
    for ($i=0; $i < $this->numcol ; $i++) { 
      $table=$table. '<th>'.$this->tableHeader[$i].'</th>';
    }
    $table=$table. '</tr></thead>';

    $table=$table. '<tbody>';
    for ($i=0; $i < $this->numrows ; $i++) { 
      $table=$table. '<tr>';
      for ($j=0; $j < $this->numcol ; $j++) {
        $table=$table. '<td>'.$this->tableColumns[$i][$j].'</td>';
      }
      $table=$table. '</tr>';
    }
    $table=$table. '</tbody></table>';
    return $table;
  }

}

// Header used in all pages
$HEADER=<<<'HEADERHTML'
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
    <title>Auction.bb</title>
    
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
    <div class="site-branding-area">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="logo">
                        <h1><a href="index.php"><img src="img/logo.png"></a></h1>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div> <!-- End site branding area -->
    
    <div class="mainmenu-area">
        <div class="container">
            <div class="row">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div> 
                <div class="navbar-collapse collapse">
                    <ul id="menu_list" class="nav navbar-nav">

                    </ul>
                </div>  
            </div>
        </div>
    </div>
HEADERHTML;

// Specific header used per page indicating title
function PrintHeader($headername){
return <<<PrintHeaderHTML
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>${headername}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
PrintHeaderHTML;
}

// Footer contained in all pages
$FOOTER=<<<'FOOTERHTML'
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
FOOTERHTML;
?>