# UnixTimestamp plugin for CakePHP

[![Build Status](https://travis-ci.org/oppara/cakephp-plugin-unix-timestamp.svg?branch=master)](https://travis-ci.org/oppara/cakephp-plugin-unix-timestamp)
[![codecov](https://codecov.io/gh/oppara/cakephp-plugin-unix-timestamp/branch/master/graph/badge.svg)](https://codecov.io/gh/oppara/cakephp-plugin-unix-timestamp)

You can use this UnixTimestampBehavior insted of CakePHP's TimestampBehavior when want to save the unix timestamp

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require oppara/cakephp-plugin-unix-timestamp
```

## Enable plugin

You need to enable the plugin your config/bootstrap.php file:

```php
<?php
Plugin::load('Oppara/UnixTimestamp');
```

If you are already using `Plugin::loadAll();`, then this is not necessary.

## Usage

more info https://book.cakephp.org/3.0/en/orm/behaviors/timestamp.html

```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created INT,
    modified INT
);
```

```php
<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class UsersTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Oppara/UnixTimestamp.UnixTimestamp');
    }
}
```

