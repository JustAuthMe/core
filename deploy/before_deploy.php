<?php

$env_vars = [
    'PROD_ENV' => 'bool',
    'PROD_HOST' => 'string',
    'LOGGING' => 'bool',
    'NAME' => 'string',
    'CLI_BASE_URL' => 'string',

    'DATA_TRANSFERT_KEY' => 'string',
    'JAM_INTERNAL_API_KEY' => 'string',
    'EMAIL_RENDERING_KEY' => 'string',

    'DB_MASTER_HOST' => 'string',
    'DB_MASTER_NAME' => 'string',
    'DB_MASTER_USER' => 'string',
    'DB_MASTER_PASS' => 'string',

    'DB_SLAVE_HOST' => 'string',
    'DB_SLAVE_NAME' => 'string',
    'DB_SLAVE_USER' => 'string',
    'DB_SLAVE_PASS' => 'string',

    'REDIS_HOST' => 'string',
    'REDIS_PORT' => 'int',
    'REDIS_PASS' => 'string',


    'WEBSOCKET_PROTOCOL' => 'string',
    'WEBSOCKET_REMOTE_HOST' => 'string',
    'WEBSOCKET_LOCAL_HOST' => 'string',
    'WEBSOCKET_PORT' => 'int',
    'WEBSOCKET_PATH' => 'string',

    'SMTP_HOST' => 'string',
    'SMTP_PORT' => 'int',
    'SMTP_USER' => 'string',
    'SMTP_PASS' => 'string',

    'OPENID_SERVER' => 'string',
    'OPENID_SIGN_KEY' => 'string',

    'UNSUBSCRIBE_SALT' => 'string',

    'ENABLE_APPLE_DEMO_ACCOUNT' => 'bool',
    'APPLE_DEMO_EMAIL'  => 'string',

    'DEPLOYED_REF' => 'CI_COMMIT_REF_NAME',
    'DEPLOYED_COMMIT' => 'CI_COMMIT_SHA',
    'ENV_NAME' => 'ENV_NAME'
];

$env_prefix = getenv('ENV_PREFIX');
$config_output = "<?php \n";
foreach ($env_vars as $KEY => $type){
    switch($type){
        case 'bool':
            $config_output .= "const $KEY = " . (getenv($env_prefix.$KEY) ? 'true':'false'). ";\n";
            break;
        case 'string':
            $config_output .= "const $KEY = '" . addcslashes(getenv($env_prefix.$KEY), "'"). "';\n";
            break;
        case 'int':
            $config_output .= "const $KEY = " . intval(getenv($env_prefix.$KEY)) . ";\n";
            break;
        default:
            $config_output .= "const $KEY = '" . addcslashes(getenv($type), "'") . "';\n";
            break;
    }
}

echo $config_output;
