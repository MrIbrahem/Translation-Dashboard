<?php

namespace Actions\AccessHelps;
/*
Usage:
include_once __DIR__ . '/actions/access_helps.php';
use function Actions\AccessHelps\get_access_from_db;
use function Actions\AccessHelps\del_access_from_db;
use function Actions\AccessHelps\add_access_to_db;
*/

include_once __DIR__ . '/mdwiki_sql.php';
include_once __DIR__ . '/../auth/config.php';
include_once __DIR__ . '/../auth/helps.php';

use function Actions\MdwikiSql\execute_query;
use function Actions\MdwikiSql\fetch_query;
use function OAuth\Helps\decode_value;
use function OAuth\Helps\encode_value;

function add_access_to_db($user, $access_key, $access_secret)
{
    $t = [
        trim($user),
        encode_value($access_key),
        encode_value($access_secret)
    ];
    //---
    $query = <<<SQL
        INSERT INTO access_keys (user_name, access_key, access_secret)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
            access_key = VALUES(access_key),
            access_secret = VALUES(access_secret);
    SQL;
    //---
    execute_query($query, $t);
};

function get_access_from_db($user)
{
    // تأكد من تنسيق اسم المستخدم
    $user = trim($user);

    // SQL للاستعلام عن access_key و access_secret بناءً على اسم المستخدم
    $query = <<<SQL
        SELECT access_key, access_secret
        FROM access_keys
        WHERE user_name = ?;
    SQL;

    // تنفيذ الاستعلام وتمرير اسم المستخدم كمعامل
    $result = fetch_query($query, [$user]);

    // التحقق مما إذا كان قد تم العثور على نتائج
    if ($result) {
        $result = $result[0];
        return [
            'access_key' => decode_value($result['access_key']),
            'access_secret' => decode_value($result['access_secret'])
        ];
    } else {
        // إذا لم يتم العثور على نتيجة، إرجاع null أو يمكنك تخصيص رد معين
        return null;
    }
}

function del_access_from_db($user)
{
    $user = trim($user);

    $query = <<<SQL
        DELETE FROM access_keys WHERE user_name = ?;
    SQL;

    $result = execute_query($query, [$user]);

}
