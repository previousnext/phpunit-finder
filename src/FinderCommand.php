<?php

namespace PhpUnitFinder;

use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Configuration;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A symfony command for finding PHPUnit files.
 */
class FinderCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this->addOption('config-file', 'c', InputOption::VALUE_OPTIONAL, "The phpunit.xml config file to use.", getcwd() . '/phpunit.xml');
    $this->addOption('bootstrap-file', 'b', InputOption::VALUE_OPTIONAL, "The tests bootstrap file.", getcwd() . '/tests/bootstrap.php');
    $this->addArgument('test-suite', InputArgument::IS_ARRAY, "The test suites to scan.");
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $configFile = $input->getOption('config-file');
    $bootstrap = $input->getOption('bootstrap-file');
    include_once $bootstrap;
    $testSuites = $input->getArgument('test-suite');

    $config = Configuration::getInstance($configFile);
    if (empty($testSuites)) {
      $testSuites = $config->getTestSuiteNames();
    }
    foreach ($testSuites as $suiteName) {
      $suite = $config->getTestSuiteConfiguration($suiteName);
      $output->writeln($suiteName);
      foreach (new \RecursiveIteratorIterator($suite->getIterator()) as $test) {
        if ($test instanceof TestCase) {
          $output->writeln((new ReflectionClass($test))->getFileName());
        }
      }
    }
  }

}
