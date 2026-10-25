# Russian (rus-RU): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated and cross-checked in October 2026; review by native speakers is welcome.

## 1. Language and register

Standard Russian as used in Russia (Cyrillic, reformed orthography of 1956/2006). The tone is neutral, concise and
professional, as in Microsoft Windows/Office and Yandex/VK business products; terminology follows the Microsoft Russian
terminology where it has a term, KDE/GNOME/LibreOffice Russian where it has none. No slang, no jargon calques where a
Russian word is established (not "дисплейте", not "кликнуть" - use "щелкнуть"). Belarusian, Kazakh and other countries
that write Russian fall back to this catalogue.

**The old catalogue** (5,352 of 6,836 messages finished, hand-made, 2000s) is the `prev` hint. Its conventions that
stay: infinitive buttons (Сохранить, Удалить, Создать, Применить), lower-case "вы" (198 uses, 5 with capital letter),
"Узел", "Класс", "Версия", "Черновик", "Корзина", "Роль", "Шаблон", "Объект". Corrected where the hint shows them:

| Old | New | Why |
|---|---|---|
| Сайт for siteaccess | доступ к сайту | siteaccess is a site configuration, not "site" |
| Установки for settings | Настройки | current word |
| Правило for policy | правило доступа | "rule" alone is ambiguous in a permission UI |
| Замещение for override | переопределение | |
| Расположение for location | расположение (kept) | |
| Корзина for both trash and shop basket | корзина for both, the shop's heading "Корзина покупок" | section 5 |
| Магазин for Store (button) | Сохранить | Store is the verb except in the admin header (section 5) |
| ё anywhere | е | section 3 |

## 2. Form of address

The user is addressed with lower-case **вы** (вам, вас, ваш), never "ты" and not capitalised "Вы" inside a sentence
(Microsoft and Yandex practice; the old catalogue agrees 198 to 5). A capital Вы only at the start of a sentence.
The pronoun is dropped wherever the sentence allows.

- **Buttons, menu items, links and action labels** are the **infinitive**: Сохранить, Изменить, Удалить, Убрать,
  Добавить, Создать, Применить, Опубликовать, Скачать, Загрузить, Закрыть. Exceptions in the established form of the
  dialog: ОК, Отмена, Да, Нет, Назад, Далее, Готово.
- **Titles, column headings and tabs** are nouns: Структура содержимого, Учетные записи, Параметры уведомлений.
- **Instructions in sentences** use the plural/polite imperative: "Выберите расположение.", "Введите допустимый
  адрес электронной почты.", "Проверьте подключение и повторите попытку." "Please" is not translated ("пожалуйста" only
  where the source is the polite request of a dialog).
- **Questions and confirmations**: "Вы действительно хотите удалить этот класс?"; short form "Удалить «%name»?".
- **Error messages** state the fact without blaming: "Не удалось создать черновик.", "Файл не найден.", "Недостаточно
  прав для удаления класса."
- **Status messages**: completed actions with the past participle: "Черновик удален.", "Настройки сохранены.",
  "Удалено объектов: %count."
- Not allowed: ты/тебя; "Вы" with capital inside a sentence; exclamation marks the source does not have; English
  words in a sentence except what TRANSLATING.md rule 5 keeps.

## 3. Script and typography

- **Alphabet**: Russian Cyrillic only. A Latin look-alike letter inside a Cyrillic word (a, o, e, p, c, x, y ...) is an
  error and is reported by `scripts/locale_check.py` (`script`). Product names, codes, file names, formats and
  abbreviations stay Latin (Exponential, Solr, PHP, URL, JSON, cron, INI).
- **ё**: written **е** throughout (Microsoft and KDE practice: учетная запись, все, еще). Only "всё" (everything, to
  keep it from "все", all) and "ещё" are allowed with ё; `locale_check.py` reports any other (`yo`).
- **Quotation marks**: «…» (nested „…“): Нажмите «Сохранить». Straight quotes of the source stay inside HTML
  attributes, code and values typed exactly.
- **Dash**: the long dash — with spaces ("Узел — основной"); a hyphen only in compounds and after Latin words
  (URL-адрес, INI-файл, Solr-индекс, Node-ID). Not " - ".
- **Punctuation**: no space before , . ; : ! ?; a colon after a label as in the source; full stop at the end of
  sentences. The ellipsis is the source's ("..." stays "...", "…" stays "…").
- **Case**: sentence case for buttons, titles and tabs ("Структура содержимого", "Создать объект"), as in Microsoft
  Russian. Only the first word and proper names are capital.
- **Numbers and units**: digits 0-9; a space before a unit (10 МБ, 5 с), no space before %. Decimal separator and dates
  come from the locale.
- **Plural**: Russian has three forms (1 файл, 2 файла, 5 файлов). A placeholder count never gets a hand-made plural;
  use the label form "Файлов: %count" or the neutral "файл(ов)" only where the source has "(s)". Prefer "Объектов: %count".
