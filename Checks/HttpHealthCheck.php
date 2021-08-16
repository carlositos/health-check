<?php
declare(strict_types=1);

namespace common\models\HealthCheck\Checks;

use common\models\HealthCheck\Exceptions\HealthWarningException;
use common\models\HealthCheck\HealthCheck;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

/**
 * Health check for HTTP endpoints
 */
class HttpHealthCheck extends HealthCheck
{
    /** @var string */
    protected $title = 'http';

    /** @var Request */
    protected $request;

    /** @var int */
    protected $expectedStatusCode;

    /** @var bool */
    protected $isWarning;

    /** @var array - will be passed on client->send */
    protected $guzzleOptions;

    /** @var Client */
    protected $guzzle;

    /**
     * HttpHealthCheck constructor.
     * @param Request $request
     * @param int $expectedStatusCode
     * @param book $isWarning
     * @param array $guzzleOptions
     * @param Client|null $guzzle
     */
    public function __construct(
        Request $request,
        $expectedStatusCode = 200,
        $isWarning = false,
        array $guzzleOptions = [],
        Client $guzzle = null
    ) {
        $this->request = $request;
        $this->expectedStatusCode = $expectedStatusCode;
        $this->isWarning = $isWarning;
        $this->guzzleOptions = $guzzleOptions;
        $this->guzzle = $guzzle ?: new Client($this->guzzleOptions);
    }

    /**
     * @return mixed|ResponseInterface|void|null
     * @throws GuzzleException
     * @throws HealthWarningException
     */
    public function run()
    {
        $startTime = microtime(true);

        try {
            $response = $this->guzzle->send(
                $this->request,
                $this->guzzleOptions
            );
        } catch (RequestException $e) {
            if (!$response = $e->getResponse()) {
                $this->throwException($e);
            }
        } finally {
            $this->duration = microtime(true) - $startTime;
        }

        if ($response->getStatusCode() !== $this->expectedStatusCode) {
            $message = "Status code {$response->getStatusCode()} does not match expected {$this->expectedStatusCode}";
            $this->throwException(new Exception($message), $message);
        }

        return $response;
    }

    /**
     * @return float
     */
    public function duration(): float
    {
        return $this->duration;
    }

    /**
     * If no description is set, we will use the request URL
     *
     * @return string|null
     */
    public function description(): ?string
    {
        $description = $this->description;

        if (!$description) {
            $description = (string) $this->request->getUri();
        }

        return $description;
    }

    /**
     * @param Exception $e
     * @param string $message
     * @throws HealthWarningException
     * @throws Exception
     */
    private function throwException(Exception $e, string $message = ''): void
    {
        if ($this->isWarning) {
            throw new HealthWarningException($message);
        }

        throw $e;
    }
}
