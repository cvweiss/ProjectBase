<?php

namespace Project\Base;

$session = Session::getSession();

$userID = $session->get("userID");
if ($userID !== null)
{
    $user = Mongo::get()->findDoc("users", ['id' => $userID]);

    if ($user !== null)
    {
        $config = Config::getInstance();
        $config->set("user_email", $user->get("email"));
        $config->set("user_name", $user->get("name"));
        $config->set("user_image", $user->get("image"));
    }
}
