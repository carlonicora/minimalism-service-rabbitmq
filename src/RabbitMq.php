<?php
namespace CarloNicora\Minimalism\Services\RabbitMq;

use CarloNicora\Minimalism\Abstracts\AbstractService;
use Exception;
use JsonException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMq extends AbstractService
{
    /** @var string  */
    private string $host;

    /** @var int  */
    private int $port;

    /** @var string  */
    private string $user;

    /** @var string|null  */
    private ?string $password;

    /** @var AMQPStreamConnection|null */
    private ?AMQPStreamConnection $connection=null;

    /**
     * RabbitMq constructor.
     * @param string $MINIMALISM_SERVICE_RABBITMQ
     */
    public function __construct(
        string $MINIMALISM_SERVICE_RABBITMQ,
    )
    {
        [
            $this->host,
            $this->port,
            $this->user,
            $this->password
        ] = explode(',', $MINIMALISM_SERVICE_RABBITMQ);
    }

    /**
     * @return AMQPChannel
     * @throws Exception
     */
    private function getChannel() : AMQPChannel {
        if ($this->connection === null){
            $this->connect();
        } else {
            try {
                $this->connection->checkHeartBeat();
            } catch (Exception) {
                $this->connect();
            }
        }

        return $this->connection->channel();
    }

    /**
     * @throws Exception
     */
    public function connect(): void
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user,$this->password);
    }

    /**
     * @param callable $callback
     * @param string $queueName
     * @throws Exception
     */
    public function listen(callable $callback, string $queueName): void
    {
        $channel = $this->getChannel();
        /** @noinspection UnusedFunctionResultInspection */
        $channel->queue_declare($queueName, false, true, false, false);

        $channel->basic_qos(null, 1, null);
        /** @noinspection UnusedFunctionResultInspection */
        $channel->basic_consume($queueName, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            try {
                $channel->wait();
            } catch (Exception){
                $channel->close();
                $this->connection->close();
                $this->connection = null;
                $this->listen($callback, $queueName);
            }
        }
        $channel->close();
    }

    /**
     * @param string $queueName
     * @throws Exception
     */
    public function purge(string $queueName): void
    {
        $channel = $this->getChannel();
        $channel->queue_purge($queueName);
        $channel->close();
    }

    /**
     * @param string $queueName
     * @return int
     */
    public function countMessagesInQueue(string $queueName): int
    {
        $messageCount = 0;
        try {
            $channel = $this->getChannel();
            [,$messageCount,] = $channel->queue_declare($queueName, true);
        } catch (Exception $e) {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            if ($e->amqp_reply_code === 404){
                $messageCount = 0;
            }
        }

        return $messageCount;
    }

    /**
     * @param string $queueName
     * @return bool
     */
    public function isQueueEmpty(string $queueName): bool {
        $messageCount = 0;
        try {
            $channel = $this->getChannel();
            /** @noinspection PhpUnusedLocalVariableInspection */
            [$queue, $messageCount, $consumerCount] = $channel->queue_declare($queueName, true);
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
     * @throws Exception
     */
    public function dispatchMessage(array $message, string $queueName): bool {
        $channel = $this->getChannel();
        /** @noinspection UnusedFunctionResultInspection */
        $channel->queue_declare($queueName, false, true, false, false);

        $jsonMessage = json_encode($message, JSON_THROW_ON_ERROR);
        $msg = new AMQPMessage(
            $jsonMessage,
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $channel->basic_publish($msg, '', $queueName);
        $channel->close();

        return true;
    }

    /**
     * @param array $message
     * @param string $queueName
     * @param int $delay delay in seconds
     * @return bool
     * @throws JsonException
     * @throws Exception
     */
    public function dispatchDelayedMessage(array $message, string $queueName, int $delay): bool {
        $channel = $this->getChannel();
        /** @noinspection UnusedFunctionResultInspection */
        $channel->queue_declare($queueName, false, true, false, false);

        $jsonMessage = json_encode($message, JSON_THROW_ON_ERROR);
        $msg = new AMQPMessage(
            $jsonMessage,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'application_headers' => new AMQPTable([
                    'x-delay' => $delay * 1000
                ])
            ]
        );

        $channel->basic_publish($msg, '', $queueName);
        $channel->close();

        return true;
    }

    /**
     *
     */
    public function destroy(): void
    {

        if ($this->connection !== null) {
            $this->connection->channel()->close();
            try {
                $this->connection->close();
            } catch (Exception) {
            }

            $this->connection = null;
        }
    }
}