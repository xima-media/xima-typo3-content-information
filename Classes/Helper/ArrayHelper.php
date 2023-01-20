<?php

namespace Xima\ContentInformation\Helper;

class ArrayHelper
{
  /**
   * Flattens a multi-level array for easier display
   */
  public function flattenArray(array $array): array
  {
    $result = [];

    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $result[$key] = $this->flattenArray($value);
      } else {
        $result[$key] = $value;
      }
    }

    return $result;
  }
}
