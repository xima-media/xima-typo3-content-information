<?php

namespace Xima\ContentInformation\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Xima\ContentInformation\Entity\Filter;

class ContentRepository
{
  public function findByFilter(Filter $filter): array
  {
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tt_content')->createQueryBuilder();

    $queryBuilder->select('tt_content.uid AS contentUid', 'pages.uid AS pagesUid', 'pages.title AS pageTitle',
      'CType', 'list_type', 'colPos', 'pages.deleted AS pagesDeleted', 'tt_content.deleted AS contentDeleted')
      ->from('tt_content')
      ->leftJoin('tt_content', 'pages', 'pages', 'tt_content.pid=pages.uid');

    $extensionKey = $filter->getExtensionKey();

    if ($extensionKey) {
      $queryBuilder->andWhere($queryBuilder->expr()->like('tt_content.list_type', ':list_type'));
      $queryBuilder->setParameter('list_type', '%' . $extensionKey . '%');
    }

    $element = $filter->getElement();

    if ($element) {
      $elementData = explode('@', $element);

      if ($elementData[0] === 'plugin') {
        $queryBuilder->andWhere($queryBuilder->expr()->like('tt_content.list_type', ':list_type'));
        $queryBuilder->setParameter('list_type', '%' . $elementData[2] . '%');
      }

      if ($elementData[0] === 'cte') {
        $queryBuilder->andWhere($queryBuilder->expr()->eq('tt_content.CType', ':ctype'));
        $queryBuilder->setParameter('ctype', $elementData[1]);
      }
    }

    $queryBuilder->andWhere($queryBuilder->expr()->in('tt_content.deleted', true === $filter->getDeleted() ? [0, 1] : [0]));

    return $queryBuilder->execute()->fetchAllAssociative();
  }
}
