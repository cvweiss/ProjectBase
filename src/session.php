<?php

namespace Project\Base;

$sessionTimeout = Config::get("session_timeout", 3600);

session_set_cookie_params($sessionTimeout);
session_start();
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout))
{
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time(); // update last activity time stamp

$user_id = @$_SESSION['user_id'];
if ($user_id !== null)
{
    $user = Mongo::findDoc("users", ['id' => $user_id]);
    if ($user != null)
    {
        Config::set("user_email", $user->get("email"));
        Config::set("user_name", $user->get("name"));
        Config::set("user_image", $user->get("image"));
    }
}
