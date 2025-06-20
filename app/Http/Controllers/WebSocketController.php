<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketController implements MessageComponentInterface
{
    //
    protected $clients;
    private $subscriptions;
    private $users;
    private $userresources;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
        $this->userresources = [];
    }

    /**
     * [onOpen description]
     * @method onOpen
     * @param  ConnectionInterface $conn [description]
     * @return [JSON]                    [description]
     * @example connection               var conn = new WebSocket('ws://localhost:8090');
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
    }

    /**
     * [onMessage description]
     * @method onMessage
     * @param  ConnectionInterface $conn [description]
     * @param  [JSON.stringify]              $msg  [description]
     * @return [JSON]                    [description]
     * @example subscribe                conn.send(JSON.stringify({command: "subscribe", channel: "global"}));
     * @example groupchat                conn.send(JSON.stringify({command: "groupchat", message: "hello glob", channel: "global"}));
     * @example message                  conn.send(JSON.stringify({command: "message", to: "1", from: "9", message: "it needs xss protection"}));
     * @example register                 conn.send(JSON.stringify({command: "register", userId: 9}));
     */
    public function onMessage(ConnectionInterface $conn, $msg)
    {
        echo $msg;
        $data = json_decode($msg);
        if (isset($data->command)) {
            switch ($data->command) {
                case "subscribe":
                    $this->subscriptions[$conn->resourceId] = $data->channel;
                break;
                case "groupchat":
                    //
                    // $conn->send(json_encode($this->subscriptions));
                    if (isset($this->subscriptions[$conn->resourceId])) {
                        $target = $this->subscriptions[$conn->resourceId];
                        foreach ($this->subscriptions as $id=>$channel) {
                            if ($channel == $target && $id != $conn->resourceId) {
                                $this->users[$id]->send($data->message);
                            }
                        }
                    }
                break;
                case "message":
                    //
                    if ( isset($this->userresources[$data->to]) ) {
                        foreach ($this->userresources[$data->to] as $key => $resourceId) {
                            if ( isset($this->users[$resourceId]) ) {
                                $this->users[$resourceId]->send($msg);
                            }
                        }
                        $conn->send(json_encode($this->userresources[$data->to]));
                    }

                    if (isset($this->userresources[$data->from])) {
                        foreach ($this->userresources[$data->from] as $key => $resourceId) {
                            if ( isset($this->users[$resourceId])  && $conn->resourceId != $resourceId ) {
                                $this->users[$resourceId]->send($msg);
                            }
                        }
                    }
                break;
                case "register":
                    //
                    if (isset($data->userId)) {
                        if (isset($this->userresources[$data->userId])) {
                            if (!in_array($conn->resourceId, $this->userresources[$data->userId]))
                            {
                                $this->userresources[$data->userId][] = $conn->resourceId;
                            }
                        }else{
                            $this->userresources[$data->userId] = [];
                            $this->userresources[$data->userId][] = $conn->resourceId;
                        }
                    }
                    $conn->send(json_encode($this->users));
                    $conn->send(json_encode($this->userresources));
                break;
                default:
                    $example = array(
                        'methods' => [
                                    "subscribe" => '{command: "subscribe", channel: "global"}',
                                    "groupchat" => '{command: "groupchat", message: "hello glob", channel: "global"}',
                                    "message" => '{command: "message", to: "1", message: "it needs xss protection"}',
                                    "register" => '{command: "register", userId: 9}',
                                ],
                    );
                    $conn->send(json_encode($example));
                break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);

        foreach ($this->userresources as &$userId) {
            foreach ($userId as $key => $resourceId) {
                if ($resourceId==$conn->resourceId) {
                    unset( $userId[ $key ] );
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
