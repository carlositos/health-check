<?php
declare(strict_types=1);

namespace common\models\HealthCheck;

use common\helpers\FormatHelper;

class HealthResult
{
    public const RESULT_SUCCESS = 0;
    public const RESULT_WARNING = 1;
    public const RESULT_FAILURE = 2;

    /** @var int Should be one of RESULT constants above */
    protected $result;

    /** @var HealthCheck */
    protected $check;

    /**
     * @param int $result
     * @param HealthCheck $check
     */
    public function __construct(int $result, HealthCheck $check)
    {
        $this->result = $result;
        $this->check = $check;
    }

    /**
     * @return bool
     */
    public function failed(): bool
    {
        return $this->result === self::RESULT_FAILURE;
    }

    /**
     * @return bool
     */
    public function passed(): bool
    {
        return $this->result === self::RESULT_SUCCESS;
    }

    /**
     * @return bool
     */
    public function warned(): bool
    {
        return $this->result === self::RESULT_WARNING;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->check->title();
    }

    /**
     * @return null|string
     */
    public function description(): ?string
    {
        return $this->check->description() . ($this->statusText() ? ' (' . $this->statusText() . ')' : '');
    }

    /**
     * @return string
     */
    public function status(): string
    {
        switch ($this->result) {
            case self::RESULT_SUCCESS:
                $status = 'pass';
                break;

            case self::RESULT_WARNING:
                $status = 'warn';
                break;

            case self::RESULT_FAILURE:
                $status = 'fail';
                break;

            default:
                $status = 'fail';
        }

        return  $status;
    }

    /**
     * @return float
     */
    public function duration(): float
    {
        return $this->check->duration();
    }

    /**
     * Get human readable duration of check executable. Example: 00:00:00.025988
     *
     * @return string
     */
    public function humanReadableDuration(): string
    {
        return FormatHelper::formatSeconds($this->duration());
    }

    /**
     * @return string|null
     */
    private function statusText(): ?string
    {
        return $this->check->status();
    }
}
