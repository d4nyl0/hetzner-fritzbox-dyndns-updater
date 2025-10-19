<?php
// File not accessibile from web with 600 permission
$apitoken = 'hetzner-dns-api-token';

// Array zone → rrset
$zones = [
    'domain1.it' => ['*/A', '@/A'],
    'domain2.it'   => ['@/A']
];

// Time Zone for comments
$timezone = 'Europe/Rome';
