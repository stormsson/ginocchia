<?php

namespace Hart\Query;

class UsersQuery {

    public static function getUserById($user_id, $app)
    {
        $sql = "SELECT * FROM users WHERE id = $user_id";
        return $app['db']->fetchAssoc($sql);
    }

    public static function getUsersByHits($app)
    {
        $sql = "
        SELECT users.facebook_id, users.first_name, count(events.id) as total
        FROM users
        JOIN events ON users.id=events.user
        GROUP BY users.first_name
        ORDER BY total DESC;
        ";
        return $app['db']->fetchAll($sql);
    }
}

