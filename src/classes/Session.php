<?php

namespace Project\Base;

class Session
{
    private static $session = null;
    private static $segment = null;

    public static function getSession($segmentName = __NAMESPACE__)
    {
        if (self::$segment === null) {
            $sessionTimeout = Config::get("session_timeout", 3600);

            $sessionFactory = new \Aura\Session\SessionFactory;
            self::$session = $sessionFactory->newInstance($_COOKIE);
            self::$session->setCookieParams(array('lifetime' => $sessionTimeout));
            self::$segment = self::$session->getSegment($segmentName);
        }
        return self::$segment;
    }

    public static function destroy()
    {
        self::$session->destroy();
    }
}
