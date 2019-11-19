<?php

namespace PhpUnitFinder\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PhpUnitFinder\FinderCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * A stub test.
 */
class FinderTest extends TestCase {

  /**
   * A stub test.
   */
  public function testDiscovery() {
    $command = new CommandTester(new FinderCommand());
    $command->execute([]);
    $this->assertStringContainsString(__FILE__, $command->getDisplay());
  }

}
