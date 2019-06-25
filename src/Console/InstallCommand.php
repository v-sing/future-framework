<?php

namespace Future\Admin\Console;

use Future\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initAdminDirectory();
        $this->initDatabase();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        try {
            $connection = config('admin.database.connection') ?: config('database.default');
            Schema::getFacadeAccessor($connection);
        } catch (\Exception $exception) {
            $this->line('数据库连接失败！，请检查数据库配置。');
            return;
        }
        $this->call('migrate');

        $userModel = config('admin.database.admin_model');

        if ($userModel::count() == 0) {
            $this->call('db:seed', ['--class' => \Future\Admin\Auth\Database\AdminTablesSeeder::class]);
        }

    }

    /**
     * Initialize the admAin directory.
     *
     * @return void
     */
    protected function initAdminDirectory()
    {

        $this->directory = config('admin.directory');
        if (is_dir($this->directory)) {
            $this->line("<error>{$this->directory} directory already exists !</error> ");
            return;
        }
        $this->info(AdminCommand::$logo);
        $this->initEnv();
        $this->makeDir('/');
        $this->line('<info>Admin directory was created:</info> ' . str_replace(base_path(), '', $this->directory));
        $this->makeDir('Controllers');
        $this->makeDir('Routes');
        $this->makeDir('Resources/lang/en-US');
        $this->makeDir('Resources/lang/zh-CN');
        $this->makeDir('Resources/views/demo');
        $this->createDemoController();
        $this->createResourcesFile();
        $this->createRoutesFile();

    }

    /**
     * Create HomeController.
     *
     * @return void
     */
    public function createDemoController()
    {
        $homeController = $this->directory . '/Controllers/DemoController.php';
        $contents       = $this->getStub('install/controller/DemoController');

        $this->laravel['files']->put(
            $homeController,
            str_replace('DummyNamespace', config('admin.route.namespace'), $contents)
        );
        $this->line('<info>DemoController file was created:</info> ' . str_replace(base_path(), '', $homeController));
    }

    /**
     * Create routes file.
     *
     * @return void
     */
    protected function createRoutesFile()
    {
        $file     = $this->directory . '/routes.php';
        $contents = $this->getStub('routes');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Routes file was created:</info> ' . str_replace(base_path(), '', $file));
        $file     = $this->directory . '/Routes/demo.php';
        $contents = $this->getStub('install/routes/demo');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Routes file was created:</info> ' . str_replace(base_path(), '', $file));
    }

    /**
     * 创建资源文件
     */
    protected function createResourcesFile()
    {
        $file     = $this->directory . '/Resources/lang/en-US/demo.php';
        $contents = $this->getStub('install/resources/lang/en-US/demo');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Resources file was created:</info> ' . str_replace(base_path(), '', $file));
        $file     = $this->directory . '/Resources/lang/zh-cn/demo.php';
        $contents = $this->getStub('install/resources/lang/zh-cn/demo');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Resources file was created:</info> ' . str_replace(base_path(), '', $file));
        $file     = $this->directory . '/Resources/views/demo/index.blade.php';
        $contents = $this->getStub('install/resources/view/demo/index.blade');
        $this->laravel['files']->put($file, str_replace('DummyNamespace', config('admin.route.namespace'), $contents));
        $this->line('<info>Resources file was created:</info> ' . str_replace(base_path(), '', $file));
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__ . "/stubs/$name.stub");
    }

    /**
     * Make new directory.
     *
     * @param string $path
     */
    protected function makeDir($path = '')
    {
        $this->laravel['files']->makeDirectory("{$this->directory}/$path", 0755, true, true);
    }

    protected function initEnv()
    {
        $connection = $this->choice('DB_CONNECTION', [
            'mysql', 'sqlite', 'pgsql', 'sqlsrv'
        ], 0);
        $host       = $this->ask('DB_HOST');
        $port       = $this->ask('DB_PORT');
        $database   = $this->ask('DB_DATABASE');
        $username   = $this->ask('DB_USERNAME');
        $password   = $this->secret('DB_PASSWORD');
        $env        = $this->laravel['files']->get('.env');
        $data       = explode("\r\n", $env);
        foreach ($data as $key => $value) {
            if ($value != '') {
                $data[$key] = explode('=', $value);
                switch ($data[$key][0]) {
                    case 'DB_CONNECTION':
                        $data[$key][1] = $connection;
                        break;
                    case 'DB_HOST':
                        $data[$key][1] = $host;
                        break;
                    case 'DB_PORT':
                        $data[$key][1] = $port;
                        break;
                    case 'DB_DATABASE':
                        $data[$key][1] = $database;
                        break;
                    case 'DB_USERNAME':
                        $data[$key][1] = $username;
                        break;
                    case 'DB_PASSWORD':
                        $data[$key][1] = $password;
                        break;

                }
                $data[$key] = implode('=', $data[$key]);
            }
        }
        $env = implode("\r\n", $data);
        $this->laravel['files']->put('.env', $env);
    }
}
