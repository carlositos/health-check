<?php
declare(strict_types=1);

namespace common\models\HealthCheck\Checks;

use common\models\HealthCheck\HealthCheck;
use yii\db\Connection;
use yii\db\Exception;

class SelectQueryHealthCheck extends HealthCheck
{
    /** @var string */
    protected $title = 'mysql:select';

    /** @var Connection */
    protected $db;

    /** @var string */
    protected $sql;

    /**
     * @param $sql
     * @param Connection|null $db
     */
    public function __construct($sql, Connection $db = null)
    {
        $this->sql = $sql;
        $this->db = $db;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $startTime = microtime(true);

        try {
            $result = $this->db->createCommand($this->sql)->queryAll();

            if (empty($result)) {
                throw new \Exception('Query result is empty');
            }
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        } finally {
            $this->duration = microtime(true) - $startTime;
        }
    }

    /**
     * @return float
     */
    public function duration(): float
    {
        return $this->duration;
    }

    /**
     * If no description is set, we will use the sql query
     *
     * @return string|null
     */
    public function description(): ?string
    {
        $description = $this->description;

        if (!$description) {
            $description = (string) $this->sql;
        }

        return $description;
    }
}
