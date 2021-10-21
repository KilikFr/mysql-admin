<?php

namespace App\Services;

use App\DTO\Table;
use App\Entity\Slave;

class DiffRowService
{
    public const DIFF_EQUAL = 0;
    public const DIFF_MISSING_MASTER = 1;
    public const DIFF_MISSING_SLAVE = 2;
    public const DIFF_DIFFERENT = 3;

    private ConnectionService $connectionService;

    public function __construct(ConnectionService $connectionService)
    {
        $this->connectionService = $connectionService;
    }

    public function diffRows(
        Table $masterTable,
        Table $slaveTable,
        string $where = '1',
        $diffCallback = null
    ): int {
        $masterStmt = $this->findAllIdHashStmt($masterTable, $where);
        $slaveStmt = $this->findAllIdHashStmt($slaveTable, $where);

        return $this->diffRowsStmt($masterStmt, $slaveStmt, $masterTable, $slaveTable, $diffCallback);
    }

    /**
     * @throws \Exception
     */
    private function findAllIdHashStmt(Table $table, string $where = '1'): \PDOStatement
    {
        $connection = $this->connectionService->getServerConnection($table->getServer());

        $primaryFields = $table->getPrimary();

        if (0 == count($primaryFields)) {
            throw new \Exception('no primary key defined on this table');
        }

        $dataFields = '';
        foreach ($table->getFields() as $field) {
            if ('PRI' != $field->getKey()) {
                if ('' != $dataFields) {
                    $dataFields .= ',';
                }
                $dataFields .= sprintf('`%s`', $field->getField());
            }
        }
        // case for many to many tables with no real data columns
        if ('' === $dataFields) {
            $dataFields = '1';
        }

        if (count($primaryFields) > 1) {
            $composedFields = '';
            $orderComposedFields = '';
            foreach ($primaryFields as $primaryField) {
                if ('' !== $composedFields) {
                    $composedFields .= ",'-',";
                    $orderComposedFields .= ',';
                }
                $composedFields .= '`'.$primaryField->getField().'`';
                $orderComposedFields .= '`'.$primaryField->getField().'`';
            }
            $query = sprintf(
                'SELECT CONCAT(%s) AS id, MD5(CONCAT(%s)) AS hash FROM `%s`.`%s` WHERE %s ORDER BY %s',
                $composedFields,
                $dataFields,
                $table->getDatabase(),
                $table->getName(),
                $where,
                $orderComposedFields,
            );
        } else {
            $query = sprintf(
                'SELECT `%s` AS id, MD5(CONCAT(%s)) AS hash FROM `%s`.`%s` WHERE %s ORDER BY `%s`',
                $primaryFields[0]->getField(),
                $dataFields,
                $table->getDatabase(),
                $table->getName(),
                $where,
                $primaryFields[0]->getField(),
            );
        }

        $stmt = $connection->query($query, \PDO::FETCH_NUM);
        if (false === $stmt) {
            $message = sprintf('error (%s): %s', $connection->errorCode(), $connection->errorInfo()[2]);
            throw new \Exception($message);
        }

        return $stmt;
    }

    private function diffRowsStmt(
        \PDOStatement $masterStmt,
        \PDOStatement $slaveStmt,
        Table $masterTable,
        Table $slaveTable,
        $diffCallback = null
    ): int {
        $nbDifferences = 0;

        $masterRow = $masterStmt->fetch();
        $slaveRow = $slaveStmt->fetch();
        while (false !== $masterRow || false !== $slaveRow) {
            $result = $this->compareRows($masterRow, $slaveRow);

            if (null !== $diffCallback) {
                call_user_func($diffCallback, $masterTable, $slaveTable, $result, $masterRow, $slaveRow);
            }

            switch ($result) {
                // missing on slave
                case self::DIFF_MISSING_SLAVE:
                    $nbDifferences++;

                    // move forward on master
                    $masterRow = $masterStmt->fetch();
                    break;
                // just equals ... nothing to display
                case self::DIFF_EQUAL:
                    // move forward on master and slave
                    $masterRow = $masterStmt->fetch();
                    $slaveRow = $slaveStmt->fetch();
                    break;
                // missing on master
                case self::DIFF_MISSING_MASTER:
                    $nbDifferences++;

                    // move forward on slave
                    $slaveRow = $slaveStmt->fetch();
                    break;
                // found on both sides, but different
                case self::DIFF_DIFFERENT:
                    $nbDifferences++;
                    // move forward on master and slave
                    $masterRow = $masterStmt->fetch();
                    $slaveRow = $slaveStmt->fetch();
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('unknown compareRows result %d', $result));
            }
        }

        return $nbDifferences;
    }

    /**
     * @param array|bool $masterRow
     * @param array|bool $slaveRow
     *
     * @return int (@see static::DIFF_*)
     */
    private function compareRows($masterRow, $slaveRow): ?int
    {
        // record is missing on slave
        if (false === $slaveRow) {
            return self::DIFF_MISSING_SLAVE;
        } // record is missing on master
        elseif (false === $masterRow) {
            return self::DIFF_MISSING_MASTER;
        }
        // id in master is lower: record is missing on slave
        if ($masterRow[0] < $slaveRow[0]) {
            return self::DIFF_MISSING_SLAVE;
        } // id in master is greater: record is missing on master
        elseif ($masterRow[0] > $slaveRow[0]) {
            return self::DIFF_MISSING_MASTER;
        } // id are equals
        else {
            if ($masterRow[1] === $slaveRow[1]) {
                return self::DIFF_EQUAL;
            } else {
                return self::DIFF_DIFFERENT;
            }
        }
    }
}
