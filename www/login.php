<?php
include_once 'common.php';
include_once 'db_connect.php';
include_once 'accesscontrol.php';

// If user is already logged in, redirect to home page
if(isset($_SESSION['idU']))
  header("Location: index.php");

// Log in user
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email=filter("email",50,FILTER_VALIDATE_EMAIL);
    $password=filter("password",50,FILTER_SANITIZE_STRING);

    // Check if user is in the database 
    $query= 'SELECT IDUtilizator,Email,Parola FROM Utilizator WHERE email = ? AND parola = ?';
    if ($stmt = $conn->prepare($query)) {
        // Hash the provided password
        $pass_hash=hash('sha256', 'BD'.$password);
        $stmt->bind_param("ss", $email,$pass_hash);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 0 )
            error('Wrong email/passowrd combination.');
        $row = $result->fetch_assoc();
        $stmt->close();

        // Start a new session
        session_regenerate_id(TRUE);
        $_SESSION["idU"]=$row["IDUtilizator"];

    } else error('Unexpecter error.');

    // Login sucessful, redirect to home page
    header("Location: index.php");
}

echo $HEADER;
echo PrintHeader("Login");
echo <<<LOGINPAGE
    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">                
                <div class="col-md-12">
                    <div class="product-content-right">
                        <div class="woocommerce">
                            <form id="login-form-wrap" method="post">
                                <p>Please enter the information requested below:</p>
                                <p class="form-row form-row-first">
                                    <label for="email">Email <span class="required">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" class="input-text">
                                </p>
                                <p class="form-row form-row-last">
                                    <label for="password">Password <span class="required">*</span>
                                    </label>
                                    <input type="password" id="password" name="password" class="input-text">
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
LOGINPAGE;
echo $FOOTER;