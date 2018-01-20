<?php
session_start();

unset($_SESSION['game']); //Clear the game of the session
unset($_SESSION['gameover']); //Clear the gameover of the session

require("requires/login_functions.php"); //Require the login functions

//Check if the user is logged in
//If not, redirect the user to the Login.php
if(!isLoggedIn()) { redirect_user("Login.php"); }

include("includes/header.html"); //Include the header

?>

<script>
    //Javascript function that gets the columns, rows and mines indicated by the user for the custom mode
    function chooseGrid(){
        let cols = window.prompt("Select the number of columns [9, 30]", "0");
        
        while(isNaN(cols)){
            cols = window.prompt("It must be a number. Select the number of columns [9, 30]", "0");   
        }
        
        while(cols < 9 || cols > 30){
            cols = window.prompt("Out of range. Select the number of columns [9, 30]", "0");   
        }
        
        
        let rows = window.prompt("Select the number of rows [9, 24]", "0");
        
        while(isNaN(rows)){
            rows = window.prompt("It must be a number. Select the number of rows [9, 24]", "0");   
        }
        
        while(rows < 9 || rows > 24){
            rows = window.prompt("Out of range. Select the number of rows [9, 24]", "0");   
        }
        
        const maxMines = (rows*cols-1);
        
        let mines = window.prompt(`Select the number of mines [1, ${maxMines}]`, "0");
        
        while(isNaN(mines)){
            mines = window.prompt(`It must be a number. Select the number of mines [1, ${maxMines}]`, "0");   
        }
        
        while(mines < 1 || mines > maxMines){
            mines = window.prompt(`Out of range. Select the number of mines [1, ${maxMines}]`, "0");   
        }
        
        //When we have all three values redirect the user to the Game page with the corresponding getters
        window.location.href = `./Game.php?diff=Custom&cols=${cols}&rows=${rows}&mines=${mines}`;
    }
</script>

  <div class="row">
    <div class="col-sm-6 text-center">
        <a href="Game.php?diff=Easy">
            <button class="btn btn-warning btn-lg diff-btn">
                <h1><strong>EASY MODE</strong></h1>
                <h2>10 X 10</h2>
                <h4>9 MINES</h4>
            </button>
        </a>
    </div>
    <div class="col-sm-6">
      <div class="col-sm-6 text-center">
          <a href="Game.php?diff=Medium">
            <button class="btn btn-warning btn-lg diff-btn">
                <h1><strong>MEDIUM MODE</strong></h1>
                <h2>16 X 16</h2>
                <h4>40 MINES</h4>
            </button>
          </a>
    </div>
    </div>
    </div>
   <div class="row mt-5">
    <div class="col-sm-6 text-center">
        <a href="Game.php?diff=Hard">
            <button class="btn btn-warning btn-lg diff-btn">
                <h1><strong>HARD MODE</strong></h1>
                <h2>16 X 30</h2>
                <h4>99 MINES</h4>
            </button>
        </a>
    </div>
    <div class="col-sm-6">
      <div class="col-sm-6 text-center">
        <button class="btn btn-warning btn-lg diff-btn" onclick="javascript:chooseGrid()"><!--This button calls the chooseGrid function-->
            <h1><strong>CUSTOM MODE</strong></h1>
            <h2>?? X ??</h2>
            <h4>? MINES</h4>
          </button>
    </div>
    </div>
  </div>
  <div class="row mt-5 mb-5">
    <div class="col-12 text-center">
        <a href="GameStats.php">
            <button class="btn btn-md btn-warning stats-btn"><h1><strong>GAME STATS</strong></h1></button>
        </a>
    </div>
  </div>

<?php
include("includes/footer.html"); //Include the footer
?>
