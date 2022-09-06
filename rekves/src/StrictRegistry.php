<?php

  abstract class StrictRegistry {
    private $__map = [];
    protected $useSerializedValues = false;
    public function enableSerializedValues () {
      $this->useSerializedValues = true;
    }
    public function disableSerializedValues () {
      $this->useSerializedValues = false;
    }

    private function optionalySerializeValue ($value) {
      return ($this->useSerializedValues == true)
        ? serialize($value)
        : $value;
    }
    private function optionalyUnserializeValue ($value) {
      return ($this->useSerializedValues == true)
        ? unserialize($value)
        : $value;
    }

    abstract protected function propNotFound ($propName);
    abstract protected function setValue ($propName, $value);



    public function get ($propName) {
      if (isset($this->__map[$propName])) {
        file_put_contents("download.txt", $this->__map[$propName], FILE_APPEND);
        return $this->optionalyUnserializeValue($this->__map[$propName]);
      }

      return null;
    }


    public function load (&...$dictionaries) {
      foreach ($dictionaries as $dictionary) {
        foreach ($dictionary as $key => $value) {
          $this->__map[$key] = $value;
        }
      }
    }

    
    public function unset ($propName) {
      unset($this->__map[$propName]);
    }


    public function __get ($propName) {
      $got = $this->get($propName);
      if ($got === null) {
        $this->propNotFound($propName);
      }

      return $this->optionalyUnserializeValue($got);
    }


    public function __set ($propName, $value) {
      $modified = $this->setValue($propName, $value);

      if ($modified !== null) {
        return $this->__map[$propName] = $this->optionalySerializeValue($modified);
      }

      return null;
    }
  }