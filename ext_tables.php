<?php

defined('TYPO3') or die();

(function () {
  \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    \Xima\ContentInformation\Configuration::EXT_KEY,
    'web',
    'index',
    '',
    [
      \Xima\ContentInformation\Controller\IndexController::class => 'index',
    ],
    [
      'access' => 'user,group',
      'icon'   => 'EXT:' . \Xima\ContentInformation\Configuration::EXT_KEY . '/Resources/Public/Icons/Extension.png',
      'labels' => 'LLL:EXT:' . \Xima\ContentInformation\Configuration::EXT_KEY . '/Resources/Private/Language/locallang_mod.xlf',
      'navigationComponentId' => '',
      'inheritNavigationComponentFromMainModule' => false
    ]
  );
})();
