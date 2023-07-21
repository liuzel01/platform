<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test\Struct;

use PHPUnit\Framework\TestCase;
use Shuwei\Core\Content\Media\MediaEntity;
use Shuwei\Core\Framework\Struct\Collection;
use Shuwei\Core\Framework\Struct\Struct;
use Shuwei\Core\System\User\UserEntity;

/**
 * @internal
 */
class CollectionTest extends TestCase
{
    public function testConstructor(): void
    {
        $elements = ['a', 'b'];
        $collection = new TestCollection($elements);

        static::assertEquals($elements, $collection->getElements());
    }

    public function testConstructorKeepingKeys(): void
    {
        $elements = ['z' => 'a', 'y' => 'b'];
        $collection = new TestCollection($elements);

        static::assertEquals($elements, $collection->getElements());
    }

    public function testClear(): void
    {
        $collection = new TestCollection();
        $collection->add('a');
        $collection->add('b');

        $collection->clear();
        static::assertEmpty($collection->getElements());
    }

    public function testCount(): void
    {
        $collection = new TestCollection();
        static::assertEquals(0, $collection->count());

        $collection->add('a');
        $collection->add('b');
        static::assertEquals(2, $collection->count());
    }

    public function testGetNumericKeys(): void
    {
        $collection = new TestCollection();
        static::assertEquals([], $collection->getKeys());

        $collection->add('a');
        $collection->add('b');
        static::assertEquals([0, 1], $collection->getKeys());
    }

    public function testHasWithNumericKey(): void
    {
        $collection = new TestCollection();
        static::assertFalse($collection->has(0));

        $collection->add('a');
        $collection->add('b');
        static::assertTrue($collection->has(0));
        static::assertTrue($collection->has(1));
    }

    public function testMap(): void
    {
        $collection = new TestCollection();
        $collection->map(function (): void {
            static::fail('map should not be called for empty collection');
        });

        $collection->add('a');
        $collection->add('b');
        $result = $collection->map(fn ($element) => $element . '_test');
        static::assertEquals(['a_test', 'b_test'], $result);
    }

    public function testFmap(): void
    {
        $collection = new TestCollection();
        $collection->fmap(function (): void {
            static::fail('fmap should not be called for empty collection');
        });

        $collection->add('a');
        $collection->add('b');
        $filtered = $collection->fmap(fn ($element) => $element === 'a' ? false : $element . '_test');
        static::assertEquals([1 => 'b_test'], $filtered);
    }

    public function testSort(): void
    {
        $collection = new TestCollection();

        $collection->sort(function (): void {
            static::fail('fmap should not be called for empty collection');
        });

        $collection->add('b');
        $collection->add('c');
        $collection->add('a');

        $collection->sort(fn ($a, $b) => strcmp((string) $a, (string) $b));

        static::assertEquals([2 => 'a', 0 => 'b', 1 => 'c'], $collection->getElements());
    }

    public function testFilterInstance(): void
    {
        $userStruct = new UserEntity();
        $mediaStruct = new MediaEntity();
        $collection = new TestCollection();
        static::assertEquals(0, $collection->filterInstance(UserEntity::class)->count());

        $collection->add('a');
        $collection->add($userStruct);
        $collection->add($mediaStruct);

        $filtered = $collection->filterInstance(Struct::class);
        static::assertEquals([$userStruct, $mediaStruct], array_values($filtered->getElements()));
    }

    public function testFilter(): void
    {
        $collection = new TestCollection();
        $collection->filter(function (): void {
            static::fail('filter should not be called for empty collection');
        });

        $collection->add('a');
        $collection->add('b');
        $collection->add('c');

        $filtered = $collection->filter(fn ($element) => $element !== 'b');
        static::assertEquals(['a', 'c'], array_values($filtered->getElements()));
    }

    public function testSlice(): void
    {
        $collection = new TestCollection();
        static::assertEmpty($collection->slice(0)->getElements());

        $collection->add('a');
        $collection->add('b');
        $collection->add('c');

        static::assertEquals(['b', 'c'], array_values($collection->slice(1)->getElements()));
        static::assertEquals(['b'], array_values($collection->slice(1, 1)->getElements()));
    }

    public function testGetElements(): void
    {
        $elements = ['a', 'b'];
        $collection = new TestCollection();
        static::assertEquals([], $collection->getElements());

        $collection->add('a');
        $collection->add('b');

        static::assertEquals($elements, $collection->getElements());
    }

    public function testJsonSerialize(): void
    {
        $elements = ['a', 'b'];
        $collection = new TestCollection();
        static::assertEquals(
            [],
            $collection->jsonSerialize()
        );

        $collection->add('a');
        $collection->add('b');

        static::assertEquals(
            $elements,
            $collection->jsonSerialize()
        );
    }

    public function testFirst(): void
    {
        $collection = new TestCollection();
        static::assertNull($collection->first());

        $collection->add('a');
        $collection->add('b');

        static::assertEquals('a', $collection->first());
    }

    public function testLast(): void
    {
        $collection = new TestCollection();
        static::assertNull($collection->last());

        $collection->add('a');
        $collection->add('b');

        static::assertEquals('b', $collection->last());
    }

    public function testGetAt(): void
    {
        $collection = new TestCollection();
        static::assertFalse($collection->has(0));

        $collection->add('a');
        $collection->add('b');
        static::assertEquals('a', $collection->getAt(0));
        static::assertEquals('b', $collection->getAt(1));
    }
}

/**
 * @internal
 *
 * @extends Collection<string|UserEntity|MediaEntity>
 */
class TestCollection extends Collection
{
}
