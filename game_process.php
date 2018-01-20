<?php

//Require the Grid and Tile classes before start the SESSION
require("requires/GridTileClasses.php");

session_start(); //Start the SESSION

$grid = $_SESSION['game']; //Assign the game into the grid variable

//If this check passes we know that a tile has been clicked
if(isset($_POST['x']) && isset($_POST['y']) && isset($_POST['action']))
{
    //Check if the time has been posted
    if(isset($_POST['time']))
    {
        //Local variable declarations, good practice
        $x = $_POST['x'];
        $y = $_POST['y'];

        $action = $_POST['action'];

        $time = $_POST['time'];

        $grid->setTime($time);

        //Check the action and call the corresponding function
        switch($action)
        {
            case 'open': $grid->CheckTile($x, $y);  break;
            case 'mark': $grid->MarkTile($x, $y); break;
            case 'double': $grid->OpenDoubleClickTiles($x, $y); break;
        }       
    }
} //If not, check if we want to se the time
else if(isset($_POST['time']))
{
    //If passes, set the time to the grid
    $time = $_POST['time'];
    $grid->setTime($time);
}

$_SESSION['game'] = $grid; //Return the updated grid to the game SESSION

?>