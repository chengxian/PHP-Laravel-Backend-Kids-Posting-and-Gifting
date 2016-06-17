<?php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kidgifting\ThinTransportVaultClient\TransitClient;

/**
 * @author: chengxian
 * Date: 4/13/16
 * @copyright Cheng Xian Lim
 */
class ThinTransitClientIntegrationTest extends TestCase
{
    // TODO provide setup instructions to run vault

    const VAULTTEST_PREFIX = 'thingtransport_test';
    const VALID_STRING = 'the quick brown fox';
    const VAULT_PREFIX = 'vault:v1:';

    public $VAULT_ADDR;
    public $VAULT_TOKEN;
    public $VAULT_ROOT_TOKEN;

    /**
     * Get env variables. these are set in phpunit.xml or can be overridden on the CLI
     */
    public function setUp()
    {
        parent::setUp();
        $this->VAULT_ADDR = getenv('VAULT_ADDR');
        $this->VAULT_TOKEN = getenv('VAULT_TOKEN');
        $this->VAULT_ROOT_TOKEN = getenv('VAULT_ROOT_TOKEN');
    }

    /**
     * @param bool $root
     * @param null $addr
     * @return TransitClient
     */
    public function getRealVaultClient($root=false, $addr=null)
    {
        if ($root) {
            $token = $this->VAULT_ROOT_TOKEN;
        } else {
            $token = $this->VAULT_TOKEN;
        }

        if ($addr == null)
        {
            $addr = $this->VAULT_ADDR;
        }
        return new TransitClient($addr, $token);
    }

    /**
     * @param $plaintext
     * @param null $client
     * @return mixed
     * @throws \Kidgifting\ThinTransportVaultClient\StringException
     * @throws \Kidgifting\ThinTransportVaultClient\VaultException
     */
    public function getEncryptResponse($plaintext, $client = null)
    {
        if ($client == null) {
            $client = $this->getRealVaultClient();
        }

        $response = $client->encrypt($this::VAULTTEST_PREFIX, $plaintext);

        return $response;
    }

    /**
     * @param $ciphertext
     * @param null $client
     * @return mixed
     * @throws \Kidgifting\ThinTransportVaultClient\StringException
     */
    public function getDecryptResponse($ciphertext, $client = null)
    {
        if ($client == null) {
            $client = $this->getRealVaultClient();
        }

        $response = $client->decrypt($this::VAULTTEST_PREFIX, $ciphertext);

        return $response;
    }

    /**
     * @test
     * @group VaultEndToEnd
     * @group EndToEnd
     * @expectedException \Kidgifting\ThinTransportVaultClient\VaultException
     */
    public function it_handles_bad_url_gracefully()
    {
        // will return a response but empty
        $client = $this->getRealVaultClient(false, 'http://batcave.com');
        $this->getEncryptResponse($this::VALID_STRING, $client);
    }

    /**
     * @test
     * @group VaultEndToEnd
     * @group EndToEnd
     * @expectedException \GuzzleHttp\Exception\ConnectException
     */
    public function it_handles_bad_url_gracefully2()
    {
        // should cause Guzzle exception
        $client = $this->getRealVaultClient(false, 'http://www.timbroder.com:8200');
        $this->getEncryptResponse($this::VALID_STRING, $client);
    }

    /**
     * @test
     * @group VaultEndToEnd
     * @group EndToEnd
     */
    public function it_encrypts_something()
    {
        $response = $this->getEncryptResponse($this::VALID_STRING);
        $this->assertContains($this::VAULT_PREFIX, $response);
    }

    /**
     * @test
     * @group VaultEndToEnd
     * @group EndToEnd
     */
    public function it_decrypts_something()
    {
        $client = $this->getRealVaultClient();
        $ciphertext = $this->getEncryptResponse($this::VALID_STRING, $client);
        unset($client);
        $client = $this->getRealVaultClient();
        $response = $this->getDecryptResponse($ciphertext, $client);
        
        $this->assertContains($this::VALID_STRING, $response);
    }

}