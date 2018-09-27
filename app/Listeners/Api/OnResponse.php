<?php

namespace App\Listeners\Api;

use Dingo\Api\Event\ResponseWasMorphed;

class OnResponse
{
    public function handle(ResponseWasMorphed $event)
    {
        if (!isset($event->content['isError'])) {
            if (
                isset($event->content['data'])
                || isset($event->content['message'])
            ) {
                $event->content = array_merge([
                    'status' => $event->response->getStatusCode(),
                    'isError' => false,
                    'message' => null
                ], $event->content);
            }
            else {
                $event->content = [
                    'status' => $event->response->getStatusCode(),
                    'isError' => false,
                    'message' => null,
                    'data' => $event->content
                ];
            }
        }
    }
}
