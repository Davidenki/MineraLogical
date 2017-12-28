<?php

require("requires/GridTileClasses.php");

session_start();

$grid = $_SESSION['game'];

if(isset($_POST['x']) && isset($_POST['y']) && isset($_POST['action']))
{
    if(isset($_POST['time']))
    {
        $x = $_POST['x'];
        $y = $_POST['y'];

        $action = $_POST['action'];

        $time = $_POST['time'];

        $grid->setTime($time);

        switch($action)
        {
            case 'open': $grid->CheckTile($x, $y);  break;
            case 'mark': $grid->MarkTile($x, $y); break;
            case 'double': $grid->OpenDoubleClickTiles($x, $y); break;
        }       
    }

}
else if(isset($_POST['time']))
{
    $time = $_POST['time'];
    $grid->setTime($time);
}
    
$_SESSION['game'] = $grid;

?>