<?php
include_once 'common.php';
include_once 'db_connect.php';
include_once 'accesscontrol.php';

// Only print the page if user is logged in as the administrator. Ignoring indentation
if($_SESSION['idU']==1){

// Reference db_connect connection to sql server
global $conn;


// FancyTable is located at common.php
class ActionTable extends FancyTable{
	private $tableKey;

	public function __construct($tableHeader,$tableKey){
	    parent::__construct($tableHeader);
	    $this->tableKey=$tableKey;
	}

	public function getActionHTML($title,$action,$conn,$sql){
		$tableKey=$this->tableKey;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$dataList='<datalist id="'.$tableKey.'">';
			while($row = $result->fetch_assoc()){
				$dataList=$dataList.'<option value="'.$row[$tableKey].'"">';
				$this->appendRow(array_values($row));
			}
			$dataList=$dataList."</datalist>";
			$table=$this->getTableHTML();
			$form=<<<TABLEFORM
			<form id="login-form-wrap" method="post">
				<p>${title}</p>
				<input list="${tableKey}" name="${tableKey}">
				${dataList}
				<input type="submit" value="${action}" id="ACTION" name="ACTION" class="button" >
				${table}
			</form>
TABLEFORM;
		}
		return $form;
	}



}
class QueryTable extends FancyTable{

	public function __construct($tableHeader){
	    parent::__construct($tableHeader);
	}

	public function getQueryHTML($title,$conn,$sql){

		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()){
				$this->appendRow(array_values($row));
			}
			$table=$this->getTableHTML();
			$form="<p>${title}</p>${table}";
		}
		return $form;
	}
}

// Action performed on database
if($_SERVER["REQUEST_METHOD"] == "POST"){

	// End a live auction by a supplied Phone Name
	if($_POST['ACTION']=='ENDAUCTION'){
		$phoneName=filter("Nume",50,FILTER_SANITIZE_STRING);
		$idUser=0;
		$price=0;
		$date="";
		$phoneID=0;

		// Test if the action can be closed and get phone id.
		$query="SELECT IDTelefon,Vandut,DataLicitatie FROM Telefon WHERE Nume = ?";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("s",$phoneName);
			$stmt->execute();

			$result = $stmt->get_result();
			// No phone with the post name
			if($result->num_rows === 0 ) 
			    error("Phone not found.");
			$row = $result->fetch_assoc();
			$stmt->close();

			// Check if phone sold
			if($row["Vandut"]==True)
				error("Phone already sold!");

			// Check if phone is in a live auction
			if($row["DataLicitatie"] > date("Y-m-d h:i:s"))
				error("Auction did not start yet!");

			// Store the phone id
			$phoneID=$row["IDTelefon"];
		} else error("Internal server error at select query.");


		//Search for winning auction
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


	    // Make an entry in the Auctions DB Table with the winning auction
		$query="INSERT INTO Licitatie(IDUtilizator,IDTelefon,PretLicitat,DataLicitatie) SELECT ?,T.IDTelefon,?,? FROM Telefon T WHERE T.IDTelefon = ?";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("idss",$idUser, $price, $date, $phoneID);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at insert query.");

		// Update the phone as sold
		$query="UPDATE Telefon SET Vandut = True WHERE IDTelefon = ?";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("s",$phoneID);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at update query.");

	//  Delete a user by a supplied CNP. Note: temporary keeping the card details for security measures
	} else if($_POST['ACTION']=='DELETEUSER'){
		$CNP=filter("CNP",13,FILTER_SANITIZE_STRING);

		$query="DELETE FROM Utilizator WHERE IDUtilizator = (SELECT IDUtilizator FROM Persoana WHERE CNP = ?)";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("s",$CNP);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at delete.");
		
	// Delete orphaned card by supplied Card ID
	} else if($_POST['ACTION']=='DELETECARD'){
		$IDCard=filter("IDCard",50,FILTER_SANITIZE_NUMBER_INT);

		$query="DELETE FROM Card WHERE IDCard = ?";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("i",$IDCard);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at delete.");
	
	// Give discount
	} else if($_POST['ACTION']=='GIVEDISCOUNT'){
		$companyName=filter("NumeFirma",50,FILTER_SANITIZE_STRING);

		$query="UPDATE Telefon SET PretInitial=PretInitial*95/100 WHERE IDUtilizator=(SELECT IDUtilizator FROM Vanzator WHERE NumeFirma=?)";
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("s",$companyName);
			$stmt->execute();
			$stmt->close();
		} else error("Internal server error at update.");

	}
}

// Show how many users the application servers
$sql = "SELECT COUNT(*) AS NumPers FROM Persoana";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$buyers=$row["NumPers"];
}

// Show how many sellers the application servers
$sql = "SELECT COUNT(*) AS NumSel FROM Vanzator";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$sellers=$row["NumSel"];
}

