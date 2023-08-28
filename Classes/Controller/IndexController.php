<?php

declare(strict_types=1);

namespace Xima\ContentInformation\Controller;

use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Xima\ContentInformation\Configuration;
use Xima\ContentInformation\Domain\Repository\ContentRepository;
use Xima\ContentInformation\Entity\Filter;
use Xima\ContentInformation\Session\BackendSession;
use Xima\ContentInformation\Utility\ContentInformationUtility;

class IndexController extends ActionController
{
  protected ContentRepository         $contentRepository;
  protected ContentInformationUtility $contentInformationUtility;

  public function __construct(
    ContentRepository $contentRepository,
    ContentInformationUtility $contentInformationUtility
  ) {
    $this->contentRepository         = $contentRepository;
    $this->contentInformationUtility = $contentInformationUtility;
  }

  public function indexAction(Filter $filter = null, string $operation = '')
  {
    $filter = $this->prepareFilter($filter, $operation);

    if (null !== $filter->getExtensionKey()) {
      $entities = $this->contentRepository->findByFilter($filter);
    } else {
      $entities = null;
    }

    $this->view->assignMultiple([
      'extensions'            => $this->contentInformationUtility->getAllExtensions(),
      'extensionOptions'      => $this->contentInformationUtility->getAllExtensionsAsOptions(),
      'elements'              => $this->contentInformationUtility->getExtensionElementsAsOptions($filter),
      'extensionInfo'         => $this->contentInformationUtility->getExtensionInfo($filter->getExtensionKey()),
      'extensionDependencies' => $this->contentInformationUtility->getExtensionDependencies($filter->getExtensionKey()),
      'filter'                => $filter,
      'entities'              => $entities,
      'submitOnChangeAttr'    => [
        'onchange' => 'this.form.submit()'
      ],
    ]);
  }

  /**
   * Prepare the filter and restore it from session if applicable.
   *
   * @param Filter|null $filter
   * @param string $operation
   * @return Filter|null
   */
  protected function prepareFilter(Filter $filter = null, string $operation = '')
  {
    $this->session = new BackendSession(Configuration::EXT_KEY);

    $extensionKeyChanged = false;
    $elementChanged      = false;

    if ($operation === 'reset-filters') {
      $filter = null;
      $this->session->remove('Filter');
    }

    if (null === $filter) {
      $filter = new Filter();

      if ($this->session->has('Filter')) {
        $filterData = $this->session->get('Filter') ?: [];

        $filter->setData($filterData);
      }
    } else {
      // form has been submitted
      $filterData = $this->session->has('Filter') ? ($this->session->get('Filter') ?: []) : [];

      if ($filter->getExtensionKey() !== $filterData['extensionKey'] ?? null) {
        $extensionKeyChanged = true;
      }

      if ($filter->getElement() !== $filterData['element'] ?? null) {
        $elementChanged = true;
      }
    }

    $extensionKey = $filter->getExtensionKey();
    $element      = $filter->getElement();

    // clear element if a plugin not belonging to the newly selected extension is selected
    if ($extensionKeyChanged) {
      if ($element) {
        $elementData = explode('@', $element);

        if ($elementData[0] === 'plugin' && $extensionKey && $elementData[1] !== $extensionKey) {
          $filter->setElement('');
        }
      }
    }

    // clear element if a plugin not belonging to the newly selected extension is selected
    if ($elementChanged) {
      if ($element) {
        $elementData = explode('@', $element);

        if ($elementData[0] === 'plugin' && $elementData[1] !== $extensionKey) {
          $filter->setExtensionKey($elementData[1]);
        }

        if ($elementData[0] === 'cte') {
          $filter->setExtensionKey('');
        }
      }
    }

    $this->session->set('Filter', $filter->getData());

    return $filter;
  }
}
