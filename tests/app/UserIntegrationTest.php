<?php
use App\User;
use Kidgifting\FrozenSettings\FrozenSetting;

/**
 * @author: chengxian
 * Date: 4/16/16
 * @copyright Cheng Xian Lim
 */
class UserIntegrationTest extends AppBaseTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        exec('php artisan migrate --database sqlite_test');
    }

    public static function tearDownAfterClass()
    {
        exec('php artisan migrate:reset --database sqlite_test');
        parent::tearDownAfterClass();
    }
    
    public function setUp()
    {
        parent::setUp();

        User::truncate();
    }

    /**
     *
     */
    public function getRealUser(){
        $u = new User();
        $u->first_name = 'Tim';
        $u->last_name = 'Broder';
        $u->email = 'timothy.broder@gmail.com';

        return $u;
    }

    /** @test */
    public function it_is_allowed()
    {
        $u = $this->getRealUser();
        $this->assertTrue($u->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_disabled()
    {
        $u = $this->getRealUser();
        $u->is_enabled = false;
        $this->assertFalse($u->isAllowed());
    }

    /** @test */
    public function it_is_not_allowed_because_deleted()
    {
        $u = $this->getRealUser();
        $u->save();
        $u->delete();
        $this->assertFalse($u->isAllowed());
        $this->seeInDatabase('users', ['email' => 'timothy.broder@gmail.com']);
    }
}