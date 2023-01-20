<?php

namespace Xima\ContentInformation\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\ListUtility;
use Xima\ContentInformation\Entity\Filter;
use Xima\ContentInformation\Helper\ArrayHelper;

class ContentInformationUtility
{
  protected ListUtility $listUtility;
  /**
   * Array of available and installed extensions
   *
   * @var array
   */
  private static $extensionsCache = [];

  /**
   * Constructor
   */
  public function __construct(ListUtility $listUtility)
  {
    $this->listUtility = $listUtility;
  }

  /**
   * Returns an array of available and installed extensions
   *
   * @return array
   */
  public function getAllExtensions(): array
  {
    if (empty(static::$extensionsCache)) {
      static::$extensionsCache = $this->listUtility->getAvailableAndInstalledExtensionsWithAdditionalInformation();
      ksort(static::$extensionsCache);
    }

    return static::$extensionsCache;
  }

  public function getAllExtensionsAsOptions()
  {
    $result = [];

    foreach (array_keys($this->getAllExtensions()) as $extension) {
      $result[str_replace('_', '', $extension)] = $extension;
    }

    return $result;
  }

  public function getExtensionInfo(string $extKey): ?array
  {
    $extensions = $this->getAllExtensions();

    return $extensions[$extKey] ?? null;
  }

  /**
   * Returns an array of extension dependencies
   */
  public function getExtensionDependencies(string $extension): array
  {
    $dependencies = [];

    foreach ($this->getAllExtensions() as $key => $val) {
      if (isset($val['constraints']['depends'][$extension])) {
        $dependencies[$key] = $val;
      }
    }

    return $dependencies;
  }

  public function getAllPlugins(): array
  {
    if (!isset($GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'])) {
      return [];
    }

    $result = [];

    foreach ($GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'] as $item) {
      $pluginName = explode('_', $item[1]);

      if (!$pluginName[0] || !$pluginName[1]) {
        continue;
      }

      if (!isset($result[$pluginName[0]])) {
        $result[$pluginName[0]] = [];
      }

        if (!in_array($pluginName[1], $result[$pluginName[0]])) {
          $label = substr($item[0], 0, 4 ) === "LLL:" ? LocalizationUtility::translate($item[0]) : $item[0];
          $result[$pluginName[0]][] = [
            'key' => $pluginName[1],
            'label' => $label
          ];
        }
    }

    return $result;
  }

  public function getExtensionPlugins(string $extension): array
  {
    $extension = GeneralUtility::underscoredToUpperCamelCase($extension);
    $plugins   = $this->getAllPlugins();

    return $plugins[$extension] ?? [];
  }

  public function getContentElements(): array
  {
    if (!isset($GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'])) {
      return [];
    }

    $result = [];

    foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $item) {
      if (!($contentElementName = $item[1]) || $contentElementName === '--div--') {
        continue;
      }

      if (!in_array($contentElementName, $result)) {
        try {
          $result[$contentElementName] = LocalizationUtility::translate($item[0]);
        } catch (\InvalidArgumentException $e) {
          // no fully qualified translation label
          $result[$contentElementName] = $contentElementName;
        }
      }
    }

    return $result;
  }

  public function getExtensionElementsAsOptions(Filter $filter): array
  {
    $options = [];
    $filterExtension = $filter->getExtensionKey();

    // plugins
    $prefixedPlugins = [];

    foreach ($this->getAllPlugins() as $extension => $plugins) {
      foreach ($plugins as $plugin) {
        $extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($extension);

        if ($filterExtension && $filterExtension !== $extensionKey) {
          continue;
        }

        $prefixedPlugins['plugin@' . $extensionKey . '@' . $plugin['key']] = "$extensionKey: " . $plugin['label'] . " [" . $plugin['key'] . "]";
      }
    }

    asort($prefixedPlugins);

    $options['plugins'] = $prefixedPlugins;

    // content elements
    $prefixedContentElements = [];

    foreach ($this->getContentElements() as $key => $label) {
      $prefixedContentElements['cte@' . $key] = "$label [$key]";
    }

    asort($prefixedContentElements);

    $options['contentElements'] = $prefixedContentElements;

    return $options;
  }
}
