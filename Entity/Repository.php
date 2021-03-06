<?php

namespace Modera\FileRepositoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gaufrette\Filesystem;
use Modera\FileRepositoryBundle\Exceptions\InvalidRepositoryConfig;
use Sli\AuxBundle\Util\Toolkit;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Every repository is associated one underlying Gaufrette filesystem.
 *
 * @ORM\Entity
 * @ORM\Table("modera_filerepository_repository")
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Repository
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Stores configuration for this repository. Some standard configuration properties:
     *
     *  * filesystem  -- DI service ID which points to \Gaufrette\Filesystem which will be used to store files for
     *                   this repository.
     *  * storage_key_generator -- DI service ID of class which implements {@class StorageKeyGeneratorInterface}.
     *
     * @ORM\Column(type="array")
     *
     * @var array
     */
    private $config = array();

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="StoredFile", mappedBy="repository", cascade={"REMOVE"})
     */
    private $files;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param $name
     * @param array $config
     */
    public function __construct($name, array $config)
    {
        $this->name = $name;
        $this->setConfig($config);

        $this->files = new ArrayCollection();
    }

    private function getInterceptors()
    {

    }

    public function beforePut(\SplFileInfo $file)
    {

    }

    public function onPut(StoredFile $storedFile, \SplFileInfo $file)
    {

    }

    public function afterPut(StoredFile $storedFile, \SplFileInfo $file)
    {

    }

    /**
     * @param ContainerInterface $container
     */
    public function init(ContainerInterface $container)
    {
        $this->container = $container;
    }

    static public function clazz()
    {
        return get_called_class();
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        $map = $this->container->get('knp_gaufrette.filesystem_map');

        return $map->get($this->config['filesystem']);
    }

    /**
     * @param \SplFileInfo $file
     * @param array $context
     *
     * @return string
     */
    public function generateStorageKey(\SplFileInfo $file, array $context)
    {
        return $this->container->get($this->config['storage_key_generator'])->generateStorageKey($file, $context);
    }

    /**
     * @param \SplFileInfo $file
     * @param array $context
     *
     * @return StoredFile
     */
    public function createFile(\SplFileInfo $file, array $context = array())
    {
        $result = new StoredFile($this, $file, $context);
        $this->files->add($result);

        return $result;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        if (!isset($config['filesystem'])) {
            throw InvalidRepositoryConfig::create('filesystem', $config);
        }
        if (!isset($config['storage_key_generator'])) {
            $config['storage_key_generator'] = 'modera_file_repository.repository.uniqid_key_generator';
        }

        $this->config = $config;
    }

    // boilerplate:

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return StoredFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }
} 