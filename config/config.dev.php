<?php

const PROD_ENV = false;
const PROD_HOST = 'localhost';
const LOGGING = true;
const NAME = 'JustAuth.Me';
const CLI_BASE_URL = 'http://localhost/JustAuthMe/core/public';

const DATA_TRANSFERT_KEY = 'some random shit';
const JAM_INTERNAL_API_KEY = 'some OTHER random shit (DIFFERENT from above)';
const EMAIL_RENDERING_KEY = 'some OTHER random shit (DIFFERENT from above)';

const DB_MASTER_HOST = 'db host';
const DB_MASTER_NAME = 'jam_core';
const DB_MASTER_USER = 'db user';
const DB_MASTER_PASS = 'db pass';

const DB_SLAVE_HOST = 'db host';
const DB_SLAVE_NAME = 'jam_core';
const DB_SLAVE_USER = 'db user';
const DB_SLAVE_PASS = 'db pass';

const REDIS_HOST = '127.0.0.1';
const REDIS_PORT = 6379;
const REDIS_PASS = 'redis pass (must be loooooooooooooooong';

const WEBSOCKET_PROTOCOL = 'ws';
const WEBSOCKET_REMOTE_HOST = PROD_HOST;
const WEBSOCKET_LOCAL_HOST = 'localhost';
const WEBSOCKET_PORT = 1337;
const WEBSOCKET_PATH = '';


const SMTP_HOST = 'mail.justauth.me';
const SMTP_PORT = 587;
const SMTP_USER = 'hello@justauth.me';
const SMTP_PASS = '';

const OPENID_SERVER = 'http://localhost/JustAuthMe/openid/public/';
const OPENID_SIGN_KEY = 'xxxxx';

const UNSUBSCRIBE_SALT = 'xxxxxxx';

const ENABLE_APPLE_DEMO_ACCOUNT = true;
const APPLE_DEMO_EMAIL = 'apple-program@justauth.me';

const DEPLOYED_COMMIT = "NA";
const DEPLOYED_REF = "NA";
const ENV_NAME = "dev";
