<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

// Doctrine
$container['em'] = function ($c) {
    $cache = new Doctrine\Common\Cache\ArrayCache;
    $annotationReader = new Doctrine\Common\Annotations\AnnotationReader;
    $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
        $annotationReader,
        $cache
    );
    $evm = new Doctrine\Common\EventManager();
    $timestampableListener = new Gedmo\Timestampable\TimestampableListener();
    $timestampableListener->setAnnotationReader($cachedAnnotationReader);
    $evm->addEventSubscriber($timestampableListener);
    $softDeleteableListener = new Gedmo\SoftDeleteable\SoftDeleteableListener();
    $softDeleteableListener->setAnnotationReader($cachedAnnotationReader);
    $evm->addEventSubscriber($softDeleteableListener);
    $settings = $c->get('settings');
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['doctrine']['meta']['entity_path'],
        $settings['doctrine']['meta']['auto_generate_proxies'],
        $settings['doctrine']['meta']['proxy_dir'],
        $cache,
        $settings['doctrine']['meta']['cache']
    );
    $config->addFilter('soft-deleteable', 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter');
    $entityManager = \Doctrine\ORM\EntityManager::create($settings['doctrine']['connection'], $config, $evm);
    $entityManager->getFilters()->enable('soft-deleteable');
    return $entityManager;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container['App\Action\HomeAction'] = function ($c) {
    return new App\Action\HomeAction($c->get('view'), $c->get('logger'), $c->get('em'));
};

$container['App\Action\BoxAction'] = function ($c) {
    return new App\Action\BoxAction($c->get('view'), $c->get('logger'), $c->get('em'), $c->get('router'));
};

$container['App\Action\TicketAction'] = function ($c) {
    return new App\Action\TicketAction($c->get('view'), $c->get('logger'), $c->get('em'), $c->get('router'));
};
