<?php

namespace Hart\Query;

class UsersQuery {

    public static function getUserById($user_id, $app)
    {
        $sql = "SELECT * FROM users WHERE id = $user_id";
        return $app['db']->fetchAssoc($sql);
    }
}

