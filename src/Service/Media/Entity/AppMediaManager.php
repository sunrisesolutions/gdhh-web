<?php
namespace App\Service\Media\Entity;

use AppBundle\Entity\Media\Media;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppMediaManager extends MediaManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string $class
     * @param ManagerRegistry $registry
     */
    public function __construct($class, ManagerRegistry $registry, ContainerInterface $container)
    {
        parent::__construct($class, $registry);
        $this->container = $container;
    }

    public function getMediaUrl(Media $medium, $format = 'reference')
    {
        $fileProvider = $this->container->get('sonata.media.provider.file');
        return $fileProvider->generatePublicUrl($medium, $format);
    }

    public function getThumbnailUrl(Media $medium, $format)
    {
        $fileProvider = $this->container->get('sonata.media.provider.file');
        $imageProvider = $this->container->get('sonata.media.provider.image');
        $thumbnail = $medium->getThumbnail();

        if ($format !== 'small') {
            $format = 'reference';
        } else {
            $format = $this->container->getParameter('thumbnail_context') . '_' . $format;
        }

        if ($medium->getMediaExtension() === 'mp4') {
            if (empty($thumbnail)) {
                $fileProvider->generateThumbnails($medium);
                $thumbnail = $medium->getThumbnail();
            }
            return $imageProvider->generatePublicUrl($thumbnail, $format);
        } elseif ($medium->getProviderName() === 'sonata.media.provider.image') {
            return $imageProvider->generatePublicUrl($medium, $format);
        } elseif ($medium->getMediaExtension() === 'youtube') {
            $metadata = $medium->getProviderMetadata();
            return $metadata['thumbnail_url'];
        }
    }

}