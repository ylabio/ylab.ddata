# Событие OnAfterLoadDataUnits

Обязательным требованием к классу является наследование абстрактного класса `\Ylab\Ddata\Interfaces\DataUnitClass`</br>
и реализация всех абстрактных методов. [Подробнее о реализации класса генератора](../data/data-value.md)

```php
    \Bitrix\Main\Loader::includeModule("ylab.ddata");
    
    AddEventHandler("ylab.ddata", "OnAfterLoadDataUnits", Array("MyClass", "OnAfterLoadDataUnitsHandler"));
    
    class MyClass extends \Ylab\Ddata\Interfaces\DataUnitClass
    {
        /**
         * Метод возвращает полный путь до класса
         */
        function OnAfterLoadDataUnitsHandler()
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
                'ID' => 'unique.id.data.unit.class',//Уникальная строка id генератора
                'NAME' => 'Кастомный генератор строки', //Имя генератора
                'DESCRIPTION' => 'Кастомный генератор строки',//Описание генератора
                'TYPE' => 'string',//Тип возвращаемых данных
                'CLASS' => __CLASS__ //Путь до класса генератора
            ];
        }
    
        /**
         * Метод getOptionForm возвращает html строку формы с настройкой генератора если таковые необходимы
         *
         * @param HttpRequest $request
         *
         * @return mixed
         */
        public function getOptionForm(HttpRequest $request)
        {
            // TODO: Implement getOptionForm() method.
        }
    
        /**
         * Метод isValidateOptions проверяет на валидность данные настройки генератора
         *
         * @param HttpRequest $request
         *
         * @return mixed
         */
        public function isValidateOptions(HttpRequest $request)
        {
            // TODO: Implement isValidateOptions() method.
        }
    
        /**
         * Метод getValue возвращает случайную запись соответствующего типа
         *
         * @return mixed
         */
        public function getValue()
        {
            // TODO: Implement getValue() method.
        }
    }
```