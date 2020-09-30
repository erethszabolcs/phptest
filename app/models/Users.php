<?php

namespace Models;

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Users extends \Phalcon\Mvc\Model
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
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => 'Please enter a correct email address',
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
        $this->setSource("user");

        // 1-n relation to Ratings
        $this->hasMany('id', 'Models\Ratings', 'user_id', ['alias' => 'ratings']);

        // n-n relation to Products through Ratings
        $this->hasManyToMany('id', 'Models\Ratings', 'user_id', 'product_id', 'Models\Products', 'id', ['alias' => 'products']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]|User|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Function to tell if User rated a specific Product or not
     */
    public function ratedThisProduct(Products $product) {
        if(!$product) {
            return false;
        }

        return $this->getRelated('products', ['[Models\Products].[id] = ' . $product->id])->count();
    }

}
