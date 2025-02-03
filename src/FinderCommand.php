<?php

namespace PhpUnitFinder;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version;
use PHPUnit\TextUI\XmlConfiguration\Loader;
use PHPUnit\TextUI\XmlConfiguration\TestSuiteMapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A symfony command for finding PHPUnit files.
 */
class FinderCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('config-file', 'c', InputOption::VALUE_OPTIONAL, "The phpunit.xml config file to use.", getcwd() . '/phpunit.xml');
        $this->addOption('bootstrap-file', 'b', InputOption::VALUE_OPTIONAL, "The tests bootstrap file.", getcwd() . '/tests/bootstrap.php');
        $this->addArgument('test-suite', InputArgument::IS_ARRAY, "The test suites to scan.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configFile = $input->getOption('config-file');
        $bootstrap = $input->getOption('bootstrap-file');
        include_once $bootstrap;
        $testSuites = $input->getArgument('test-suite');
        $testFilenames = [];

        $config = (new Loader())->load($configFile);

        foreach ($config->testSuite() as $suite) {
            if ($testSuites && !in_array($suite->name(), $testSuites, TRUE)) {
                continue;
            }
            // PHPUnit 9.4 and earlier.
            if (str_starts_with(Version::id(), '9')) {
                $testSuite = (new TestSuiteMapper())->map($config->testSuite(), $suite->name());
            } // PHPUnit 10.0 and later.
            else {
                $testSuite = (new TestSuiteMapper())->map($config->filename(), $config->testSuite(), $suite->name(), '');
            }
            foreach (new \RecursiveIteratorIterator($testSuite) as $test) {
                if ($test instanceof TestCase) {
                    $testFilenames[] = ((new \ReflectionClass($test))->getFileName());
                }
            }
        }

        $testFilenames = array_unique($testFilenames);
        foreach ($testFilenames as $testFilename) {
            $output->writeln($testFilename);
        }

        return 0;
    }

}
