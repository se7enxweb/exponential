# Ukrainian (ukr-UA): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Ukrainian, neutral and technical, as in current Ukrainian software, following the current orthography
(Український правопис, 2019).

No other locale falls back to `ukr-UA`. Its own fallback is `untranslated` (`settings/i18n.ini`,
`FallbackLanguages[ukr-UA]=untranslated`): an extension without a Ukrainian catalogue shows its English source
texts, never another language. Write Ukrainian vocabulary and syntax, not words and constructions taken over from
Russian (see section 3).

## 2. Form of address

The user is addressed formally with **ви**, everywhere.

- **Buttons, menu items and links**: the infinitive, mostly perfective. "Зберегти", "Скасувати", "Вилучити",
  "Редагувати", "Вилучити вибране", "Зберегти чернетку", "Очистити кошик", "Надіслати на публікацію". Not the
  imperative ("Збережіть").
- **Titles, column headings and tabs**: nouns. "Налаштування", "Переклади", "Головне розташування", "Стани
  об'єктів".
- **Requests and instructions**: the imperative plural, usually without "будь ласка": "Виберіть розташування.",
  "Введіть дійсну адресу електронної пошти.", "Перевірте з'єднання й спробуйте ще раз."
- **Confirmations**: "Ви справді хочете ...?": "Ви справді хочете вилучити мову?". "Ви впевнені, що ...?" is also
  correct.
- **Statements about the user**: ви, вас, ваш: "Адреса електронної пошти вашого облікового запису змінюється",
  "У вас немає дозволу на доступ до <%siteaccess_name>." Where the system is the subject, an impersonal form is
  fine: "Не можна вилучити останній клас ...", "Не вдалося запустити cronjob."
- **ви** is written in lower case inside a sentence; a capital Ви only at the start of a sentence.
- Not allowed: ти, тебе, твій; the imperative on buttons; mixing ви and the infinitive in one instruction.

## 3. Script and typography

- Ukrainian Cyrillic, with і, ї, є and ґ where the word has them. No Russian letters (ы, э, ъ, ё).
- **Apostrophe**: the straight apostrophe ', as in the rest of the catalogue: "об'єкт", "зв'язки", "з'єднання".
  Not ’ or ʼ, so that searching the catalogue finds every form.
- **Quotation marks**: Ukrainian angle quotes «...»: «%name». Straight quotes only where the source quotes code or a
  value that is typed exactly.
- **Dashes**: the em dash with spaces — in running text.
- **No space** before ":", ";", "?" and "!".
- **Counts**: Ukrainian has three plural forms and the catalogue only one translation for a count. Phrase counts as
  "label: %count" ("Локалей із налаштованою резервною мовою: %count") so that every number reads correctly.
- **Numbers**: digits 0-9, decimals with a comma in prose ("1,5 МБ").
- **Capitals**: only the first word of a button, a title or a heading starts with a capital. No title case.

## 4. Glossary

Binding for the kernel and every extension.

