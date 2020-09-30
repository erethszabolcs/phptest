<?php

namespace Models\Resultsets;

use \Phalcon\Mvc\Model\Resultset\Simple;

class ProductResultset extends Simple
{
    /**
     * Override toArray() to call toArrayWithAvgRating() on individual models
     */
    public function toArray(bool $renameColumns = NULL): array {
        $data = [];
        foreach($this as $product) {
            $data []= $product->toArrayWithAvgRating();
        }
        return $data;
    }
}