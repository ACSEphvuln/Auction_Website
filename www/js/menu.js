var pageName = window.location.pathname.split("/").pop();

let items = [
        ['Home','index.php'],
        ['Shop','shop.php'],
        ['Purchases','purchases.php']
    ];


if(document.cookie.indexOf("auth=")>0){
    items.push(['Account Info','account_info.php']);
    items.push(['Logout','logout.php']);
} else{
    items.push(['Register','register.php']);
    items.push(['Login','login.php']);
}


ul = document.createElement('ul');
ul.classList.add("nav");
ul.classList.add("navbar-nav");
ul.setAttribute("id", "menu_list");

document.getElementsByClassName('navbar-collapse collapse')[0].appendChild(ul);

for (var i = 0; i < items.length; i++) {
    let li = document.createElement('li');
    ul.appendChild(li);
    let a = document.createElement('a');

    a.setAttribute("href",items[i][1]);
    a.innerHTML=items[i][0];
    li.appendChild(a);

    if(items[i][1]==pageName)
        li.setAttribute("class","active")

};




