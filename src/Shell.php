<?php
namespace MPM;

class Shell
{
    /**
     * Execute an array of shell commands
     *
     * @param array $command
     * @param boolean $printOutput
     * @return void
     */
	public static function exec(array $command, bool $printOutput = false)
	{
		$commandString = implode(' ', $command);

		echo '$ ' . $commandString . PHP_EOL;

		exec($commandString . ' 2> /dev/null', $output);

        if ($printOutput) {
            echo implode(PHP_EOL, $output);
            echo PHP_EOL;
        }

        return $output;
	}

    /**
     * Normalize a path, replacing ".", ".." and "~"
     *
     * @param string $path
     * @return void
     */
    public static function normalizePath(string $path)
    {
        $path = str_replace('~', posix_getpwuid(posix_getuid())['dir'], $path);

        $explode = explode('/', $path);
        $newPath = [];

        foreach ($explode as $segment) {
            if ($segment === '..') {
                array_pop($newPath);
                continue;
            }

            if ($segment === '.') {
                continue;
            }

            $newPath[] = $segment;
        }

        return implode('/', $newPath);
    }
}