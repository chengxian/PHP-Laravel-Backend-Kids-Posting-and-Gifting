<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Kidgifting\LaraVault\LaraVaultServiceProvidor;
use Venturecraft\Revisionable\RevisionableTrait;

class LaraVaultUnitTest extends LaraVaultBaseTest
{
//    /** @test */
//    public function it_boots()
//    {
//        $model = new \App\TimModel();
//        $dumyModel = Mockery::mock('DummyModel');
//        $dumyModel->shouldReceive('bootLaraVault')->times(1);
//        $dummyModel->expects($this->once())->method('bootLaraVault');


//        dd($dummyObj->getVaultKeyForModel());

//        $this->getMockBuilder('DummyModel')->getMock();

//        $mock = Mockery::mock('DummyModel');
//        $mock->shouldReceive('bootLaraVault')
//            ->once();

//        $this->getMockForTrait('\Kidgifting\LaraVault\LaraVault');

//        $this->getMock('DummyModel');

//    }
//
//    /** @test */
//    public function it_saves_on_save()
//    {
////        $dummyModel = new DummyModel();
////        $this->assertEquals(false, $dummyModel->beforeSaveFired);
////        $dummyModel->save();
////        $this->assertEquals(true, $dummyModel->beforeSaveFired);
//    }

    /** @test */
    public function it_enables()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getObjectForTrait('\Kidgifting\LaraVault\LaraVault');
        $dummyObj->setVaultClient($mockClient, true);
        $this->assertTrue($this->invokeMethod($dummyObj, 'isEnabled'));
    }

    /** @test */
    public function it_disables()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getObjectForTrait('\Kidgifting\LaraVault\LaraVault');
        $dummyObj->setVaultClient($mockClient);
        $this->assertFalse($this->invokeMethod($dummyObj, 'isEnabled'));
    }

    /** @test */
    public function it_needs_a_client()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getObjectForTrait('\Kidgifting\LaraVault\LaraVault');
        $this->assertFalse($this->invokeMethod($dummyObj, 'hasClient'));
    }

    /** @test  */
    public function it_has_a_client_and_is_enabled()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getMockTrait();
        $dummyObj->setVaultClient($mockClient, true);
        $this->assertNull($this->invokeMethod($dummyObj, 'checkVaultClient'));
    }

    /** @test  */
    public function it_has_a_client_and_is_disabled()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getMockTrait();
        $dummyObj->setVaultClient($mockClient);
        $this->assertNull($this->invokeMethod($dummyObj, 'checkVaultClient'));
    }

    /** @test  */
    public function it_should_not_have_encryption_enabled()
    {

        $dummyObj = $this->getMockTrait();
        $this->assertFalse($this->invokeMethod($dummyObj, 'modelHasEncryptionEnabled'));
    }

    /** @test  */
    public function it_should_have_encryption_enabled()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $this->assertTrue($this->invokeMethod($dummyObj, 'modelHasEncryptionEnabled'));
    }

    /** @test */
    public function it_has_bad_setup_and_shouldnt_enable()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = new DummyModelBadSetup();
        $this->assertFalse($this->invokeMethod($dummyObj, 'modelHasEncryptionEnabled'));

    }

    /** @test */
    public function phone_should_be_encryptable()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $this->assertTrue($this->invokeMethod($dummyObj, 'attrIsEncryptable', ['phone']));
    }

    /** @test */
    public function email_should_not_be_encryptable()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $this->assertFalse($this->invokeMethod($dummyObj, 'attrIsEncryptable', ['email']));
    }

    /** @test */
    public function field_is_correctly_encrypted()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $mockClient = $this->getMockTransitClient();
        $cipher = $mockClient->encrypt('key', $this::VALID_PHONE);
        
        $this->assertTrue($this->invokeMethod($dummyObj, 'isEncrypted', [$cipher]));
    }

    /** @test */
    public function field_is_correctly_unencrypted()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);

        $this->assertFalse($this->invokeMethod($dummyObj, 'isEncrypted', [$this::VALID_PHONE]));
    }

    /** @test */
    public function it_should_set_a_table_vault_key()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $dummyObj->phone = $this::VALID_PHONE;

        $this->assertEquals($this->invokeMethod($dummyObj, 'getVaultKeyForModel'), $this::VAULT_MODEL_KEY);

    }

    /** @test */
    public function it_encrypts_an_unencrypted_val()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getUnitTraitWithMockClient($mockClient, true);
        $dummyObj->phone = $this::VALID_PHONE;
        $this->assertEquals($this->invokeMethod($dummyObj, 'encryptAttribute', ['phone']), $this::ENCRYPTED_VALUE);
    }

    /** @test */
    public function it_encrypts_an_unencrypted_val_internally()
    {
        $mockClient = $this->getMockTransitClient();
        $dummyObj = $this->getUnitTraitWithMockClient($mockClient, true);
        $dummyObj->phone = $this::VALID_PHONE;
        $this->invokeMethod($dummyObj, 'encryptAttribute', ['phone']);

        $attrs = $dummyObj->getDummyAttributes();
        $this->assertEquals($attrs['phone'], $this::ENCRYPTED_VALUE);
    }

    /** @test */
    public function it_encrypts_an_unencrypted_val_using_the_client()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->once())
            ->method('encrypt')
            ->with($this::VAULT_MODEL_KEY, $this::VALID_PHONE);

        $dummyObj = $this->getUnitTraitWithMockClient($mockClient, true);
        $dummyObj->phone = $this::VALID_PHONE;
        $this->invokeMethod($dummyObj, 'encryptAttribute', ['phone']);
    }

    /** @test */
    public function it_encrypts()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $dummyObj->phone = $this::VALID_PHONE;

        $this->assertTrue($dummyObj->save());
    }

    /** @test */
    public function it_should_not_decrypt_empty_phone()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $this->assertFalse(
            $this->invokeMethod($dummyObj, 'shouldDecrypt', ['phone']));

    }

    /** @test */
    public function it_should_not_decrypt_phone()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['phone'] = $this::VALID_PHONE;
        $dummyObj->setDummyAttributes($attributes);
        $this->assertFalse(
            $this->invokeMethod($dummyObj, 'shouldDecrypt', ['phone']));

    }

    /** @test */
    public function it_should_decrypt_phone()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['phone'] = $this::ENCRYPTED_VALUE;
        $dummyObj->setDummyAttributes($attributes);
        $this->assertTrue(
            $this->invokeMethod($dummyObj, 'shouldDecrypt', ['phone']));

    }

    /** @test */
    public function it_should_not_decrypt_empty_email()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $this->assertFalse(
            $this->invokeMethod($dummyObj, 'shouldDecrypt', ['email']));

    }

    /** @test */
    public function it_should_not_decrypt_email()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['email'] = $this::VALID_PHONE;
        $dummyObj->setDummyAttributes($attributes);
        $this->assertFalse(
            $this->invokeMethod($dummyObj, 'shouldDecrypt', ['email']));

    }

    /** @test */
    public function it_should_not_decrypt_email2()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['email'] = $this::ENCRYPTED_VALUE;
        $dummyObj->setDummyAttributes($attributes);
        $this->assertFalse(
            $this->invokeMethod($dummyObj, 'shouldDecrypt', ['email']));

    }

    /** @test */
    public function it_decrypts_an_encrypted_val_using_the_client()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->once())
            ->method('decrypt')
            ->with($this::VAULT_MODEL_KEY, $this::ENCRYPTED_VALUE);

        $dummyObj = $this->getUnitTraitWithMockClient($mockClient, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['phone'] = $this::ENCRYPTED_VALUE;
        $dummyObj->setDummyAttributes($attributes);

        $this->invokeMethod($dummyObj, 'decryptAttribute', ['phone', $attributes['phone']]);
    }

    /** @test */
    public function it_shouldnt_decrypt_an_encrypted_val_using_the_client()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->never())
            ->method('decrypt')
            ->with($this::VAULT_MODEL_KEY, $this::ENCRYPTED_VALUE);

        $dummyObj = $this->getUnitTraitWithMockClient($mockClient, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['phone'] = $this::VALID_PHONE;
        $dummyObj->setDummyAttributes($attributes);

        $this->invokeMethod($dummyObj, 'decryptAttribute', ['phone', $attributes['phone']]);
    }

    /** @test */
    public function it_shouldnt_decrypt_an_encrypted_unconfigured_val_using_the_client()
    {
        $mockClient = $this->getMockTransitClient();
        $mockClient->expects($this->never())
            ->method('decrypt')
            ->with($this::VAULT_MODEL_KEY, $this::ENCRYPTED_VALUE);

        $dummyObj = $this->getUnitTraitWithMockClient($mockClient, true);
        $attributs = $dummyObj->getDummyAttributes();
        $attributes['email'] = $this::ENCRYPTED_VALUE;
        $dummyObj->setDummyAttributes($attributes);

        $this->invokeMethod($dummyObj, 'decryptAttribute', ['email', $attributes['email']]);
    }


    /** @test */
    public function it_decrypt_attrs()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $dummyObj->phone = $this::VALID_PHONE;
        $this->invokeMethod($dummyObj, 'encryptAttribute', ['phone']);

        $attributes = $dummyObj->getDummyAttributes();
        $decryptedAttributes = $this->invokeMethod($dummyObj, 'decryptAttributes', [$attributes]);
        $this->assertTrue($this->hasDecryptedPhone($decryptedAttributes));
    }

    /** @test */
    public function it_decrypts_attrs_from_array()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $dummyObj->phone = $this::VALID_PHONE;
        $this->invokeMethod($dummyObj, 'encryptAttribute', ['phone']);

        $attributes = $dummyObj->getDummyAttributes();
        $decryptedAttributes = $dummyObj->getAttributes();
        $this->assertTrue($this->hasDecryptedPhone($decryptedAttributes));
    }

    /** @tests */
    public function it_decrypts_arrayable_attrs()
    {
        $dummyObj = $this->getUnitTraitWithMockClient(null, true);
        $dummyObj->phone = $this::VALID_PHONE;
        $this->invokeMethod($dummyObj, 'encryptAttribute', ['phone']);

        $attributes = $dummyObj->getDummyAttributes();
        $decryptedAttributes = $this->invokeMethod($dummyObj, 'getArrayableAttributes');
        $this->assertTrue($this->hasDecryptedPhone($decryptedAttributes));
    }

}
