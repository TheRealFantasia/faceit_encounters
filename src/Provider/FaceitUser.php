<?php

namespace App\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class FaceitUser implements ResourceOwnerInterface
{
    private $data = [];

    public function __construct(array $response)
    {
        $this->data = $response;
    }

    public function getId()
    {
        return $this->get('guid');
    }

    public function toArray()
    {
        return $this->data;
    }

    private function get(string $property)
    {
        return isset($this->data[$property]) ? $this->data[$property] : '';
    }

    public function getPicture(): string
    {
        return $this->get('picture');
    }

    public function getNickname()
    {
        return $this->get('nickname');
    }

    public function getLocale()
    {
        return $this->get('locale');
    }

    public function getGuid()
    {
        return $this->get('guid');
    }
}
