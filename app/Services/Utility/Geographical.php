<?php

namespace App\Services\Utility;

use Illuminate\Database\Eloquent\Builder;

trait Geographical
{
    /**
     * Locale scope: distance.
     *
     * @param Builder $query
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return Builder
     */
    public function scopeDistance($query, $latitude, $longitude)
    {
        $latName = $this->getLatitudeColumn();
        $lonName = $this->getLongitudeColumn();
        $query->select($this->getTable() . '.*');
        $sql = "((ACOS(SIN(? * PI() / 180) * SIN(" . $latName . " * PI() / 180) + COS(? * PI() / 180) * COS(" .
            $latName . " * PI() / 180) * COS((? - " . $lonName . ") * PI() / 180)) * 180 / PI()) * 60 * ?) as distance";
        $kilometers = false;
        if (property_exists(static::class, 'kilometers')) {
            $kilometers = static::$kilometers;
        }
        if ($kilometers) {
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515 * 1.609344]);
        } else {
            $query->selectRaw($sql, [$latitude, $latitude, $longitude, 1.1515]);
        }
        return $query;
    }

    /**
     * Locale scope: Geofence.
     *
     * @param $query
     * @param $latitude
     * @param $longitude
     * @param $innerRadius
     * @param $outerRadius
     * @return Builder
     */
    public function scopeGeofence($query, $latitude, $longitude, $innerRadius, $outerRadius)
    {
        $query = $this->scopeDistance($query, $latitude, $longitude);
        return $query->havingRaw('distance BETWEEN ? AND ?', [$innerRadius, $outerRadius]);
    }

    /**
     * Get latitude column with ".table" prefix.
     *
     * @return string
     */
    protected function getLatitudeColumn()
    {
        return $this->getTable() . '.' . "latitude";
    }

    /**
     * Get longitude column with ".table" prefix.
     *
     * @return string
     */
    protected function getLongitudeColumn()
    {
        return $this->getTable() . '.' . "longitude";
    }
}
