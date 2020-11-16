<?php
include_once 'db_connect.php';
include_once 'accesscontrol.php';
include_once 'common.php';

// User must be logged in in order to access the page
if(!isset($_SESSION['idU'])){
    header("Location: login.php");
}

// Reference db_connect connection to sql server
global $conn;

// Used to introduce card details
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Sanitise inputs
    $owner = filter('owner',50,FILTER_SANITIZE_STRING);
    $details = filter('details',50,FILTER_SANITIZE_STRING);
    $ccv = filter('ccv',50,FILTER_SANITIZE_STRING);
    $date = filter('date',10,FILTER_DEFAULT);

    // Date is parsed differently
    if (!date_parse_from_format("Y-m-d", $date))
      error("Bad expiration date format!");

    // Insert card details
    $query="INSERT INTO Card (Propietar, Exp, Detalii,CCV) VALUES(?,?,?,?)"; 
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssss",$owner,$date,$details,$ccv);
        $stmt->execute();
        $stmt->close();
    } else error("Internal server error at inserting Card.");

/*    $sql = "SELECT IDCard FROM Card WHERE CCV = \"".$ccv."\" AND  Detalii=\"".$details."\"";
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $idcard=$row["IDCard"];
    } else error("Internal server error at CardIdentification.");
---------------------------TODO-FIX-QUERYS----------------
    $query="UPDATE Persoana SET IDCard = ? WHERE IDUtilizator=?"; 
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ss",$idcard,$_SESSION['idU']);
        $stmt->execute();
        $stmt->close();
    } else error("Internal server error at Insert.");*/

    // Link the card with the person 
    $query="UPDATE Persoana SET IDCard = (SELECT Card FROM (SELECT * FROM Card WHERE CCV = ? AND  Detalii= ? AS C) WHERE IDUtilizator = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ssi",$ccv,$details,$_SESSION['idU']);
        $stmt->execute();
        $stmt->close();
    } else error("Internal server error at linking Card to User.");


}

// HTML parts that can be empty
$details='';
$cardDetails='';

// Print user details and store in $card if the user has a card
$card=True;
$query = "SELECT U.Email, P.Nume, P.Prenume, P.CNP, P.Adresa, P.IDCard FROM Utilizator U INNER JOIN Persoana P ON  U.IDUtilizator = P.IDUtilizator Where U.IDUtilizator = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i",$_SESSION['idU']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
        $lname=$row["Nume"];
        $email=$row["Email"];
        $CNP=$row["CNP"];
        $card=$row["IDCard"];
        $fname=$row["Prenume"];
        $address=$row["Adresa"];
        $details=
<<<DETAILS
    <h3>Full name: ${lname} ${fname}</h3>
    <h3>Email: ${email}</h3>
    <h3>CNP: ${CNP}</h3>
    <h3>Address:</h3> <h4> ${address}</h4>
DETAILS;
        $card=$row["IDCard"];
        if(!$card)
            $card=False;

      }
    }
} else error("Internat sever error.")
// If user has a card, print some card details back to user
if($card){
    $sql = "SELECT Propietar, Exp, CCV FROM Card Where IDCard = \"" .$card. "\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
        $cardDetails=<<<CARDDET
        <h3> Propietar card: <?php echo ${row["Propietar"]} ?></h3>
        <h3>Exp: <?php echo ${row["Exp"]}?></h3>
        <h3>CCV:</h3> <h4> <?php echo ${row["CCV"]}?></h4>
CARDDET;
      }
    }
// If user dose not have a card, print form where user can introduce card details
} else $cardDetails=<<<ENTERCARD
<div class="woocommerce-info">Ready to puchase? <a class="showlogin" data-toggle="collapse" href="#login-form-wrap" aria-expanded="false" aria-controls="login-form-wrap">Click here to add credit card details</a></div>
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
        <label for="date">Expiration date (YYYY-MM-DD): <span class="required">*</span>
        </label>
        <input type="text" id="date" name="date" class="input-text">
    </p>
    <p class="form-row">
        <input type="submit" value="Add Card" name="login" class="button">
    </p>
</form>
ENTERCARD;

echo $HEADER;
echo PrintHeader("Account info");
echo
<<<PART
<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">                
            <div class="col-md-12">
                <div class="product-content-right">
                    <div class="woocommerce">
                    ${details}
                    ${cardDetails}
                    </div>                       
                </div>                    
            </div>
        </div>
    </div>
</div>
PART;
echo $FOOTER;
