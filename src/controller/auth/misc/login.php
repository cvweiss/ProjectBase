<?php

namespace cvweiss\projectbase\Controller\auth\misc;

use cvweiss\projectbase\Mongo;
use cvweiss\projectbase\Session;

class login
{
    public function doGet($render, $params)
    {
        $render->render("auth/misc/login", $params);
    }

    public function doPost($render, $params)
    {    
        $userID = strtolower(filter_input(INPUT_POST, 'userid'));
        $pass = filter_input(INPUT_POST, 'password');
         
        $error = null;
        $message = null;

        $array = ['id' => $userID];
        $user = Mongo::get()->findDoc('users', ['id' => $userID]);
        $hash = $user->get('password');

        if (password_verify($pass, $hash)) {
            $message = "Successful login.";
            Session::getSession()->set("userID", $userID);
            $params['user_name'] = $userID;
        } else {
            $error = "No such credentials.";
        }
        
        $params['errorCode'] = $error == null ? 'Success' : 'Error';
        $params['errorMessage'] = $error == null ? $message : $error;
        $render->render('error', $params);
    }
}
