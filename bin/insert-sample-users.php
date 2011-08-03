<?php

require_once '../config/bootstrap.php';

$userIds = array();

for ($i = 0; $i < 1000; $i++) {
    $hash = md5(uniqid(mt_rand(), true));
    

    $query = '
        INSERT INTO user SET
        user_email = "user-' . $hash . '@example.com",
        user_password = "' . md5('test') . '",
        user_nickname = "john_doe_' . $hash . '",
        user_location = "location ' . $hash . '",
        user_name = "John Doe ' . $hash . '",
        user_friend_ids = ""
    ';

    print $query;
    print chr(10);

    $app['db.orm.em']->getConnection()->executeUpdate($query);
    $userIds[] = $app['db.orm.em']->getConnection()->lastInsertId();
}

$friendIdsMap = array();

foreach ($userIds as $userId) {
    $possibleFriends = array_diff( $userIds, array($userId));
    shuffle($possibleFriends);

    $friendIds = array_slice($possibleFriends, 0, mt_rand(10, 30));

    if (!isset($friendIdsMap[$userId])) {
        $friendIdsMap[$userId] = array();
    }

    $friendIdsMap[$userId] = array_merge($friendIdsMap[$userId], $friendIds);

    foreach ($friendIds as $friendId) {
        if (!isset($friendIdsMap[$friendId])) {
            $friendIdsMap[$friendId] = array();
        }

        $friendIdsMap[$friendId][] = $userId;
    }

}

$friendIdsMap = array_map('array_unique', $friendIdsMap);

foreach ($friendIdsMap as $userId => $friendIds) {
    $query = '
        UPDATE user SET
        user_friend_ids = \'' . serialize($friendIds) . '\'
        WHERE user_id = "' . $userId . '"
    '; 

    print $query;
    print chr(10);

    $app['db.orm.em']->getConnection()->executeUpdate($query);
}



