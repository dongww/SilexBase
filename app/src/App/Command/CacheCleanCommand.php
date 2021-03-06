<?php
/**
 * User: dongww
 * Date: 14-6-28
 * Time: 下午1:38
 */

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Dongww\SilexBase\Developer\Cleaner;

class CacheCleanCommand extends CommandAbstract
{
    protected function configure()
    {
        $this
            ->setName('sb:cache:clean')
            ->setDescription('更新缓存。');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tc = new Cleaner\TwigCacheCleaner(
            $this->app['cache_path'] . '/twig'
        );

        $tc->clean();
        $output->writeln('Twig caches clean!');

        $rc = new Cleaner\RoutesCleaner(
            $this->app,
            $this->app['cache_path'] . '/config/routes.php',
            $this->app['src_path'] . '/*/*/_resources/routes'
        );

        $rc->clean();
        $output->writeln('Routes caches clean!');
    }
}
