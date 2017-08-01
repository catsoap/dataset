Dataset
=======

This repository hosts a helper class to programmatically format an array of records into a tree structure.

Example:

From this set of records:

```
[
  [
    'aggregate1' => '2017-07-01',
    'aggregate2' => 'foo',
    'metric1' => 51,
    'metric2' => 19,
    'metric3' => 7,
  ],
  [
    'aggregate1' => '2017-07-01',
    'aggregate2' => 'foo',
    'metric1' => 33,
    'metric2' => 13,
    'metric3' => 37,
  ],
  [
    'aggregate1' => '2017-07-02',
    'metric1' => 3,
    'metric2' => 35,
    'metric3' => 22,
  ],
]
```

We should get this:

```
[
  '2017-07-01' => [
    'foo' => [
      'metric1' => 84,
      'metric2' => 32,
      'metric3' => 44,
    ],
  ],
  '2017-07-02' => [
    'metric1' => 20
    'metric2' => 29
    'metric3' => 27
  ]
]
```

Here the metrics are summed, but it should be allowed to provide an aggregate function

@todo tests with raw dumb data