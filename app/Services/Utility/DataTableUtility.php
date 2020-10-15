<?php

namespace App\Services\Utility;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/4/28
 */
class DataTableUtility
{
    /**
     * Get the search columns' info.
     *
     * @param $dataTableParameters
     * @return array
     */
    public static function searchColumn($dataTableParameters)
    {
        $searchArray = [];
        $columns = $dataTableParameters["columns"];
        $search = $dataTableParameters["search"]["value"];
        if ($search) {
            foreach ($columns as $column) {
                if ($column["searchable"] == "true") {
                    $key = $column["data"];
                    if (str_contains($column["data"], ".")) {
                        $columnsArr = explode(".", $column["data"]);
                        $modelName = $columnsArr[0];
                        $modelAttribute = $columnsArr[1];
                        if (str_contains($modelName, "_")) {// if the model name container underscore, then transform to camel formation.
                            $modelName = camel_case($modelName);
                        }
                        $key = $modelName . "." . $modelAttribute;
                    }
                    $searchArray[$key] = $search;
                }
            }
        }
        return $searchArray;
    }


    /**
     * Get the search columns' info.
     *
     * @param $dataTableParameters
     * @return array
     */
    public static function basicSearch($dataTableParameters)
    {
        $filterArray = [];
        $searchArray = [];
        $columns = $dataTableParameters["bs_params"];
        if ($columns) {
            foreach ($columns as $column) {
                if ($column["filterable"] == "true" && !is_null($column["value"]) && !is_array($column["value"])) {
                    $key = $column["name"];
                    if (str_contains($column["name"], ".")) {
                        $columnsArr = explode(".", $column["name"]);
                        $modelName = $columnsArr[0];
                        $modelAttribute = $columnsArr[1];
                        if (str_contains($modelName, "_")) {// if the model name container underscore, then transform to camel formation.
                            $modelName = camel_case($modelName);
                        }
                        $key = $modelName . "." . $modelAttribute;
                    }
                    $filterArray[$key] = $column["value"];
                }

                if (is_array($column["value"])) {
                    $key = $column["name"];
                    if (str_contains($column["name"], ".")) {
                        $columnsArr = explode(".", $column["name"]);
                        $modelName = $columnsArr[0];
                        $modelAttribute = $columnsArr[1];
                        if (str_contains($modelName, "_")) {// if the model name container underscore, then transform to camel formation.
                            $modelName = camel_case($modelName);
                        }
                        $key = $modelName . "." . $modelAttribute;
                    }
                    if (array_get($column["value"], 'type') === "date") {
                        $mySqlTimeRange = ["1970-01-01 00:00:01", "2038-01-19 03:14:07"];
                        for ($i = 0; $i < sizeof($column["value"]["value"]); $i++) {
                            if (is_null($column["value"]["value"][$i]))
                                $column["value"]["value"][$i] = $mySqlTimeRange[$i];
                            else
                                $column["value"]["value"][$i] = Helper::getDateTimeString($column["value"]["value"][$i]);
                        }
                    }
                    $filterArray[$key] = $column["value"];
                }

                if ($column["searchable"] == "true" && !is_null($column["value"])) {
                    $key = $column["name"];
                    if (str_contains($column["name"], ".")) {
                        $columnsArr = explode(".", $column["name"]);
                        $modelName = $columnsArr[0];
                        $modelAttribute = $columnsArr[1];
                        if (str_contains($modelName, "_")) {// if the model name container underscore, then transform to camel formation.
                            $modelName = camel_case($modelName);
                        }
                        $key = $modelName . "." . $modelAttribute;
                    }
                    $searchArray[$key] = $column["value"];
                }
            }
        }

        return ["filterArray" => $filterArray, "searchArray" => $searchArray];
    }

    /**
     * Get the order columns' info.
     *
     * @param $dataTableParameters
     * @return array
     */
    public static function orderColumn($dataTableParameters)
    {
        $orderColumn = [];
        if (array_has($dataTableParameters, "order")) {

            $orderArray = $dataTableParameters["order"];
            $columns = $dataTableParameters["columns"];
            foreach ($orderArray as $order) {
                $key = $columns[$order["column"]]["data"];
                if (str_contains($key, ".")) {
                    $columnsArr = explode(".", $key);
                    $modelName = $columnsArr[0];
                    $modelAttribute = $columnsArr[1];
                    if (str_contains($modelName, "_")) {// if the model name container underscore, then transform to camel formation.
                        $modelName = camel_case($modelName);
                    }
                    $key = $modelName . "." . $modelAttribute;
                }
                $orderColumn[$key] = $order["dir"];
            }

        }
        return $orderColumn;
    }

}