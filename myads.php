<?php
include_once ("glava.php");

function deleteAd($ad_id){
    global $conn;
    $query = "DELETE FROM ads WHERE id=$ad_id";
    $res = $conn->query($query);
}

function extendDate($ad_id){
    global $conn;
    $queryGET = "SELECT expdate FROM ads WHERE id=$ad_id";
    $res = $conn->query($queryGET);
    $val = $res->fetch_array();

    $expdate = $val['expdate'];
    echo $expdate;
    $newexpdate = date('Y-m-d H:i:s', strtotime("+30 days", strtotime($expdate)));
    echo $newexpdate;
    $query = "UPDATE ads SET expdate='$newexpdate' WHERE id=$ad_id";
    $res = $conn->query($query);

}

function getUserAds(){
    global $conn;
    $user_id = $_SESSION["USER_ID"];
    $query = "";
    $timestamp = date('Y-m-d H:i:s');
    if(isset($_POST["hideexpads"])){
        $query = "SELECT * FROM ads WHERE user_id='$user_id' AND expdate >='$timestamp'  ORDER BY expdate DESC";
    }
    else {
        $query = "SELECT * FROM ads WHERE user_id='$user_id' ORDER BY expdate DESC";
    }
    $res = $conn->query($query);
    $ads = array();
    while($ad = $res->fetch_object()){
        /** @noinspection PhpArrayPushWithOneElementInspection */
        array_push($ads, $ad);
    }
    return $ads;
}

$ads = getUserAds();

if(isset($_POST["extensionButton"])){
    extendDate($_POST["id"]);
    header("Location: myads.php");
}
else {
    $error = "Deadline extension failed!";
}

if(isset($_POST["deleteAd"])){
    deleteAd($_POST["id"]);
    header("Location: myads.php");
}
else {
    $error = "Deletion of ad failed!";
}

?>

<style>
    .oglas {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-gap: 20px;
        margin: auto;
        width: 50%;
        box-shadow: 5px 10px #808080;
        border: 1px solid;
        padding: 0 0 20px 20px;
        background-color: #e3dcc8;

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
        padding: 20px;
        margin: auto;
        height: 100px;
        width: 100px;
    }
</style>

<div class="ads-container">
    <?php
        foreach ($ads as $oglas){
    ?>
        <div class="oglas">
            <div class="child">
                <div class="header">
                    <h4 class="ad-title"><?php echo $oglas->title;?></h4>

                    <!--<p class="ad-info"><?php echo $oglas->description;?></p>
                    <p class="date-uploaded"><?php echo $oglas->uploaddate;?></p>
                    <p class="ad-category">Category:<?php echo $oglas->category;?></p>
                    <p class="date-uploaded">Uploaded on: <?php echo date("Y-m-d", strtotime($oglas->uploaddate));?></p>
                    <p class="date-expiry">Expiring on: <?php echo date("Y-m-d", strtotime($oglas->expdate));?></p>
                    <p class="numofviews">Viewcount: <?php echo $oglas->viewcount;?></p>-->
                </div>

                <div class="extra-options">
                    <form action="myads.php" method="post">
                        <input type="submit" name="extensionButton" value="Extend by 30 days">
                        <input type="submit" name="deleteAd" value="Delete ad">
                        <input type="hidden" name="id" value="<?php echo $oglas->id;?>">
                    </form>

                </div>
            </div>

            <div class="child">
                <div class="image-preview">
                    <?php
                    $img_data = base64_encode($oglas->image);
                    ?>
                    <img src="data:image/jpg;base64, <?php echo $img_data;?>" alt="Ad photo" height="100" width="200" style="object-fit: contain"/>
                    <a href="oglas.php?id=<?php echo "$oglas->id&link=myads.php";?>"><button>See ad</button></a>
                </div>
            </div>

        </div>
        <hr/>

    <?php
        }
    ?>
</div>