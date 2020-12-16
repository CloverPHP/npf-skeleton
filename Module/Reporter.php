<?php

namespace Module;

use Npf\Core\App;
use Npf\Core\Common;
use Npf\Exception\InternalError;

/**
 * Class Reporter
 * @package Module
 * @method debug($type, $content, $params = [])
 * @method log($type, $content, $params = [])
 * @method raw_debug($type, $content, $params = [])
 * @method raw_log($type, $content, $params = [])
 * @method plain_debug($type, $content, $params = [])
 * @method plain_log($type, $content, $params = [])
 * @method both_debug($type, $content, $params = [])
 * @method both_log($type, $content, $params = [])
 */
class Reporter
{
    /**
     * @var App
     */
    private $app;
    /**
     * @var mixed
     */
    private $reporterConfig;

    /**
     * Reporter constructor.
     * @param App $app
     * @throws InternalError
     */
    public function __construct(App &$app)
    {
        $this->app = &$app;
        $this->reporterConfig = $app->config('Profiler')->get('rocketChat');
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool
     */
    public function __call($name, $arguments)
    {
        $type = 'normal';
        $matches = [];
        if (preg_match('/(raw|plain|both)_(.+)/', $name, $matches)) {
            $type = $matches[1];
            $name = $matches[2];
        }
        if (isset($this->reporterConfig['channel'][$name]))
            return $this->report($name, $type, $arguments);
        else
            return false;
    }

    /**
     * @param $channel
     * @param $type
     * @param array $arguments
     * @return bool
     */
    public function report($channel, $type, array $arguments = [])
    {
        if (isset($arguments[0])) {
            if (is_array($arguments[0]))
                $arguments[0] = implode(',', $arguments[0]);
            $arguments['title'] = $arguments[0];
            unset($arguments[0]);
        }

        if (isset($arguments[1])) {
            $arguments['content'] = $arguments[1];
            unset($arguments[1]);
        }
        if (isset($arguments[2])) {
            $arguments['params'] = $arguments[2];
            unset($arguments[2]);
        }

        $params = [
            'header' => []
        ];
        if (isset($arguments['params'])) {
            if (is_array($arguments['params']))
                $params['header'] = $arguments['params'];
        }

        if (empty($this->reporterConfig['gateway']) ||
            empty($channel) ||
            empty($arguments['content']) ||
            !isset($this->reporterConfig['channel'][$channel])
        ) {
            return false;
        }
        $params['room'] = $this->reporterConfig['channel'][$channel];
        if (is_array($arguments['content']) || is_object($arguments['content']))
            $params['text'] = json_encode($arguments['content'], JSON_PRETTY_PRINT);
        elseif (in_array($type, ['raw', 'both'], true))
            $params['raw'] = (string)$arguments['content'];
        else
            $params['text'] = (string)$arguments['content'];
        if (!in_array($type, ['plain', 'both'], true)) {
            $params['header'] += [
                'System' => strtoupper($this->app->getAppName()),
                'Environment' => strtoupper($this->app->getEnv()),
                'IP' => Common::getClientIp(),
                'Time' => Common::datetime(true),
            ];
        } else {
            $params['plain'] = 1;
            unset($params['header']);
        }
        $this->app->library->rpc->setConnectionTimeout($this->reporterConfig['connectionTimeout'] ?: 30);
        $this->app->library->rpc->setTimeout($this->reporterConfig['timeout'] ?: 10);
        return $this->app->library->rpc->run($this->reporterConfig['gateway'], 'POST', $params);
    }
}