# Создание кастомных схем данных

Обязательным требованием к классу является наследование абстрактного класса `\Ylab\Ddata\Interfaces\EntityUnitClass`</br>
и реализация всех абстрактных методов.

Включение класса в модуль возможно через событие [OnAfterLoadEntityUnits](../events/OnAfterLoadEntityUnits.md).

#### Конструктор класса
Для создания нового профиля конструктор вызывается без параметров. В случае загрузки профиля в параметре будет</br>
передан идентификатор профиля. Вызов родительского конструктора обязателен, в нем инициализируется поле $profile,</br>
в поле будет записана информация о профиле и его настройки.
```php
    /**
     * @var array Запись профиля с параметрами
     */
    public $profile;
    
    /**
     * CatalogElement constructor.
     *
     * @param $iProfileID
     */
    public function __construct($iProfileID = false)
    {
        parent::__construct($iProfileID);
    }
```

#### getDescription - Метод возвращает описывающий массив
Метод возвращает описывающий массив, все поля, кроме 'DESCRIPTION', обязательны.</br>
Поле 'ID' содержит уникальную строку - идентификатор схемы данных.</br>
Поля 'ID' и 'TYPE' могут содержать одно и то же значение.
```php
    /**
     * Метод возвращает описывающий массив
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'new-entity-unit',
            'NAME' => 'Новая схема данных',
            'DESCRIPTION' => 'Новая схема данных',
            'TYPE' => 'new-data-scheme',
            'CLASS' => __CLASS__
        ];
    }
```

#### getPrepareForm - Метод возвращает html строку с полями предварительной настройки сущности
Если для схемы необходимы настройки, например, имя таблицы или id инфоблока и пр., в методе можно</br>
сформировать форму для настройки схемы данных.
```php
    /**
     * Метод возвращает html строку с полями предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     *
     * @return string
     */
    public function getPrepareForm(HttpRequest $oRequest)
    {
        // TODO: Implement getPrepareForm() method.
    }
```

#### isValidPrepareForm - Метод проверяет на валидность данные из предварительной настройки сущности
Метод вызывается после отправки формы предварительной настройки,</br>
если у схемы нет настроек, метод должен возвращать всегда true
```php
    /**
     * Метод проверяет на валидность данные из предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     *
     * @return boolean
     */
    public function isValidPrepareForm(HttpRequest $oRequest)
    {
        // TODO: Implement isValidPrepareForm() method.
    }
```

#### getFields - Метод возвращает массив полей и свойств сущности
Метод возвращает массив, описывающий структуру данных. Поле 'PROPERTIES' заполняется аналогично 'FIELDS'</br>
и существует только для визуального разделения полей на отдельные вкладки. 
```php
    /**
     * Метод возвращает массив полей и свойств сущности
     *
     * @param HttpRequest $oRequest
     *
     * @return array
     */
    public function getFields(HttpRequest $oRequest)
    {
        $arFields = [
            'FIELDS' => [
                '#FIELD_CODE#' => [//Код поля
                    'type' => ['',...,''],//Массив типов данных которые может принимать поле
                    'title' => '',//Наименование поля
                    'required' => true/false,//Признак обязательности заполнения поля
                    'multiple' => true/false,//Признак множественного заполнения поля
                    'group-id' => ''//Идентификатор группы
                ]
            ],
            'PROPERTIES' => [],//Наполнение аналогично FIELDS
            'GROUPS_NAME' => [
                '#GROUP_ID#' => ''//Ключ идентификатор группы, значение наименование группы
            ],
        ];
        // TODO: Implement getFields() method.
    }
```

#### genUnit - Записывает в базу 1 экземпляр сгенерированной сущности
Метод реализует запись 1 экземпляра данных. Метод должен вернуть массив, содержащий поля:</br>
'NEW_ELEMENT_ID' - В случае успеха содержит идентификатор записи</br>
'ERROR' - В случае ошибки строку с описанием
```php
    /**
     * Записывает в базу 1 экземляр сгенерированной сущности
     *
     * @return array
     */
    public function genUnit()
    {
        // TODO: Implement genUnit() method.
    }
```

#### deleteGenData - Удаление сгенерированных данных
Метод реализует очистку сгенерированных демо данных, когда в них пропадает необходимость.
```php
    /**
     * Удаление сгенерированных данных
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function deleteGenData()
    {
        $arGenData = $this->getGenData();//Получение списка с идентификаторами сгенерированных записей
        // TODO: Implement deleteGenData() method.
    }
```