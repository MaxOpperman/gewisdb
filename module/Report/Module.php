<?php
namespace Report;

use Zend\ModuleManager\ModuleManagerInterface;
use Zend\EventManager\EventInterface;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

class Module
{

    /**
     * Init.
     */
    public function init(ModuleManagerInterface $manager)
    {
        $events = $manager->getEventManager();

        $events->getSharedManager()->attach('doctrine', 'loadCli.post', array($this, 'initializeDoctrineConsole'), -100);
    }

    /**
     * Initialize doctrine console.
     */
    public function initializeDoctrineConsole(EventInterface $event) {
        $cli = $event->getTarget();
        $sm = $event->getParam('ServiceManager');

        $em = $sm->get('doctrine.entitymanager.orm_report');
        $helperSet = $cli->getHelperSet();
        $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');
        $helperSet->set(new EntityManagerHelper($em), 'em');
    }

    /**
     * Get the autoloader configuration.
     *
     * @return array Autoloader config
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                )
            )
        );
    }

    /**
     * Get the configuration for this module.
     *
     * @return array Module configuration
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Get service configuration.
     *
     * @return array Service configuration
     */
    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
            ),
            'factories' => array(
                'doctrine.connection.orm_report' => new \DoctrineORMModule\Service\DBALConnectionFactory('orm_report'),
                'doctrine.configuration.orm_report' => new \DoctrineORMModule\Service\ConfigurationFactory('orm_report'),
                'doctrine.entitymanager.orm_report' => new \DoctrineORMModule\Service\EntityManagerFactory('orm_report'),
                'doctrine.driver.orm_report' => new \DoctrineModule\Service\DriverFactory('orm_report'),
                'doctrine.eventmanager.orm_report' => new \DoctrineModule\Service\EventManagerFactory('orm_report'),
                'doctrine.entity_resolver.orm_report' => new \DoctrineORMModule\Service\EntityResolverFactory('orm_report'),
                'doctrine.sql_logger_collector.orm_report' => new \DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_report'),
                'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function($sm) {
                    return new \DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_report'));
                }
            )
        );
    }
}