<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="LOGIN" content="This where you're going to login to access the CozyRack  system">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <title>COZYRACK LOGiN PAGE</title>
</head>
<body>
    <header >
         <img src="src/page-logo.png">
            <nav> 
                 <ul>
                       <li> <a href="index.php">Home</a> </li>
                      <li> <a href="#">About</a> </li>
                      <li> <a href="#">Contact Us</a> </li>
                 </ul> 
            </nav> 
</header> 
<main> 
    <div class="form-container">
        <form action="#">
            <h1 class="title">COZYRACK LOGIN PAGE</h1>
            <div class="inputbox"> 
                 <input type="text" name="username" id="username" placeholder="Username" required>
            </div>
            <div class="inputbox">
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Not registered? <a href="register.php">Create an account</a></p>
    </div>
</main>
</body>
</html>