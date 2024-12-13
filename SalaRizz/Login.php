<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="img/mainlogo.svg" type="image/icon type">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Salarizz</title>
</head>

<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
       
    }

    body{
        font-family: 'Poppins', sans-serif;
        
        margin: 0;
        padding: 0;
        background-color: black;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    
        margin: 0;
    }
    .container{
        display: flex;
        justify-content: center;
        align-items: center;
        display: flex;
        margin: 0;
    }

    .rotate {
        height: 140px;
        width: 150px;
        padding: 0;
       animation: rotate 4s linear infinite; 
}

.sala{
    color: #26F7B2;
    font-size: 150px;
    font-family: 'Poppins', sans-serif;
    font-weight: bold;
        

}
.rizz{
    color: white;
    font-size: 120px;
    font-family: 'Poppins', sans-serif;
    font-weight: bold;

}

.centerEverything{
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

</style>

<body>

  <div class="container" style="margin-left: 10%;">
    <div class="rotate">
        <img style="height: 150px; width: 150px;" src="img/logo.svg">
    </div>
    <h2 class="sala">Sala<span class="rizz">RIZZ</span></h2>
</div>
<!-- action="salarizz PHP/login.php" -->
<div class="container" style="margin-right: 5%;font-family: 'Poppins', sans-serif;">
    <form style="background-color: white; height: 600px; width: 400px; border-radius: 5%; justify-content: center;align-items: center;"  method="POST">
        <div style="background-color: #26F7B2; height: 60px; border-radius: 20px 20px 0% 0%;">
            <div style="display: flex;justify-content: center;">
                <img style="height: 60px; width: 50px;" src="img/mainlogo.svg">
            </div>
        </div>
        <div class="form-group">
            <label style="color: black; padding-bottom: 2%;margin-top: 30%; margin-left: 10%;">Username</label>
            <input style="border-color: #26F7B2; width: 300px; box-shadow: 5px 5px 5px rgb(183, 192, 196);margin-left: 10%;" type="email" class="form-control" id="email" placeholder="Enter Username" name="email">
        </div>
        <div class="form-group">
            <label style="color: black;padding-bottom: 2%;margin-left: 10%;padding-top: 2%;" for="pwd">Password</label>
            <input style="border-color: #26F7B2; width: 300px; box-shadow: 5px 5px 5px rgb(183, 192, 196);margin-left: 10%;" type="password" class="form-control" id="pwd" placeholder="Enter password" name="password">
        </div>
        <button style="background-color: #26F7B2; margin-top: 30px; box-shadow: 5px 5px 5px rgb(183, 192, 196);margin-left: 40%; font-weight: 600; font-family: 'Poppins', sans-serif;" type="submit" class="btn btn-default">Log In</button>
    </form>
</div>
   
    
<?php
require 'salarizz PHP/login.php'; // Include your database connection script
?>

</body>
</html>