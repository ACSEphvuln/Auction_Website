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
        $idt=filter("idtelefon",10,FILTER_VALIDATE_INT);
        $val=filter("valoare",14,FILTER_VALIDATE_INT);

        $query = "SELECT PretInitial FROM Telefon WHERE IDTelefon = ?";
        if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i",$idt);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        } else error("Internal server error.");

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();

            if($row["PretInitial"] < $val){

                $f = fopen("./auction/".$idt.".csv", "a") or die("Unable to open file!");
                fwrite($f, date("Y-m-d h:i:s").",".$_SESSION['idU'].",".$val."\n");
                fclose($f);

            } else{
                error("Pret mai mic decat cel minim!");
            }

        } else error("Internal Server Error.");


    }else{
        error("Introduce card details before in section Account Information.");
    }
  }



}



$id_tel=trim($_GET["t"]);
$id_tel=filter_var($id_tel, FILTER_VALIDATE_INT);
if (!filter_var($id_tel, FILTER_VALIDATE_INT)) 
    error("Internal Server Error.");
$query = "SELECT T.*, V.NumeFirma FROM Telefon T INNER JOIN Vanzator V ON V.IDUtilizator = T.IDUtilizator INNER JOIN Utilizator U ON  V.IDUtilizator = U.IDUtilizator Where T.IDTelefon = ?";
if ($stmt = $conn->prepare($query)) {
$stmt->bind_param("i",$id_tel);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
} else error("Internal server error.");

if ($result->num_rows > 0) {
  // output data of each row
  $row = $result->fetch_assoc(); 
  $imageLocation=$row['LocImagine'];
  $seller=$row["NumeFirma"];
  $name=$row["Nume"];
  $year=$row["AnAparitie"];
  $initialPrice=$row["PretInitial"];
  $sold=$row['Vandut'];
  $phoneid=$_GET["t"];
  $spec=$row["Specificatii"];
    if($sold)
        $price=<<<SOLD
<div class="product-inner-category">
    <p>VANDUT</p>
</div> 
SOLD;
    else
        $price=<<<PRICE
<div class="product-inner-price">
    <ins>Pret Initial: ${initialPrice} lei</ins>
</div>
<form action="" class="cart" method="post">
    <label for="valoare">Licit (lei):</label> 
    <input name="valoare" type="text">
    <input name="idtelefon" type="text" value=${phoneid} hidden="True" >
    <button class="add_to_cart_button" type="submit">Licita</button>
</form>
PRICE;


$auctions='';

if(isset($_GET['t']))
        $idt=$_GET['t'];
    else 
        $idt=$_POST['idtelefon'];
    $idt ="./auction/".$idt.".csv";
    if(file_exists($idt)){
        $f=fopen($idt, "r");
        if($f !== FALSE){
        while (($data = fgetcsv($f, 1000, ",")) !== FALSE)
            $auctions=$auctions.$data[0]." - ".$data[2]." lei<br>";
        
        fclose($f);
        }
    }


echo $HEADER;
echo printHeader("Product");
echo <<<PART
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                    
                <div class="col-md-8">
                    <div class="product-content-right">

            Vandut de ${seller}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="product-images">
                                    <div class="product-main-img">
                                        <img src="${imageLocation}" alt="">
                                    </div>

                                    <div class="product-gallery">
                                        <img src="${imageLocation}" alt="">
                                    </div>
                                </div>
                            </div>
                              
                            <div class="col-sm-6">
                                <div class="product-inner">
                                    <h2 class="product-name">${name} - ${year}</h2>
                                    ${price}
                                     <div role="tabpanel">
                                        <ul class="product-tab" role="tablist">
                                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Description</a></li>
                                            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Licitatii</a></li>

                                        </ul>
                                    
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                                <h2>Product Description</h2>  
                                                ${spec}
                                            </div>
  


                                            <div role="tabpanel" class="tab-pane fade" id="profile">
                                                <h2>Licitatii</h2>
                                            ${auctions}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
PART;
}
else error("Invalid phone id.");
echo $FOOTER;