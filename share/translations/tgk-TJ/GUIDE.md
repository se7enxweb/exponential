# Tajik (tgk-TJ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Tajik (забони тоҷикӣ) as written in Tajikistan, in the **Tajik Cyrillic alphabet**, in the neutral,
polite register of Tajik government portals, Tajik news (Khovar, Asia-Plus Tajik, Radio Ozodi) and the Tajik
localisations of Windows, Android and Firefox (tg) and Tajik Wikipedia. No other locale falls back to `tgk-TJ`.

- Word order is subject - object - verb; the verb comes last. The ezofat -и links nouns and modifiers (файли нав,
  номи корбар); the plural is -ҳо (файлҳо) or -он for people (корбарон).
- Technical words: the Tajik word where one is established (файл, корбар, рамз / парол, танзимот, ҷустуҷӯ, пойгоҳи
  додаҳо); a Russian or international loan where Tajik IT writing uses it (сервер, скрипт, модул, кэш). Persian
  (fas-IR) is the same language in another script and may help with vocabulary; Russian (rus-RU) shows the loans in
  use. Write Tajik spelling and Cyrillic, never Persian script and never Russian forms (не -> на, файлы -> файлҳо,
  сохранить -> захира кардан). Never leave the English word in Latin letters unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **шумо** (polite "you") with the matching verb forms (метавонед, мехоҳед).
