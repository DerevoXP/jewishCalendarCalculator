<?php
/**
 * Файл предназначен для работы с удалённым сервером 'hebcal.com',
 * который позволяет получать время начала и окончания Шаббата,
 * а так же положенное чтение из Торы (за исключением периодов Песаха и Суккота и только для русского языка)
 *
 * @category Utilities
 * @package  dorrevii
 *
 * @author  DerevoXP <derevoxp2015@gmail.com>
 * @license free
 *
 * @link https://dorrevii.ru/
 */

//getMyDate();                               // для глобальной отладки
//echo parseResult(getMyDate());             // для тестирования сегодняшней даты
//echo parseResult(getMyDate(19, 8, 2021));  // для точечного тестирования конкретной даты
//itemTest();                                // для каждого из 365 дней в году

/**
 * Функция для тестирования.
 * Итерируемся по всем дням переданного в параметрах года.
 *
 * @param int $currentYear - тестируемый год (2021 - невисокосный, 2022 - високосный)
 *
 * @return void
 */
function itemTest($currentYear = 2022)
{
    for ($currentMonth = 1; $currentMonth < 13; $currentMonth++) {
        for ($currentDay = 1; $currentDay < 32; $currentDay++) {
            $result = getMyDate($currentDay, $currentMonth, $currentYear);
            echo 'Для даты ' . $currentDay . "/" . $currentMonth . "/" . $currentYear . "\n" . parseResult($result);
        }
    }
}

/**
 * Возвращает результат работы функции getMyDate()
 * в человекопонятном виде для тестирования.
 *
 * @param array $events - результат работы функции getMyDate()
 *
 * @return string
 */
function parseResult(array $events): string
{
    return
        'Зажигание свечей: '
        . $events['start']['dataNormal']
        . " (" . $events['start']['dataJewish'] . ") в "
        . $events['start']['time'] . "\n"
        . 'Авдала:           '
        . $events['finish']['dataNormal']
        . " (" . $events['finish']['dataJewish'] . ") в "
        . $events['finish']['time'] . "\n"
        . "Чтение из Торы:   "
        . $events['parashat'] . "\n"
        . "Город:            " . $events['city'] . "\n\n";
}

/**
 * Запрос на сервер hebcal.com и интерпретация результата.
 *
 * @param int $currentDay   - день
 * @param int $currentMonth - месяц
 * @param int $currentYear  - год
 *
 * @return array
 */
