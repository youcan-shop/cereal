<?php

namespace YouCanShop\Cereal\Laravel;

use Illuminate\Contracts\Database\ModelIdentifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Contracts\SerializationHandler;

final class ModelSerializationHandler implements SerializationHandler
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * @param Model $value
     *
     * @return ModelIdentifier|mixed
     */
    public function serialize(Serializable $serializable, $value)
    {
        return $this->getSerializedPropertyValue($value);
    }

    /**
     * @param ModelIdentifier|mixed $value
     *
     * @return mixed
     */
    public function deserialize(Serializable $serializable, $value)
    {
        return $this->getRestoredPropertyValue($value);
    }
}
