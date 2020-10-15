<?php

namespace App\Services;

use App\Contracts\BaseService;
use App\Models\BaseModel;
use App\Services\Utility\HttpClient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/4/30
 * Time: 14:31
 */
class BaseServiceImpl implements BaseService
{
    /**
     * Fetch page object by table's name , page size, searching info ,and ordering info;
     *
     * $modelClass : The  Class Name of eloquent model.
     * Page Info : page num and page size.
     * Filter Columns : Key : column's name, Value : filter value.
     * Search Columns :  Key : column's name, Value : search value
     * Order Columns : Key : column's name, Value : ordering type ("asc", or "desc")
     * Eager Loading : Eager Loading attributes;
     *
     * @param $modelClass
     * @param array $pageInfo
     * @param array $filterColumn
     * @param array $orderColumn
     * @param array $searchColumn
     * @param array $eagerLoading
     * @param array $scopes
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection
     */
    public function fetch($modelClass, $pageInfo = [], $filterColumn = [], $orderColumn = [], $searchColumn = [], $eagerLoading = [], $scopes = [])
    {
        Log::debug(get_class($this) . "::fetch => Fetch page object by table's name , page size, searching info ,and ordering info.");

        $query = $modelClass::whereRaw("1=1");

        if (isset($scopes) && sizeof($scopes) > 0) {
            foreach ($scopes as $scope) {
                $query->{$scope["scope"]}(join(",", $scope["parameters"]));
            }
        }


        if (isset($filterColumn) && sizeof($filterColumn) > 0) {
            $query->where(function ($q) use ($filterColumn) {
                foreach ($filterColumn as $column => $filter) {
                    if (strpos($column, ".") !== false) {
                        $relationColumn = explode(".", $column);
                        $className = "App\\Models\\" . ucfirst($relationColumn[0]);
                        if (class_exists($className)) {
                            $relationTable = (new $className)->getTable();
                        } else {
                            $relationTable = $relationColumn[0];
                        }
                        if (isset($filter)) {
                            if (is_array($filter) && array_get($filter, "type") == BaseModel::STATUS_NEGATIVE) {
                                $q->whereDoesntHave($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $filter) {
                                    $this->generateCriteria($relateQuery, $relationTable . "." . $relationColumn[1], $filter);
                                });
                            } else {
                                $q->whereHas($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $filter) {
                                    $this->generateCriteria($relateQuery, $relationTable . "." . $relationColumn[1], $filter);
                                });
                            }
                        } else {
                            $q->has($relationColumn[0]);
                        }
                    } else {
                        $q = $this->generateCriteria($q, $column, $filter);
                    }
                }
            });
        }

        if (isset($searchColumn) && sizeof($searchColumn) > 0) {
            $query->where(function ($q) use ($searchColumn) {
                foreach ($searchColumn as $column => $search) {
                    if (strpos($column, ".") !== false) {
                        $relationColumn = explode(".", $column);
                        $className = "App\\Models\\" . ucfirst($relationColumn[0]);
                        $relationTable = (new $className)->getTable();
                        $q->orWhereHas($relationColumn[0], function ($relateQuery) use ($relationTable, $relationColumn, $search) {
                            $relateQuery->where($relationTable . "." . $relationColumn[1], "like", "%" . $search . "%");
                        });
                    } else {
                        $q->orWhere($column, "like", "%" . $search . "%");
                    }
                }
            });
        }

        if (isset($eagerLoading) && sizeof($eagerLoading) > 0) {
            foreach ($eagerLoading as $value) {
                $query = $query->with($value);
            }
        }

        if (isset($orderColumn) && sizeof($orderColumn) > 0) {
            foreach ($orderColumn as $column => $dir) {
                if (strpos($column, ".") !== false) {
                    $relationColumn = explode(".", $column);
                    $query->with([$relationColumn[0] => function ($relateQuery) use ($relationColumn, $dir) {
                        $relateQuery->orderBy($relationColumn[1], $dir);
                    }]);

                } else {
                    $query->orderBy($column, $dir);
                }
            }
        } else {
            $query->orderBy("updated_at", "desc");
        }

        if (isset($pageInfo) && array_get($pageInfo, "pageSize")) { // if the page info exists , then fetch the pagination info.
            $pageSize = $pageInfo["pageSize"];
            $result = $query->paginate($pageSize);
        } else {
            $result = $query->get();
        }

        return $result;
    }

    /**
     * @param $q
     * @param $column
     * @param $filter
     *
     * @return object
     */
    protected function generateCriteria($q, $column, $filter)
    {
        if (is_array($filter)) {
            $operation = array_get($filter, "operation");
            $value = array_get($filter, "value");
            if ("isNull" == $operation) {
                $q->whereNull($column);
            } else if ("isNotNull" == $operation) {
                $q->whereNotNull($column);
            } else if ("in" == $operation && is_array($value)) {
                $q->whereIn($column, $value);
            } else if ("notIn" == $operation && is_array($value)) {
                $q->whereNotIn($column, $value);
            } else if ("between" == $operation && is_array($value)) {
                $q->whereBetween($column, $value);
            } else {
                $q->where($column, $operation, $value);
            }
        } else {
            if (null == $filter) {
                $q->whereNull($column);
            } else {
                $q->where($column, "=", $filter);
            }
        }

        return $q;
    }

    /**
     * Fetch Model by id.
     * Eager Loading : Eager Loading attributes;
     *
     * @param $modelClass
     * @param $id
     * @param array $eagerLoading
     * @return Model|null
     */
    public function fetchModelById($modelClass, $id, $eagerLoading = [])
    {
        Log::debug(get_class($this) . "::fetchModelById => Fetch Model by id.");
        $result = null;
        $query = $modelClass::where("id", $id);
        if (sizeof($eagerLoading) > 0) {
            foreach ($eagerLoading as $value) {
                $query = $query->with($value);
            }
        }
        $result = $query->first();
        return $result;
    }

    /**
     * Create a new model(Persistence data).
     *
     * @param $modelClass
     * @param $data
     * @return Model
     */
    public function createModel($modelClass, $data)
    {
        Log::debug(get_class($this) . "::createModel => Create a new model(Persistence data).");
        $model = new $modelClass();
        foreach ($data as $col => $value) {
            $model->{$col} = $value;
        }
        $model->save();

        return $model;
    }

    /**
     * Update model by id.
     * $data : attributes which should be updated.
     *
     * @param $modelClass
     * @param $id
     * @param $data
     * @return Model
     */
    public function updateModel($modelClass, $id, $data)
    {
        Log::debug(get_class($this) . "::updateModel => Update model by id.");
        $result = null;
        $model = $modelClass::find($id);
        if ($model) {
            foreach ($data as $key => $value) {
                $model->$key = $value;
            }
            $model->save();
        }
        $result = $model;

        return $result;
    }

    /**
     * Fetch the distance matrix from google api.
     * Output Format is JSON.
     * mode is default : driving.
     * language is default as : en.
     *
     * @param array $from
     * @param array $to
     * @param null $mode
     * @param string $language
     * @return string
     */
    public function fetchDistanceMatrix($from = [], $to = [], $mode = null, $language = null)
    {
        $httpClient = HttpClient::getInstance();
        $origins = implode("|", $from);
        $origins = str_replace(" ", "+", $origins);

        $destinations = implode("|", $to);
        $destinations = str_replace(" ", "+", $destinations);

        $key = config("app.google_api_key");

        $paramArray = [
            "units" => "imperial",
            "origins" => $origins,
            "destinations" => $destinations,
            "key" => $key,
        ];

        if ($mode) {
            $paramArray["mode"] = $mode;
        }
        if ($language) {
            $paramArray["language"] = $language;
        }

        $result = $httpClient->get("https://maps.googleapis.com/maps/api/distancematrix/json", $paramArray);
        return $result;
    }
}