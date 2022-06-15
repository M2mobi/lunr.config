<?php

/**
 * This file contains the main configuration class,
 * holding all configuration values and managing
 * access to those values.
 *
 * @package    Lunr\Core
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2011-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Core;

use ArrayAccess;
use Iterator;
use Countable;

/**
 * Configuration Class
 */
class Configuration implements ArrayAccess, Iterator, Countable
{

    /**
     * Configuration values
     * @var array
     */
    private $config;

    /**
     * Position of the array pointer
     * @var int
     */
    private $position;

    /**
     * Size of the $config array
     * @var int
     */
    private $size;

    /**
     * Whether the cached size is invalid (outdated)
     * @var boolean
     */
    private $size_invalid;

    /**
     * Constructor.
     *
     * @param array|bool $bootstrap Bootstrap config values, aka config values used before
     *                              the class has been instantiated.
     */
    public function __construct($bootstrap = FALSE)
    {
        if (!is_array($bootstrap))
        {
            $bootstrap = [];
        }

        if (!empty($bootstrap))
        {
            $bootstrap = $this->convert_array_to_class($bootstrap);
        }

        $this->config = $bootstrap;
        $this->rewind();
        $this->size_invalid = TRUE;
        $this->count();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->config);
        unset($this->position);
        unset($this->size);
        unset($this->size_invalid);
    }

    /**
     * Called when cloning the object.
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->config as $key => $value)
        {
            if ($value instanceof self)
            {
                $this[$key] = clone $value;
            }
        }

        $this->count();
    }

    /**
     * Handle the case when the object is treated like a string.
     *
     * Pretend to be an Array.
     *
     * @return string $return
     */
    public function __toString()
    {
        return 'Array';
    }

    /**
     * Load a config file.
     *
     * @param string $identifier Identifier string for the config file to load.
     *                           e.g.: For conf.lunr.inc.php the identifier would be 'lunr'
     *
     * @return void
     */
    public function load_file(string $identifier): void
    {
        $config = $this->config;

        include_once 'conf.' . $identifier . '.inc.php';

        if (!is_array($config))
        {
            $config = [];
            return;
        }

        if (!empty($config))
        {
            $config = $this->convert_array_to_class($config);
        }

        $this->config       = $config;
        $this->size_invalid = TRUE;
    }

    /**
     * Convert an input array recursively into a Configuration class hierarchy.
     *
     * @param array $array Input array
     *
     * @return mixed $array A scalar value or an array
     */
    #[\ReturnTypeWillChange]
    private function convert_array_to_class($array)
    {
        if (!is_array($array))
        {
            return $array;
        }

        if (empty($array))
        {
            return new self([]);
        }

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $array[$key] = new self($value);
            }
        }

        return $array;
    }

    /**
     * Offset to set.
     *
     * Assigns a value to the specified offset.
     * (inherited from ArrayAccess)
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value  The value to set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $value = $this->convert_array_to_class($value);
        if (is_null($offset))
        {
            $this->config[] = $value;
        }
        else
        {
            $this->config[$offset] = $value;
        }

        $this->size_invalid = TRUE;
    }

    /**
     * Whether an offset exists.
     *
     * Whether or not an offset exists.
     * This method is executed when using isset() or empty().
     * (inherited from ArrayAccess)
     *
     * @param mixed $offset An offset to check for
     *
     * @return boolean $return TRUE on success, FALSE on failure
     */
    public function offsetExists($offset): bool
    {
        return isset($this->config[$offset]);
    }

    /**
     * Offset to unset.
     *
     * (Inherited from ArrayAccess)
     *
     * @param mixed $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->config[$offset]);
        $this->size_invalid = TRUE;
    }

    /**
     * Offset to retrieve.
     *
     * Returns the value at specified offset.
     * (Inherited from ArrayAccess)
     *
     * @param mixed $offset The offset to retrieve
     *
     * @return mixed $return The value of the requested offset or null if
     *                       it doesn't exist.
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->config[$offset]) ? $this->config[$offset] : NULL;
    }

    /**
     * Convert class content to an array.
     *
     * @return array $data Array of all config values
     */
    public function toArray(): array
    {
        $data = $this->config;
        foreach ($data as $key => $value)
        {
            if ($value instanceof self)
            {
                $data[$key] = $value->toArray();
            }
        }

        return $data;
    }

    /**
     * Rewinds back to the first element of the Iterator.
     *
     * (Inherited from Iterator)
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->config);
        $this->position = 0;
    }

    /**
     * Return the current element.
     *
     * (Inherited from Iterator)
     *
     * @return mixed $return The current value of the config array
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->config);
    }

    /**
     * Return the key of the current element.
     *
     * (Inherited from Iterator)
     *
     * @return scalar $return Scalar on success, NULL on failure
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->config);
    }

    /**
     * Move forward to next element.
     *
     * (Inherited from Iterator)
     *
     * @return void
     */
    public function next(): void
    {
        next($this->config);
        ++$this->position;
    }

    /**
     * Checks if current position is valid.
     *
     * (Inherited from Iterator)
     *
     * @return boolean $return TRUE on success, FALSE on failure
     */
    public function valid(): bool
    {
        $return = $this->current();
        if (($return === FALSE) && ($this->position + 1 <= $this->count()))
        {
            $return = TRUE;
        }

        return $return !== FALSE;
    }

    /**
     * Count elements of an object.
     *
     * (Inherited from Countable)
     *
     * @return integer $size Size of the config array
     */
    public function count(): int
    {
        if ($this->size_invalid === TRUE)
        {
            $this->size         = count($this->config);
            $this->size_invalid = FALSE;
        }

        return $this->size;
    }

}

?>
