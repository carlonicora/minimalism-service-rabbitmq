<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Configurations;

use CarloNicora\Minimalism\Core\Events\MinimalismErrorEvents;
use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use Exception;

class RabbitMqConfigurations extends AbstractServiceConfigurations {
    /** @var string  */
    private string $host;

    /** @var int  */
    private int $port;

    /** @var string  */
    private string $user;

    /** @var string  */
    private string $password;

    /**
     * rabbitMqConfigurations constructor.
     * @throws Exception
     */
    public function __construct() {
        if (!($rabbitMqConnection = getenv('MINIMALISM_SERVICE_RABBITMQ'))) {
            MinimalismErrorEvents::CONFIGURATION_ERROR('MINIMALISM_SERVICE_RABBITMQ')->throw();
        }

        try {
            [
                $this->host,
                $this->port,
                $this->user,
                $this->password
            ] = explode(',', $rabbitMqConnection);
        } catch (Exception $e) {
            MinimalismErrorEvents::CONFIGURATION_ERROR('MINIMALISM_SERVICE_RABBITMQ (incorrect)')->throw();
        }
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }
}