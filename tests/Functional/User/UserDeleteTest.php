<?php


namespace App\Tests\Functional\User;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserDeleteTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    const URL_DELETE = "/api/users/";

    public function testDeleteUserWrongRole()
    {
        $client = static::createClient();
        $user = $this->loginUser($client);

        $this->sendRequestJson($client, "DELETE", self::URL_DELETE . $user->getId(), null, 403);
    }

    public function testDeleteUserMe()
    {
        $client = static::createClient();
        $user = $this->loginUserAdmin($client);

        $this->sendRequestJson($client, "DELETE", self::URL_DELETE . $user->getId(), null, 400);
    }

    public function testDeleteUserSomebody()
    {
        $client = static::createClient();
        $toDelete = $this->createUser('toDelete');
        $this->loginUserAdmin($client);

        $this->sendRequestJson($client, "DELETE", self::URL_DELETE . $toDelete->getId(), null, 200);
    }

    public function testDeleteUserGroupDevIn()
    {
        $client = static::createClient();
        $toDelete = $this->createUser('toDelete');
        $toDelete2 = $this->createUser('toDelete2');
        $this->setToDev($toDelete);
        $this->loginUserAdmin($client);

        $json = '['. $toDelete->getId() .', '. $toDelete2->getId() .']';

        $this->sendRequestJson($client, "DELETE", self::URL_DELETE, $json, 403);
    }

    public function testDeleteUserGroupMeIn()
    {
        $client = static::createClient();
        $toDelete = $this->createUser('toDelete');
        $toDelete2 = $this->createUser('toDelete2');
        $user = $this->loginUserAdmin($client);

        $json = '['. $toDelete->getId() .', '. $toDelete2->getId() .', '. $user->getId() .']';

        $this->sendRequestJson($client, "DELETE", self::URL_DELETE, $json, 400);
    }

    public function testDeleteUserGroupValid()
    {
        $client = static::createClient();
        $toDelete = $this->createUser('toDelete');
        $toDelete2 = $this->createUser('toDelete2');
        $this->loginUserAdmin($client);

        $json = '['. $toDelete->getId() .', '. $toDelete2->getId() .']';

        $this->sendRequestJson($client, "DELETE", self::URL_DELETE, $json, 200);
    }
}