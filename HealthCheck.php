<?php
declare(strict_types=1);

namespace common\models\HealthCheck;

abstract class HealthCheck
{
    /** @var string|null Title for the health check */
    protected $title;

    /** @var string|null Brief description of the health check */
    protected $description;

    /** @var string|null Status message for the health check */
    protected $status;

    /** @var float Duration of check executable */
    protected $duration;

    /**
     * Run the health check.
     *
     * If no exception is thrown we will consider the health check successful.
     */
    abstract public function run();

    /**
     * Get duration of check executable in seconds with milliseconds. Example: 1.23901972
     *
     * @return float
     */
    abstract public function duration(): float;

    /**
     * Get the title of the health check.
     *
     * If not $title is set on the class, the class name will be used as a default.
     *
     * @return string
     */
    public function title(): string
    {
        $title = $this->title;

        if (!$title) {
            $classTitle = explode('\\', get_class($this));
            $title = array_pop($classTitle);
        }

        return $title;
    }

    /**
     * Set the title for the health check
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get description for the health check.
     *
     * @return null|string
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description for the health check
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the status of the health check
     *
     * If an exception is thrown, the status message for a health check will be replaced with the exceptions message.
     *
     * @return null|string
     */
    public function status(): ?string
    {
        return $this->status;
    }

    /**
     * Set the status of the health check
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