- **Buttons, menu items and links**: the short infinitive, as Tajik interfaces do: Захира кардан (save), Бекор кардан
  (cancel), Нест кардан (delete), Хориҷ кардан (remove), Таҳрир кардан (edit), Илова кардан (add), Эҷод кардан
  (create), Ҷустуҷӯ (search), Интихоб кардан (select), Фиристодан (send), Дидан (view), Нашр кардан (publish), Бор
  кардан (upload), Боргирӣ кардан (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Танзимот, Тарҷумаҳо, Ҷойгиршавии асосӣ, Ном.
- **Instructions**: the polite imperative in -ед: "Унсурро интихоб кунед ва \"OK\"-ро пахш кунед.". "Please" is
  "Лутфан".
- **Confirmations and questions**: "Шумо дар ҳақиқат мехоҳед ...?": "Шумо дар ҳақиқат мехоҳед ин бахшро нест
  кунед?".
- **What the user can or may do**: "Шумо метавонед ...", "Шумо наметавонед ..."; "You do not have permission" is
  "Шумо барои ... иҷозат надоред".
- **Messages about a result**: the passive past: "Сиёҳнавис захира шуд.", "Файл бомуваффақият бор карда шуд.".

## 3. Script and typography

- **Tajik Cyrillic** with the letters ғ ӣ қ ӯ ҳ ҷ (U+0493, U+04E3, U+049B, U+04EF, U+04B3, U+04B7) and their capitals;
  never the Latin or Russian look-alikes (г for ғ, и for ӣ, к for қ, у for ӯ, х for ҳ, ч for ҷ).
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A suffix (the ezofat -и, -ро, -ҳо) is never written onto a placeholder
  (%nameро is a different placeholder): use a noun that carries it ("файли %name", "%count унсур") or join it with a
  hyphen after quotation marks ("%name"-ро). The same for code, file names, INI settings and product names.
- **Quotation marks**: «...» or straight double quotes "..." as in the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Tajik | Note |
|---|---|---|
| content | мундариҷа | content object: объекти мундариҷа |
| object | объект | |
| tree | дарахт | tree menu: менюи дарахтӣ; content structure tree: дарахти сохтори мундариҷа |
| node | гиреҳ | main node: гиреҳи асосӣ; parent node: гиреҳи волидайн |
| location | ҷойгиршавӣ | main location: ҷойгиршавии асосӣ |
| item (list entry) | унсур | |
| class | синф | the content class; class group: гурӯҳи синфҳо |
| attribute | хосият | |
| datatype | навъи додаҳо | |
| version | версия | version history: таърихи версияҳо |
| draft | сиёҳнавис | store draft: Захира кардани сиёҳнавис |
| publish | нашр кардан | published: нашршуда |
| send for publishing | барои нашр фиристодан | |
| discard | рад кардан | discard draft: Рад кардани сиёҳнавис |
| archived / pending / rejected | бойгонишуда / дар интизорӣ / радшуда | |
| siteaccess | дастрасии сомона | siteaccess as a setting name stays in code |
| site, website | сомона | |
| section | бахш | the Exponential section; an INI section: бахши INI |
| role | нақш | |
| policy | сиёсат | |
| limitation | маҳдудият | |
| permission | иҷозат | access denied: дастрасӣ манъ аст |
| module | модул | |
| function | функсия | |
| user / user group | корбар / гурӯҳи корбарон | |
| group | гурӯҳ | |
| anonymous | беном | anonymous user: корбари беном |
| administrator | маъмур | administration (interface): идоракунӣ |
| username | номи корбар | login (the name): логин |
| password | парол | |
| log in / log out | ворид шудан / баромадан | |
| e-mail | почтаи электронӣ | e-mail address: суроғаи почтаи электронӣ |
| cache | кэш | clear cache: Тоза кардани кэш |
| template | қолиб | override template: қолиби ҷойгузин |
| override | ҷойгузинкунӣ | |
| fallback | эҳтиётӣ | fallback language: забони эҳтиётӣ |
| design | тарҳ | |
| workflow | ҷараёни корӣ | |
| trigger | ангезанда | |
| event | ҳодиса | |
| trash | сабад | empty trash: Холӣ кардани сабад; move to trash: Ба сабад гузоштан |
| bookmark | хатчӯб | |
| settings | танзимот | configuration: конфигуратсия |
| setup | насб | the Setup tab |
| layout | тарҳбандӣ | Exponential Layouts stays |
| block / zone | блок / минтақа | |
| extension | васеъкунӣ | |
| package | баста | |
| search | ҷустуҷӯ | search index: индекси ҷустуҷӯ |
| log | сабт | a log file: файли сабт; never "log in" |
| queue | навбат | job queue: навбати вазифаҳо |
| job / task | вазифа | |
| cron | cron | |
| script | скрипт | |
| kernel, core | ҳаста | |
| server | сервер | |
| database | пойгоҳи додаҳо | |
| folder / directory | ҷузвдон | |
| file | файл | |
| image | тасвир | thumbnail: тасвирчаи хурд |
| preview | пешнамоиш | |
| upload / download | бор кардан / боргирӣ кардан | |
| export / import | содирот / воридот | to export / import: содир кардан / ворид кардан |
| remove | хориҷ кардан | take out of a list or place |
| delete | нест кардан | destroy; deleted: нест карда шуд |
| edit | таҳрир кардан | |
| editor | муҳаррир | |
| save / store | захира кардан | the verb "Store" of the forms |
| Store (shop tab) | мағоза | the shop, as "Shop" |
| cancel | бекор кардан | |
| OK | ХУБ | |
| apply | татбиқ кардан | |
| back | бозгашт | |
| next / previous | баъдӣ / қаблӣ | |
| enable / disable | фаъол кардан / ғайрифаъол кардан | enabled / disabled: фаъол / ғайрифаъол |
| active / inactive | фаъол / ғайрифаъол | |
| run | иҷро кардан | |
| start / stop | оғоз кардан / қатъ кардан | |
| session | ҷаласа | |
| hidden / visible | пинҳон / намоён | hide / reveal: пинҳон кардан / нишон додан |
| archive | бойгонӣ | |
| order (sort) | тартиб | sort order: тартиби мураттабсозӣ |
| order (shop) | фармоиш | |
| shop | мағоза | |
| basket | сабади харид | |
| product | маҳсулот | |
| customer | мизоҷ | |
| discount | тахфиф | |
| VAT | ААИ | |
| price / currency | нарх / асъор | |
| tag | барчасп | |
| translation | тарҷума | |
| language | забон | main language: забони асосӣ |
| locale | маҳаллӣсозӣ | |
| state | ҳолат | the object state |
| status | вазъ | |
| view (button) | дидан | a module view (noun): намуд |
| format | формат | |
| sort | мураттаб кардан | |
| filter | филтр | to filter: филтр кардан |
| select | интихоб кардан | select all: Ҳамаро интихоб кардан; selected: интихобшуда |
| copy / move | нусхабардорӣ кардан / кӯчонидан | |
| add / create / new | илова кардан / эҷод кардан / нав | |
| restore | барқарор кардан | |
| install / uninstall | насб кардан / нест кардани насб | |
| related objects | объектҳои алоқаманд | relation: алоқа |
| dashboard | лавҳаи идоракунӣ | |
| identifier / ID | муайянкунанда / ID | |
| priority | афзалият | |
| owner | соҳиб | |
| modified / created | тағйирёфта / эҷодшуда | |
| date / time | сана / вақт | |
| error / warning | хато / огоҳӣ | |
| help | кӯмак | |
| notification | огоҳинома | |
| account | ҳисоб | |
| default | пешфарз | |
| link | пайванд | |
| none / all | ҳеҷ / ҳама | |
| yes / no | ҳа / не | |
| name / title / description | ном / сарлавҳа / тавсиф | |
| value / type / list | қимат / навъ / рӯйхат | |
| page / path | саҳифа / роҳ | |
| required / optional | ҳатмӣ / ихтиёрӣ | |
| options | имконот | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is мағоза, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is захира кардан.
- "Login" in the user, session and audit lists is the user's login name (логин); the Login button of the error page
  is the act (Ворид шудан).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
