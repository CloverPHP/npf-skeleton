<?php

namespace Module\Admin;

use Exception\LoginRequired;
use Exception\ObjectMismatch;
use Npf\Exception\DBQueryError;

/**
 * Class Profile
 * @package Module\Admin
 */
class Manager extends Base
{
    /**
     * @param $role
     * @param $user
     * @param $pass
     * @param $name
     * @throws DBQueryError
     * @throws LoginRequired
     */
    final public function add($role, $user, $pass, $name)
    {
        $type = 'sub';
        if ((int)$role === 0)
            $type = 'main';
        $pass = sha1($user . $pass);
        $this->model->AdminManager->add($type, $role, $user, sha1($user . $pass), $name, $this->admin->getAdminId());
    }

    /**
     * @param $role
     * @param $pass
     * @param $name
     * @param $id
     * @throws DBQueryError
     * @throws ObjectMismatch
     * @throws LoginRequired
     */
    final public function update($role, $pass, $name, $id)
    {
        if (!$info = $this->get($id))
            throw new ObjectMismatch('Admin not found');

        $type = 'sub';
        if ((int)$role === 0)
            $type = 'main';
        if (!empty($pass))
            $pass = sha1($info['user'] . $pass);
        else
            $pass = $info['pass'];
        $this->model->AdminManager->update($type, $role, $pass, $name, $this->admin->getAdminId(), $id);
    }

    /**
     * @param $id
     * @param $status
     * @throws DBQueryError
     */
    final public function updateStatus($status, $id)
    {
        if ((int)$status === 1)
            $this->model->AdminManager->active($id);
        else
            $this->model->AdminManager->deActive($id);
    }

    /**
     * @param $id
     * @return array
     * @throws DBQueryError
     */
    final public function get($id)
    {
        return $this->model->AdminManager->get($id);
    }

    /**
     * @param $name
     * @param $roleId
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws DBQueryError
     */
    final public function search($name, $roleId, $page = 1, $pageSize = 100)
    {
        $roles = $this->module->Admin->Role->listField();
        $count = $this->model->AdminManager->countSearch($name, $roleId);
        $maxPage = ceil($count / $pageSize);
        if ($page > $maxPage) $page = $maxPage;
        if ($page < 1) $page = 1;

        $lists = $this->model->AdminManager->searchList($name, $roleId, $page - 1, $pageSize);
        foreach ($lists as &$admin)
            $admin['roleName'] = isset($roles[$admin['roleid']]) ? $roles[$admin['roleid']] : $admin['type'];

        return [
            'roles' => $roles,
            'pagination' => [
                'count' => $count,
                'page' => $page,
                'size' => $pageSize,
                'total' => $maxPage,
            ],
            'lists' => $lists,
        ];
    }
}