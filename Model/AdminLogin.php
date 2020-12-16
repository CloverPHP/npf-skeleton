<?php

namespace Model;

use mysqli_result;
use Npf\Core\Common;
use Npf\Core\Model;
use Npf\Exception\DBQueryError;

/**
 * Class AdminLogin
 * @package Model
 */
class AdminLogin extends Model
{
    protected $tableName = 'admin_login';

    /**
     * member login log
     * @param $adminId
     * @param $userAgent
     * @param $browser
     * @param $platform
     * @param $clientIp
     * @return bool|mysqli_result
     * @throws DBQueryError
     */
    public function addLog($adminId, $userAgent, $browser, $platform, $clientIp)
    {
        return $this->addOne([
            'adminid' => $adminId,
            'useragent' => $userAgent,
            'browser' => $browser,
            'platform' => $platform,
            'ip' => $clientIp,
            'created' => Common::datetime(),
        ]);
    }
}