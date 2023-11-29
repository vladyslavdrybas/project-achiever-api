<?php

declare(strict_types=1);

namespace App\Constants;

interface RouteConstants
{
    public const LOGIN_ROUTE = 'api_auth_login';
    public const HOMEPAGE_ROUTE = 'app_homepage';

    public const PARAM_TIMESTAMP_DEFAULT = 2019686400; // year 2034

    public const REQUIREMENT_LIST_LIMIT = '1|2|3|4|5|7|11|17|10|20|50';
}
