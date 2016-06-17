<?php

use Kidgifting\LaraVault\LaraVaultHash;

class LaraVaultHasherUnitTest extends LaraVaultBaseTest
{

    const EXPECTED_KEY = 'Kidgifting\LaraVault\LaraVaultHash-key';

    /**
     * @test
     * @expectedException Exception
     * @expectedExceptionMessage field must be a string
     */
    public function it_only_accepts_string_for_field()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $dummy->hash($model, 123, '1231231234');
    }

    // FIXME Why did I remove this check from the app?
//    /**
//     * @test
//     * @expectedException Exception
//     * @expectedExceptionMessage value must be a string
//     */
//    public function it_only_accepts_string_for_value()
//    {
//        $dummy = $this->getRealHasherWithMockCLient();
//        $model = new LaraVaultHash();
//        $dummy->hash($model, 'phone', 123123);
//    }

    /** @test */
    public function it_makes_a_key()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $expectedKey = self::EXPECTED_KEY;
        $key = $this->invokeMethod($dummy, 'generateFieldKey', [
            $model,
            'key'
        ]);

        $this->assertEquals($expectedKey, $key);
    }

    /** @test */
    public function it_checks_to_see_if_key_already_exists()
    {
        $dummy = $this->getMockHasherWithMockClient(['getRecord']);

        $dummy->expects($this->once())
            ->method('getRecord')
            ->with(self::EXPECTED_KEY)
            ->willReturn(new LaraVaultHash());

        $model = new LaraVaultHash();

        $dummy->hash($model, 'key', '1231231234');
    }

    /** @test */
    public function it_generates_a_portion_of_salt()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $rand = $this->invokeMethod($dummy, 'getRandForSalt');

        $this->assertNotNull($rand);
    }

    /** @test */
    public function it_generates_a_random_portion_of_salt()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $rands = [];

        for ($i = 0; $i < 10000; $i++) {
            $rand = $this->invokeMethod($dummy, 'getRandForSalt');
            $rands [] = $rand;
        }

        $checkRands = array_unique($rands);
        $this->assertEquals(count($rands), count($checkRands));
    }

    /** @test */
    public function it_generates_a_portion_of_salt_with_len_22()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $model->setVaultClient($this->getMockTransitClient());
        
        $rand = $this->invokeMethod($dummy, 'getRandForSalt');
        $this->assertEquals(22, strlen($rand));
    }

    /** @test */
    public function it_generates_a_salt()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $salt = $this->invokeMethod($dummy, 'makeSalt');

        $this->assertNotNull($salt);
    }

    /** @test */
    public function it_generates_a_salt_with_appropriate_header()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $salt = $this->invokeMethod($dummy, 'makeSalt');

        $this->assertEquals(0, strpos($salt, "$2y"));
    }

    /** @test */
    public function it_generates_a_salt_with_appropriate_cost()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $salt = $this->invokeMethod($dummy, 'makeSalt');

        $this->assertEquals(3, strpos($salt, "$12$"));
    }

    /** @test */
    public function it_generates_a_random_salt()
    {
        $dummy = $this->getRealHasherWithMockCLient();
        $model = new LaraVaultHash();
        $rands = [];

        for ($i = 0; $i < 10000; $i++) {
            $rand = $this->invokeMethod($dummy, 'makeSalt');
            $rands [] = $rand;
        }

        $checkRands = array_unique($rands);
        $this->assertEquals(count($rands), count($checkRands));
    }
}
