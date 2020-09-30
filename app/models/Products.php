<?php

namespace Models;

use Models\Ratings;
use Phalcon\Validation;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Alnum;
use Models\ResultSets\ProductResultset;

class Products extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var double
     */
    public $price;

    /**
     * Validations and business logic
     *
     * @return boolean
     */

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'code',
            new StringLength(
                [
                    'min'     => 6,
                    'max'     => 6,
                    'message' => 'Code must be 6 digits long',
                ]
            )
        );

        $validator->add(
            'code',
            new Alnum(
                [
                    'message' => 'Code must contain only alphanumeric characters',
                ]
            )
        );

        $validator->add(
            'name',
            new PresenceOf(
                [
                    'message' => 'The name is required',
                ]
            )
        );

        $validator->add(
            'price',
            new PresenceOf(
                [
                    'message' => 'The price is required',
                ]
            )
        );

        $validator->add(
            'price',
            new Numericality(
                [
                    'message' => 'The price must be a numeric value',
                ]
            )
        );

        $validator->add(
            'price',
            new Between(
                [
                    "minimum" => 0,
                    "maximum" => INF,
                    "message" => "The price must be greater than 0",
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
        $this->setSource("product");

        // 1-n relation to Ratings
        $this->hasMany('id', 'Models\Ratings', 'product_id', ['alias' => 'ratings']);

        // n-n relation to Users through Ratings
        $this->hasManyToMany('id', 'Models\Ratings', 'product_id', 'user_id', 'Models\Users', 'id', ['alias' => 'users']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Product[]|Product|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Product|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Use of custom Resultset to use overriden toArray() on Resultset
     */
    public function getResultsetClass()
    {
        return ProductResultset::class;
    }

    /**
     * Function to extend toArray() with virtual field avg_rating
     */
    public function toArrayWithAvgRating($columns = null) {
        // calls the regular toArray() method
        $data = parent::toArray($columns);

        // adds the average rating of this product as a new key-value pair
        $data['avg_rating'] = Ratings::average(
            [
                'column' => 'value',
                'conditions' => 'product_id = ' . $this->id,
            ]
        );
    
        return $data;
    }
}
