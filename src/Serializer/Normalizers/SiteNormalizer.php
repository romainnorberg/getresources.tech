<?php

namespace App\Serializer\Normalizers;

use App\Entity\Site;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class SiteNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /* @var $object \App\Entity\Site */
        return [
            'name'        => $object->getName(),
            'url'         => $object->getUrl(),
            'description' => $object->getDescription(),
            'slug'        => $object->getSlug(),
            'author'      => $object->getAuthor(),
            'authorUrl'   => $object->getAuthorUrl(),
            'rate'        => $object->getRate(),
            'hit'         => $object->getHit(),
            'parentId'    => $object->getParent() ? $object->getParent()->getId() : null,
            'publishedAt' => $object->getPublishedAt(),
            'isValidated' => $object->getisValidated(),
            'validatedAt' => $object->getValidatedAt(),
            'created'     => $this->serializer->normalize($object->getCreated(), $format, $context),
            'updated'     => $this->serializer->normalize($object->getUpdated(), $format, $context),
            'isHighlight' => $object->getisHighlight(),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Site;
    }
}