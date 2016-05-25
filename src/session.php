<?php

namespace Project\Base;

$session = Session::getSession();

$userID = $session->get("userID");
if ($userID !== null)
{
    $user = Mongo::get()->findDoc("users", ['id' => $userID]);
    if ($user !== null)
    {
        Config::set("user_email", $user->get("email"));
        Config::set("user_name", $user->get("name"));
        Config::set("user_image", $user->get("image"));
    }
}
