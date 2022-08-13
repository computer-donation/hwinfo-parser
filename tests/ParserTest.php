<?php

namespace Tienvx\HwinfoParser\Tests;

use PHPUnit\Framework\TestCase;
use Tienvx\HwinfoParser\Parser;

/**
 * @covers \Tienvx\HwinfoParser\Parser
 */
class ParserTest extends TestCase
{
    public function testParse(): void
    {
        $parser = new Parser();
        $devices = $parser->parse(__DIR__ . '/probe/F52FF3FF24');

        self::assertCount(44, $devices);
        self::assertArrayNotHasKey(0, $devices[0]);
        self::assertSame('bios', $devices[0]['Hardware Class']);
        self::assertSame('off', $devices[0]['BIOS Keyboard LED Status']['Scroll Lock']);
        self::assertSame('"_ASUS_"', $devices[0]['MP spec rev 1.4 info']['Product id']);
        self::assertSame([
            'PCI supported',
            'BIOS flashable',
            'BIOS shadowing allowed',
            'CD boot supported',
            'Selectable boot supported',
            'BIOS ROM socketed',
            'EDD spec supported',
            '1.2MB Floppy supported',
            '720kB Floppy supported',
            '2.88MB Floppy supported',
            'Print Screen supported',
            '8042 Keyboard Services supported',
            'Serial Services supported',
            'Printer Services supported',
            'ACPI supported',
            'USB Legacy supported',
            'Smart Battery supported',
            'BIOS Boot Spec supported',
        ], $devices[0]['BIOS Info']['Features']);
        self::assertSame(
            ['2QQzte7dL1JPk', 'iYbRfBnjiGHoX', 'j9KHuh7aicXFA', '90NIOA312W1232VD13AC'],
            $devices[0]['OEM Strings']
        );
    }
}
