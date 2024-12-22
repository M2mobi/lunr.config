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

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the Configuration class.
 *
 * @covers Lunr\Core\Configuration
 */
abstract class ConfigurationTest extends LunrBaseTest
{

    /**
     * Default config values.
     * @var array
     */
    protected array $config;

    /**
     * Instance of the tested class.
     * @var Configuration
     */
    protected Configuration $class;

    /**
     * Setup a default Configuration class.
     *
     * @return void
     */
    protected function setUpNonArray(): void
    {
        $this->class = new Configuration();

        parent::baseSetUp($this->class);
    }

    /**
     * Setup a Configuration class initialized with an existing $config array.
     *
     * @param array $config Existing configuration values
     *
     * @return void
     */
    protected function setUpArray(array $config): void
    {
        $this->config = $config;
        $this->class  = new Configuration($config);

        parent::baseSetUp($this->class);
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->config);
        unset($this->class);

        parent::tearDown();
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
