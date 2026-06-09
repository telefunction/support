<?php

namespace Telefunction\Support\Traits\Network;

trait InteractsWithIpRanges
{
    protected const array LOCAL_IP_RANGES = [
        '127.0.0.0/8',
        '10.0.0.0/8',
        '172.16.0.0/12',
        '192.168.0.0/16',
        '::1/128',
        'fc00::/7',
        'fe80::/10',
    ];

    final protected function checkIpRange(
        string $ip,
        array $ranges,
        bool $includeLocalRanges = false
    ): bool {
        if (! $this->isValidIp($ip)) {
            return false;
        }

        foreach ($this->ipRanges($ranges, $includeLocalRanges) as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    final protected function ipRanges(
        array $ranges = [],
        bool $includeLocalRanges = false
    ): array {
        return $includeLocalRanges
            ? array_values(array_unique([...$ranges, ...self::LOCAL_IP_RANGES]))
            : $ranges;
    }

    final protected function ipInRange(string $ip, string $range): bool
    {
        if (! str_contains($range, '/')) {
            return $ip === $range;
        }

        [$subnet, $prefix] = explode('/', $range, 2);

        if (! is_numeric($prefix)) {
            return false;
        }

        $prefix = (int) $prefix;

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $this->ipInRangeV4($ip, $subnet, $prefix);
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
            && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $this->ipInRangeV6($ip, $subnet, $prefix);
        }

        return false;
    }

    final protected function ipInRangeV4(
        string $ip,
        string $subnet,
        int $prefix
    ): bool {
        if ($prefix < 0 || $prefix > 32) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $ipLong = sprintf('%u', $ipLong);
        $subnetLong = sprintf('%u', $subnetLong);

        $mask = $prefix === 0
            ? 0
            : ((-1 << (32 - $prefix)) & 0xFFFFFFFF);

        return (((int) $ipLong & $mask) === ((int) $subnetLong & $mask));
    }

    final protected function ipInRangeV6(
        string $ip,
        string $subnet,
        int $prefix
    ): bool {
        if ($prefix < 0 || $prefix > 128) {
            return false;
        }

        $ipBinary = inet_pton($ip);
        $subnetBinary = inet_pton($subnet);

        if ($ipBinary === false || $subnetBinary === false) {
            return false;
        }

        $bytes = intdiv($prefix, 8);
        $bits = $prefix % 8;

        if ($bytes > 0 && substr($ipBinary, 0, $bytes) !== substr($subnetBinary, 0, $bytes)) {
            return false;
        }

        if ($bits === 0) {
            return true;
        }

        $mask = ~((1 << (8 - $bits)) - 1) & 0xFF;

        return (ord($ipBinary[$bytes]) & $mask) === (ord($subnetBinary[$bytes]) & $mask);
    }

    final protected function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
}
