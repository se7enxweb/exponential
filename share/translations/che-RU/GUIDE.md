# Chechen (che-RU): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Chechen (нохчийн литературин мотт) in the current Cyrillic orthography, in the neutral, formal
register of official and technical writing and of the Chechen Wikipedia and its interface. The locale is `che-RU`.
No other locale falls back to it. Russian (`rus-RU`) is the language Chechen readers know the computing terms
from: its catalogue helps to understand a message, but it is no model for the Chechen words or grammar.

- **Computing terms** that Chechen has no word of its own for are taken over in their Russian form, as Chechen
  writing does (сервер, файл, папка, кэш, модуль, скрипт, браузер, пароль, логин). Chechen words are used where
  they exist and are current: чулацам (content), мотт (language), лехар (search), бакъо (permission), хьажорг
  (link), декъашхо (user), гоч (translation).
- English words stay only where they are names or code (see TRANSLATING.md, rule 5).

## 2. Form of address

The interface addresses the user formally with **шу** (you), **шун** (your), **шуна**, **шуьга**.

- **Buttons, menu items and links**: the infinitive, as the commands of a Chechen interface read: Ӏалашдан,
  юхадаккха, дӀадайа, нисдан, арахеца, дӀадаккха, тӀетоха, кхолла. A command naming an object puts the object
  first: «Керла класс кхолла», «Файл чудахьа», «Харжинарг дӀадаккха».
- **Verb agreement**: a verb with a class prefix agrees with its object or subject (д-, б-, в-, й-). Where the
  object is not named, the д-class form is used (дӀадаккха, дӀадайа).
- **Titles, column headings and tabs**: nouns or verbal nouns: Параметраш, Гочдарш, Коьрта меттиг, Арахецар.
- **Instructions** in sentences: polite, with «Дехар до» (please) where it reads naturally, else the infinitive or
  an impersonal sentence: «Элемент а хаьржина, „Дика ду“ тӀе таӀа.». A reference ("see ...") is «Хьажа ...».
- **Confirmations and questions**: «Шуна ... лаьий?» or «Шуна баккъалла а ... лаьий?»: «Шуна баккъалла а и класс
  дӀайайа лаьий?».
- **What the user can or may do**: «Шуна ... мега», «Шуна ... мегар дац», «... йиш яц». "You do not have
  permission" is «Шун ... бакъо яц».
- Not allowed: the familiar **хьо** / **хьан**, slash forms, Russian sentences.

## 3. Script and typography

- **Cyrillic script** with the palochka **Ӏ** (U+04C0) in every position, as the Chechen Wikipedia writes it:
  дӀа, Ӏалашдан, гӀалат, кхоьллина, тӀетоха. Never a Latin I or l, a digit 1, the Ukrainian і/І (U+0456/U+0406) or
  the Latin capital I in its place. The lowercase palochka ӏ (U+04CF) is not used.
- The digraphs and letters of Chechen are written as the orthography sets them: аь, оь, уь, юь, яь, гӀ, кх, къ,
  кӀ, пӀ, тӀ, хь, хӀ, цӀ, чӀ.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged in Latin letters and stand free:
  a space before and after them, or a punctuation mark right after. Never a case ending glued to a placeholder
  (%nameн, %countан): use a label or a noun in front: «ЦӀе: %name», «%count объект», «класс %name» instead of a
  genitive of the placeholder. Code, file names, INI settings and product names stay unchanged too.
- **Hyphenated compounds** with Latin text: URL-псевдоним, PHP-файл, HTML-код.
- **Quotation marks**: the guillemets «...» (and „...“ inside them) where the source has its own quotation marks in
  text; where the source has `&quot;`, a straight " or ' that is code or an entity, keep it exactly.
- **Punctuation**: as in the source; no space before a colon or a question mark.
- **Numbers**: digits 0-9. A noun after a number stays in the singular («%count объект»); the catalogue has no
  plural forms, so a label with a count is used where a sentence would need one: «Объекташ: %count».
- **Capitals**: only the first word of a button, title or heading starts with a capital, apart from names. A word
  beginning with the palochka letter is capitalised as Ӏ itself (Ӏалашдан).

