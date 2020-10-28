document.getElementById("menuBar").innerHTML=`

    <div class="site-branding-area">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="logo">
                        <h1><a href="index.html"><img src="img/logo.png"></a></h1>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div> <!-- End site branding area -->
    
    <div class="mainmenu-area">
        <div class="container">
            <div class="row">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div> 
                <div class="navbar-collapse collapse">
                    <ul id="menu_list" class="nav navbar-nav">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.html">Shop page</a></li>
                        <li><a href="cart.html">Purchases</a></li>
                        <li><a href="checkout.html">Checkout</a></li>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>  
            </div>
        </div>
    </div> <!-- End mainmenu area -->
	`
;



var menuList = document.getElementById("menu_list");
var menuListArr = menuList.getElementsByTagName("li");
var pageName = window.location.pathname.split("/").pop();

console.log(pageName);

for(var i=0; i<menuListArr.length;i++)
	if(menuListArr[i].getElementsByTagName('a')[0].getAttribute('href') == pageName )
		menuListArr[i].setAttribute("class", "active");


