<?php

/*
 * This file is part of Hiject.
 *
 * Copyright (C) 2016 Hiject Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hiject\Providers;

use Hiject\Services\Update\LatestRelease;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class UpdateProvider.
 */
class UpdateProvider implements ServiceProviderInterface
{
    /**
     * Register providers.
     *
     * @param \Pimple\Container $container
     *
     * @return \Pimple\Container
     */
    public function register(Container $container)
    {
        $container['updateManager'] = new LatestRelease($container);

        return $container;
    }
}
