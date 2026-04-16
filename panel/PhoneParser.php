<?php

declare(strict_types=1);

namespace Panel;

class PhoneParser
{
    private static ?array $carrierMap = null;

    private const MIN_DIGITS = 10;
    private const MAX_DIGITS = 13;
    private const COUNTRY_CODE = '55';
    private const DDD_MIN = 11;
    private const DDD_MAX = 99;

    private const TYPE_MOBILE = 'mobile';
    private const TYPE_FIXED = 'fixed';
    private const TYPE_UNKNOWN = 'unknown';

    private const INVALID_REASON_EMPTY = 'Telefone vazio';
    private const INVALID_REASON_TOO_SHORT = 'Número muito curto (mínimo 10 dígitos)';
    private const INVALID_REASON_TOO_LONG = 'Número muito longo (máximo 13 dígitos com +55)';
    private const INVALID_REASON_INVALID_DDD = 'DDD inválido (deve estar entre 11 e 99)';
    private const INVALID_REASON_INVALID_FORMAT = 'Formato de telefone brasileiro inválido';
    private const INVALID_REASON_MOBILE_MUST_START_9 = 'Celular deve começar com 9 (após o DDD)';

    public static function parse(string $input): array
    {
        $normalized = self::normalize($input);

        if ($normalized === '') {
            return self::invalidResponse(self::INVALID_REASON_EMPTY, $input);
        }

        $validation = self::validateStructure($normalized);
        if (!$validation['is_valid']) {
            $response = self::invalidResponse($validation['reason'], $input);
            $response['normalized'] = $normalized;

            return $response;
        }

        $parsed = self::extractComponents($normalized);
        $parsed['is_valid'] = true;
        $parsed['input'] = $input;
        $parsed['carrier_inferred_from_prefix'] = self::findCarrierByPrefix($normalized);
        $parsed['carrier_is_guaranteed'] = false;

        return $parsed;
    }

    public static function parseWithFullValidation(string $input): array
    {
        $normalized = self::normalize($input);

        if ($normalized === '') {
            return self::invalidResponse(self::INVALID_REASON_EMPTY, $input);
        }

        $validation = self::validateFullRules($normalized);
        if (!$validation['is_valid']) {
            $response = self::invalidResponse($validation['reason'], $input);
            $response['normalized'] = $normalized;

            return $response;
        }

        $parsed = self::extractComponents($normalized);
        $parsed['is_valid'] = true;
        $parsed['input'] = $input;
        $parsed['carrier_inferred_from_prefix'] = self::findCarrierByPrefix($normalized);
        $parsed['carrier_is_guaranteed'] = false;

        return $parsed;
    }

    private static function normalize(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            return $digits;
        }

        if (strlen($digits) === 10 || strlen($digits) === 11) {
            return self::COUNTRY_CODE . $digits;
        }

