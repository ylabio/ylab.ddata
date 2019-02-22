# Создание кастомных генераторов данных

Обязательным требованием к классу является наследование абстрактного класса `\Ylab\Ddata\Interfaces\DataUnitClass`</br>
и реализация всех абстрактных методов.

Включение класса в модуль возможно через событие [OnAfterLoadDataUnits](../events/OnAfterLoadDataUnits.md).

#### Конструктор класса
```php
    /**
     * RandomCheckbox constructor.
     *
     * @param $sProfileID   - ID профиля
     * @param $sFieldCode   - Символьный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);
    }
```

#### getDescription - Метод возвращает массив, описывающий тип данных. ID, Имя, Тип данных
Все поля, кроме 'DESCRIPTION', являются обязательными. По значению поля 'TYPE' генераторы сопоставляются с полями схемы данных, для</br>
которых этот генератор будет доступен.
```php
    /**
     * Метод возвращает массив, описывающий тип данных. ID, Имя, Тип данных
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => '',
            'NAME' => '',
            'DESCRIPTION' => '',
            'TYPE' => '',
            'CLASS' => __CLASS__
        ];
    }
```

#### getOptionForm - Метод возвращает html строку формы с настройкой генератора, если таковые необходимы

```php
    /**
     * Метод возвращает html строку формы с настройкой генератора, если таковые необходимы
     *
     * @param HttpRequest $request
     *
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        // TODO: Implement getOptionForm() method.
    }
```

#### isValidateOptions - Метод проверяет на валидность данные из формы настройки генератора
Если у генератора настроек нет, метод должен всегда возвращать true
```php
    /**
     * Метод проверяет на валидность данные из формы настройки генератора
     *
     * @param HttpRequest $request
     *
     * @return mixed
     */
    public function isValidateOptions(HttpRequest $request)
    {
        // TODO: Implement isValidateOptions() method.
    }
```

#### getValue - Метод возвращает случайные данные соответствующего типа

```php
    /**
     * Метод возвращает случайные данные соответствующего типа
     *
     * @return mixed
     */
    public function getValue()
    {
        // TODO: Implement getValue() method.
    }
```