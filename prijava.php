<?php
include_once('glava.php');

function validate_login($username, $password){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$pass = sha1($password);
	$query = "SELECT * FROM users WHERE username='$username' AND password='$pass'";
	$res = $conn->query($query);
	if($user_obj = $res->fetch_object()){
		return $user_obj->id;
	}
	return -1;
}

$error="";
if(isset($_POST["poslji"])){
	//Preveri prijavne podatke
	if(($user_id = validate_login($_POST["username"], $_POST["password"])) >= 0){
		//Zapomni si prijavljenega uporabnika v seji in preusmeri na index.php
		$_SESSION["USER_ID"] = $user_id;
        $_SESSION["USERNAME"] = $_POST["username"];
		header("Location: index.php");
		die();
	} else{
		$error = "Prijava ni uspela.";
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
            width: 100%;
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
            text-align: center;
            alignment: center;
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

	<h2 align="center">Log in</h2>
    <fieldset>
        <form action="prijava.php" method="POST">
            <label>User name</label><input type="text" name="username" /> <br/>
            <label>Password</label><input type="password" name="password" /> <br/>
            <input type="submit" name="poslji" value="Submit" /> <br/>
            <label style="font-size: 90%"><?php echo $error; ?></label>
            <label id="reg" style="font-size: 70%; margin: auto">Not a user? Register <a href="registracija.php">here.</a> </label>
        </form>
    </fieldset>

<?php
include_once('noga.php');
?>