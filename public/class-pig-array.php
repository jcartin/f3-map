<?php

/**
 * Summary.
 * 
 * This class wraps a standard php array and help protect against out of bounds access. If 
 * an out of bounds indexer is used, the default value passed in the constructur (or NULL) 
 * will be returned.
 * 
 * code is taken from:
 * https://stackoverflow.com/questions/10300868/safely-get-array-element-value-for-defined-and-undefined-indexes 
 */
class PigArray implements ArrayAccess {

    private $array;
    private $default;

    public function __construct( array $array, $default = NULL ) {
        $this->array = $array;
        $this->default = $default;
    }

    public function offsetExists( $offset ) {
        return isset( $this->array[$offset] );
    }

    public function offsetGet( $offset ) {
        return isset( $this->array[$offset] ) ? $this->array[$offset] : $this->default;
    }

    public function offsetSet( $offset, $value ) {
        $this->array[$offset] = $value;
    }

    public function offsetUnset( $offset ) {
        unset( $this->array[$offset] );
    }

}

?>