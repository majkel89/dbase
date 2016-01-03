#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd "$DIR/.."

./vendor/bin/athletic -p benchmarks/ -b vendor/autoload.php
