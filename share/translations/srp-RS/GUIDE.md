# Serbian (srp-RS Cyrillic, ser-SR Latin): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated and cross-checked in October 2026; review by native speakers is welcome. **Only the Cyrillic catalogue is translated.** The
Latin catalogue `ser-SR` is derived from it by a letter-for-letter transliteration: a translator never
writes Latin Serbian.

## 1. Language and register

Standard Serbian of Serbia: **Ekavian** pronunciation reflex (место, време, следећи, лепо), **Cyrillic** script for
`srp-RS`. The tone is neutral and professional, as in Microsoft Serbian, KDE and Mozilla sr; terminology follows
Microsoft/Mozilla where they agree. Not Croatian or Bosnian forms (`bos-BA`, `cro-HR` are separate catalogues);
`locale_check.py` reports Ijekavian and Croatian forms (`ijek`): мјесто, вријеме, тисућа, точно (Serbian: место, време, хиљада, тачно). The word датотека is Serbian and fine.

**The old catalogue** (5,358 of 7,502 messages finished, hand-made; `ser-SR` carries 98 percent of the same text in
Latin, see README) is the `prev` hint. Its conventions stay: buttons in the **imperative singular** (Измени, Уклони,
Одустани, Објави, Примени), instructions in sentences in the **plural imperative** (367 messages: Изаберите, Унесите),
the terms Чвор, Класа, Верзија, Скица, Улога, Шаблон, Објекат, Датотека, Лозинка, Корисник. Corrected where the hint
shows them:

| Old | New | Why |
|---|---|---|
| Обриши for Remove | Уклони | remove and delete are kept apart; Delete: Обриши |
| Сегмент / секција for section | секција | one word everywhere (the old catalogue had both) |
| Смеће for trash | Корпа за отпатке | "Смеће" is rubbish itself; Microsoft: Корпа за отпатке |
| Ознаке за књигу for bookmarks | Обележивачи | calque of "bookmark" |
| Корпа for trash and basket | Корпа for the shop basket only | the trash is "Корпа за отпатке" |
| Учитај for Upload and Download | Отпреми / Преузми | upload and download are kept apart |
| Сними for Save | Сачувај | Microsoft/Mozilla |
| Приступ сајту for siteaccess | приступ сајту | kept |
| Креирај | Направи | Microsoft: Направи; "Креирај" kept in names of existing UI terms only |

## 2. Form of address

The user is addressed in the **polite plural (ви)**, written with a **lower-case** "ви/вас/ваш" inside a sentence
(Microsoft practice; the old catalogue has 79 lower-case and 23 capital uses, now unified). Capital Ви only at the
start of a sentence.

- **Buttons, menu items, links and commands** are the **imperative singular**: Сачувај, Измени, Обриши, Уклони, Додај,
  Направи, Одустани, Примени, Објави, Отпреми, Преузми, Затвори, Назад. Fixed forms: У реду (OK; Latin "OK" for the
  acronym key), Да, Не, Даље, Претходно.
- **Titles, column headings and tabs** are nouns: Структура садржаја, Кориснички налози, Подешавања обавештења.
- **Instructions in sentences** are the **plural imperative**: "Изаберите локацију.", "Унесите исправну адресу
  е-поште.", "Проверите везу и покушајте поново." "Молимо" is not translated.
- **Questions and confirmations**: "Да ли сте сигурни да желите да обришете ову класу?"
- **Error messages** state the fact: "Није могуће направити скицу.", "Датотека није пронађена.", "Немате дозволу за
  брисање класа."
- **Status messages**: "Скица је обрисана.", "Подешавања су сачувана.", "Обрисано је објеката: %count."
- Not allowed: singular "ти"; capital Ви inside a sentence; Croatian/Ijekavian forms.

## 3. Script and typography

- **Alphabet**: Serbian Cyrillic only (30 letters: А Б В Г Д Ђ Е Ж З И Ј К Л Љ М Н Њ О П Р С Т Ћ У Ф Х Ц Ч Џ Ш). No Russian
  letters (ё ы э ю я щ ъ ь), no Latin with Serbian diacritics (č ć š ž đ) in the Cyrillic catalogue, no Latin
  look-alike letters inside a Cyrillic word: reported as `script`. Product names, codes, file names, formats and
  abbreviations stay Latin (Exponential, Solr, PHP, URL, JSON, cron, INI) in both catalogues; `translit.py` leaves
  every non-Cyrillic character untouched, so these never change.
- **Quotation marks**: „…“ for quoted text and names of buttons ("Притисните „Сачувај“."); nested ‚…‘. Straight quotes
  of the source stay inside HTML attributes, code and values typed exactly (reported as `quote` otherwise).
