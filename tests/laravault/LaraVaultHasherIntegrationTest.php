<?php

use Kidgifting\LaraVault\LaraVaultHash;
use Kidgifting\LaraVault\LaraVaultHasher;

class LaraVaultHasherIntegrationTest extends LaraVaultBaseTest
{

    const EXPECTED_KEY = 'Kidgifting\LaraVault\LaraVaultHash-key';
    const KEY = 'key';
    const VALUE = '123abc';
    const PHONE_KEY = 'phone_hashed';
    const PHONE1 = '1231231234';
    const PHONE2 = '4324324321';

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

        LaraVaultHash::truncate();
    }

    public function tearDown()
    {
        $this->artisan('migrate:reset', [
            '--database' => 'sqlite_test'
        ]);
        parent::tearDown();
    }

    /** @test */
    public function it_saves_hashrecord()
    {
        $hasher = $this->getRealHasherWithMockCLient();
        
        $model = new LaraVaultHash();
        $model->setVaultClient($this->getMockTransitClient());
        
        $salt = $hasher->hash($model, self::KEY, self::VALUE);

        $this->seeInDatabase('laravault_hash', ['key' => self::EXPECTED_KEY]);
    }

    /** @test */
    public function it_hashes_the_same_twice()
    {
        $hasher = $this->getRealHasherWithMockCLient();

        $model1 = new LaraVaultHash();
        $model1->setVaultClient($this->getMockTransitClient());

        $salt1 = $hasher->hash($model1, self::KEY, self::VALUE);

        $model2 = new LaraVaultHash();
        $model2->setVaultClient($this->getMockTransitClient());

        $salt2 = $hasher->hash($model2, self::KEY, self::VALUE);

        $this->assertEquals($salt1, $salt2);
    }

    /** @test */
    public function it_hashes_differently_twice()
    {
        $hasher = $this->getRealHasherWithMockCLient();

        $model1 = new LaraVaultHash();
        $model1->setVaultClient($this->getMockTransitClient());

        $salt1 = $hasher->hash($model1, self::KEY, self::VALUE);

        $model2 = new LaraVaultHash();
        $model2->setVaultClient($this->getMockTransitClient());

        $salt2 = $hasher->hash($model2, self::KEY, 'ada');

        $this->assertNotEquals($salt1, $salt2);
    }

    /** @test */
    public function it_keeps_1_record_per_key()
    {
        $hasher = $this->getRealHasherWithMockCLient();

        $model1 = new LaraVaultHash();
        $model1->setVaultClient($this->getMockTransitClient());

        $salt1 = $hasher->hash($model1, self::KEY, self::VALUE);

        $model2 = new LaraVaultHash();
        $model2->setVaultClient($this->getMockTransitClient());

        $salt2 = $hasher->hash($model2, self::KEY, self::VALUE);

        $this->assertEquals(1, LaraVaultHash::all()->count());
    }

    public function setup_3_saves()
    {
        $hasher = $this->getRealHasherWithMockCLient();



        $dummy1 = new DummyModelIntegrating();
        $dummy1->setVaultClient($this->getMockTransitClient());
//        $dummy1->phone = self::PHONE1;
        $hashed1 = $hasher->hash($dummy1, self::PHONE_KEY, self::PHONE1);
        $dummy1->phone_hashed = $hashed1;
        $dummy1->save();


        $dummy2 = new DummyModelIntegrating();
        $dummy2->setVaultClient($this->getMockTransitClient());
//        $dummy2->phone = self::PHONE1;
        $hashed2 = $hasher->hash($dummy2, self::PHONE_KEY, self::PHONE1);
        $dummy2->phone_hashed = $hashed2;
        $dummy2->save();


        $dummy3 = new DummyModelIntegrating();
        $dummy3->setVaultClient($this->getMockTransitClient());
//        $dummy3->phone = self::PHONE2;
        $hashed3 = $hasher->hash($dummy3, self::PHONE_KEY, self::PHONE2);
        $dummy3->phone_hashed = $hashed3;
        $dummy3->save();

    }

    /** @test */
    public function hash_can_be_used_for_lookup()
    {
        $hasher = $this->getRealHasherWithMockCLient();

        $this->setup_3_saves();
        $dummyModel = new DummyModelIntegrating();
        $lookup = $hasher->hash($dummyModel, self::PHONE_KEY, self::PHONE1);

        $count = DummyModelEncrypting::where(self::PHONE_KEY, $lookup)->count();
        $this->assertEquals(2, $count);
    }
}
