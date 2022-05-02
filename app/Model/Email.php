<?php
namespace App\Model;

use Base\AbstractModel;
use Base\Db;

class Email extends AbstractModel
{
    public function getUser(string $email)
    {
        $db = Db::getInstance();
        $select = "SELECT * FROM users WHERE `email` = :email";
        return $db->fetchOne($select, __METHOD__, [':email' => $email]);
    }
}