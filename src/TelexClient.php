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

    public function sendEvent(string $eventName, array $customer, array $placeholderData, array $tagData=[], string $receiverEmail=null, array $metadataOptions=[])
    {
        $url = "{$this->telexDomain}/api/events";
        $data = [
            'customer' => $customer,
            'metadata' => [
                'placeholders' => $placeholderData
            ],
            'event_type' => $eventName,
            'tag_data' => $tagData,
        ];

        if (!empty($receiverEmail)) {
            $data['metadata']['receiver_email'] = $receiverEmail;
        }

        $data['metadata'] = array_merge($data['metadata'], $metadataOptions);

        $payload = [
            'json' => $data
        ];

        return $this->client->request('POST', $url, $payload);
    }

}
