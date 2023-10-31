<?php

namespace App\Entity;

enum FcmTokenDeviceType: string
{
    case UNKNOWN = 'unknown';
    case WEB = 'web';
    case WEB_EXTENSION = 'web_ext';
    case POSTMAN = 'postman';
//    case telegram;
//    case android;
//    case ios;
//    case slack;
//    case linkedin;
//    case sms;
//    case phone;
//    case facebook;
//    case whatsup;
//    case viber;

    public static function getOrDefault(string $value): FcmTokenDeviceType
    {
        $value = self::tryFrom($value);
        if (null === $value) {
            $value = self::UNKNOWN;
        }

        return $value;
    }
}
