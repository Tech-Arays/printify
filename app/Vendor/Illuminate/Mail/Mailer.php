<?php

namespace App\Vendor\Illuminate\Mail;

class Mailer extends \Illuminate\Mail\Mailer
{
    /**
     * Add the content to a given message.
     *
     * @param  \Illuminate\Mail\Message  $message
     * @param  string  $view
     * @param  string  $plain
     * @param  string  $raw
     * @param  array  $data
     * @return void
     */
    protected function addContent($message, $view, $plain, $raw, $data)
    {
        $data = \App\Components\Mailer::attachBeautymailSettings($data);
        
        if (isset($view)) {
            $message->setBody($this->getView($view, $data), 'text/html');
        }

        if (isset($plain)) {
            $method = isset($view) ? 'addPart' : 'setBody';

            $message->$method($this->getView($plain, $data), 'text/plain');
        }

        if (isset($raw)) {
            $method = (isset($view) || isset($plain)) ? 'addPart' : 'setBody';

            $message->$method($raw, 'text/plain');
        }
    }
}
