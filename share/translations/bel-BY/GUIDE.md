# Belarusian (bel-BY): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Belarusian (беларуская мова) in the official orthography of 2008 (the norm of schools, dictionaries and state
media), in the neutral, polite register of Belarusian software localisations. No other locale falls back to `bel-BY`.

- Use Belarusian words and forms, never Russian ones: "файл", "налады", "карыстальнік", "выдаліць", "захаваць", not
  "настройки", "пользователь". Russian (`rus-RU`) and Ukrainian (`ukr-UA`) may help with the meaning and the
  terminology; the text itself is translated from the English.
- Spelling: akanne and dzekanne as written (рэдагаваць, налады, дзеянне), the letter **ў** after a vowel (паўтарыць,
  аўтар, з'яўляецца), the apostrophe **'** (U+0027) before я, е, ё, ю, і after a consonant (аб'ект, з'ява), the soft
  sign as the norm wants it. Doubled consonants of the norm: заданне, дзеянне, значэнне.
- Loan words in their established Belarusian form: сервер, кэш, шаблон, модуль, пакет, браўзер, інтэрфейс,
  праграма, сістэма, канфігурацыя, катэгорыя, URL-адрас.
- Short, plain sentences.

## 2. Form of address

- The user is addressed with the polite **Вы** form, written in lower case inside a sentence (**вы**, вашы, вам), as
  Belarusian software does.
- **Buttons, menu items and links**: the infinitive, as Belarusian interfaces do: Захаваць, Скасаваць, Выдаліць,
  Прыбраць, Рэдагаваць, Дадаць, Стварыць, Шукаць, Выбраць, Адправіць, Праглядзець, Апублікаваць, Загрузіць,
  Спампаваць, Ужыць, Аднавіць.
- **Titles, column headings and tabs**: nouns or noun phrases: Налады, Пераклады, Галоўнае размяшчэнне, Назва.
- **Instructions**: the imperative of the вы form: "Выберыце аб'ект і націсніце \"OK\".", "Калі ласка" where the
  source has please.
- **Confirmations and questions**: "Вы сапраўды хочаце ...?".
- **What the user can or may do**: "Вы можаце ...", "Вы не можаце ..."; "You do not have permission" is "У вас няма
  дазволу на ...".
- **Messages about a result**: the past tense or the short participle: "Чарнавік захаваны.", "Файл паспяхова
  загружаны.".

## 3. Script and typography

- **Cyrillic** alphabet of Belarusian, with **і** (never и), **ў**, **ё** where the norm has it, and the apostrophe
  **'** (U+0027); never щ, ъ, и.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder (no case ending on a
  placeholder); rephrase so that the placeholder needs no inflection ("Аб'ект: %name"). The same for code, file names,
  INI settings and product names, which stay in Latin letters.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source; a comma before subordinate clauses (што, які, калі) as the norm wants it.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Belarusian | Note |
