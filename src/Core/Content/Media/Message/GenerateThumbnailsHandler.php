<?php declare(strict_types=1);

namespace Shuwei\Core\Content\Media\Message;

use Shuwei\Core\Content\Media\MediaCollection;
use Shuwei\Core\Content\Media\Thumbnail\ThumbnailService;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shuwei\Core\Framework\Feature;
use Shuwei\Core\Framework\Log\Package;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @internal
 */
#[AsMessageHandler]
#[Package('content')]
final class GenerateThumbnailsHandler
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ThumbnailService $thumbnailService,
        private readonly EntityRepository $mediaRepository
    ) {
    }

    public function __invoke(GenerateThumbnailsMessage|UpdateThumbnailsMessage $msg): void
    {
        if (Feature::isActive('v6.6.0.0')) {
            $context = $msg->getContext();
        } else {
            $context = $msg->readContext();
        }

        $criteria = new Criteria();
        $criteria->addAssociation('mediaFolder.configuration.mediaThumbnailSizes');
        $criteria->addFilter(new EqualsAnyFilter('media.id', $msg->getMediaIds()));

        /** @var MediaCollection $entities */
        $entities = $this->mediaRepository->search($criteria, $context)->getEntities();

        if ($msg instanceof UpdateThumbnailsMessage) {
            foreach ($entities as $media) {
                $this->thumbnailService->updateThumbnails($media, $context, $msg->isStrict());
            }
        } else {
            $this->thumbnailService->generate($entities, $context);
        }
    }
}
