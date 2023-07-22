<?php declare(strict_types=1);

namespace Shuwei\Core\Framework\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Shuwei\Core\Defaults;
use Shuwei\Core\Framework\Api\Context\SystemSource;
use Shuwei\Core\Framework\Context;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Struct\ArrayEntity;
use Shuwei\Core\Framework\Struct\Serializer\StructNormalizer;
use Shuwei\Core\Framework\Uuid\Uuid;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @internal
 */
#[Package('core')]
class ContextTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $context = Context::createDefaultContext();

        static::assertInstanceOf(SystemSource::class, $context->getSource());
        static::assertEquals(Context::SYSTEM_SCOPE, $context->getScope());
        static::assertEquals([], $context->getRuleIds());
        static::assertEquals(Defaults::LIVE_VERSION, $context->getVersionId());
    }

    public function testScope(): void
    {
        $context = Context::createDefaultContext();

        static::assertEquals(Context::SYSTEM_SCOPE, $context->getScope());

        $context->scope('foo', function (Context $context): void {
            static::assertEquals('foo', $context->getScope());
        });

        static::assertEquals(Context::SYSTEM_SCOPE, $context->getScope());
    }

    public function testVersionChange(): void
    {
        $versionId = Uuid::randomHex();

        $context = Context::createDefaultContext();
        $versionContext = $context->createWithVersionId($versionId);

        static::assertEquals(Defaults::LIVE_VERSION, $context->getVersionId());
        static::assertEquals($versionId, $versionContext->getVersionId());
    }

    public function testVersionChangeInheritsExtensions(): void
    {
        $context = Context::createDefaultContext();
        $context->addExtension('foo', new ArrayEntity());

        static::assertNotNull($context->getExtension('foo'));

        $versionContext = $context->createWithVersionId(Uuid::randomHex());

        static::assertNotNull($versionContext->getExtension('foo'));
    }

    public function testExtensionsAreStripped(): void
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);

        $normalizers = [new StructNormalizer(), new ObjectNormalizer($classMetadataFactory, null, null, null, $discriminator), new ArrayDenormalizer()];
        $serializer = new Serializer($normalizers, [new JsonEncoder()]);

        $context = Context::createDefaultContext();

        $context->addExtension('foo', new ArrayEntity());

        $serialized = $serializer->serialize($context, 'json');
        $deserialized = $serializer->deserialize($serialized, Context::class, 'json');

        static::assertInstanceOf(Context::class, $deserialized);

        static::assertEmpty($deserialized->getVars()['extensions']);
    }
}
