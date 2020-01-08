<?php

namespace Telex;

use GuzzleHttp\Client;

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
                'ORGANIZATION-KEY' => $this->organizationKey
            ]
        ]);
    }

    public function sendEvent(string $eventName, array $customer, array $placeholderData, array $options=[])
    {
        $url = "{$this->telexDomain}/api/events";

        $receiverEmail = $options['receiver_email'] ?? '';
        $tagData = $options['tag_data'] ?? [];
        $metadata = $options['metadata'] ?? [];
        $organizationKey = $options['organization_key'] ?? '';

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

        $data['metadata'] = array_merge($data['metadata'], $metadata);

        $requestType = 'json';

        if (isset($data['metadata']['attachments'])) {
            $requestType = 'multipart';
        }

        $payload = [
            $requestType => $data
        ];

        if (!empty($organizationKey)) {
            $client = new Client([
                'headers' => [
                    'ORGANIZATION-KEY' => $organizationKey
                ]
            ]);
            return $client->request('POST', $url, $payload);

        } else {
            return $this->client->request('POST', $url, $payload);
        }

    }

}
