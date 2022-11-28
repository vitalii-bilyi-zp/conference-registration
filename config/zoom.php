<?php

return [
    'api_url' => env('ZOOM_API_URL', 'https://api.zoom.us/v2'),
    'oauth_url' => env('ZOOM_OAUTH_URL', 'https://zoom.us/oauth/token'),
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
];
