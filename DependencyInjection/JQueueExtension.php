<?php

namespace An1zhegorodov\JQueueBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class JQueueExtension extends Extension
{
    const DEFAULT_JOB_ID = 32767;

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->processJobTypeConfig($config['job_types'], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    protected function processJobTypeConfig(array $config, ContainerBuilder $container)
    {
        $config[] = array('id' => static::DEFAULT_JOB_ID, 'title' => 'default');
        foreach ($config as $item) {
            $parameter = sprintf('jqueue.job_types.%s', $item['title']);
            $value = $item['id'];
            $container->setParameter($parameter, $value);
        }
    }
}
