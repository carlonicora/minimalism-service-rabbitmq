<?php
namespace phlow\library\services\rabbitMq\factories;

use carlonicora\minimalism\core\services\abstracts\abstractServiceFactory;
use carlonicora\minimalism\core\services\exceptions\configurationException;
use carlonicora\minimalism\core\services\factories\servicesFactory;
use phlow\library\services\rabbitMq\configurations\rabbitMqConfigurations;
use phlow\library\services\rabbitMq\rabbitMq;

class serviceFactory extends abstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param servicesFactory $services
     * @throws configurationException
     */
    public function __construct(servicesFactory $services) {
        $this->configData = new rabbitMqConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return mixed|void
     */
    public function create(servicesFactory $services) {
        return new rabbitMq($this->configData, $services);
    }
}