| English | Ukrainian | Note |
|---|---|---|
| content | вміст | not контент |
| content object | об'єкт вмісту | |
| object | об'єкт | |
| node | вузол | |
| location | розташування | the place of an object in the tree |
| main location | головне розташування | |
| site | сайт | |
| class | клас | the content class; a PHP class is клас PHP |
| content class | клас вмісту | |
| class group | група класів | |
| attribute | атрибут | |
| datatype | тип даних | |
| identifier | ідентифікатор | |
| remote ID | віддалений ID | |
| version | версія | |
| draft | чернетка | |
| publish | опублікувати | the noun: публікація |
| published | опубліковано | |
| send for publishing | надіслати на публікацію | |
| store draft | зберегти чернетку | |
| discard | відхилити | discard draft: відхилити чернетку |
| archived | архівовано | an archived version |
| pending | очікує | |
| rejected | відхилено | |
| siteaccess | доступ до сайту | where it is a setting or code, it stays as it is |
| section | розділ | always the Exponential section |
| role | роль | |
| policy | політика | |
| limitation | обмеження | |
| module | модуль | |
| function | функція | |
| permission | права доступу | |
| user | користувач | |
| user group | група користувачів | |
| user account | обліковий запис | |
| log in | увійти | the field Login: логін |
| username | ім'я користувача | |
| log out | вийти | |
| password | пароль | |
| e-mail | електронна пошта | e-mail address: адреса електронної пошти |
| cache | кеш | clear cache: очистити кеш |
| template | шаблон | |
| override | перевизначення | |
| fallback | резервний | fallback language: резервна мова |
| workflow | робочий процес | |
| trigger | тригер | |
| event | подія | |
| trash | кошик | empty trash: очистити кошик |
| bookmarks | закладки | |
| settings | налаштування | |
| configuration | конфігурація | |
| setup | налаштування | the Setup tab |
| dashboard | панель керування | |
| layout | макет | Exponential Layouts; layouts: макети |
| block | блок | |
| zone | зона | |
| design | дизайн | |
| extension | розширення | |
| package | пакет | |
| kernel | ядро | |
| search | пошук | the verb: шукати |
| search index | пошуковий індекс | indexing: індексування |
| search engine | пошукова система | |
| log | журнал | a log file |
| queue | черга | queued: у черзі |
| job | завдання | |
| cronjob | завдання cron | |
| session | сеанс | |
| worker | обробник | |
| file | файл | |
| folder | папка | |
| image | зображення | |
| thumbnail | мініатюра | |
| media | медіа | media library: медіатека |
| preview | попередній перегляд | |
| upload | завантажити | |
| download | завантажити | |
| export | експорт | the button: експортувати |
| import | імпорт | the button: імпортувати |
| remove | вилучити | take out of a list or place |
| delete | видалити | destroy |
| edit | редагувати | |
| editor | редактор | |
| save / store | зберегти | the verb "Store" of the forms too |
| Store / shop | магазин | webshop: інтернет-магазин |
| cancel | скасувати | |
| OK | гаразд | |
| back | назад | |
| next / previous | далі / назад | |
| enable / disable | увімкнути / вимкнути | |
| enabled / disabled | увімкнено / вимкнено | |
| active / inactive | активний / неактивний | |
| hidden / visible | приховано / видимий | hide: приховати |
| apply | застосувати | |
| reset | скинути | |
| clear | очистити | |
| archive | архів | |
| order (sort) | порядок | sort by: сортувати за |
| order (shop) | замовлення | |
| ascending / descending | за зростанням / за спаданням | |
| basket | кошик | |
| wish list | список бажань | |
| product | товар | |
| customer | клієнт | |
| discount | знижка | discount group: група знижок |
| VAT | ПДВ | |
| tag (keyword) | тег | eztags |
| tag (HTML/XML) | тег | тег HTML |
| translation | переклад | |
| language | мова | main language: основна мова |
| locale | локаль | |
| state | стан | the object state; object states: стани об'єктів |
| status | стан | статус is also correct where стан could be read as the object state |
| view (button) | переглянути | |
| view (noun) | подання | a module view |
| format | формат | |
| select | вибрати | invert selection: інвертувати вибір |
| browse | переглянути | |
| copy | копіювати | |
| move | перемістити | |
| add | додати | |
| create | створити | created: створено |
| new | новий | |
| restore | відновити | |
| install / uninstall | встановити / видалити | |
| sub items | підлеглі елементи | |
| children / parent | дочірні / батьківський | |
| subtree | піддерево | |
| related objects | пов'язані об'єкти | relations: зв'язки |
| URL alias | псевдонім URL | |
| content structure | структура вмісту | |
| owner | власник | |
| creator / author | автор | |
| modified | змінено | |
| priority | пріоритет | |
| debug | налагодження | |
| default | типово | as an adjective: типовий; not за замовчуванням |
| none | немає | |
| yes / no | так / ні | |
| help | довідка | |

## 5. Decisions after the translation

- **remove and delete** are kept apart: вилучити takes something out of a list or place, видалити destroys. The
  button "Remove" is Вилучити, the button "Delete" is Видалити.
- **upload and download** are both завантажити, as in current Ukrainian software; the context (choosing a file on
  your computer, or receiving one) makes the direction clear. Where both stand in one text, name the direction:
  "завантажити на сервер".
- **Order** in the shop is замовлення, also as a column heading in the shop; порядок is only the sort order.
- **trash and basket** are both кошик in Ukrainian software. They appear in different parts of the administration;
  where both could be meant, the shop basket is кошик покупок.
