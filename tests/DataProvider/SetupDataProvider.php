<?php 
namespace Tests\DataProvider;

class SetupDataProvider {
    
    public static function getConfig($version = ''): array
    {
        $url = 'https://develop.opencast.org';
        $username = 'admin';
        $password = 'opencast';
        $timeout = 3000;
        $config =  [
            'url' => $url,
            'withauth' => true,
            'username' => $username,
            'password' => $password,
            'timeout' => $timeout,
            'version' => '1.6.0'
        ];
        if (!empty($version)) {
            $config['version'] = $version;
        }
        return $config;
    }
}
?>