<?php

namespace App\Services\Utility;

use App\Contracts\Constant;
use App\Models\TicketHeader;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;

/**
 * Created by Jianfeng Li.
 * User: Jianfeng Li
 * Date: 2017/2/14
 */
class Helper
{
    /**
     * Regex phone number.
     *
     * @param $phoneNumberString
     * @return string
     */
    public static function phoneNumber($phoneNumberString)
    {
        if ($phoneNumberString) {
            $pattern = "/(\\D)/";
            $phoneNumber = preg_replace($pattern, "", $phoneNumberString);
            return $phoneNumber;
        }
        return $phoneNumberString;
    }

    /**
     * Generate code by model class and code prefix.
     *
     * @param string $model
     * @param null $codePrefix
     * @return string
     */
    public static function generateCode($model = null, $codePrefix = null)
    {
        $code = (isset($codePrefix) ? $codePrefix : strtoupper(substr((new \ReflectionClass($model))->getShortName(), 0, 2))) . (new Carbon())->getTimestamp();
        return $code;
    }

    /**
     * Get Brand's name by app locale.
     *
     * @param $restaurant
     * @return string
     */
    public static function getRestaurantName($restaurant)
    {
        $locale = App::getLocale();
        if (Constant::LOCALE_ZH_CN == $locale) {
            return isset($restaurant->restaurant_name_cn) ? $restaurant->restaurant_name_cn : $restaurant->restaurant_name;
        } else {
            return isset($restaurant->restaurant_name) ? $restaurant->restaurant_name : $restaurant->restaurant_name_cn;
        }
    }

    /**
     * Convert restaurant's store hours or available hours string formation to array.
     *
     * @param string $hoursString
     * @return array
     */
    public static function convertHoursToArray($hoursString)
    {
        $hours = json_decode($hoursString, true);

        $monArray = explode("|", $hours[0]);
        $tueArray = explode("|", $hours[1]);
        $wedArray = explode("|", $hours[2]);
        $thuArray = explode("|", $hours[3]);
        $friArray = explode("|", $hours[4]);
        $satArray = explode("|", $hours[5]);
        $sunArray = explode("|", $hours[6]);


        $monFrom = trim($monArray[0]);
        $tueFrom = trim($tueArray[0]);
        $wedFrom = trim($wedArray[0]);
        $thuFrom = trim($thuArray[0]);
        $friFrom = trim($friArray[0]);
        $satFrom = trim($satArray[0]);
        $sunFrom = trim($sunArray[0]);

        $monTo = trim(array_get($monArray, 1, ""));
        $tueTo = trim(array_get($tueArray, 1, ""));
        $wedTo = trim(array_get($wedArray, 1, ""));
        $thuTo = trim(array_get($thuArray, 1, ""));
        $friTo = trim(array_get($friArray, 1, ""));
        $satTo = trim(array_get($satArray, 1, ""));
        $sunTo = trim(array_get($sunArray, 1, ""));

        return [
            "mondayFrom" => self::formatOldDtTime($monFrom),
            "mondayTo" => self::formatOldDtTime($monTo),
            "tuesdayFrom" => self::formatOldDtTime($tueFrom),
            "tuesdayTo" => self::formatOldDtTime($tueTo),
            "wednesdayFrom" => self::formatOldDtTime($wedFrom),
            "wednesdayTo" => self::formatOldDtTime($wedTo),
            "thursdayFrom" => self::formatOldDtTime($thuFrom),
            "thursdayTo" => self::formatOldDtTime($thuTo),
            "fridayFrom" => self::formatOldDtTime($friFrom),
            "fridayTo" => self::formatOldDtTime($friTo),
            "saturdayFrom" => self::formatOldDtTime($satFrom),
            "saturdayTo" => self::formatOldDtTime($satTo),
            "sundayFrom" => self::formatOldDtTime($sunFrom),
            "sundayTo" => self::formatOldDtTime($sunTo),
        ];
    }

