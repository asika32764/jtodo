<?php

/**
 * Print Array or Object as tree node. If send multiple params in this method, will batch print it.
 * 
 * @param    mixed    $data    Array or Object to print.
 */
function show($data)
{
    $args   = func_get_args();
    
    // Print Multiple values
    if(count($args) > 1) {    
        $prints = array();
        
        $i = 1 ;
        foreach( $args as $arg ):
            $prints[] = "[Value " . $i . "]\n" . print_r($arg, 1);
            $i++ ;
        endforeach;
        
        echo '<pre>'.implode("\n\n", $prints).'</pre>' ;
    }else{
        // Print one value.
        echo '<pre>'.print_r($data, 1).'</pre>' ;
    }        
}

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
function array_merge_recursive_distinct ( array $array1, array $array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      $merged [$key] = $value;
    }
  }

  return $merged;
}