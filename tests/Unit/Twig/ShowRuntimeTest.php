<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Rem42\SyliusUiShow\Accessor\ValueAccessor;
use Rem42\SyliusUiShow\Factory\ShowFactoryInterface;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Renderer\LabelGenerator;
use Rem42\SyliusUiShow\Renderer\ShowRendererInterface;
use Rem42\SyliusUiShow\Renderer\TemplateResolver;
use Rem42\SyliusUiShow\Twig\Runtime\ShowRuntime;

/**
 * @internal
 */
final class ShowRuntimeTest extends TestCase
{
    private ShowRuntime $runtime;

    protected function setUp(): void
    {
        $this->runtime = new ShowRuntime(
            $this->createStub(ShowFactoryInterface::class),
            $this->createStub(ShowRendererInterface::class),
            new ValueAccessor(),
            new LabelGenerator(),
            new TemplateResolver([]),
        );
    }

    public function testRenderAttributesMergesClassesAndEscapesValues(): void
    {
        $card = Card::create('card')
            ->addClass('shadow')
            ->setAttribute('data-title', '"quoted" <b>')
            ->setAttribute('hidden', true)
            ->setAttribute('data-skip', false)
        ;

        self::assertSame(
            ' class="card mb-3 shadow" id="main" data-title="&quot;quoted&quot; &lt;b&gt;" hidden',
            $this->runtime->renderAttributes($card, ['class' => 'card mb-3', 'id' => 'main']),
        );
    }

    public function testStringify(): void
    {
        self::assertSame('', $this->runtime->stringify(null));
        self::assertSame('42', $this->runtime->stringify(42));
        self::assertSame('draft', $this->runtime->stringify(RuntimeStatus::Draft));
        self::assertSame('Pending', $this->runtime->stringify(RuntimePureStatus::Pending));
        self::assertSame('a, b', $this->runtime->stringify(new \ArrayIterator(['a', 'b'])));
        self::assertSame('2024-01-02T00:00:00+00:00', $this->runtime->stringify(new \DateTimeImmutable('2024-01-02', new \DateTimeZone('UTC'))));
        self::assertSame('stdClass', $this->runtime->stringify(new \stdClass()));
    }

    public function testHasShowRejectsNonObjects(): void
    {
        self::assertFalse($this->runtime->hasShow(null));
        self::assertFalse($this->runtime->hasShow(['array']));
    }
}

enum RuntimeStatus: string
{
    case Draft = 'draft';
}

enum RuntimePureStatus
{
    case Pending;
}
