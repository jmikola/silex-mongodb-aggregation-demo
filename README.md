# MongoDB aggregation demo (using Silex)

This is a port of [Ross Lawley][1]'s [candlestick demo][2] ported to PHP 5.4 for
presentation at the [MidwestPHP conference][3].

## Setup

### Install Dependencies

    $ composer.phar install

### Load Data Fixtures

Load the provide sample data into the `demo.money` collection:

    $ mongorestore -d demo -c money --drop ./money.bson

### Launch Demo

    $ php -S localhost:8080 -t web web/index.php

Alternative web server configurations for Silex may be found [here][4].

  [1]: https://github.com/rozza
  [2]: https://github.com/rozza/demos
  [3]: http://midwestphp.org/
  [4]: http://silex.sensiolabs.org/doc/web_servers.html
