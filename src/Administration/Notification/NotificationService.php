<?php declare(strict_types=1);

namespace Shuwei\Administration\Notification;

use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\Context\AdminApiSource;
use Shuwei\Core\Framework\Api\Context\Exception\InvalidContextSourceException;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('administration')]
class NotificationService
{
    public function __construct(private readonly EntityRepository $notificationRepository)
    {
    }

    public function createNotification(array $data, Context $context): void
    {
        $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($data): void {
            $this->notificationRepository->create([$data], $context);
        });
    }

    public function getNotifications(Context $context, int $limit, ?string $latestTimestamp): array
    {
        $source = $context->getSource();
        if (!$source instanceof AdminApiSource) {
            throw new InvalidContextSourceException(AdminApiSource::class, $context->getSource()::class);
        }

        $criteria = new Criteria();
        $isAdmin = $source->isAdmin();
        if (!$isAdmin) {
            $criteria->addFilter(new EqualsFilter('adminOnly', false));
        }

        if ($latestTimestamp) {
            $criteria->addFilter(new RangeFilter('createdAt', [
                RangeFilter::GT => $latestTimestamp,
            ]));
        }

        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::ASCENDING));
        $criteria->setLimit($limit);

        $notifications = $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($criteria) {
            /** @var NotificationCollection $notifications */
            $notifications = $this->notificationRepository->search($criteria, $context)->getEntities();

            return $notifications;
        });

        if ($notifications->count() === 0) {
            return [
                'notifications' => new NotificationCollection(),
                'timestamp' => null,
            ];
        }

        /** @var NotificationEntity $notification */
        $notification = $notifications->last();

        /** @var \DateTimeInterface $latestTimestamp */
        $latestTimestamp = $notification->getCreatedAt();
        $latestTimestamp = $latestTimestamp->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        if ($isAdmin) {
            return [
                'notifications' => $notifications,
                'timestamp' => $latestTimestamp,
            ];
        }

        $notifications = $this->formatNotifications($notifications, $source);

        return [
            'notifications' => $notifications,
            'timestamp' => $latestTimestamp,
        ];
    }

    private function formatNotifications(NotificationCollection $notifications, AdminApiSource $source): NotificationCollection
    {
        $responseNotifications = new NotificationCollection();

        /** @var NotificationEntity $notification */
        foreach ($notifications as $notification) {
            if ($this->isAllow($notification->getRequiredPrivileges(), $source)) {
                $responseNotifications->add($notification);
            }
        }

        return $responseNotifications;
    }

    private function isAllow(array $privileges, AdminApiSource $source): bool
    {
        foreach ($privileges as $privilege) {
            if (!$source->isAllowed($privilege)) {
                return false;
            }
        }

        return true;
    }
}
