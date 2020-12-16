<?php

namespace Module\Admin;

use Exception\LoginRequired;
use Exception\ObjectMismatch;
use Npf\Exception\DBQueryError;
use Npf\Exception\ErrorException;
use Npf\Exception\InternalError;

/**
 * Class Menu
 * @package Module\Admin
 */
class Menu extends Base
{
    /**
     * @param $parentId
     * @param $name
     * @param $icon
     * @param $uri
     * @throws DBQueryError
     */
    final public function add($parentId, $name, $icon, $uri)
    {
        $level = 0;
        if ((int)$parentId > 0) {
            $menu = $this->get($parentId);
            $level = $menu['level'] + 1;
            $this->model->AdminMenu->updateUri('', $menu['id']);
        }
        $index = $this->model->AdminMenu->getLastIndexBy($parentId) + 1;
        $this->model->AdminMenu->add($parentId, $name, $level, $index, $icon, $uri);
        $this->resortIndexbyParentId($parentId);
    }

    /**
     * @param $parentId
     * @param $name
     * @param $icon
     * @param $uri
     * @param $id
     * @throws DBQueryError
     * @throws ObjectMismatch
     */
    final public function update($parentId, $name, $icon, $uri, $id)
    {
        if (!$item = $this->get($id))
            throw new ObjectMismatch('Admin menu not found');

        $level = $item['level'];
        if ((int)$item['parentid'] !== (int)$parentId) {
            if ($parentId > 0) {
                $menu = $this->get($parentId);
                $level = $menu['level'] + 1;
                $this->model->AdminMenu->updateUri('', $menu['id']);
            } else
                $level = 0;
        }
        $this->model->AdminMenu->update($parentId, $name, $level, $icon, $uri, $id);
        $this->resortIndexbyParentId($parentId);
        if ((int)$item['parentid'] !== (int)$parentId)
            $this->resortIndexbyParentId($item['parentid']);
    }

    /**
     * @param $id
     * @param $status
     * @throws DBQueryError
     */
    final public function updateStatus($status, $id)
    {
        if ((int)$status === 1)
            $this->model->AdminMenu->active($id);
        else
            $this->model->AdminMenu->deActive($id);
    }

    /**
     * @param $parentId
     * @throws DBQueryError
     */
    private function resortIndexbyParentId($parentId)
    {
        $menuIds = $this->model->AdminMenu->getIdsByParentId($parentId);
        $seq = 0;
        foreach ($menuIds as $menuId) {
            $seq++;
            $this->model->AdminMenu->updateIndex($seq, $menuId);
        }
    }

    /**
     * @param $id
     * @throws DBQueryError
     */
    final public function moveUp($id)
    {
        $menu = $this->model->AdminMenu->get($id);
        $this->model->AdminMenu->updateIndex($menu['index'] - 1.5, $id);
        $this->resortIndexbyParentId($menu['parentid']);
    }

    /**
     * @param $id
     * @throws DBQueryError
     */
    final public function moveDown($id)
    {
        $menu = $this->model->AdminMenu->get($id);
        $this->model->AdminMenu->updateIndex($menu['index'] + 1.5, $id);
        $this->resortIndexbyParentId($menu['parentid']);
    }

    /**
     * @param $id
     * @return array
     * @throws DBQueryError
     */
    final public function get($id)
    {
        return $this->model->AdminMenu->get($id);
    }

    /**
     * @param $uri
     * @return array
     * @throws DBQueryError
     * @throws LoginRequired
     */
    final public function getMenuByUri($uri)
    {
        $admin = $this->admin->getAdmin();
        return $this->module->Model->AdminMenu->getByUri($uri, $admin['type'] !== 'main' ? $admin['role']['menus'] : null);
    }

