# Tatar (tat-RU): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Tatar (татар әдәби теле) as written in Tatarstan, in the neutral, technical register of current
Tatar software (the Tatar localisations of Windows, Microsoft Office, Yandex and Mozilla) and of the Tatar Wikipedia.
No other locale falls back to `tat-RU`; Russian (`rus-RU`), Kazakh (`kaz-KZ`) and Turkish (`tur-TR`) only help with
terminology.

- Tatar words are used where the software already has them: саклау, бетерү, көйләүләр, йөкләп алу, йөкләү,
  сылтама, кыстыргыч, серсүз. Internationalisms of computing keep their usual (Russian-shaped) spelling: сервер,
  скрипт, модуль, кэш, файл, браузер, папка.
- No Russian words where Tatar has its own (not "настройки", "пароль", "ссылка" for көйләүләр, серсүз, сылтама).
- Tatar suffixes follow vowel harmony (back and front) and the final sound of the stem: файлны, серверга,
  модульнең, кэшны.

## 2. Form of address

The interface addresses the user with the polite **Сез** (you), **Сезнең** (your), as Tatar software does.

- **Buttons, menu items and links**: the verbal noun in -у/-ү, as Tatar software does: Саклау, Баш тарту, Бетерү,
  Төзәтү, Бастыру, Бастыруга җибәрү, Сайланганнарны алып ташлау, Чүп савытына күчерү. Not the imperative
  (Саклагыз) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Көйләүләр, Тәрҗемәләр, Төп урын.
- **Instructions**: the polite imperative in -гыз/-гез, -ыгыз/-егез: "Элементны сайлагыз һәм «OK» төймәсенә
  басыгыз.", "Көтеп торыгыз ...". A reference ("see ...") is "... карагыз".
- **Confirmations and questions**: "Бу классны чыннан да бетерергә телисезме?", "... телисезме?".
- **What the user can or may do**: "... аласыз", "... алмыйсыз", or impersonal: "Төп телне үзгәртеп булмый ...".
  "You do not have permission" is "Сездә ... рөхсәт юк".
- Not allowed: the familiar син / синең, slash forms.

## 3. Script and typography

- **Cyrillic**, the script of the shipped locale (`share/locale/tat-RU.ini`, LanguageName Татар) and, by federal
  law, the official script of Tatar in Russia. The Tatar letters ә (U+04D9), ө (U+04E9), ү (U+04AF), җ (U+0497),
  ң (U+04A3) and һ (U+04BB) are written with their own code points, never look-alike Latin letters.
- **The Latin alphabet (Zamanälif)**: Tatarstan adopted a Latin alphabet in 1999, but a federal law of 2002 made
  Cyrillic mandatory for the official languages of the republics; Latin is in use only among part of the diaspora.
  This catalogue is Cyrillic.
- **Latin text inside Tatar**: product names, codes and acronyms stay in Latin letters. A Tatar suffix after them
  follows a hyphen (Exponential-ның, PHP-ны), never an apostrophe; a wording without a suffix often reads better:
  "PHP файлы", "URL адресы", "HTML коды".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Tatar letter or hyphenated suffix glued
  to a placeholder: "%count объект", not "%count-ны" or "%countобъект". Put the placeholder where it needs no
  suffix: "Объект: %name", "%count элемент".
- **Numbers**: a noun after a number stays in the singular, as Tatar grammar wants it: "%count объект", not
  "объектлар". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon. Tatar puts the verb at the end of the sentence and the modifier before its noun.

## 4. Glossary

Binding for the kernel and every extension.

