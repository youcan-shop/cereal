<p align="center">
<img width="150" height="150" src="https://github.com/youcan-shop/cereal/blob/main/assets/cereallogo.svg" alt="Cereal package logo"/>
<br><b>Cereal</b>
</p>

[![Tests](https://github.com/youcan-shop/Cereal/actions/workflows/tests.yml/badge.svg)](https://github.com/NextmediaMa/Cereal/actions/workflows/tests.yaml)
[![Total Downloads](https://img.shields.io/packagist/dt/youcanshop/cereal.svg?style=flat-square)](https://packagist.org/packages/youcanshop/cereal)
[![License](https://img.shields.io/github/license/youcan-shop/Cereal?style=flat-square)](https://github.com/NextmediaMa/Cereal/blob/master/LICENSE.md)

Cereal is a PHP package that allows you to easily serialize and deserialize your data.

## Installation

You can install the package via composer:

```bash
composer require youcanshop/cereal
```

## Usage

To start using the package, you need to create a class that implements the `YouCanShop\Cereal\Contracts\Serializable` interface, and use the `YouCanShop\Cereal\Cereal` trait which
contains some helpers for a default configuration.

```php
class User implements Serializable
{
    use Cereal;

    public int $age;
    public string $name;
    public float $weight;
    public bool $isStraight;

    public function __construct(
        string $name,
        int $age,
        float $weight,
        bool $isStraight
    ) {
        $this->age = $age;
        $this->name = $name;
        $this->weight = $weight;
        $this->isStraight = $isStraight;
    }

    public function serializes(): array
    {
        return ['name', 'age', 'weight', 'isStraight'];
    }
}
```

The `serializes` method returns an array of the properties that you want to serialize. Then the package will automatically serialize and deserialize the data based on the type
hinted properties.

> **Note** 
> - The package will only serialize public properties that are type hinted.
> - The package will serialize properties in the order they are defined in the `serialize` method. 

### Serialization Handlers

In most uses cases, you will need to serialize and deserialize some properties in a different way. For example, you may want to serialize a `DateTime` object to a timestamp, or
serialize a `User` object to an array or something.
For that, you're going to create your own serialization handler and add it to the `YouCanShop\Cereal\SerializationHandlerFactory`.

```php
class UserHandler implements SerializationHandler {

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function serialize(Serializable $serializable, $value): string
    {
        return $value->getId();
    }

    /**
     * @param string $value
     *
     * @return ?User
     */
    public function deserialize(Serializable $serializable, $value): ?User
    {
        return $this->userRepository->find($value);
    }
}
```

In the above example, when trying to serialize a user object, we serialize only the ID, and when trying to deserialize it, we fetch it again from database.

Then, you need to add your handler to the `YouCan\Cereal\SerializationHandlerFactory`:

```php
use YouCan\Cereal\SerializationHandlerFactory;

SerializationHandlerFactory::getInstance()
    ->addHandler(User::class, new UserHandler($userRepository));
```

Now, when working with our object that will be serialized, and if it contains a `User` instance, it will automatically be serialized and deserialized using our handler.

```php
class Post implements Serializable
{
    use SerializeTrait;

    public string $title;
    public string $content;
    
    public User $author;

    public function __construct(string $title, string $content, User $author)
    {
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
    }

    public function serializes(): array
    {
        return ['title', 'content', 'author'];
    }
}
```

### Laravel Bridge

// TODO