// Make a table with all the live auctions
$sql = "SELECT  V.NumeFirma,T.Nume, T.PretInitial, T.DataLicitatie FROM Telefon T INNER JOIN Vanzator V ON V.IDUtilizator = T.IDUtilizator INNER JOIN Utilizator U ON  V.IDUtilizator = U.IDUtilizator WHERE T.Vandut = False AND T.DataLicitatie < NOW()";
$auctionAction=new ActionTable(Array('Seller','Phone Name','Starting Price','Started at'),'Nume');
$endAuctionForm=$auctionAction->getActionHTML('End and bill auctions:','ENDAUCTION',$conn,$sql);

// Make a table with all users from Person Table
$sql = "SELECT U.Email, P.Nume, P.Prenume, P.CNP, P.IDCard  FROM Utilizator U INNER JOIN Persoana P ON U.IDUtilizator = P.IDUtilizator";
$usersAction=new ActionTable(Array('Email','Last Name','Frist Name','CNP','Card ID'),'CNP');
$deleteUserForm=$usersAction->getActionHTML('Delete user:','DELETEUSER',$conn,$sql);

// Make table with temporary held cards
$sql = "SELECT C.IDCard, C.Propietar, C.Exp FROM Card C LEFT OUTER JOIN Persoana P ON C.IDCard = P.IDCard  WHERE P.IDUtilizator IS NULL";
$cardAction=new ActionTable(Array('Card ID','Owner','Exp'),$tableKey='IDCard');
$deleteCardForm=$cardAction->getActionHTML('Remove temporary Held Cards:','DELETECARD',$conn,$sql);

// Make table for discounts for all the stock of a seller
$sql = "SELECT V.NumeFirma, SUM(T.PretInitial)*5/100 FROM Vanzator V INNER JOIN Telefon T ON V.IDUtilizator=T.IDUtilizator GROUP BY NumeFirma";
$discountAction=new ActionTable(Array('Company name','Total money that will be discounted:'),$tableKey='NumeFirma');
$discountTable=$discountAction->getActionHTML('Give 5% initial-price discount for all phones of the company:','GIVEDISCOUNT',$conn,$sql);

// Get users with expired cards
$sql = "SELECT U.Email, P.Nume, P.Prenume FROM Persoana P INNER JOIN Utilizator U ON U.IDUtilizator=P.IDUtilizator  WHERE P.IDCard IN (SELECT IDCard FROM Card WHERE Exp < DATE_FORMAT(now(), '%y-%m'))";
$expiredCards=new QueryTable(Array('Email','Last Name', 'First Name'));
$expiredCardsTable=$expiredCards->getQueryHTML('Users with expired cards:',$conn,$sql);

// Get sellers with more than 2 recorded sells
$sql = "SELECT U.Email, V.NumeFirma FROM Vanzator V INNER JOIN Utilizator U ON U.IDUtilizator=V.IDUtilizator  WHERE V.IDUtilizator IN (SELECT T.IDUtilizator FROM Telefon T INNER JOIN Licitatie L ON L.IDTelefon = T.IDTelefon GROUP BY T.IDUtilizator HAVING COUNT(L.IDLicitatie)>2)";
$goodSellers=new QueryTable(Array('Email','Company Name'));
$sellsTable=$goodSellers->getQueryHTML('Companies 2 or more sold phones:',$conn,$sql);


// Users that bought phones released from last year to present
$sql = "SELECT DISTINCT CONCAT(P.Nume, ' ', P.Prenume) AS NumeUtilizator FROM Persoana P INNER JOIN Licitatie L ON P.IDUtilizator = L.IDUtilizator WHERE L.IDTelefon = ANY (SELECT T.IDTelefon FROM Telefon T WHERE T.AnAparitie >= YEAR(now())-1)";
$goodBuyers=new QueryTable(Array('Users that bought phones released from last year to present'));
$buysTable=$goodBuyers->getQueryHTML('Top active users',$conn,$sql);



// Sellers and number of phones on sale
$sql = "SELECT V.NumeFirma,(SELECT COUNT(IDUtilizator) FROM Telefon T WHERE T.IDUtilizator = V.IDUtilizator) AS NrTel FROM Vanzator V";
$numberSell=new QueryTable(Array('Seller','Number of phones to sell'));
$numselTable=$numberSell->getQueryHTML('Sellers/Number of phones in sale',$conn,$sql);


echo $HEADER;
echo printHeader("Administrative pannel");
echo <<<BODY
<center>
Number of registered users: ${buyers} <br/>
Number of registered sellers: ${sellers} <br/>

${endAuctionForm}
${sellsTable}
${buysTable}
${discountTable}

${expiredCardsTable}
${deleteCardForm}
${deleteUserForm}
${numselTable}

</center>
BODY;
echo $FOOTER;





}?>