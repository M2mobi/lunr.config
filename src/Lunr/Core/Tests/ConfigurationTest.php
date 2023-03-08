<?php

/**
 * This file contains the ConfigurationTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;

use Lunr\Core\Configuration;
use Lunr\Halo\LunrBaseTest;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the Configuration class.
 *
 * @covers     Lunr\Core\DateTime
 */
abstract class ConfigurationTest extends LunrBaseTest
{

    /**
     * Default config values.
     * @var array
     */
    protected $config;

    /**
     * Setup a default Configuration class.
     *
     * @return void
     */
    protected function setUpNonArray(): void
    {
        $this->class      = new Configuration();
        $this->reflection = new ReflectionClass('Lunr\Core\Configuration');
    }

    /**
     * Setup a Configuration class initialized with an existing $config array.
     *
     * @param array $config Existing configuration values
     *
     * @return void
     */
    protected function setUpArray($config): void
    {
        $this->config     = $config;
        $this->class      = new Configuration($config);
        $this->reflection = new ReflectionClass('Lunr\Core\Configuration');
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->config);
        unset($this->class);
        unset($this->reflection);
    }

    /**
     * Construct the multi-dimensional test array.
     *
     * @return array Test $config array
     */
    protected function construct_test_array(): array
    {
        $config                   = [];
        $config['test1']          = 'String';
        $config['test2']          = [];
        $config['test2']['test3'] = 1;
        $config['test2']['test4'] = FALSE;

        return $config;
    }

    /**
     * Unit Test Data Provider for non-array values.
     *
     * @return array $values Set of non-array values.
     */
    public function nonArrayValueProvider(): array
    {
        $values   = [];
        $values[] = [ 'String' ];
        $values[] = [ 1 ];
        $values[] = [ NULL ];
        $values[] = [ FALSE ];
        $values[] = [ new stdClass() ];

        return $values;
    }

    /**
     * Unit Test Data Provider for existing $config key->value pairs.
     *
     * @return array $pairs Existing key->value pairs
     */
    public function existingConfigPairProvider(): array
    {
        $pairs   = [];
        $pairs[] = [ 'test1', 'String' ];

        return $pairs;
    }

    /**
     * Unit Test Data Provider for not existing $config key->value pairs.
     *
     * @return array $pairs Not existing key->value pairs
     */
    public function nonExistingConfigPairProvider(): array
    {
        $pairs   = [];
        $pairs[] = [ 'test4', 'Value' ];

        return $pairs;
    }

}

?>
