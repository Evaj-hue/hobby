<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="Register" content="This is where you'll need to register your account">
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
            <h1 class="title">COZYRACK REGISTRATION</h1>
            
            <div class="form-group">
                <div class="inputbox">
                    <input type="text" name="full_name" id="full_name" placeholder="Full Name" required>
                </div>
                <div class="inputbox">
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
            </div>

            <div class="form-group">
                <div class="inputbox">
                    <input type="text" name="contact_number" id="contact_number" placeholder="Contact Number" required>
                </div>
                <div class="inputbox">
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
            </div>

            <div class="form-group">
                <div class="inputbox">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <div class="inputbox">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                </div>
            </div>

            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</main>

</body>
</html>