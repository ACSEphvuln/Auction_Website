<?
include_once 'db_connect.php';
include_once 'common.php';
include_once 'accesscontrol.php';

if(!isset($_SESSION['idU']))
  header("Location: login.php");

echo $HEADER;
echo PrintHeader('Puchases');
  
$hasPurchased=True;

$query = "SELECT T.IDTelefon, T.LocImagine, T.Nume, L.PretLicitat, L.DataLicitatie FROM Licitatie L INNER JOIN Telefon T ON L.IDTelefon = T.IDTelefon Where L.IDUtilizator = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i",$_SESSION['idU']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0){
        $purchases='';

        $pur = new FancyTable(4,Array('Phone','Name','Price','Date bougth'));
        while($row = $result->fetch_assoc()){
            $phoneID=$row['IDTelefon'];
            $imgLoc=$row['LocImagine'];
            $phoneName=$row["Nume"];
            $bill=$row["PretLicitat"];
            $purchaseDate=$row["DataLicitatie"];

            $thumbnail="<a href=\"single-product.php?t=${phoneID}\"><img width=\"50\" height=\"50\" alt=\"poster_1_up\" class=\"shop_thumbnail\" src=\"${imgLoc}\"></a>";
            $name="<a href=\"single-product.php?t=${phoneID}\">${phoneName}</a>";
            $pur->appendRow(Array($thumbnail,$name,$bill,$purchaseDate));
        }
        $purchases=$pur->getHTML();
    } else 
        $hasPurchased=False;
    $stmt->close();
} else error("Internal server error.");

if(!$hasPurchased)
    $purchases="<h1> No purchases yet. Ready to auction? </h1>";




echo <<<BODYHTML
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">                
                <div class="col-md-12">
                    <div class="product-content-right">
                        <div class="woocommerce">
                            ${purchases}
                        </div>                        
                    </div>                    
                </div>
            </div>
        </div>
    </div>
BODYHTML;

echo $FOOTER;

?>