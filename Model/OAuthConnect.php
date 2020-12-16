<?php

namespace Model;

use Npf\Core\Model;
use Npf\Core\Common;
use Npf\Exception\DBQueryError;

/**
 * Class AccMainLedger
 * @package Model
 */
final class OAuthConnect extends Model
{
    /**
     * @var string
     */
    protected $tableName = 'oauth_connect';

    /**
     * @param $roleId
     * @param $service
     * @param $serviceId
     * @return array|bool|null
     * @throws DBQueryError
     */
    final public function create($roleId, $service, $serviceId)
    {
        return $this->addOne([
            'roleid' => $roleId,
            'service' => $service,
            'serviceid' => $serviceId,
            'created' => Common::datetime(),
            'createdts' => Common::timestamp(),
        ]);
    }

    /**
     * @param $roleId
     * @param $service
     * @return array|bool|null
     * @throws DBQueryError
     */
    final public function delete($roleId, $service)
    {
        return $this->deleteOneByCond([
            'roleid' => $roleId,
            'service' => $service,
        ]);
    }

    /**
     * @param $party
     * @param $partyId
     * @return array
     * @throws DBQueryError
     */
    final public function getByParty($party, $partyId)
    {
        return $this->getCellByCond('roleid', [
            'service' => $party,
            'serviceid' => $partyId,
        ]);
    }

    /**
     * @param $roleId
     * @return array
     * @throws DBQueryError
     */
    final public function listServiceByRoleId($roleId)
    {
        return $this->getColumnByCond('service', ['roleid' => $roleId]);
    }
}

