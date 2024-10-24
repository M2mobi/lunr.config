<?php

/**
 * This file contains the ConfigurationAccessMockTrait.
 *
 * SPDX-FileCopyrightText: Copyright 2023 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests\Helpers;

use Lunr\Core\Configuration;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;

/**
 * This trait contains test methods to mock array access to the Configuration class.
 */
trait ConfigurationAccessMockTrait
{

    use MockeryPHPUnitIntegration;

    /**
     * Mock instance of the Configuration class.
     * @var Configuration&MockInterface
     */
    protected $config;

    /**
     * Expect access to the Configuration class based on given array structure.
     *
     * @param array $config Blueprint array structure
     *
     * @return void
     */
    private function expectConfigurationAccess(array $config): void
    {
        foreach ($config as $key => $value)
        {
            $this->config->shouldReceive('offsetExists')
                         ->with($key)
                         ->andReturn(!is_null($value));

            if (is_array($value) === TRUE && array_is_list($value) === FALSE)
            {
                $return = $this->expectSubConfigurationAccess($value);

                $this->config->shouldReceive('offsetGet')
                             ->atLeast()
                             ->once()
                             ->with($key)
                             ->andReturn($return);
            }
            else
            {
                $this->config->shouldReceive('offsetGet')
                             ->atLeast()
                             ->once()
                             ->with($key)
                             ->andReturn($value);
            }
        }
    }

    /**
     * Expect access to Configuration class within a Configuration class based on given array structure.
     *
     * @param array $config Blueprint array structure
     *
     * @return Configuration&MockInterface
     */
    private function expectSubConfigurationAccess(array $config): Configuration
    {
        $subconfig = Mockery::mock('Lunr\Core\Configuration');

        foreach ($config as $key => $value)
        {
            $subconfig->shouldReceive('offsetExists')
                      ->with($key)
                      ->andReturn(!is_null($value));

            if (is_array($value) === TRUE && array_is_list($value) === FALSE)
            {
                $return = $this->expectSubConfigurationAccess($value);

                $subconfig->shouldReceive('offsetGet')
                          ->atLeast()
                          ->once()
                          ->with($key)
                          ->andReturn($return);
            }
            else
            {
                $subconfig->shouldReceive('offsetGet')
                          ->atLeast()
                          ->once()
                          ->with($key)
                          ->andReturn($value);
            }
        }

        return $subconfig;
    }

}

?>
