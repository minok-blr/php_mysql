<?php
include_once('glava.php');

// Funkcija prebere oglase iz baze in vrne polje objektov
function get_oglasi(){
	global $conn;
    $query = "";
    $timestamp = date('Y-m-d H:i:s');
	$query = "";
    if(isset($_POST["searchfunc"])){
        $searchstring = $_POST["searchfunc"];
        $query = "SELECT * FROM ads WHERE title LIKE '%$searchstring%' OR description LIKE '%$searchstring%' ORDER BY expdate DESC;";
    }
    else if(isset($_POST["categories"]) && isset($_POST["hideexpads"])){
        $cat = $_POST["categories"];
        $query = "SELECT * FROM ads WHERE category='$cat' AND expdate >='$timestamp' ORDER BY expdate DESC";
    }
    else if(isset($_POST["categories"])){
        $masterQuery = "SELECT ads.* FROM ads LEFT JOIN ad_category ON ad_category.ad_id = ads.id WHERE ad_category.category_id=";
        $count = count($_POST["categories"]);
        //echo $count;
        $iter = 0;
        foreach($_POST["categories"] as $cat){
            $query = "SELECT id FROM categories WHERE name='$cat';";
            $res = $conn->query($query);
            $res = $res->fetch_assoc();
            $masterQuery .= $res["id"];
            $iter++;
            if($iter==$count){
                $masterQuery .= " GROUP BY ads.id;";
            }
            else {
                $masterQuery .= " OR ad_category.category_id=";
            }

        }
        //echo  $masterQuery;
//        $cat = $_POST["categories"];
//        echo $cat;
//        $query = "SELECT * FROM ads WHERE category='$cat' ORDER BY expdate DESC";
        $query = $masterQuery;
    }
    else if(isset($_POST["hideexpads"])){
        $query = "SELECT * FROM ads WHERE expdate >='$timestamp' ORDER BY expdate DESC";
    }
    else {
        $query = "SELECT * FROM ads ORDER BY expdate DESC;";
    }
    $res = $conn->query($query);
	$oglasi = array();

	while($oglas = $res->fetch_object()){
        //echo $oglas->id;
		array_push($oglasi, $oglas);
	}
	return $oglasi;
}

//Preberi oglase iz baze
$oglasi = get_oglasi();

//Izpiši oglase
//Doda link z GET parametrom id na oglasi.php za gumb 'Preberi več'

?>

<style>
    .oglas {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-gap: 20px;
        margin: auto;
        width: 50%;
        box-shadow: 5px 10px #808080;
        background-color: #e8e2bc;
        border: 1px solid;
        padding: 0 0 20 20;
    }

    .header {
        margin: auto;
        position: center;
    }

    .extra-options {
        display: inline;
        margin: auto;
        padding: 0 20px 0 0;
    }

    .image-preview {
        display: inline-grid;
        resize: inherit;
        overflow: auto;
        padding: 20px;
        margin: auto;
        height: 100%;
        width: 100%;
    }
</style>

<div class="ads-container">
    <?php
    foreach($oglasi as $oglas){

        $queryCat = "SELECT name FROM categories LEFT JOIN ad_category ON ad_category.category_id = categories.id WHERE ad_category.ad_id = '$oglas->id';";
        //echo $queryCat;
        $res = $conn->query($queryCat);
        $catnames = "";
        while($row = $res->fetch_assoc()){
            $catnames .= " ". $row["name"];
        }
        //echo $catnames;
	?>
	<div class="oglas">
        <div class="child">
            <div class="header">
                <h4 class="ad-title"><?php echo $oglas->title;?></h4>
                <p class="ad-info"><?php echo $oglas->description;?></p>
                <!--<p class="date-uploaded"><?php echo $oglas->uploaddate;?></p>-->
                <p class="ad-category">Category:<?php echo $catnames;?></p>
                <p class="date-uploaded">Uploaded on: <?php echo date("Y-m-d", strtotime($oglas->uploaddate));?></p>
                <p class="date-expiry">Expiring on: <?php echo date("Y-m-d", strtotime($oglas->expdate));?></p>
                <p class="numofviews">Viewcount: <?php echo $oglas->viewcount;?></p>
            </div>
        </div>

        <div class="child">
            <div class="image-preview">
                <?php
                    if($oglas->image == '0')
                    {
                        $query = "SELECT img FROM images WHERE ad_ID=$oglas->id LIMIT 1;";
                        $res = $conn->query($query);
                        $res = $res->fetch_row();
                        $img_data = base64_encode($res[0]);
                    }
                    else{
                        $img_data = base64_encode($oglas->image);
                    }
                ?>
                    <a href="oglas.php?id=<?php echo "$oglas->id&link=index.php";?>">
                        <img src="data:image/jpg;base64, <?php echo $img_data;?>" height="180" width="200" style="object-fit: contain"/>
                    </a>
            </div>
        </div>
	</div>
	<hr/>
	<?php
}
?>
</div>

<?php
include_once('noga.php');
?>