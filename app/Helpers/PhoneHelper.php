<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Нормализует номер телефона к формату 7XXXXXXXXXX
     * 
     * @param string $phone
     * @return string
     */
    public static function normalize($phone)
    {
        // Удаляем все символы кроме цифр
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Если номер начинается с 8, заменяем на 7
        if (strlen($phone) === 11 && substr($phone, 0, 1) === '8') {
            $phone = '7' . substr($phone, 1);
        }
        
        // Если номер начинается с +7, убираем +
        if (strlen($phone) === 12 && substr($phone, 0, 2) === '77') {
            $phone = substr($phone, 1);
        }
        
        // Если номер без кода страны, добавляем 7
        if (strlen($phone) === 10) {
            $phone = '7' . $phone;
        }
        
        return $phone;
    }

    /**
     * Форматирует номер телефона для отображения
     * 7XXXXXXXXXX -> +7 (XXX) XXX-XX-XX
     * 
     * @param string $phone
     * @return string
     */
    public static function format($phone)
    {
        $phone = self::normalize($phone);
        
        if (strlen($phone) === 11) {
            return sprintf(
                '+%s (%s) %s-%s-%s',
                substr($phone, 0, 1),
                substr($phone, 1, 3),
                substr($phone, 4, 3),
                substr($phone, 7, 2),
                substr($phone, 9, 2)
            );
        }
        
        return $phone;
    }

    /**
     * Проверяет валидность номера телефона
     * 
     * @param string $phone
     * @return bool
     */
    public static function isValid($phone)
    {
        $normalized = self::normalize($phone);
        return strlen($normalized) === 11 && substr($normalized, 0, 1) === '7';
    }
}
