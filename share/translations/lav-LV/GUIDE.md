# Latvian (lav-LV): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Latvian (literārā latviešu valoda) as used in Latvia, following the current spelling norms of the
Latvian Language Expert Commission (LZA Terminoloģijas komisija) and the terminology of the Latvian versions of
Windows and Microsoft Office. No other locale falls back to this catalogue. The tone is neutral, concise and
professional. Avoid colloquial forms, barbarisms and needless English words (no "seivot", "apdeitot", "klikot").

## 2. Form of address

- **Buttons, menu items, links and action labels** use the infinitive, as in the Latvian versions of Windows and
  Office: "Saglabāt", "Dzēst", "Rediģēt", "Pievienot", "Atcelt", "Izveidot jaunu", "Lietot izmaiņas",
  "Nosūtīt publicēšanai", "Atlasīt visu", "Notīrīt kešatmiņu".
- **Titles, column headings and tabs** are nouns: "Satura struktūra", "Lietotāju konti", "Izkārtojuma tips",
  "Paziņojumu iestatījumi", "Multivides bibliotēka".
- **Sentences, hints, questions and error messages** address the user with the formal **jūs**, written in lower
  case (jūs, jums, jūsu), and the 2nd person plural imperative for instructions:
  - "Vai tiešām vēlaties noņemt šo klasi?"
  - "Lūdzu, izvēlieties atrašanās vietu" (Please choose a location)
  - "Jums nav atļaujas noņemt klases."
  - "Noklikšķiniet uz pogas „Atcelt“ un mēģiniet noņemt tikai tās atrašanās vietas, kuras jums ir atļauts
    noņemt."
  - "Versiju vēstures iestatījumus varat mainīt failā content.ini ..."
  - Error messages: "Neizdevās saglabāt melnrakstus", "Formas marķiera trūkst vai tas ir nepareizs. Lūdzu,
    atkārtoti ielādējiet lapu."
- **Tooltips that describe a click** use "Noklikšķiniet ..., lai ...": "Noklikšķiniet uz ikonas, lai atvērtu
  konteksta izvēlni."
- **Status messages** are passive or impersonal: "Apakškoks ir noņemts.", "Mezglu piešķīrumu sarakstā ir izdzēsts
  mezgls.", "Netika atrasta neviena cron uzdevuma izpilde".
- Not allowed: the familiar form (tu, tev, tavs, vēlies, vari, izvēlies), "Jūs/Jūsu" with a capital letter inside
  a sentence (the capital is a convention of personal letters, not of software), the 2nd person plural imperative
  on a button ("Saglabājiet" is wrong on a button; write "Saglabāt"), and the infinitive as an instruction inside
  a sentence ("Izmantot šo lauku, lai ..." is wrong; write "Šis lauks tiek izmantots, lai ...").

## 3. Script and typography

- Latin script with the full set of Latvian letters (ā č ē ģ ī ķ ļ ņ š ū ž and their capitals). Never replace
  them with digraphs or plain letters ("sh", "a" for "ā"). Sentence case: "Satura struktūra", "Objektu stāvokļi";
  only the first word and proper names are capitalised. Names of months and days are lower case.
- Quotation marks in running text: „…“; nested quotes: ‚…‘. Inside HTML attributes and JavaScript the
  straight quotes of the source stay.
- Latvian has three plural forms (CLDR: zero for 0 and numbers ending in 0 or 11–19, one for numbers ending in 1
  except 11, other). The source only has "item(s)"; never write "vienums(-i)" or "vienumi(s)". Use a label form in
  which the number stands after a colon: "Objekti: %count", "Darba procesi: %count", "Melnraksti: %count".
