# Событие OnAfterLoadEntityUnits

Событие "OnAfterLoadEntityUnits" вызывается при загрузке списка доступных сущностей для генерации данных.

Обязательным требованием к классу является наследование абстрактного класса `\Ylab\Ddata\Interfaces\EntityUnitClass`</br>
и реализация всех абстрактных методов. [Подробнее о реализации класса сущности](../data/data-scheme.md)

### Пример регистрации обработчика
~~~php
    \Bitrix\Main\Loader::includeModule("ylab.ddata");
    AddEventHandler("ylab.ddata", "OnAfterLoadEntityUnits", Array("MyClass", "OnAfterLoadEntityUnitsHandler"));
    
    class MyClass extends \Ylab\Ddata\Interfaces\EntityUnitClass
    {
        /**
         * Метод возвращает полный путь до класса
         */
        function OnAfterLoadEntityUnitsHandler()
        {
            return __CLASS__;
        }
    
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
    
        /**
         * Метод проверяет на валидность данные  предварительной настройки сущности
         *
         * @param HttpRequest $oRequest
         *
         * @return boolean
         */
        public function isValidPrepareForm(HttpRequest $oRequest)
        {
            // TODO: Implement isValidPrepareForm() method.
        }
    
        /**
         * Записывает в базу 1 экземляр сгенерированной сущности
         *
         * @return mixed
         */
        public function genUnit()
        {
            // TODO: Implement genUnit() method.
        }
    
        /**
         * Метод возвращает массив полей и свойств сущности
         *
         * @param HttpRequest $oRequest
         *
         * @return array
         */
        public function getFields(HttpRequest $oRequest)
        {
            // TODO: Implement getFields() method.
        }
    
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
            // TODO: Implement deleteGenData() method.
        }
    }
~~~