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
        if (!getenv('RABBITMQ_QUEUE_NAME')) {
            throw new configurationException('rabbitMq', 'RABBITMQ_QUEUE_NAME is a required configuration');
        }

        if (!($rabbitMqConnection = getenv('RABBITMQ'))) {
            throw new configurationException('rabbitMq', 'RABBITMQ is a required configuration');
        }

        $this->queueName = getenv('RABBITMQ_QUEUE_NAME');

        $this->rabbitMqConnection = [];
        [
            $this->rabbitMqConnection['host'],
            $this->rabbitMqConnection['port'],
            $this->rabbitMqConnection['user'],
            $this->rabbitMqConnection['password']
        ] = explode(',', $rabbitMqConnection);
    }
}