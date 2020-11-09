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
        $purchases="<tr class=\"cart_item\">";
        while($row = $result->fetch_assoc()){
                $phoneID=$row['IDTelefon'];
                $imgLoc=$row['LocImagine'];
                $phoneName=$row["Nume"];
                $bill=$row["PretLicitat"];
                $purchaseDate=$row["DataLicitatie"];
                $purchases=$purchases.<<<SINGLEPHONE
                <td class="product-thumbnail">
                    <a href="single-product.php?t=${phoneID}"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" src="${imgLoc}"></a>
                </td>

                <td class="product-name">
                    <a href="single-product.php?t=${phoneID}">${phoneName}</a> 
                </td>

                <td class="product-price">
                    <span class="amount">${bill}</span> 
                </td>

                <td class="product-purchase-date">
                    <span class="amount">${purchaseDate}</span> 
                </td>
SINGLEPHONE;
}
        $purchases=$purchases."</tr>";
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
                            <table cellspacing="0" class="shop_table cart">
                                <thead>
                                    <tr>
                                        <th class="product-thumbnail">Phone</th>
                                        <th class="product-name">Name</th>
                                        <th class="product-price">Price</th>
                                        <th class="product-purchase-date">Date bougth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                ${purchases}
                                </tbody>
                            </table>
                        </div>                        
                    </div>                    
                </div>
            </div>
        </div>
    </div>
BODYHTML;

echo $FOOTER;

?>