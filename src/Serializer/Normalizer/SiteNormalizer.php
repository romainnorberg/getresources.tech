<?php

namespace App\Serializer\Normalizer;

use App\Entity\Site;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class SiteNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var UrlGeneratorInterface
     */
    private $UrlGeneratorInterface;

    public function __construct(UrlGeneratorInterface $UrlGeneratorInterface)
    {
        $this->UrlGeneratorInterface = $UrlGeneratorInterface;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        /* @var $object \App\Entity\Site */
        $site = [
            'name'        => $object->getName(),
            'url'         => $object->getUrl(),
            'description' => $object->getDescription(),
            'slug'        => $object->getSlug(),
            'author'      => $object->getAuthor(),
            'authorUrl'   => $object->getAuthorUrl(),
            'rate'        => $object->getRate(),
            'hit'         => $object->getHit(),
            'parentId'    => $object->getParent() ? $object->getParent()->getId() : null,
            'publishedAt' => $this->serializer->normalize($object->getPublishedAt(), $format, $context),
            'isValidated' => $object->getisValidated(),
            'validatedAt' => $this->serializer->normalize($object->getValidatedAt(), $format, $context),
            'created'     => $this->serializer->normalize($object->getCreated(), $format, $context),
            'updated'     => $this->serializer->normalize($object->getUpdated(), $format, $context),
            'isHighlight' => $object->getisHighlight(),
            'link'        => $this->UrlGeneratorInterface->generate('site_hit_open', [
                'siteSlug' => $object->getSlug(),
            ]),
        ];

        return $site;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Site;
    }
}