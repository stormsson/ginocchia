<?php

namespace Hart\Query;

class EventsQuery {

    public static function countEventsByUser($user_id, $app)
    {
        $sql = "SELECT count(*) as total FROM events WHERE user = $user_id";
        return $app['db']->fetchAssoc($sql);
    }

    public static function countEvents($app)
    {
        $sql = "SELECT count(*) as total FROM events ";
        return $app['db']->fetchAssoc($sql);
    }
}

