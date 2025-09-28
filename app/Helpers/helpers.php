<?php

use App\Models\UserVerifyCode;

function generateUserVerifyCode(): String
{
    $is_unique = false;
    $code = null;
    while ($is_unique == false) {
        $code = rand(1000, 9999);
        if (!UserVerifyCode::query()->where('code', $code)->exists()) {
            $is_unique = true;
        }
    }
    return $code;
}

function normalizePhone($phone): String
{
    $phone = preg_replace('/[^+0-9]/', '', $phone);

    if (str_starts_with($phone, '+')) {
        $phone = substr($phone, 1);
    }

    if (str_starts_with($phone, '8')) {
        $phone = '+7' . substr($phone, 1);
    } elseif (str_starts_with($phone, '7')) {
        $phone = '+7' . substr($phone, 1);
    }

    return $phone;
}

function transliterate($text): string
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'yo',  'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'j',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'ts',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',    'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'Yo',  'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'J',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'Ts',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );

    $transliterated = strtr($text, $converter);
    if (preg_match('/\s/', $transliterated)) {
        $transliterated = preg_replace('/\s+/', '-', $transliterated);
    }

    return mb_strtolower($transliterated);
}
