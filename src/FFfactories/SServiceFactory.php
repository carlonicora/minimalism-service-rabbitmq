<?php
namespace carlonicora\minimalism\services\rabbitMq\FFfactories;

use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use carlonicora\minimalism\services\rabbitMq\CConfigurations\RRabbitMqConfigurations;
use carlonicora\minimalism\services\rabbitMq\RRabbitMq;

class SServiceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new RRabbitMqConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return mixed|void
     */
    public function create(servicesFactory $services) {
        return new RRabbitMq($this->configData, $services);
    }
}