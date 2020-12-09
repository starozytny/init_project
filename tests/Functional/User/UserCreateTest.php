<?php


namespace App\Tests\Functional\User;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserCreateTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUserAdminRole()
    {
        $client = static::createClient();
        $this->loginUserAdmin($client);

        $this->createUserApi($client, [
            'username' => 'cheeseplease',
            'email' => 'cheseplease@outlook.fr',
            'password' => 'brie'
        ], 201);
    }

    public function testCreateUserWrongRole()
    {
        $client = static::createClient();
        $this->loginUser($client);

        $this->createUserApi($client, [
            'username' => 'cheeseplease',
            'email' => 'cheseplease@outlook.fr',
            'password' => 'brie'
        ], 403);
    }

    public function testCreateUserWrongEmail()
    {
        $client = static::createClient();
        $this->loginUserAdmin($client);

        $this->createUserApi($client, [
            'username' => 'cheeseplease',
            'email' => 'cheseplease',
            'password' => 'brie'
        ], 400);
    }

    public function testCreateUserNoWriteableProperty()
    {
        $client = static::createClient();
        $this->loginUserAdmin($client);

        $this->createUserApi($client, [ 'roles' => ['ROLE_USER'] ], 400);
    }

    public function testCreateUserEmpty()
    {
        $client = static::createClient();
        $this->loginUserAdmin($client);

        $this->createUserApi($client, [], 400);
    }

    public function testCreateUserNoUsername()
    {
        $client = static::createClient();
        $this->loginUserAdmin($client);

        $this->createUserApi($client, [ 'password' => 'azerty' ], 400);
    }

    public function testCreateUserNoPassword()
    {
        $client = static::createClient();
        $this->loginUserAdmin($client);

        $this->createUserApi($client, [ 'username' => 'cheesepleaseNoPassword' ], 400);
    }

    protected function createUserApi($client, Array $json, $codeReturn)
    {
        $client->request('POST', '/api/users', [ 'json' => $json ]);
        $this->assertResponseStatusCodeSame($codeReturn);
    }
}