## 4. Glossary

Binding for the kernel and every extension.

| English | Chechen | Note |
|---|---|---|
| content | чулацам | |
| content object | чулацаман объект | |
| object | объект | plural объекташ |
| node | шад | plural шадаш; main node: коьрта шад; parent node: лакхара шад |
| location | меттиг | plural меттигаш; main location: коьрта меттиг |
| item (list entry) | элемент | plural элементаш |
| class | класс | the content class, plural классаш; class group: классийн тоба; a PHP class: PHP-класс |
| content class | чулацаман класс | |
| attribute | атрибут | plural атрибуташ |
| datatype | хаамийн тайпа | |
| version | верси | plural версеш; version history: версийн истори |
| draft | черновик | plural черновикаш; store draft: черновик Ӏалашдан |
| publish | арахеца | published: арахецна; publishing: арахецар |
| send for publishing | арахецархьама дӀадахьийта | |
| discard | дӀатасса | discard draft: черновик дӀатасса |
| archived | архивехь | |
| pending | хьоьжуш | |
| rejected | юхатоьхна | |
| siteaccess | сайте тӀекхачар | |
| site, website | сайт | |
| section | дакъа | the Exponential section; plural дакъош |
| role | роль | plural ролаш |
| policy | политика | |
| limitation | доза | plural дозанаш |
| permission | бакъо | plural бакъонаш |
| module | модуль | plural модулаш |
| function | функци | plural функцеш |
| user | декъашхо | plural декъашхой |
| user group | декъашхойн тоба | |
| group | тоба | plural тобанаш |
| anonymous | цӀе йоцу | |
| administrator | администратор | |
| administration (interface) | урхалла | the administration interface: урхаллин интерфейс |
| username | декъашхочун цӀе | |
| password | пароль | |
| log in / sign in | чугӀо | the noun: чугӀор |
| log out / sign out | аравала | the noun: аравалар |
| e-mail | электронан пошт | e-mail address: электронан поштан адрес |
| cache | кэш | clear the cache: кэш цӀанъян |
| template | кеп | plural кепаш |
| override | тӀехийцар | an override template: тӀехийцаран кеп |
| fallback | резервни | fallback language: резервни мотт |
| design | дизайн | |
| workflow | белхан некъ | |
| trigger | триггер | |
| event | хилларг | plural хилларш |
| trash | нехийн тускар | move to trash: нехийн тускаре дӀаяккха |
| bookmark | билгало | plural билгалонаш |
| settings | параметраш | |
| setup | дӀахӀоттор | |
| layout | макет | Exponential Layouts; kept apart from the page layout |
| block | блок | plural блокаш |
| zone | зона | |
| extension | шордар | plural шордарш |
| package | пакет | plural пакеташ |
| search | лаха | the noun: лехар; search results: лехаран жамӀаш |
| log | журнал | a log file; never "log in" |
| queue | рогӀ | |
| job / task | болх / тӀедиллар | |
| cron | cron | |
| script | скрипт | |
| worker | белхан процесс | |
| kernel, core | бух | |
| server | сервер | |
| database | хаамийн база | |
| folder | папка | |
| file | файл | plural файлаш |
| image | сурт | plural суьрташ |
| preview | хьалххе хьажар | |
| upload | чудахьа | the noun: чудахьар |
| download | охьадаккха | the noun: охьадаккхар |
| export / import | экспорт ян / импорт ян | nouns: экспорт / импорт |
| remove | дӀадаккха | take out of a list or place |
| delete | дӀадайа | destroy; the noun: дӀадайар |
| edit | нисдан | the noun: нисдар |
| editor | редактор | the person or the software |
| save / store | Ӏалашдан | |
| Store (shop tab) | туька | |
| cancel | юхадаккха | |
| OK | Дика ду | |
| apply | леладан | |
| back | юхадерза | |
| next / previous | тӀехьара / хьалхара | |
| enable / disable | латто / дӀасацо | |
| enabled / disabled | лаьтта / дӀасаций | |
| active / inactive | жигара / жигара доцу | |
| run | дӀадоло | |
| start / stop | доло / сацо | |
| session | сесси | |
| hidden / visible | къайлаха / гуш | hide: къайладаккха; reveal: гайта |
| archive | архив | a file archive: файлийн архив |
| order (sort) | рогӀалла | sort order: дӀанисъяран рогӀалла |
| order (shop) | заказ | in the shop context only |
| shop | туька | |
| basket | эцаран тускар | |
| product | товар | |
| customer | эцархо | plural эцархой |
| discount | мах лахбар | |
| VAT | НДС | |
| price | мах | |
| currency | валюта | |
| tag | тег | a keyword tag; an HTML tag: HTML-тег |
| translation | гоч | plural гочдарш |
| language | мотт | plural меттанаш |
| locale | локаль | |
| state | хьал | the object state |
| status | статус | |
| view (button) | хьажа | the module view: хьажар |
| format | формат | text formatting: форматировани |
| sort | рогӀехь дӀанисдан | |
| filter | фильтр | to filter: фильтр тоха |
| select | харжа | selected: хаьржина |
| browse | схьалаха | |
| copy | копи ян | the noun: копи |
| move | дехьадаккха | |
| add | тӀетоха | |
| create | кхолла | |
| new | керла | |
| restore | меттахӀотто | |
| install / uninstall | дӀахӀотто / дӀадаккха | |
| sub items | чуьра элементаш | |
| subtree | кӀелара гӀаш | |
| related objects | уьйр йолу объекташ | |
| URL alias | URL-псевдоним | |
| redirect | кхечу дӀахьажор | |
| dashboard | урхаллин панель | |
| content structure | чулацаман структура | |
| media library | медиабиблиотека | |
| identifier / ID | идентификатор / ID | |
| priority | приоритет | |
| owner | да | |
| modified / created | хийцина / кхоьллина | |
| date / time | терахь / хан | |
| error / warning | гӀалат / дӀахьедар | |
| help | гӀо | |
| notification | хаам | plural хаамаш |
| wizard | гӀоьнча | |
| feed (RSS) | лента | |
| tab | вкладка | plural вкладкаш |
| audit, audit trail | таллам, талламан лар | |
| repository | репозиторий | |
| checksum | контролан жамӀ | |
| account | аккаунт | |
| default | стандартан | default value: стандартан маьӀна |
| toolbar | гӀирсийн панель | |
| container | контейнер | |
| AI | ИИ | исскуственни интеллект where it is spelled out |
| pattern (name pattern) | хатӀ | |
| wildcard | маска | |
| remote ID | генара ID | |
| placeholder | плейсхолдер | |
| collection | гулам | information collector: хаамаш гулдийраг |
| approve, approval | тӀелаца, тӀелацар | |
| software | программа | |
| creator / modifier | кхоллархо / хийцархо | |
| request (HTTP) | дехар | plural дехарш |
| query | хаттар | a database query: хаамийн базе хаттар |
| handler / provider / listener | обработчик / провайдер / ладогӀархо | |
| engine | движок | |
| release / upgrade | релиз / карлаяккхар | |
| backup | резервни копи | |
| maintenance | латтор | |
| directory | каталог | |
| header (HTTP) | корта | |
| page layout (pagelayout template) | агӀон макет | |
| page | агӀо | plural агӀонаш |
| passkey | чугӀоран догӀа | |
| two-step sign-in | шина гӀулчехь чугӀор | |
| device / browser | гӀирс / браузер | |
| link | хьажорг | plural хьажоргаш |
| registration, register | дӀаяздар, дӀаяздала | |
| payment gateway | мах баран шлюз | |
| none | цхьа а | "no ...": цхьа а ... яц |
| yes / no | хӀаъ / хӀан-хӀа | |
| name | цӀе | plural цӀерш |
| description | дийцар | |
| title | корта | |
| type | тайпа | |
| value | маьӀна | |
| list | могӀам | |
| close | дӀакъовла | |
| open | схьаделла | |
| show | гайта | |
| update | карлаян | |
| confirm | тӀечӀагӀдан | |
| send | дахьийта | |
| configuration | конфигураци | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is туька, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Ӏалашдан.
- "Settings" is **параметраш** and "edit" is **нисдан**, so that the two are never confused on one screen.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
