<?php
namespace carlonicora\minimalism\services\rabbitMq\configurations;

use carlonicora\minimalism\core\services\abstracts\abstractServiceConfigurations;
use carlonicora\minimalism\core\services\exceptions\configurationException;

class rabbitMqConfigurations extends abstractServiceConfigurations {
    /** @var string  */
    public string $queueName;

    /** @var array  */
    public array $rabbitMqConnection;

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

        $this->rabbitMqConnection = [];
        [
            $this->rabbitMqConnection['host'],
            $this->rabbitMqConnection['port'],
            $this->rabbitMqConnection['user'],
            $this->rabbitMqConnection['password']
        ] = explode(',', $rabbitMqConnection);
    }
}