<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\DataAbstractionLayer\Search\Parser;

use Shuwei\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\InvalidAggregationQueryException;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\InvalidFilterQueryException;
use Shuwei\Core\Framework\DataAbstractionLayer\Exception\SearchRequestException;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Aggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\DateHistogramAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\FilterAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\AvgAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MaxAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MinAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\RangeAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\StatsAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\SumAggregation;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shuwei\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shuwei\Core\Framework\Log\Package;

/**
 * @internal
 */
#[Package('core')]
class AggregationParser
{
    public function buildAggregations(EntityDefinition $definition, array $payload, Criteria $criteria, SearchRequestException $searchRequestException): void
    {
        if (!\is_array($payload['aggregations'])) {
            throw new InvalidAggregationQueryException('The aggregations parameter has to be a list of aggregations.');
        }

        foreach ($payload['aggregations'] as $index => $aggregation) {
            $parsed = $this->parseAggregation($index, $definition, $aggregation, $searchRequestException);

            if ($parsed) {
                $criteria->addAggregation($parsed);
            }
        }
    }

    public function toArray(array $aggregations): array
    {
        $data = [];

        foreach ($aggregations as $aggregation) {
            $data[] = $this->aggregationToArray($aggregation);
        }

        return $data;
    }

    private function aggregationToArray(Aggregation $aggregation): array
    {
        if ($aggregation instanceof AvgAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'avg',
                'field' => $aggregation->getField(),
            ];
        }
        if ($aggregation instanceof MaxAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'max',
                'field' => $aggregation->getField(),
            ];
        }
        if ($aggregation instanceof MinAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'min',
                'field' => $aggregation->getField(),
            ];
        }
        if ($aggregation instanceof StatsAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'stats',
                'field' => $aggregation->getField(),
            ];
        }
        if ($aggregation instanceof SumAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'sum',
                'field' => $aggregation->getField(),
            ];
        }
        if ($aggregation instanceof CountAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'count',
                'field' => $aggregation->getField(),
            ];
        }
        if ($aggregation instanceof EntityAggregation) {
            return [
                'name' => $aggregation->getName(),
                'type' => 'entity',
                'field' => $aggregation->getField(),
                'definition' => $aggregation->getEntity(),
            ];
        }
        if ($aggregation instanceof FilterAggregation) {
            $filters = [];
            foreach ($aggregation->getFilter() as $filter) {
                $filters[] = QueryStringParser::toArray($filter);
            }

            return [
                'name' => $aggregation->getName(),
                'type' => 'filter',
                'filter' => $filters,
                'aggregation' => $this->aggregationToArray($aggregation->getAggregation()),
            ];
        }
        if ($aggregation instanceof DateHistogramAggregation) {
            $data = [
                'name' => $aggregation->getName(),
                'type' => 'histogram',
                'interval' => $aggregation->getInterval(),
                'format' => $aggregation->getFormat(),
                'field' => $aggregation->getField(),
                'timeZone' => $aggregation->getTimeZone(),
            ];

            if ($aggregation->getSorting()) {
                $data['sort'] = [
                    'order' => $aggregation->getSorting()->getDirection(),
                    'naturalSorting' => $aggregation->getSorting()->getNaturalSorting(),
                    'field' => $aggregation->getSorting()->getField(),
                ];
            }

            if ($aggregation->getAggregation()) {
                $data['aggregation'] = $this->aggregationToArray($aggregation->getAggregation());
            }

            return $data;
        }

        if ($aggregation instanceof TermsAggregation) {
            $data = [
                'name' => $aggregation->getName(),
                'type' => 'terms',
                'field' => $aggregation->getField(),
            ];

            if ($aggregation->getSorting()) {
                $data['sort'] = [
                    'order' => $aggregation->getSorting()->getDirection(),
                    'naturalSorting' => $aggregation->getSorting()->getNaturalSorting(),
                    'field' => $aggregation->getSorting()->getField(),
                ];
            }

            if ($aggregation->getAggregation()) {
                $data['aggregation'] = $this->aggregationToArray($aggregation->getAggregation());
            }

            return $data;
        }

        throw new InvalidAggregationQueryException(sprintf('The aggregation of type "%s" is not supported.', $aggregation::class));
    }

    private function parseAggregation(int $index, EntityDefinition $definition, array $aggregation, SearchRequestException $exceptions): ?Aggregation
    {
        if (!\is_array($aggregation)) {
            $exceptions->add(new InvalidAggregationQueryException('The field "%s" should be a list of aggregations.'), '/aggregations/' . $index);

            return null;
        }

        $name = \array_key_exists('name', $aggregation) ? (string) $aggregation['name'] : null;

        if (empty($name) || is_numeric($name)) {
            $exceptions->add(new InvalidAggregationQueryException('The aggregation name should be a non-empty string.'), '/aggregations/' . $index);

            return null;
        }

        /** @var string|null $type */
        $type = $aggregation['type'] ?? null;

        if (empty($type) || is_numeric($type)) {
            $exceptions->add(new InvalidAggregationQueryException('The aggregations of "%s" should be a non-empty string.'), '/aggregations/' . $index);

            return null;
        }

        if (empty($aggregation['field']) && $type !== 'filter') {
            $exceptions->add(new InvalidAggregationQueryException('The aggregation should contain a "field".'), '/aggregations/' . $index . '/' . $type . '/field');

            return null;
        }

        $field = null;
        if ($type !== 'filter') {
            $field = self::buildFieldName($definition, $aggregation['field']);
        }
        switch ($type) {
            case 'avg':
                return new AvgAggregation($name, $field);
            case 'max':
                return new MaxAggregation($name, $field);
            case 'min':
                return new MinAggregation($name, $field);
            case 'stats':
                return new StatsAggregation($name, $field);
            case 'sum':
                return new SumAggregation($name, $field);
            case 'count':
                return new CountAggregation($name, $field);
            case 'range':
                if (!isset($aggregation['ranges'])) {
                    $exceptions->add(new InvalidAggregationQueryException('The aggregation should contain "ranges".'), '/aggregations/' . $index . '/' . $type . '/field');

                    return null;
                }

                return new RangeAggregation($name, (string) $field, $aggregation['ranges']);
            case 'entity':
                if (!isset($aggregation['definition'])) {
                    $exceptions->add(new InvalidAggregationQueryException('The aggregation should contain a "definition".'), '/aggregations/' . $index . '/' . $type . '/field');

                    return null;
                }

                return new EntityAggregation($name, $field, $aggregation['definition']);

            case 'filter':
                if (empty($aggregation['filter'])) {
                    $exceptions->add(new InvalidAggregationQueryException('The aggregation should contain an array of filters in property "filter".'), '/aggregations/' . $index . '/' . $type . '/field');

                    return null;
                }
                if (empty($aggregation['aggregation'])) {
                    $exceptions->add(new InvalidAggregationQueryException('The aggregation should contain an array of filters in property "filter".'), '/aggregations/' . $index . '/' . $type . '/field');

                    return null;
                }
                $filters = [];

                foreach ($aggregation['filter'] as $filterIndex => $query) {
                    try {
                        $filters[] = QueryStringParser::fromArray($definition, $query, $exceptions, '/filter/' . $filterIndex);
                    } catch (InvalidFilterQueryException $ex) {
                        $exceptions->add($ex, $ex->getPath());
                    }
                }

                $nested = $this->parseAggregation($index, $definition, $aggregation['aggregation'], $exceptions);

                return new FilterAggregation($name, $nested, $filters);

            case 'histogram':
                $nested = null;
                $sorting = null;

                if (!isset($aggregation['interval'])) {
                    $exceptions->add(new InvalidAggregationQueryException('The aggregation should contain an date interval.'), '/aggregations/' . $index . '/' . $type . '/interval');

                    return null;
                }

                $interval = $aggregation['interval'];
                $format = $aggregation['format'] ?? null;
                $timeZone = $aggregation['timeZone'] ?? null;

                if (isset($aggregation['aggregation'])) {
                    $nested = $this->parseAggregation($index, $definition, $aggregation['aggregation'], $exceptions);
                }
                if (isset($aggregation['sort'])) {
                    $sort = $aggregation['sort'];
                    $order = $sort['order'] ?? FieldSorting::ASCENDING;
                    $naturalSorting = $sort['naturalSorting'] ?? false;

                    if (strcasecmp((string) $order, 'desc') === 0) {
                        $order = FieldSorting::DESCENDING;
                    } else {
                        $order = FieldSorting::ASCENDING;
                    }

                    $sorting = new FieldSorting($sort['field'], $order, (bool) $naturalSorting);
                }

                return new DateHistogramAggregation($name, $field, $interval, $sorting, $nested, $format, $timeZone);

            case 'terms':
                $nested = null;
                $limit = null;
                $sorting = null;

                if (isset($aggregation['aggregation'])) {
                    $nested = $this->parseAggregation($index, $definition, $aggregation['aggregation'], $exceptions);
                }

                if (isset($aggregation['limit'])) {
                    $limit = (int) $aggregation['limit'];
                }
                if (isset($aggregation['sort'])) {
                    $sort = $aggregation['sort'];
                    $order = $sort['order'] ?? FieldSorting::ASCENDING;
                    $naturalSorting = $sort['naturalSorting'] ?? false;

                    if (strcasecmp((string) $order, 'desc') === 0) {
                        $order = FieldSorting::DESCENDING;
                    } else {
                        $order = FieldSorting::ASCENDING;
                    }

                    $sorting = new FieldSorting($sort['field'], $order, (bool) $naturalSorting);
                }

                return new TermsAggregation($name, $field, $limit, $sorting, $nested);

            default:
                $exceptions->add(new InvalidAggregationQueryException(sprintf('The aggregation type "%s" used as key does not exists.', $type)), '/aggregations/' . $index);

                return null;
        }
    }

    private static function buildFieldName(EntityDefinition $definition, string $fieldName): string
    {
        $prefix = $definition->getEntityName() . '.';

        if (mb_strpos($fieldName, $prefix) === false) {
            return $prefix . $fieldName;
        }

        return $fieldName;
    }
}
