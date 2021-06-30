<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $config): void {
    $config->parameters()
        ->set(Option::PATHS, [
            __DIR__ . '/lib',
            __DIR__ . '/tests',
        ]);

    (require __DIR__ . '/vendor/prezly/code-style/ecs.php')($config);
};
