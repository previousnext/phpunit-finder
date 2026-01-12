<?php

namespace PhpUnitFinder\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpUnitFinder\FinderCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * A test of the command functionality
 */
class FinderTest extends TestCase {

  /**
   * A stub test.
   */
  public function testDiscovery(): void {
    $command = new CommandTester(new FinderCommand());
    $command->execute([
      '--config-file' => dirname(__DIR__) . '/fixtures/phpunit.xml'
    ]);
    $output = $command->getDisplay();
    $this->assertStringContainsString('TestUnitTest.php', $output);
    $this->assertStringContainsString('TestFunctionalTest.php', $output);
  }

}
