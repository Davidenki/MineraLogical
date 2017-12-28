<?php

require('requires/mysqli_connect.php');
require("requires/GridTileClasses.php");

session_start();

if(isset($_POST['diff']) && isset($_POST['time']))
{
    $grid = $_SESSION['game'];

    $time = $_POST['time'];
    $grid->setTime($time);

    $dataSerialized = serialize($grid);
    $dataEncoded = base64_encode($dataSerialized);


    $q = "SELECT GameID FROM savedgames WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_POST['diff']."';";
    $r = mysqli_query($dbc, $q);
    if(mysqli_num_rows($r) === 1)
    {
        $q = "UPDATE savedgames SET Data = '".$dataEncoded."' WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_POST['diff']."';";
        $r = mysqli_query($dbc, $q);
    }
    else
    {
        $q = "SELECT GameID FROM savedgames ORDER BY GameID DESC LIMIT 1;";
        $r = mysqli_query($dbc, $q);
        if(mysqli_num_rows($r) === 1)
        {
            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
            $nextGameID = $row['GameID'];
            $nextGameID += 1;
        }
        else
        {
            $nextGameID = 1;
        }

        $q = "INSERT INTO savedgames (GameID, Difficulty, Data, UserID) VALUES (".$nextGameID.", '".$_POST['diff']."', '".$dataEncoded."', ".$_SESSION['user_id'].");";
        $r = mysqli_query($dbc, $q);    
    }

    if($r)
    {
        echo "Your game was saved"; 
    }
    else
    {
        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
    }
}

?>