- **Placeholders** stand free, with a space on each side (TRANSLATING.md, rule 1); a Latin letter directly behind one
  makes it a different placeholder. A Cyrillic ending glued to a placeholder ("%countов") is wrong for the same reason.

## 4. Glossary

Binding for the kernel and every extension. Section 5 adds the settled special cases and goes first.

| English | Russian | Note |
|---|---|---|
| content | содержимое | |
| content object / object | объект содержимого / объект | |
| item (list entry) | элемент | sub items: дочерние элементы |
| node / main node | узел / основной узел | |
| parent node / child node | родительский узел / дочерний узел | |
| location / main location | расположение / основное расположение | |
| content class / class | класс содержимого / класс | a PHP class: класс PHP |
| class group | группа классов | |
| attribute | атрибут | |
| datatype | тип данных | |
| version / version history | версия / журнал версий | |
| draft | черновик | Store draft: Сохранить черновик; Discard draft: Отказаться от черновика |
| publish / published | опубликовать / опубликовано | Send for publishing: Отправить на публикацию |
| translation | перевод | |
| language / main language / content language | язык / основной язык / язык содержимого | |
| interface language | язык интерфейса | |
| siteaccess | доступ к сайту | the INI word SiteAccess stays in code |
| site / website | сайт | |
| design | оформление | the design of a site; the Exponential design: дизайн |
| template | шаблон | |
| override / override template | переопределение / переопределяющий шаблон | overridden: переопределено |
| fallback (language, design) | резервный | fallback language: резервный язык |
| URL alias / URL wildcard | псевдоним URL / подстановочный URL | |
| redirect | перенаправление | |
| section (Exponential) | секция | a part of a page: раздел |
| object state / state group | состояние объекта / группа состояний | |
| status | статус | |
| trash / empty trash | корзина / очистить корзину | |
| restore | восстановить | |
| bookmark | закладка | |
| notification | уведомление | |
| dashboard | панель мониторинга | |
| content structure / media library | структура содержимого / медиатека | |
| collected information | собранная информация | |
| form | форма | |
| poll / vote | опрос / голосование | |
| comment | комментарий | in code: примечание |
| newsletter / subscription | рассылка / подписка | |
| feed / RSS feed | лента / RSS-лента | |
| user / user group | пользователь / группа пользователей | |
| user account / account | учетная запись | |
| username / login (the field) | имя пользователя | |
| password | пароль | |
| anonymous user | анонимный пользователь | |
| administrator / admin interface | администратор / интерфейс администратора | |
| log in / log out | войти / выйти | not "Логин" for the verb; Login as a field: Имя пользователя |
| sign up, register | зарегистрироваться | |
| role / policy | роль / правило доступа | |
| permission / access | разрешение / доступ | |
| access denied | доступ запрещен | |
| limitation (policy) | ограничение | |
| assign / unassign | назначить / снять назначение | |
| approve / reject / approval | утвердить / отклонить / утверждение | |
| pending | ожидает | |
| owner / author / creator | владелец / автор / создатель | |
| editor (person) / editor (software) | редактор / редактор | rich text editor: визуальный редактор |
| save / store (button) | сохранить | |
| edit | изменить (button: Изменить) | as Microsoft, decided 25.10.2026; the noun editing = редактирование |
| delete / remove | удалить / убрать | remove = take out of a list or place; delete = destroy |
| cancel / OK / apply | отмена / ОК / применить | |
| discard | отменить | |
| create / new / add | создать / новый / добавить | |
| copy / move / swap | копировать / переместить / поменять местами | |
| back / next / previous | назад / далее / предыдущий | pager: Назад / Вперед |
| open / close | открыть / закрыть | |
| select / choose | выбрать | select all: выбрать все |
| browse | обзор | |
| preview | предварительный просмотр | |
| view (button) / view (module view) | просмотреть / представление | |
| upload / download | загрузить / скачать | |
| import / export | импортировать / экспортировать | |
| search / advanced search | поиск / расширенный поиск | |
| filter | фильтр | verb: отфильтровать |
| sort / sort order | сортировка / порядок сортировки | |
| refresh / reload | обновить / перезагрузить | |
| enable / disable | включить / отключить | |
| activate / deactivate | активировать / деактивировать | |
| install / uninstall | установить / удалить | |
| update / upgrade | обновить / обновление версии | |
| click / double-click | щелкнуть / дважды щелкнуть | |
| drag / drop | перетащить / отпустить | |
| print | печать | |
| retry | повторить | |
| file | файл | |
| folder / directory | папка / каталог | |
| path / file system | путь / файловая система | |
| binary file | двоичный файл | |
| image / image alias | изображение / псевдоним изображения | |
| video / audio | видео / аудио | |
| font | шрифт | |
| database / table (database) | база данных / таблица | |
| field | поле | |
| column / row | столбец / строка | |
| data / information | данные / информация | |
| message | сообщение | |
| cache / clear cache | кэш / очистить кэш | |
| index / reindex | индекс / переиндексировать | |
| search engine | поисковая система | |
| keyword | ключевое слово | |
| tag (keyword) / tag (HTML) | тег / HTML-тег | |
| format / formatting | формат / форматирование | |
| settings / setup | параметры / установка | as Microsoft (Параметры); Setup (admin tab): Настройка; decided 25.10.2026 |
| configuration | конфигурация | |
| INI file / INI setting | INI-файл / параметр INI | |
| default | по умолчанию | |
| cron job | задание cron | |
| package | пакет | |
| extension | расширение | |
| plugin / provider | плагин / провайдер | |
| server / web server | сервер / веб-сервер | |
| network | сеть | |
| software / program | программное обеспечение / программа | |
| code / source code | код / исходный код | |
| function / method | функция / метод | |
| variable / parameter | переменная / параметр | |
| string / character | строка / символ | |
| encoding | кодировка | |
| interface (UI) / interface (PHP) | интерфейс | |
| module | модуль | |
| link / broken link | ссылка / неработающая ссылка | |
| menu / context menu | меню / контекстное меню | |
| toolbar / sidebar | панель инструментов / боковая панель | |
| tab | вкладка | |
| page / layout | страница / макет | Exponential Layouts: макеты |
| block / zone | блок / зона | |
| navigation / breadcrumb | навигация / цепочка навигации | |
| icon / button | значок / кнопка | |
| checkbox / drop-down list / list | флажок / раскрывающийся список / список | |
| dialog / wizard | диалоговое окно / мастер | |
| window / browser | окно / браузер | |
| help / about | справка / о программе | |
| log (file) / log viewer | журнал / просмотр журнала | never the verb "log in" |
| debug | отладка | |
| error / warning / notice | ошибка / предупреждение / уведомление | |
| report / statistics | отчет / статистика | |
| modified / created | изменено / создано | column headings |
| name / description / title | название / описание / заголовок | a person's name: имя |
| identifier / ID / remote ID | идентификатор / ID / удаленный ID | |
| remote / local | удаленный / локальный | |
| hidden / visible | скрытый / видимый | |
| priority | приоритет | |
| unknown / none / all / any | неизвестно / нет / все / любой | |
| archive (versions, orders) / archive file | архив / архивный файл | |
| backup | резервная копия | |
| storage / disk space | хранилище / место на диске | |
| memory / performance | память / производительность | |
| process / thread | процесс / поток | |
| queue / job / task | очередь / задание / задача | |
| schedule | расписание | |
| timeout | время ожидания | |
| request / response | запрос / ответ | |
| session / cookie | сеанс / файл cookie | |
| token / API key | токен / ключ API | |
| credentials / certificate | учетные данные / сертификат | |
| authentication / two-factor | аутентификация / двухфакторная аутентификация | |
| encryption | шифрование | |
| locale / time zone | региональные параметры / часовой пояс | |
| project | проект | |
| shop / webshop | магазин / интернет-магазин | |
| order (shop) / order (sort) | заказ / порядок | |
| basket / checkout / wish list | корзина / оформление заказа / список желаний | |
| product / price / discount / currency | товар / цена / скидка / валюта | |
| customer | клиент | |
| VAT | НДС | |
| AI / model / prompt | ИИ / модель / промпт | |
| embedding / vector / chunk | векторное представление / вектор / фрагмент | |
| health check | проверка работоспособности | |
| confirmation question | Вы действительно хотите …? | |

