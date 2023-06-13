<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Write\Validation;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\ShuweiHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('core')]
class RestrictDeleteViolationException extends ShuweiHttpException
{
    /**
     * @var RestrictDeleteViolation[]
     */
    private readonly array $restrictions;

    /**
     * @param RestrictDeleteViolation[] $restrictions
     */
    public function __construct(
        EntityDefinition $definition,
        array $restrictions
    ) {
        $restriction = $restrictions[0];
        $usages = [];
        $usagesStrings = [];

        /** @var string $entityName */
        /** @var array<string> $ids */
        foreach ($restriction->getRestrictions() as $entityName => $ids) {
            $name = $entityName;
            $usages[] = [
                'entityName' => $name,
                'count' => \count($ids),
            ];
            $usagesStrings[] = sprintf('%s (%d)', $name, \count($ids));
        }

        $this->restrictions = $restrictions;

        parent::__construct(
            'The delete request for {{ entity }} was denied due to a conflict. The entity is currently in use by: {{ usagesString }}',
            ['entity' => $definition->getEntityName(), 'usagesString' => implode(', ', $usagesStrings), 'usages' => $usages]
        );
    }

    /**
     * @return RestrictDeleteViolation[]
     */
    public function getRestrictions(): array
    {
        return $this->restrictions;
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_CONFLICT;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__DELETE_RESTRICTED';
    }
}
