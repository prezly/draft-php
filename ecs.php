<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $config) {
    (require __DIR__ . '/vendor/prezly/code-style/ecs.php')($config);
};
