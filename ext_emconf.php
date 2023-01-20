<?php

$EM_CONF[$_EXTKEY] = [
  'title' => 'Content Information',
  'description' => 'This extension provides useful information based on the content in your TYPO3 CMS installation.',
  'category' => 'plugin',
  'author' => 'XIMA MEDIA GmbH',
  'author_email' => 'kontakt@xima.de',
  'author_company' => 'XIMA MEDIA GmbH',
  'state' => 'beta',
  'version' => '0.1.2',
  'constraints' => [
    'depends' => [
      'typo3' => '11.5.0-11.99.*',
    ],
    'conflicts' => [
    ],
    'suggests' => [
    ],
  ],
  'autoload' => [
    'psr-4' => [
      'Xima\\ContentInformation\\' => 'Classes',
    ],
  ],
];
