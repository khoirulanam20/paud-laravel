<?php

namespace App\Support;

class AiTokenFeature
{
    public const MONEV = 'monev';

    public const PENCAPAIAN = 'pencapaian';

    public const CHAT = 'chat';

    public const PERSONA = 'persona';

    public static function transactionType(string $feature): string
    {
        return match ($feature) {
            self::MONEV => \App\Models\SekolahAiTokenTransaction::TYPE_MONEV,
            self::PENCAPAIAN => \App\Models\SekolahAiTokenTransaction::TYPE_PENCAPAIAN,
            self::CHAT => \App\Models\SekolahAiTokenTransaction::TYPE_CHAT,
            self::PERSONA => \App\Models\SekolahAiTokenTransaction::TYPE_PERSONA,
            default => $feature,
        };
    }

    public static function featureFromTransactionType(string $type): string
    {
        return match ($type) {
            \App\Models\SekolahAiTokenTransaction::TYPE_MONEV => self::MONEV,
            \App\Models\SekolahAiTokenTransaction::TYPE_PENCAPAIAN => self::PENCAPAIAN,
            \App\Models\SekolahAiTokenTransaction::TYPE_CHAT => self::CHAT,
            \App\Models\SekolahAiTokenTransaction::TYPE_PERSONA => self::PERSONA,
            default => self::CHAT,
        };
    }
}
