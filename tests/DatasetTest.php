<?php

use Ekino\Dataset;
use PHPUnit\Framework\Constraint\IsType;

class DatasetTest extends \PHPUnit\Framework\TestCase
{
    private static $day = 1;

    /**
     * @dataProvider providerDataset
     */
    public function testToArray($rows, $aggregates, $expected)
    {
        $ds = new Dataset();

        $ds->setAggregates($aggregates);

        foreach ($rows as $row) {
            $ds->addRow((array) $row);
        }

        $keysList = $ds->getAggregatedKeys((array) $row);
        //$ds->formatValues($keysList, 'array_sum');

        $this->assertEquals($expected, $ds->toArray());
    }

    public function providerDataset()
    {
        $ret = [];
        for ($j=1; $j<=5;$j++) {
            $rows = $expected = [];
            for ($i=1;$i<=10;$i++) {
                $aggregates = $this->getAggregates($j);
                $rows[] = $row = (object) $this->getRow($aggregates);

                $aggregates = array_keys($aggregates);
                $current = &$expected;
                foreach ($aggregates as $aggregate) {
                    $current = &$current[(string) $row->{$aggregate}];
                }

                $current['metric1'][] = $row->metric1;
                $current['metric2'][] = $row->metric2;
                $current['metric3'][] = $row->metric3;
            }

            $this->formatValues($expected, ['metric1', 'metric2', 'metric3']);

            $ret[sprintf('nb aggregates: %d', $j)] = [$rows, $aggregates, $expected];
        }

        return $ret;
    }

    protected function getRow($aggregates)
    {
        return array_merge($aggregates, [
            'metric1'    => rand(1, 55),
            'metric2'    => rand(1, 55),
            'metric3'    => rand(1, 55),
        ]);
    }

    protected function getAggregates($num)
    {
        $aggregates = [
            'aggregate1' => '2017-07-' . str_pad($this->getDay(), 2, '0', STR_PAD_LEFT),
            'aggregate2' => ['foo', 'bar', 'baz', 'bal', 'buz', 'bli'][rand(0,5)],
            'aggregate3' => ['foo', 'bar', 'baz', 'bal', 'buz', 'bli'][rand(0,5)],
            'aggregate4' => ['foo', 'bar', 'baz', 'bal', 'buz', 'bli'][rand(0,5)],
            'aggregate5' => ['didi', 'dudu', 'dada', 'dodo', 'dede'][rand(0, 4)],
        ];

        if (count($aggregates) < $num) {
            throw new \InvalidArgumentException(sprintf('There is only %d available aggregates', count($aggregates)));
        }

        return array_slice($aggregates, 0, $num);
    }

    protected function getDay()
    {
        return static::$day = (static::$day > 0 && static::$day <= 30) ? ++static::$day : 1;
    }

    public function testFormatRow() {
        $ds = new Dataset();

        $row = $this->invokeMethod($ds, 'formatRow', [[321], [123]]);
        $keys = array_keys($row);
        $this->assertInternalType(IsType::TYPE_STRING, $keys[0]);
    }

    protected function formatValues(&$element, $keyList) {
        foreach($element as $key => &$value) {
            if( in_array( $key, $keyList) ) {
                if ($key && (1 === count($value))) {
                    $value = array_pop($value);
                }
            } else {
                $this->formatValues($value, $keyList);
            }
        }
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

