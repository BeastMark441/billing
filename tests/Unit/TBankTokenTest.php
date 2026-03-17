<?php

namespace Tests\Unit;

use App\Services\TBankService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TBankTokenTest extends TestCase
{
    #[Test]
    public function it_excludes_receipt_and_data_from_token_generation(): void
    {
        config()->set('services.tbank.terminal_key', 'TEST_TERMINAL');
        config()->set('services.tbank.password', 'TEST_PASSWORD');
        config()->set('services.tbank.url', 'https://securepay.tinkoff.ru/v2/');

        $service = new TBankService;

        $paramsBase = [
            'TerminalKey' => 'TEST_TERMINAL',
            'Amount' => 1000,
            'OrderId' => '123',
            'Description' => 'test',
        ];

        $paramsWithReceipt = $paramsBase + [
            'Receipt' => [
                'Email' => 'a@b.c',
                'Taxation' => 'osn',
                'Items' => [
                    [
                        'Name' => 'x',
                        'Price' => 1000,
                        'Quantity' => 1,
                        'Amount' => 1000,
                        'Tax' => 'none',
                    ],
                ],
            ],
            'DATA' => ['Email' => 'a@b.c'],
        ];

        $ref = new \ReflectionClass($service);
        $method = $ref->getMethod('generateToken');
        $method->setAccessible(true);

        $token1 = $method->invoke($service, $paramsBase);
        $token2 = $method->invoke($service, $paramsWithReceipt);

        $this->assertSame($token1, $token2);
    }
}
