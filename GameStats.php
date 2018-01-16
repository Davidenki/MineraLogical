<?php
session_start();

require("requires/login_functions.php");

if(!isLoggedIn()) { redirect_user("Login.php"); }

include("includes/header.html");



?>
<div id="accordionOne">

  <div class="card">
    <div class="card-header text-center">
      <a class="card-link" data-toggle="collapse" data-parent="#accordionOne" href="#collapseOne">
        NORMAL MODE STATS
      </a>
    </div>
    <div id="collapseOne" class="collapse show">
      <div class="card-body">
          
          <div id="accordion1-1">
              
              <div class="card">
                <div class="card-header text-center">
                  <a class="card-link" data-toggle="collapse" data-parent="#accordion1-1" href="#collapse1-1">
                    ALL TIME
                  </a>
                </div>
                <div id="collapse1-1" class="collapse show">
                  <div class="card-body">
                      <div id="carousel1-1" class="carousel slide" data-interval="false" data-ride="carousel">
                          <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active">
                                <canvas id="myChart1-1"></canvas>
                            </div>
                            <div class="carousel-item">
                                <canvas id="myChart1-2"></canvas>  
                            </div>
                            <div class="carousel-item">
                                <canvas id="myChart1-3"></canvas> 
                            </div>
                          </div>
                          <a class="carousel-control-prev" href="#carousel1-1" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          </a>
                          <a class="carousel-control-next" href="#carousel1-1" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          </a>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="card">
                <div class="card-header text-center">
                  <a class="collapsed card-link" data-toggle="collapse" data-parent="#accordion1-1" href="#collapse1-2">
                    USERS
                  </a>
                </div>
                <div id="collapse1-2" class="collapse">
                  <div class="card-body">
                      
                    <div id="carousel1-2" class="carousel slide" data-interval="false" data-ride="carousel">
                      <div class="carousel-inner" role="listbox">
                        <div class="carousel-item active">
                            <canvas id="myChart1-4"></canvas>
                        </div>
                        <div class="carousel-item">
                            <canvas id="myChart1-5"></canvas>  
                        </div>
                        <div class="carousel-item">
                            <canvas id="myChart1-6"></canvas> 
                        </div>
                      </div>
                      <a class="carousel-control-prev" href="#carousel1-2" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      </a>
                      <a class="carousel-control-next" href="#carousel1-2" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      </a>
                </div>  
                      
              </div>
            </div>
          </div>
              
        </div>
          
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header text-center">
      <a class="collapsed card-link" data-toggle="collapse" data-parent="#accordionOne" href="#collapseTwo">
        Collapsible Group Item #2
      </a>
    </div>
    <div id="collapseTwo" class="collapse">
      <div class="card-body">
        <canvas id="myChart2"></canvas>      
      </div>
    </div>
  </div>
    
</div>
<div>
<a href="MainMenu.php">Return to Main Menu</a>
</div>

<!--<div class="chart-container" style="position: relative; height:40vh; width:50vw">
    <canvas id="myChart"></canvas>
    <canvas id="myChart2"></canvas>
</div>-->

<?php

require('requires/mysqli_connect.php');

$q = "SELECT ES.Wins AS EasyWins, ES.Loses AS EasyLoses, (ES.Wins+ES.Loses) AS TotalEasy,
             MS.Wins AS MediumWins, MS.Loses AS MediumLoses, (MS.Wins+MS.Loses) AS TotalMedium,
             HS.Wins AS HardWins, HS.Loses AS HardLoses, (HS.Wins+HS.Loses) AS TotalHard,
             (ES.Wins+MS.Wins+hS.Wins) AS TotalWins,
             (ES.Loses+MS.Loses+hS.Loses) AS TotalLoses
        FROM (((Users AS U INNER JOIN EasyScores AS ES ON U.UserID = ES.UserID)
			INNER JOIN MediumScores AS MS ON U.UserID = MS.UserID)
				INNER JOIN HardScores AS HS ON U.UserID = HS.UserID)
					INNER JOIN MiscScores AS MSC ON U.UserID = MSC.UserID
        WHERE U.UserID = ".$_SESSION['user_id'].";";

