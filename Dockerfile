FROM php:7.2-cli
COPY . /usr/src/app
WORKDIR /usr/src/app
COPY entrypoint.sh /
ENTRYPOINT ["/entrypoint.sh"]
