<?php

namespace YouCan\Cereal\Laravel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;
use YouCan\Cereal\Contracts\SerializationHandler;

final class ModelSerializationHandler implements SerializationHandler
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * @param Model $value
     *
     * @return mixed
     */
    public function serialize($value)
    {
        return $this->getSerializedPropertyValue($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function deserialize($value)
    {
        return $this->getRestoredPropertyValue($value);
    }
}