        return $digits;
    }

    private static function validateStructure(string $digits): array
    {
        $length = strlen($digits);

        if ($length < self::MIN_DIGITS) {
            return ['is_valid' => false, 'reason' => self::INVALID_REASON_TOO_SHORT];
        }

        if ($length > self::MAX_DIGITS) {
            return ['is_valid' => false, 'reason' => self::INVALID_REASON_TOO_LONG];
        }

        if (!str_starts_with($digits, self::COUNTRY_CODE)) {
            return ['is_valid' => false, 'reason' => self::INVALID_REASON_INVALID_FORMAT];
        }

        $ddd = (int) substr($digits, 2, 2);
        if ($ddd < self::DDD_MIN || $ddd > self::DDD_MAX) {
            return ['is_valid' => false, 'reason' => self::INVALID_REASON_INVALID_DDD];
        }

        return ['is_valid' => true, 'reason' => null];
    }

    private static function validateFullRules(string $digits): array
    {
        $structure = self::validateStructure($digits);
        if (!$structure['is_valid']) {
            return $structure;
        }

        $length = strlen($digits);
        $subscriberStart = $digits[3] ?? '';

        if ($length === 12 && $subscriberStart !== '9') {
            return ['is_valid' => false, 'reason' => self::INVALID_REASON_MOBILE_MUST_START_9];
        }

        if ($length === 11 && $subscriberStart !== '9') {
            return ['is_valid' => false, 'reason' => self::INVALID_REASON_MOBILE_MUST_START_9];
        }

        if ($length === 13) {
            $subscriber = substr($digits, 3);
            if (strlen($subscriber) !== 9) {
                return ['is_valid' => false, 'reason' => 'Número de celular deve ter 9 dígitos'];
            }
            if ($subscriber[0] !== '9') {
                return ['is_valid' => false, 'reason' => self::INVALID_REASON_MOBILE_MUST_START_9];
            }
        }

        return ['is_valid' => true, 'reason' => null];
    }

    private static function extractComponents(string $digits): array
    {
        $ddd = substr($digits, 2, 2);
        $subscriber = substr($digits, 4);
        $length = strlen($digits);

        $type = self::TYPE_UNKNOWN;
        if ($length === 13 || ($length === 12 && ($digits[3] ?? '') === '9')) {
            $type = self::TYPE_MOBILE;
        } elseif ($length === 12 || $length === 11 || $length === 10) {
            $type = self::TYPE_FIXED;
        }

        return [
            'normalized' => $digits,
            'country_code' => self::COUNTRY_CODE,
            'ddd' => $ddd,
            'subscriber' => $subscriber,
            'type' => $type,
        ];
    }

    private static function invalidResponse(string $reason, string $input): array
    {
        return [
            'is_valid' => false,
            'input' => $input,
            'normalized' => null,
            'country_code' => null,
            'ddd' => null,
            'subscriber' => null,
            'type' => null,
            'carrier_inferred_from_prefix' => null,
            'carrier_is_guaranteed' => false,
            'reason' => $reason,
        ];
    }

    private static function findCarrierByPrefix(string $digits): ?string
    {
        $map = self::loadCarrierMap();

        if ($map === []) {
            return null;
        }

        // 🔴 PRIMEIRO verifica correções manuais (elas tem prioridade MÁXIMA)
        $fixedPrefixes = [
            '55519996' => 'TIM', // Correção RS 51 prefixo 9996
            '55519997' => 'TIM',
            '55519998' => 'TIM',
            '55519993' => 'Vivo', // Mantém o que funciona
        ];

        foreach ($fixedPrefixes as $prefix => $carrier) {
            if (str_starts_with($digits, (string) $prefix)) {
                return $carrier;
            }
        }

        // Verifica do MAIOR para o MENOR prefixo (maior especificidade PRIMEIRO)
        $maxLen = min(9, strlen($digits));
        for ($len = $maxLen; $len >= 4; $len--) {
            $prefix = substr($digits, 0, $len);
            if (isset($map[$prefix])) {
                return $map[$prefix];
            }
        }

        return null;
    }

    private static function loadCarrierMap(): array
    {
        if (self::$carrierMap !== null) {
            return self::$carrierMap;
        }

        $carrierFile = __DIR__ . '/../carrier.txt';

        if (!file_exists($carrierFile)) {
            self::$carrierMap = [];

            return self::$carrierMap;
        }

        $map = [];
        $handle = fopen($carrierFile, 'r');

        if ($handle === false) {
            self::$carrierMap = [];

            return self::$carrierMap;
        }

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if ($line === '' || $line[0] === '#') {
                continue;
            }

            $parts = explode('|', $line);
            if (count($parts) !== 2) {
                continue;
            }

            $prefix = trim($parts[0]);
            $carrier = trim($parts[1]);

            if ($prefix === '' || $carrier === '') {
                continue;
            }

            $map[$prefix] = $carrier;
        }

        fclose($handle);

        self::$carrierMap = $map;

        return self::$carrierMap;
    }

    public static function getStateFromDDD(string $ddd): ?string
    {
        $dddStateMap = [
            '11' => 'SP', '12' => 'SP', '13' => 'SP', '14' => 'SP', '15' => 'SP',
            '16' => 'SP', '17' => 'SP', '18' => 'SP', '19' => 'SP',
            '21' => 'RJ', '22' => 'RJ', '24' => 'RJ',
            '27' => 'ES', '28' => 'ES',
            '31' => 'MG', '32' => 'MG', '33' => 'MG', '34' => 'MG', '35' => 'MG',
            '37' => 'MG', '38' => 'MG', '39' => 'MG',
            '41' => 'PR', '42' => 'PR', '43' => 'PR', '44' => 'PR', '45' => 'PR',
            '46' => 'PR',
            '47' => 'SC', '48' => 'SC', '49' => 'SC',
            '51' => 'RS', '53' => 'RS', '54' => 'RS', '55' => 'RS',
            '61' => 'DF', '62' => 'GO', '63' => 'GO', '64' => 'GO',
            '65' => 'MT', '66' => 'MT', '67' => 'MS',
            '68' => 'AC', '69' => 'RO',
            '71' => 'BA', '73' => 'BA', '74' => 'BA', '75' => 'BA', '77' => 'BA',
            '79' => 'PB',
            '81' => 'PE', '82' => 'AL', '83' => 'PB', '84' => 'RN', '85' => 'CE',
            '86' => 'PI', '87' => 'PE', '88' => 'CE', '89' => 'PI',
            '91' => 'PA', '92' => 'AM', '93' => 'AM', '94' => 'PA', '95' => 'AP',
            '96' => 'PA', '97' => 'AM', '98' => 'MA', '99' => 'MA',
        ];

        return $dddStateMap[$ddd] ?? null;
    }

    public static function getStateNameFromDDD(string $ddd): ?string
    {
        $dddStateNameMap = [
            '11' => 'São Paulo', '12' => 'São Paulo', '13' => 'São Paulo',
            '14' => 'São Paulo', '15' => 'São Paulo', '16' => 'São Paulo',
            '17' => 'São Paulo', '18' => 'São Paulo', '19' => 'São Paulo',
            '21' => 'Rio de Janeiro', '22' => 'Rio de Janeiro', '24' => 'Rio de Janeiro',
            '27' => 'Espírito Santo', '28' => 'Espírito Santo',
            '31' => 'Minas Gerais', '32' => 'Minas Gerais', '33' => 'Minas Gerais',
            '34' => 'Minas Gerais', '35' => 'Minas Gerais', '37' => 'Minas Gerais',
            '38' => 'Minas Gerais', '39' => 'Minas Gerais',
            '41' => 'Paraná', '42' => 'Paraná', '43' => 'Paraná', '44' => 'Paraná',
            '45' => 'Paraná', '46' => 'Paraná',
            '47' => 'Santa Catarina', '48' => 'Santa Catarina', '49' => 'Santa Catarina',
            '51' => 'Rio Grande do Sul', '53' => 'Rio Grande do Sul',
            '54' => 'Rio Grande do Sul', '55' => 'Rio Grande do Sul',
            '61' => 'Distrito Federal', '62' => 'Goiás', '63' => 'Goiás',
            '64' => 'Goiás',
            '65' => 'Mato Grosso', '66' => 'Mato Grosso', '67' => 'Mato Grosso do Sul',
            '68' => 'Acre', '69' => 'Rondônia',
            '71' => 'Bahia', '73' => 'Bahia', '74' => 'Bahia', '75' => 'Bahia',
            '77' => 'Bahia',
            '79' => 'Pernambuco',
            '81' => 'Pernambuco', '82' => 'Alagoas', '83' => 'Paraíba',
            '84' => 'Rio Grande do Norte', '85' => 'Ceará', '86' => 'Piauí',
            '87' => 'Pernambuco', '88' => 'Ceará', '89' => 'Piauí',
            '91' => 'Pará', '92' => 'Amazonas', '93' => 'Amazonas', '94' => 'Pará',
            '95' => 'Amapá', '96' => 'Pará', '97' => 'Amazonas', '98' => 'Maranhão',
            '99' => 'Maranhão',
        ];

        return $dddStateNameMap[$ddd] ?? null;
    }
}