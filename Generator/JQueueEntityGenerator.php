<?php

namespace An1zhegorodov\JQueueBundle\Generator;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class JQueueEntityGenerator extends Generator
{
    private $filesystem;
    private $registry;

    public function __construct(Filesystem $filesystem, RegistryInterface $registry)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
    }

    public function generate(BundleInterface $bundle, $entity, $table, $repository)
    {
        // configure the bundle (needed if the bundle does not contain any Entities yet)
        $config = $this->registry->getManager(null)->getConfiguration();
        $config->setEntityNamespaces(array_merge(
            array($bundle->getName() => $bundle->getNamespace().'\\Entity'),
            $config->getEntityNamespaces()
        ));

        $entityClass = $this->registry->getAliasNamespace($bundle->getName()).'\\'.$entity;
        $entityPath = $bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php';
        if (file_exists($entityPath)) {
            throw new \RuntimeException(sprintf('Entity "%s" already exists.', $entityClass));
        }

        $class = new ClassMetadataInfo($entityClass);
        $class->setPrimaryTable($table);
        if ($repository) {
            $class->customRepositoryClassName = $repository.'Repository';
        }

        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->setClassToExtend('An1zhegorodov\JQueueBundle\Entity\Job');
        $entityCode = $entityGenerator->generateEntityClass($class);

        $this->filesystem->mkdir(dirname($entityPath));
        file_put_contents($entityPath, $entityCode);
    }

    protected function getEntityGenerator()
    {
        $entityGenerator = new EntityGenerator();
        $entityGenerator->setGenerateAnnotations(true);
        $entityGenerator->setGenerateStubMethods(true);
        $entityGenerator->setRegenerateEntityIfExists(false);
        $entityGenerator->setUpdateEntityIfExists(true);
        $entityGenerator->setNumSpaces(4);
        $entityGenerator->setAnnotationPrefix('ORM\\');

        return $entityGenerator;
    }
}