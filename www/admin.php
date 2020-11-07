<?php
include_once 'common.php';
include_once 'db_connect.php';
include_once 'accesscontrol.php';

if($_SESSION['idU']==1){

global $conn;



if($_SERVER["REQUEST_METHOD"] == "POST"){
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

}


$sql = "SELECT  V.NumeFirma,T.Nume, T.PretInitial, T.DataLicitatie FROM Telefon T INNER JOIN Vanzator V ON V.IDUtilizator = T.IDUtilizator INNER JOIN Utilizator U ON  V.IDUtilizator = U.IDUtilizator Where T.Vandut = False AND T.DataLicitatie < NOW()";
$result = $conn->query($sql);
$tab='';
$datalist='<datalist id="auction">';
if ($result->num_rows > 0) {
	$tab="<table style=\"width:100%\"><tr><td>Nume Firma</td><td>Nume Telefon</td><td>Pret Initial</td><td>Data Inceput Licitiatie</td> </tr>";
  // output data of each row
	while($row = $result->fetch_assoc()){
		$name=$row['Nume'];
		$datalist=$datalist."<option value=\"${name}\">";
		$tab=$tab."<tr>";
		foreach ($row as $value) {
			$tab=$tab."<td>".$value."</td>";
		}
		$tab=$tab."</tr>";
	}

	$tab=$tab."</table>";
}

$datalist=$datalist."</datalist>";

$BODY=<<<BODY
<center>
<form id="login-form-wrap" method="post">

	<p>End and bill auctions:</p>
	<input list="auction" name="auction">
	${datalist}
	<input type="submit" value="END" id="END" name="END" class="button" >
	${tab}
</form>
</center>
BODY;


echo $HEADER;
echo printHeader("Administrative pannel");
echo $BODY;

echo $FOOTER;





}?>