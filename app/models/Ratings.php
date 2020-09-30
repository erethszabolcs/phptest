<?php

namespace Models;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Numericality;

class Ratings extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $product_id;

    /**
     *
     * @var integer
     */
    public $value;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'product_id',
            new PresenceOf(
                [
                    'message' => 'Rating must have a product_id',
                ]
            )
        );

        $validator->add(
            'user_id',
            new PresenceOf(
                [
                    'message' => 'Rating must have a user_id',
                ]
            )
        );

        $validator->add(
            'value',
            new PresenceOf(
                [
                    'message' => 'You must give a value to your rating',
                ]
            )
        );

        $validator->add(
            'value',
            new Between(
                [
                    "minimum" => 1,
                    "maximum" => 10,
                    "message" => "The rating value must be between 1 and 10",
                ]
            )
        );

        $validator->add(
            'value',
            new Numericality(
                [
                    'message' => 'The value must be a numeric value',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phptest");
        $this->setSource("rating");

        // n-1 relation to Products
        $this->belongsTo('product_id', 'Models\Products', 'id', ['alias' => 'product']);

        // n-1 relation to Users
        $this->belongsTo('user_id', 'Models\Users', 'id', ['alias' => 'user']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Rating[]|Rating|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Rating|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
