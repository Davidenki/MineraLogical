<?php
session_start();

unset($_SESSION['game']);
unset($_SESSION['gameover']);

require("requires/login_functions.php");

if(!isLoggedIn()) { redirect_user("Login.php"); }

//echo $_SESSION['user_id']."<br />";
//echo $_SESSION['username']."<br />";

include("includes/header.html");

?>

<div class="row">
    <div class="col-12 col-md-6">
        <div class="game-mode">
            <button type="button" class="btn" data-toggle="collapse" data-target="#difficulties">Normal Mode</button>
            <div class="collapse" id="difficulties">
                <div class="btn-group-vertical">
                  <a href="Game.php?diff=Easy&cols=9&rows=9&mines=10" class="btn btn-primary">Easy Mode</a>
                  <a href="Game.php?diff=Medium&cols=16&rows=16&mines=40" class="btn btn-primary">Medium Mode</a>
                  <a href="Game.php?diff=Hard&cols=30&rows=16&mines=99" class="btn btn-primary">Hard Mode</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="game-mode">
            <button type="button" class="btn"><a href="#" >Time Trial Mode</a></button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-6">
        <div class="game-mode">
            <button type="button" class="btn"><a href="#" >1V1 Mode</a></button>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="game-mode">
            <button type="button" class="btn"><a href="#" >Creator Mode</a></button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col text-center">
        <button type="button" class="btn"><a href="GameStats.php">MineraLogical Stats</a></button>
    </div>
</div>

<?php
include("includes/footer.html");
?>
