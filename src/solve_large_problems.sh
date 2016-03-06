#!/bin/bash

BASEDIR=$(cd $(dirname $0)/.. && pwd)
DATADIR=$BASEDIR/data/large
SOURCEDIR=$BASEDIR/src
ANSWERDIR=$BASEDIR/answer/large

for problem in $DATADIR/problems_*.txt; do
    base=$(basename $problem .txt)
    size=$(echo ${base##*_} | sed 's/^0*//')
    echo $base
    answer=$ANSWERDIR/$(echo $base | sed 's/problems/answer/').txt
    time php $SOURCEDIR/solve_large.php $size <$problem >$answer
done
