<?php

namespace Telex;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;


class TelexClient
{
    
    protected $client;
    protected $telexDomain;
    protected $organizationKey;

    public function __construct($organizationKey, $telexDomain='https://telex.im')
    {
        $this->telexDomain = $telexDomain;
        $this->organizationKey = $organizationKey;
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'ORGANIZATION-KEY' => $this->organizationKey
            ]
        ]);
    }

    public function sendEvent(string $eventName, array $customer, array $placeholderData, array $tagData=[], string $receiverEmail=null)
    {
        $url = "{$this->telexDomain}/api/events";
        $payload = [
            'json' => [
                'customer' => $customer,
                'metadata' => [
                    'placeholders' => $placeholderData
                ],
                'event_type' => $eventName,
                'tag_data' => $tagData,
            ]
        ];

        if (!empty($receiverEmail)) {
            $payload['json']['metadata']['receiver_email'] = $receiverEmail;
        }

        return $this->client->request('POST', $url, $payload);
    }

}
