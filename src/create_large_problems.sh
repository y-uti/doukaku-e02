#!/bin/bash

BASEDIR=$(cd $(dirname $0)/.. && pwd)
DATADIR=$BASEDIR/data/large
SOURCEDIR=$BASEDIR/src

NROWS=${1:-10}

function build_problems() {
    n=$1
    c=$2
    s=$3
    for i in $(seq 1 $NROWS); do
        php $SOURCEDIR/create_problem.php $n $c $s
    done >$DATADIR/problems_$(printf '%04d' $n)_$(printf '%04d' $c)_$(printf '%05d' $s).txt
}

for n in $(seq 10 10 100); do
    build_problems $n 100 1000
done

for c in $(seq 50 50 500); do
    build_problems 10 $c 1000
    build_problems $(($c / 10)) $c 1000
done

for s in $(seq 500 500 4000); do
    build_problems 10 100 $s
done
