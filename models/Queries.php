<?php

namespace app\models;

use app\database\DbConnection;
use mysqli_stmt;
use app\log\Log;
use app\exceptions\PrepareQueryFailedException;
use app\exceptions\QueryExecuteFailedException;

class Queries
{
    private static $dbName;

    function __construct($db_name){
        self::$dbName = $db_name;
    }

    /** 
     *    prepare query
     *    returns a statement object or false if an error occurred.
     *    @param string
     *    @return mysqli_stmt|bool
     */
    private function prepareQuery(string $query): mysqli_stmt|bool
    {
        Log::logInfo("Queries", "prepareQuery", "preparing query", "pending", $query);

        return DbConnection::getDbConnectionInstance()->getDbConnection()->prepare($query);
    }

    /** 
     *    get the recent date inserted from a table
     *    @param string $table 
     *    @param string $column 
     *    @return array  
     */
    public function getRecentDate(string $table, string $column): array
    {
        $query = "SELECT MAX($column) AS latest_order_date
                FROM $table";

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("failed query - $query", Queries::class, "getRecentDate");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("failed query - $query", Queries::class, "getRecentDate");
        }
        $result = $statement->get_result();
        Log::logInfo("Queries", "getRecentDate", "get the recent date inserted from a table", "success", "data => table - $table; column - $column");

        return $result->fetch_all(MYSQLI_ASSOC);

    }

    /** 
     *    get unique coulmn names of a table
     *    @param string $table 
     *    @param array $columnsWithData 
     *    @return array|bool
     */
    public function getUniqueCoulmnNames(string $tableName) : array|bool
    {
        $dbName = self::$dbName;

        $query = "
            SELECT COLUMN_NAME AS column_name
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = '$dbName'  
            AND TABLE_NAME = '$tableName'
            AND NON_UNIQUE = 0;
        ";
        Log::logInfo("Queries", "getUniqueCoulmnNames", "trying to get unique column names of a table", "success", "db name - $dbName; table - $tableName");

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("failed query - $query", Queries::class, "getUniqueCoulmnNames");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("failed query - $query", Queries::class, "getUniqueCoulmnNames");
        }
        $result = $statement->get_result();

        // $resultArray[0]['column_name]
        $resultArray = $result->fetch_all(MYSQLI_ASSOC);

        if (count($resultArray) === 0) {
            Log::logInfo("Queries", "getUniqueCoulmnNames", "get unique column names of a table", "success", "no unique columns");

            return false;
        }
        $logData = "";
        foreach($resultArray as $columnNames){
            $logData .= $columnNames['column_name']."|";
        }
        Log::logInfo("Queries", "getUniqueCoulmnNames", "get unique column names of a table", "success", "unique columns - $logData");

        return $resultArray;
    }

    /** 
     *    update dates of tables
     *    @param string $table 
     *    @param array $columnsWithData 
     *    @return int  
     */
    public function updateDates(string $table, array $columnsWithData): int
    {
        $setData = "";
        $uniqueColumnNames = $this->getUniqueCoulmnNames($table);
        $orderBy = "";

        foreach ($columnsWithData as $columnName => $data) {
            if($uniqueColumnNames){
                if(in_array($columnName, $uniqueColumnNames)){
                    $orderBy = "ORDER BY $columnName";
                }
            }
            
            $modifier = ($data['modifier'] === "add") ? "DATE_ADD" : "DATE_SUB";
            $dateDiff = $data['dateDiff'];
            $setData .= "$columnName = IF($columnName IS NOT NULL ,$modifier($columnName, INTERVAL $dateDiff DAY),$columnName),";
        }

        $setData = substr($setData, 0, -1);

        $query = "
            UPDATE $table SET 
                $setData $orderBy 
        ";









        









        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("failed query - $query", Queries::class, "updateDates");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("failed query - $query", Queries::class, "updateDates");
        }
        Log::logInfo("Queries", "updateDates", "update dates of tables", "success", "data => table - $table; set data - $setData");

        return $statement->affected_rows;
    }

    public function getLatestTableNames(string $tablePrefix, string $tableRemovePart, array $numericPart, int $limit): array|bool
    {
        $numericPartStart = $numericPart['start'];
        $numericPartLength = $numericPart['length'];
        $dbName = self::$dbName;
        $query = "
            SELECT table_name AS table_name
            FROM information_schema.tables
            WHERE table_schema = '$dbName'
            AND table_name LIKE '{$tablePrefix}%' 
            AND table_name NOT LIKE '%{$tableRemovePart}' 
            ORDER BY CAST(SUBSTRING(table_name, $numericPartStart, $numericPartLength) AS UNSIGNED) DESC
            LIMIT $limit;
        ";

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("failed query - $query", Queries::class, "getLatestTableName");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("failed query - $query", Queries::class, "getLatestTableName");
        }
        $result = $statement->get_result();
        Log::logInfo("Queries", "getLatestTableName", "get latest created table name", "success", "db name - $dbName; table prefix - $tablePrefix; table remove part - $tableRemovePart; numeric part - [start:$numericPartStart, length:$numericPartLength]");

        $resultArray = $result->fetch_all(MYSQLI_ASSOC);

        if (count($resultArray) === 0) {

            return false;
        }

        return $resultArray;
    }

    public function copyTable(string $originalTable, string $newTable)
    {
        //   CREATE TABLE $newTable LIKE $originalTable
        $query = "
            CREATE TABLE $newTable SELECT * FROM $originalTable
        ";

        $statement = $this->prepareQuery($query);

        if ($statement === false)
            throw new PrepareQueryFailedException("failed query - $query", Queries::class, "copyTable");

        if ($statement->execute() === false) {
            throw new QueryExecuteFailedException("failed query - $query", Queries::class, "copyTable");
        }
        Log::logInfo("Queries", "copyTable", "copy table structure and data into new table", "success", "data => original table - $originalTable; new table - $newTable");
    }

    // public function copyTableData(string $originalTable, string $newTable)
    // {
    //     $query = "
    //         INSERT INTO $newTable SELECT * FROM $originalTable
    //     ";

    //     $statement = $this->prepareQuery($query);

    //     if ($statement === false)
    //         throw new PrepareQueryFailedException("failed query - $query", Queries::class, "copyTableData");

    //     if ($statement->execute() === false) {
    //         throw new QueryExecuteFailedException("failed query - $query", Queries::class, "copyTableData");
    //     }
    //     Log::logInfo("Queries", "copyTableData", "copy table data into new table", "success", "data => data => original table - $originalTable; new table - $newTable");
    // }
}


