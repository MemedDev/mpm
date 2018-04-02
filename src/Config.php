<?php
namespace MPM;

class Config
{
    private const FILE_NAME = 'mpm.json';
    private static $installedPaths = [];
    private static $iterationsCounter = 0;
    private static $scriptsToRun = [];

    /**
     * Verifies if the current path has the mpm json file
     *
     * @param string $rootPath
     * @return boolean
     */
    public static function hasConfig(string $rootPath)
    {
        if (file_exists($rootPath . '/' . self::FILE_NAME)) {
            return true;
        }

        return false;
    }

    /**
     * Creates a new file with an example of usage
     *
     * @return void
     */
    public static function init()
    {
        $config = [
            'sources' => [
                [
                    'path' => '../foo',
                    'url' => 'git@github.com:MemedDev/mpm.git',
                    'rev' => 'master'
                ]
            ]
        ];

        file_put_contents(self::FILE_NAME, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Install the sources specified on the mpm json file
     *
     * @param string $rootPath
     * @return void
     */
    public static function install(string $rootPath)
    {
        $config = self::load($rootPath);

        ++self::$iterationsCounter;

        foreach ($config['sources'] as $source) {
            $path = Shell::normalizePath($rootPath . '/' . $source['path']);

            if (in_array($path, self::$installedPaths)) {
                continue;
            }

            self::$installedPaths[] = $path;

            Dependency::install(
                $path,
                $source
            );
        }

        if (array_key_exists('scripts', $config)) {
            self::pushScript($rootPath, $config['scripts'], 'postinstall');
        }

        --self::$iterationsCounter;

        if (self::$iterationsCounter === 0) {
            self::runScripts();
        }
    }

    /**
     * Load the mpm json file, decoding the json
     *
     * @param string $rootPath
     * @return void
     */
    protected static function load(string $rootPath)
    {
        return json_decode(file_get_contents($rootPath . '/' . self::FILE_NAME), true);
    }

    /**
     * Schedule a script to run after all the sources are fetched
     *
     * @param [type] $rootPath
     * @param [type] $scripts
     * @param [type] $name
     * @return void
     */
    protected static function pushScript($rootPath, $scripts, $name)
    {
        if (empty($scripts)) {
            return;
        }

        if (empty($scripts[$name])) {
            return;
        }

        if (! array_key_exists($rootPath, self::$scriptsToRun)) {
            self::$scriptsToRun[$rootPath] = [];
        }

        $script = $scripts[$name];

        if (is_string($script) && strpos($script, '@') === 0) {
            $script = $scripts[str_replace('@', '', $script)];
        }

        if (is_array($script)) {
            $script = implode(' && ', $script);
        }

        self::$scriptsToRun[$rootPath][$name] = $script;
    }

    /**
     * Run a script by the name
     *
     * @param [type] $rootPath
     * @param [type] $name
     * @return void
     */
    public static function runScript($rootPath, $name)
    {
        $config = self::load($rootPath);

        if (! array_key_exists('scripts', $config)) {
            return;
        }

        self::pushScript($rootPath, $config['scripts'], $name);
        self::runScripts();
    }

    /**
     * Run all scheduled scripts
     *
     * @return void
     */
    protected static function runScripts()
    {
        foreach (self::$scriptsToRun as $path => $scripts) {
            foreach ($scripts as $script) {
                Shell::exec([
                    'cd',
                    $path,
                    '&&',
                    $script
                ], true);
            }
        }
    }
}