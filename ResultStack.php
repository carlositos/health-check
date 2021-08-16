<?php
declare(strict_types=1);

namespace common\models\HealthCheck;

class ResultStack
{
    /** @var array */
    protected $items = [];

    /**
     * @param array $results
     */
    public function __construct(array $results = [])
    {
        $this->items = $results;
    }

    /**
     * Determine if any results in the stack have failed
     *
     * @return bool
     */
    public function hasFailures(): bool
    {
        $hasFailure = false;

        foreach ($this->items as $result) {
            /** @var HealthResult $result */
            if ($result->failed()) {
                $hasFailure = true;
                break;
            }
        }

        return $hasFailure;
    }

    /**
     * Determine if any results in the stack have warnings
     *
     * @return bool
     */
    public function hasWarnings(): bool
    {
        $hasWarning = false;

        foreach ($this->items as $result) {
            /** @var HealthResult $result */
            if ($result->warned()) {
                $hasWarning = true;
                break;
            }
        }

        return $hasWarning;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }
}
