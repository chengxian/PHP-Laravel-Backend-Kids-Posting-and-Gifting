<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    const LOAN_SETTING = 'kf_loan_approval_required';
    const TRANSFER_SETTING = 'kf_transaction_approval_required';
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        touch('database' . DIRECTORY_SEPARATOR . 'database_test.sqlite');

        putenv('DB_CONNECTION=sqlite_test');
//        global $app;
//
//        if (is_null($app)) {
            $app = require __DIR__ . '/../bootstrap/app.php';

            $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

//        }

        return $app;
    }

    protected function tearDown()
    {
        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
//                dd($prop);
                $prop->setAccessible(true);
                $prop->setValue($this, null);
//                unset($prop);
            }
        }
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function seeLikeInDatabase($table, $what, $like, $who, $connection = null)
    {
        $database = $this->app->make('db');

        $connection = $connection ?: $database->getDefaultConnection();
        $count = $database->connection($connection)->table($table)->where($what, $like, $who)->count();
        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [$what, $like, $who].', $table
        ));

        return $this;
    }

    protected function getDummyMigrationsDir()
    {
        $path = "tests/laravault/migrations";

        return $path;
    }

    protected function getMigrationsDir()
    {
        $path = "database/migrations";

        return $path;
    }
}
