<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Phiki;

use Jramke\FluidPrimitives\Utility\Typed;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;
use Phiki\Phiki;
use Phiki\Theme\Theme;

// Copy pasted from the original PhikiExtension so we can use our custom CodeBlockRenderer
class PhikiCommonMarkExtension implements ConfigurableExtensionInterface
{
    /**
     * @param  bool  $withGutter  Include a gutter in the generated HTML. The gutter typically contains line numbers and helps provide context for the code.
     */
    public function __construct(
        private readonly string|array|Theme $theme = Theme::Nord,
        private readonly Phiki $phiki = new Phiki(),
        private readonly bool $withGutter = false,
    ) {}

    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('phiki', Expect::structure([
            'theme' => Expect::mixed()->default($this->theme),
            'with_gutter' => Expect::bool()->default($this->withGutter),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $config = $environment->getConfiguration();

        // Narrowed immediately below via the instanceof/is_array/is_string check - the config schema
        // itself declares this value as Expect::mixed().
        // @mago-expect analysis:mixed-assignment
        $rawTheme = $config->get('phiki/theme');
        $theme = $rawTheme instanceof Theme || is_array($rawTheme) || is_string($rawTheme) ? $rawTheme : $this->theme;
        $withGutter = Typed::bool($config->get('phiki/with_gutter'));

        $environment->addRenderer(FencedCode::class, new CodeBlockRenderer($theme, $this->phiki, $withGutter), 10);
    }
}
