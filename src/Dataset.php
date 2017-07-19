<?php

namespace Ekino;

class Dataset
{
    protected $data;

    protected $aggregates;

    public function __construct(array $aggregates)
    {
        if (empty($aggregates)) {
            throw new \InvalidArgumentException('You must specify at least one aggregate');
        }

        $this->data = array();
        $this->aggregates = $aggregates;
    }

    public function addRow($row)
    {
        $aggregates = [];

        foreach ($this->aggregates as $aggregate) {
            if (!array_key_exists($aggregate, $row)) {
                throw new \InvalidArgumentException(sprintf('%s is not a valid aggregate (row name not found)', $aggregate));
            }

            $index = array_search($aggregate, array_keys($row));
            $values = array_splice($row, $index, 1);
            $aggregates[] = array_pop($values);
        }

        $row = $this->formatRow($aggregates, $row);

        $this->data = array_merge_recursive($this->data, $row);
    }

    protected function formatRow($aggregates, $row)
    {
        $row = [array_pop($aggregates) => $row];
        if (empty($aggregates)) return $row;

        return $this->formatRow($aggregates, $row);
    }

    public function toArray()
    {
        return $this->data;
    }
}
