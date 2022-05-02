<?php
namespace App\Model;
use Base\AbstractModel;
use Base\Db;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB1;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'eloquent',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

class post extends Model
{
    private $message;
    private $userId;

    public function __construct($message = null, $userId = null)
    {
        $this->message = $message;
        $this->userId = $userId;
    }


    public function saveMessage()
    {
        $curDate = date('Y-m-d');
        Capsule::table('posts')->insert(['user_id' => $this->userId, 'created_date' => $curDate, 'message' => $this->message]);
    }
    public function showLastMessages()
    {
        return post::query()->limit(20)->orderBy('id', 'desc')->get();
    }
    public function getIdSavedImage()
    {
        return Capsule::table('posts')->orderBy('id', 'desc')->limit(1)->get();
    }
    public function getLastMessagesById($userId)
    {
        $data = Capsule::table('posts')->where('user_id', '=', $userId)->orderBy('id', 'desc')->limit(20)->get();
        if (!$data) {
            return false;
        } else {
            return json_encode($data);
        }
    }
    public function checkUserRole($userId)
    {
        $data = Capsule::table('users')->where('id', '=', $userId)->value('userRole');
        if ($data == 1) {
            return USER_ROLE;
        } else {
            return ADMIN_ROLE;
        }
    }
    public function deleteMessage($postId)
    {
        Capsule::table('posts')->where('id', '=', $postId)->delete();
    }
    public function getAvatar()
    {
        return Capsule::table('users')->orderBy('id', 'asc')->get();
//        $results = $data->toArray();
//        foreach ($results as $key, $value) {


    }
}
