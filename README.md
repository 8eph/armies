Two randomly generated armies duke it out.

Configuration available in "src/Configuration/config.yml"

[Usage with Docker]

composer install
docker build -t armies .
// army1 size, army2 size, number of rounds
docker run armies 10 10 --rounds 10

[Usage with local php7.2]

composer install
bin/console armies:battle 10 10 --rounds 10
