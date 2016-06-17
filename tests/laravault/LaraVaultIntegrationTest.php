<?php

class LaraVaultIntegrationTest extends LaraVaultBaseTest
{

    protected function getDummyMigrationsDir()
    {
        $path = "tests/laravault/migrations";

        return $path;
    }

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

    /** @test */
    public function it_saves()
    {
        $dummyObj = $this->getIntegrationTraitWithMockClient(null);
        $dummyObj->save();

        $this->seeInDatabase('dummy', ['name' => 'Testy McTesterstine']);
    }

    /** @test */
    public function client_encrypts_once_for_single_attr()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->once())
            ->method('encrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $this->seeInDatabase('dummy', ['phone' => $this::ENCRYPTED_VALUE]);
    }

    /** @test */
    public function client_encrypts_more_than_once_for_multiple_attr()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->exactly(2))
            ->method('encrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->cell = $this::VALID_PHONE;
        $dummyObj->save();

        $this->seeInDatabase('dummy', ['phone' => $this::ENCRYPTED_VALUE]);
        $this->seeInDatabase('dummy', ['cell' => $this::ENCRYPTED_VALUE]);

    }

    /** @test */
    public function it_encrypts_once_for_single_attr()
    {
        $dummyObj = $this->getIntegrationTraitWithMockClient(null);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        // saving and creating events
        $this->assertEquals(2, $dummyObj->encryptedCount);

    }

    /** @test */
    public function it_encrypts_once_for_multiple_attr()
    {
        $dummyObj = $this->getIntegrationTraitWithMockClient(null);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->cell = $this::VALID_PHONE;
        $dummyObj->save();

        // saving and creating events
        $this->assertEquals(2, $dummyObj->encryptedCount);
    }

    /** @test */
    public function client_encrypts_more_than_once_for_attr_change()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->exactly(2))
            ->method('encrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();
    }

    /** @test */
    public function it_encrypts_more_than_once_for_attr_change()
    {
        $dummyObj = $this->getIntegrationTraitWithMockClient(null);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        // saving and updating events
        $this->assertEquals(3, $dummyObj->encryptedCount);
    }

    /** @test */
    public function it_should_decrypt_once_on_attr_get()
    {
        $decryptCount = 1;
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->exactly($decryptCount))
            ->method('decrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $get = $dummyObj->phone;
        $this->assertEquals($decryptCount, $dummyObj->decryptedCount);
    }

    /** @test */
    public function it_should_decrypt_never_on_collection_where()
    {
        $decryptCount = 0;
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->exactly($decryptCount))
            ->method('decrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $dummy = DummyModelIntegrating::whereName('Testy McTesterstine')->firstOrFail();
        $dummy->setVaultClient($mockClient, true);

        $this->assertEquals($decryptCount, $dummy->decryptedCount);
    }

    /** @test */
    public function it_should_decrypt_never_on_collection_loop()
    {
        $decryptCount = 0;
        $decryptedCount = 0;
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->exactly($decryptCount))
            ->method('decrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $dummys = DummyModelIntegrating::whereName('Testy McTesterstine');

        $this->assertGreaterThanOrEqual(2, $dummys->count());

        foreach ($dummys as $dummy) {
            $dummy->setVaultClient($mockClient, true);
            $id = $dummy->id;
            $decryptedCount += $dummy->decryptedCount;
        }

        $this->assertEquals($decryptCount, $decryptedCount);
    }

    /** @test */
    public function it_should_decrypt_multiple_on_collection_loop()
    {
        $decryptCount = 2;
        $decryptedCount = 0;

        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->exactly($decryptCount))
            ->method('decrypt');

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();
        $this->seeInDatabase('dummy', ['phone' => $this::ENCRYPTED_VALUE]);

        $dummyObj = $this->getIntegrationTraitWithMockClient($mockClient);
        $dummyObj->phone = $this::VALID_PHONE;
        $dummyObj->save();

        $dummys = DummyModelIntegrating::whereName('Testy McTesterstine');
        $this->assertGreaterThanOrEqual(2, $dummys->count());

        foreach ($dummys->get() as $dummy) {
            $dummy->setVaultClient($mockClient, true);
            $phone = $dummy->phone;
            $this->assertEquals($this::VALID_PHONE, $phone);
            $decryptedCount += $dummy->decryptedCount;
        }

        $this->assertEquals($decryptCount, $decryptedCount);
    }
}
