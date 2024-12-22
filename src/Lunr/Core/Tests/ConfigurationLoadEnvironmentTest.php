<?php

/**
 * This file contains the ConfigurationLoadFileTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core\Tests;


/**
 * This tests loading configuration files via the Configuration class.
 *
 * @covers \Lunr\Core\Configuration
 */
class ConfigurationLoadEnvironmentTest extends ConfigurationTest
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpArray($this->construct_test_array());
    }

    /**
     * Test loading the environment correctly.
     */
    public function testLoadEnvironment(): void
    {
        $_ENV = [];

        $_ENV['LOAD_ONE'] = 'Value';
        $_ENV['LOAD_TWO'] = 'String';

        $this->class->load_environment();

        $this->config['load_one'] = 'Value';
        $this->config['load_two'] = 'String';

        $this->assertEquals($this->config, $this->class->toArray());

        unset($_ENV['LOAD_ONE']);
        unset($_ENV['LOAD_TWO']);
    }

    /**
     * Test loading a correct config file.
     */
    public function testLoadFileOverwritesValues(): void
    {
        $_ENV = [];

        $_ENV['TEST1'] = 'Test';

        $this->class->load_environment();

        $config                   = [];
        $config['test1']          = 'Test';
        $config['test2']          = $this->config['test2'];

        $this->assertEquals($config, $this->class->toArray());

        unset($_ENV['TEST1']);
    }

    /**
     * Test that loading a file invalidates the cached size value.
     */
    public function testLoadFileInvalidatesSize(): void
    {
        $property = $this->reflection->getProperty('size_invalid');
        $property->setAccessible(TRUE);

        $this->assertFalse($property->getValue($this->class));

        $this->class->load_file('correct');

        $this->assertTrue($property->getValue($this->class));
    }

}

?>
