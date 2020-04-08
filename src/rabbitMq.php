<?php
namespace carlonicora\minimalism\services\rabbitMq;

use carlonicora\minimalism\core\services\abstracts\abstractService;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\core\services\interfaces\serviceConfigurationsInterface;
use Exception;
use carlonicora\minimalism\services\rabbitMq\configurations\rabbitMqConfigurations;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class rabbitMq extends abstractService {
    /** @var rabbitMqConfigurations  */
    public rabbitMqConfigurations $configData;

    /** @var AMQPStreamConnection*/
    private ?AMQPStreamConnection $connection=null;

    /**
     * abstractApiCaller constructor.
     * @param serviceConfigurationsInterface $configData
     * @param servicesFactory $services
     */
    public function __construct(serviceConfigurationsInterface $configData, servicesFactory $services) {
        parent::__construct($configData, $services);

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->configData = $configData;
    }

    /**
     * @throws Exception
     */
    public function __destruct() {
        if ($this->connection !== null) {
            $this->connection->channel()->close();
            try {
                $this->connection->close();
            } catch (Exception $e) {

            }
        }
    }

    /**
     * @return AMQPStreamConnection
     */
    private function connection() : AMQPStreamConnection {
        if ($this->connection === null) {
            $this->connection = new AMQPStreamConnection(
                $this->configData->rabbitMqConnection['host'],
                $this->configData->rabbitMqConnection['port'],
                $this->configData->rabbitMqConnection['user'],
                $this->configData->rabbitMqConnection['password']);
        }

        return $this->connection;
    }

    /**
     * @param $callback
     */
    public function initialiseDispatcher(&$callback): void {
        $this->connection()->channel()->queue_declare($this->configData->queueName, false, true, false, false);

        $this->connection()->channel()->basic_qos(null, 1, null);
        $this->connection()->channel()->basic_consume($this->configData->queueName , '', false, false, false, false, $callback);
    }

    /**
     * @param array $message
     * @return bool
     */
    public function dispatchMessage(array $message): bool {
        $this->connection()->channel()->queue_declare($this->configData->queueName, false, true, false, false);

        $jsonMessage = json_encode($message, JSON_THROW_ON_ERROR, 512);
        $msg = new AMQPMessage(
            $jsonMessage,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->connection()->channel()->basic_publish($msg, '', $this->configData->queueName);

        return true;
    }

    /**
     * @param int $delay delay in seconds
     * @param array $message
     * @return bool
     */
    public function dispatchDelayedMessage(array $message, int $delay): bool {
        $this->connection()->channel()->queue_declare($this->configData->queueName, false, true, false, false);

        $jsonMessage = json_encode($message, JSON_THROW_ON_ERROR, 512);
        $msg = new AMQPMessage(
            $jsonMessage,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => new AMQPTable([
                    'x-delay' => $delay * 1000
                ])
            ]
        );

        $this->connection()->channel()->basic_publish($msg, '', $this->configData->queueName);

        return true;
    }

    /**
     * @return AMQPChannel
     */
    public function channel() : AMQPChannel {
        return $this->connection()->channel();
    }

    /**
     * @return int
     */
    public function currentQueueLength() : int {
        return count($this->connection()->channel()->callbacks);
    }
}