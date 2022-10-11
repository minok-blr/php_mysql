<?php
include_once('glava.php');

// Funkcija preveri, ali v bazi obstaja uporabnik z določenim imenom in vrne true, če obstaja.
function username_exists($username){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$query = "SELECT * FROM users WHERE username='$username'";
	$res = $conn->query($query);
	return mysqli_num_rows($res) > 0;
}

// Funkcija ustvari uporabnika v tabeli users. Poskrbi tudi za ustrezno šifriranje uporabniškega gesla.
function register_user($username, $password, $email){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$pass = sha1($password);
    $email = mysqli_real_escape_string($conn, $email);
	/* 
		Tukaj za hashiranje gesla uporabljamo sha1 funkcijo. V praksi se priporočajo naprednejše metode, ki k geslu dodajo naključne znake (salt).
		Več informacij: 
		http://php.net/manual/en/faq.passwords.php#faq.passwords 
		https://crackstation.net/hashing-security.htm
	*/
	$query = "INSERT INTO users (username, password, email) VALUES ('$username', '$pass', '$email');";
	if($conn->query($query)){
		return true;
	}
	else{
		echo mysqli_error($conn);
		return false;
	}
}

$error = "";
if(isset($_POST["poslji"])){
	/*
		VALIDACIJA: preveriti moramo, ali je uporabnik pravilno vnesel podatke (unikatno uporabniško ime, dolžina gesla,...)
		Validacijo vnesenih podatkov VEDNO izvajamo na strežniški strani. Validacija, ki se izvede na strani odjemalca (recimo Javascript), 
		služi za bolj prijazne uporabniške vmesnike, saj uporabnika sproti obvešča o napakah. Validacija na strani odjemalca ne zagotavlja
		nobene varnosti, saj jo lahko uporabnik enostavno zaobide (developer tools,...).
	*/
	//Preveri če se gesli ujemata
	if($_POST["password"] != $_POST["repeat_password"]){
		$error = "Gesli se ne ujemata.";
	}
	//Preveri ali uporabniško ime obstaja
	else if(username_exists($_POST["username"])){
		$error = "Uporabniško ime je že zasedeno.";
	}
	//Podatki so pravilno izpolnjeni, registriraj uporabnika
	else if(register_user($_POST["username"], $_POST["password"], $_POST["email"])){
		header("Location: prijava.php");
		die();
	}
	//Prišlo je do napake pri registraciji
	else{
		$error = "Prišlo je do napake med registracijo uporabnika.";
	}
}

?>
    <style>
        *, *:before, *:after {
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111111;
        }

        form {
            max-width: 400px;
            margin: 30px auto;
            padding: 10px 20px;
            background: #f4f7f8;
            border-radius: 8px;
        }

        /* Full-width input fields */
        input[type=text], input[type=password] {
            width: 50%;
            height: 10px;
            padding: 12px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: none;
            background: #e6e6e6;
            color: #111111;
        }

        label {
            height: 10px;
            display: block;
            margin-bottom: 8px;
        }

        fieldset {
            margin-bottom: 20px;
            border: none;
            margin: auto;
            text-align: center;
        }

        input[type=text]:focus, input[type=password]:focus {
            background-color: #ddd;
            outline: none;
        }

        input[name=poslji] {
            width: 50%;
        }

        hr {
            border: 1px solid #f1f1f1;
            margin-bottom: 5px;
        }

        /* Set a style for all buttons */
        input {
            background-color: #333;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            opacity: 0.9;
        }

        input:hover {
            opacity:1;
        }

        /* Extra styles for the cancel button */
        .cancelbtn {
            padding: 14px 20px;
            background-color: #f44336;
        }

        /* Float cancel and signup buttons and add an equal width */
        .cancelbtn, .signupbtn {
            float: left;
            width: 50%;
        }

        /* Add padding to container elements */
        .container {
            padding: 16px;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        /* Change styles for cancel button and signup button on extra small screens */
        @media screen and (max-width: 300px) {
            .cancelbtn, .signupbtn {
                width: 100%;
            }
        }
    </style>
	<form action="registracija.php" method="POST">
        <div>
        <h2 align="center">Registration</h2>
        <p align="center">Please fill out this form to create an account.</p>
        <hr>
        <fieldset>
            <!--<label><b>First name</b></label>-->
            <input type="text" name="firstname" placeholder="First name" required/> <br/>
<!--            <label><b>Last name</b></label>-->
            <input type="text" name="lastname" placeholder="Last name" required/> <br/>
<!--            <label><b>User name</b></label>-->
            <input type="text" name="username" placeholder="User name" required/> <br/>
            <input type="text" name="email" placeholder="E-mail" required/> <br/>
<!--            <label><b>Password</b></label>-->
            <input type="password" name="password" placeholder="Password" required/> <br/>
<!--            <label><b>Repeat password</b>-->
            </label><input type="password" name="repeat_password" placeholder="Repeat password" required/> <br/>
            <input type="submit" name="poslji" value="Submit" /> <br/>
        </fieldset>

		<label><?php echo $error; ?></label>
        </div>
	</form>
<?php
include_once('noga.php');
?>