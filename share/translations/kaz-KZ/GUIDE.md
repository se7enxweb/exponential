# Kazakh (kaz-KZ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Kazakh (қазақ әдеби тілі), in the neutral, technical register of current Kazakh software (the
Kazakh localisations of Windows, Microsoft Office, Google and Mozilla), of the Kazakh Wikipedia and of the state
terminology commission. No other locale falls back to `kaz-KZ`; Russian (`rus-RU`) is a different language and is
not used for missing words.

- Kazakh words are used where the software already has them: сақтау, жою, баптаулар, қалта, жүктеп алу, жүктеп
  салу, сілтеме, қойынды, құпиясөз. Internationalisms of computing keep their usual spelling: сервер, скрипт,
  модуль, кэш, файл, браузер.
- No Russian words where Kazakh has its own (not "настройки", "папка", "пароль" in running text for баптаулар,
  қалта, құпиясөз).
- Kazakh suffixes follow vowel harmony and the final sound of the stem: файлды, серверге, модульдің, кэшті.

## 2. Form of address

The interface addresses the user with the polite **Сіз** (you), **Сіздің** (your), written with a capital С as
Kazakh software does.

- **Buttons, menu items and links**: the verbal noun in -у/-ю, as Kazakh software does: Сақтау, Бас тарту, Жою,
  Өңдеу, Жариялау, Жариялауға жіберу, Таңдалғанды алып тастау, Қоқыс себетіне жылжыту. Not the imperative
  (Сақтаңыз) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Баптаулар, Аудармалар, Негізгі орын.
- **Instructions**: the polite imperative in -ңыз/-ңіз, -ыңыз/-іңіз: "Элементті таңдап, «ОК» түймесін басыңыз.",
  "Күте тұрыңыз ...". A reference ("see ...") is "... қараңыз".
- **Confirmations and questions**: "... шынымен ... келе ме?" or "... қалайсыз ба?": "Бұл класты шынымен жойғыңыз
  келе ме?".
- **What the user can or may do**: "... аласыз", "... алмайсыз", or impersonal: "Негізгі тілді өзгерту мүмкін
  емес ...". "You do not have permission" is "Сізде ... рұқсат жоқ".
- Not allowed: the familiar сен / сенің, slash forms.

## 3. Script and typography

- **Cyrillic**, the script of the shipped locale (`share/locale/kaz-KZ.ini`, LanguageName Қазақ тілі) and of
  the Kazakh software in use today. The 42 letters of the Kazakh Cyrillic alphabet are written with their own code
  points: ә ғ қ ң ө ұ ү һ і (і is U+0456, never the Latin i; ұ U+04B1, ү U+04AF). The Russian letters ё, ц, ч, щ, ъ,
  ь, э, ю, я appear only in loanwords.
- **The Latin alphabet**: Kazakhstan decided in 2017 to move the language to a Latin alphabet; the alphabet was
  revised in 2018 and again in 2021, and the change is planned in stages up to about 2031, with state documents,
  schools and media going first since 2023. Software still uses Cyrillic, and so does this catalogue. When the
  Latin alphabet becomes the usual one in software, a Latin catalogue can be made from this one by transliteration
  under its own locale, with the official 2021 alphabet (ä, ğ, q, ñ, ö, ū, ü, h, ı, i ...).
- **Latin text inside Kazakh**: product names, codes and acronyms stay in Latin letters. A Kazakh suffix after an
  acronym is joined with a hyphen, following the pronunciation: PHP-файл, URL-дің, HTML-коды. Often a wording
  without a suffix reads better: PHP файлы.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Kazakh letter or hyphenated suffix
  glued to a placeholder: "%count нысан", not "%count-ы" or "%countнысан". Put the placeholder where it needs no
  suffix: "Нысан: %name", "%count элемент".
- **Numbers**: a noun after a number stays in the singular, as Kazakh grammar wants it: "%count нысан", not
  "нысандар". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon. Kazakh puts the verb at the end of the sentence and the modifier before its noun.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kazakh | Note |
