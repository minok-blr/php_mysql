<?php
include_once('glava.php');

// Funkcija vstavi nov oglas v bazo. Preveri tudi, ali so podatki pravilno izpolnjeni. 
// Vrne false, če je prišlo do napake oz. true, če je oglas bil uspešno vstavljen.
function publish($title, $desc,$img){
	global $conn;
	$title = mysqli_real_escape_string($conn, $title);
    //$category = mysqli_real_escape_string($conn, $category);
	$desc = mysqli_real_escape_string($conn, $desc);
	$user_id = $_SESSION["USER_ID"];

    date_default_timezone_set('Europe/Ljubljana');
    $timestamp = date('Y-m-d H:i:s');

    $expdate = date('Y-m-d H:i:s', strtotime("+30 days"));


	/*//Preberemo vsebino (byte array) slike
	$img_file = file_get_contents($img["tmp_name"]);
	//Pripravimo byte array za pisanje v bazo (v polje tipa LONGBLOB)
	$img_file = mysqli_real_escape_string($conn, $img_file);*/
	$valzero = 0;

    // multiple queries here, insert into table ads, then get the latest ad id, then iterate through
    // the array of categories from the dropdown selection in publish.php and send to
    // junction ad-category table
	$query = "INSERT INTO ads (title, description, category, user_id, image, uploaddate, expdate, viewcount)
				VALUES('$title', '$desc', 'word','$user_id', '0', '$timestamp', '$expdate', '$valzero');";

    if($conn->query($query)){
        $ad_id_retrieve = $conn->insert_id;
        foreach ($_POST["categories"] as $ctg_name){
            $queryGET_CTG_ID = "SELECT id FROM categories WHERE name='$ctg_name'";
            $ctg_id = $conn->query($queryGET_CTG_ID);
            $ctg_id = $ctg_id->fetch_row()[0];
            $queryJUNC = "INSERT INTO ad_category (ad_id, category_id) VALUES ('$ad_id_retrieve', '$ctg_id')";
            if(!$conn->query($queryJUNC)){
                echo "Junction query failed!";
                //Izpis MYSQL napake z: echo mysqli_error($conn);
                return false;
            }
        }
        foreach ($_FILES["image"]["tmp_name"] as $photo){
            $img_file = file_get_contents($photo);
            //Pripravimo byte array za pisanje v bazo (v polje tipa LONGBLOB)
            $img_file = mysqli_real_escape_string($conn, $img_file);
            $queryIMG = "INSERT INTO images (ad_ID, img) VALUES ('$ad_id_retrieve', '$img_file');";
            if(!$conn->query($queryIMG)){
                echo "Image insertion failed!";
                //Izpis MYSQL napake z: echo mysqli_error($conn);
                return false;
            }
        }
        return true;
	}
	else{
        echo "MASSIVE FAIL!";
		//Izpis MYSQL napake z: echo mysqli_error($conn); 
		return false;
	}
	
	/*
	//Pravilneje bi bilo, da sliko shranimo na disk. Poskrbeti moramo, da so imena slik enolična. V bazo shranimo pot do slike.
	//Paziti moramo tudi na varnost: v mapi slik se ne smejo izvajati nobene scripte (če bi uporabnik naložil PHP kodo). Potrebno je urediti ustrezna dovoljenja (permissions).
		
		$imeSlike=$photo["name"]; //Pazimo, da je enolično!
		//sliko premaknemo iz začasne poti, v neko našo mapo, zaradi preglednosti
		move_uploaded_file($photo["tmp_name"], "slika/".$imeSlike);
		$pot="slika/".$imeSlike;		
		//V bazo shranimo $pot
	*/
}

$error = "";
if(isset($_POST["poslji"])){
	if(publish($_POST["title"], $_POST["description"], $_FILES["image"][0])){
		echo $_POST["title"];
        echo $_POST["description"];

        header("Location: index.php");
		die();
	}
	else{
        echo "MASSIVE FAIL!";
		$error = "Prišlo je do našpake pri objavi oglasa.";
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
            max-width: 500px;
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

        input[type=file]{
            width: 50%;
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
            opacity: 1;
        }

    </style>

	<h2 align="center">Objavi oglas</h2>
    <fieldset>
        <form action="objavi.php" method="POST" enctype="multipart/form-data">
            <label>Name</label><input type="text" name="title" required/> <br/>
            <label>Choose a category:</label>
            <select name="categories[]" multiple="multiple">
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
            <label>Content</label><textarea name="description" rows="10" cols="50"></textarea> <br/>
            <label>Image</label><input type="file" name="image[]" multiple="multiple" required/> <br/>
            <input type="submit" name="poslji" value="Publish" /> <br/>
            <label><?php echo $error; ?></label>
        </form>
    </fieldset>

<?php
include_once('noga.php');
?>