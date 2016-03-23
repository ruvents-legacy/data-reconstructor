# RUVENTS Data Reconstructor

Data reconstructor helps to process data after `json_decode` and allows to use any classes rather than `stdClass` to be filled with data.

It receives a nested associative array and returns a data structure of any complexity built of objects.

## Installation

`$ composer require ruvents/data-reconstructor:^1.0`

## Usage

```php
<?php

use Ruvents\DataReconstructor\DataReconstructor;

$reconstructor = new DataReconstructor($config);

$data = $reconstructor->reconstruct($assocArrayOfRawData, 'Namespace\InitialClassName');
```

## Configuration

```php
<?php

$config = [
    'map' => [
        'Namespace\InitialClassName' => [
            'property' => 'Namespace\InitialClassName',
            'arrayProp' => 'Namespace\ClassName[]',
        ],
        'Namespace\ClassName' => [
            'propertyTwo' => 'Namespace\ClassNameThree'
        ],
        // ...
    ],
];
```

## Example

Get a json response from somewhere:

```json
{
    "id": 1,
    "name": "Guns N' Roses",
    "genres": [
        "rock"
    ],
    "musicians": [
        {
            "id": 1,
            "name": "Axl Rose",
            "instruments": [
                {
                    "id": 1,
                    "title": "vocals"
                },
                {
                    "id": 2,
                    "title": "piano"
                }
            ]
        },
        {
            "id": 2,
            "name": "Slash"
        },
        {
            "id": 3,
            "name": "Matt Sorum"
        }
    ]
}
```

Parse it into an associative array:

```php
<?php

$rawData = json_decode($json, true);
```

Create models for bands, musicians and instruments:

```php
<?php

namespace Model;

class Band
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $genres;

    /**
     * @var Musician[]
     */
    public $musicians;
}

class Musician
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Instrument[]
     */
    public $instruments;
}

class Instrument
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;
}
```

Configure class relations:

```php
<?php

$config = [
    'map' => [
        'Model\Band' => [
            'musicians' => 'Model\Musician[]',
        ],
        'Model\Musician' => [
            'instruments' => 'Model\Instrument[]',
        ],
    ],
];
```

Reconstruct data by passing the name of the initial class:

```php
<?php

use Ruvents\DataReconstructor\DataReconstructor;

$reconstructor = new DataReconstructor($config);
$data = $reconstructor->reconstruct($rawData, 'Model\Band');
var_dump($data);
```

Receive the result:

```
object(Model\Band)[3]
  public 'id' => int 1
  public 'name' => string 'Guns N' Roses' (length=13)
  public 'genres' =>
    array (size=1)
      0 => string 'rock' (length=4)
  public 'musicians' =>
    array (size=3)
      0 =>
        object(Model\Musician)[5]
          public 'id' => int 1
          public 'name' => string 'Axl Rose' (length=8)
          public 'instruments' =>
            array (size=2)
              0 =>
                object(Model\Instrument)[6]
                  public 'id' => int 1
                  public 'title' => string 'vocals' (length=6)
              1 =>
                object(Model\Instrument)[7]
                  public 'id' => int 2
                  public 'title' => string 'piano' (length=5)
      1 =>
        object(Model\Musician)[8]
          public 'id' => int 2
          public 'name' => string 'Slash' (length=5)
          public 'instruments' => null
      2 =>
        object(Model\Musician)[9]
          public 'id' => int 3
          public 'name' => string 'Matt Sorum' (length=10)
          public 'instruments' => null
```

## Property Access

Data constructor uses [The Symfony PropertyAccess Component](http://symfony.com/doc/current/components/property_access/index.html) to access the properies of a constructed class.

This means that you can have private and protected properies with setters like this:

```php
<?php

namespace Model;

class Instrument
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
```

## Gain control over the whole process

If you wish to use your own logic for reconstructing process, you can implement the `Ruvents\DataReconstructor\ReconstructInterface` interface:

```php
<?php

namespace Model;

use Ruvents\DataReconstructor\ReconstructInterface;
use Ruvents\DataReconstructor\DataReconstructor;

class Musician implements ReconstructInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @inheritdoc
     */
    public function reconstruct(&$data, DataReconstructor $dataReconstructor) {
        $data['name'] = 'Cool guy Mr. '.$data['name'];
    }
}

class Instrument implements ReconstructInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @inheritdoc
     */
    public function reconstruct(&$data, DataReconstructor $dataReconstructor) {
        $this->id = (int)$data['id'];

        return false;
    }
}
```

Return `false` to finish the reconstruction of this object.
