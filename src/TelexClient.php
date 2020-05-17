<?php

namespace Telex;

use GuzzleHttp\Client;

class TelexClient
{
    
    protected $client;
    protected $telexDomain;
    protected $organizationKey;

    public function __construct($organizationKey, $telexDomain='https://messaging.telex.im')
    {
        $this->telexDomain = $telexDomain;
        $this->organizationKey = $organizationKey;
        $this->client = new Client([
            'headers' => [
                'ORGANIZATION-KEY' => $this->organizationKey
            ]
        ]);
    }

    private function parseArrayItem(Array $jsonInputArray, String $arrayLabel='') 
    {
        $multipartArray = [];

        foreach ($jsonInputArray as $key => $value) {
            $multipartKey = "[${key}]";
            if ($arrayLabel != '') {
                $multipartKey = "${arrayLabel}${multipartKey}";
            }

            if (gettype($value) === 'array') {
                $nestedItems = self::parseArrayItem($value, $multipartKey);
                $multipartArray = array_merge($multipartArray, $nestedItems);
            } else {
                $multipartArray[$multipartKey] = $value;
            }
   
        }

        return $multipartArray;
    }

    public function sendEvent(string $eventName, array $customer, array $placeholderData, array $options=[])
    {
        $url = "{$this->telexDomain}/api/events";

        $receiverEmail = $options['receiver_email'] ?? '';
        $tagData = $options['tag_data'] ?? [];
        $metadata = $options['metadata'] ?? [];
        $attachments = $options['attachments'] ?? null;
        $organizationKey = $options['organization_key'] ?? '';

        $data = [
            'customer' => $customer,
            'metadata' => [
                'placeholders' => $placeholderData
            ],
            'attachments' => $attachments,
            'event_type' => $eventName,
            'tag_data' => $tagData,
        ];

        if (!empty($receiverEmail)) {
            $data['metadata']['receiver_email'] = $receiverEmail;
        }

        $data['metadata'] = array_merge($data['metadata'], $metadata);

        $requestType = 'json';

        if (isset($attachments)) {
            $requestType = 'multipart';
            $requestData = [];

            foreach ($data as $key => $value) {
                if (gettype($value) != 'array') {
                    $multipartArray = [
                        'name' => $key,
                        'contents' => $value
                    ];

                    if ($key === 'attachments') {
                        $filename = stream_get_meta_data($value)['uri'];
                        $multipartArray['filename'] = $filename;
                    }

                    array_push($requestData, $multipartArray);
                } else {
                    
                    $multipartArray = self::parseArrayItem($value, $key);
                    foreach ($multipartArray as $key => $value) {
                        $data  = [
                            'name' => $key,
                            'contents' => $value
                        ];

                        array_push($requestData, $data);
                    }
                    
                }

            }
            
            $data = $requestData;
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
