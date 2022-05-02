<?php
namespace App\Model;

use Base\AbstractModel;
use Base\Db;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;


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

class User extends Model
{
    private $id;
    private $name;
    private $password;
    private $email;
    private $createdAt;
    private $userRole;

    public function __construct($data = [])
    {
        if ($data) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->password = $data['password'];
            $this->email = $data['email'];
            $this->createdAt = $data['createdAt'];
            $this->userRole = $data['userRole'];
        }
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId($email)
    {
        return Capsule::table('users')->where('email', '=', $email)->value('id');
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword($email)
    {
        return Capsule::table('users')->where('email', '=', $email)->value('password');
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAtThis(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function saveMe()
    {
        $curDate = date('Y-m-d');
        $id = Capsule::table('users')->insertGetId(['name' => $this->name, 'password' => $this->password, 'createdAt' => $curDate, 'email' => $this->email]);

        $this->id = $id;

        return $id;
    }

    public static function getById(int $id): ?self
    {
        $data = Capsule::table('users')->where('id', $id)->get();
        if (!$data) {
            return null;
        }
        return new self($data);
    }

    public static function getByEmail(string $email): ?self
    {
        $data = Capsule::table('users')->where('email', $email)->get();
        if (!$data) {
            return null;
        }
        return new self($data);
    }

    public static function getPasswordHash(string $password)
    {
        return sha1('nfoui3w' . $password);
    }

    public static function getAllUsers()
    {
        return Capsule::table('users')->get();
    }
    public static function checkEmail(string $email)
    {
        $data = Capsule::table('users')->where('email', $email)->get();
        if ($data) {
            $data = $data->toArray();
            return array_shift($data)->email;
        }
        return null;
    }
    public static function updateUser($id, $email, $name, $role, $avatar)
    {
        if ($role == 'admin') {
            $role = ADMIN_ROLE;
        } else {
            $role = USER_ROLE;
        }

        $data = Capsule::table('users')->where('id', $id)->update(['email' => $email, 'name' => $name, 'userRole' => $role, 'avatar' => $avatar]);

        if ($data) {
            return true;
        } else {
            return false;
        }
    }
}
