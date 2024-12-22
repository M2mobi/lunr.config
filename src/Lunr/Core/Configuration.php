<?php

/**
 * This file contains the main configuration class,
 * holding all configuration values and managing
 * access to those values.
 *
 * SPDX-FileCopyrightText: Copyright 2011 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Core;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Configuration Class
 *
 * @implements ArrayAccess<int|string,mixed>
 * @implements Iterator<int|string,mixed>
 */
class Configuration implements ArrayAccess, Iterator, Countable
{

    /**
     * Configuration values
     * @var array<int|string,mixed>
     */
    private array $config;

    /**
     * Position of the array pointer
     * @var int
     */
    private int $position;

    /**
     * Size of the $config array
     * @var int
     */
    private int $size;

    /**
     * Whether the cached size is invalid (outdated)
     * @var bool
     */
    private bool $size_invalid;

    /**
     * Constructor.
     *
     * @param array<int|string,mixed>|bool $bootstrap Bootstrap config values, aka config values used before
     *                                                the class has been instantiated.
     */
    public function __construct(array|bool $bootstrap = FALSE)
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
    public function __clone(): void
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
     * @return string
     */
    public function __toString(): string
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

        /**
         * Since we're including a config file here, it's conceivable that $config
         * might not stay an array. Ignore the phpstan check for that.
         *
         * @phpstan-ignore function.alreadyNarrowedType
         */
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
     * @param array<int|string,mixed> $array Input array
     *
     * @return array<int|string,mixed> An array with sub-arrays converted
     */
    private function convert_array_to_class(array $array): array
    {
        if (empty($array))
        {
            return [];
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
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_array($value))
        {
            $value = $this->convert_array_to_class($value);
        }

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
     * @return bool TRUE on success, FALSE on failure
     */
    public function offsetExists(mixed $offset): bool
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
    public function offsetUnset(mixed $offset): void
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
     * @return mixed The value of the requested offset or null if
     *               it doesn't exist.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->config[$offset] ?? NULL;
    }

    /**
     * Convert class content to an array.
     *
     * @return array<int|string,mixed> Array of all config values
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
     * @return mixed The current value of the config array
     */
    public function current(): mixed
    {
        return current($this->config);
    }

    /**
     * Return the key of the current element.
     *
     * (Inherited from Iterator)
     *
     * @return scalar Scalar on success, NULL on failure
     */
    public function key(): mixed
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
     * @return bool TRUE on success, FALSE on failure
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
     * @return int Size of the config array
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
