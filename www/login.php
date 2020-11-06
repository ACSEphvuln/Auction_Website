<?php
include_once 'common.php';
include_once 'db_connect.php';
include_once 'accesscontrol.php';

if(isset($_SESSION['idU']))
  header("Location: index.php");


if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email=filter("email",50,FILTER_VALIDATE_EMAIL);
    $password=filter("password",50,FILTER_SANITIZE_STRING);

    $query= 'SELECT Email,Parola FROM Utilizator WHERE email = ? AND parola = ?';
    if ($stmt = $conn->prepare($query)) {
      $pass_hash=hash('sha256', 'BD'.$password);
        $stmt->bind_param("ss", $email,$pass_hash);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
           error('Wrong email/passowrd combination.');
      }
      $stmt->close();
    } else error('Unexpecter error.');

    // login sucessful
    session_regenerate_id(TRUE);
    $_SESSION['email']=$email;

    $sql = "SELECT IDUtilizator FROM Utilizator WHERE email = \"".$email."\"";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $_SESSION["idU"]=$row["IDUtilizator"];
    } else error("Internal server error.");


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