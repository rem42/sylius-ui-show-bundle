<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Rem42\SyliusUiShow\Exception\InvalidShowException;
use Rem42\SyliusUiShow\Registry\ShowRegistry;
use Rem42\SyliusUiShow\Symfony\DependencyInjection\Compiler\RegisterShowsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 */
final class RegisterShowsPassTest extends AbstractCompilerPassTestCase
{
    public function testItRegistersShowsSortedByPriority(): void
    {
        $this->setDefinition('rem42_sylius_ui_show.registry', new Definition(ShowRegistry::class));
        $this->setDefinition('app.low', (new Definition(\stdClass::class))->addTag('rem42_sylius_ui_show.show', ['name' => 'low', 'resource_class' => 'App\Book', 'priority' => -5]));
        $this->setDefinition('app.default', (new Definition(\stdClass::class))->addTag('rem42_sylius_ui_show.show', ['resource_class' => 'App\Book']));
        $this->setDefinition('app.high', (new Definition(\stdClass::class))->addTag('rem42_sylius_ui_show.show', ['name' => 'high', 'resource_class' => 'App\Book', 'priority' => 10]));

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('rem42_sylius_ui_show.registry', '$resourceClasses', [
            'high' => 'App\Book',
            'app.default' => 'App\Book',
            'low' => 'App\Book',
        ]);
    }

    public function testItRejectsDuplicatedNames(): void
    {
        $this->setDefinition('rem42_sylius_ui_show.registry', new Definition(ShowRegistry::class));
        $this->setDefinition('app.a', (new Definition(\stdClass::class))->addTag('rem42_sylius_ui_show.show', ['name' => 'book', 'resource_class' => 'App\Book']));
        $this->setDefinition('app.b', (new Definition(\stdClass::class))->addTag('rem42_sylius_ui_show.show', ['name' => 'book', 'resource_class' => 'App\Book']));

        $this->expectException(InvalidShowException::class);

        $this->compile();
    }

    public function testItRequiresAResourceClass(): void
    {
        $this->setDefinition('rem42_sylius_ui_show.registry', new Definition(ShowRegistry::class));
        $this->setDefinition('app.a', (new Definition(\stdClass::class))->addTag('rem42_sylius_ui_show.show', ['name' => 'book']));

        $this->expectException(InvalidShowException::class);

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterShowsPass());
    }
}
