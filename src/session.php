<?php

namespace Project\Supply;

$sessionTimeout = Config::get("session_timeout", 3600);

session_start();
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
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
