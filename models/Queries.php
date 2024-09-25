<?php

namespace app\models;

use app\database\DbConnection;
use mysqli_stmt;
use app\log\Log;
use app\exceptions\PrepareQueryFailedException;
use app\exceptions\QueryExecuteFailedException;

class Queries
{

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
     *    update dates of tables
     *    @param string $table 
     *    @param array $columnsWithData 
     *    @return int  
     */
    public function updateDates(string $table, array $columnsWithData): int
    {
        $setData = "";

        foreach ($columnsWithData as $columnName => $data) {
            $modifier = ($data['modifier'] === "add") ? "DATE_ADD" : "DATE_SUB";
            $dateDiff = $data['dateDiff'];
            $setData .= "$columnName = IF($columnName IS NOT NULL ,$modifier($columnName, INTERVAL $dateDiff DAY),$columnName),";
        }

        $setData = substr($setData, 0, -1);

        $query = "
            UPDATE $table SET 
                $setData 
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
}


