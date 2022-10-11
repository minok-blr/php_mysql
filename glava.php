<?php
	session_start();
	
	//Seja poteče po 30 minutah - avtomatsko odjavi neaktivnega uporabnika
	if(isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] < 1800){
		session_regenerate_id(true);
	}
	$_SESSION['LAST_ACTIVITY'] = time();
	
	//Poveži se z bazo
	$conn = new mysqli('localhost', 'root', '', 'vaja1');
	//Nastavi kodiranje znakov, ki se uporablja pri komunikaciji z bazo
	$conn->set_charset("UTF8");
?>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<title>Vaja 1</title>
    <style>
        h1 {
            padding: 10px 0 0 0;
        }
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333;
        }

        li {
            float: left;
        }

        li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }


        li a:hover:not(.active) {
            background-color: #111;
        }

        .active {
            background-color: #04AA6D;
        }

        .searchbox{
            width: 65%;
            display: inline-grid;
            color: black;
            text-align: center;
            padding: 15px 0px 0px 0px;
        }
        input[type=text], button[type=submit]{
            text-align: left;
        }

        .catchoose{
            margin: auto;
            display: flex;
        }

        .catchoose-hideads{
            display: inline-block;
            float: left;
            text-align: center;
        }

        .catchoose-dropdown{
            display: inline-grid;
            margin: 0px 0px 0px 20px;
        }

    </style>
</head>
<body style="background-color: #bfbfbf">
	<h1 align="center">Advertisements</h1>
	<nav>
		<ul>
			<li><a href="index.php">Home</a></li>
			<?php
			if(isset($_SESSION["USER_ID"])){
				?>
                <li style="float:left"><a href="myads.php">My ads</a></li>
				<li style="float:left"><a href="objavi.php">Publish</a></li>
				<li style="float:right"><a href="odjava.php">Log out</a></li>
				<?php
			} else{
				?>
				<li style="float:right"><a href="prijava.php">Log in</a></li>
				<li style="float:right"><a href="registracija.php">Registration</a></li>
				<?php
			}
			?>
            <li class="searchbox">
                <form action="index.php?results" method="post">
                    <input type="text" placeholder="Search" name="searchfunc">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </li>
		</ul>
	</nav>

    <?php
        if(isset($_SESSION["USER_ID"])){?>
            <h3 style="text-align: center;">Welcome, <?php echo $_SESSION["USERNAME"] ?></h3>
    <?php } ?>

    <?php
    if(($_SERVER['REQUEST_URI'] != "/test/objavi.php") && ($_SERVER['REQUEST_URI'] != "/test/prijava.php")
        && ($_SERVER['REQUEST_URI'] != "/test/registracija.php")){
    ?>
        <form class="catchoose" action="" method="post">
            <div class="catchoose-hideads">
                <input type="checkbox" id="hideexpads" name="hideexpads" value="showAll" <?php if(isset($_POST['hideexpads'])) echo "checked='checked'"; ?>>
                <label for="hideexpads">Hide expired ads</label><br>
                <input type="submit" value="Submit">
            </div>

            <div class="catchoose-dropdown">
                <label>Choose categories</label>
                <select name="categories[]" multiple="multiple" >
                    <option value="blank" name="blank"></option>
                    <option value="electronics">Electronics
                        <option value="chargers">&nbsp;&nbsp;&nbsp;Chargers</option>
                    </option>
                    <option value="hardware">Hardware</option>
                    <option value="animals">Animals</option>
                    <option value="vehicles">Vehicles</option>
                        <option value="cars">&nbsp;&nbsp;&nbsp;Cars</option>
                            <option value="audi">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Audi</option>
                            <option value="mitsubishi">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mitsubishi</option>
                            <option value="mazda">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mazda</option>
                        <option value="motorcycles">&nbsp;&nbsp;&nbsp;Motorcycles</option>
                            <option value="kawasaki">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kawasaki</option>
                            <option value="honda">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Honda</option>
                    <option value="games">Games</option>
                    <option value="hygiene">Hygiene</option>
                    <option value="weapons">Weapons</option>
                    <option value="Camping">Camping</option>
                </select>
            </div>
        </form>
    <?php
    }
    ?>


	<hr/>