- **Placeholders and case endings.** Latvian declines nouns, but a placeholder is never declined and nothing is
  glued to it (`%names` would be a different placeholder). Put the placeholder in apposition after a declined head
  noun, or after a colon:
  - "Rediģēt sadaļu %name", "Klase <%class_name>", "Lietotāja %user privilēģijas" (not "%user privilēģijas" or
    "%usera privilēģijas").
  - "Satura objekts %id nepastāv vai ir izdzēsts.", "sāka lietotājs %user".
  - Where a case is unavoidable, choose another construction: "Parādīt: %name" instead of an accusative.
- Numbers: decimal comma (3,5), a non-breaking space as thousands separator (12 500); dates as 23.10.2026. or
  2026. gada 23. oktobris. Ordinal numbers take a full stop: "3. versija". Digits stay 0–9.
- Time units after a number: "min", "h", "s", "d"; "pirms %n min".
- Abbreviations from the source that are codes or formats (URL, HTML, ID, PDF) stay; "ID" is neuter and
  indeclinable in text ("objekta ID").
- The product name in running text, where grammar needs a case: "sistēma Exponential", "sistēmā Exponential".

## 4. Glossary

| English | Latvian | Note |
|---|---|---|
| content | saturs | |
| content object | satura objekts | |
| object | objekts | |
| item | vienums | pl. vienumi; list entry |
| node | mezgls | main node: galvenais mezgls |
| location | atrašanās vieta | |
| main location | galvenā atrašanās vieta | |
| content class | satura klase | |
| class | klase | always the content class; a PHP class: PHP klase |
| class group | klašu grupa | |
| attribute | atribūts | |
| datatype | datu tips | |
| version | versija | version history: versiju vēsture; version number: versija Nr. %version |
| draft | melnraksts | Store draft: Saglabāt melnrakstu; Discard draft: Atmest melnrakstu |
| publish | publicēt | Send for publishing: Nosūtīt publicēšanai |
| published | publicēts | |
| publishing | publicēšana | |
| translation | tulkojums | |
| language | valoda | |
| main language | galvenā valoda | |
| content language | satura valoda | interface language: saskarnes valoda |
| siteaccess | vietnes piekļuve | pl. vietnes piekļuves; a fixed term, never "sitepiekļuve" |
| site / website | vietne | |
| design (eZ design) | dizains | |
| structure | struktūra | |
| template | veidne | template editor: veidņu redaktors |
| override | aizstāšana | override template: aizstājošā veidne; overridden: aizstāts |
| fallback | rezerves | fallback language: rezerves valoda; fallback chain: rezerves ķēde |
| alias / URL alias | aizstājvārds / URL aizstājvārds | image alias: attēla aizstājvārds |
| wildcard (URL) | aizstājējzīme | URL wildcard: URL aizstājējzīme |
| redirect | novirzīt | noun: novirzīšana |
| forward (URL) | pārsūtīt | |
| cache | kešatmiņa | |
| clear cache | notīrīt kešatmiņu | |
| workflow | darbplūsma | |
| workflow event | darbplūsmas notikums | |
| event | notikums | |
| trigger | trigeris | |
| role | loma | |
| policy | politika | pl. politikas |
| permission | atļauja | |
| access | piekļuve | access denied: piekļuve liegta |
| limitation (policy) | ierobežojums | |
| user | lietotājs | |
| user group | lietotāju grupa | |
| user account | lietotāja konts | user accounts: lietotāju konti |
| anonymous user | anonīms lietotājs | |
| administrator | administrators | |
| admin interface | administrēšanas saskarne | |
| interface / user interface | saskarne / lietotāja saskarne | one word for a PHP interface and the user interface |
| log in / log out | pierakstīties / izrakstīties | |
| login (field) / username | lietotājvārds | |
| password | parole | |
| e-mail | e-pasts | |
| section | sadaļa | always the Exponential section; never "nodaļa" or "sekcija" |
| object state | objekta stāvoklis | |
| state | stāvoklis | |
| state group | stāvokļu grupa | |
| relation | relācija | |
| related objects | saistītie objekti | reverse related: objekti, kas atsaucas uz šo objektu |
| trash | atkritne | |
| bookmark | grāmatzīme | |
| sub-items / children | apakšvienumi | |
| parent | vecākvienums | parent node: vecākmezgls |
| subtree | apakškoks | |
| owner | īpašnieks | |
| creator | autors | |
| modifier | modificētājs | |
| created / modified | izveidots / modificēts | column headings |
| layout (Exponential Layouts) | izkārtojums | layout type: izkārtojuma tips; the name Exponential Layouts stays |
| layout (directory or release structure) | struktūra | |
| block | bloks | |
| zone | zona | |
| setting / settings | iestatījums / iestatījumi | configuration, never the page layout |
| Setup (admin tab) | Iestatīšana | the installation setup and its wizard: see section 5 |
| configuration / configure | konfigurācija / konfigurēt | |
| Content structure | Satura struktūra | |
| Media library | Multivides bibliotēka | |
| Dashboard | Informācijas panelis | |
| extension | paplašinājums | |
| package | pakotne | |
| module | modulis | |
| view (noun, module view) | skats | "Module view": moduļa skats |
| View (button) | Skatīt | |
| function | funkcija | |
| search | meklēt | noun: meklēšana; search engine: meklētājprogramma |
| search index | meklēšanas indekss | |
| index (verb) / reindex | indeksēt / atkārtoti indeksēt | noun: atkārtota indeksēšana |
| index (database) | indekss | index file: indeksa fails |
| cronjob | cron uzdevums | |
| schedule | grafiks | verb: ieplānot |
| job | darbs | |
| queue | rinda | job queue: darbu rinda |
| worker | darba process | pl. darba procesi |
| task | uzdevums | |
| run (verb) | palaist | |
| run (noun) | izpilde | a cron, index or program run |
| dry run | izmēģinājuma izpilde | |
| self-test | pašpārbaude | |
| log | žurnāls | never the verb "log in" |
| log file | žurnālfails | |
| rotation / rotate (log) | rotācija / rotēt | |
| audit / audit trail | audits / audita žurnāls | |
| notification | paziņojums | |
| notice | paziņojums | |
| collaboration | sadarbība | |
| preview | priekšskatījums | verb: priekšskatīt |
| ID | ID | |
| remote ID | attālais ID | |
| identifier | identifikators | |
| file | fails | |
| folder | mape | |
| directory | direktorijs | in file system paths |
| path | ceļš | |
| image | attēls | |
| media | multivide | |
| thumbnail | sīktēls | |
| upload / download | augšupielādēt / lejupielādēt | |
| export / import | eksportēt / importēt | |
| shop | veikals | |
| Store (shop tab) | Veikals | every other "Store" is the verb "Saglabāt" |
| basket | grozs | |
| order (shop) | pasūtījums | order status: pasūtījuma statuss |
| order (sorting) | secība | sort order: kārtošanas secība |
| customer | klients | |
| product | produkts | |
| checkout | norēķināšanās | |
| VAT / VAT type | PVN / PVN veids | |
| discount | atlaide | |
| discount rule | atlaides kārtula | |
| rule | kārtula | |
| price | cena | |
| currency | valūta | |
| wish list | vēlmju saraksts | |
| payment gateway | maksājumu vārteja | |
| newsletter | biļetens | |
| subscriber / subscription / subscribe | abonents / abonements / abonēt | |
| tag (keyword, eztags) | tags | pl. tagi; as in Windows Explorer |
| keyword | atslēgvārds | |
| tag (HTML/XML) | HTML tags / XML tags | always with HTML or XML before it, to keep it apart from eztags |
| custom tag | pielāgotais tags | |
| move | pārvietot | also in a tree, a group or a status |
| copy | kopēt | |
| swap | apmainīt | |
| embedding (vector) | iegulums | |
| chunk | fragments | |
| vector | vektors | |
| embed / embedded (object in text) | iegult / iegults | |
| prompt | uzvedne | |
| model | modelis | |
| token | marķieris | any token; form token: formas marķieris; bearer token: nesēja marķieris |
| AI | MI | mākslīgais intelekts |
| deploy / deployment | izvietot / izvietošana | |
| release | laidiens | |
| enable / disable | iespējot / atspējot | |
| enabled / disabled | iespējots / atspējots | |
| active / inactive | aktīvs / neaktīvs | |
| error | kļūda | |
| warning | brīdinājums | |
| required | obligāts | |
| optional | neobligāts | |
| default | noklusējuma | "by default": pēc noklusējuma |
| priority | prioritāte | |
| sort | kārtot | |
| ascending / descending | augošā secībā / dilstošā secībā | abbreviated Asc. / Desc.: Aug. / Dilst. |
| visible / hidden | redzams / paslēpts | |
| hide / show / reveal, unhide | paslēpt / rādīt / parādīt | |
| expand / collapse | izvērst / sakļaut | |
| OK / Cancel | Labi / Atcelt | Microsoft uses "Labi" for OK |
| Yes / No | Jā / Nē | |
| Back / Next / Previous | Atpakaļ / Tālāk / Iepriekšējais | |
| Close | Aizvērt | |
| select | atlasīt | to choose one option: izvēlēties |
| Select all | Atlasīt visu | |
| Deselect all / Select none | Notīrīt atlasi | |
| browse | pārlūkot | |
| apply | lietot | Apply changes: Lietot izmaiņas |
| save / store | saglabāt | |
| discard | atmest | |
| edit | rediģēt | |
| remove | noņemt | take out of a list, group or place |
| delete | dzēst | destroy; deleted: izdzēsts |
| new | jauns | |
| create | izveidot | |
| add | pievienot | |
| refresh | atsvaidzināt | |
| update | atjaunināt | |
| reset | atiestatīt | |
| restore | atjaunot | |
| assign / assignment | piešķirt / piešķīrums | |
| approve / approval | apstiprināt / apstiprinājums | approved: apstiprināts; approver: apstiprinātājs |
| reject / deny | noraidīt / liegt | rejected: noraidīts |
| accept / accepted | pieņemt / pieņemts | |
| pending | gaida | "Gaida apstiprinājumu" |
| archive (stored place, menu item) | arhīvs | |
| archived (version, object, order) | arhivēts | |
| Archive / Unarchive (button) | Arhivēt / Izņemt no arhīva | |
| archive (file .tar/.zip/.phar) | arhīva fails | |
| statistics | statistika | |
| system information | sistēmas informācija | |
| database | datu bāze | |
| handler | apdarinātājs | |
| wizard | vednis | |
| backend | aizmugursistēma | |
| provider | nodrošinātājs | |
| endpoint | galapunkts | |
| method | metode | |
| listener | klausītājs | |
| controller | kontrolleris | |
| allowlist | atļauto saraksts | |
| read-only | tikai lasāms | |
| argument | arguments | |
| slot | slots | |
| collection | kolekcija / iesūtījums | kolekcija for a MongoDB collection, a layout block or a cache group; one information-collection submission: iesūtījums (Collection ID: Iesūtījuma ID) |
| facets | aspekti | search facets |
| elevate | paaugstināt | search elevation |
| filter | filtrs | verb: filtrēt |
| details | detalizēta informācija | |
| tick (checkbox) | atzīmēt | |
| container (content class, "Is container") | konteiners | |
| container (CSS) | konteiners | |
| locale | lokalizācija | the setting (Locale=...) |
| localization | lokalizēšana | the process |
| charset | rakstzīmju kopa | |
| license | licence | |
| privilege (database) / grant | privilēģija / piešķirt | |
| format | formāts | text formatting: formatēšana |
| linked | saistīts | |
| common | kopīgs | |
| flags (class features) | karodziņi | |
| teaser | anonss | |
| slug | URL nosaukums | the readable part of a URL |
| placeholder | vietturis | |
| preset | priekšiestatījums | |
| information collection / collector | informācijas vākšana / vācējs | |
| overview | pārskats | |
| summary | kopsavilkums | |
| digest | apkopojums | any digest; error digest: kļūdu apkopojums |
| tab | cilne | |
| rich text | formatēts teksts | |
| feed | plūsma | |
| session | sesija | |
| scope | tvērums | |
| thread | pavediens | also a process thread |
| chain | ķēde | |
| batch | pakete | |
| pool | kopa | |
| schema / registry / manifest | shēma / reģistrs / manifests | |
| stylesheet | stila lapa | |
| theme | motīvs | to keep it apart from design: dizains |
| icon | ikona | |
| port | ports | |
| profile | profils | |
| core (Solr) | kodols | |
| kernel | kodols | |
| lane (queue) | josla | |
| editor (person) | redaktors | |
| editor (software) | redaktors | template editor: veidņu redaktors |
| Editor (siteaccess type) | redaktora vietnes piekļuve | |
| page | lapa | |
| link | saite | |
| title | virsraksts | |
| name | nosaukums | a person's name: vārds |
| description | apraksts | |
| date / time | datums / laiks | |
| status | statuss | |
| type | tips | |
| group | grupa | |
| list | saraksts | |
| value / field | vērtība / lauks | |
| number / count | skaitlis / skaits | |
| width / height / size | platums / augstums / lielums | file size: faila lielums |
| server / client | serveris / klients | |
| request / response | pieprasījums / atbilde | |
| pattern | paraugs | regular expression: regulārā izteiksme |
| repository | repozitorijs | |
| maintainer | uzturētājs | |
| install / uninstall / installation | instalēt / atinstalēt / instalācija | |
| checksum | kontrolsumma | |
| quarantine | karantīna | |
| mapping | kartēšana | |
| resolve / resolver | atrisināt / atrisinātājs | path or name resolution: atrisināšana |
| checkpoint | kontrolpunkts | |
| child documents (Solr) | pakārtotie dokumenti | |
| script | skripts | |
| revoke | atsaukt | |
| label | etiķete | |
| plugin | spraudnis | |
| debug | atkļūdošana | |
| Confirmation questions ("Delete …?") | "Vai tiešām vēlaties dzēst …?" | "Cancel this …?": "Vai tiešām vēlaties atcelt …?" |