$r = mysqli_query($dbc, $q);
if(mysqli_num_rows($r) === 1)
{
    $rowWinsLoses = mysqli_fetch_array($r, MYSQLI_ASSOC);
}

?>

<script type="text/javascript">
    Chart.defaults.global.defaultFontFamily = "sans-serif";
    
    
    //WINS/LOSES ALL TIME CHART
    const ctx11 = document.getElementById("myChart1-1").getContext("2d");
    const myChart11 = new Chart(ctx11, {
        type: 'bar',
        data: {
            labels: ["Easy", "Medium", "Hard", "All"],
            datasets: [{
                label: 'Wins',
                data: [<?php if(isset($rowWinsLoses)) { echo $rowWinsLoses['EasyWins'].", ".$rowWinsLoses['MediumWins'].", ".$rowWinsLoses['HardWins'].", ".$rowWinsLoses['TotalWins']; } ?>],
                backgroundColor: 'rgba(51, 204, 51, 0.2)',
                borderColor: 'rgba(51, 204, 51, 1)',
                borderWidth: 1
            }, {
                label: 'Loses',
                data: [<?php if(isset($rowWinsLoses)) { echo $rowWinsLoses['EasyLoses'].", ".$rowWinsLoses['MediumLoses'].", ".$rowWinsLoses['HardLoses'].", ".$rowWinsLoses['TotalLoses']; } ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Total',
                data: [<?php if(isset($rowWinsLoses)) { echo $rowWinsLoses['TotalEasy'].", ".$rowWinsLoses['TotalMedium'].", ".$rowWinsLoses['TotalHard'].", ".($rowWinsLoses['TotalWins']+$rowWinsLoses['TotalLoses']); } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.4)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1                
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        fontStyle: 'bold'
                    }                     
                }]
            },
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 20,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: true,
                text: "WINS/LOSES"
            }
        }
    });
    
<?php
    
$q = "SELECT ES.Streak AS EasyStreak, MS.Streak AS MediumStreak, HS.Streak AS HardStreak, MSC.Streak AS AllStreak
        FROM (((Users AS U INNER JOIN EasyScores AS ES ON U.UserID = ES.UserID)
			INNER JOIN MediumScores AS MS ON U.UserID = MS.UserID)
				INNER JOIN HardScores AS HS ON U.UserID = HS.UserID)
					INNER JOIN MiscScores AS MSC ON U.UserID = MSC.UserID
        WHERE U.UserID = ".$_SESSION['user_id'].";";

$r = mysqli_query($dbc, $q);
if(mysqli_num_rows($r) === 1)
{
    $rowStreaks = mysqli_fetch_array($r, MYSQLI_ASSOC);
}

?>    
    
    //WINSTREAK ALL TIME CHART
    const ctx12 = document.getElementById("myChart1-2").getContext("2d");
    const myChart12 = new Chart(ctx12, {
        type: 'bar',
        data: {
            labels: ["Easy", "Medium", "Hard", "All"],
            datasets: [{
                label: 'Current',
                data: [<?php if(isset($rowStreaks)) { echo $rowStreaks['EasyStreak'].", ".$rowStreaks['MediumStreak'].", ".$rowStreaks['HardStreak'].", ".$rowStreaks['AllStreak']; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
            }, {
                label: 'Best',
                data: [<?php if(isset($rowStreaks)) { echo $rowStreaks['EasyStreak'].", ".$rowStreaks['MediumStreak'].", ".$rowStreaks['HardStreak'].", ".$rowStreaks['AllStreak']; } ?>],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        fontStyle: 'bold'
                    }                     
                }]
            },
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 20,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: true,
                text: "WIN STREAK"   
            }
        }
    });
    
    
<?php
    
