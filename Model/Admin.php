<?php

namespace Model;

use mysqli_result;
use Npf\Core\Common;
use Npf\Core\Model;
use Npf\Exception\DBQueryError;

/**
 * Class Admin
 * @package Model
 */
class Admin extends Model
{
    protected $tableName = 'admin';

    /**
     * @param $username
     * @param $password
     * @return array
     * @throws DBQueryError
     */
    public function getByLogin($username, $password)
    {
        return $this->getOneByCond('*', [
            'user' => $username,
            'pass' => $password,
            'status' => 1
        ]);
    }

    /**
     * @param $id
     * @return array
     * @throws DBQueryError
     */
    public function get($id)
    {
        return $this->getOneById('*', $id);
    }

    /**
     * @param $user
     * @param $password
     * @param $name
     * @param $createBy
     * @return bool|mysqli_result
     * @throws DBQueryError
     */
    public function create($user, $password, $name, $createBy)
    {
        return $this->addOne([
            'user' => $user,
            'pass' => $password,
            'name' => $name,
            'status' => 1,
            'updateby' => $createBy,
            'createby' => $createBy,
            'updated' => Common::datetime(),
            'created' => Common::datetime(),
        ]);
    }

    /**
     * @param $status
     * @param $id
     * @param $updateBy
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updateStatus($status, $id, $updateBy)
    {
        return $this->updateOneById([
            'status' => $status,
            'updateby' => $updateBy,
            'updated' => Common::datetime(),
        ], $id);
    }

    /**
     * @param $password
     * @param $id
     * @param $updateBy
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updatePassword($password, $id, $updateBy)
    {
        return $this->updateOneById([
            'pass' => $password,
            'updateby' => $updateBy,
            'updated' => Common::datetime(),
        ], $id);
    }

    /**
     * @param $name
     * @param $id
     * @param $updateBy
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updateName($name, $id, $updateBy)
    {
        return $this->updateOneById([
            'name' => $name,
            'updateby' => $updateBy,
            'updated' => Common::datetime(),
        ], $id);
    }

    /**
     * @return array
     * @throws DBQueryError
     */
    public function listAdmin()
    {
        return $this->getAllByCond(['id', 'user', 'name', 'status'], null, null, ['id' => 'DESC']);
    }
}