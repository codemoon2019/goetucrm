<?php

namespace Guesl\CardConnect\CoPilot\Models;

use JsonSerializable;

abstract class Model implements JsonSerializable
{
    protected static $classes = [
        "demographic" => Demographic::class,
        "bankDetail" => BankDetail::class,
        "ownership" => Ownership::class,
        "merchantContactInfo" => MerchantContactInfo::class,
        "processing" => Processing::class,
        "pricing" => Pricing::class,
        "fee" => Fee::class,
        "customerField" => CustomField::class,
        "depositBank" => Bank::class,
        "withdrawalBank" => Bank::class,
        "businessAddress" => Address::class,
        "mailingAddress" => Address::class,
        "ownerAddress" => Address::class,
        "platformDetails" => PlatformDetails::class,
        "businessDetails" => BusinessDetails::class,
        "volumeDetails" => VolumeDetails::class,
        "deliveryPercentages" => DeliveryDetails::class,
        "modeOfTransaction" => TransactionDetails::class,
        "flatPricing" => FlatPricing::class,
        "icPlusPricing" => ICPlusPricing::class,
        "signer" => Signer::class,
        "merchantStatus" => MerchantStatus::class,
    ];

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    protected function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Populate the object by the values array.
     *
     * @param array $values
     * @return $this
     */
    public function populate(array $values)
    {
        $classes = static::$classes;
        $hasAttributes = get_object_vars($this);
        foreach ($hasAttributes as $name => $oldValue) {
            if (array_key_exists($name, $values)) {
                $newValue = $values[$name];
                if (isset($newValue) && is_array($newValue) && array_key_exists($name, $classes)) {
                    $className = $classes[$name];
                    if (class_exists($className)) {
                        $this->$name = (new $className)->populate($newValue);
                    }
                } else {
                    $this->$name = isset($newValue) ? $newValue : $oldValue;
                }

                // Special
                $this->populateDefinition($name, $newValue, $oldValue);
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @param $newValue
     * @param $oldValue
     * @return bool
     */
    protected function populateDefinition($name, $newValue, $oldValue)
    {
        return false;
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);
        return $json;
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
