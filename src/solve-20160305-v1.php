<?php

function read_problem($problem)
{
    list ($n, $data) = explode(':', $problem);
    $stones = explode(',', $data);
    $positions = array();
    foreach ($stones as $s) {
        $x = to_num($s[0]);
        $y = to_num($s[1]);
        $positions[] = array($x, $y);
    }

    return array($n, $positions);
}

function to_num($char)
{
    if (($pos = ord($char) - ord('0')) < 10) {
        return $pos;
    } elseif (($pos = ord($char) - ord('A')) < 26) {
        return $pos + 10;
    } else {
        $pos = ord($char) - ord('a');
        return $pos + 10 + 26;
    }
}

// 解く。
// O(B^2 * S * log(S)); B は盤面の x の長さ。S は石の数

function solve($n, $positions)
{
    $whole_min = 99 * 99;
    $whole_max = -1;

    // x 座標に関しては、最小最大を動かしながら全パターンを試す
    // このループが盤面のサイズの二乗のオーダー
    for ($x1 = 0; $x1 < 62; ++$x1) {
        for ($x2 = $x1; $x2 < 62; ++$x2) {

            // x 座標が x1 から x2 の範囲にある石だけ取ってくる
            // (石の x 座標はもう要らないので y 座標だけ取っておく)
            // この処理で石の数のオーダー (この後のソートの方が大きい)
            $ys = filter_by_x($x1, $x2, $positions);

            // 取ってきた石がそもそも n 個未満なら条件を満たさない
            if (count($ys) < $n) {
                continue;
            }

            // 石の y 座標の順にソートする
            // 石の数に対して c * log(c) のオーダー
            sort($ys);
            // echo "$n, $x1, $x2: " . json_encode($ys) . "\n";

            // y 座標については石を n 個含むような窓を動かしていく
            // 石の数のオーダー (手前のソートの方が大きい)
            list ($min, $max) = minmax($n, $ys);
            // echo "min=$min, max=$max\n";

            if ($max == -1) {
                // 無かった
                continue;
            }

            $min = $min * ($x2 - $x1 + 1);
            if ($min < $whole_min) {
                $whole_min = $min;
            }

            $max = $max * ($x2 - $x1 + 1);
            if ($max > $whole_max) {
                $whole_max = $max;
            }
        }
    }

    return array($whole_min, $whole_max);
}

function filter_by_x($x1, $x2, $positions)
{
    $result = array();
    foreach ($positions as list($x, $y)) {
        if ($x1 <= $x && $x <= $x2) {
            $result[] = $y;
        }
    }

    return $result;
}

function minmax($n, $ys)
{
    $min = 99;
    $max = -1;
    $ylen = count($ys);

    for ($i = 0; $i <= $ylen - $n; ++$i) {

        // 一つ前の石が同じ y 座標なら飛ばす (一つ前で調べているはず)
        if ($i > 0 && $ys[$i] == $ys[$i - 1]) {
            continue;
        }

        // n 個目と n+1 個目が同じ y 座標なら、ここから n 個は取れない
        if ($i < $ylen - $n && $ys[$i + $n - 1] == $ys[$i + $n]) {
            continue;
        }

        // n 個取ったときのサイズ。最小を更新したら記録
        $size = $ys[$i + $n - 1] - $ys[$i] + 1;
        if ($size < $min) {
            $min = $size;
        }

        // 一つ前の石の y 座標 + 1 から取る。これが最初の石なら上端から
        if ($i == 0) {
            $y1 = 0;
        } else {
            $y1 = $ys[$i - 1] + 1;
        }

        // n+1 個目の石の y 座標 - 1 まで取る。n 個目が最後なら下端まで
        if ($i == $ylen - $n) {
            $y2 = 61;
        } else {
            $y2 = $ys[$i + $n] - 1;
        }

        // それが n 個含む最大のサイズ。最大を更新したら記録
        $size = $y2 - $y1 + 1;
        if ($size > $max) {
            $max = $size;
        }
    }

    return array($min, $max);
}

//////////

// $problems = file('data/problem.txt');
$problems = file(__DIR__ . '/../data/original/problem.txt');

foreach ($problems as $problem) {
    list ($n, $positions) = read_problem($problem);
    list ($min, $max) = solve($n, $positions);
    if ($max != -1) {
        echo "$min,$max\n";
    } else {
        echo "-\n";
    }
}

// $problem = '3:Oh,Be,AF,in,eG,ir,l5,Q8,mC,7T,Ty,tT';
// list ($n, $positions) = read_problem($problem);
// // echo json_encode($positions) . "\n";

// list ($min, $max) = solve($n, $positions);
// if ($max != -1) {
//     echo "$min,$max\n";
// } else {
//     echo "-\n";
// }
