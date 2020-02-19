<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Laravelizer\LaravelizerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->resetFileSystem();
        });
        $this->beforeApplicationDestroyed(function () {
            $this->resetFileSystem();
        });


        if (!isset($_ENV['IS_CHIPPER'])) {
            Config::set('database.connections.chipperci.driver', 'mysql');
            Config::set('database.connections.chipperci.username', 'chipperci');
            Config::set('database.connections.chipperci.password', 'secret');
            Config::set('database.connections.chipperci.host', '127.0.0.1');
            Config::set('database.connections.chipperci.port', '3306');
            Config::set('database.connections.chipperci.database', 'chipperci');
            Config::set('database.default', 'chipperci');
        }
    }

    public function resetFileSystem()
    {
        foreach (['model', 'migration', 'factory', 'nova', 'test'] as $component) {
            Config::set('laravelizer.'.$component.'.path', '/tmp/laravelizer/'.$component);

            $dir = '/tmp/laravelizer/'.$component;
            if (is_dir($dir)) {
                $files = array_diff(scandir($dir), ['.', '..']);
                foreach ($files as $file) {
                    unlink("$dir/$file");
                }
                rmdir($dir);
            }
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelizerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [__DIR__.'/../resources/views']);
        $app['config']->set('app.key', 'base64:r0w0xC+mYYqjbZhHZ3uk1oH63VadA3RKrMW52OlIDzI=');
    }
}
