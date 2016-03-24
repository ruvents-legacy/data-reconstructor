# RUVENTS Data Reconstructor

Data reconstructor helps to process data after `json_decode` and allows to use any classes rather than `stdClass` to be filled with data.

It receives a nested associative array and returns a data structure of any complexity built of objects.

## Installation

`$ composer require ruvents/data-reconstructor`

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

Data constructor uses [The Symfony PropertyAccess Component](http://symfony.com/doc/current/components/property_access/index.html) to access the properies of a constructed class. Along with public properties you can use private and protected properties with appropriate setters or even a magic `__set` method.

Setters have higher priority, which means that if a public property has a setter method, the Property Access Component will use the setter instead of writing to the property directly.

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
     * @var string
     */
    private $title;

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }
}
```

Nonexistent and nonaccesible properties will not be filled with data without throwing any errors.

## Gain control over the whole process

If you wish to use your own logic, you can implement the `Ruvents\DataReconstructor\ReconstructInterface` interface:

```php
<?php

namespace Model;

use Ruvents\DataReconstructor\ReconstructInterface;
use Ruvents\DataReconstructor\DataReconstructor;

class Musician implements ReconstructInterface
{
    /**
     * @inheritdoc
     */
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map) {
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
    public function reconstruct(DataReconstructor $dataReconstructor, &$data, array $map) {
        $this->id = (int)$data['id'];

        return false;
    }
}
```

Return `false` to finish reconstruction of the current object.

You can also use `$dataReconstructor->reconstruct($data, $className)` to reconstruct other pieces of data from inside the implemented method.
