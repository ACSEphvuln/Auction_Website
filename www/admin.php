<?php
include_once 'common.php';
include_once 'db_connect.php';
include_once 'accesscontrol.php';

if($_SESSION['idU']==1){

global $conn;



if($_SERVER["REQUEST_METHOD"] == "POST"){

	if($_POST['ACTION']=='ENDAUCTION'){
		$phoneName=filter("auction",50,FILTER_SANITIZE_STRING);
		$idUser=0;
		$price=0;
		$date="";
		$phoneID=0;

		$query="SELECT IDTelefon,Vandut,DataLicitatie FROM Telefon WHERE Nume = ?";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("s",$phoneName);
			$stmt->execute();

			$result = $stmt->get_result();
			if($result->num_rows === 0 ) 
			    error("Phone not found.");
			$row = $result->fetch_assoc();
			$stmt->close();

			if($row["Vandut"]==True)
				error("Phone already sold!");

			if($row["DataLicitatie"] > date("Y-m-d h:i:s"))
				error("Auction did not start yet!");

			$phoneID=$row["IDTelefon"];
		} else error("Internal server error at select query.");


		//Searching for winning auction
		$max=0;
		$idt=$phoneID;
	    $idt ="./auction/".$idt.".csv";
	    if(file_exists($idt)){
	        $f=fopen($idt, "r");
	        if($f !== FALSE){
	        while (($data = fgetcsv($f, 1000, ",")) !== FALSE)
	        	if($max<$data[2]){
		            $date=$data[0];
		            $idUser=$data[1];
		            $price=$data[2];
		        }
	        fclose($f);
	        } else error("Unable to open file.");
	    } else error("No auctions!");


		$query="INSERT INTO Licitatie(IDUtilizator,IDTelefon,PretLicitat,DataLicitatie) SELECT ?,Telefon.IDTelefon,?,? FROM Telefon WHERE Telefon.IDTelefon = ?";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("idss",$idUser, $price, $date, $phoneID);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at insert query.");

		//$query="UPDATE Telefon SET Vandut = True WHERE IDTelefon = (SELECT IDTelefon FROM (SELECT * FROM Telefon) AS A WHERE Nume = ?)";
		$query="UPDATE Telefon SET Vandut = True WHERE IDTelefon = ?";
		if ($stmt = $conn->prepare($query)) {
			//$stmt->bind_param("s",$phoneName);
			$stmt->bind_param("s",$phoneID);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at update query.");

	} else if($_POST['ACTION']=='DELETEUSER'){
		$CNP=filter("CNP",13,FILTER_SANITIZE_STRING);

		$query="DELETE FROM Utilizator WHERE IDUtilizator IN (SELECT * FROM (SELECT IDUtilizator FROM Persoana WHERE CNP = ?) AS P)";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("s",$CNP);
			$stmt->execute();
			$stmt->close();
		} else error("Bad delete syntax.");
		

	}
}


$sql = "SELECT  V.NumeFirma,T.Nume, T.PretInitial, T.DataLicitatie FROM Telefon T INNER JOIN Vanzator V ON V.IDUtilizator = T.IDUtilizator INNER JOIN Utilizator U ON  V.IDUtilizator = U.IDUtilizator Where T.Vandut = False AND T.DataLicitatie < NOW()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
$tableAuction='';
$datalist='<datalist id="auction">';
	$tab=new FancyTable(4,Array('Seller','Phone Name','Starting Price','Started at'));
	while($row = $result->fetch_assoc()){
		$datalist=$datalist."<option value=\"".$row['Nume']."\">";
		$tab->appendRow(Array($row['NumeFirma'],$row['Nume'],$row['PretInitial'],$row['DataLicitatie']));
	}
	$tableAuction=$tab->getHTML();
$datalist=$datalist."</datalist>";
}

$sql = "SELECT U.Email, P.Nume, P.Prenume, P.CNP, P.IDCard  FROM Utilizator U INNER JOIN Persoana P ON U.IDUtilizator = P.IDUtilizator";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
$tableUsers='';
$datalistUser='<datalist id="CNP">';
	$tab=new FancyTable(5,Array('Email','Last Name','Frist Name','CNP','Card(?)'));
	while($row = $result->fetch_assoc()){
		$datalistUser=$datalistUser."<option value=\"".$row['CNP']."\">";
		
		if($row['IDCard']!== NULL)
			$card='Yes';
		else
			$card="X";
		$tab->appendRow(Array($row['Email'],$row['Nume'],$row['Prenume'],$row['CNP'],$card));
	}
	$tableUsers=$tab->getHTML();
$datalistUser=$datalistUser."</datalist>";
}


$sql = "SELECT COUNT(*) AS NumPers FROM Persoana";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$buyers=$row["NumPers"];
}


$sql = "SELECT COUNT(*) AS NumSel FROM Vanzator";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$sellers=$row["NumSel"];
}



$BODY=<<<BODY
<center>
Number of registered users: ${buyers} <br/>
Number of registered sellers: ${sellers} <br/>
<form id="login-form-wrap" method="post">

	<p>End and bill auctions:</p>
	<input list="auction" name="auction">
	${datalist}
	<input type="submit" value="ENDAUCTION" id="ACTION" name="ACTION" class="button" >
	${tableAuction}
</form>

<form id="login-form-wrap" method="post">

	<p>Delete user:</p>
	<input list="CNP" name="CNP">
	${datalistUser}
	<input type="submit" value="DELETEUSER" id="ACTION" name="ACTION" class="button" >
	${tableUsers}
</form>
</center>
BODY;


echo $HEADER;
echo printHeader("Administrative pannel");
echo $BODY;

echo $FOOTER;





}?>