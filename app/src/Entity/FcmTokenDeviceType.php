<?php

namespace App\Entity;

enum FcmTokenDeviceType: string
{
    case UNKNOWN = 'unknown';
    case WEB = 'web';
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
}
