<?php 
$host = 'mysql';
$user = 'root';
$pass = 'rootpassword';
$db_name = 'myDb';
$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

/*
$sql = "SELECT id_ticket, id_user, id_location,paid FROM Tickets";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["id_ticket"]. " - id_user: " . $row["id_user"].  " - id_location: " . $row["id_location"]. " - paid: " . $row["paid"]."<br>";
  }
} else {
  echo "0 results";
}
*/

/*
//Test database:
$sql = "SELECT id_user, name,email, password FROM Person";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["id_user"]. " - Name: " . $row["name"].  " - Email: " . $row["email"]. " - Password: " . $row["password"]."<br>";
  }
} else {
  echo "0 results";
}*/

/*
//Test Location database:
$sql = "SELECT id_location, name, price, description, image FROM Location";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["id_location"]. " - Name: " . $row["name"].  " - price: " . $row["price"]. " - Desc: " . $row["description"].  " - Img: " . $row["image"]. "<br>";
  }
} else {
  echo "0 results";
}
*/


  ?>
