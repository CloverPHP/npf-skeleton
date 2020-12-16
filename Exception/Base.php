<?php

namespace Exception;

use Npf\Core\Exception;

/**
 * Class Model
 * @package Model
 */
abstract class Base extends Exception
{
    /**
     * Base constructor.
     * @param string $desc
     * @param string $code
     * @param string $status
     * @param array $extra
     */
    public function __construct($desc = '', $code = '', $status = 'error', array $extra = [])
    {
        parent::__construct($desc, $code, $status, $extra);
    }
}