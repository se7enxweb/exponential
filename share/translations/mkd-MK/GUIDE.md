# Macedonian (mkd-MK): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Macedonian (македонски јазик) in the standard literary language and the orthography of the Institute for the
Macedonian Language, with the computing terms of the Macedonian software localisations, in a neutral, polite
register. No other locale falls back to `mkd-MK`.

- Write real Macedonian, never Bulgarian or Serbian forms: "датотека", "лозинка", "папка", "врска", "корисник",
  "избриши", "зачувај", "поставки". Bulgarian (`bul-BG`) and Serbian (`srp-RS`) may help with the meaning and the
  terminology; the text itself is translated from the English.
- Macedonian has no cases and no infinitive; the definite article is a suffix (-от, -та, -то, -те, and -ов/-ва/-во,
  -он/-на/-но where the sense wants it): објектот, датотеката, поставките.
- Loan words in their Macedonian form: сервер, кеш, шаблон, модул, пакет, интерфејс, конфигурација, категорија.
- Short, plain sentences.

## 2. Form of address

- The user is addressed with the polite **Вие** form, written with a capital В (Вие, Вашиот, Ви), as Macedonian
  public and software texts do.
- **Buttons, menu items and links**: the imperative singular, as Macedonian interfaces do: Зачувај, Откажи, Избриши,
  Отстрани, Уреди, Додај, Создај, Барај, Избери, Испрати, Прегледај, Објави, Прикачи, Преземи, Примени, Врати.
- **Titles, column headings and tabs**: nouns or noun phrases: Поставки, Преводи, Главна локација, Име.
- **Instructions**: the polite imperative: "Изберете објект и кликнете \"Во ред\".", "Ве молиме" where the source
  has please.
- **Confirmations and questions**: "Дали сте сигурни дека сакате да ...?".
- **What the user can or may do**: "Можете да ...", "Не можете да ..."; "You do not have permission" is "Немате
  дозвола да ...".
- **Messages about a result**: the perfect or the passive: "Нацртот е зачуван.", "Датотеката е успешно прикачена.".

## 3. Script and typography

- **Cyrillic** alphabet of Macedonian, with **ѓ ќ ѕ џ љ њ ј** as composed characters; never ы, щ, ъ, ь, э, й, ђ, ћ.
- **Capitals**: only the first word of a button, title or sentence, proper names and the polite Вие forms.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder (no article suffix on a
  placeholder); put a noun before it if the sentence needs the article ("објектот %name"). The same for code, file
  names, INI settings and product names, which stay in Latin letters.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Macedonian | Note |
|---|---|---|
| content | содржина | content object: објект со содржина |
| object | објект | |
| node | јазол | main node: главен јазол; parent node: родителски јазол |
| location | локација | main location: главна локација |
| item (list entry) | ставка | |
| tree | дрво | tree menu: мени во облик на дрво; content structure: структура на содржината |
| class | класа | content class: класа на содржина; class group: група класи |
| attribute | атрибут | |
| datatype | тип на податоци | |
| version | верзија | version history: историја на верзии |
| draft | нацрт | store draft: Зачувај нацрт |
| publish | објави | published: објавен; publishing: објавување |
| send for publishing | испрати за објавување | |
| discard | отфрли | discard draft: Отфрли нацрт |
| archived / pending / rejected | архивиран / на чекање / одбиен | |
| siteaccess | пристап до сајтот | siteaccess as a setting name stays in code |
| site, website | сајт, веб-сајт | |
| section | секција | the Exponential section; an INI section: INI-секција |
| role | улога | |
| policy | политика | |
| limitation | ограничување | |
| permission | дозвола | access denied: пристапот е одбиен |
| module | модул | |
| function | функција | |
| user / user group | корисник / група корисници | |
| group | група | |
| anonymous | анонимен | anonymous user: анонимен корисник |
| administrator | администратор | administration interface: администраторски интерфејс |
| username | корисничко име | |
| password | лозинка | |
| log in / log out | најави се / одјави се | the act: најава / одјава |
| e-mail | е-пошта | e-mail address: адреса на е-пошта |
| cache | кеш | clear cache: Исчисти го кешот |
| template | шаблон | override template: шаблон за препокривање |
| override | препокривање | |
| fallback | резервен | fallback language: резервен јазик |
| design | дизајн | |
| workflow | работен тек | |
| trigger | активатор | |
| event | настан | |
| trash | корпа | empty trash: Испразни ја корпата; move to trash: Премести во корпата |
| bookmark | обележувач | |
| settings | поставки | configuration: конфигурација |
| setup | конфигурација | the Setup tab |
| layout | распоред | Exponential Layouts stays |
| block / zone | блок / зона | |
| extension | проширување | |
| package | пакет | |
| search | пребарување | to search: пребарај; search index: индекс за пребарување |
| log | дневник | a log file: датотека на дневник; never "log in" |
| queue | редица | job queue: редица на задачи |
| job / task | задача | |
| kernel, core | јадро | |
| server | сервер | |
| database | база на податоци | |
| folder / directory | папка / директориум | |
| file | датотека | |
| image | слика | thumbnail: сличица |
| preview | преглед | |
| upload / download | прикачи / преземи | |
| export / import | извези / увези | |
| remove | отстрани | take out of a list or place |
| delete | избриши | destroy; deleted: избришан |
| edit | уреди | |
| editor | уредник | person; the software: уредувач |
| save / store | зачувај | the verb "Store" of the forms |
| Store (shop tab) | Продавница | the shop, as "Shop" |
| cancel | откажи | |
| OK | Во ред | |
| apply | примени | |
| back | назад | |
| next / previous | следно / претходно | |
| enable / disable | овозможи / оневозможи | enabled / disabled: овозможено / оневозможено |
| active / inactive | активен / неактивен | |
| run | изврши | |
| start / stop | започни / запри | |
| session | сесија | |
| hidden / visible | скриен / видлив | hide / reveal: скриј / прикажи |
| archive | архива | |
| order (sort) | редослед | sort order: редослед на подредување |
| order (shop) | нарачка | |
| shop | продавница | |
| basket | кошничка | |
| product | производ | |
| customer | купувач | |
| discount | попуст | |
| VAT | ДДВ | |
| price / currency | цена / валута | |
| tag | ознака | |
| translation | превод | |
| language | јазик | main language: главен јазик |
| locale | регионални поставки | |
| state | состојба | the object state |
| status | статус | |
| view (button) | прегледај | a module view (noun): приказ |
| format | формат | |
| sort | подреди | |
| filter | филтер | to filter: филтрирај |
| select | избери | select all: Избери ги сите; selected: избран |
| copy / move | копирај / премести | |
| add / create / new | додај / создај / нов | |
| restore | врати | |
| install / uninstall | инсталирај / деинсталирај | |
| related objects | поврзани објекти | relation: врска |
| dashboard | контролна табла | |
| identifier / ID | идентификатор / ID | |
| priority | приоритет | |
| owner | сопственик | |
| modified / created | изменето / создадено | |
| date / time | датум / време | |
| error / warning | грешка / предупредување | |
| help | помош | |
| notification | известување | |
| account | сметка | |
| default | стандардно | |
| link | врска | |
| none / all | нема / сите | |
| yes / no | да / не | |
| name / title / description | име / наслов / опис | |
| value / type / list | вредност / тип / листа | |
| page / path | страница / патека | |
| required / optional | задолжително / незадолжително | |
| options | опции | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Продавница, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Зачувај.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Најави се).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
