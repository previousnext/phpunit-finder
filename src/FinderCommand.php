<?php

namespace PhpUnitFinder;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestDirectoryNotFoundException;
use PHPUnit\TextUI\TestSuiteMapper;
use PHPUnit\TextUI\XmlConfiguration\Configuration;
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

    $phpunit_9 = FALSE;
    if (!class_exists('\PHPUnit\Util\Configuration')) {
      $config = (new \PHPUnit\TextUI\XmlConfiguration\Loader())->load($configFile);
      $phpunit_9 = TRUE;
    }
    else {
      $config = PHPUnit\Util\Configuration::getInstance($configFile);
    }
    if (empty($testSuites)) {
      $testSuites = $phpunit_9 ? $config->testSuite() : $config->getTestSuiteNames();
    }
    $testFilenames = [];
    if ($phpunit_9) {
      foreach (array_map(function (\PHPUnit\TextUI\XmlConfiguration\TestSuite $suite) {
        return $suite->name();
      }, iterator_to_array($testSuites)) as $name) {
        try {
          foreach ((new TestSuiteMapper)->map($testSuites, $name) as $test) {
            if ($test instanceof TestCase) {
              $testFilenames[] = ((new \ReflectionClass($test))->getFileName());
            }
          }
        }
        catch (TestDirectoryNotFoundException $e) {
          continue;
        }
      }
    }
    else {
      foreach ($testSuites as $suite) {
        $suite = $config->getTestSuiteConfiguration($suite);
        foreach (new \RecursiveIteratorIterator($suite->getIterator()) as $test) {
          if ($test instanceof TestCase) {
            $testFilenames[] = ((new \ReflectionClass($test))->getFileName());
          }
        }
      }

    }
    $testFilenames = array_unique($testFilenames);
    foreach ($testFilenames as $testFilename) {
      $output->writeln($testFilename);
    }
  }

}
