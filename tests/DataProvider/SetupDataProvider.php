<?php 
namespace Tests\DataProvider;

class SetupDataProvider {
    
    public static function getConfig($version = ''): array
    {
        // $url = 'https://develop.opencast.org';
        // $username = 'admin';
        // $password = 'opencast';
        // $timeout = 3;
        // $connectTimeout = 3;
        $url = 'https://moodle-opencast.opencast-niedersachsen.de/';
        $username = 'moodle';
        $password = '6dwt5qbuXEBkvsu';
        $timeout = 3;
        $connectTimeout = 3;
        $config =  [
            'url' => $url,
            'username' => $username,
            'password' => $password,
            'timeout' => $timeout,
            'version' => '1.6.0',
            'connect_timeout' => $connectTimeout
        ];
        if (!empty($version)) {
            $config['version'] = $version;
        }
        return $config;
    }
}
?>