<?php
namespace MPM;

class Dependency
{
    /**
     * Install a dependency
     *
     * @param string $path
     * @param array $config
     * @return void
     */
    public static function install(string $path, array $config)
    {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        Git::clone($config['url'], $path);

        $gitRevs = [
            Git::getBranch($path),
            Git::getHash($path),
            Git::getTag($path),
        ];

        if (empty($config['rev'])) {
            $config['rev'] = 'master';
        }

        if (! in_array($config['rev'], $gitRevs)) {
            Git::fetch($config['url'], $path, $config['rev']);
        }

        Git::update($path, $config['rev']);

        if (Config::hasConfig($path)) {
            Config::install($path);
        }
    }
}