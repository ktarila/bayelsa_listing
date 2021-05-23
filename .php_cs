<?php

$header = <<<'EOF'
This file is part of Bayelsa Listing Symfony Project.

(c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude('tests/Fixtures')
    ->exclude('var')
    ->exclude('vendor')
    ->in(__DIR__)
    ->append([__DIR__.'/php-cs-fixer'])
;

$config = PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP56Migration' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => ['header' => $header],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'list_syntax' => ['syntax' => 'short'],
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;

// special handling of fabbot.io service if it's using too old PHP CS Fixer version
if (false !== getenv('FABBOT_IO')) {
    try {
        PhpCsFixer\FixerFactory::create()
            ->registerBuiltInFixers()
            ->registerCustomFixers($config->getCustomFixers())
            ->useRuleSet(new PhpCsFixer\RuleSet($config->getRules()));
    } catch (PhpCsFixer\ConfigurationException\InvalidConfigurationException $e) {
        $config->setRules([]);
    } catch (UnexpectedValueException $e) {
        $config->setRules([]);
    } catch (InvalidArgumentException $e) {
        $config->setRules([]);
    }
}

return $config;
