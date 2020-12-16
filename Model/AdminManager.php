<?php

namespace Model;

use mysqli_result;
use Npf\Core\Common;
use Npf\Core\Model;
use Npf\Exception\DBQueryError;

/**
 * Class AdminManager
 * @package Model
 */
class AdminManager extends Model
{
    protected $tableName = 'admin_manager';

    /**
     * @param $type
     * @param $roleId
     * @param $user
     * @param $pass
     * @param $name
     * @param $createBy
     * @return bool|mysqli_result
     * @throws DBQueryError
     */
    public function add($type, $roleId, $user, $pass, $name, $createBy)
    {
        return $this->addOne([
            'type' => $type,
            'roleid' => $roleId,
            'user' => $user,
            'name' => $name,
            'pass' => $pass,
            'status' => 1,
            'updateby' => $createBy,
            'createby' => $createBy,
            'updated' => Common::datetime(),
            'created' => Common::datetime(),
        ]);
    }

    /**
     * @param $type
     * @param $roleId
     * @param $pass
     * @param $name
     * @param $updateBy
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function update($type, $roleId, $pass, $name, $updateBy, $id)
    {
        return $this->updateOneById([
            'type' => $type,
            'roleid' => $roleId,
            'pass' => $pass,
            'name' => $name,
            'updateby' => $updateBy,
            'updated' => Common::datetime(),
        ], $id);
    }

    /**
     * @param $password
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updatePassword($password, $id)
    {
        return $this->updateOneById([
            'pass' => $password,
        ], $id);
    }

    /**
     * @param $name
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updateName($name, $id)
    {
        return $this->updateOneById([
            'name' => $name,
        ], $id);
    }

    /**
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function active($id)
    {
        return $this->updateOneById(['status' => 1], $id);
    }

    /**
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function deActive($id)
    {
        return $this->updateOneById(['status' => 9], $id);
    }

    /**
     * @param $id
     * @return array
     * @throws DBQueryError
     */
    public function get($id)
    {
        return $this->getOneById(['id', 'user', 'pass', 'name', 'type', 'roleid', 'status'], $id);
    }

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
        ]);
    }

    /**
     * @param $name
     * @param $role
     * @return bool|int
     * @throws DBQueryError
     */
    public function countSearch($name, $role)
    {
        $cond = null;

        if (isset($name))
            $cond['name'] = "{DB_LIKE}%{$name}%";

        if (!empty($role))
            $cond["roleid"] = $role;

        return $this->getCount('*', $cond);
    }

    /**
     * @param $name
     * @param $role
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws DBQueryError
     */
    public function searchList($name, $role, $page = 1, $pageSize = 10)
    {
        $cond = null;

        if (isset($name))
            $cond['name'] = "{DB_LIKE}%{$name}%";

        if (!empty($role))
            $cond["roleid"] = $role;

        return $this->getAllByCond(['id', 'user', 'name', 'type', 'roleid', 'status'], null, $cond,
            ['id' => 'DESC'],
            [$page * $pageSize, $pageSize]);
    }

}