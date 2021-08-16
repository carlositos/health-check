<?php
declare(strict_types=1);

namespace common\models\HealthCheck\Checks;

use common\models\HealthCheck\Exceptions\HealthWarningException;
use common\models\HealthCheck\HealthCheck;

class MemcachedHealthCheck extends HealthCheck
{
    protected $title = 'memcached';

    /** @var string */
    protected $description = 'Check the memcache connection.';

    protected $servers = [];

    /** @var \Memcached */
    protected $memcached;

    public function __construct($memcached = null)
    {
        $this->memcached = $memcached ?: new \Memcached();
    }

    /**
     * Check for connection to memcached servers
     *
     * @return void
     *
     * @throws HealthWarningException
     */
    public function run(): void
    {
        $startTime = microtime(true);

        if (count($this->servers())) {
            $this->memcached->addServers($this->servers());
        }

        $result = $this->memcached->set('test.connection', 'success', 1);

        $this->duration = microtime(true) - $startTime;

        if (!$result) {
            throw new HealthWarningException('Unable to set test value in memcache');
        }

        $this->setStatus('able to set test value in memcache');
    }

    /**
     * Add server to check
     *
     * @param string $server
     * @param int $port
     * @param int $weight
     *
     * @return self
     */
    public function addServer($server, $port = 11211, $weight = 0): HealthCheck
    {
        $this->servers[] = [$server, $port, $weight];

        return $this;
    }

    /**
     * @return float
     */
    public function duration(): float
    {
        return $this->duration;
    }

    /**
     * Get servers
     *
     * @return array
     */
    public function servers(): array
    {
        return $this->servers;
    }
}
