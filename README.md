## Bitrix модуль генерации демо данных

Модуль ylab.ddata реализует генерацию сущностей Bitrix, заполненных случайными данными. Модуль поможет при отладке
публичной части сайта, API и в других ситуациях, когда необходимы сущности (ИБ, HL, Пользователи), заполненные тестовыми
данными.

Генерация производится на основе преднастроенного профиля в административном разделе.
Настройка профиля производится в разделе `Рабочий стол  -> Сервисы -> Ylab Демо данные -> Профили сущностей`.

[Примеры использования модуля](docs/usecase/usecase.md) 

[Доступные схемы данных](docs/data/data.md)

## Документация
- [Документация](docs/docs.md)

## Требования
1. Требуется версия 1С-Битрикс от 17.0.0.
2. Требуется версия PHP >= 7.0

## Установка
```
$ cd local/modules
$ git clone https://github.com/ylabio/ylab.ddata.git
```
В папку `local/modules` будет склонирован репозиторий модуля, после этого в панели администратора необходимо установить
модуль: `Рабочий стол -> Marketplace -> Установленные решения`.

Ведущие разработчики: 

- [Alexandr Zemlyanoy (Galamoon)](https://github.com/Galamoon)
- [Roman Shvykov (shvykov)](https://github.com/shvykov)

Разработчики:

- [Vladimir Admaev (vladimiradmaev)](https://github.com/vladimiradmaev)
- [Lev Dmitriev (LevDmitriev)](https://github.com/LevDmitriev)
- [Anton (ivanovQQQ)](https://github.com/ivanovQQQ)

