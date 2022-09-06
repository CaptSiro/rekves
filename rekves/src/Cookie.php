<?php

  class Cookie {
    public $value, $expire, $path, $domain, $secure, $httponly;

    public function __construct (
      $value = "",
      int $expires = 0,
      string $path = "",
      string $domain = "",
      bool $secure = false,
      bool $httponly = false
    ) {
      $this->value = $value;
      $this->expires = $expires;
      $this->path = $path;
      $this->domain = $domain;
      $this->secure = $secure;
      $this->httponly = $httponly;
    }

    public function set ($name) {
      setcookie(
        $name,
        serialize($this->value),
        $this->expires,
        $this->path,
        $this->domain,
        $this->secure,
        $this->httponly
      );
    }
  }