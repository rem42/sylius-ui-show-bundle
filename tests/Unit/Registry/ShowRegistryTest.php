<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\Registry;

use PHPUnit\Framework\TestCase;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Exception\InvalidShowException;
use Rem42\SyliusUiShow\Exception\ShowNotFoundException;
use Rem42\SyliusUiShow\Registry\ShowRegistry;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @internal
 */
final class ShowRegistryTest extends TestCase
{
    public function testItFindsTheShowOfAResourceThroughItsHierarchy(): void
    {
        $registry = $this->createRegistry([
            'interface_show' => RegistryResourceInterface::class,
            'parent_show' => RegistryParentResource::class,
        ]);

        self::assertSame('parent_show', $registry->findNameFor(new RegistryChildResource()));
        self::assertSame('parent_show', $registry->findNameFor(RegistryParentResource::class));
        self::assertSame('interface_show', $registry->findNameFor(new RegistryOtherResource()));
        self::assertNull($registry->findNameFor(new \stdClass()));
    }

    public function testTheFirstRegisteredShowWinsForTheSameClass(): void
    {
        $registry = $this->createRegistry([
            'high_priority' => RegistryParentResource::class,
            'low_priority' => RegistryParentResource::class,
        ]);

        self::assertSame('high_priority', $registry->findNameFor(new RegistryParentResource()));
        self::assertSame(['high_priority', 'low_priority'], $registry->getNames());
    }

    public function testItReturnsInvokableShows(): void
    {
        $registry = $this->createRegistry(['parent_show' => RegistryParentResource::class]);

        self::assertTrue($registry->has('parent_show'));
        self::assertFalse($registry->has('unknown'));
        self::assertIsCallable($registry->get('parent_show'));
    }

    public function testItThrowsForUnknownShows(): void
    {
        $this->expectException(ShowNotFoundException::class);

        $this->createRegistry([])->get('unknown');
    }

    public function testItThrowsForNonInvokableShows(): void
    {
        $registry = new ShowRegistry(new ServiceLocator(['broken' => static fn (): object => new \stdClass()]), ['broken' => \stdClass::class]);

        $this->expectException(InvalidShowException::class);

        $registry->get('broken');
    }

    /**
     * @param array<string, string> $resourceClasses
     */
    private function createRegistry(array $resourceClasses): ShowRegistry
    {
        $factories = [];

        foreach (array_keys($resourceClasses) as $name) {
            $factories[$name] = static fn (): \Closure => static function (ShowBuilder $show, object $resource): void {};
        }

        return new ShowRegistry(new ServiceLocator($factories), $resourceClasses);
    }
}

interface RegistryResourceInterface {}

class RegistryParentResource implements RegistryResourceInterface {}

final class RegistryChildResource extends RegistryParentResource {}

final class RegistryOtherResource implements RegistryResourceInterface {}
