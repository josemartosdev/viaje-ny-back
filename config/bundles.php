<?php

$bundles = [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
];

if (class_exists('Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle')) {
    $bundles['Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle'] = ['dev' => true, 'test' => true];
}

if (class_exists('Symfony\Bundle\MakerBundle\MakerBundle')) {
    $bundles['Symfony\Bundle\MakerBundle\MakerBundle'] = ['dev' => true];
}

return $bundles;
