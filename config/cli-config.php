<?php

// config for vendor/bin/doctrine*

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__.'/bootstrap.php';

/**
 * @var \Symfony\Component\DependencyInjection\ContainerInterface
 * @var EntityManagerInterface                                    $entityManager
 */
$entityManager = $container->get(EntityManagerInterface::class);

return ConsoleRunner::createHelperSet($entityManager);
