<?php
    use Ratchet\Server\IoServer;

    require 'test.php';
    require '../ratchet/vendor/autoload.php';

    $server = IoServer::factory(
        new Chat(),
        8080
    );

    print('HEY');
    $server->run();
    print('HEY3');
?>