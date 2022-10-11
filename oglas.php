    <?php
    include_once('glava.php');

    //Funkcija izbere oglas s podanim ID-jem. Doda tudi uporabnika, ki je objavil oglas.
    function get_ad($id){
        global $conn;
        $id = mysqli_real_escape_string($conn, $id);
        $query = "SELECT ads.*, users.username FROM ads LEFT JOIN users ON users.id = ads.user_id WHERE ads.id = $id;";
        $res = $conn->query($query);
        if($obj = $res->fetch_object()){
            return $obj;
        }
        return null;
    }

    if(!isset($_GET["id"])){
        echo "Manjkajoči parametri.";
        die();
    }
    $link = $_GET["link"];
    $id = $_GET["id"];
    $oglas = get_ad($id);
    if($oglas == null){
        echo "Oglas ne obstaja.";
        die();
    }
    //Base64 koda za sliko (hexadecimalni zapis byte-ov iz datoteke)
    //$img_data = base64_encode($oglas->image);

    global $conn;
    $queryUPDT = "UPDATE ads SET viewcount=viewcount+1 WHERE id=$id";
    $conn->query($queryUPDT);
    $queryEMAIL = "SELECT email FROM users LEFT JOIN ads ON users.id = ads.user_id WHERE ads.id = $id";
    $returnedEmail = $conn->query($queryEMAIL);
    $row = $returnedEmail->fetch_object();

    ?>

        <style>
            *{
                margin: 0;
                padding: 0;
            }
            body{

            }
            .container{
                padding-top: ;
                position: absolute;
                top: 70%;
                left: 50%;
                height: 300px;
                width: 500px;
                transform: translate(-50%, -50%);
                border: 1px solid #fff;
                overflow: hidden;
            }

            .navigator{
                display: flex;
                position: absolute;
                left: 50%;
                bottom: 20px;
                transform: translate(-50%);
            }
            .navigator .bar{
                height: 15px;
                width: 45px;
                background: #100f0f;
                cursor: pointer;
                border: 2px solid #000;
                margin-left: 8px;
                box-shadow: 2px 2px 6px #fff, -2px -2px 6px #fff;
            }
            .navigator .bar:hover{
                background: #fff;
            }

            input[type="radio"]{
                position: absolute;
                visibility: hidden;
            }

            .slides {
                display: flex;
                height: 100%;
                width: 500%;
            }

            .image{
                width: 20%;
                transition: 0.4s ease;
            }

            img{
                width: 100%;
                height: 100%;
            }

            #btn1:checked ~ .first{
                margin-left: 0;
            }
            #btn2:checked ~ .first{
                margin-left: -20%;
            }
            #btn3:checked ~ .first{
                margin-left: -40%;
            }
            #btn4:checked ~ .first{
                margin-left: -60%;
            }
            #btn5:checked ~ .first{
                margin-left: -80%;
            }


        </style>

        <div class="oglas">
            <h4><?php echo $oglas->title;?></h4>
            <p><?php echo $oglas->description;?></p>

            <div class="container">
                <div class="slides">
                                        <?php
                        $query = "SELECT images.img FROM images WHERE ad_ID='$id';";
                        $res = $conn->query($query);
                        $buttonIndex = 0;
                        $count = $res->num_rows;
                        $trigger = 0;
                        $val = 0;
                        while($count != 0){
                            $buttonIndex++;?>
                            <input type="radio" name="common" id="btn<?php echo $buttonIndex?>">
                            <?php
                        $count--;
                        }
                        while($row_data = $res->fetch_assoc()){
                            $img_data = base64_encode($row_data["img"]);
                            ?>
                                <div class="image <?php if($trigger == $val){echo "first"; $val=1;}
                                ?>">
                                    <img src="data:image/jpg;base64, <?php echo $img_data;?>" width="400"/>
                                </div>
                            <?php

                        }
                        ?>
                </div>
                <div class="navigator">
                        <?php
                        $count = $res->num_rows;
                        $buttonIndex = 0;
                        while($count != 0){
                            $buttonIndex++;
                            echo "<label for=\"btn$buttonIndex\" class=\"bar\">";
                            echo "</label>";
                            $count--;
                        }
                    ?>
                </div>
            </div>

            <p>Published by: <?php echo $oglas->username; ?></p>
            <p>Email contact: <?php echo $row->email; ?></p>
            <a href="<?php echo $link; ?>"><button>Back</button></a>
        </div>
        <hr/>
        <?php

    include_once('noga.php');
    ?>