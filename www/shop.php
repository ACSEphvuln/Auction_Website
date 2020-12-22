<?php
include_once 'db_connect.php';
include_once 'common.php';
include_once 'accesscontrol.php';

global $conn;

// Show all phones
$sql = "SELECT IDTelefon, LocImagine,Nume,PretInitial,Vandut, DataLicitatie FROM Telefon";
$result = $conn->query($sql);
$phones='';
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $imgLocation=$row['LocImagine'];
    $name=$row['Nume'];
    $initialPrice=$row['PretInitial'];
    $phoneID=$row['IDTelefon'];
    $auctionDate=$row['DataLicitatie'];

    // Sold phone 
    $auctionStatus='';
    if($row["Vandut"])
        $price = "SOLD";
    else{
        $price =" <div class=\"product-carousel-price\"><ins>Starting price: ${initialPrice} lei</ins></div> ";
        if($auctionDate <= date("Y-m-d h:i:s"))
            $auctionStatus='Started NOW!';
        else 
            $auctionStatus="Starting at: ${auctionDate}";
    }

    // Add phone to view
    $phones=$phones.<<<PHONE
    <div class="col-md-3 col-sm-6">
        <div class="single-shop-product">
            <div class="product-upper">
                <img src=${imgLocation}>
            </div>
            <h2><a href="">${name}</a></h2>
            ${auctionStatus}
            ${price}
            <div class="product-option-shop">
                <a class="add_to_cart_button" data-quantity="1" data-product_sku="" data-product_id="70" rel="nofollow" href="single-product.php?t=${phoneID}" >Details</a>
            </div>                       
        </div>
    </div>
PHONE;
 }
}




echo $HEADER;
echo PrintHeader("Shop");
echo <<<SHOPPAGE
<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">
        ${phones}
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="product-pagination text-center">
                    <nav>
                      <ul class="pagination">
                        <li>
                          <a aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                          </a>
                        </li>
                        <li><a href="#">1</a></li>
                        <li>
                          <a  aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                          </a>
                        </li>
                      </ul>
                    </nav>                        
                </div>
            </div>
        </div>
    </div>
</div>
SHOPPAGE;
echo $FOOTER;

?>