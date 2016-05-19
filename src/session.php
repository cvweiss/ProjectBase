<?php

namespace Project\Base;

$session = Session::getSession();

$userID = $session->get("userID");
if ($userID !== null)
{
    $user = Db::get()->users->findOne(['id' => $userID]);
    if ($user !== null)
    {
        Config::set("user_email", $user->email);
        Config::set("user_name", $user->name);
        Config::set("user_image", $user->image);
    }
}
