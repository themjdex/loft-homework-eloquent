<?php
namespace Base;
class RedirectException extends \Exception
{
    private $url;
    public function __construct(string $url)
    {
        $this->url = $url;
    }
    public function getUrl(): string
    {
        return $this->url;
    }
}