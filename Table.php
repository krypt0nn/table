<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     Table
 * @copyright   2021 Podvirnyy Nikita (Observer KRypt0n_)
 * @license     GNU GPL-3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @author      Podvirnyy Nikita (Observer KRypt0n_)
 * 
 * Contacts:
 *
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    <https://vk.com/technomindlp>
 *        <https://vk.com/hphp_convertation>
 * 
 */

namespace Table;

class Table
{
    protected array $columns = [];
    protected array $items   = [];

    /**
     * [@var string $delimiter = "\0"] - character or string which will be used to split items in encoded strings
     */
    public string $delimiter = "\0";

    /**
     * [@param array $columns = []] - array of columns titles
     * [@param array $items = []] - array of items
     * 
     * @example
     * 
     * $table = new Table (['id', 'name'], [
     *     [1, 'hello'],
     *     [2, 'world']
     * ]);
     * 
     * or just
     * 
     * $table = new Table;
     */
    public function __construct (array $columns = [], array $items = [])
    {
        $this->columns = array_filter ($columns, 'is_scalar');
        $this->items = $items;
    }

    /**
     * Get columns titles or set them
     * 
     * [@param array $columns = null] - list of columns titles if you want to set them
     */
    public function columns (array $columns = null): mixed
    {
        if ($columns === null)
            return $this->columns;

        else
        {
            $this->columns = array_filter ($columns, 'is_scalar');
            
            return $this;
        }
    }

    /**
     * Get list of items or set them
     * 
     * [@param array $items = null] - list of items if you want to set them
     * 
     * @return array - returns list of existing items
     */
    public function items (array $items = null): array
    {
        if ($items === null)
            return $this->items;

        else
        {
            $this->items = $items;
            
            return $this;
        }
    }

    /**
     * Get number of items in table
     * 
     * @return int
     */
    public function size (): int
    {
        return sizeof ($this->items);
    }

    /**
     * Add new item
     * 
     * @param array $item
     * 
     * @return self
     */
    public function push (array $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Add list of new items
     * 
     * @param array $items
     * 
     * @return self
     */
    public function merge (array $items): self
    {
        $this->items = array_merge ($this->items, $items);

        return $this;
    }

    /**
     * Get table of items by some filtering function
     * 
     * @param callable $filter (array $item, array $columns)
     * 
     * @return self
     */
    public function where (callable $filter): self
    {
        $items = [];

        foreach ($this->items as $item)
            if ($filter ($item, $this->columns))
                $items[] = $item;

        return new self ($this->columns, $items);
    }

    /**
     * Execute function on each item in table
     * 
     * @param callable $iterator (array $item, array $columns)
     * 
     * @return self
     */
    public function foreach (callable $iterator): self
    {
        foreach ($this->items as $item)
            $iterator ($item, $this->columns);

        return $this;
    }

    /**
     * Get list of items with titled indexes
     * 
     * @return array
     */
    public function get (): array
    {
        $result = [];

        foreach ($this->items as $item)
            $result[] = array_combine ($this->columns, $item);

        return $result;
    }

    /**
     * Get string that representing this table
     * 
     * @return string
     */
    public function encode (): string
    {
        return $this->slash (sizeof ($this->columns)) . $this->delimiter .
               join ($this->delimiter, array_map ([$this, 'slash'], $this->columns)) . $this->delimiter .
               join ($this->delimiter, array_map (function (array $item)
                {
                    return join ($this->delimiter, array_map ([$this, 'slash'], $item));
                }, $this->items));
    }

    /**
     * Get table represented by given string
     * 
     * @return self
     */
    public function decode (string $string): self
    {
        $data = $this->deslash ($string);
        $item = [];

        $this->columns = array_slice ($data, 1, $data[0]);
        $this->items   = [];

        for ($i = $data[0] + 1, $l = sizeof ($data); $i < $l; $i += $data[0])
        {
            for ($j = 0; $j < $data[0]; ++$j)
                $item[] = $data[$i + $j];

            $this->items[] = $item;
            $item = [];
        }

        return $this;
    }

    protected function slash (string $string): string
    {
        $newString = '';

        for ($i = 0, $l = strlen ($string); $i < $l; ++$i)
        {
            $newString .= $string[$i];

            if ($string[$i] == $this->delimiter)
                $newString .= $this->delimiter;
        }

        return $newString;
    }

    protected function deslash (string $string): array
    {
        $items = [];
        $item  = '';

        $screened = false;

        for ($i = 0, $l = strlen ($string); $i < $l; ++$i)
        {
            if ($string[$i] == $this->delimiter)
            {
                if ($screened == false)
                    $screened = true;

                else
                {
                    $item .= $this->delimiter;

                    $screened = false;
                }
            }

            elseif ($screened == true)
            {
                $items[] = $item;
                $item    = $string[$i];

                $screened = false;
            }

            else $item .= $string[$i];
        }

        $items[] = $item;

        return $items;
    }
}