    /**
     *
     * Bullshit data........
     *
     * Format old bullshit data.
     *
     * @param $time
     * @return null|string
     */
    public static function formatOldDtTime($time)
    {
        if (empty($time) || "off" == strtolower($time)) {
            return null;
        }
        try {
            return Carbon::createFromFormat("g:i A", strtolower($time))->toTimeString();
        } catch (Exception $e) {
            try {
                return Carbon::createFromFormat("g:iA", strtolower($time))->toTimeString();
            } catch (Exception $e) {
                try {
                    return Carbon::createFromFormat("gA", strtolower($time))->toTimeString();
                } catch (Exception $e) {
                    try {
                        return Carbon::createFromFormat("g:i A", substr(strtolower($time), 0, strlen($time) - 1))->toTimeString();
                    } catch (Exception $e) {
                        try {
                            return Carbon::createFromFormat("g:i A", substr(strtolower($time), 1))->toTimeString();
                        } catch (Exception $e) {
                            try {
                                return Carbon::createFromFormat("g:i A", str_replace_first("nn", "PM", strtolower($time)))->toTimeString();
                            } catch (Exception $e) {
                                try {
                                    return Carbon::createFromFormat("g:i A", str_replace_first("am pm", "am", strtolower($time)))->toTimeString();
                                } catch (Exception $e) {
                                    return null;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Convert the store hours or available hours from array to json string.
     *
     * @param array $hours
     * @return string
     */
    public static function stringifyHours($hours)
    {
        $mondayFrom = array_get($hours, "monday_from") ?: "";
        $mondayTo = array_get($hours, "monday_to") ?: "";
        $tuesdayFrom = array_get($hours, "tuesday_from") ?: "";
        $tuesdayTo = array_get($hours, "tuesday_to") ?: "";
        $wednesdayFrom = array_get($hours, "wednesday_from") ?: "";
        $wednesdayTo = array_get($hours, "wednesday_to") ?: "";
        $thursdayFrom = array_get($hours, "thursday_from") ?: "";
        $thursdayTo = array_get($hours, "thursday_to") ?: "";
        $fridayFrom = array_get($hours, "friday_from") ?: "";
        $fridayTo = array_get($hours, "friday_to") ?: "";
        $saturdayFrom = array_get($hours, "saturday_from") ?: "";
        $saturdayTo = array_get($hours, "saturday_to") ?: "";
        $sundayFrom = array_get($hours, "sunday_from") ?: "";
        $sundayTo = array_get($hours, "sunday_to") ?: "";

        $result = [
            0 => $mondayFrom . "|" . $mondayTo,
            1 => $tuesdayFrom . "|" . $tuesdayTo,
            2 => $wednesdayFrom . "|" . $wednesdayTo,
            3 => $thursdayFrom . "|" . $thursdayTo,
            4 => $fridayFrom . "|" . $fridayTo,
            5 => $saturdayFrom . "|" . $saturdayTo,
            6 => $sundayFrom . "|" . $sundayTo,
        ];

        return json_encode($result);
    }

    /**
     * Get datetime string from datetime picker.
     *
     * @param string $dateTimeString
     * @param string $format
     * @return string|null
     */
    public static function getDateTimeString($dateTimeString, $format = "m/d/Y h:i A")
    {
        if (isset($dateTimeString)) {
            return Carbon::createFromFormat($format, $dateTimeString)->toDateTimeString();
        }
        return null;
    }

    /**
     * Create date from timestamp.
     *
     * @param $timeStamp
     * @param null $tz
     * @param string $format
     * @return string|null
     */
    public static function createFromTimestamp($timeStamp, $tz = null, $format = "m/d/Y h:i A")
    {
        if (isset($timeStamp)) {
            return Carbon::createFromTimestamp($timeStamp, $tz)->format($format);
        }
        return null;
    }

    /**
     * Get the special date time formation of the date time string.
     *
     * @param $dateTimeString
     * @param $format
     * @return null|string
     */
    public static function format($dateTimeString, $format)
    {
        if (isset($dateTimeString)) {
            return (new Carbon($dateTimeString))->format($format);
        }
        return null;
    }


    /**
     * Get the special date time formation of the date time string.
     *
     * @param $dateTimeString
     * @param string $format
     * @return string
     */
    public static function formatDatetimeString($dateTimeString, $format = "m/d/Y h:i A")
    {
        return self::format($dateTimeString, $format);
    }

    /**
     * Get datetime string from time picker.
     *
     * @param string $timeString
     * @param string $format
     * @return string|null
     */
    public static function getTimeString($timeString, $format = "h:i A")
    {
        if (isset($timeString)) {
            return Carbon::createFromFormat($format, $timeString)->toDateTimeString();
        }
        return null;
    }

    /**
     * Get the special time formation of the date time string.
     *
     * @param $dateTimeString
     * @param string $format
     * @return string
     */
    public static function formatTimeString($dateTimeString, $format = "h:i A")
    {
        return self::format($dateTimeString, $format);
    }

    /**
     * Get datetime string from date picker.
     *
     * @param string $dateString
     * @param string $format
     * @return string|null
     */
    public static function getDateString($dateString, $format = "m/d/Y")
    {
        if (isset($dateString)) {
            return Carbon::createFromFormat($format, $dateString)->toDateTimeString();
        }
        return null;
    }

    /**
     * Get the special date formation of the date time string.
     *
     * @param $dateTimeString
     * @param string $format
     * @return string
     */
    public static function formatDateString($dateTimeString, $format = "m/d/Y")
    {
        return self::format($dateTimeString, $format);
    }

    /**
     * Is string a json string
     *
     * @param $string
     * @return bool|null
     */
    public static function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * Generated Created
     *
     * @param bool $isForTableSeeder
     * @return array
     */
    public static function generateCreated($isForTableSeeder = false)
    {
        $current = Carbon::now()->format('Y-m-d H:i:s');
        $created = [
            'future' => [
                'tomorrow_with_hours' => [
                    Carbon::now()->subDay(1)->format('Y-m-d H:i:s'), $current
                ],
                'tomorrow' => Carbon::now()->addDay(1)->format('Y-m-d') . '%'
            ],
            'current' => [
                'now' => Carbon::now()->format('Y-m-d H:i:s'),
                'today' => Carbon::now()->format('Y-m-d') . '%',
                'week' => [
                    Carbon::now()->startOfWeek()->format('Y-m-d') . ' 00:00:00',
                    Carbon::now()->endOfWeek()->format('Y-m-d') . ' 23:59:59'
                ],
                'month' => Carbon::now()->format('Y-m-') . '%',
                'minutes_5' => [
                    Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'), $current
                ],
                'minutes_15' => [
                    Carbon::now()->subMinutes(15)->format('Y-m-d H:i:s'), $current
                ],
                'minutes_30' => [
                    Carbon::now()->subMinutes(30)->format('Y-m-d H:i:s'), $current
                ],
                'hour_1' => [
                    Carbon::now()->subHour(1)->format('Y-m-d H:i:s'), $current
                ],
                'hours_4' => [
                    Carbon::now()->subHour(4)->format('Y-m-d H:i:s'), $current
                ],
                'hours_12' => [
                    Carbon::now()->subHour(12)->format('Y-m-d H:i:s'), $current
                ],
            ],
            'past' => [
                'yesterday' => Carbon::now()->subDay(1)->format('Y-m-d') . '%',
                'week' => [
                    Carbon::now()->subDays(7)->startOfWeek()->format('Y-m-d') . ' 00:00:00',
                    Carbon::now()->subDays(7)->endOfWeek()->format('Y-m-d') . ' 23:59:59'
                ],
                'days_30' => [
                    Carbon::now()->subMonth(1)->format('Y-m-d') . ' 00:00:00',
                    Carbon::now()->format('Y-m-d') . ' 23:59:59'
                ],
                'days_60' => [
                    Carbon::now()->subMonths(2)->format('Y-m-d') . ' 00:00:00',
                    Carbon::now()->format('Y-m-d') . ' 23:59:59'
                ],
                'days_180' => [
                    Carbon::now()->subMonths(4)->format('Y-m-d') . ' 00:00:00',
                    Carbon::now()->format('Y-m-d') . ' 23:59:59'
                ]
            ]
        ];

        if ($isForTableSeeder) {
            return [
                'current.minutes_5',
                'current.minutes_15',
                'current.minutes_30',
                'current.hour_1',
                'current.hours_4',
                'current.hours_12',
                'future.tomorrow_with_hours',
                'current.today',
                'past.yesterday',
                'current.week',
                'past.week',
                'current.month',
                'past.days_30',
                'past.days_60',
                'past.days_180'
            ];
        } else {
            return $created;
        }
    }


}