<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
<style>

*{
    margin: 0;
    padding:0;
    box-sizing: 0;
}

body{
    overflow:hidden;
    height: 100vh;
    width:100%;
    display:grid;
    place-items: center;
}
.form{
    width:200px;
}
input,button{
    width: 100%;
    margin:15px 12px;
}
</style>
</head>
<body>
    <div class="form">
        <input type="text" id="usr" placeholder="Username">
        <input type="text" id="pwd" placeholder="Password">
        <button id="submit">Login</button>
    </div>
</body>
</html>
<script>

const usr = document.querySelector("#usr");
const pwd = document.querySelector("#pwd");
const btn = document.querySelector("#submit");

btn.addEventListener("click",() => {
    var form = new FormData();
    form.append("usr",usr.value);
    form.append("pwd",pwd.value);

    var http = new XMLHttpRequest();
    http.onload = () => {
        if(http.readyState == 4){
            var jsondata = JSON.parse(http.responseText);
            if(http.status == 200){
                if("token" in jsondata){
                    localStorage.setItem("token",jsondata.token);
                    if(jsondata.role == "a"){
                        location.href = "/admin";
                    }else{
                        location.href = "/home";
                    }
                }else{
                    alert("Unknown error");
                }
            }else{
                if("msg" in jsondata){
                    alert(jsondata.msg);
                }else{
                    alert("Unknown error");
                }
            }
        }
    }

    http.open("POST","/api/login");
    http.send(form);
})

</script>