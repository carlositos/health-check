<?php
declare(strict_types=1);

namespace common\models\HealthCheck\Checks;

use common\models\HealthCheck\HealthCheck;
use yii\db\Connection;
use yii\db\Exception;

class MysqlHealthCheck extends HealthCheck
{
    /** @var string */
    protected $title = 'mysql';

    /** @var string */
    protected $description = 'Check the mysql database connection.';

    /** @var Connection */
    protected $db;

    /**
     * @param Connection|null $db
     */
    public function __construct(Connection $db = null)
    {
        $this->db = $db;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $startTime = microtime(true);

        try {
            $this->db->open();
            $this->db->getMasterPdo();
            $this->db->close();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } finally {
            $this->duration = microtime(true) - $startTime;
        }

        $this->setStatus('connected');
    }

    /**
     * @return float
     */
    public function duration(): float
    {
        return $this->duration;
    }
}
