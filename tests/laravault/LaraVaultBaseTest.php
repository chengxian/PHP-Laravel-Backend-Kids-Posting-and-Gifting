<?php

use Kidgifting\LaraVault\LaraVaultHasher;
use Kidgifting\LaraVault\LaraVaultServiceProvidor;

class LaraVaultBaseTest extends TestCase
{
    const ENCRYPTED_VALUE = "vault:v1:UEhQVW5pdF9GcmFtZXdvcmtfTW9ja09iamVjdF9Nb2NrT2JqZWN0";
    const VAULT_MODEL_KEY = "laravault-dummy";
    const VALID_PHONE = '1231231234';

    public function getMockTransitClient()
    {
        $vaultAddr = config('vault.addr');
        $vaultToken = config('vault.token');
        $mock = $this->getMockBuilder('Kidgifting\ThinTransportVaultClient\TransitClient')
            ->setConstructorArgs([$vaultAddr, $vaultToken])
            ->getMock();

        $mock->method('encrypt')->willReturn($this::ENCRYPTED_VALUE);
        $mock->method('decrypt')->willReturn($this::VALID_PHONE);

        return $mock;
    }

    public function getMockTrait()
    {
        return $this->getObjectForTrait('\Kidgifting\LaraVault\LaraVault');
    }

    public function getUnitTrait($encryptable=false)
    {
        if (!$encryptable) {
            return new DummyModel();
        }

        return new DummyModelEncrypting();
    }

    public function getUnitTraitWithMockClient(PHPUnit_Framework_MockObject_MockObject $mockClient = null, $encryptable=false)
    {
        if ($mockClient === null)
        {
            $mockClient = $this->getMockTransitClient();
        }

        $model = $this->getUnitTrait($encryptable);
        $model->setVaultClient($mockClient, true);

        return $model;
    }

    public function getIntegrationTrait()
    {
        return new DummyModelIntegrating();
    }

    public function getIntegrationTraitWithMockClient(PHPUnit_Framework_MockObject_MockObject $mockClient = null)
    {
        if ($mockClient === null)
        {
            $mockClient = $this->getMockTransitClient();
        }

        $model = $this->getIntegrationTrait();
        $model->name = "Testy McTesterstine";

        $model->setVaultClient($mockClient, true);

        return $model;
    }

    public function getUnitTraitWithRealClient($encryptable=false)
    {
        $realTransitClient = LaraVaultServiceProvidor::getTransitClient();

        $model = $this->getUnitTrait($encryptable);
        $model->setVaultClient($realTransitClient, true);

        return $model;
    }

    public function hasDecryptedPhone($attributes)
    {
        if (!array_key_exists('phone', $attributes)) {
            return false;
        }

        return $attributes['phone'] == $this::VALID_PHONE;
    }

    /**
     * @return LaraVaultHasher
     */
    public function getRealHasherWithRealClient()
    {
        $realTransitClient = LaraVaultServiceProvidor::getTransitClient();
        $dummy = new LaraVaultHasher($realTransitClient);
        return $dummy;
    }

    public function getRealHasherWithMockCLient()
    {
        $mockClient = $this->getMockTransitClient();
        $dummy = new LaraVaultHasher($mockClient);
        return $dummy;
    }

    public function getMockHasherWithRealClient()
    {
        $realTransitClient = LaraVaultServiceProvidor::getTransitClient();
        $mock = $this->getMockBuilder('Kidgifting\LaraVault\LaraVaultHasher')
            ->setConstructorArgs([$realTransitClient])
            ->getMock();

        return $mock;
    }

    public function getMockHasherWithMockClient(Array $methods = null)
    {
        $mockClient = $this->getMockTransitClient();
        $mockBuilder = $this->getMockBuilder('Kidgifting\LaraVault\LaraVaultHasher', ['keyExists'])
            ->setConstructorArgs([$mockClient]);

        if ($methods != null && count($methods) > 0) {
            $mockBuilder->setMethods($methods);
        }

//        $mock = $this->getMock('Kidgifting\LaraVault\LaraVaultHasher', ['keyExists']);
//
        return $mockBuilder->getMock();
    }

    /** @test */
    public function this_supresses_a_no_test_warning() {}

}
