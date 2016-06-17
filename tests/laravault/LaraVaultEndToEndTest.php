<?php

/**
 * @author: chengxian
 * Date: 4/15/16
 * @copyright Cheng Xian Lim
 */
class LaraVaultEndToEndTest extends ThinTransitClientIntegrationTest
{
    // TODO provide setup instructions to run vault

    const ENCRYPTED_VALUE = "vault:v1:UEhQVW5pdF9GcmFtZXdvcmtfTW9ja09iamVjdF9Nb2NrT2JqZWN0";
    const VAULT_MODEL_KEY = "laravault-dummy";
    const VALID_PHONE = '1231231234';

    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:reset', [
            '--database' => 'sqlite_test'
        ]);
        $this->artisan('migrate', [
            '--database' => 'sqlite_test',
            '--path' => $this->getDummyMigrationsDir()
        ]);
    }

    public function tearDown()
    {
        $this->artisan('migrate:reset', [
            '--database' => 'sqlite_test'
        ]);
        parent::tearDown();
    }

    public function getRealDummyObject()
    {
        $model = new DummyModelIntegrating();
        $client = $this->getRealVaultClient();
        $model->setVaultClient($client, true);

        return $model;
    }

    /**
     * @test
     * @group VaultEndToEnd
     * @group EndToEnd
     */
    public function it_really_encrypts()
    {
        $dummy = $this->getRealDummyObject();
        $dummy->phone = $this::VALID_PHONE;
        $dummy->save();

        $this->seeLikeInDatabase('dummy','phone', 'LIKE', "%" . $this::VAULT_PREFIX . "%");
    }

    /**
     * @test
     * @group VaultEndToEnd
     * @group EndToEnd
     */
    public function it_really_decrypts()
    {
        $dummy = $this->getRealDummyObject();
        $dummy->phone = $this::VALID_PHONE;
        $dummy->save();


        $real = DummyModelIntegrating::all()->first();

        $this->assertNotNull($real->id);
        $this->assertEquals($this::VALID_PHONE, $real->phone);


    }


}
