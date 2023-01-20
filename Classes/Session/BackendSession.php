<?php

declare(strict_types=1);

namespace Xima\ContentInformation\Session;

class BackendSession
{
  private $storageKey = null;

  public function __construct($storageKey)
  {
    if (!is_string($storageKey)) {
      throw new \Exception('The given Session $storageKey is not a string!');
    }

    $this->storageKey = $storageKey;
  }

  public function set($key, $value)
  {
    $session       = $this->loadSessionData();
    $session[$key] = $value;
    $GLOBALS['BE_USER']->setAndSaveSessionData($this->storageKey, $session);
  }

  public function get($key)
  {
    $session = $this->loadSessionData();

    return (isset($session[$key])) ? $session[$key] : null;
  }

  public function has($key)
  {
    $session = $this->loadSessionData();

    return (isset($session[$key])) ? true : false;
  }

  public function remove($key)
  {
    $session = $this->loadSessionData();
    unset($session[$key]);
    $GLOBALS['BE_USER']->setAndSaveSessionData($this->storageKey, $session);
  }

  private function loadSessionData()
  {
    if ($GLOBALS['BE_USER']->getSessionData($this->storageKey)) {
      $session = $GLOBALS['BE_USER']->getSessionData($this->storageKey);
    } else {
      $session = [];
    }

    return $session;
  }
}
