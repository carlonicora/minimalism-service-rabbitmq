<?php
namespace CarloNicora\Minimalism\Services\RabbitMq;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractService;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Core\Services\Interfaces\ServiceConfigurationsInterface;
use ErrorException;
use Exception;
use CarloNicora\Minimalism\Services\RabbitMq\Configurations\RabbitMqConfigurations;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMq extends AbstractService {
    /** @var RabbitMqConfigurations  */
    public RabbitMqConfigurations $configData;

    /** @var AMQPStreamConnection|null */
    private ?AMQPStreamConnection $connection=null;

    /**
     * abstractApiCaller constructor.
     * @param ServiceConfigurationsInterface $configData
     * @param ServicesFactory $services
     */
    public function __construct(ServiceConfigurationsInterface $configData, ServicesFactory $services) {
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
     * @return AMQPChannel
     */
    private function getChannel() : AMQPChannel {
        if ($this->connection === null){
            $this->connection = new AMQPStreamConnection(
                $this->configData->getHost(),
                $this->configData->getPort(),
                $this->configData->getUser(),
                $this->configData->getPassword());
        }
        return $this->connection->channel();
    }


    /**
     * @param callable $callback
     * @param string $queueName
     * @throws ErrorException
     */
    public function listen($callback, string $queueName): void {
        $this->getChannel()->queue_declare($queueName, false, true, false, false);

        $this->getChannel()->basic_qos(null, 1, null);
        $this->getChannel()->basic_consume($queueName , '', false, false, false, false, $callback);

        while(count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }
        $this->getChannel()->close();
        $this->connection->close();
    }

    /**
     * @param string $queueName
     * @return bool
     */
    public function isQueueEmpty(string $queueName): bool {
        $messageCount = 0;
        try {
            /** @noinspection PhpUnusedLocalVariableInspection */
            [$queue, $messageCount, $consumerCount] = $this->getChannel()->queue_declare($queueName, true);
        } catch (Exception $e) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            if ($e->amqp_reply_code === 404){
                $messageCount = 0;
            }
        }

        return $messageCount === 0;
    }


    /**
     * @param array $message
     * @param string $queueName
     * @return bool
     * @throws JsonException
     */
    public function dispatchMessage(array $message, string $queueName): bool {
        $this->getChannel()->queue_declare($queueName, false, true, false, false);

        $jsonMessage = json_encode($message, JSON_THROW_ON_ERROR, 512);
        $msg = new AMQPMessage(
            $jsonMessage,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->getChannel()->basic_publish($msg, '', $queueName);

        return true;
    }

    /**
     * @param array $message
     * @param string $queueName
     * @param int $delay delay in seconds
     * @return bool
     * @throws JsonException
     */
    public function dispatchDelayedMessage(array $message, string $queueName, int $delay): bool {
        $this->getChannel()->queue_declare($queueName, false, true, false, false);

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

        $this->getChannel()->basic_publish($msg, '', $queueName);

        return true;
    }

    /**
     *
     */
    public function cleanNonPersistentVariables(): void {
        $this->connection = null;
    }
}