| English | Tatar | Note |
|---|---|---|
| content | эчтәлек | |
| content object | эчтәлек объекты | |
| object | объект | |
| node | төен | main node: төп төен; parent node: ата төен; child: бала төен |
| location | урын | main location: төп урын |
| item (list entry) | элемент | |
| class | класс | the content class; class group: класслар төркеме; a PHP class: PHP классы |
| content class | эчтәлек классы | |
| attribute | атрибут | |
| datatype | мәгълүмат төре | |
| version | версия | version history: версияләр тарихы |
| draft | каралама | store draft: караламаны саклау |
| publish | бастыру | published: бастырылган |
| send for publishing | бастыруга җибәрү | |
| discard | ташлау | discard draft: караламаны ташлау |
| archived | архивланган | |
| pending | көтелә | |
| rejected | кире кагылган | |
| siteaccess | сайтка керү ноктасы | the configuration unit |
| site, website | сайт, веб-сайт | |
| section | бүлек | the Exponential section |
| role | роль | |
| policy | сәясәт | |
| limitation | чикләү | |
| permission | рөхсәт | access rights: керү хокуклары |
| module | модуль | |
| function | функция | |
| user | кулланучы | |
| user group | кулланучылар төркеме | |
| group | төркем | |
| anonymous | аноним | anonymous user: аноним кулланучы |
| administrator | администратор | |
| administration (interface) | идарә итү интерфейсы | |
| username | кулланучы исеме | the column "Login" too |
| password | серсүз | |
| log in / sign in | керү | |
| log out / sign out | чыгу | |
| e-mail | электрон почта | e-mail address: электрон почта адресы |
| cache | кэш | clear cache: кэшны чистарту |
| template | шаблон | |
| override | алыштыру | override template: алыштыручы шаблон |
| fallback | запас | fallback language: запас тел |
| design | дизайн | |
| workflow | эш агымы | |
| trigger | триггер | |
| event | вакыйга | |
| trash | чүп савыты | empty trash: чүп савытын бушату |
| bookmark | кыстыргыч | |
| settings | көйләүләр | |
| setup | көйләү | the Setup tab; installation: урнаштыру |
| layout | макет | Exponential Layouts |
| block | блок | |
| zone | зона | |
| extension | киңәйтү | |
| package | пакет | |
| search | эзләү | |
| log | журнал | log file: журнал файлы |
| queue | чират | |
| job / task | бирем | |
| cron | cron | cron job: cron биреме |
| script | скрипт | |
| worker | эшкәртү процессы | |
| kernel, core | үзәк | |
| server | сервер | |
| database | мәгълүмат базасы | |
| folder | папка | |
| file | файл | |
| image | рәсем | |
| preview | алдан карау | |
| upload | йөкләү | |
| download | йөкләп алу | |
| export / import | экспортлау / импортлау | |
| remove | алып ташлау | take out of a list or a place |
| delete | бетерү | destroy |
| edit | төзәтү | |
| editor | редактор | |
| save / store | саклау | |
| Store (shop tab) | Кибет | the word the shop menu uses for "Shop" |
| cancel | баш тарту | |
| OK | OK | |
| apply | куллану | |
| back | артка | |
| next / previous | киләсе / алдагы | |
| enable / disable | кабызу / сүндерү | a setting or feature |
| enabled / disabled | кабызылган / сүндерелгән | |
| active / inactive | актив / актив түгел | |
| run | башкару | a script or job; start a service: эшләтеп җибәрү |
| start / stop | башлау / туктату | |
| session | сеанс | |
| hidden / visible | яшерен / күренә торган | hide / reveal: яшерү / күрсәтү |
| archive | архив | |
| order (sort) | тәртип | sort order: рәтләү тәртибе |
| order (shop) | заказ | |
| shop | кибет | |
| basket | кәрзин | kept apart from чүп савыты |
| product | товар | |
| customer | сатып алучы | |
| discount | ташлама | |
| VAT | КБС | VAT type: КБС төре |
| price | бәя | |
| currency | валюта | |
| tag | тег | keyword tag; an HTML tag: HTML тегы |
| translation | тәрҗемә | |
| language | тел | main language: төп тел |
| locale | локаль | |
| state | халәт | the object state; state group: халәтләр төркеме |
| status | статус | |
| view (button) | карау | a module view: күренеш |
| format | формат | |
| sort | рәтләү | |
| filter | фильтр | |
| select | сайлау | selected: сайланган |
| browse | карап чыгу | |
| copy | күчермә ясау | copied: күчермәсе ясалган |
| move | күчерү | |
| add | өстәү | |
| create | булдыру | |
| new | яңа | |
| restore | торгызу | |
| install / uninstall | урнаштыру / бетерү | |
| sub items | эчке элементлар | |
| subtree | эчке агач | |
| related objects | бәйле объектлар | relation: бәйләнеш |
| URL alias | URL псевдонимы | |
| redirect | юнәлтү | |
| dashboard | идарә панеле | |
| content structure | эчтәлек төзелеше | |
| media library | медиа китапханә | |
| identifier / ID | идентификатор / ID | |
| priority | өстенлек | |
| owner | хуҗа | |
| modified / created | үзгәртелгән / булдырылган | |
| date / time | дата / вакыт | |
| error / warning | хата / кисәтү | |
| help | ярдәм | |
| notification | белдерү | |
| wizard | остаз | |
| feed (RSS) | тасма | RSS feed: RSS тасмасы |
| tab | кыстырма | |
| audit, audit trail | аудит, аудит журналы | |
| repository | репозиторий | |
| checksum | тикшерү суммасы | |
| account | хисап язмасы | |
| default | гадәттәге | default value: гадәттәге кыйммәт |
| toolbar | кораллар панеле | |
| container | контейнер | |
| AI | ясалма интеллект | |
| pattern (name pattern) | үрнәк | kept apart from шаблон (template) |
| wildcard | алыштыру билгесе | |
| remote ID | ерак ID | |
| placeholder | урынбасар | |
| collection | җыентык | information collector: мәгълүмат җыючы |
| approve, approval | раслау | |
| software | программа тәэминаты | |
| creator / modifier | булдыручы / үзгәртүче | |
| request (HTTP) | сорау | |
| query | сорату | search query: эзләү соратуы |
| handler / provider / listener | эшкәрткеч / провайдер / тыңлаучы | |
| engine | двигатель | |
| release / upgrade | чыгарылыш / яңарту | update: яңарту |
| backup | резерв күчермә | |
| maintenance | техник хезмәт күрсәтү | |
| directory | каталог | |
| header (HTTP) | баш юл | |
| page layout (pagelayout template) | бит шаблоны | kept apart from макет (Exponential Layouts) |
| passkey | керү ачкычы | |
| two-step sign-in | ике адымлы керү | |
| device / browser | җайланма / браузер | |
| link | сылтама | |
| registration, register | теркәлү, теркәү | |
| payment gateway | түләү шлюзы | |
| form | форма | |
| comment | аңлатма | |
| newsletter / subscription | хәбәрнамә / язылу | |
| field | кыр | |
| value | кыйммәт | |
| list | исемлек | |
| page | бит | |
| title / name | исем / атама | the column "Name": Исем |
| description | тасвирлама | |
| type | төр | |
| path | юл | |
| width / height | киңлек / биеклек | |
| size | зурлык | |
| none | юк | |
| yes / no | әйе / юк | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Кибет, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is саклау.
- "Copy" is күчермә ясау and "move" is күчерү, so that the two actions are never confused.
