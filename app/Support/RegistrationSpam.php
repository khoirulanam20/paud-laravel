<?php

namespace App\Support;

use Closure;

class RegistrationSpam
{
    /**
     * Domain email sekali-pakai yang dipakai bot pendaftaran.
     * ponytail: daftar statis. Domain baru cukup ditambah di sini.
     * Bot keluarga yang sama juga ketahuan dari karakter ~ di local-part.
     *
     * @var list<string>
     */
    private const DISPOSABLE_DOMAINS = [
        'merepost.com',
        'fextemp.com',
        'mailinator.com',
        'guerrillamail.com',
        'guerrillamail.info',
        'guerrillamail.biz',
        'guerrillamail.de',
        'guerrillamail.net',
        'guerrillamail.org',
        'guerrillamailblock.com',
        'grr.la',
        'sharklasers.com',
        'pokemail.net',
        'spam4.me',
        '10minutemail.com',
        '10minutemail.net',
        'tempmail.com',
        'temp-mail.org',
        'tempmailo.com',
        'tempinbox.com',
        'yopmail.com',
        'yopmail.fr',
        'yopmail.net',
        'trashmail.com',
        'trashmail.de',
        'trash-mail.com',
        'dispostable.com',
        'maildrop.cc',
        'getnada.com',
        'inboxkitten.com',
        'mohmal.com',
        'throwawaymail.com',
        'throwaway.email',
        'fakeinbox.com',
        'mailnesia.com',
        'mintemail.com',
        'emailondeck.com',
        'moakt.com',
        'dropmail.me',
        'bccto.me',
        'chacuo.net',
        'tempm.com',
        'tmpmail.net',
        'tmpmail.org',
        'discard.email',
        'harakirimail.com',
        'burnermail.io',
        'spamgourmet.com',
        'mytemp.email',
        'emailfake.com',
        'generator.email',
        'fakemail.net',
        'mailcatch.com',
        'getairmail.com',
        'tempail.com',
    ];

    public static function textRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (self::textIsSpam(is_string($value) ? $value : null)) {
                $fail('Isi tidak boleh berisi tautan, kode, atau promosi.');
            }
        };
    }

    public static function emailRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (self::emailIsBlocked(is_string($value) ? $value : null)) {
                $fail('Gunakan email aktif, bukan email sementara.');
            }
        };
    }

    public static function textIsSpam(?string $value): bool
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        if (preg_match('/https?:\/\/|www\.|<\s*\/?\s*[a-z]|javascript\s*:/i', $value)) {
            return true;
        }

        if (preg_match('/\b(scam|viagra|casino|crypto|bitcoin|forex|injection)\b|union\s+select|onerror\s*=|onload\s*=|\bhs=[a-f0-9]{8,}/i', $value)) {
            return true;
        }

        $symbols = preg_match_all('/[^\p{L}\p{N}\s.,\'\-\/&()]/u', $value);
        $length = mb_strlen($value);

        return $length >= 8 && ($symbols / $length) > 0.25;
    }

    public static function emailIsBlocked(?string $email): bool
    {
        $email = strtolower(trim((string) $email));
        if (! str_contains($email, '@')) {
            return false;
        }

        [$local, $domain] = explode('@', $email, 2);

        if (str_contains($local, '~')) {
            return true;
        }

        return in_array($domain, self::DISPOSABLE_DOMAINS, true);
    }
}
