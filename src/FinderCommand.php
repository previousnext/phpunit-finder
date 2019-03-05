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
    $this->addArgument('test-suite', InputArgument::IS_ARRAY, "The test suites to scan.", ['unit', 'functional']);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $configFile = $input->getOption('config-file');
    $testSuites = $input->getArgument('test-suite');
    $config = Configuration::getInstance($configFile);
    foreach ($testSuites as $suiteName) {
      $suite = $config->getTestSuiteConfiguration($suiteName);
      foreach (new \RecursiveIteratorIterator($suite->getIterator()) as $test) {
        if ($test instanceof TestCase) {
          $output->writeln((new ReflectionClass($test))->getFileName());
        }
      }
    }
  }

}
