<?php

namespace Ekino;

class Dataset
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $aggregates;

    public function setAggregates(array $aggregates) : void
    {
        $this->aggregates = $aggregates;
    }

    public function addRow(array $row) : void
    {
        $aggregates = array();

        foreach ($this->aggregates as $aggregate) {
            if (!array_key_exists($aggregate, $row)) {
                throw new \InvalidArgumentException(sprintf('%s is not a valid aggregate (row name not found)', $aggregate));
            }

            $index = array_search($aggregate, array_keys($row));
            $values = array_splice($row, $index, 1);
            $aggregates[] = array_pop($values);
        }

        $row = $this->getRow($row);
        $row = $this->formatRow($aggregates, $row);

        $this->data = array_merge_recursive($this->data, $row);
    }

    protected function formatRow(array $aggregates, array $row) : array
    {
        $aggregate = array_pop($aggregates);

        $row = is_numeric($aggregate)
            ? $this->numKeyToString($aggregate, $row)
            : array($aggregate => $row);

        if (empty($aggregates)) return $row;

        return $this->formatRow($aggregates, $row);
    }

    protected function numKeyToString(string $key, array $item) : array
    {
        $o = new \StdClass();
        $o->{$key} = $item;
        $item = (array) $o;

        return $item;
    }

    protected function getRow($row)
    {
        return $row;
    }

    public function getAggregatedKeys(array $record) : array
    {
        return array_diff(array_keys($record), $this->aggregates);
    }

    public function formatValues(array $keyList, callable $callback) : Dataset
    {
        $this->doFormatValues($this->data, $keyList, $callback);

        return $this;
    }

    public function formatRows(array $keyList, callable $callback) : Dataset
    {
        $this->doFormatRows($this->data, $keyList, $callback);

        return $this;
    }

    protected function doFormatValues(array &$element, array $keyList, callable $callback) {
        foreach ($element as $key => &$value) {
            if (is_array($value)) {
                if (in_array($key, $keyList, true)) {
                    $value = call_user_func($callback, $value);
                } else {
                    $this->doFormatValues($value, $keyList, $callback);
                }
            }
        }
    }

    protected function doFormatRows(array &$element, array $keyList, callable $callback) {
        foreach ($element as $key => &$value) {
            if (is_array($value)) {
                if (!array_intersect_key(array_flip($keyList), $value)) {
                    $this->doFormatRows($value, $keyList, $callback);
                } else {
                    $value = call_user_func($callback, $value);
                }
            }
        }
    }

    public function toArray() : array
    {
        return $this->data;
    }
}

