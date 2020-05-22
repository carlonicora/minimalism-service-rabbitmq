<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Tests\Unit\Factories;

use CarloNicora\Minimalism\Services\RabbitMq\Configurations\RabbitMqConfigurations;
use CarloNicora\Minimalism\Services\RabbitMq\Factories\ServiceFactory;
use CarloNicora\Minimalism\Services\RabbitMq\RabbitMq;
use CarloNicora\Minimalism\Services\RabbitMq\Tests\Abstracts\AbstractTestCase;

class ServiceFactoryTest extends AbstractTestCase
{
    /**
     * @return ServiceFactory
     */
    public function testServiceInitialisation() : ServiceFactory
    {
        $response = new ServiceFactory($this->getServices());

        $this->assertEquals(1,1);

        return $response;
    }

    /**
     * @param ServiceFactory $service
     * @depends testServiceInitialisation
     */
    public function testServiceCreation(ServiceFactory $service) : void
    {
        $this->setEnv('MINIMALISM_SERVICE_RABBITMQ', 'host,123,user,password,queue');
        $config = new RabbitMqConfigurations();
        $services = $this->getServices();
        $rabbitmq = new RabbitMq($config, $services);

        $this->assertEquals($rabbitmq, $service->create($services));
    }
}