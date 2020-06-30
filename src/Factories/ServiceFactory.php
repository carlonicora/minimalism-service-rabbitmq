<?php
namespace CarloNicora\Minimalism\Services\RabbitMq\Factories;

use CarloNicora\Minimalism\Core\Services\Abstracts\AbstractServiceFactory;
use CarloNicora\Minimalism\Core\Services\Exceptions\ConfigurationException;
use CarloNicora\Minimalism\Core\Services\Factories\ServicesFactory;
use CarloNicora\Minimalism\Services\RabbitMq\Configurations\RabbitMqConfigurations;
use CarloNicora\Minimalism\Services\RabbitMq\RabbitMq;
use Exception;

class ServiceFactory extends AbstractServiceFactory {
    /**
     * serviceFactory constructor.
     * @param ServicesFactory $services
     * @throws ConfigurationException
     * @throws Exception
     */
    public function __construct(ServicesFactory $services) {
        $this->configData = new RabbitMqConfigurations();

        parent::__construct($services);
    }

    /**
     * @param servicesFactory $services
     * @return mixed|void
     */
    public function create(servicesFactory $services) {
        return new RabbitMq($this->configData, $services);
    }
}