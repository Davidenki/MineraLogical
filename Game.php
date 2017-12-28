<html>
    <head>
        <title>Game</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="Styles/GameStyles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        
        <style>
            th {
                background-color: black;
                color: white;
            }
        </style>
    </head>
    <body>
        <?php
        
        require('requires/mysqli_connect.php');
        require("requires/GridTileClasses.php");
        require("requires/login_functions.php");
        
        session_start();

        if(!isLoggedIn()) { redirect_user("Login.php"); }
        
        
        if(!isset($_SESSION['game']))
        {
            $q = "SELECT Data FROM savedgames WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_GET['diff']."';";
            $r = mysqli_query($dbc, $q);
            if(mysqli_num_rows($r) == 1)
            {
                $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                $dataEncoded = $row['Data'];
                $dataSerialized = base64_decode($dataEncoded);
                $grid = unserialize($dataSerialized);
            }
            else
            {
                $cols = $_GET['cols'];
                $rows = $_GET['rows'];
                $mines = $_GET['mines'];
                $grid = new Grid($cols, $rows, $mines); 
            }
            
            $_SESSION['game'] = $grid;
            $_SESSION['gameover'] = false;
        }
        else
        {
            $grid = $_SESSION['game'];
        }
        
        if($grid->CheckWin())
        {
            echo '<div>YOU WIN<br /><div id="gameContainer">'.$grid->Show().'<div><a href="MainMenu.php">Return to Main Menu</a></div>';

            if($_SESSION['gameover'] === false)
            {
                $q = "UPDATE ".$_GET['diff']."Scores SET Streak = Streak+1 WHERE UserID = ".$_SESSION['user_id'];
                $r = mysqli_query($dbc, $q);
                if($r)
                {
                    $q = "UPDATE MiscScores SET Streak = Streak+1 WHERE UserID = ".$_SESSION['user_id'];
                    $r = mysqli_query($dbc, $q);
                    if($r)
                    {
                        $q = "SELECT BestTime FROM ".$_GET['diff']."Scores WHERE UserID = ".$_SESSION['user_id'];
                        $r = mysqli_query($dbc, $q);
                        if(mysqli_num_rows($r) == 1)
                        {
                            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);

                            if($row['BestTime'] == null || $grid->getTime() < $row['BestTime'])
                            {
                                $q = "UPDATE ".$_GET['diff']."Scores SET BestTime = ".$grid->getTime()." WHERE UserID = ".$_SESSION['user_id'];
                                $r = mysqli_query($dbc, $q);
                                if($r)
                                {
                                    gameOver($dbc, "Wins", $grid->getTilesOpened()); 
                                }
                                else
                                {
                                    echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                                }
                            } 
                        }
                        else
                        {
                            echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                        }
                    }
                    else
                    {
                        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                    }
                }
                else
                {
                    echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                }
            }
        }
        else if($grid->Lost())
        {
            echo '<div>YOU LOST<br /><div id="gameContainer">'.$grid->Show().'<div><a href="MainMenu.php">Return to Main Menu</a></div>';
            if($_SESSION['gameover'] === false)
            {
                $q = "UPDATE ".$_GET['diff']."Scores SET Streak = 0 WHERE UserID = ".$_SESSION['user_id'];
                $r = mysqli_query($dbc, $q);
                if($r)
                {
                    $q = "UPDATE MiscScores SET Streak = 0 WHERE UserID = ".$_SESSION['user_id'];
                    $r = mysqli_query($dbc, $q);
                    if($r)
                    {
                        gameOver($dbc, "Loses", $grid->getTilesOpened());
                    }
                    else
                    {
                        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                    }
                }
                else
                {
                    echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                }
            }
        }
        else
        {
            echo '<div id="gameContainer">'.$grid->Show().'</div><div id="save"><button onclick="javascript:saveGame()">Save Game</button></div>'; 
        }
        
        
        function gameOver($dbc, $result, $tilesOpened)
        {
            if($_GET['diff'] != 'Custom')
            {
                $q = "UPDATE ".$_GET['diff']."scores SET ".$result." = ".$result."+1 WHERE UserID = ".$_SESSION['user_id'].";";
                $r = mysqli_multi_query ($dbc, $q);
                if($r)
                {
                    $q = "UPDATE ".$_GET['diff']."Scores SET TilesOpened = TilesOpened+".$tilesOpened.";";
                    $r = mysqli_query($dbc, $q);
                    if($r)
                    {
                        $q = "SELECT GameID FROM savedgames WHERE UserID = ".$_SESSION['user_id']." AND Difficulty = '".$_GET['diff']."';";
                        $r = mysqli_query ($dbc, $q);

                        if(mysqli_num_rows($r) == 1)
                        {
                            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
                            $q = "DELETE FROM savedgames WHERE GameID = ".$row['GameID'].";";
                            $r = mysqli_query($dbc, $q);
                            if(!$r)
                            {
                                echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>'; 
                            }
                        }                 
                    }
                    else
                    {
                        echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                    }
                    
                    $_SESSION['gameover'] = true;
                }
                else
                {
                    echo '<p>'.mysqli_error($dbc).'<br /><br />Query: '.$q.'</p>';
                }
            }
        }
        
        ?>
        
        <script type="text/javascript">
            
            let totalSeconds = <?php echo $grid->getTime()-1; ?>;
            Timer();
            <?php if($_SESSION['gameover'] === false) { echo 'setInterval(Timer, 1000);'; } ?>
            
            function Timer(){
                totalSeconds += 1;
                const hours = timeToString(Math.floor(totalSeconds/3600));
                const minutes = timeToString(Math.floor(totalSeconds/60));
                const seconds = timeToString(Math.floor(totalSeconds%60));
                $("#timer").html("Time: " + hours + ":" + minutes + ":" + seconds);
            }
            
            function timeToString(value)
            {
                const valStr = value.toString();

                if(valStr.length < 2) return "0" + valStr;
                else                  return valStr;
            }
            
            
            
            function performMarkTileProcess(event, x, y){
                <?php if($_SESSION['gameover']) { echo "return;"; } ?>
                if(event.which == 3)
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
            
            function performOpenTileProcess(event, x, y){
                <?php if($_SESSION['gameover']) { echo "return;"; } ?>
                if(event.which == 1)
                {
                    const param = {
                        "x" : x,
                        "y" : y,
                        "action" : 'open',
                        "time" : totalSeconds
                    };

                    performPOSTProcess(param);
                }
                else if(event.which == 3)
                {
                    performMarkTileProcess(event, x, y);
                }
            }
            
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
            
            function performPOSTProcess(param)
            {
                $.ajax({
                    data:  param,
                    url:   'game_process.php',
                    type:  'post',
                    success:  function (response) {
                        //$("#gameContainer").html(response);
                        location.reload();
                    }
                });
            }
            
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

            window.oncontextmenu = function() { return false; };
            
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