<?php

declare(strict_types=1);

final class TimeHelper
{
    /** @return array<string, string> value => label */
    public static function slots30(): array
    {
        $out = [];
        for ($h = 6; $h <= 22; $h++) {
            foreach ([0, 30] as $m) {
                if ($h === 22 && $m > 0) {
                    break;
                }
                $v = sprintf('%02d:%02d:00', $h, $m);
                $out[$v] = sprintf('%02d:%02d', $h, $m);
            }
        }
        return $out;
    }
}
