<?php

namespace Model;

use mysqli_result;
use Npf\Core\Common;
use Npf\Core\Model;
use Npf\Exception\DBQueryError;

/**
 * Class AdminRole
 * @package Model
 */
class AdminRole extends Model
{
    protected $tableName = 'admin_role';

    /**
     * @param $name
     * @param $desc
     * @param $menu
     * @param $action
     * @param $createBy
     * @return bool|mysqli_result
     * @throws DBQueryError
     */
    public function add($name, $desc, $menu, $action, $createBy)
    {
        return $this->addOne([
            'name' => $name,
            'description' => $desc,
            'menus' => $menu,
            'actions' => $action,
            'status' => 1,
            'updateby' => $createBy,
            'createby' => $createBy,
            'updated' => Common::datetime(),
            'created' => Common::datetime(),
        ]);
    }

    /**
     * @param $name
     * @param $desc
     * @param $menu
     * @param $action
     * @param $updateBy
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function update($name, $desc, $menu, $action, $updateBy, $id)
    {
        return $this->updateOneById([
            'name' => $name,
            'description' => $desc,
            'menus' => $menu,
            'actions' => $action,
            'updateby' => $updateBy,
            'updated' => Common::datetime(),
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
        return $this->getOneById('*', $id);
    }

    /**
     * @param $id
     * @return array|mixed
     * @throws DBQueryError
     */
    public function getRoleMenus($id)
    {
        return $this->getCellById('menus', $id);
    }

    /**
     * @return array
     * @throws DBQueryError
     */
    public function listField()
    {
        return $this->getColumnByCond(['id', 'name'], null, ['status' => 1], ['id' => 'ASC']);
    }

    /**
     * @return array
     * @throws DBQueryError
     */
    public function listRole()
    {
        return $this->getAllByCond(['id', 'name', 'description', 'status'], null, null, ['id' => 'DESC']);
    }
}