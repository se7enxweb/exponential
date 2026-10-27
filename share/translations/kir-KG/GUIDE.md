# Kyrgyz (kir-KG): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard literary Kyrgyz (кыргыз адабий тили), in the neutral, technical register of current Kyrgyz software (the
Kyrgyz localisations of Windows, Google and Mozilla), of the Kyrgyz Wikipedia and of the state language commission.
No other locale falls back to `kir-KG`; Russian (`rus-RU`), Kazakh (`kaz-KZ`) and Turkish (`tur-TR`) only help with
terminology.

- Kyrgyz words are used where the software already has them: сактоо, жок кылуу, жөндөөлөр, жүктөп алуу, жүктөө,
  шилтеме, өтмөк, сырсөз. Internationalisms of computing keep their usual spelling: сервер, скрипт, модуль, кэш,
  файл, браузер, папка.
- No Russian words where Kyrgyz has its own (not "настройки", "пароль", "ссылка" for жөндөөлөр, сырсөз, шилтеме).
- Kyrgyz suffixes follow vowel harmony (including the rounded ө, ү) and the final sound of the stem: файлды,
  серверге, модулдун, кэшти.

## 2. Form of address

The interface addresses the user with the polite **Сиз** (you), **Сиздин** (your), as Kyrgyz software does.

- **Buttons, menu items and links**: the verbal noun in -уу/-үү/-оо/-өө, as Kyrgyz software does: Сактоо, Жокко
  чыгаруу, Жок кылуу, Түзөтүү, Жарыялоо, Жарыялоого жөнөтүү, Тандалгандарды алып салуу, Таштанды кутусуна
  жылдыруу. Not the imperative (Сактаңыз) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Жөндөөлөр, Котормолор, Негизги орун.
- **Instructions**: the polite imperative in -ңыз/-ңиз/-ңуз/-ңүз: "Элементти тандап, «OK» баскычын басыңыз.",
  "Күтө туруңуз ...". A reference ("see ...") is "... караңыз".
- **Confirmations and questions**: "Бул классты чын эле жок кылгыңыз келеби?", "... каалайсызбы?".
- **What the user can or may do**: "... аласыз", "... албайсыз", or impersonal: "Негизги тилди өзгөртүүгө
  болбойт ...". "You do not have permission" is "Сизде ... уруксат жок".
- Not allowed: the familiar сен / сенин, slash forms.

## 3. Script and typography

- **Cyrillic**, the script of the shipped locale (`share/locale/kir-KG.ini`, LanguageName Кыргызча) and of the
  software in use in Kyrgyzstan. The 36 letters of the Kyrgyz Cyrillic alphabet are written with their own code
  points: ң (U+04A3), ө (U+04E9), ү (U+04AF), never look-alike Latin letters. The Russian letters ё, ц, щ, ъ, ь,
  э, ю, я appear only in loanwords.
- **Latin text inside Kyrgyz**: product names, codes and acronyms stay in Latin letters. A Kyrgyz suffix after
  them follows a hyphen (Exponential-дын, PHP-ни), never an apostrophe; a wording without a suffix often reads
  better: "PHP файлы", "URL дареги", "HTML коду".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Kyrgyz letter or hyphenated suffix glued
  to a placeholder: "%count объект", not "%count-ти" or "%countобъект". Put the placeholder where it needs no
  suffix: "Объект: %name", "%count элемент".
- **Numbers**: a noun after a number stays in the singular, as Kyrgyz grammar wants it: "%count объект", not
  "объекттер". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon. Kyrgyz puts the verb at the end of the sentence and the modifier before its noun.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kyrgyz | Note |
