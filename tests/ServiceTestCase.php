<?php


namespace App\Tests;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceTestCase extends WebTestCase
{
    protected ContainerInterface $container;
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->container = static::createClient()->getContainer();
    }
}