|---|---|---|
| content | змесціва | content object: аб'ект змесціва |
| object | аб'ект | |
| node | вузел | main node: галоўны вузел; parent node: бацькоўскі вузел |
| location | размяшчэнне | main location: галоўнае размяшчэнне |
| item (list entry) | элемент | |
| tree | дрэва | tree menu: меню-дрэва; content structure: структура змесціва |
| class | клас | content class: клас змесціва; class group: група класаў |
| attribute | атрыбут | |
| datatype | тып даных | |
| version | версія | version history: гісторыя версій |
| draft | чарнавік | store draft: Захаваць чарнавік |
| publish | апублікаваць | published: апублікаваны; publishing: публікацыя |
| send for publishing | адправіць на публікацыю | |
| discard | адмовіцца ад | discard draft: Адмовіцца ад чарнавіка |
| archived / pending / rejected | архіўны / у чаканні / адхілены | |
| siteaccess | доступ да сайта | siteaccess as a setting name stays in code |
| site, website | сайт, вэб-сайт | |
| section | раздзел | the Exponential section; an INI section: раздзел INI |
| role | роля | |
| policy | палітыка | |
| limitation | абмежаванне | |
| permission | дазвол | access denied: доступ забаронены |
| module | модуль | |
| function | функцыя | |
| user / user group | карыстальнік / група карыстальнікаў | |
| group | група | |
| anonymous | ананімны | anonymous user: ананімны карыстальнік |
| administrator | адміністратар | administration interface: адміністрацыйны інтэрфейс |
| username | імя карыстальніка | |
| password | пароль | |
| log in / log out | увайсці / выйсці | the act: уваход / выхад |
| e-mail | электронная пошта | e-mail address: адрас электроннай пошты |
| cache | кэш | clear cache: Ачысціць кэш |
| template | шаблон | override template: шаблон перавызначэння |
| override | перавызначэнне | |
| fallback | рэзервовы | fallback language: рэзервовая мова |
| design | дызайн | |
| workflow | працоўны працэс | |
| trigger | трыгер | |
| event | падзея | |
| trash | сметніца | empty trash: Ачысціць сметніцу; move to trash: Перамясціць у сметніцу |
| bookmark | закладка | |
| settings | налады | configuration: канфігурацыя |
| setup | канфігурацыя | the Setup tab |
| layout | макет | Exponential Layouts stays |
| block / zone | блок / зона | |
| extension | пашырэнне | |
| package | пакет | |
| search | пошук | to search: шукаць; search index: пошукавы індэкс |
| log | журнал | a log file: файл журнала; never "log in" |
| queue | чарга | job queue: чарга задач |
| job / task | задача | |
| kernel, core | ядро | |
| server | сервер | |
| database | база даных | |
| folder / directory | папка / каталог | |
| file | файл | |
| image | выява | thumbnail: мініяцюра |
| preview | папярэдні прагляд | |
| upload / download | загрузіць / спампаваць | |
| export / import | экспартаваць / імпартаваць | |
| remove | прыбраць | take out of a list or place |
| delete | выдаліць | destroy; deleted: выдалены |
| edit | рэдагаваць | |
| editor | рэдактар | person or software |
| save / store | захаваць | the verb "Store" of the forms |
| Store (shop tab) | Крама | the shop, as "Shop" |
| cancel | скасаваць | |
| OK | OK | allowlisted where it stands alone |
| apply | ужыць | |
| back | назад | |
| next / previous | наступны / папярэдні | |
| enable / disable | уключыць / выключыць | enabled / disabled: уключаны / выключаны |
| active / inactive | актыўны / неактыўны | |
| run | запусціць | |
| start / stop | пачаць / спыніць | |
| session | сеанс | |
| hidden / visible | схаваны / бачны | hide / reveal: схаваць / паказаць |
| archive | архіў | |
| order (sort) | парадак | sort order: парадак сартавання |
| order (shop) | заказ | |
| shop | крама | |
| basket | кошык | |
| product | тавар | |
| customer | кліент | |
| discount | зніжка | |
| VAT | ПДВ | |
| price / currency | цана / валюта | |
| tag | тэг | |
| translation | пераклад | |
| language | мова | main language: асноўная мова |
| locale | лакаль | |
| state | стан | the object state |
| status | статус | |
| view (button) | праглядзець | a module view (noun): выгляд |
| format | фармат | |
| sort | сартаваць | |
| filter | фільтр | |
| select | выбраць | select all: Выбраць усе; selected: выбраны |
| copy / move | капіяваць / перамясціць | |
| add / create / new | дадаць / стварыць / новы | |
| restore | аднавіць | |
| install / uninstall | усталяваць / выдаліць | |
| related objects | звязаныя аб'екты | relation: сувязь |
| dashboard | панэль кіравання | |
| identifier / ID | ідэнтыфікатар / ID | |
| priority | прыярытэт | |
| owner | уладальнік | |
| modified / created | зменены / створаны | |
| date / time | дата / час | |
| error / warning | памылка / папярэджанне | |
| help | даведка | |
| notification | апавяшчэнне | |
| account | уліковы запіс | |
| default | па змаўчанні | |
| link | спасылка | |
| none / all | няма / усе | |
| yes / no | так / не | |
| name / title / description | імя / назва / апісанне | |
| value / type / list | значэнне / тып / спіс | |
| page / path | старонка / шлях | |
| required / optional | абавязковы / неабавязковы | |
| options | параметры | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Крама, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  Захаваць.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Увайсці).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, OK ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