$q = "SELECT ES.TilesOpened AS EasyTiles, (ES.Wins+ES.Loses) AS TotalEasy,
             MS.TilesOpened AS MediumTiles, (MS.Wins+MS.Loses) AS TotalMedium,
             HS.TilesOpened AS HardTiles, (HS.Wins+HS.Loses) AS TotalHard
        FROM (((Users AS U INNER JOIN EasyScores AS ES ON U.UserID = ES.UserID)
			INNER JOIN MediumScores AS MS ON U.UserID = MS.UserID)
				INNER JOIN HardScores AS HS ON U.UserID = HS.UserID)
					INNER JOIN MiscScores AS MSC ON U.UserID = MSC.UserID
        WHERE U.UserID = ".$_SESSION['user_id'].";";

$r = mysqli_query($dbc, $q);
if(mysqli_num_rows($r) === 1)
{
    $rowTiles = mysqli_fetch_array($r, MYSQLI_ASSOC);
}

?>
    
    const ctx13 = document.getElementById("myChart1-3").getContext("2d");
    const myChart13 = new Chart(ctx13, {
        type: 'bar',
        data: {
            labels: ["Easy(71 Tiles/Game)", "Medium(216 Tiles/Game)", "Hard(381 Tiles/Game)"],
            datasets: [{
                label: 'Total Opened',
                data: [<?php if(isset($rowTiles)) { echo $rowTiles['EasyTiles'].", ".$rowTiles['MediumTiles'].", ".$rowTiles['HardTiles']; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                hidden: true
            }, {
                label: 'AVG per Game',
                data: [<?php if(isset($rowTiles)) { echo floor($rowTiles['EasyTiles']/$rowTiles['TotalEasy']).", ".floor($rowTiles['MediumTiles']/$rowTiles['TotalMedium']).", ".floor($rowTiles['HardTiles']/$rowTiles['TotalHard']); } ?>],
                backgroundColor: 'rgba(255, 255, 0, 0.4)',
                borderColor: 'rgba(255, 180, 51, 1)',
                borderWidth: 1                
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 18,
                        fontStyle: 'bold'
                    }                     
                }]
            },
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 20,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: true,
                text: "TILES OPENED"
            }
        }
    });
    
<?php
    
$q = "SELECT AVG(ES.Wins) AS EasyWinsAVG, AVG(MS.Wins) AS MediumWinsAVG, AVG(HS.Wins) AS HardWinsAVG
        FROM (((Users AS U INNER JOIN EasyScores AS ES ON U.UserID = ES.UserID)
                INNER JOIN MediumScores AS MS ON U.UserID = MS.UserID)
                    INNER JOIN HardScores AS HS ON U.UserID = HS.UserID)
                        INNER JOIN MiscScores AS MSC ON U.UserID = MSC.UserID";

$r = mysqli_query($dbc, $q);
if(mysqli_num_rows($r) === 1)
{
    $rowWinsU = mysqli_fetch_array($r, MYSQLI_ASSOC);
}

?>    
    const ctx14 = document.getElementById("myChart1-4").getContext("2d");
    const myChart14 = new Chart(ctx14, {
        type: 'bar',
        data: {
            labels: ["Easy", "Medium", "Hard"],
            datasets: [{
                label: 'YOU',
                data: [<?php if(isset($rowWinsLoses)) { echo $rowWinsLoses['EasyWins'].", ".$rowWinsLoses['MediumWins'].", ".$rowWinsLoses['HardWins']; } ?>],
                backgroundColor: 'rgba(51, 204, 51, 0.2)',
                borderColor: 'rgba(51, 204, 51, 1)',
                borderWidth: 1
            }, {
                label: 'USERS',
                data: [<?php if(isset($rowWinsU)) { echo $rowWinsU['EasyWinsAVG'].", ".$rowWinsU['MediumWinsAVG'].", ".$rowWinsU['HardWinsAVG']; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        fontStyle: 'bold'
                    }                     
                }]
            },
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 20,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: true,
                text: "AVERAGE WINS"
            }
        }
    });
    