function getMyDate($currentDay = 0, $currentMonth = 0, $currentYear = 0): array
{
    // формируем шаблон ответа
    $result = [
        'start' => [            // начало Шаббата
            'dataNormal' => '', // православная дата
            'dataJewish' => '', // еврейская дата
            'time' => '',       // время захода солнца
        ],
        'finish' => [           // конец Шаббата
            'dataNormal' => '', // православная дата
            'dataJewish' => '', // еврейская дата
            'time' => '',       // время захода солнца
        ],
        'parashat' => '',       // чтение из Торы (отсутствует на период Песаха и Суккота)
        'city' => ''            // город
    ];

    // получаем геолокацию
    $locale = unserialize(
        file_get_contents('http://ip-api.com/php/' . $_SERVER['REMOTE_ADDR'] . '?lang=ru')
    );
    if ($locale['status'] == 'success') {
        $city = $locale['city'];              // город
        $geo =
            "&latitude=" . $locale['lat']     // широта
            . "&longitude=" . $locale['lon']  // долгота
            . "&tzid=" . $locale['timezone']; // идентификатор временной зоны
    } else {
        $city = 'Москва';
        $geo = "&geonameid=472072";           // id геолокации по-умолчанию (Москва, Алтуфьево)
    }

    // формируем кастомную дату (только для теста)
    $currentYear = $currentYear ? "&gy=" . $currentYear : '';
    $currentMonth = $currentMonth ? "&gm=" . $currentMonth : '';
    $currentDay = $currentDay ? "&gd=" . $currentDay : '';

    // делаем запрос на сервер hebcal.com
    $response = file_get_contents("https://www.hebcal.com/shabbat?"
        . "cfg=json"
        . "&b=18"  // за сколько минут до захода зажигаются свечи
        . "&M=on"  // Авдала
        . "&lg=ru" // ЯЗЫК НЕ МЕНЯТЬ!!!
        . $geo
        . $currentYear
        . $currentMonth
        . $currentDay
    );

    $rawArr = json_decode($response, true);

    foreach ($rawArr['items'] as $item) {

        $date = explode('T', $item['date'])[0];
        $dayOfWeek = idate("w", strtotime($date));

        // чтение из Торы
        if ($item['category'] == 'parashat') {
            $result['parashat'] = $item['title'];
        }

        // город, для которого выводим ответ
        $result['city'] = $city;

        if ($dayOfWeek == 5 || $dayOfWeek == 6) { // если Пятница или Суббота
            $arr = explode(":", $item['title']);
            if (
                $dayOfWeek == 5
                && $arr[0] == 'Зажигание свечей'
            ) {
                $result['start']['dataNormal'] = getNormalData($date);
                $result['start']['dataJewish'] = getJewishData($date);
                $result['start']['time'] = date("H:i", strtotime($arr[1] . ':' . $arr[2]));
            } elseif (
                $arr[0] == 'Авдала'
                || $arr[0] == 'Зажигание свечей'
            ) {
                $result['finish']['dataNormal'] = getNormalData($date);
                $result['finish']['dataJewish'] = getJewishData($date);
                $result['finish']['time'] = date("H:i", strtotime($arr[1] . ':' . $arr[2]));
            }
        }
    }

    return $result;
}

/**
 * Возвращает строковое представление григорианской даты
 *
 * @param string $data - строка вида "2021-04-02"
 *
 * @return string
 */
function getNormalData(string $data): string
{
    $arr = explode('-', $data);
    $months = [
        '',          // 0 (заглушка)
        'Января',    // 1
        'Февраля',   // 2
        'Марта',     // 3
        'Апреля',    // 4
        'Мая',       // 5
        'Июня',      // 6
        'Июля',      // 7
        'Августа',   // 8
        'Сентября',  // 9
        'Октября',   // 10
        'Ноября',    // 11
        'Декабря'    // 12
    ];

    return ((int)$arr[2]) . " " . $months[(int)$arr[1]];
}

/**
 * Возвращает строковое представление еврейской даты
 *
 * @param string $data - строка вида "2021-04-02"
 *
 * @return string
 */
function getJewishData(string $data): string
{
    $arr = explode('-', $data);
    $julian = cal_to_jd(CAL_GREGORIAN, (int)$arr[1], (int)$arr[2], (int)$arr[0]); // переводим из ГР в Ю
    $jude = jdtojewish($julian); // переводим из Ю в ЕВР
    $result = explode('/', $jude);
    $hebrewMonth = (int)$result[0];

    // проверяем високосность
    $m = array(3, 6, 8, 11, 14, 17, 19);
    $meuberet = !in_array(($result[2] % 19), $m);
    if ($meuberet) {
        if ($hebrewMonth == 7) { // если не високосный год, то 7 месяц это 6-й. Внезапно, да. Но факт.
            $hebrewMonth = 6;
        }
    }

    $months = [
        '',             // 0 (заглушка)
        'Тишрея',       // 1
        'Хешвана',      // 2
        'Кислева',      // 3
        'Тевета',       // 4
        'Швата',        // 5
        'Адара',        // 6 - в високосном или 7 в невисокосном году
        'Адара II',     // 7 - в високосный год
        'Нисана',       // 8
        'Ияра',         // 9
        'Сивана',       // 10
        'Тамуза',       // 11
        'Ава',          // 12
        'Элуля',        // 13
    ];

    return ((int)$result[1]) . " " . $months[$hebrewMonth];
}
