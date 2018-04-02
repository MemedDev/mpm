<?php
namespace MPM;

use MPM\Shell;

class Git
{
    /**
     * Clone a git repository, using cache
     *
     * @param string $url
     * @param string $path
     * @param string $cachePath
     * @return void
     */
	public static function clone(string $url, string $path, string $cachePath = '~/.gitcache')
	{
        $repoName = explode('/', rtrim($url, '.git'));
        $repoName = $repoName[count($repoName) - 1];

        $cacheName = $repoName;
        $cachePath .= '/' . md5($cacheName) . '.' . $cacheName;
        $cachePath = Shell::normalizePath($cachePath);

        $path = Shell::normalizePath($path);

        if (! file_exists($cachePath)) {
            Shell::exec([
                'git',
                'clone',
                '--mirror',
                $url,
                $cachePath
            ]);
        }

        if (! file_exists($path . '/.git')) {
            Shell::exec([
                'git',
                'clone',
                '--reference',
                $cachePath,
                $url,
                $path
            ]);
        }
	}

    /**
     * Fetch a remote git repository
     *
     * @param string $url
     * @param string $path
     * @param string $rev
     * @return void
     */
    public static function fetch(string $url, string $path, string $rev = '')
    {
        $params = [
            'git',
            '-C',
            $path,
            'remote',
            'set-url',
            'origin',
            $url,
            'fetch',
            '--tags',
            '--force',
            '--prune',
            'origin'
        ];

        if (! empty($rev)) {
            $params[] = $rev;
        }

        return Shell::exec($params);
    }

    /**
     * Get the current branch of a git path
     *
     * @param string $path
     * @return void
     */
    public static function getBranch(string $path)
    {
        return Shell::exec([
            'git',
            '-C',
            $path,
            'rev-parse',
            '--abbrev-ref',
            'HEAD'
        ])[0];
    }

    /**
     * Get the current commit hash of a git path
     *
     * @param string $path
     * @return void
     */
    public static function getHash(string $path)
    {
        return Shell::exec([
            'git',
            '-C',
            $path,
            'rev-parse',
            'HEAD'
        ])[0];
    }

    /**
     * Get the current commit tag of a git path
     *
     * @param string $path
     * @return void
     */
    public static function getTag(string $path)
    {
        $return = Shell::exec([
            'git',
            '-C',
            $path,
            'describe',
            '--tags',
            '--exact-match'
        ]);

        if (! count($return)) {
            return '';
        }

        return $return[0];
    }

    /**
     * Update a git path
     *
     * @param string $path
     * @param string $rev
     * @return void
     */
    public static function update(string $path, string $rev)
    {
        Shell::exec(['git', '-C', $path, 'stash']);
        Shell::exec(['git', '-C', $path, 'clean', '--force', '-d', '-x']);
        Shell::exec(['git', '-C', $path, 'checkout', '--force', $rev]);
        Shell::exec(['git', '-C', $path, 'branch', '--set-upstream-to', 'origin/' . $rev]);
        Shell::exec(['git', '-C', $path, 'pull', '--ff-only', '--no-rebase']);
    }
}