<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require 'vendor/autoload.php';
require __DIR__ . '/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Annotation/Timestampable.php';
require __DIR__ . '/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Mapping/Annotation/SoftDeleteable.php';

$settings = include 'app/settings.php';
$settings = $settings['settings']['doctrine'];

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    $settings['meta']['entity_path'],
    $settings['meta']['auto_generate_proxies'],
    $settings['meta']['proxy_dir'],
    $settings['meta']['cache'],
    false
);

$em = \Doctrine\ORM\EntityManager::create($settings['connection'], $config);

return ConsoleRunner::createHelperSet($em);