|---|---|---|
| content | мазмун | |
| content object | мазмун объекти | |
| object | объект | |
| node | түйүн | main node: негизги түйүн; parent node: ата түйүн; child: туунду |
| location | орун | main location: негизги орун |
| item (list entry) | элемент | |
| class | класс | the content class; class group: класстар тобу; a PHP class: PHP классы |
| content class | мазмун классы | |
| attribute | атрибут | |
| datatype | маалымат түрү | |
| version | версия | version history: версиялар тарыхы |
| draft | долбоор | store draft: долбоорду сактоо |
| publish | жарыялоо | published: жарыяланган |
| send for publishing | жарыялоого жөнөтүү | |
| discard | таштоо | discard draft: долбоорду таштоо |
| archived | архивделген | |
| pending | күтүүдө | |
| rejected | четке кагылган | |
| siteaccess | сайтка кирүү чекити | the configuration unit |
| site, website | сайт, веб-сайт | |
| section | бөлүм | the Exponential section |
| role | роль | |
| policy | саясат | |
| limitation | чектөө | |
| permission | уруксат | access rights: кирүү укуктары |
| module | модуль | |
| function | функция | |
| user | колдонуучу | |
| user group | колдонуучулар тобу | |
| group | топ | |
| anonymous | аноним | anonymous user: аноним колдонуучу |
| administrator | администратор | |
| administration (interface) | башкаруу интерфейси | |
| username | колдонуучу аты | the column "Login" too |
| password | сырсөз | |
| log in / sign in | кирүү | |
| log out / sign out | чыгуу | |
| e-mail | электрондук почта | e-mail address: электрондук почта дареги |
| cache | кэш | clear cache: кэшти тазалоо |
| template | шаблон | |
| override | кайра аныктоо | override template: кайра аныктоочу шаблон |
| fallback | камдык | fallback language: камдык тил |
| design | дизайн | |
| workflow | иш агымы | |
| trigger | триггер | |
| event | окуя | |
| trash | таштанды кутусу | empty trash: таштанды кутусун бошотуу |
| bookmark | кыстарма | |
| settings | жөндөөлөр | |
| setup | жөндөө | the Setup tab; installation: орнотуу |
| layout | макет | Exponential Layouts |
| block | блок | |
| zone | аймак | |
| extension | кеңейтүү | |
| package | пакет | |
| search | издөө | |
| log | журнал | log file: журнал файлы |
| queue | кезек | |
| job / task | тапшырма | |
| cron | cron | cron job: cron тапшырмасы |
| script | скрипт | |
| worker | иштетүү процесси | |
| kernel, core | ядро | |
| server | сервер | |
| database | маалымат базасы | |
| folder | папка | |
| file | файл | |
| image | сүрөт | |
| preview | алдын ала көрүү | |
| upload | жүктөө | |
| download | жүктөп алуу | |
| export / import | экспорттоо / импорттоо | |
| remove | алып салуу | take out of a list or a place |
| delete | жок кылуу | destroy |
| edit | түзөтүү | |
| editor | редактор | |
| save / store | сактоо | |
| Store (shop tab) | Дүкөн | the word the shop menu uses for "Shop" |
| cancel | жокко чыгаруу | |
| OK | OK | |
| apply | колдонуу | |
| back | артка | |
| next / previous | кийинки / мурунку | |
| enable / disable | иштетүү / өчүрүү | a setting or feature |
| enabled / disabled | иштетилген / өчүрүлгөн | |
| active / inactive | активдүү / активдүү эмес | |
| run | аткаруу | a script or job; start a service: ишке киргизүү |
| start / stop | баштоо / токтотуу | |
| session | сеанс | |
| hidden / visible | жашырылган / көрүнүүчү | hide / reveal: жашыруу / көрсөтүү |
| archive | архив | |
| order (sort) | тартип | sort order: иреттөө тартиби |
| order (shop) | буйрутма | |
| shop | дүкөн | |
| basket | себет | kept apart from таштанды кутусу |
| product | товар | |
| customer | кардар | |
| discount | арзандатуу | |
| VAT | КНС | VAT type: КНС түрү |
| price | баа | |
| currency | валюта | |
| tag | тег | keyword tag; an HTML tag: HTML теги |
| translation | котормо | |
| language | тил | main language: негизги тил |
| locale | локаль | |
| state | абал | the object state; state group: абалдар тобу |
| status | статус | |
| view (button) | көрүү | a module view: көрүнүш |
| format | формат | |
| sort | иреттөө | |
| filter | чыпка | |
| select | тандоо | selected: тандалган |
| browse | карап чыгуу | |
| copy | көчүрүү | |
| move | жылдыруу | |
| add | кошуу | |
| create | түзүү | |
| new | жаңы | |
| restore | калыбына келтирүү | |
| install / uninstall | орнотуу / алып салуу | |
| sub items | ички элементтер | |
| subtree | ички дарак | |
| related objects | байланышкан объекттер | relation: байланыш |
| URL alias | URL псевдоними | |
| redirect | кайра багыттоо | |
| dashboard | башкаруу панели | |
| content structure | мазмун түзүмү | |
| media library | медиа китепкана | |
| identifier / ID | идентификатор / ID | |
| priority | артыкчылык | |
| owner | ээси | |
| modified / created | өзгөртүлгөн / түзүлгөн | |
| date / time | күнү / убактысы | |
| error / warning | ката / эскертүү | |
| help | жардам | |
| notification | билдирүү | |
| wizard | чебер | |
| feed (RSS) | түрмөк | RSS feed: RSS түрмөгү |
| tab | өтмөк | |
| audit, audit trail | аудит, аудит журналы | |
| repository | репозиторий | |
| checksum | текшерүү суммасы | |
| account | каттоо эсеби | |
| default | демейки | default value: демейки маани |
| toolbar | куралдар панели | |
| container | контейнер | |
| AI | жасалма интеллект | |
| pattern (name pattern) | калып | kept apart from шаблон (template) |
| wildcard | ордун басуучу белги | |
| remote ID | алыскы ID | |
| placeholder | толтургуч | |
| collection | жыйнак | information collector: маалымат чогулткуч |
| approve, approval | бекитүү | |
| software | программалык камсыздоо | |
| creator / modifier | түзгөн / өзгөрткөн | |
| request (HTTP) | суроо | |
| query | сурам | search query: издөө сурамы |
| handler / provider / listener | иштеткич / провайдер / уккуч | |
| engine | кыймылдаткыч | |
| release / upgrade | чыгарылыш / жаңыртуу | update: жаңыртуу |
| backup | камдык көчүрмө | |
| maintenance | техникалык тейлөө | |
| directory | каталог | |
| header (HTTP) | баш сап | |
| page layout (pagelayout template) | барак шаблону | kept apart from макет (Exponential Layouts) |
| passkey | кирүү ачкычы | |
| two-step sign-in | эки баскычтуу кирүү | |
| device / browser | түзмөк / браузер | |
| link | шилтеме | |
| registration, register | катталуу, каттоо | |
| payment gateway | төлөм шлюзу | |
| form | форма | |
| comment | комментарий | |
| newsletter / subscription | маалымат бюллетени / жазылуу | |
| field | талаа | |
| value | маани | |
| list | тизме | |
| page | барак | |
| title / name | аталыш / аты | the column "Name": Аты |
| description | сүрөттөмө | |
| type | түрү | |
| path | жол | |
| width / height | туурасы / бийиктиги | |
| size | өлчөмү | |
| none | жок | |
| yes / no | ооба / жок | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Дүкөн, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is сактоо.
- "Disable" is өчүрүү and "delete" is жок кылуу, so that a switched-off setting is never read as a removed one.
