<?php

namespace Model;

use mysqli_result;
use Npf\Core\Model;
use Npf\Exception\DBQueryError;

/**
 * Class Merchant
 * @package Model
 */
class AdminMenu extends Model
{
    protected $tableName = 'admin_menu';

    /**
     * @param $parentId
     * @param $level
     * @param $name
     * @param $index
     * @param $icon
     * @param $uri
     * @return bool|mysqli_result
     * @throws DBQueryError
     */
    public function add($parentId, $name, $level, $index, $icon, $uri)
    {
        return $this->addOne([
            'parentid' => $parentId,
            'name' => $name,
            'level' => $level,
            'index' => $index,
            'icon' => $icon,
            'uri' => $uri,
        ]);
    }

    /**
     * @param $parentId
     * @param $name
     * @param $level
     * @param $icon
     * @param $uri
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function update($parentId, $name, $level, $icon, $uri, $id)
    {
        return $this->updateOneById([
            'parentid' => $parentId,
            'name' => $name,
            'level' => $level,
            'icon' => $icon,
            'uri' => $uri,
        ], $id);
    }

    /**
     * @param $uri
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updateUri($uri, $id)
    {
        return $this->updateOneById([
            'uri' => $uri,
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
        return $this->updateOneById(['status' => 0], $id);
    }

    /**
     * @param $index
     * @param $id
     * @return bool|int|mysqli_result
     * @throws DBQueryError
     */
    public function updateIndex($index, $id)
    {
        return $this->updateOneById(['index' => $index], $id);
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
     * @param $uri
     * @param $ids
     * @return array
     * @throws DBQueryError
     */
    public function getByUri($uri, $ids = null)
    {
        $cond = ['uri' => $uri, 'status' => 1];
        if (is_array($ids))
            $cond['id'] = ['IN' => $ids];
        return $this->getOneByCond('*', $cond);
    }

    /**
     * @param $parentId
     * @return array
     * @throws DBQueryError
     */
    public function getLastIndexBy($parentId)
    {
        return $this->getCellByCond('index',
            ['status' => 1, 'parentid' => $parentId], ['index' => 'DESC']);
    }

    /**
     * @param $parentId
     * @return array
     * @throws DBQueryError
     */
    public function getIdsByParentId($parentId)
    {
        return $this->getColumnByCond('id',
            ['status' => 1, 'parentid' => $parentId], ['index' => 'ASC']);
    }

    /**
     * @return array
     * @throws DBQueryError
     */
    public function getWithUriIds()
    {
        return $this->getColumnByCond('id',
            ['status' => 1, 'uri' => '{DB_NE}'], ['index' => 'ASC']);
    }

    /**
     * @param $ids
     * @return array
     * @throws DBQueryError
     */
    public function getMenuList($ids = null)
    {
        $cond = ['status' => 1];
        if (!empty($ids) && is_array($ids))
            $cond['id'] = ['IN' => $ids];
        return $this->getAllByCond('*', null, $cond,
            ['index' => 'ASC', 'name' => 'ASC']);
    }

    /**
     * @param null $maxLevel
     * @return array
     * @throws DBQueryError
     */
    public function listMenu($maxLevel = null)
    {
        $cond = ['status' => 1];
        if($maxLevel !== null)
            $cond['level'] = "{DB_LE}{$maxLevel}";
        return $this->getAllByCond(
            ['id', 'parentid', 'name', 'level', 'index', 'icon', 'uri'], 'id', $cond,
            ['index' => 'ASC', 'name' => 'ASC']);
    }
}