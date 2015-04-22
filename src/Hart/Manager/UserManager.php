<?php

namespace Hart\Manager;

class UserManager {

    protected static $session_key = 'user_id';

    public static function getSessionToken($app)
    {
        return $app['session']->get(self::$session_key);
    }

    public static function setSessionToken($app, $user)
    {
        $app['session']->set(self::$session_key, array(self::$session_key => $user['id']));
        return $app['session']->get(self::$session_key);
    }

    public static function deleteSessionToken($app)
    {
        if($app['session']->get(self::$session_key)) {
            $app['session']->set(self::$session_key, null);
            return true;
        } else {
            return false;
        }
    }
}

