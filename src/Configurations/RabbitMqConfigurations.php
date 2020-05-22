<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Configurations;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceConfigurations;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;

class RabbitMqConfigurations extends AbstractServiceConfigurations {
    /** @var string  */
    private string $queueName;

    /** @var string  */
    private string $host;

    /** @var string  */
    private string $port;

    /** @var string  */
    private string $user;

    /** @var string  */
    private string $password;

    /**
     * rabbitMqConfigurations constructor.
     * @throws configurationException
     */
    public function __construct() {
        if (!getenv('MINIMALISM_SERVICE_RABBITMQ_QUEUE_NAME')) {
            throw new configurationException('rabbitMq', 'MINIMALISM_SERVICE_RABBITMQ_QUEUE_NAME is a required configuration');
        }

        if (!($rabbitMqConnection = getenv('MINIMALISM_SERVICE_RABBITMQ'))) {
            throw new configurationException('rabbitMq', 'MINIMALISM_SERVICE_RABBITMQ is a required configuration');
        }

        $this->queueName = getenv('MINIMALISM_SERVICE_RABBITMQ_QUEUE_NAME');

        [
            $this->host,
            $this->port,
            $this->user,
            $this->password
        ] = explode(',', $rabbitMqConnection);
    }

    /**
     * @return string
     */
    public function getPort(): string
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
    public function getQueueName(): string
    {
        return $this->queueName;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }
}