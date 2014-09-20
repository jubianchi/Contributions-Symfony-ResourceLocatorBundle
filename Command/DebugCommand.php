<?php
namespace Hoathis\Bundle\ResourceLocatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DebugCommand extends ContainerAwareCommand
{
    public function __construct($name = null)
    {
        parent::__construct($name ?: 'locator:debug');
    }

    protected function configure()
    {
        parent::configure();

        $this->addArgument('protocol', InputArgument::OPTIONAL, 'The protocol to debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $this->getHelper('table');
        $table->setHeaders([
            'scheme',
            'path'
        ]);

        $protocols = $this->getContainer()->get('hoathis.locator.wrapper');

        if (null !== ($protocol = $input->getArgument('protocol'))) {
            $protocol = str_replace('://', '', $protocol);

            if (false === isset($protocols[$protocol])) {
                throw new \RuntimeException('Protocol not found: ' . $protocol . '://');
            }

            $protocols = array($protocols[$protocol]);
        }

        foreach ($protocols as $protocol) {
            $path = array_filter(
                array_map(
                    function($path) {
                        return realpath($path);
                    },
                    explode(';', $protocol->getReach())
                ),
                function($path) {
                    return $path !== false;
                }
            );

            $table->addRows(
                array(
                    array(
                        $protocol->getScheme() . '://',
                        implode(PHP_EOL, $path)
                    ),
                    array('', '')
                )
            );

        }

        $table->render($output);
    }
} 
