#!/bin/sh
set -e # all executed commands are printed to the terminal

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
        set -- apache2-foreground "$@"
fi

exec "$@"


