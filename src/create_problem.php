<?php

if ($argc < 3) {
    echo "Usage: ${argv[0]} N C [size = 62]\n";
    echo "  N\tThe number of cells should be included in a rectangle\n";
    echo "  C\tThe total number of cells on the board\n";
    echo "  size\tThe size (the width and the height) of the board\n";
    exit(0);
}

$chars = array_merge(range('0', '9'), range('A', 'Z'), range('a', 'z'));

$n = intval($argv[1]);
$c = intval($argv[2]);
$s = intval($argv[3] ?? count($chars));

$p = range(0, $s ** 2 - 1);
shuffle($p);
$p = array_slice($p, 0, $c);

$encoded = array();
foreach ($p as $cell) {
    $x = $cell % $s;
    $y = intval($cell / $s);
    $encoded[] = $s <= count($chars) ? $chars[$x] . $chars[$y] : "$x/$y";
}

echo $n, ':', implode(',', $encoded), "\n";
