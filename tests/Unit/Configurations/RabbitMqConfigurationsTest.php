<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Tests\Unit\Configurations;

use CarloNicora\Minimalism\Services\RabbitMq\Configurations\RabbitMqConfigurations;
use CarloNicora\Minimalism\Services\RabbitMq\Tests\Abstracts\AbstractTestCase;

class RabbitMqConfigurationsTest extends AbstractTestCase
{
    public function testUnconfiguredConfiguration() : void
    {
        $this->expectExceptionCode(500);

        new RabbitMqConfigurations();
    }

    public function testIncompleteConfigurationHost() : void
    {
        $this->expectExceptionCode(500);

        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password');
        new RabbitMqConfigurations();
    }

    public function testConfiguredConfigurationHost() : void
    {
        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password,queue');
        $config = new RabbitMqConfigurations();

        $this->assertEquals('host', $config->getHost());
    }

    public function testConfiguredConfigurationPassword() : void
    {
        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password,queue');
        $config = new RabbitMqConfigurations();

        $this->assertEquals('password', $config->getPassword());
    }

    public function testConfiguredConfigurationUser() : void
    {
        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password,queue');
        $config = new RabbitMqConfigurations();

        $this->assertEquals('user', $config->getUser());
    }

    public function testConfiguredConfigurationPort() : void
    {
        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password,queue');
        $config = new RabbitMqConfigurations();

        $this->assertEquals(123, $config->getPort());
    }

    public function testConfiguredConfigurationQueueName() : void
    {
        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password,queue');
        $config = new RabbitMqConfigurations();

        $this->assertEquals('queue', $config->getQueueName());
    }
}