## 5. Decisions after the translation

Settled before the round (the context-dependent words of the previous rounds). They take precedence over the
glossary. Terms settled while translating are added here.

| English | Translation | Distinction |
|---|---|---|
| Store (button: save) | Сохранить | the shop tab of the admin header (kernel\|design/admin/pagelayout): Магазин; secrets: context override |
| Order (shop) / Order (sort order) | заказ / порядок | the context decides (shop, orderview, basket vs. class/edit, bookmark sorting): context overrides |
| State (object state) / State (address) | состояние / область | the address field of the shop and of user data: область (регион) |
| Login (button, verb) / Login (the user name) | Войти / Имя пользователя | |
| View (button) / View (module view) | Просмотреть / Представление | |
| Up / Down (move in a list) | Выше / Ниже | expqueue "Up" (how long a worker runs): Время работы |
| Top / Bottom (position in an order) | В начало / В конец | a position on the page: Вверху / Внизу |
| Delete (the key) | клавиша Delete | key names stay Latin |
| trash vs. shop basket | корзина vs. корзина покупок | both "корзина"; headings of the shop say "Корзина покупок" |
| Remove vs. Delete | Убрать vs. Удалить | Remove selected: Убрать выбранные |
| True / False (setting values) | Да / Нет | code literals true / false stay Latin |


## Decisions 25.10.2026 (user)

- Edit → Изменить, Settings → Параметры (as Microsoft). Delete → Удалить and Remove → Убрать stay distinct.
