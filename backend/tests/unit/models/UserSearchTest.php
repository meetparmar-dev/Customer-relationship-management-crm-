<?php

namespace backend\tests\unit\models;

use backend\models\UserSearch;
use common\models\User;
use Codeception\Test\Unit;

class UserSearchTest extends Unit
{
    protected function _before()
    {
        User::deleteAll();

        $this->createUser('John', 'Doe', 'john1', 'john@test.com', 10, User::STATUS_ACTIVE, User::ROLE_ADMIN);
        $this->createUser('Jane', 'Smith', 'jane1', 'jane@test.com', 20, User::STATUS_ACTIVE, User::ROLE_USER);
        $this->createUser('Mike', 'Brown', 'mike1', 'mike@test.com', 30, User::STATUS_INACTIVE, User::ROLE_USER);
    }

    private function createUser($first, $last, $username, $email, $daysAgo, $status, $role)
    {
        $user = new User();
        $user->first_name = $first;
        $user->last_name = $last;
        $user->username = $username;
        $user->email = $email;
        $user->status = $status;
        $user->role = $role;
        $user->created_at = time() - (86400 * $daysAgo);
        $user->setPassword('password123');
        $user->generateAuthKey();
        $user->save(false);
    }

    /** ✅ Empty search returns all */
    public function testSearchWithoutFiltersReturnsAll()
    {
        $search = new UserSearch();
        $provider = $search->search([]);

        $this->assertEquals(3, $provider->getTotalCount());
    }

    /** ✅ Search by full name */
    public function testSearchByFullName()
    {
        $search = new UserSearch();
        $provider = $search->search(['UserSearch' => ['full_name' => 'John Doe']]);

        $models = $provider->getModels();

        $this->assertCount(1, $models);
        $this->assertEquals('john1', $models[0]->username);
    }

    /** ✅ Search by username */
    public function testSearchByUsername()
    {
        $search = new UserSearch();
        $provider = $search->search(['UserSearch' => ['username' => 'jane']]);

        $this->assertEquals(1, $provider->getTotalCount());
    }

    /** ✅ Search by email */
    public function testSearchByEmail()
    {
        $search = new UserSearch();
        $provider = $search->search(['UserSearch' => ['email' => 'mike@test.com']]);

        $this->assertEquals(1, $provider->getTotalCount());
    }

    /** ✅ Filter by status */
    public function testSearchByStatus()
    {
        $search = new UserSearch();
        $provider = $search->search(['UserSearch' => ['status' => User::STATUS_ACTIVE]]);

        $this->assertEquals(2, $provider->getTotalCount());
    }

    /** ✅ Filter by role */
    public function testSearchByRole()
    {
        $search = new UserSearch();
        $provider = $search->search(['UserSearch' => ['role' => User::ROLE_ADMIN]]);

        $this->assertEquals(1, $provider->getTotalCount());
    }
}