- **Punctuation**: the source's; no space before , . ; : ! ?. The ellipsis is the source's. Dash: the long dash — with spaces for a break,
  a hyphen in compounds with Latin words (URL-адреса, INI-датотека).
- **Case**: sentence case ("Структура садржаја", "Направи објекат").
- **Numbers and units**: digits 0-9; decimal comma and thousands point from the locale; a space before a unit (10 MB).
- **Plural**: the count is not declined by hand: "Објеката: %count" or "Обрисано је објеката: %count".
- **Placeholders** stand free (TRANSLATING.md, rule 1); a Cyrillic ending is never glued to one.
- **Latin catalogue**: derived mechanically. Rules for the derivation (not for the translator): 1:1 mapping per
  letter (љ→lj, њ→nj, џ→dž, ђ→đ, ћ→ć, ч→č, ш→š, ж→ž), uppercase digraphs LJ, NJ, DŽ when a neighbour is uppercase
  (ЉУБАВ→LJUBAV) and Lj, Nj, Dž otherwise; everything else (Latin words, `%placeholders`, `<tags>`, entities, code, URLs, digits,
  punctuation) is untouched. Because the Latin catalogue is a derivative, **write the Cyrillic so that it reads
  correctly in Latin too**: no Cyrillic letter that exists in Latin only as a different word (no mixed alphabets inside
  one word), and Latin product names always in Latin.

## 4. Glossary

Binding for the kernel and every extension (the Latin form is the derived one). Section 5 goes first.

