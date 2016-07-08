<?php

namespace cvweiss\projectbase\Controller\auth\misc;

use cvweiss\projectbase\Mongo;
use cvweiss\projectbase\MongoDoc;
use cvweiss\projectbase\Session;

class register
{
    public function doGet($render, $params)
    {
        $render->render("auth/misc/register", $params);
    }

    public function doPost($render, $params)
    {
        $userID = strtolower(filter_input(INPUT_POST, 'userid'));
        $password = filter_input(INPUT_POST, 'password');

        $error = null;
        $message = null;
        $user = Mongo::get()->findDoc("users", ["id" => $userID]);

        if ($user != null) {
            $error = 'User account already exists';
        } else {
            $user = new MongoDoc("users");
            $user->set("id", $userID);
            $user->set("name", $userID);
            $user->set("password", password_hash($password, PASSWORD_DEFAULT));
            $user->save();
            $message = "Account created!";
            $params['user_name'] = $userID;
            Session::getSession()->set("userID", $userID);
        }

        $params['errorCode'] = $error === null ? 'Success' : 'Error';
        $params['errorMessage'] = $error === null ? $message : $error;
        $render->render('error', $params);
    }
}
