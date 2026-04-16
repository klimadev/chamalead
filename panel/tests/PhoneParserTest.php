<?php

declare(strict_types=1);

require_once __DIR__ . '/../PhoneParser.php';

use Panel\PhoneParser;

class PhoneParserTest
{
    private int $passed = 0;
    private int $failed = 0;

    public function run(): void
    {
        $this->testNormalizeWithPlus55();
        $this->testNormalizeWithMask();
        $this->testNormalizePlainDigits();
        $this->testMobileValidation();
        $this->testFixedValidation();
        $this->testInvalidDDD();
        $this->testInvalidTooShort();
        $this->testCarrierDetection();
        $this->testStateFromDDD();

        echo "\n========================================\n";
        echo "Passed: {$this->passed}\n";
        echo "Failed: {$this->failed}\n";
        echo "========================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            echo "✓ {$message}\n";
            $this->passed++;
        } else {
            echo "✗ {$message}\n";
            $this->failed++;
        }
    }

    public function testNormalizeWithPlus55(): void
    {
        $result = PhoneParser::parse('+55 11 99309-4045');
        $this->assert($result['normalized'] === '5511993094045', 'Normalize: +55 11 99309-4045 -> 5511993094045');
    }

    public function testNormalizeWithMask(): void
    {
        $result = PhoneParser::parse('(11) 99309-4045');
        $this->assert($result['normalized'] === '5511993094045', 'Normalize: (11) 99309-4045 -> 5511993094045');
    }

    public function testNormalizePlainDigits(): void
    {
        $result = PhoneParser::parse('11993094045');
        $this->assert($result['normalized'] === '5511993094045', 'Normalize: 11993094045 -> 5511993094045');
    }

    public function testMobileValidation(): void
    {
        $result = PhoneParser::parse('+55 11 99309-4045');
        $this->assert($result['is_valid'] === true, 'Mobile: should be valid');
        $this->assert($result['type'] === 'mobile', 'Mobile: type should be mobile');
        $this->assert($result['ddd'] === '11', 'Mobile: DDD should be 11');
    }

    public function testFixedValidation(): void
    {
        $result = PhoneParser::parse('+55 11 3232-3232');
        $this->assert($result['is_valid'] === true, 'Fixed: should be valid');
        $this->assert($result['type'] === 'fixed', 'Fixed: type should be fixed');
    }

    public function testInvalidDDD(): void
    {
        $result = PhoneParser::parse('+55 01 99309-4045');
        $this->assert($result['is_valid'] === false, 'Invalid DDD: should be invalid');
        $this->assert(str_contains($result['reason'] ?? '', 'DDD inválido'), 'Invalid DDD: should have DDD error');
    }

    public function testInvalidTooShort(): void
    {
        $result = PhoneParser::parse('1199309');
        $this->assert($result['is_valid'] === false, 'Too short: should be invalid');
    }

    public function testCarrierDetection(): void
    {
        $result = PhoneParser::parse('+55 11 99309-4045');
        $carrier = $result['carrier_inferred_from_prefix'];
        $this->assert($carrier !== null, 'Carrier: should detect carrier');
    }

    public function testStateFromDDD(): void
    {
        $state = PhoneParser::getStateFromDDD('11');
        $this->assert($state === 'SP', 'State: DDD 11 should be SP');

        $stateName = PhoneParser::getStateNameFromDDD('11');
        $this->assert($stateName === 'São Paulo', 'State: DDD 11 should be São Paulo');
    }
}

$test = new PhoneParserTest();
$test->run();