    /**
     * @return array
     * @throws DBQueryError
     * @throws LoginRequired
     * @throws InternalError
     */
    final public function getMenus()
    {
        $admin = $this->admin->getAdmin(true);
        if (!empty($admin)) {
            if ($admin['type'] === 'main')
                $menuIds = null;
            else
                $menuIds = $admin['role']['menus'];
            $lists = $this->app->session->get('menu', []);
            if (empty($lists)) {
                $lists = $this->model->AdminMenu->listMenu();
                $lists = $this->buildTree($lists);
                if ($admin['type'] !== 'main')
                    $this->clearAdminMenu($lists, $menuIds);
                $this->app->session->set('menu', $lists);
            }
            return $lists;
        } else
            return [];
    }

    /**
     * @param bool $withUriOnly
     * @param array $ignoreIds
     * @param int $maxLevel
     * @return array
     * @throws DBQueryError
     */
    final public function listField($withUriOnly = false, $ignoreIds = [], $maxLevel = 4)
    {
        $lists = $this->model->AdminMenu->listMenu($maxLevel);
        $menu = [];
        foreach ($lists as $key => &$item) {
            $item['seq'] = $this->getSeq($lists, $item['id']);
            try {
                $menu[$item['id']] = implode(' / ', $this->chaseFar($lists, $item['id']));
            } catch (ErrorException $ex) {
                unset($lists[$key]);
                continue;
            }
        }
        if ($withUriOnly)
            $menu = array_intersect_key($menu, array_flip($this->model->AdminMenu->getWithUriIds()));
        if (!empty($ignoreIds) && is_array($ignoreIds))
            $menu = array_diff_key($menu, array_flip($ignoreIds));
        uksort($menu, function ($a, $b) use ($lists) {
            $result = $lists[$a]['seq'] > $lists[$b]['seq'] ? 1 : ($lists[$a]['seq'] === $lists[$b]['seq'] ?
                ($lists[$a]['name'] > $lists[$b]['name'] ? 1 : -1)
                : -1);
            return $result;
        });
        return $menu;
    }

    /**
     * @param int $maxLevel
     * @return array
     * @throws DBQueryError
     */
    final public function listMenu($maxLevel = 4)
    {
        $list = $this->model->AdminMenu->listMenu($maxLevel);
        return $this->buildTree($list);
    }

    /**
     * @param array $lists
     * @param int $parentId
     * @return array
     */
    private function buildTree(array $lists, $parentId = 0)
    {
        $branch = [];
        foreach ($lists as $item) {
            if ($item['parentid'] === (int)$parentId) {
                $children = $this->buildTree($lists, $item['id']);
                if ($children) {
                    $item['lists'] = $children;
                } else
                    $item['last'] = 1;
                $branch[] = $item;
            }
        }
        return $branch;
    }

    /**
     * @param array $lists
     * @param $userMenu
     * @return void
     */
    private function clearAdminMenu(array &$lists, $userMenu)
    {
        foreach ($lists as $key => &$item) {
            if (!empty($item['lists']))
                $this->clearAdminMenu($item['lists'], $userMenu);
            if ((empty($item['lists']) && empty($item['last'])) || (!empty($item['last']) && !in_array((string)$item['id'], $userMenu, true)))
                unset($lists[$key]);
        }
    }

    /**
     * @param array $lists
     * @param int $id
     * @param string $seq
     * @return float|int
     */
    private function getSeq(array $lists, $id = 0, $seq = '')
    {
        $seq = "{$lists[$id]['index']}" . (!empty($seq) ? "," : "") . $seq;
        if (!empty($lists[$id]['parentid']))
            $seq = $this->getSeq($lists, $lists[$id]['parentid'], $seq);
        return $seq;
    }

    /**
     * @param array $lists
     * @param $id
     * @return array
     */
    private function chaseFar(array &$lists, $id)
    {
        $name = [$lists[$id]['name']];
        if (!empty($lists[$id]['parentid']))
            $name = array_merge($this->chaseFar($lists, $lists[$id]['parentid']), $name);
        return $name;
    }
}