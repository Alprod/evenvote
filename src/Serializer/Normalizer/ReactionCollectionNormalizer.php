<?php

namespace App\Serializer\Normalizer;

use App\Repository\ReactionRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReactionCollectionNormalizer implements NormalizerInterface
{
    public function __construct(
		private readonly NormalizerInterface $normalizer,
		private readonly ReactionRepository $reactionRepo) {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
		$data += $this->reactionRepo->countReaction($context['subresource_identifiers']['id']);
		unset($data['hydra:member']);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return 'api_sessions_get_subresource' === ($context['subresource_operation_name'] ?? null)
	        && false === ($context['api_sub_level'] ?? null)
	        && $this->normalizer->supportsNormalization($data,$format,$data);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
