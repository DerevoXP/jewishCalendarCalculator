<?php
/**
 * Class JewishHolidays предназначен для перевода
 * православный даты в иудейский формат с особым цинизмом
 *
 * @category Utilities
 * @package  dorrevii
 *
 * @author  DerevoXP <derevoxp2015@gmail.com>
 * @license free
 *
 * @link https://dorrevii.ru/
 */
class JewishHolidays
{
   public $today_j             = [];   // сегодня в еврейском календаре
   public $holidays_j          = [];   // массив праздничных периодов (еврейские даты - ключ, значение - название)
   public $holidays_j_period   = [];   // ключ - название, значение - период (григ/евр)
   private $holidays_j_to_case = [];   // массив названий; ключ - единственное число, значение - множественное
   private $months_j           = [];   // названия еврейских месяцев, нулевой - пустая строка
   private $months_g           = [];   // названия православных месяцев, нулевой - пустая строка

    /**
     * JewishHolidays constructor.
     */
    public function __construct()
    {
         $d = date('d');
         $m = date('m');
         $y = date('Y');

        /*
        * Пост Эстер на Субботу     - в високосном году   - 15.03.2014 / 13.07.5774 -> 13.03.2014 / 11.07.5774 (1)
        *                           - в невисокосном году - 11.03.2017 / 13.07.5777 -> 09.03.2017 / 11.07.5774 (2)
        *
        * Йом ха-Шоа на Пятницу     - в високосном году   - 02.05.2008 / 27.08.5768 -> 01.05.2008 / 26.08.5768 (3)
        *                           - в невисокосном году - 09.04.2021 / 27.08.5781 -> 08.04.2021 / 26.08.5781 (4)
        *
        *            на Воскресенье - в високосном году   - 01.05.2011 / 27.08.5771 -> 02.05.2011 / 28.08.5771 (5)
        *                           - в невисокосном году - 23.04.2017 / 27.08.5777 -> 24.04.2017 / 28.08.5777 (6)
        *
        */

        // 1)
        // $d = 14;
        // $m = 03;
        // $y = 2014;

        // 2)
        // $d = 010;
        // $m = 03;
        // $y = 2017;

        // 3)
        // $d = 01;
        // $m = 05;
        // $y = 2008;

        // 4)
        // $d = 07;
        // $m = 04;
        // $y = 2021;

        // 5)
        // $d = 30;
        // $m = 04;
        // $y = 2011;

        // 6)
        // $d = 10;
        // $m = 3;
        // $y = 2017;

        $this->today_j = $this->getDateInJ($d, $m, $y);

        $this->holidays_j = [                    // в невисокосном году
            '10.4' => 'Асара-Бэтевет',           // 10 тевета
            '15.5' => 'Ту-би-Шват',              // 15 швата
            '14.7' => 'Пурим',                   // 14 адара
            '15.7' => 'Пурим',                   // в некоторых городах празднуется 15 адара
            '15.8' => 'Песах',                   // с 15 нисана
            '16.8' => 'Песах',
            '17.8' => 'Песах',
            '18.8' => 'Песах',
            '19.8' => 'Песах',
            '20.8' => 'Песах',
            '21.8' => 'Песах',                   // по 21 нисана
            '14.9' => 'Песах шени',              // 14 ияра
            '18.9' => 'Лаг-Баомер',              // 18 ияра
            '6.10' => 'Шавуот',                  // 6 сивана
            '7.10' => 'Шавуот',                  // 7 сивана
            '9.12' => '9 Ава',                   // 9 ава, как ни странно
            '1.1'  => 'Рош ха-Шана',             // 1 тишрея
            '2.1'  => 'Рош ха-Шана',             // 2 тишрея
            '3.1'  => 'Пост Гедалии',            // 3 тишрея
            '10.1' => 'Йом Кипур (Судный день)', // 10 тишрея
            '15.1' => 'Суккот',                  // с 15 тишрея
            '16.1' => 'Суккот',
            '17.1' => 'Суккот',
            '18.1' => 'Суккот',
            '19.1' => 'Суккот',
            '20.1' => 'Суккот',
            '21.1' => 'Суккот',
            '22.1' => 'Шмини Ацерет',            // по 22 тишрея
            '23.1' => 'Симхат Тора',             // 23 тишрея
            '25.3' => 'Ханука',                  // с 25 кислева
            '26.3' => 'Ханука',
            '27.3' => 'Ханука',
            '28.3' => 'Ханука',
            '29.3' => 'Ханука',
            '30.3' => 'Ханука',
            '1.4'  => 'Ханука',
            '2.4'  => 'Ханука',
            '3.4'  => 'Ханука',                  // по 3 тевета
        ];

        $this->holidays_j_period = [
            'Асара-Бэтевет'           => '10.4',
            'Ту-би-Шват'              => '15.5',
            'Пурим'                   => '14.7-15.7',
            'Песах'                   => '15.8-21.8',
            'Песах шени'              => '14.9',
            'Лаг-Баомер'              => '18.9',
            'Шавуот'                  => '6.10-7.10',
            'Тиша-бэ-Ав'              => '9.12',
            'Рош ха-Шана'             => '1.1-2.1',
            'Пост Гедалии'            => '3.1',
            'Йом Кипур (Судный день)' => '10.1',
            'Суккот'                  => '15.1-22.1',
            'Шмини Ацерет'            => '22.1',
            'Симхат Тора'             => '23.1',
            'Ханука'                  => '25.3-3.4'
        ];

        $this->holidays_j_to_case = [
            'Асара-Бэтевет'           => 'Асара-Бэтевета',
            'Ту-би-Шват'              => 'Ту-би-Швата',
            'Пост Эстер'              => 'Поста Эстер',
            'Пурим'                   => 'Пурима',
            'Песах'                   => 'Песаха',
            'Йом ха-Шоа'              => 'Йом ха-Шоа',
            'Песах шени'              => 'Песаха шени',
            'Лаг-Баомер'              => 'Лаг-Баомера',
            'Шавуот'                  => 'Шавуота',
            'Тиша-бэ-Ав'              => 'Тиша-бэ-Ава',
            'Рош ха-Шана'             => 'Рош ха-Шана',
            'Пост Гедалии'            => 'Поста Гедалии',
            'Йом Кипур (Судный день)' => 'Йом Кипура (Судного дня)',
            'Суккот'                  => 'Суккота',
            'Шмини Ацерет'            => 'Шмини Ацерета',
            'Симхат Тора'             => 'Симхат Тора',
            'Ханука'                  => 'Хануки'
        ];

        $this->months_j = [
            '',             // 0
            'Тишрея',       // 1
            'Хешвана',      // 2
            'Кислева',      // 3
            'Тевета',       // 4
            'Швата',        // 5
            'Адара',        // 6 - в високосном или 7 в невисокосном году
            'Адар бет',     // 7 - в високосный год
            'Нисана',       // 8
            'Ияра',         // 9
            'Сивана',       // 10
            'Тамуза',       // 11
            'Ава',          // 12
            'Элуля',        // 13
        ];

        $this->months_g = [
            '',          // 0
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

        $this->getDateForEsterAndYomHaShoa(); // отдельно определяем даьы для Поста Эстер и Йом ха-Шоа
    }

    /**
     * Функция задаёт даты для Поста Эстер и Йом Ха-Шоа,
     * если они выпадают на субботу или пятницу
     *
     * @return void
     */
    public function getDateForEsterAndYomHaShoa() {
        // позже Нисана нет смысла в запуске и инициализации элементов массива - Пост Эстер и Йом Ха-Шоа уже прошли.
        if ($this->today_j['month'] < 9) {

            $julianEster = cal_to_jd(CAL_JEWISH, 7, 13, $this->today_j['year']);
            $gregorianEster = jdtogregorian($julianEster);
            $dateEster = date("m/d/Y", strtotime($gregorianEster));

            // echo "\ndateEster = " . $dateEster . "\n";
            // echo "День недели Таанит Эстер: " . date("w", strtotime($dateEster)) . "\n\n";

            if (date("w", strtotime($dateEster)) == 6) { // если пост Эстер выпадает на субботу, то
                $this->holidays_j['11.7'] = 'Пост Эстер'; // переносим на четверг, 11-го Адара
                $this->holidays_j_period['Пост Эстер'] = '11.7';
            } else {
                $this->holidays_j['13.7'] = 'Пост Эстер'; // иначе - по дефолту
                $this->holidays_j_period['Пост Эстер'] = '13.7';
            }

            $julianYomHaShoa = cal_to_jd(CAL_JEWISH, 8, 27, $this->today_j['year']);
            $gregorianYomHaShoa = jdtogregorian($julianYomHaShoa);
            $dateYomHaShoa = date("m/d/Y", strtotime($gregorianYomHaShoa));

            // echo "dateYomHaShoa = " . $dateYomHaShoa . "\n";
            // echo "День недели Йом ха-Шоа: " . date("w", strtotime($dateYomHaShoa)) . "\n\n";

            if (date("w", strtotime($dateYomHaShoa)) == 5) { // если Йом ха-Шоа выпадает на пятницу, то
                $this->holidays_j['26.8'] = 'Йом ха-Шоа'; // переносим на четверг, 26-го
                $this->holidays_j_period['Йом ха-Шоа'] = '26.8';
            } elseif (date("w", strtotime($dateYomHaShoa)) == 0) { // если на воскресенье
                $this->holidays_j['28.8'] = 'Йом ха-Шоа'; // то переносим на 28, понедельник
                $this->holidays_j_period['Йом ха-Шоа'] = '28.8';
            } else {
                $this->holidays_j['27.8'] = 'Йом ха-Шоа'; // иначе - по дефолту
                $this->holidays_j_period['Йом ха-Шоа'] = '27.8';
            }
        }
    }

    /**
     * Функция перевода григорианской даты в еврейскую
     *
     * @param $d - день
     * @param $m - месяц
     * @param $y - год
     *
     * @return array
     */
    public function getDateInJ($d, $m, $y): array
    {
        $julian_days = cal_to_jd(CAL_GREGORIAN, $m, $d, $y); // переводим из ГР в Ю
        $jude_d = jdtojewish($julian_days); // переводим из Ю в ЕВР
        $date = explode('/', $jude_d);
        $date_j = [];

        $date_j['month'] = $date[0]; // ЕВР месяц
        $date_j['day'] = $date[1]; // ЕВР день
        $date_j['year'] = $date[2]; // ЕВР год

        return $date_j;
    }

    /**
     * Функция проверяет переданную еврейскую дату на наличие празника.
     * Вызывается из getClosestDate()
     *
     * @param array $date_j - еврейская дата
     *
     * @return bool
     */
    public function checkDay(array $date_j): bool
    {
        $dm = $date_j['day'] . '.' . $date_j['month'];
        if (array_key_exists($dm, $this->holidays_j)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Функция прибавляет к текущей дате определенное количество дней и возвращает дату в еврейском календаре
     *
     * @param int $days - сколько дней прибавляем?
     *
     * @return array
     */
    function getNextDayJ(int $days): array
    {
        $time = time() + (60 * 60 * 24 * $days);

        $d = date("d", $time);
        $m = date("m", $time);
        $y = date("Y", $time);

        return $this->getDateInJ($d, $m, $y);
    }

    /**
     * Функция находит ближайший праздник(число) включая сегодняшний день.
     *
     * @return array|false
     */
    public function getClosestDate()
    {
        $check_today = $this->checkDay($this->today_j); // проверяем сегодня на наличие праздника

        // если сегодня праздник, то возвращем сегодняшнее число
        if ($check_today) {
            return $this->today_j;
        }

        // иначе идем искать ближайшее
        $found = false; // флаг статуса поиска праздника
        $counter = 1; // счетчик дней
        do {
            $nextDay = $this->getNextDayJ($counter);
            if ($this->checkDay($nextDay)) {
                $found = $nextDay;
            } else {
                $counter++;
            }
            // если за целый год так ничего и не нашли - значит или наступил конец света, или нужна отладка
            if ($counter == 365) {
                break;
            }
        } while (!$found);

        return $found;
    }

    /**
     * Должна возвращать название праздника.
     *
     * @param array $date_j - массив c днём, месяцем и годом
     *
     * @return false|string
     */
    public function getHolidayNameByDate(array $date_j)
    {
        $dm = $date_j['day'] . '.' . $date_j['month'];

        if (array_key_exists($dm, $this->holidays_j)) {
            return $this->holidays_j[$dm];
        }
        return false;
    }

    /**
     * Возвращает строку с датами из массива.
     *
     * @param $name
     *
     * @return false|string
     */
    public function getHolidayPeriodByName($name)
    {
        if (array_key_exists($name, $this->holidays_j_period)) {
            return $this->holidays_j_period[$name];
        } else {
            return false;
        }
    }

    /**
     * Метод возвращает строковое представление о периоде праздника
     * в формате Григорианская дата / Еврейская дата
     *
     * @param string $period_string - еврейская дата
     *
     * @return string
     */
    public function formatPeriodString(string $period_string): string
    {
        $result = "";
        $d = explode('-', $period_string); // еврейская дата

        // debug
        // print_r("\nПериод: " . $period_string . "\n");

        if (isset($d[1])) { // больше одного элемента - период

            // парсим еврейскую дату
            $a1 = explode('.', $d[0]);
            $a2 = explode('.', $d[1]);

            $startHebrewDate = [
                'day' => $a1[0],
                'month' => $a1[1],
                'year' => $this->today_j['year'],
            ];

            $finishHebrewDate = [
                'day' => $a2[0],
                'month' => $a2[1],
                'year' => $this->today_j['year'],
            ];

            // узнаём православную дату в формате 03/28/2021
            $startNormalDate = $this->jewishToGregorian($startHebrewDate);
            $finishNormalDate = $this->jewishToGregorian($finishHebrewDate);
            // парсим её
            $b1 = explode('/', $startNormalDate);
            $b2 = explode('/', $finishNormalDate);

            // формируем переменные для вывода
            $startGregDay = $b1[1];
            $finishGregDay = $b2[1];
            $startGregMonth = $this->months_g[(int) $b1[0]];
            $finishGregMonth = $this->months_g[(int) $b2[0]];
            $gregYear = $b1[2];

            $startHebrewDay = $a1[0];
            $finishHebrewDay = $a2[0];
            $startHebrewMonth = $this->months_j[$a1[1]];
            $finishHebrewMonth = $this->months_j[$a2[1]];
            $hebrewYear = $this->today_j['year'];

            // первый элемент строки - григорианский вывод
            if ($startGregMonth == $finishGregMonth) {
                $result =
                    $startGregDay
                    . " - "
                    . $finishGregDay
                    . " "
                    . $startGregMonth
                    . " "
                    . $gregYear
                    . " / ";
            } else {
                $result .=
                    $startGregDay
                    . " "
                    . $startGregMonth
                    . " - "
                    . $finishGregDay
                    . " "
                    . $finishGregMonth
                    . " "
                    . $gregYear
                    . " / ";
            }

            // второй элемент строки - еврейский вывод
            if ($startHebrewMonth == $finishHebrewMonth) { // если стартовый и конечный месяцы совпадают
                $result .=
                    $startHebrewDay
                    . " - "
                    . $finishHebrewDay
                    . " "
                    . $startHebrewMonth
                    . " "
                    . $hebrewYear;
            } else { // если начинается в одном месяце, а заканчивается в другом
                $result .=
                    $startHebrewDay
                    . " "
                    . $startHebrewMonth
                    . " - "
                    . $finishHebrewDay
                    . " "
                    . $finishHebrewMonth
                    . " "
                    . $hebrewYear;
            }
        } else { // если не период, то выводим просто дату

            $a = explode('.', $d[0]);

            $hebrewDate = [
                'day' => $a[0],
                'month' => $a[1],
                'year' => $this->today_j['year'],
            ];

            $normalDate = $this->jewishToGregorian($hebrewDate);
            $b = explode('/', $normalDate);

            $result =
                $b[1]
                . " "
                . $this->months_g[(int) $b[0]]
                . $b[2]
                . " / "
                .
                $a[0]
                . " "
                . $this->months_j[$a[1]]
                . " "
                . $this->today_j['year'];
        }

        return $result;
    }

    /**
     * Переводит еврейскую дату в григорианскую в формате 01/31/2020.
     *
     * @param array $date_j - массив с еврейской датой
     *
     * @return false|string
     */
    public function jewishToGregorian(array $date_j)
    {
        $julian = cal_to_jd(CAL_JEWISH, $date_j['month'], $date_j['day'], $date_j['year']);
        $gregorian = jdtogregorian($julian);
        $holiday_date_g = date("m/d/Y", strtotime($gregorian));

        return $holiday_date_g;
    }

    /**
     * Склоняет название праздника на основании
     * значений из массива $this->holidays_j_to_case
     *
     * @param string $name - название праздника
     *
     * @return string - родительный падеж от названия праздника
     */
    public function toCase(string $name): string
    {
        return $this->holidays_j_to_case[$name];
    }

} // конец класса

$jewishHolidays = new JewishHolidays(); // создаём экземпляр класса
$closestDate = $jewishHolidays->getClosestDate(); // получаем дату ближайшего праздника

$closest_holiday = [];
$closest_holiday['day'] = $closestDate['day'];
$closest_holiday['month'] = $closestDate['month'];
$closest_holiday['year'] = $closestDate['year'];

$closest_holiday['name'] = $jewishHolidays->getHolidayNameByDate($closest_holiday);
$closest_holiday['period'] = $jewishHolidays->getHolidayPeriodByName($closest_holiday['name']);
$closest_holiday['period'] = $jewishHolidays->formatPeriodString($closest_holiday['period']);
$closest_holiday['gregorian'] = $jewishHolidays->jewishToGregorian($closest_holiday);
$closest_holiday['name_to_case'] = $jewishHolidays->toCase($closest_holiday['name']);
