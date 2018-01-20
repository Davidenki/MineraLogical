<?php

require('requires/mysqli_connect.php'); //Connect to the database

//Require the Grid and Tile classes before start the SESSION
require("requires/GridTileClasses.php");

session_start(); //Start the session

//Check if diff and time are posted
if(isset($_POST['diff']) && isset($_POST['time']))
{
    //Get the grid
    $grid = $_SESSION['game'];

    //Get the time and set it to the grid
    $time = $_POST['time'];
    $grid->setTime($time);
    
    //Serialize and encode the grid object, so we can insert it into the database in a BLOB type field
    $dataSerialized = serialize($grid);
    $dataEncoded = base64_encode($dataSerialized);


    //Check first if the user already saved a game
    $q = "SELECT GameID FROM savedgames WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_POST['diff']."';";
    $r = mysqli_query($dbc, $q);
    if(mysqli_num_rows($r) === 1)
    {
        //If it exists, overwrite it
        $q = "UPDATE savedgames SET Data = '".$dataEncoded."' WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_POST['diff']."';";
        $r = mysqli_query($dbc, $q);
    }
    else //If not, let's insert a new saved game in the database
    {
        //This block of code is for getting the next id after the last game saved in the database
        $q = "SELECT GameID FROM savedgames ORDER BY GameID DESC LIMIT 1;";
        $r = mysqli_query($dbc, $q);
        if(mysqli_num_rows($r) === 1)
        {
            //If we found the last game get its ID and add 1 to it
            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
            $nextGameID = $row['GameID'];
            $nextGameID += 1;
        }
        else
        {
            //If we did not found a game it means there is no saved games in the database
            //So the nex ID will be the first one
            $nextGameID = 1;
        }

        //Insert the game into the database with all the corresponding values
        $q = "INSERT INTO savedgames (GameID, Difficulty, Data, UserID) VALUES (".$nextGameID.", '".$_POST['diff']."', '".$dataEncoded."', ".$_SESSION['user_id'].");";
        $r = mysqli_query($dbc, $q);   
    }

    if($r) //If all ran okey return an OKAY message
    {
        echo "Your game was saved"; 
    }
    else //If not return an error message
    {
        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
    }
}

?>