<?php

namespace Module\Admin;

use Exception\LoginRequired;
use Exception\ObjectMismatch;
use Npf\Exception\DBQueryError;
use Npf\Exception\InvalidParams;

/**
 * Class Menu
 * @package Module\Admin
 */
class Role extends Base
{
    /**
     * @param $name
     * @param $desc
     * @param $menu
     * @param $action
     * @throws DBQueryError
     * @throws LoginRequired
     */
    final public function add($name, $desc, $menu, $action)
    {
        if (!is_array($menu))
            $menu = [];
        if (!is_array($action))
            $action = [];
        $action = json_encode($action);
        $menu = json_encode($menu);
        $this->model->AdminRole->add($name, $desc, $menu, $this->admin->getAdminId(), $action);
    }

    /**
     * @param $name
     * @param $desc
     * @param $menus
     * @param $actions
     * @param $id
     * @throws DBQueryError
     * @throws LoginRequired
     * @throws ObjectMismatch
     */
    final public function update($name, $desc, $menus, $actions, $id)
    {
        if (!$info = $this->get($id))
            throw new ObjectMismatch('Admin menu not found');

        if (!is_array($menus))
            $menus = [];
        if (!is_array($actions))
            $actions = [];
        $actions = json_encode($actions);
        $menus = json_encode($menus);
        $this->model->AdminRole->update($name, $desc, $menus, $actions, $this->admin->getAdminId(), $id);
    }

    /**
     * @param $id
     * @param $status
     * @throws DBQueryError
     */
    final public function updateStatus($status, $id)
    {
        if ((int)$status === 1)
            $this->model->AdminRole->active($id);
        else
            $this->model->AdminRole->deActive($id);
    }

    /**
     * @param $id
     * @return array
     * @throws DBQueryError
     */
    final public function get($id)
    {
        $role = $this->model->AdminRole->get($id);
        if (!empty($role)) {
            $role['menus'] = json_decode($role['menus'], true);
            $role['actions'] = json_decode($role['actions'], true);
        }
        return $role;
    }

    /**
     * @return array
     * @throws DBQueryError
     */
    final public function listField()
    {
        return $this->model->AdminRole->listField();
    }

    /**
     * @return array
     * @throws DBQueryError
     */
    final public function listRole()
    {
        return $this->model->AdminRole->listRole();
    }
}