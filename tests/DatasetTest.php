<?php

use Ekino\Dataset;

class DataSetTest extends \PHPUnit\Framework\TestCase
{
    private static $day = 1;

    /**
     * @dataProvider providerDatasetOneDimension
     * @dataProvider providerDatasetTwoDimensions
     * @dataProvider providerDatasetMultipleDimensions
     */
    public function testToArray($aggregates, $rows, $expected)
    {
        $ds = new Dataset($aggregates);

        foreach ($rows as $row) {
            $ds->addRow($row);
        }

        $this->assertEquals($expected, $ds->toArray());
    }

    public function providerDatasetOneDimension()
    {
        $rows = $expected = [];
        for ($i=1;$i<=10;$i++) {
            $row = $this->getRowOneAggregate();
            $rows[] = $row;

            $expected = array_merge_recursive(
                $expected, [
                    $row['aggregate1'] => [
                        'metric1'    => $row['metric1'],
                        'metric2'    => $row['metric2'],
                        'metric3'    => $row['metric3'],
                    ]
                ]);
        }

        return [[['aggregate1'], $rows, $expected]];
    }

    public function providerDatasetTwoDimensions()
    {
        $rows = $expected = [];
        for ($i=1;$i<=10;$i++) {
            $row = $this->getRowTwoAggregates();
            $rows[] = $row;

            $expected = array_merge_recursive(
                $expected, [
                $row['aggregate1'] => [
                    $row['aggregate2'] => [
                        'metric1'    => $row['metric1'],
                        'metric2'    => $row['metric2'],
                        'metric3'    => $row['metric3'],
                    ]]
            ]);
        }

        return [[['aggregate1', 'aggregate2'], $rows, $expected]];
    }

    public function providerDatasetMultipleDimensions()
    {
        $rows = $expected = [];
        for ($i=1;$i<=10;$i++) {
            $row = $this->getRowMultipleAggregates();
            $rows[] = $row;

            $expected = array_merge_recursive(
                $expected, [
                $row['aggregate1'] => [
                        $row['aggregate2'] => [
                            $row['aggregate3'] => [
                                $row['aggregate4'] => [
                                    $row['aggregate5'] => [
                                        'metric1'    => $row['metric1'],
                                        'metric2'    => $row['metric2'],
                                        'metric3'    => $row['metric3'],
                ]]]]]
            ]);
        }

        return [[['aggregate1', 'aggregate2', 'aggregate3', 'aggregate4', 'aggregate5'], $rows, $expected]];
    }

    protected function getRowOneAggregate()
    {
        $row = [
            'aggregate1' => '2017-07-' . str_pad($this->getDay(), 2, '0', STR_PAD_LEFT),
            'metric1'    => rand(1, 55),
            'metric2'    => rand(1, 55),
            'metric3'    => rand(1, 55),
        ];

        return $row;
    }

    protected function getRowTwoAggregates()
    {
        return array_merge($this->getRowOneAggregate(), [
            'aggregate2' => range(1,2)[rand(0,1)] . ' ',
        ]);
    }

    protected function getRowMultipleAggregates()
    {
        return array_merge($this->getRowOneAggregate(), [
            'aggregate2' => range(1,10)[rand(0,9)],
            'aggregate3' => range(1,23)[rand(0,22)],
            'aggregate4' => ['foo', 'bar', 'baz', 'bal', 'buz', 'bli'][rand(0,5)],
            'aggregate5' => ['didi', 'dudu', 'dada', 'dodo', 'dede'][rand(0, 4)],
        ]);
    }

    protected function getDay()
    {
        return rand(0,1) ? ++static::$day : static::$day;
    }
}

