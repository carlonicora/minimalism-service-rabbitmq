<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Tests\Unit;

use CarloNicora\Minimalism\Services\RabbitMq\RabbitMq;
use CarloNicora\Minimalism\Services\RabbitMq\Tests\Abstracts\AbstractTestCase;

class RabbitMqTest extends AbstractTestCase
{
    /** @var RabbitMq|null  */
    private ?RabbitMq $rabbitMq=null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rabbitMq = new RabbitMq($this->getRabbitMqConfigurations(), $this->getServices());
    }

    public function testCleanNonPersistentVariables() : void
    {
        $this->rabbitMq->cleanNonPersistentVariables();
        $this->assertEquals(1,1);
    }

    public function testRedisConnectionException() : void
    {
        $this->setProperty($this->rabbitMq, 'connection', $this->generateConnection());

        $this->rabbitMq->isQueueEmpty();

        $this->assertEquals(1,1);
    }
}