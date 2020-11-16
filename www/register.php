<?php 
include_once 'db_connect.php';
include_once 'common.php';
include_once 'accesscontrol.php';

// If user already logged in, redirect to home page
if(isset($_SESSION['idU']))
  header("Location: index.php");

// Register user
if($_SERVER["REQUEST_METHOD"] == "POST"){

  $email=filter("email",50,FILTER_VALIDATE_EMAIL);
  $password=filter("password",50,FILTER_SANITIZE_STRING);
  $firstname=filter("firstname",50,FILTER_SANITIZE_STRING);
  $lastname=filter("lastname",50,FILTER_SANITIZE_STRING);
  $CNP=filter("CNP",13,FILTER_SANITIZE_STRING);
  $address=filter("address",80,FILTER_SANITIZE_STRING);

  if(!is_numeric($CNP))
      error("Please provide a valid CNP.");

  if($CNP < 0)
      error("Please provide a valid CNP.");


  // Check if email is already registered
  $query= 'SELECT IDUtilizator FROM Utilizator WHERE Email = ?';
  if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        error('There is already a registered account with your email.');
    }
    $stmt->close();
  }

  // Add user
  $query="INSERT INTO Utilizator (Email, Parola) VALUES(?,?)"; 
  if ($stmt = $conn->prepare($query)) {
    $pass_hash=hash('sha256', 'BD'.$password);
    $stmt->bind_param("ss",$email,$pass_hash);
    $stmt->execute();
    $stmt->close();
  } else error("Internal server error.");

  // Add user information
  $query="INSERT INTO Persoana (IDUtilizator, Nume, Prenume, CNP, Adresa) SELECT Utilizator.IDUtilizator,?,?,?,? FROM Utilizator WHERE Utilizator.Email = ? "; 
  if ($stmt = $conn->prepare($query)) {
    $pass_hash=hash('sha256', 'BD'.$password);
    $stmt->bind_param("sssss",$lastname,$firstname,$CNP,$address,$email);
    $stmt->execute();
    $stmt->close();
  } else error("Internal server error.");


  //Register success.
  error("Registration succesful. Please login.");

}

echo $HEADER;
echo PrintHeader("Register");
echo <<<REGISTERPAGE
<div class="single-product-area">
    <div class="zigzag-bottom"></div>
    <div class="container">
        <div class="row">                
            <div class="col-md-12">
                <div class="product-content-right">
                    <div class="woocommerce">
                        <form id="login-form-wrap" method="post">
                            <p>Please enter the information requested below:</p>
                            <p class="form-row">
                                <label for="lastname">Last name:<span class="required">*</span>
                                </label>
                                <input type="text" id="lastname" name="lastname" class="input-text">
                            </p>
                            <div class="clear"></div>
                            <p class="form-row">
                                <label for="firstname">First name:<span class="required">*</span>
                                </label>
                                <input type="text" id="firstname" name="firstname" class="input-text">
                            </p>
                            <div class="clear"></div>
                            <p class="form-row">
                                <label for="email">Email <span class="required">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="input-text">
                            </p>
                            <p class="form-row">
                                <label for="psw">Password <span class="required">*</span>
                                </label>
                                <input type="password" id="password" name="password" class="input-text">
                            </p>
                            <div class="clear"></div>

                            <p class="form-row">
                                <label for="psw-repeat">Confirm Password <span class="required">*</span>
                                </label>
                                <input type="password" id="psw-repeat" name="psw-repeat" class="input-text">
                            </p>
                            <div class="clear"></div>
                            <p class="form-row">
                                <label for="CNP">CNP <span class="required">*</span>
                                </label>
                                <input type="text" id="CNP" name="CNP" class="input-text">
                            </p>
                            <div class="clear"></div>

                            <p class="form-row">
                                <label for="address">Address <span class="required">*</span>
                                </label>
                                <input type="text" id="address" name="address" class="input-text">
                            </p>
                            <div class="clear"></div>
                            <p class="form-row">
                                <input type="submit" value="Login" name="login" class="button">
                            </p>
                            <p class="lost_password">
                                <a href="#">Lost your password?</a>
                            </p>
                        </form>
                    </div>                       
                </div>                    
            </div>
        </div>
    </div>
</div>
REGISTERPAGE;
echo $FOOTER;
?>