|---|---|---|
| content | мазмұн | |
| content object | мазмұн нысаны | |
| object | нысан | |
| node | түйін | main node: негізгі түйін; parent node: ата-түйін; child: еншілес |
| location | орын | main location: негізгі орын |
| item (list entry) | элемент | |
| class | класс | the content class; class group: класс тобы; a PHP class: PHP класы |
| content class | мазмұн класы | |
| attribute | атрибут | |
| datatype | деректер түрі | |
| version | нұсқа | version history: нұсқалар тарихы |
| draft | жоба | plural жобалар; store draft: жобаны сақтау |
| publish | жариялау | published: жарияланған |
| send for publishing | жариялауға жіберу | |
| discard | қабылдамау | discard draft: жобадан бас тарту |
| archived | мұрағатталған | |
| pending | күтуде | |
| rejected | қабылданбаған | |
| siteaccess | сайтқа кіру нүктесі | the configuration unit |
| site, website | сайт, веб-сайт | |
| section | бөлім | the Exponential section |
| role | рөл | |
| policy | саясат | |
| limitation | шектеу | |
| permission | рұқсат | access rights: кіру құқықтары |
| module | модуль | |
| function | функция | |
| user | пайдаланушы | |
| user group | пайдаланушылар тобы | |
| group | топ | |
| anonymous | анонимді | anonymous user: анонимді пайдаланушы |
| administrator | әкімші | |
| administration (interface) | әкімшілік интерфейсі | |
| username | пайдаланушы аты | the column "Login" too |
| password | құпиясөз | |
| log in / sign in | кіру | |
| log out / sign out | шығу | |
| e-mail | электрондық пошта | e-mail address: электрондық пошта мекенжайы |
| cache | кэш | clear cache: кэшті тазалау |
| template | үлгі | |
| override | қайта анықтау | override template: қайта анықтайтын үлгі |
| fallback | резервтік | fallback language: резервтік тіл |
| design | дизайн | |
| workflow | жұмыс процесі | |
| trigger | триггер | |
| event | оқиға | |
| trash | қоқыс себеті | empty trash: қоқыс себетін босату |
| bookmark | бетбелгі | |
| settings | баптаулар | |
| setup | баптау | the Setup tab; installation: орнату |
| layout | макет | Exponential Layouts |
| block | блок | |
| zone | аймақ | |
| extension | кеңейтім | |
| package | бума | |
| search | іздеу | |
| log | журнал | log file: журнал файлы |
| queue | кезек | |
| job / task | тапсырма | |
| cron | cron | cron job: cron тапсырмасы |
| script | скрипт | |
| worker | өңдеу процесі | |
| kernel, core | ядро | |
| server | сервер | |
| database | дерекқор | |
| folder | қалта | |
| file | файл | |
| image | сурет | |
| preview | алдын ала қарау | |
| upload | жүктеп салу | |
| download | жүктеп алу | |
| export / import | экспорттау / импорттау | |
| remove | алып тастау | take out of a list or a place |
| delete | жою | destroy |
| edit | өңдеу | |
| editor | редактор | |
| save / store | сақтау | |
| Store (shop tab) | Дүкен | the word the shop menu uses for "Shop" |
| cancel | бас тарту | |
| OK | ОК | Cyrillic letters |
| apply | қолдану | |
| back | артқа | |
| next / previous | келесі / алдыңғы | |
| enable / disable | қосу / өшіру | a setting or feature |
| enabled / disabled | қосулы / өшірулі | |
| active / inactive | белсенді / белсенді емес | |
| run | орындау | a script or job; start a service: іске қосу |
| start / stop | бастау / тоқтату | |
| session | сеанс | |
| hidden / visible | жасырын / көрінетін | hide / reveal: жасыру / көрсету |
| archive | мұрағат | |
| order (sort) | рет | sort order: сұрыптау реті |
| order (shop) | тапсырыс | |
| shop | дүкен | |
| basket | сауда себеті | kept apart from қоқыс себеті |
| product | өнім | |
| customer | тапсырыс беруші | |
| discount | жеңілдік | |
| VAT | ҚҚС | VAT type: ҚҚС түрі |
| price | баға | |
| currency | валюта | |
| tag | тег | keyword tag; an HTML tag: HTML тегі |
| translation | аударма | |
| language | тіл | main language: негізгі тіл |
| locale | локаль | |
| state | күй | the object state; state group: күйлер тобы |
| status | мәртебе | |
| view (button) | қарау | a module view: көрініс |
| format | пішім | |
| sort | сұрыптау | |
| filter | сүзгі | |
| select | таңдау | selected: таңдалған |
| browse | шолу | |
| copy | көшіру | |
| move | жылжыту | |
| add | қосу | |
| create | жасау | |
| new | жаңа | |
| restore | қалпына келтіру | |
| install / uninstall | орнату / жою | |
| sub items | ішкі элементтер | |
| subtree | ішкі ағаш | |
| related objects | байланысты нысандар | relation: байланыс |
| URL alias | URL бүркеншік аты | |
| redirect | қайта бағыттау | |
| dashboard | басқару тақтасы | |
| content structure | мазмұн құрылымы | |
| media library | медиа кітапхана | |
| identifier / ID | идентификатор / ID | |
| priority | басымдық | |
| owner | иесі | |
| modified / created | өзгертілген / жасалған | |
| date / time | күні / уақыты | |
| error / warning | қате / ескерту | |
| help | анықтама | |
| notification | хабарландыру | |
| wizard | шебер | |
| feed (RSS) | арна | RSS feed: RSS арнасы |
| tab | қойынды | |
| audit, audit trail | аудит, аудит журналы | |
| repository | репозиторий | |
| checksum | бақылау сомасы | |
| account | тіркелгі | |
| default | әдепкі | default value: әдепкі мән |
| toolbar | құралдар тақтасы | |
| container | контейнер | |
| AI | жасанды интеллект | |
| pattern (name pattern) | өрнек | kept apart from үлгі (template) |
| wildcard | қойылмалы таңба | |
| remote ID | қашықтағы ID | |
| placeholder | толтырғыш | |
| collection | жинақ | information collector: ақпарат жинағыш |
| approve, approval | бекіту | |
| software | бағдарламалық жасақтама | |
| creator / modifier | жасаушы / өзгертуші | |
| request (HTTP) | сұрау | |
| query | сұраныс | search query: іздеу сұранысы |
| handler / provider / listener | өңдегіш / провайдер / тыңдаушы | |
| engine | қозғалтқыш | |
| release / upgrade | шығарылым / жаңарту | update: жаңарту |
| backup | сақтық көшірме | |
| maintenance | техникалық қызмет көрсету | |
| directory | каталог | |
| header (HTTP) | тақырып | |
| page layout (pagelayout template) | бет үлгісі | kept apart from макет (Exponential Layouts) |
| passkey | кіру кілті | |
| two-step sign-in | екі қадамды кіру | |
| device / browser | құрылғы / браузер | |
| link | сілтеме | |
| registration, register | тіркелу, тіркеу | |
| payment gateway | төлем шлюзі | |
| form | пішін | |
| comment | пікір | |
| newsletter / subscription | ақпараттық бюллетень / жазылым | |
| field | өріс | |
| value | мән | |
| list | тізім | |
| page | бет | |
| title / name | тақырып / атауы | the column "Name": Атауы |
| description | сипаттама | |
| type | түрі | |
| path | жол | |
| width / height | ені / биіктігі | |
| size | өлшемі | |
| none | жоқ | |
| yes / no | иә / жоқ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Дүкен, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is сақтау.
- "Add" and "enable" are both қосу in Kazakh software; the context tells them apart, and where both stand side by
  side the setting is іске қосу.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
