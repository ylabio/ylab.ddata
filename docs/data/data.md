# Доступные схемы и генераторы данных

- [Создание кастомных схем данных](data-scheme.md)
- [Создание кастомных генераторов данных](data-value.md)

#### Доступные схемы данных
| Схема                       | ID                     | TYPE                   | CLASS                                   | 
|-----------------------------|------------------------|------------------------|-----------------------------------------| 
| Элемент торгового каталога  | catalog-element        | catalog                | Ylab\Ddata\Entity\CatalogElement        | 
| Highload-block              | highloadblock-element  | highloadblock-element  | Ylab\Ddata\Entity\HighloadblockElement  | 
| Элемент инфоблока           | iblock-element         | iblock                 | Ylab\Ddata\Entity\IblockElement         | 
| Элемент ORM-таблицы         | orm                    | orm                    | Ylab\Ddata\Entity\Orm                   | 
| Пользователь                | user                   | user                   | Ylab\Ddata\Entity\User                  | 


#### Доступные генераторы данных
| Генератор                                                                 | ID                          | TYPE            | CLASS                                | 
|---------------------------------------------------------------------------|-----------------------------|-----------------|--------------------------------------| 
| Генерация случайного значения checkbox (Да, Нет)                          | checkbox.unit               | checkbox        | Ylab\Ddata\Data\RandomCheckbox       | 
| Генерация случайной валюты, доступной в торговом каталоге                 | currency.unit               | currency        | Ylab\Ddata\data\RandomCurrency       | 
| Генерация случайной даты                                                  | datetime.unit               | datetime        | Ylab\Ddata\Data\RandomDateTime       | 
| Генератор справочника инфоблока                                           | dictionary.iblock           | dictionary      | Ylab\Ddata\data\RandomDictionaryHL   | 
| Генерация случайного значения из доступного списка                        | enum.unit                   | enum            | Ylab\Ddata\Data\RandomEnum           | 
| Генерация случайного значения из доступного списка сущности ХайлоадБлока  | enum.hl.unit                | enum.hl         | Ylab\Ddata\data\RandomEnumHL         | 
| Генерация случайного изображения                                          | picture.file.unit           | file            | Ylab\Ddata\Data\RandomPicture        | 
| Генерация случайного файла                                                | file.unit                   | file            | Ylab\Ddata\Data\RandomFile           | 
| Генерация изображения для ORM                                             | picture.file.unit.id        | file.orm        | Ylab\Ddata\Data\RandomOrmPicture     | 
| Генерация файла для ORM                                                   | file.unit.id                | file.orm        | Ylab\Ddata\Data\RandomOrmFile        | 
| Генерация случайного дробного числа                                       | random.float.unit           | float           | Ylab\Ddata\Data\RandomFloat          | 
| Генератор привязки к элементам highload-блоков                            | hl.element                  | hl.element      | Ylab\Ddata\data\RandomHLElement      | 
| Генерация случайного элемента инфоблока                                   | random.iblock.element.unit  | iblock.element  | Ylab\Ddata\Data\RandomIBlockElement  | 
| Генерация случайного элемента списка                                      | iblock.list                 | iblock.list     | Ylab\Ddata\Data\RandomIBlockList     | 
| Генерация случайной категории инфоблока                                   | iblock.section              | iblock.section  | Ylab\Ddata\Data\RandomIBlockSection  | 
| Генератор случайного номера мобильного телефона                           | random.mobilenumber.unit    | integer         | Ylab\Ddata\Data\RandomMobileNumber   | 
| Генерация случайного числа                                                | random.integer.unit         | integer         | Ylab\Ddata\Data\RandomInteger        | 
| Генерация текста Lorem Ipsum                                              | random.loremipsum.unit      | string          | Ylab\Ddata\Data\RandomLoremIpsum     | 
| Генерация случайного логина                                               | login.string.unit           | string          | Ylab\Ddata\Data\RandomLogin          | 
| Генерация случайного e-mail                                               | email.string.unit           | string          | Ylab\Ddata\Data\RandomEmail          | 
| Генерация случайного пароля                                               | password.unit               | string          | Ylab\Ddata\Data\RandomPassword       | 
| Генерация строки по шаблону                                               | random.patternstring.unit   | string          | Ylab\Ddata\Data\RandomPatternString  | 
| Генерация строки из справочника                                           | string.dictionary.unit      | string          | Ylab\Ddata\data\RandomDictionary     | 
| Генерация случайной строки                                                | random.string.unit          | string          | Ylab\Ddata\Data\RandomString         | 
| Случайный ID пользователя                                                 | user.unit                   | user            | Ylab\Ddata\Data\RandomUser           | 
| Генерация случайной страны                                                | user.country                | user.country    | Ylab\Ddata\Data\RandomUserCountry    | 
| Генерация случайного значения пола (Мужской, Женский)                     | user.gender                 | user.gender     | Ylab\Ddata\Data\RandomUserGender     | 
| Генерация пользовательской группы                                         | user.group                  | user.group      | Ylab\Ddata\Data\RandomUserGroup      | 
 
