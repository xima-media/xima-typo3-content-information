<?php

namespace Xima\ContentInformation\Entity;

class Filter
{
  protected string $extensionKey = '';
  protected string $element      = '';
  protected bool   $deleted      = true;

  public function getExtensionKey(): string
  {
    return $this->extensionKey;
  }

  public function setExtensionKey(string $extensionKey): void
  {
    $this->extensionKey = $extensionKey;
  }

  public function getDeleted(): bool
  {
    return $this->deleted;
  }

  public function setDeleted(bool $deleted): void
  {
    $this->deleted = $deleted;
  }

  public function setData(array $data)
  {
    foreach ($data as $key => $value) {
      $this->{$key} = $value;
    }
  }

  public function getData()
  {
    return [
      'extensionKey' => $this->extensionKey,
      'element' => $this->element,
      'deleted'      => $this->deleted,
    ];
  }

  public function getElement(): string
  {
    return $this->element;
  }

  public function setElement(string $element): void
  {
    $this->element = $element;
  }
}
