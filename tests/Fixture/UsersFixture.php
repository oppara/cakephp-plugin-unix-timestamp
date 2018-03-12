<?php
namespace Oppara\UnixTimestamp\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture
{

    public $fields = [
        'id' => ['type' => 'integer'],
        'username' => ['type' => 'string', 'null' => true],
        'password' => ['type' => 'string', 'null' => true],
        'created' => ['type' => 'integer', 'null' => true],
        'updated' => ['type' => 'integer', 'null' => true],
        '_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
    ];

}


