<?php

namespace App\Support;

class AiPersonaScope
{
    public const CHAT_ORANGTUA = 'chat_orangtua';

    public const MONEV = 'monev';

    public const FEEDBACK_PENCAPAIAN = 'feedback_pencapaian';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::CHAT_ORANGTUA,
            self::MONEV,
            self::FEEDBACK_PENCAPAIAN,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::CHAT_ORANGTUA => 'Chat Orang Tua',
            self::MONEV => 'Monev',
            self::FEEDBACK_PENCAPAIAN => 'Feedback Pencapaian',
        ];
    }

    public static function label(string $scope): string
    {
        return self::labels()[$scope] ?? $scope;
    }

    public static function defaultName(string $scope): string
    {
        return match ($scope) {
            self::MONEV => 'Guru Monev PAUD',
            self::FEEDBACK_PENCAPAIAN => 'Guru PAUD',
            default => 'Asisten PAUD',
        };
    }

    public static function defaultRoleTitle(string $scope): string
    {
        return match ($scope) {
            self::MONEV => 'Guru PAUD penulis ringkasan monev',
            self::FEEDBACK_PENCAPAIAN => 'Guru PAUD penulis umpan balik pencapaian',
            default => 'Asisten Chat Orang Tua PAUD',
        };
    }

    public static function generateContext(string $scope): string
    {
        return match ($scope) {
            self::MONEV => 'Persona untuk AI yang menulis ringkasan monitoring & evaluasi (monev) perkembangan siswa PAUD per bulan.',
            self::FEEDBACK_PENCAPAIAN => 'Persona untuk AI yang memberikan saran umpan balik pencapaian siswa PAUD untuk dicatat guru.',
            default => 'Persona untuk AI chat yang membantu orang tua memahami perkembangan anak di PAUD/daycare.',
        };
    }
}
