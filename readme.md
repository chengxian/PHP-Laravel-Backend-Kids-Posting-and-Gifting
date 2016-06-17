  * [Install](#install)
  * [Common Commands](#common-commands)
  * [PHPUnit Tests](#phpunit-tests)
  * [Behat Tests](#behat-tests)
  * [Queues](#queues)
  * [Packages](#packages)
  * [Seed Data](#seed-data)
    * [Known Data](#known-data)
    * [Random Volume Testing](#random-volume-testing)
  * [Vault Encryption](#vault-encryption)
    * [Vault Setup](#vault-setup)
    * [Vault Process](#vault-process)
    * [Laravel Trait](#laravel-trait)
    * [Notes](#notes)
    * [Test harness](#test-harness)
    * [Todo](#todo)
* [Contributing](contributing.md "Contributing")

# Install

1. ```git clone ```
2. ```cp .env.example .env``` & configure
3. ```php artisan migrate``` to build your database
3. ```vagrant up```

# Common Commands

Generate model docblocks

```
artisan ide-helper:models --dir="packages/Kidgifting/DwollaWrapper/src/Models/" --dir="packages/Kidgifting/USAlliance/src/Models/"
```

Clear artisan cache

```
artisan clear-compiled
composer dumpautoload
artisan optimize
```
Listen to all our queues

```
artisan queue:listen --tries=3 --queue=default,gifts,sentry
```
# PHPUnit Tests

Live in ```tests``` *(for now. Some will move to packages)*

To run all:

```
phpunit -d memory_limit=512M
```

If you don't have Vault setup or enabled, turn off the VaultEndToEnd group

If you aren't VPNed into US.A disable the VPN group

```
phpunit -d memory_limit=512M --exclude-group VaultEndToEnd,VPN
```

# Behat Tests

There are 2 relevant folders for Behat tests

1. /features where the [Gherkin](docs.behat.org/en/v3.0/guides/1.gherkin.html "Gherkin") format test casts live
2. /features/bootstrap where the test harnesses live

To run: 

```
vendor/bin/behat
```

# Queues

* default
* sentry
* recurring
* gifts
* parentqueuedpaymentpop

# Packages

Kidgifting Laravel packages can be created with:

```
php artisan packager:new Kidgifting [PackageName]
```

# Seed Data

## Known Data
Seed data is available for testing. These seeds are self cleaning; they will delete the data they put in, if it exists, in advance to avoid duplicate data. Aside from the content of a comment, data will be the same each time. Comments are Lorem Ipsum

To run all seeders:

```
php artisan db:seed
```

To run individual seeders:

```
php artisan db:seed --class=UserTableSeeder
php artisan db:seed --class=ChildTableSeeder
php artisan db:seed --class=PostTableSeeder
php artisan db:seed --class=LikeTableSeeder
php artisan db:seed --class=CommentTableSeeder
```

## Random Volume Testing

To generate high volume random data, set the env property ```VOLUME_TESTING``` to the number of entities to generate. 

* *Recommended number is 500*
* Volume testing is not self-cleaning

**Note**: This is currently only set up for User, Child, and Media. More can be added in ```database/factories/ModelFactory.php```

# Vault Encryption

Kidgifting uses Hashicorp's [Vault](https://www.vaultproject.io/ "Vault") to encrypt user PII. There are 3 main aspects to this:

1. The Vault Server
2. The Vault Client
3. The Laravel Model trait that encrypts attributes

The vault server can be run from the command line. If it is [installed](https://www.vaultproject.io/downloads.html "installed") the server can be started with this command, from the root of the Kidgifting project:

```
vault server -config ./packages/Kidgifting/ThinTransportVaultClient/vaultconfig/vault.hcl
```

## Vault Setup

If running vault locally for the first time, it needs to be set up. This is only needed for the first time. After this, Laravel will interact with Vault for you. The only exception to this is unseal. You will need to unseal the vault each time it's started.

1. Leave the window where you started vault open
2. In a new window: ```export VAULT_ADDR=http://192.168.20.20:8200```
3. ```vault init``` will give you the master key shards for your instance. Hold on to these
4. Also make note of the initial root token. Take it and run this: ```export VAULT_TOKEN=5fa583c8-f5b5-85cb-58e4-c4e97d2ec59e```
5. ```vault unseal``` and put in 3 of the master key shards (keep running the command)
6. Create the access policy that Laravel will use: ```vault policy-write web ./packages/Kidgifting/ThinTransportVaultClient/vaultconfig/vault.policy.web.json```
7. Get an access token for Laravel: ```vault token-create -orphan -policy="web"```
8. Add this token to app/vault.php

## Vault Process

If a Laravel Model is encrypting a field this is the flow using Vault's [Transit](https://www.vaultproject.io/docs/secrets/transit/index.html "Transit") backend

1. Model determines if encryption is needed and sends cleartext to Vault Client
2. Vault client talks to Vault Server and gets ciphertext
3. Vault client hands ciphertext to Laravel Model
4. Laravel saves ciphertext in Laravel's data store

Decryption:

1. Model retreives ciphertext from Laravel's data store
2. Model determines if decryption is needed and sends ciphertext to Vault Client
2. Vault client talks to Vault Server and gets cleartext
3. Vault client hands cleartext to Laravel Model


## Laravel Trait

To enable encryption on a trait: 

```php
use Kidgifting\LaraVault\LaraVault;

class User extends Authenticatable
{
    use LaraVault;

    protected $encrypts = [
            'driver_licence',
    ];
}
```

**Fields using Vault MUST be larger than normal:**

```php
$table->string('driver_licence', 255)
```

## Notes

* The master key is unknown to anyone except the operator
* A different encryption key is used for each field that is encrypted. Each key is encrypted with the master key
* Every row gets it's own context in Vault

## Test harness

Mild one available: ```php artisan vault:transittest```

## Todo

* Setup Vault Server Logging
* Enable Vault Server Cert Auth
* Enable Vault Server TLS
* Unit Tests
