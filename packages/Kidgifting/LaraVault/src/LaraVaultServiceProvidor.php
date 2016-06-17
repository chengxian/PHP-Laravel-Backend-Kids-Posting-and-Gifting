<?php
/**
 * @author: chengxian
 * Date: 4/11/16
 * @copyright Cheng Xian Lim
 */


namespace Kidgifting\LaraVault;


use App\TimModel;
use Illuminate\Support\ServiceProvider;
use Kidgifting\ThinTransportVaultClient\TransitClient;

class LaraVaultServiceProvidor extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    public static function getTransitClient()
    {

        $enabled = config('vault.enabled');

        if (!$enabled) {
            return null;
        }

        $vaultAddr = config('vault.addr');
        $vaultToken = config('vault.token');

        if ($vaultToken == null || $vaultToken == 'none') {
            throw new Exception("Vault token must be configured");
        }

        $_client = new TransitClient($vaultAddr, $vaultToken);
        return $_client;
    }

    /**
     * Register the command.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Kidgifting\ThinTransportVaultClient\TransitClient', function ($app) {
            return $this::getTransitClient();
        });

        $this->app->singleton('Kidgifting\LaraVault\LaraVaultHasher', function ($app) {
            return new LaraVaultHasher($app['Kidgifting\ThinTransportVaultClient\TransitClient']);
        });
    }
}
