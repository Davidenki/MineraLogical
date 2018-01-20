<?php

require('requires/mysqli_connect.php'); //Connect to the database
require("requires/GridTileClasses.php"); //Require the Grid an Tile classes
require("requires/login_functions.php"); //Require the login functions

session_start(); //Start the SESSION

//Check if the user is logged in
//If not, redirect the user to the Login.php
if(!isLoggedIn()) { redirect_user(); }

?>

<!--I do not include the header because this page is totally different-->
<html>
    <head>
        <title>Game</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="Styles/GameStyles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
        <!-- If I do not put this style here it does not work, I do not really know why-->
        <style>
            th {
                background-color: black;
                color: white;
            }
        </style>
    </head>
    <body>
        <?php
        
        //If there is no game in the SESSION array, it means that we are starting a new game
        if(!isset($_SESSION['game']))
        {
            //Check if the user has a saved game with the difficulty selected
            $q = "SELECT Data FROM savedgames WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_GET['diff']."';";
            $r = mysqli_query($dbc, $q);
            
            //If it is a saved game, we need to unserialize the field and assign the object to the grid variable
            if(mysqli_num_rows($r) == 1)
            {
                $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                $dataEncoded = $row['Data'];
                $dataSerialized = base64_decode($dataEncoded);
                $grid = unserialize($dataSerialized);
            }
            else
            {
                //If it is not a saved game, we need to create a new one
                //We are going to create a different grid with different number of mines depending of the difficulty selecter
                switch($_GET['diff'])
                {
                    case 'Easy':
                        $cols = 9;
                        $rows = 9;
                        $mines = 10;
                        break;
                    case 'Medium':
                        $cols = 16;
                        $rows = 16;
                        $mines = 40;
                        break;
                    case 'Hard':
                        $cols = 30;
                        $rows = 16;
                        $mines = 99;
                        break;
                    case 'Custom':
                        //This is the unique case that we need to GET the cols, rows and mines specified by the user
                        $cols = $_GET['cols'];
                        $rows = $_GET['rows'];
                        $mines = $_GET['mines'];                        
                        break;
                }
                
                //When we are done, create a new grid and assign it to the grid variable
                $grid = new Grid($cols, $rows, $mines);
            }
            
            //Assign the grid into the SESSION game key and the gameover key to false
            $_SESSION['game'] = $grid;
            $_SESSION['gameover'] = false;
        }
        else
        {
            //If there is a game in the SESSION array, assign it to the grid variable
            $grid = $_SESSION['game'];
        }
        
        //Check if we won the game
        if($grid->CheckWin())
        {
            //Show a winning message
            echo '<div>YOU WIN<br /><div id="gameContainer">'.$grid->Show().'<div><a href="MainMenu.php">Return to Main Menu</a></div>';
            //Check if the game was already over, we do not want to update the wins if we already did that
            if($_SESSION['gameover'] === false)
            {
                //Check if the difficulty is custom because this difficulty does not have scores
                if($_GET['diff'] !== "Custom")
                {
                    //Query to add one to the winning streak of the user
                    $q = "UPDATE ".$_GET['diff']."Scores SET Streak = Streak+1 WHERE UserID = ".$_SESSION['user_id'];
                    $r = mysqli_query($dbc, $q); //Run the query
                    if($r) //Check if it ran okay
                    {
                        //Query to get the best time of the user in this difficulty
                        $q = "SELECT BestTime FROM ".$_GET['diff']."Scores WHERE UserID = ".$_SESSION['user_id'];
                        $r = mysqli_query($dbc, $q); //Run the query
                        if(mysqli_num_rows($r) == 1) //Check if we got a row out of the select
                        {
                            $row = mysqli_fetch_array($r, MYSQLI_ASSOC); //Assign the row to a variable
                            
                            //Check if the best time of the user is null OR the time of this game was less than the current best time
                            if($row['BestTime'] == null || $grid->getTime() < $row['BestTime'])
                            {
                                //If the check passes, update the current best time of the user in this difficulty
                                $q = "UPDATE ".$_GET['diff']."Scores SET BestTime = ".$grid->getTime()." WHERE UserID = ".$_SESSION['user_id'];
                                $r = mysqli_query($dbc, $q); //Run the query
                                if(!$r)
                                {
                                    //If it did not run okay show an error message
                                    echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';

                                }
                            }
                            
                            //Go to the function gameover
                            gameOver($dbc, "Wins", $grid->getTilesOpened()); 
                        }
                        else
                        {
                            //If not show an error message
                            echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                        }
                    }
                    else
                    {
                        //If not show an error message
                        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                    }
                }
                else
                {
                    //If it was a custom game, just set the gameover session value to true
                    $_SESSION['gameover'] = true;
                }
            }
        }//If we did not win, check if we lost instead
        else if($grid->Lost())
        {
            //Show a message saying that we lost
            echo '<div>YOU LOST<br /><div id="gameContainer">'.$grid->Show().'<div><a href="MainMenu.php">Return to Main Menu</a></div>';
            //Check if the game was already over, we do not want to update the wins if we already did that
            if($_SESSION['gameover'] === false)
            {
                //Check if the difficulty is custom because this difficulty does not have scores
                if($_GET['diff'] !== "Custom")
                {
                    //Query to set the winning streak of the user to 0
                    $q = "UPDATE ".$_GET['diff']."Scores SET Streak = 0 WHERE UserID = ".$_SESSION['user_id'];
                    $r = mysqli_query($dbc, $q); //Run the query
                    if($r)
                    {
                        //If it ran okay, go to the gameOver function
                        gameOver($dbc, "Loses", $grid->getTilesOpened());
                    }
                    else
                    {
                        //If not, show a error message
                        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                    }
                }
                else
                {
                    //If it was a custom game, just set the gameover session value to true
                    $_SESSION['gameover'] = true;
                }
            }
        }
        else
        {
            //If we have not won or lost, just show the game grid
            echo '<div id="gameContainer">'.$grid->Show().'</div><div id="save"><button onclick="javascript:saveGame()">Save Game</button></div>'; 
        }
        
        
        function gameOver($dbc, $result, $tilesOpened)
        {
            //Check if the difficulty is custom because this difficulty does not have scores
            if($_GET['diff'] != 'Custom')
            {
                //Query that adds one to the wins or loses of the corresponding difficulty
                $q = "UPDATE ".$_GET['diff']."scores SET ".$result." = ".$result."+1 WHERE UserID = ".$_SESSION['user_id'].";";
                $r = mysqli_multi_query ($dbc, $q); //Run the query
                if($r) //Check if it ran okay
                {
                    //Query that adds the tiles opened in the game of the corresponding difficulty
                    $q = "UPDATE ".$_GET['diff']."Scores SET TilesOpened = TilesOpened+".$tilesOpened.";";
                    $r = mysqli_query($dbc, $q); //Run the query
                    if($r) //Check if it ran okay
                    {
                        //Query that checks if this game was saved before finishing it
                        $q = "SELECT GameID FROM savedgames WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_GET['diff']."';";
                        $r = mysqli_query ($dbc, $q); //Run the query
                        if(mysqli_num_rows($r) == 1)
                        {
                            //If we find it assign it to the row variable
                            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                            //Query that will delete the saved game
                            $q = "DELETE FROM savedgames WHERE GameID = ".$row['GameID'].";";
                            $r = mysqli_query($dbc, $q); //Run the query
                            if(!$r)
                            {
                                //If it did not run okay, show an error message
                                echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>'; 
                            }
                        }                 
                    }
                    else
                    {
                        //If it did not run okay, show an error message
                        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                    }
                    
                    //Finally, set the gameover of the session array to true
                    $_SESSION['gameover'] = true;
                }
                else
                {
                    //If it did not run okay, show an error message
                    echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                }
            }
        }
        
        ?>
        
        <script type="text/javascript">
            
            //Get the time of the grid
            let totalSeconds = <?php echo $grid->getTime(); ?>;
            Timer();
            
            //If the game is no over make the 1 second interval for the timer
            <?php if($_SESSION['gameover'] === false) { echo 'setInterval(Timer, 1000);'; } ?>
            
            //Function that adds one to the totalseconds and shows it to the screen
            function Timer(){
                totalSeconds <?php echo $grid->getTimeTrial(); ?>= 1;
                const hours = timeToString(Math.floor(totalSeconds/3600));
                const minutes = timeToString(Math.floor(totalSeconds/60));
                const seconds = timeToString(Math.floor(totalSeconds%60));
                $("#timer").html("Time: " + hours + ":" + minutes + ":" + seconds);
            }
            
            //Little function to convert the value (hours, minutes, seconds) to a string with two characters
            function timeToString(value)
            {
                const valStr = value.toString();

                if(valStr.length < 2) return "0" + valStr;
                else                  return valStr;
            }
            
            
            //This is a function that makes a parameter with the corresponding properties to do the mark tile process
            //After that calls another function that will do the AJAX call
            function performMarkTileProcess(event, x, y){
                <?php if($_SESSION['gameover']) { echo "return;"; } ?>
                if(event.which == 3)// WE NEED TO ENSURE THAT THE USER RIGHT CLICKED
                {
                   const param = {
                        "x" : x,
                        "y" : y,
                        "action" : 'mark',
                        "time" : totalSeconds
                    };

                    performPOSTProcess(param); 
                }
            }
 
            //This is a function that makes a parameter with the corresponding properties to do the open tile process
            //After that calls another function that will do the AJAX call
            function performOpenTileProcess(event, x, y){
                <?php if($_SESSION['gameover']) { echo "return;"; } ?>
                if(event.which == 1)//(THE USER LEFT CLICKED)
                {
                    const param = {
                        "x" : x,
                        "y" : y,
                        "action" : 'open',
                        "time" : totalSeconds
                    };

                    performPOSTProcess(param);
                }
                else if(event.which == 3) //(THE USER RIGHT CLICKED)
                {
                    performMarkTileProcess(event, x, y);
                }
            }
            
            //This is a function that makes a parameter with the corresponding properties to do the open tile process with the double click
            //After that calls another function that will do the AJAX call
            function performOpenNearProcess(x, y)
            {
                <?php if($_SESSION['gameover']) { echo "return;"; } ?>
                const param = {
                        "x" : x,
                        "y" : y,
                        "action" : 'double',
                        "time" : totalSeconds
                };
                
                performPOSTProcess(param);
            }
            
            //This is the function that does the AJAX call to the game_process.php file with the parameter that we made before
            //After that call we refresh the page to simulate a real POST action
            function performPOSTProcess(param)
            {
                $.ajax({
                    data:  param,
                    url:   'game_process.php',
                    type:  'post',
                    success:  function (response) { location.reload(); }
                });
            }
            
            //This is the function that does the AJAX call to the save_game.php file
            //After that call we show a message with the response of the called file          
            function saveGame()
            {
                $.ajax({
                    data:  {
                        "diff" : '<?php echo $_GET['diff']; ?>',
                        "time" : totalSeconds
                    },
                    url:   'save_game.php',
                    type:  'post',
                    success:  function (response) {
                        document.getElementById("save").innerHTML += response;
                    }
                });    
            }

            //Just to make the right click not show the context menu of the browser
            window.oncontextmenu = function() { return false; };
            
            //If the user refreshes the page we need to update the seconds of the grid
            //So this function will do that after the page unloads
            window.onbeforeunload = function()
            {
                $.ajax({
                    data:  {
                        "time" : totalSeconds
                    },
                    url:   'game_process.php',
                    type:  'post'
                });   
            };
        </script>
    </body>
</html>