<?php
namespace App\Service\Media\Provider;

use Gaufrette\Filesystem;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\FileProvider;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class AppFileProvider extends FileProvider
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array(), MetadataBuilderInterface $metadata = null,ContainerInterface $container)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail,$allowedExtensions,$allowedMimeTypes,$metadata);
        $this->container = $container;
    }


    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($format == 'reference') {
            $path = $this->getReferenceImage($media);
        } else {
            // @todo: fix the asset path
            $path = sprintf('sonatamedia/files/%s/file.png', $format);
        }
        $path = $this->container->getParameter('s3_directory') . '/' . $path;
        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }
}