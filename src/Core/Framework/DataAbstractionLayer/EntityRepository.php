<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer;

use Shuwei\Core\Framework\Adapter\Database\ReplicaConnection;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchedEvent;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent;
use Shuwei\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shuwei\Core\Framework\DataAbstractionLayer\Read\EntityReaderInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\EntityAggregatorInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\EntitySearcherInterface;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\CloneBehavior;
use Shuwei\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\ArrayEntity;
use Shuwei\Core\Framework\Uuid\Exception\InvalidUuidException;
use Shuwei\Core\Framework\Uuid\Uuid;
use Shuwei\Core\Profiling\Profiler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @final
 *
 * @template TEntityCollection of EntityCollection
 */
#[Package('core')]
class EntityRepository
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityDefinition $definition,
        private readonly EntityReaderInterface $reader,
        private readonly VersionManager $versionManager,
        private readonly EntitySearcherInterface $searcher,
        private readonly EntityAggregatorInterface $aggregator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityLoadedEventFactory $eventFactory
    ) {
    }

    public function getDefinition(): EntityDefinition
    {
        return $this->definition;
    }

    /**
     * @return EntitySearchResult<TEntityCollection>
     */
    public function search(Criteria $criteria, Context $context): EntitySearchResult
    {
        if (!$criteria->getTitle()) {
            return $this->_search($criteria, $context);
        }

        return Profiler::trace($criteria->getTitle(), fn () => $this->_search($criteria, $context), 'repository');
    }

    public function aggregate(Criteria $criteria, Context $context): AggregationResultCollection
    {
        $criteria = clone $criteria;

        $result = $this->aggregator->aggregate($this->definition, $criteria, $context);

        $event = new EntityAggregationResultLoadedEvent($this->definition, $result, $context);
        $this->eventDispatcher->dispatch($event, $event->getName());

        return $result;
    }

    public function searchIds(Criteria $criteria, Context $context): IdSearchResult
    {
        $criteria = clone $criteria;

        $this->eventDispatcher->dispatch(new EntitySearchedEvent($criteria, $this->definition, $context));

        $result = $this->searcher->search($this->definition, $criteria, $context);

        $event = new EntityIdSearchResultLoadedEvent($this->definition, $result);
        $this->eventDispatcher->dispatch($event, $event->getName());

        return $result;
    }

    /**
     * @param array<array<string, mixed|null>> $data
     */
    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        ReplicaConnection::ensurePrimary();

        $affected = $this->versionManager->update($this->definition, $data, WriteContext::createFromContext($context));
        $event = EntityWrittenContainerEvent::createWithWrittenEvents($affected, $context, []);
        $this->eventDispatcher->dispatch($event);

        return $event;
    }

    /**
     * @param array<array<string, mixed|null>> $data
     */
    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        ReplicaConnection::ensurePrimary();

        $affected = $this->versionManager->upsert($this->definition, $data, WriteContext::createFromContext($context));
        $event = EntityWrittenContainerEvent::createWithWrittenEvents($affected, $context, []);
        $this->eventDispatcher->dispatch($event);

        return $event;
    }

    /**
     * @param array<array<string, mixed|null>> $data
     */
    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        ReplicaConnection::ensurePrimary();

        $affected = $this->versionManager->insert($this->definition, $data, WriteContext::createFromContext($context));
        $event = EntityWrittenContainerEvent::createWithWrittenEvents($affected, $context, []);
        $this->eventDispatcher->dispatch($event);

        return $event;
    }

    /**
     * @param array<array<string, mixed|null>> $ids
     */
    public function delete(array $ids, Context $context): EntityWrittenContainerEvent
    {
        ReplicaConnection::ensurePrimary();

        $affected = $this->versionManager->delete($this->definition, $ids, WriteContext::createFromContext($context));
        $event = EntityWrittenContainerEvent::createWithDeletedEvents($affected->getDeleted(), $context, $affected->getNotFound());

        if ($affected->getWritten()) {
            $updates = EntityWrittenContainerEvent::createWithWrittenEvents($affected->getWritten(), $context, []);

            if ($updates->getEvents() !== null) {
                $event->addEvent(...$updates->getEvents());
            }
        }

        $this->eventDispatcher->dispatch($event);

        return $event;
    }

    public function createVersion(string $id, Context $context, ?string $name = null, ?string $versionId = null): string
    {
        ReplicaConnection::ensurePrimary();

        if (!$this->definition->isVersionAware()) {
            throw new \RuntimeException(sprintf('Entity %s is not version aware', $this->definition->getEntityName()));
        }

        return $this->versionManager->createVersion($this->definition, $id, WriteContext::createFromContext($context), $name, $versionId);
    }

    public function merge(string $versionId, Context $context): void
    {
        ReplicaConnection::ensurePrimary();

        if (!$this->definition->isVersionAware()) {
            throw new \RuntimeException(sprintf('Entity %s is not version aware', $this->definition->getEntityName()));
        }
        $this->versionManager->merge($versionId, WriteContext::createFromContext($context));
    }

    public function clone(string $id, Context $context, ?string $newId = null, ?CloneBehavior $behavior = null): EntityWrittenContainerEvent
    {
        ReplicaConnection::ensurePrimary();

        $newId ??= Uuid::randomHex();
        if (!Uuid::isValid($newId)) {
            throw new InvalidUuidException($newId);
        }

        $affected = $this->versionManager->clone(
            $this->definition,
            $id,
            $newId,
            $context->getVersionId(),
            WriteContext::createFromContext($context),
            $behavior ?? new CloneBehavior()
        );

        $event = EntityWrittenContainerEvent::createWithWrittenEvents($affected, $context, [], true);
        $this->eventDispatcher->dispatch($event);

        return $event;
    }

    /**
     * @return TEntityCollection
     */
    private function read(Criteria $criteria, Context $context): EntityCollection
    {
        $criteria = clone $criteria;

        /** @var TEntityCollection $entities */
        $entities = $this->reader->read($this->definition, $criteria, $context);

        if ($criteria->getFields() === []) {
            $event = $this->eventFactory->create($entities->getElements(), $context);
        } else {
            $event = $this->eventFactory->createPartial($entities->getElements(), $context);
        }

        $this->eventDispatcher->dispatch($event);

        return $entities;
    }

    /**
     * @return EntitySearchResult<TEntityCollection>
     */
    private function _search(Criteria $criteria, Context $context): EntitySearchResult
    {
        $criteria = clone $criteria;
        $aggregations = null;
        if ($criteria->getAggregations()) {
            $aggregations = $this->aggregate($criteria, $context);
        }

        if (!RepositorySearchDetector::isSearchRequired($this->definition, $criteria)) {
            $this->eventDispatcher->dispatch(
                new EntitySearchedEvent($criteria, $this->definition, $context)
            );
            $entities = $this->read($criteria, $context);

            return new EntitySearchResult($this->definition->getEntityName(), $entities->count(), $entities, $aggregations, $criteria, $context);
        }

        $ids = $this->searchIds($criteria, $context);

        if (empty($ids->getIds())) {
            /** @var TEntityCollection $collection */
            $collection = $this->definition->getCollectionClass();

            return new EntitySearchResult($this->definition->getEntityName(), $ids->getTotal(), new $collection(), $aggregations, $criteria, $context);
        }

        $readCriteria = $criteria->cloneForRead($ids->getIds());

        $entities = $this->read($readCriteria, $context);

        $search = $ids->getData();

        foreach ($entities as $element) {
            if (!\array_key_exists($element->getUniqueIdentifier(), $search)) {
                continue;
            }

            $data = $search[$element->getUniqueIdentifier()];
            unset($data['id']);

            if (empty($data)) {
                continue;
            }

            $element->addExtension('search', new ArrayEntity($data));
        }

        $result = new EntitySearchResult($this->definition->getEntityName(), $ids->getTotal(), $entities, $aggregations, $criteria, $context);
        $result->addState(...$ids->getStates());

        $event = new EntitySearchResultLoadedEvent($this->definition, $result);
        $this->eventDispatcher->dispatch($event, $event->getName());

        return $result;
    }
}
