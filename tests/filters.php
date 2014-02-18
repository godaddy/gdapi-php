<?php
  include('class/Type.php');

  test('', array());

  test('k=42', array('k' => 42));

  test('k_ne=42', array('k' => array('modifier' => 'ne', 'value' => 42)));

  test('k_ne=43&k_gt=44', array(
    'k' => array(
      array('modifier' => 'ne', 'value' => 43),
      array('modifier' => 'gt', 'value' => 44)
    )
  ));

  test('k_ne=43&k_gt=44&k=45&j=46',array(
      'k' => array(
        array('modifier' => 'ne', 'value' => 43),
        array('modifier' => 'gt', 'value' => 44),
        45
      ),
      'j' => 46,
    )
  );

  function test($expect, $filters)
  {
    $base = 'http://a.com';
    $got = GDAPI\Type::listHref($base,array('filters' => $filters));

    $index = strpos($got,'?');
    if ( $index === FALSE )
      $query = "";
    else
      $query = substr($got,$index+1);

    echo "Expect: $expect\nGot: $query\nResult: " . ($query == $expect ? "ok" : "FAIL") . "\n\n";
  }

?>
