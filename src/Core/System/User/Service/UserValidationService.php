<?php declare(strict_types=1);

namespace Shuwei\Core\System\User\Service;

use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shuwei\Core\Framework\Log\Package;

#[Package('system-settings')]
class UserValidationService
{
    /**
     * @internal
     */
    public function __construct(private readonly EntityRepository $userRepo)
    {
    }

    /**
     * @throws InconsistentCriteriaIdsException
     */
    public function checkEmailUnique(string $userEmail, string $userId, Context $context): bool
    {
        $criteria = new Criteria();

        $criteria->addFilter(
            new MultiFilter(
                'AND',
                [
                    new EqualsFilter('email', $userEmail),
                    new NotFilter('AND', [
                        new EqualsFilter('id', $userId),
                    ]),
                ]
            )
        );

        return $this->userRepo->searchIds($criteria, $context)->getTotal() === 0;
    }

    /**
     * @throws InconsistentCriteriaIdsException
     */
    public function checkUsernameUnique(string $userUsername, string $userId, Context $context): bool
    {
        $criteria = new Criteria();

        $criteria->addFilter(
            new MultiFilter(
                'AND',
                [
                    new EqualsFilter('username', $userUsername),
                    new NotFilter('AND', [
                        new EqualsFilter('id', $userId),
                    ]),
                ]
            )
        );

        return $this->userRepo->searchIds($criteria, $context)->getTotal() === 0;
    }
}