## 5. Decisions after the translation

These decisions take precedence over the glossary in section 4.

| English | Latvian | Distinction |
|---|---|---|
| hidden by (a) parent / by superior | paslēpts vecākvienuma dēļ / paslēpts augstāka līmeņa vienuma dēļ | not "paslēpts vecākvienumā" or "ar vecākvienumu" |
| grip (drag handle) | turis | gen. tura; not "turektis" |
| fetch function / fetch alias | ielādes funkcija / ielādes aizstājvārds | the template fetch(); not "iegūšanas …" |
| engine (database, template, server, chain) | dzinējs | not "dzinis"; search engine stays meklētājprogramma |
| setup wizard / setup (installation) | instalēšanas vednis / instalēšana | the admin tab Setup stays "Iestatīšana" |
| step (wizard, workflow event) | darbība | "Pirmā darbība", as in Microsoft wizards |
| step (process, chain, run, "step by step") | solis | pl. soļi; "%count. solis" |
| commit (Git) | iesūtījums | merge commit: sapludināšanas iesūtījums; verb: iesūtīt |
| commit (database or Solr transaction) | apstiprināšana (commit) | the code name commit() stays |
| storage (place: var/storage, package storage) | krātuve | storage key: krātuves atslēga; not "glabātuve" |
| storage (the act of storing) | glabāšana | storage engine: glabāšanas dzinējs |
| render / rendered / renderer | atveidot / atveidots / atveidotājs | not "renderēt" or "attēlot" |
| front page / start page | sākumlapa | the cover of a PDF export: titullapa |
| page layout (pagelayout.tpl, classic designs) | lapas izkārtojums | always with "lapas"; bare "izkārtojums" is Exponential Layouts |
| maintenance mode / maintenance page | apkopes režīms / apkopes lapa | not "uzturēšanas"; maintenance as ongoing upkeep stays uzturēšana |
| machine (server, host, configuration layer) | iekārta | "Iekārtas vērtības"; the user's own computer: dators |
| host / hostname / virtual host | resursdators / resursdatora nosaukums / virtuālais resursdators | hosting: mitināšana |
| shell / socket / proxy | čaula / ligzda / starpniekserveris | |
| header / footer (HTTP, mail) | galvene / kājene | |
| query / query cache | vaicājums / vaicājumu kešatmiņa | |
| SQL statement | priekšraksts | statement digest: priekšrakstu apkopojums |
| implements / extends | realizē / paplašina | not "īsteno" |
| report (generated: support, license, audit) | atskaite | "Licenču atskaite"; overview stays pārskats |
| report (by a person: bug, content, CSP) | ziņojums | verb: ziņot |
| review / staged | pārskatīt, pārskatīšana / sagatavots | staged changes: sagatavotās izmaiņas |
| finding | konstatējums | |
| issue (cron, system check) / issue (GitHub) | problēma / pieteikums | issue tracker: pieteikumu reģistrs |
| alert (audit, monitoring rule) | trauksme | "Audita trauksme"; warning stays brīdinājums |
| alert (e-mail category) | brīdinājumi | security and administration alert mail only |
| purge (cache, files, log, audit) | iztīrīt / iztīrīšana | |
| purge (object, order, trash) | neatgriezeniski dzēst / galīga dzēšana | |
| backup | dublējums | not "rezerves kopija" |
| orphan / orphaned files | bāreņi / bāreņfaili | |
| stale / dead entries | novecojis / mirušie ieraksti | |
| credentials | akreditācijas dati | |
| redact / mask | maskēt / maskēts | |
| hash / fingerprint | jaucējkods / nospiedums | not bare "hash" in running text; hash() in template code stays |
| gateway (payment, LLM) | vārteja | a gate (mail gate, write gate) is vārti |
| rerank / reranker | pārranžēt / pārranžētājs | |
| retention | glabāšana | zero retention: nulles glabāšana |
| autoload / autoloader | automātiskā ielāde / automātiskais ielādētājs | |
| capability | spēja | pl. spējas; not "iespēja" |
| asset | resurss | |
| node picker / finder | mezglu atlasītājs / satura meklētājs | |
| guide / manual | ceļvedis / rokasgrāmata | |
| sink / actor / severity (audit) | saņēmējs / izpildītājs / nopietnības līmenis | |
| consent / double opt-in | piekrišana / dubultā piekrišana | opt-out stays "opt-out", with "atteikšanās iespēja" at first mention |
| bounce / hard bounce | piegādes atteikums / neatgriezenisks piegādes atteikums | |
| environment: Development / Test / Staging / Production | Izstrāde / Testēšana / Pirmsražošana / Ražošana | the badges DEV, TEST, STAGE, PROD stay codes |
| rollback / symlink | atritināšana / simboliskā saite | |
| stub (OER import) | sagatave | |
| lesson / pupil / tutor / grade level | nodarbība / skolēns / tutors / skolas klases līmenis | never bare "klase" for a grade |
| hit / miss / hit rate | trāpījums / netrāpījums / trāpījumu īpatsvars | |
| probe (remote) | zonde | |

- **Remove and delete**: noņemt takes something out of a list, group or place; dzēst destroys it. Where a "remove"
  text describes destroying data ("are deleted from the package storage"), dzēst is right.
- **Two senses, two words**: collection, step, storage, report, alert, purge, front page, machine and commit each
  have two senses above. Decide by context, and never mix both words on one screen.
- **Technical words in running text** take the Latvian form ("iesūtījums", "jaucējkods", "atveidot"); code names in
  parentheses or backquotes (commit(), --engine, hash()) stay as they are.
