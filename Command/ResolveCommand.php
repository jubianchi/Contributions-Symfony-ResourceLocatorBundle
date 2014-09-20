<?php
namespace Hoathis\Bundle\ResourceLocatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ResolveCommand extends ContainerAwareCommand
{
    public function __construct($name = null)
    {
        parent::__construct($name ?: 'locator:resolve');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->addArgument('path', InputArgument::REQUIRED, 'The path to resolve')
            ->addOption('relative', 'r', InputOption::VALUE_NONE, 'Resolve to a relative path')
            ->addOption('relative-root', 'o', InputOption::VALUE_REQUIRED, 'Resolve relative path from', getcwd())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $resolved = $this->getContainer()->get('hoathis.locator.wrapper')->resolve($input->getArgument('path'));

        if ($input->getOption('relative') === true) {
            $resolved = $fs->makePathRelative(dirname($resolved), $input->getOption('relative-root')) . basename($resolved);
        }

        $table = $this->getHelper('table');
        $table
            ->addRows(
                array(
                    array('Virtual path', sprintf('<info>%s</info>', $input->getArgument('path'))),
                    array('Resolved path', sprintf('<info>%s</info>', $resolved)),
                    array('Is directory', sprintf('<info>%s</info>', $this->yesNo(is_dir($resolved)))),
                    array('Is file', sprintf('<info>%s</info>', $this->yesNo(is_file($resolved)))),
                    array('Is symlink', sprintf('<info>%s</info>', $this->yesNo(is_link($resolved)))),
                )
            )
            ->render($output)
        ;
    }

    protected function yesNo($bool)
    {
        return $bool ? 'yes' : 'no';
    }
} 