| English | Serbian (Cyrillic) | Note |
|---|---|---|
| content | садржај | |
| content object / object | објекат садржаја / објекат | |
| item (list entry) | ставка | sub items: подставке |
| node / main node | чвор / главни чвор | |
| parent node / child node | родитељски чвор / подређени чвор | |
| location / main location | локација / главна локација | |
| content class / class | класа садржаја / класа | a PHP class: PHP класа |
| class group | група класа | |
| attribute | атрибут | |
| datatype | тип података | |
| version / version history | верзија / историја верзија | |
| draft | скица | Store draft: Сачувај скицу; Discard draft: Одбаци скицу |
| publish / published | објави / објављено | |
| translation | превод | |
| language / main language / content language | језик / главни језик / језик садржаја | |
| interface language | језик интерфејса | |
| siteaccess | приступ сајту | the INI word SiteAccess stays in code |
| site / website | сајт / веб-сајт | |
| design | дизајн | |
| template | шаблон | |
| override / override template | заобилажење / шаблон који заобилази | overridden: заобиђено |
| fallback | резерва | fallback language: резервни језик |
| URL alias / URL wildcard | URL алијас / URL џокер | |
| redirect | преусмеравање | |
| section (Exponential) | секција | a part of a page: одељак |
| object state / state group | стање објекта / група стања | |
| status | статус | |
| trash / empty trash | корпа за отпатке / испразни корпу | |
| restore | врати | |
| bookmark | обележивач | |
| notification | обавештење | |
| dashboard | контролна табла | |
| content structure / media library | структура садржаја / медијска библиотека | |
| collected information | прикупљене информације | |
| form | образац | |
| poll / vote | анкета / глас | |
| comment | коментар | |
| newsletter / subscription | билтен / претплата | |
| feed / RSS feed | извор / RSS извор | |
| user / user group | корисник / група корисника | |
| user account / account | кориснички налог / налог | |
| username / password | корисничко име / лозинка | |
| anonymous user | анонимни корисник | |
| administrator | администратор | |
| log in / log out | пријави се / одјави се | Login as a field: Корисничко име |
| sign up, register | региструј се | |
| role / policy | улога / политика | policy: политика приступа |
| permission / access | дозвола / приступ | |
| access denied | приступ одбијен | |
| limitation | ограничење | |
| assign / unassign | додели / уклони доделу | |
| approve / reject / approval | одобри / одбиј / одобрење | |
| pending | на чекању | |
| owner / author / creator | власник / аутор / творац | |
| editor (person) / editor (software) | уредник / уређивач | |
| save / store | сачувај | |
| edit | измени | |
| delete / remove | обриши / уклони | remove = take out of a list or place; delete = destroy |
| cancel / OK / apply | одустани / У реду / примени | |
| discard | одбаци | |
| create / new / add | направи / нови / додај | |
| copy / move / swap | копирај / премести / замени | |
| back / next / previous | назад / даље / претходно | |
| open / close | отвори / затвори | |
| select / choose | изабери | select all: изабери све |
| browse | прегледај | |
| preview | преглед | verb: прегледај унапред |
| view (button) / view (module view) | прикажи / приказ | |
| upload / download | отпреми / преузми | |
| import / export | увези / извези | |
| search / advanced search | претрага / напредна претрага | |
| filter | филтер | verb: филтрирај |
| sort / sort order | сортирај / редослед | |
| refresh / reload | освежи / поново учитај | |
| enable / disable | омогући / онемогући | |
| activate / deactivate | активирај / деактивирај | |
| install / uninstall | инсталирај / деинсталирај | |
| update / upgrade | ажурирај / надоградња | |
| click / double-click | кликните / двапут кликните | |
| drag / drop | превуци / отпусти | |
| print | штампај | |
| retry | покушај поново | |
| file | датотека | |
| folder / directory | фасцикла / директоријум | |
| path / file system | путања / систем датотека | |
| image / image alias | слика / алијас слике | |
| video / audio | видео / аудио | |
| font | фонт | |
| database / table | база података / табела | |
| field | поље | |
| column / row | колона / ред | |
| data / information | подаци / информације | |
| message | порука | |
| cache / clear cache | кеш / очисти кеш | |
| index / reindex | индекс / поново индексирај | |
| search engine | претраживач | |
| keyword | кључна реч | |
| tag (keyword) / tag (HTML) | ознака / HTML ознака | |
| format | формат | |
| settings / setup | подешавања / подешавање | |
| configuration | конфигурација | |
| INI file | INI датотека | |
| default | подразумевано | |
| cron job | cron задатак | |
| package | пакет | |
| extension | екстензија | |
| plugin / provider | додатак / провајдер | |
| server / web server | сервер / веб сервер | |
| network | мрежа | |
| software / program | софтвер / програм | |
| code / source code | кôд / изворни кôд | |
| function / method | функција / метода | |
| variable / parameter | променљива / параметар | |
| string / character | ниска / знак | |
| encoding | кодирање | |
| interface | интерфејс | |
| module | модул | |
| link / broken link | веза / неисправна веза | |
| menu / context menu | мени / контекстуални мени | |
| toolbar / sidebar | трака са алаткама / бочна трака | |
| tab | картица | |
| page / layout | страница / распоред | Exponential Layouts: распореди |
| block / zone | блок / зона | |
| navigation / breadcrumb | навигација / путања навигације | |
| icon / button | икона / дугме | |
| checkbox / drop-down list / list | поље за потврду / падајућа листа / листа | |
| dialog / wizard | дијалог / чаробњак | |
| window / browser | прозор / прегледач | |
| help / about | помоћ / о програму | |
| log / log viewer | дневник / приказивач дневника | |
| debug | отклањање грешака | |
| error / warning / notice | грешка / упозорење / напомена | |
| report / statistics | извештај / статистика | |
| modified / created | измењено / направљено | |
| name / description / title | назив / опис / наслов | |
| identifier / ID / remote ID | идентификатор / ID / удаљени ID | |
| remote / local | удаљено / локално | |
| hidden / visible | скривено / видљиво | |
| priority | приоритет | |
| unknown / none / all / any | непознато / ниједан / сви / било који | |
| archive | архива | |
| backup | резервна копија | |
| storage / memory | складиште / меморија | |
| process / thread | процес / нит | |
| queue / job / task | ред / посао / задатак | |
| schedule | распоред | |
| timeout | истек времена | |
| request / response | захтев / одговор | |
| session / cookie | сесија / колачић | |
| token / API key | токен / API кључ | |
| credentials / certificate | акредитиви / сертификат | |
| authentication | потврда идентитета | |
| encryption | шифровање | |
| locale / time zone | локализација / временска зона | |
| project | пројекат | |
| shop / basket | продавница / корпа | |
| order (shop) / order (sorting) | наруџбина / редослед | |
| customer / product | купац / производ | |
| VAT / discount / price / currency | ПДВ / попуст / цена / валута | |
| wish list | листа жеља | |
| AI / model / prompt | ВИ / модел / упит | |
| embedding / vector / chunk | угнежђивање / вектор / одломак | |
| health check | провера исправности | |
| confirmation question | Да ли сте сигурни да желите …? | |

## 5. Decisions after the translation

Settled before the round (the context-dependent words of the previous rounds); they take precedence over the glossary.

| English | Translation | Distinction |
|---|---|---|
| Store (button: save) | Сачувај | the shop tab of the admin header (kernel\|design/admin/pagelayout): Продавница; secrets: context override |
| Order (shop) / Order (sort order) | наруџбина / редослед | the context decides: context overrides |
| State (object state) / State (address) | стање / држава | the address field of the shop: држава/покрајина |
| Login (button, verb) / Login (the user name) | Пријави се / Корисничко име | |
| View (button) / View (module view) | Прикажи / Приказ | |
| Up / Down (move in a list) | Горе / Доле | expqueue "Up" (how long a worker runs): Време рада |
| Delete (the key) | тастер Delete | key names stay Latin |
| True / False (setting values) | Тачно / Нетачно | code literals true / false stay Latin |
