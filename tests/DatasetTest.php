<?php

use Ekino\Dataset;
use PHPUnit\Framework\Constraint\IsType;

class DataSetTest extends \PHPUnit\Framework\TestCase
{
    private static $day = 1;

    /**
     * @dataProvider providerDataset
     */
    public function testToArray($rows, $aggregates, $expected)
    {
        $ds = new Dataset($aggregates);

        foreach ($rows as $row) {
            $ds->addRow($row);
        }

        $this->assertEquals($expected, $ds->toArray());
    }

    public function providerDataset()
    {
        $ret = [];
        for ($j=1; $j<=5;$j++) {
            $rows = $expected = [];
            for ($i=1;$i<=10;$i++) {
                $aggregates = $this->getAggregates($j);
                $row = (object) $this->getRow($aggregates);

                $rows[] = $row;

                $aggregates = array_keys($aggregates);
                $current = &$expected;
                foreach ($aggregates as $aggregate) {
                    $current = &$current[(string) $row->{$aggregate}];
                }

                $current['metric1'][] = $row->metric1;
                $current['metric2'][] = $row->metric2;
                $current['metric3'][] = $row->metric3;
            }

            $ret[sprintf('%d aggregates', $j)] = [$rows, $aggregates, $expected];
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
            'aggregate2' => range(1,10)[rand(0,9)],
            'aggregate3' => range(1,23)[rand(0,22)],
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
        return rand(0,1) ? ++static::$day : static::$day;
    }

    public function testFormatRow() {
        $ds = new Dataset(['whatever']);

        $row = $this->invokeMethod($ds, 'formatRow', [[123], 0]);
        $keys = array_flip($row);
        $this->assertInternalType(IsType::TYPE_STRING, $keys[0]);
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