<?php
$q = "SELECT AVG(ES.Streak) AS EasyStreakAVG, AVG(MS.Streak) AS MediumStreakAVG, AVG(HS.Streak) AS HardStreakAVG
        FROM (((Users AS U INNER JOIN EasyScores AS ES ON U.UserID = ES.UserID)
                INNER JOIN MediumScores AS MS ON U.UserID = MS.UserID)
                    INNER JOIN HardScores AS HS ON U.UserID = HS.UserID)
                        INNER JOIN MiscScores AS MSC ON U.UserID = MSC.UserID";

$r = mysqli_query($dbc, $q);
if(mysqli_num_rows($r) === 1)
{
    $rowStreakU = mysqli_fetch_array($r, MYSQLI_ASSOC);
}

?>    
    const ctx15 = document.getElementById("myChart1-5").getContext("2d");
    const myChart15 = new Chart(ctx15, {
        type: 'bar',
        data: {
            labels: ["Easy", "Medium", "Hard"],
            datasets: [{
                label: 'YOU',
                data: [<?php if(isset($rowStreaks)) { echo $rowStreaks['EasyStreak'].", ".$rowStreaks['MediumStreak'].", ".$rowStreaks['HardStreak']; } ?>],
                backgroundColor: 'rgba(51, 204, 51, 0.2)',
                borderColor: 'rgba(51, 204, 51, 1)',
                borderWidth: 1
            }, {
                label: 'USERS',
                data: [<?php if(isset($rowStreakU)) { echo $rowStreakU['EasyStreakAVG'].", ".$rowStreakU['MediumStreakAVG'].", ".$rowStreakU['HardStreakAVG']; } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        fontStyle: 'bold'
                    }                     
                }]
            },
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 20,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: true,
                text: "AVERAGE STREAK"
            }
        }
    });
    
<?php
$q = "SELECT (SUM(ES.TilesOpened)/SUM(ES.Wins+ES.Loses)) AS EasyTilesAVG,
             (SUM(MS.TilesOpened)/SUM(MS.Wins+MS.Loses)) AS MediumTilesAVG,
             (SUM(HS.TilesOpened)/SUM(HS.Wins+HS.Loses)) AS HardTilesAVG
        FROM (((Users AS U INNER JOIN EasyScores AS ES ON U.UserID = ES.UserID)
                INNER JOIN MediumScores AS MS ON U.UserID = MS.UserID)
                    INNER JOIN HardScores AS HS ON U.UserID = HS.UserID)
                        INNER JOIN MiscScores AS MSC ON U.UserID = MSC.UserID";

$r = mysqli_query($dbc, $q);
if(mysqli_num_rows($r) === 1)
{
    $rowTilesU = mysqli_fetch_array($r, MYSQLI_ASSOC);
}

?>    
    const ctx16 = document.getElementById("myChart1-6").getContext("2d");
    const myChart16 = new Chart(ctx16, {
        type: 'bar',
        data: {
            labels: ["Easy", "Medium", "Hard"],
            datasets: [{
                label: 'YOU',
                data: [<?php if(isset($rowTiles)) { echo floor($rowTiles['EasyTiles']/$rowTiles['TotalEasy']).", ".floor($rowTiles['MediumTiles']/$rowTiles['TotalMedium']).", ".floor($rowTiles['HardTiles']/$rowTiles['TotalHard']); } ?>],
                backgroundColor: 'rgba(51, 204, 51, 0.2)',
                borderColor: 'rgba(51, 204, 51, 1)',
                borderWidth: 1
            }, {
                label: 'USERS',
                data: [<?php if(isset($rowTilesU)) { echo floor($rowTilesU['EasyTilesAVG']).", ".floor($rowTilesU['MediumTilesAVG']).", ".floor($rowTilesU['HardTilesAVG']); } ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        beginAtZero:true
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'black',
                        fontSize: 20,
                        fontStyle: 'bold'
                    }                     
                }]
            },
            legend: {
                labels: {
                    fontColor: 'black',
                    fontSize: 20,
                    fontStyle: 'bold'
                }
            },
            title: {
                display: true,
                text: "AVERAGE TILES OPENED"
            }
        }
    });
</script>