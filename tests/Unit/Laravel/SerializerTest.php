<?php

namespace Tests\Unit\Laravel;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Laravel\Cereal;
use YouCanShop\Cereal\Laravel\SerializationHandlerFactory as LaravelSerializationHandlerFactory;
use YouCanShop\Cereal\SerializationHandlerFactory as BaseSerializationHandlerFactory;

it('serializes eloquent models', function () {
    $capsule = new Capsule;

    $capsule->addConnection(
        [
            "driver" => "sqlite",
            "database" => ':memory:',
        ]
    );

    $capsule->setAsGlobal();

    $capsule->bootEloquent();

    $capsule->getContainer()->singleton(
        BaseSerializationHandlerFactory::class,
        fn() => LaravelSerializationHandlerFactory::getInstance()
    );

    if (Capsule::schema()->hasTable('users') === false) {
        Capsule::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('full_name');
        });
    } else {
        Capsule::table('users')->truncate();
    }

    class MModel extends Model
    {
    }

    /**
     * @property int $id
     * @property string $full_name
     */
    class User extends MModel
    {
        /** @var string[] */
        protected $guarded = [];
        public $timestamps = false;
    }

    User::query()->create(['full_name' => 'Aymane']);

    class WelcomeEmail implements Serializable
    {
        use Cereal;

        public User $user;

        public function __construct(User $user)
        {
            $this->user = $user;
        }

        public function serializes(): array
        {
            return ['user'];
        }

    }

    /** @var User $user */
    $user = User::query()->first();
    $welcomeEmail = new WelcomeEmail($user);
    $serializedEmail = serialize($welcomeEmail);
    $deserializedEmail = unserialize($serializedEmail);

    expect($deserializedEmail->user->id)->toBe($user->id);
    expect($deserializedEmail->user->full_name)->toBe($user->full_name);
});
