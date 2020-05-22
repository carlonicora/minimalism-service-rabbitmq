<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Tests\Abstracts;

use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\RabbitMq\Configurations\RabbitMqConfigurations;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

abstract class AbstractTestCase extends TestCase
{

    /**
     * @return ServicesFactory
     */
    protected function getServices() : ServicesFactory
    {
        return new ServicesFactory();
    }

    /**
     * @param string $name
     * @param string $value
     */
    protected function setEnv(string $name, string $value) : void
    {
        putenv($name.'='.$value);
    }

    /**
     * @param $object
     * @param $parameterName
     * @return mixed|null
     */
    protected function getProperty($object, $parameterName)
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $property = $reflection->getProperty($parameterName);
            $property->setAccessible(true);
            return $property->getValue($object);
        } catch (ReflectionException $e) {
            return null;
        }
    }

    /**
     * @param $object
     * @param $parameterName
     * @param $parameterValue
     */
    protected function setProperty($object, $parameterName, $parameterValue): void
    {
        try {
            $reflection = new ReflectionClass(get_class($object));
            $property = $reflection->getProperty($parameterName);
            $property->setAccessible(true);
            $property->setValue($object, $parameterValue);
        } catch (ReflectionException $e) {
        }
    }

    /**
     * @return RabbitMqConfigurations|MockObject
     */
    protected function getRabbitMqConfigurations() : RabbitMqConfigurations
    {
        /** @var MockObject|RabbitMqConfigurations $response */
        $response = $this->getMockBuilder(RabbitMqConfigurations::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response->method('getHost')
            ->willReturn('host');

        $response->method('getPassword')
            ->willReturn('password');

        $response->method('getPort')
            ->willReturn(123);

        $response->method('getUser')
            ->willReturn('user');

        $response->method('getQueueName')
            ->willReturn('queue');

        return $response;
    }

    /**
     * @return AMQPStreamConnection|MockObject
     */
    protected function generateConnection() : AMQPStreamConnection
    {
        $response = $this->getMockBuilder(AMQPStreamConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channel = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response->method('channel')
            ->willReturn($channel);

        return $response;
    }
}