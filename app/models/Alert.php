<?php


namespace Model;


class Alert {
    const ALERT_TYPES = ['info', 'warning'];
    const ALERT_CACHE_KEY = 'app_alert';
    const ALERT_MINIMUM_TTL = 86400; // 24 hours
}