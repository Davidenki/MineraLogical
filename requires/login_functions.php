<?php

function redirect_user ($page = 'Game.php')
{
    $url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

    $url = rtrim($url, '/\\');

    $url .= '/'.$page;

    header("Location: $url");
    exit();
}

function check_login($dbc, $username = '', $pass = '')
{
    $errors = array();

    //CHECK USERNAME
    if (empty($username))
    {
        $errors[0] = '<div class="input-group"><label for="username" class="input-group-addon"><i class="fas fa-user fa-2x"></i></label><input class="form-control is-invalid" type="text" name="username" id="username" placeholder="Type your Username" /><div class="invalid-feedback"><label for="username">You forgot to enter your username.</label></div></div>';
    }
    else { $u = mysqli_real_escape_string($dbc, trim($username)); }

    //CHECK PASSWORD
    if (empty($pass))
    {
        $errors[1] = '<div class="input-group"><label for="password" class="input-group-addon"><i class="fas fa-lock fa-2x"></i></label><input class="form-control is-invalid" type="password" name="password" id="password" placeholder="Type your Password" /><div class="invalid-feedback"><label for="password">You forgot to enter your password.</label></div></div>';
    }
    else { $p = mysqli_real_escape_string($dbc, trim($pass)); }

    if (empty($errors))
    {
        $q = "SELECT UserID, Username FROM users WHERE Username='$u' AND Password=SHA1('$p')";		
        $r = @mysqli_query ($dbc, $q);

        if (mysqli_num_rows($r) === 1)
        {
            $row = mysqli_fetch_array ($r, MYSQLI_ASSOC);

            return array(true, $row);
        }
        else
        {
            $errors[2] = '<div class="alert alert-danger text-center" role="alert"><strong>The Username or the Password entered are not correct!</strong></div>';
        }
    }

    return array(false, $errors);
}
function isLoggedIn()
{
    return ((isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['agent'])) && $_SESSION['agent'] === md5($_SERVER['HTTP_USER_AGENT']));
}
 ?>