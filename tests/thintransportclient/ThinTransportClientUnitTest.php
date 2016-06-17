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
class ThinTransitClientUnitTest extends TestCase
{
    const ENCRYPTED_VALUE = "vault:v1:UEhQVW5pdF9GcmFtZXdvcmtfTW9ja09iamVjdF9Nb2NrT2JqZWN0";
    const VALID_STRING = 'the quick brown fox';
    const ENCODED_VALID_STRING = 'dGhlIHF1aWNrIGJyb3duIGZveA==';
    const VAULTTEST_PREFIX = 'thingtransport_test';
    const VAULT_PREFIX = 'vault:v1:';
    const VAULT_CONTEXT = 'test';
    const ENCODED_VAULT_CONTEXT = 'dGVzdA==';
    const ENCODED__ENCRYPTED_VALUE = 'dmF1bHQ6djE6VUVoUVZXNXBkRjlHY21GdFpYZHZjbXRmVFc5amEwOWlhbVZqZEY5TmIyTnJUMkpxWldOMA==';
    const ENCRYPTED_RETURN = "{
      \"data\": {
        \"ciphertext\": \"vault:v1:UEhQVW5pdF9GcmFtZXdvcmtfTW9ja09iamVjdF9Nb2NrT2JqZWN0\"
      }
    }";
    const DECRYPTED_RETURN = "{
      \"data\": {
        \"plaintext\": \"dGhlIHF1aWNrIGJyb3duIGZveA==\"
      }
    }";

    public function getGuzzleClient()
    {
        $serverUrl = config('vault.addr');

        return new Client([
            'base_uri' => $serverUrl,
            'timeout' => 2.0
        ]);
    }
    
    public function getMockGuzzleClient()
    {
        $serverUrl = config('vault.addr');
        
        $mock = $this->getMockBuilder('GuzzleHttp\Client')
            ->setConstructorArgs([[
                'base_uri' => $serverUrl,
                'timeout' => 2.0
            ]])
            ->getMock();

        return $mock;
    }

    public function getRealClient($guzzleClient=null)
    {
        $serverUrl = config('vault.addr');
        $token = config('vault.token');

        if (!$guzzleClient) {
            $guzzleClient = $this->getGuzzleClient();
        }

        $client = new TransitClient($serverUrl, $token, $guzzleClient);

        return $client;
    }
    
    public function testItEncodesProperlyToBase64()
    {
        $client = $this->getRealClient();
        $this->assertEquals($this::ENCODED_VALID_STRING, $this->invokeMethod($client, 'encode', [$this::VALID_STRING]));
    }

    public function testItDecodesProperlyFromBase64()
    {
        $client = $this->getRealClient();
        $this->assertEquals($this::VALID_STRING, $this->invokeMethod($client, 'decode', [$this::ENCODED_VALID_STRING]));
    }

    /** @test */
    public function it_creates_a_contextless_encrypt_payload()
    {
        $expected = [
            'plaintext' => $this::ENCODED_VALID_STRING
        ];
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'getEncryptPayload', [$this::VAULTTEST_PREFIX,$this::VALID_STRING]);

        $this->assertEquals($expected, $data);
    }

    /** @test */
    public function it_creates_a_contextfull_encrypt_payload()
    {
        $expected = [
            'plaintext' => $this::ENCODED_VALID_STRING,
            'context' => $this::ENCODED_VAULT_CONTEXT
        ];
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'getEncryptPayload', [$this::VAULTTEST_PREFIX, $this::VALID_STRING, $this::VAULT_CONTEXT]);

        $this->assertEquals($expected, $data);
    }

    /**
     * @test
     * @expectedException     Kidgifting\ThinTransportVaultClient\StringException
     */
    public function encrypt_key_must_be_string()
    {
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'encrypt', [[$this::VAULTTEST_PREFIX], $this::VALID_STRING, $this::VAULT_CONTEXT]);
    }

    /**
     * @test
     * @expectedException     Kidgifting\ThinTransportVaultClient\StringException
     */
    public function encrypt_key_must_be_string2()
    {
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'encrypt', [123, $this::VALID_STRING, $this::VAULT_CONTEXT]);
    }

    /**
     * @test
     * @expectedException     Kidgifting\ThinTransportVaultClient\StringException
     */
    public function encrypt_plaintext_must_be_string()
    {
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'encrypt', [$this::VAULTTEST_PREFIX, [$this::VALID_STRING], $this::VAULT_CONTEXT]);
    }

    /**
     * @test
     * @expectedException     Kidgifting\ThinTransportVaultClient\StringException
     */
    public function encrypt_plaintext_must_be_string2()
    {
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'encrypt', [$this::VAULTTEST_PREFIX, 1234, $this::VAULT_CONTEXT]);
    }

    /**
     * @test
     * @expectedException     Kidgifting\ThinTransportVaultClient\StringException
     */
    public function encrypt_context_must_be_string()
    {
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'encrypt', [$this::VAULTTEST_PREFIX, $this::VALID_STRING, [$this::VAULT_CONTEXT]]);
    }

    /**
     * @test
     * @expectedException     Kidgifting\ThinTransportVaultClient\StringException
     */
    public function encrypt_context_must_be_string2()
    {
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'encrypt', [$this::VAULTTEST_PREFIX, $this::VALID_STRING, 1234]);
    }
    
    /** @test */
    public function it_sends_encrypt_command()
    {
        $mockResponse = new MockHandler([
            new Response(200,
                $headers = [],
                $body = $this::ENCRYPTED_RETURN)
        ]);
        $mockHandler = HandlerStack::create($mockResponse);
        $mock = new Client(['handler' => $mockHandler]);

        $expected = [
            'data' => [
                "ciphertext" => "vault:v1:UEhQVW5pdF9GcmFtZXdvcmtfTW9ja09iamVjdF9Nb2NrT2JqZWN0"
            ]
        ];

        $client = $this->getRealClient($mock);
        $response = $this->invokeMethod($client, 'command', [$this::VAULTTEST_PREFIX,$this::VALID_STRING]);
        $this->assertEquals($expected, $response);
    }

    public function it_encrypts()
    {
        $mockResponse = new MockHandler([
            new Response(200,
                $headers = [],
                $body = $this::ENCRYPTED_RETURN)
        ]);
        $mockHandler = HandlerStack::create($mockResponse);
        $mock = new Client(['handler' => $mockHandler]);

        $client = $this->getRealClient($mock);
        $response = $this->invokeMethod($client, 'encrypt', [$this::VAULTTEST_PREFIX,$this::VALID_STRING]);
        $this->assertEquals($this::ENCRYPTED_VALUE, $response);
    }

    /** @test */
    public function it_creates_a_contextless_decrypt_payload()
    {
        $expected = [
            'ciphertext' => $this::ENCRYPTED_VALUE
        ];
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'getDecryptPayload', [$this::ENCRYPTED_VALUE]);

        $this->assertEquals($expected, $data);
    }

    /** @test */
    public function it_creates_a_contextfull_decrypt_payload()
    {
        $expected = [
            'ciphertext' => $this::ENCRYPTED_VALUE,
            'context' => $this::ENCODED_VAULT_CONTEXT
        ];
        $client = $this->getRealClient();
        $data = $this->invokeMethod($client, 'getDecryptPayload', [$this::ENCRYPTED_VALUE, $this::VAULT_CONTEXT]);

        $this->assertEquals($expected, $data);
    }

    /** @test */
    public function it_decrypts()
    {
        $mockResponse = new MockHandler([
            new Response(200,
                $headers = [],
                $body = $this::DECRYPTED_RETURN)
        ]);
        $mockHandler = HandlerStack::create($mockResponse);
        $mock = new Client(['handler' => $mockHandler]);

        $client = $this->getRealClient($mock);
        $response = $this->invokeMethod($client, 'decrypt', [$this::VAULTTEST_PREFIX,$this::ENCRYPTED_VALUE]);
        $this->assertEquals($this::VALID_STRING, $response);
    }
}