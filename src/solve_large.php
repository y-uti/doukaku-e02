<?php

function main($argc, $argv)
{
    if ($argc < 1) {
        echo "Usage: ${argv[0]} size\n";
        exit(0);
    }

    $size = intval($argv[1]);
    $problems = file('php://stdin');
    foreach ($problems as $problem) {
        list ($n, $cells) = read_problem($problem);
        list ($min, $max) = solve_problem($n, $cells, $size);
        echo $max != -1 ? "$min,$max\n" : "-\n";
    }
}

function read_problem($problem)
{
    list ($n, $data) = explode(':', $problem);
    $cells = array_map(function ($encoded) {
        list ($x, $y) = explode('/', $encoded);
        $x = intval($x);
        $y = intval($y);
        return array($x, $y);
    }, explode(',', $data));

    return array($n, $cells);
}

function solve_problem($n, $cells, $size)
{
    $whole_min = PHP_INT_MAX;
    $whole_max = -1;

    $xh = build_x_sorted_hash($cells);
    $xk = array_keys($xh);
    $xl = count($xk);

    for ($i1 = 0; $i1 < $xl; ++$i1) {
        $x0 = $i1 == 0 ? 0 : $xk[$i1 - 1] + 1;
        $x1 = $xk[$i1];
        $yl = array();
        for ($i2 = $i1; $i2 < $xl; ++$i2) {
            $x2 = $xk[$i2];
            $x3 = $i2 < $xl - 1 ? $xk[$i2 + 1] - 1 : $size - 1;
            $yl = array_merge($yl, $xh[$x2]);
            if (count($yl) < $n) {
                continue;
            }
            sort($yl);
            list ($min, $max) = search_rectangle($n, $yl, $size);
            if ($max != -1) {
                $whole_min = min($whole_min, $min * ($x2 - $x1 + 1));
                $whole_max = max($whole_max, $max * ($x3 - $x0 + 1));
            }
        }
    }

    return array($whole_min, $whole_max);
}

function build_x_sorted_hash($cells)
{
    $result = array();
    foreach ($cells as list ($x, $y)) {
        if (!array_key_exists($x, $result)) {
            $result[$x] = array();
        }
        $result[$x][] = $y;
    }

    ksort($result);
    foreach ($result as &$ys) {
        sort($ys);
    }

    return $result;
}

function search_rectangle($n, $ylist, $size)
{
    $min = PHP_INT_MAX;
    $max = -1;

    $ylen = count($ylist);
    for ($i = 0; $i < $ylen - $n + 1; ++$i) {
        $y0 = $i == 0 ? 0 : $ylist[$i - 1] + 1;
        $y1 = $ylist[$i];
        if ($y0 <= $y1) {
            $y2 = $ylist[$i + $n - 1];
            $y3 = $i < $ylen - $n ? $ylist[$i + $n] - 1 : $size - 1;
            if ($y2 <= $y3) {
                $min = min($min, $y2 - $y1 + 1);
                $max = max($max, $y3 - $y0 + 1);
            }
        }
    }

    return array($min, $max);
}

main($argc, $argv);
