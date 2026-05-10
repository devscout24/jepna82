<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotDisposable implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = substr(strrchr($value, "@"), 1);

        // 1. DNS check (MX record)
        if (!checkdnsrr($domain, "MX")) {
            $fail('The email domain is invalid or does not exist.');
            return;
        }

        // 2. Block temp mail domains (Comprehensive List)
        $blockedDomains = [
            'tempmail.com', 'mailinator.com', 'guerrillamail.com', '10minutemail.com',
            'temp-mail.org', 'yopmail.com', 'maildrop.cc', 'dispostable.com',
            'getnada.com', 'maildrop.io', 'protonmail.ch', 'sharklasers.com',
            'guerillamail.info', 'guerillamail.biz', 'guerillamail.com',
            'guerillamail.de', 'guerillamail.net', 'guerillamail.org',
            'guerillamailblock.com', 'pokemail.net', 'spam4.me', 'grr.la',
            'teleworm.us', 'dayrep.com', 'fleckens.hu', 'rhyta.com',
            'superrito.com', 'armyspy.com', 'cuvox.de', 'einrot.com',
            'gustr.com', 'jourrapide.com', 'mailbox.org', 'trashmail.com',
            'burnerex.com', 'temp-mail.io', 'mail-temp.com', 'disposable.com',
            'mail.com', 'email.com', 'mail.ru'
        ];

        if (in_array($domain, $blockedDomains)) {
            $fail('Temporary or disposable emails are not allowed.');
        }
    }
}
