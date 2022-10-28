<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/mysqlreport.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\Mysqlreport\Domain\Repository;

use Doctrine\DBAL\Exception;

/**
 * Repository to get records to profile the queries of a request
 */
class ProfileRepository extends AbstractRepository
{
    public function findProfileRecordsForCall(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->add('select', 'crdate, unique_call_identifier, mode, SUM(duration) as duration, COUNT(*) as amount')
            ->from('tx_mysqlreport_domain_model_profile')
            ->groupBy('unique_call_identifier')
            ->orderBy('crdate', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        $profileRecords = [];
        try {
            while ($profileRecord = $result->fetchAssociative()) {
                $profileRecords[] = $profileRecord;
            }
        } catch (Exception $exception) {
            return [];
        }

        return $profileRecords;
    }

    public function getProfileRecordsByUniqueIdentifier(string $uniqueIdentifier): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->add('select', 'query_type, unique_call_identifier, SUM(duration) as duration, COUNT(*) as amount')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier, \PDO::PARAM_STR)
                )
            )
            ->groupBy('query_type')
            ->orderBy('duration', 'DESC');

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        try {
            $profileRecords = [];
            while ($profileRecord = $result->fetchAssociative()) {
                $profileRecords[] = $profileRecord;
            }
        } catch (Exception $exception) {
            return [];
        }

        return $profileRecords;
    }

    public function getProfileRecordsByQueryType(string $uniqueIdentifier, string $queryType): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->add('select', 'uid, query_id, LEFT(query, 120) as query, not_using_index, duration')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'unique_call_identifier',
                    $queryBuilder->createNamedParameter($uniqueIdentifier)
                ),
                $queryBuilder->expr()->eq(
                    'query_type',
                    $queryBuilder->createNamedParameter($queryType)
                )
            )
            ->orderBy('duration', 'DESC');

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        try {
            $profileRecords = [];
            while ($profileRecord = $result->fetchAssociative()) {
                $profileRecords[] = $profileRecord;
            }
        } catch (Exception $exception) {
            return [];
        }

        return $profileRecords;
    }

    public function getProfileRecordByUid(int $uid): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->select('query', 'query_type', 'profile', 'explain_query', 'not_using_index', 'unique_call_identifier', 'duration')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            );

        try {
            return $this->connectionHelper->executeQueryBuilder($queryBuilder)->fetchAssociative();
        } catch (Exception $exception) {
            return [];
        }
    }

    public function findProfileRecordsWithFilesort(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->add('select', 'uid, LEFT(query, 255) as query, explain_query, duration, unique_call_identifier')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->like(
                    'explain_query',
                    $queryBuilder->createNamedParameter('%using filesort%')
                )
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        try {
            $profileRecords = [];
            while ($profileRecord = $result->fetchAssociative()) {
                $profileRecords[] = $profileRecord;
            }
        } catch (Exception $exception) {
            return [];
        }

        return $profileRecords;
    }

    public function findProfileRecordsWithFullTableScan(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->add('select', 'uid, LEFT(query, 255) as query, explain_query, duration, unique_call_identifier')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->eq(
                    'using_fulltable',
                    $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
                )
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        try {
            $profileRecords = [];
            while ($profileRecord = $result->fetchAssociative()) {
                $profileRecords[] = $profileRecord;
            }
        } catch (Exception $exception) {
            return [];
        }

        return $profileRecords;
    }

    public function findProfileRecordsWithSlowQueries(): array
    {
        $queryBuilder = $this->connectionHelper->getQueryBuilderForTable('tx_mysqlreport_domain_model_profile');
        $queryBuilder
            ->add('select', 'uid, LEFT(query, 255) as query, explain_query, duration, unique_call_identifier')
            ->from('tx_mysqlreport_domain_model_profile')
            ->where(
                $queryBuilder->expr()->gte(
                    'duration',
                    $queryBuilder->createNamedParameter($this->extConf->getSlowQueryTime(), \PDO::PARAM_LOB)
                )
            )
            ->orderBy('duration', 'DESC')
            ->setMaxResults(100);

        $result = $this->connectionHelper->executeQueryBuilder($queryBuilder);

        try {
            $profileRecords = [];
            while ($profileRecord = $result->fetchAssociative()) {
                $profileRecords[] = $profileRecord;
            }
        } catch (Exception $exception) {
            return [];
        }

        return $profileRecords;
    }
}
