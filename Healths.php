<?php
declare(strict_types=1);

namespace common\models\HealthCheck;

use common\helpers\FormatHelper;
use common\models\HealthCheck\Exceptions\HealthWarningException;

/**
 * Collection of health checks to run.
 */
class Healths
{
    /** @var array */
    protected $items = [];

    /** @var */
    protected $generalDuration;

    /**
     * @param HealthCheck[] $healthChecks
     */
    public function __construct($healthChecks = [])
    {
        $this->items = $healthChecks;
    }

    /**
     * Run the health checks in the stack
     */
    public function run(): ResultStack
    {
        $results = [];

        $startTime = microtime(true);
        foreach ($this->items as $check) {
            $resultCode = HealthResult::RESULT_SUCCESS;

            try {
                $check->run();
            } catch (\Throwable $e) {
                $check->setStatus($e->getMessage());
                $resultCode = $e instanceof HealthWarningException
                    ? HealthResult::RESULT_WARNING : HealthResult::RESULT_FAILURE;
            }

            $results[] = new HealthResult($resultCode, $check);
        }
        $this->generalDuration = microtime(true) - $startTime;

        return new ResultStack($results);
    }

    /**
     * @return string
     */
    public function getHumanReadableDuration(): string
    {
        return FormatHelper::formatSeconds($this->generalDuration);
    }
}
