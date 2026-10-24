# Lower Sorbian (dsb-DE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Lower Sorbian (dolnoserbšćina) as written in Lower Lusatia (Brandenburg, Germany), following the current
orthography and the vocabulary of the Lower Sorbian dictionaries (dolnoserbski.de). No other locale falls back to
this catalogue; it is not Upper Sorbian, and Upper Sorbian (hsb) forms are wrong here (dsb "wužywaŕ", "pšosym",
"gronidło", "papjernik", not hsb "wužiwar", "prošu", "hesło", "kós").

The tone is neutral, concise and professional, as in the Lower Sorbian versions of Firefox, Thunderbird and
LibreOffice. The terms of those localizations are the reference for everything an office or browser user already
knows (save, cancel, settings, tab, bookmark, cache, password). Avoid German words where a Lower Sorbian one is
established, and avoid needless English; do not invent new words where an established international one exists
(atribut, modul, paket, wersija, filter).

## 2. Form of address

The user is addressed formally with **Wy** (2nd person plural), written with a capital letter in every form, as is
the Sorbian courtesy convention: Wy, Was, Wam, Wami, Waš, Waša, Wašo, Wašogo, Wašeje, Wašym, Waše. The verb ending
already marks the person, so the pronoun is usually left out ("Njamaśo pšawo ...", not "Wy njamaśo pšawo ...").

- **Buttons, menu items, links and action labels** use the infinitive (ending in -ś or -c), as in the Lower Sorbian
  Firefox and LibreOffice: "Składowaś", "Wulašowaś", "Wótpóraś", "Wobźěłaś", "Pśidaś", "Pśetergnuś", "Napóraś nowy",
  "Změny nałožyś", "K wózjawjenju pósłaś", "Wšykno wubraś", "Nacerjenje składowaś", "Nacerjenje zachyśiś",
  "Papjernik wuprozniś". The object comes first where Lower Sorbian puts it there ("Pufrowak wuprozniś").
- **Titles, column headings and tabs** are nouns: "Struktura wopśimjeśa", "Wužywaŕske konta",
  "Typ wugótowanja", "Nastajenja powěźeńkow", "Głowna rěc", "Stawy objektow".
- **Requests** use "pšosym" with the plural imperative: "Pšosym wubjeŕśo městno.", "Pšosym zapódajśo płaśiwu e-mailowu
  adresu."
- **Instructions in hints and messages** use the plural imperative: "Pśeglědajśo zwisk a wopytajśo hyšći raz.",
  "Klikniśo na tłocašk „Pśetergnuś“ a wopytajśo jano te městna wótpóraś, kótarež smějośo wótpóraś."
- **Questions and confirmations**: "Cośo ... napšawdu ...?": "Cośo toś tu klasu napšawdu wótpóraś?",
  "Cośo toś ten blok napšawdu wulašowaś?"
- **Error messages** state the fact, with Wy where the user is meant: "Njamaśo pšawo klase wótpóraś.",
  "Njamóžośo slědnu klasu wótpóraś, kótaraž wopśimujo wužywaŕske konta.", "Dataja njejo se namakała."
- **Statements about the user**: "Nastajenja historije wersijow móžośo w dataji content.ini změniś ...",
  "Nowe gronidło za Wašo konto nastajiś."
- **Tooltips that describe a click** use "Klikniśo ...": "Klikniśo na symbol, aby se kontekstowy meni pokazał."
- **Status messages** are impersonal (reflexive or passive participle): "Toś ten objekt jo se wulašował ...", "Změny su
  se wuspěšnje składowali.", "Žeden běg nadawkow cron njejo se namakał."
- Not allowed: the familiar form (ty, tebje, twój, coš, móžoš, singular imperative "Składuj", "Wubjeŕ"); "wy",
  "waš" in lower case where the user is addressed; the imperative on buttons ("Składujśo"); the infinitive as an
  instruction inside a sentence ("Toś to pólo wužywaś za ..." is wrong; write "Toś to pólo słužy za ...").

## 3. Script and typography

- Latin script with the full Lower Sorbian alphabet: a b c č ć d e ě f g h ch i j k ł l m n ń o ó p r ŕ s š ś t u w
  y z ž ź. Use the precomposed characters (ŕ U+0155, ś U+015B, ź U+017A, ć U+0107, ń U+0144, ě U+011B, ł U+0142),
  never a letter plus a combining accent, and never the Upper Sorbian ř, dź, wo- spellings.
- **Capitals**: sentence case; only the first word of a button, title or heading, proper names and the courtesy
  pronoun Wy/Waš are capitalized: "Struktura wopśimjeśa", "Medijowa biblioteka". No English title case and no
  German noun capitals.
- **Quotation marks** in running text: „…“ (U+201E, U+201C), as in German and Upper Sorbian: „%name“. Inside HTML
  attributes and JavaScript the straight quotes of the source stay.
- **No space** before ":", ";", "?" and "!". Dashes: the en dash with spaces – for a break in a sentence.
- **Compounds with a code or abbreviation** take a hyphen and the Sorbian word declines: "URL-alias",
  "XML-tag", "INI-dataja", "PDF-dokument", "e-mailowa adresa".
- **Plural forms**: Lower Sorbian has four number forms: 1 (singular: jaden objekt), 2 (dual: dwa objekta),
  3–4 (plural: tśi objekty) and 5+ (genitive plural: pěś objektow). The source only has "item(s)". Never write
  "objekt(y)" or "objekt(a)". Use a label form in which the count stands alone: "Objekty: %count",
  "Źěłowe procese: %count", "Wubrane: %count."
- **Placeholders and case**: a placeholder is never declined and nothing is glued to it (`%namea`, `%name-a` is not
  Sorbian practice either). Put a noun in front that carries the case, so that the placeholder stays in the
  nominative: "Klasu <%class_name> wobźěłaś", "Njesmějośo objekt %id w rěcy %language wobźěłaś". Or use a label:
  "Klase: %count".
- **Numbers**: digits 0-9; decimal comma ("1,5 MB"), a space or a dot as thousands separator as in German
  conventions; dates as "23.10.2026", time as "14:05". Time units after a number: "min", "h", "s", "d"; "pśed %n min", "pśed %n d". A unit after a placeholder is
  never declined.
- **Exponential** and other product names are not declined; where grammar needs a case, use a frame word:
  "system Exponential", "w systemje Exponential".

## 4. Glossary

Binding for the kernel and every extension. Verbs are given in the infinitive (the button form).

| English | Lower Sorbian | Note |
|---|---|---|
| content | wopśimjeśe | gen. wopśimjeśa |
| content object / object | objekt wopśimjeśa / objekt | |
| item (list entry) | zapisk | pl. zapiski; dead entries: mjertwe zapiski |
| node | suk | main node: głowny suk |
| location / main location | městno / głowne městno | the placement of a node |
| content class / class | klasa wopśimjeśa / klasa | a PHP class: PHP-klasa |
| class group | kupka klasow | |
| attribute | atribut | |
| datatype | datowy typ | |
| version | wersija | version history: historija wersijow |
| draft | nacerjenje | Store draft: Nacerjenje składowaś; Discard draft: Nacerjenje zachyśiś |
| publish / published / publishing | wózjawiś / wózjawjony / wózjawjenje | Send for publishing: K wózjawjenju pósłaś |
| translation | pśełožk | pl. pśełožki |
| language / main language | rěc / głowna rěc | interface language: rěc pówjercha |
| content language | rěc wopśimjeśa | |
| fallback (language) / fallback (anything else) | rezerwna rěc / rezerwne rěšenje | fallback chain: rěd rezerwnych rěcow; fallback template: rezerwna pśedłoga |
| siteaccess | pśistup k sedłu | pl. pśistupy k sedłu |
| site / website | sedło / websedło | |
| design (Exponential design) | design | |
| structure | struktura | |
| template | pśedłoga | template editor: editor pśedłogow |
| override / overridden | pśepisanje / pśepisany | override template: pśepisujuca pśedłoga; a setting that replaces another |
| alias / URL alias | alias / URL-alias | |
| cache | pufrowak | Clear cache: Pufrowak wuprozniś |
| workflow | źěłowy wótběg | pl. źěłowe wótběgi |
| workflow event / event | tšojenje źěłowego wótběga / tšojenje | a workflow event is a step (kšac) of the workflow |
| trigger | wuwołak | |
| role | rola | |
| policy | zasada | pl. zasady; a permission rule inside a role |
| permission | pšawo | pl. pšawa; file permissions (chmod): pšawa datajow; never licenca |
| access / access denied | pśistup / pśistup zakazany | |
| limitation (policy) | wobgranicowanje | |
| user | wužywaŕ | pl. wužywarje |
| user group | wužywaŕska kupka | |
| user account / user accounts | wužywaŕske konto / wužywaŕske konta | |
| anonymous user | anonymny wužywaŕ | |
| administrator / admin interface | administrator / administraciski pówjerch | |
| user interface | wužywaŕski pówjerch | |
| log in / log out | pśizjawiś se / wótzjawiś se | |
| login (field) / username | pśizjawjeńske mě / wužywaŕske mě | |
| password | gronidło | |
| e-mail | e-mail | e-mail address: e-mailowa adresa |
| section | wótrězk | always the Exponential section; a section of an INI file is blok, a part of a page or document is źěl |
| object state / state | staw objekta / staw | state group: kupka stawow; the health of a system is also staw |
| status | status | the status of an order or a job |
| relation | relacija | |
| related objects / reverse related | zwězane objekty / objekty, kótarež na toś ten objekt pokazuju | |
| trash | papjernik | Empty trash: Papjernik wuprozniś |
| bookmark / bookmarks | cytańske znamje / cytańske znamjenja | |
| sub-items / children | pódrědowane zapiski | |
| parent | nadrědowany zapisk | parent node: nadrědowany suk |
| subtree | pódbom | |
| owner / creator / modifier | wobsejźaŕ / awtor / slědny wobźěłaŕ | |
| created / modified | napórany / změnjony | column headings: Napórane / Změnjone |
| layout (Exponential Layouts) | wugótowanje | layout type: typ wugótowanja; the name Exponential Layouts stays |
| layout (directory or release structure) | struktura | |
| block / zone | blok / cona | |
| setting / settings | nastajenje / nastajenja | configuration values |
| Setup (admin tab) | Zarědowanje | |
| configuration / configure | konfiguracija / konfigurěrowaś | |
| Content structure | Struktura wopśimjeśa | |
| Media library | Medijowa biblioteka | |
| Dashboard | Pśeglědowa tafla | |
| overview / summary | pśeglěd / zjeśe | |
| extension | rozšyrjenje | |
| package | paket | |
| module | modul | |
| view (noun, module view) | naglěd | Module view: naglěd modula |
| view (button) | pokazaś | |
| function | funkcija | |
| search (verb / noun) | pytaś / pytanje | search engine: pytańska mašina |
| search query, search phrase / search terms | pytański wuraz / pytańske słowa | pl. pytańske wuraze |
| search index / reindex | pytański indeks / znowego indeksěrowaś | index (verb): indeksěrowaś |
| index (database) | indeks | index file: indeksowa dataja |
| query (database, Solr) | wótpšašowanje | query cache: pufrowak wótpšašowanjow; query (verb): wótpšašowaś |
| cronjob / cronjob part | nadawk cron / źěl nadawkow cron | pl. nadawki cron / źěle nadawkow cron |
| schedule | casowy plan | |
| job / task | nadawk | job queue: cakański rěd nadawkow |
| queue | cakański rěd | |
| worker | źěłowy proces | |
| lane (queue) | linija | |
| log / log file | protokol / protokolowa dataja | never the verb "log in" |
| audit / audit trail | audit / auditowy slěd | |
| notification | powěźeńka | |
| collaboration | zgromadne źěło | |
| preview | pśeglěd | overview is also pśeglěd, the context decides; where a heading could be read both ways, add the object ("Pśeglěd wopśimjeśa") |
| ID / remote ID / identifier | ID / zdalony ID / identifikator | |
| file | dataja | |
| folder / directory | zarědnik | |
| image / media | wobraz / medije | |
| thumbnail | miniatura | |
| upload / download | nagraś / ześěgnuś | |
| export / import | eksportěrowaś / importěrowaś | |
| shop | wobchod | |
| Store (shop tab) | Wobchod | every other "Store" is the verb składowaś |
| basket | nakupowański kórbik | |
| order (shop) | skazanka | order status: status skazanki |
| order (sorting, sequence) | pórěd | sort order: sortěrowański pórěd; in order: pó pórěźe; "in order to" is aby |
| customer / product | kupowaŕ / produkt | |
| checkout | kasa | |
| VAT / VAT type | dank z pśidateje gódnoty / typ danka | short: dank |
| discount / discount rule | rabat / pšawidło rabata | |
| rule | pšawidło | |
| price / currency | płaśizna / wałuta | |
| payment gateway | płaśeńska słužba | |
| wish list | lisćina žycenjow | |
| newsletter | powěsćowy list | |
| subscriber / subscription / subscribe | abonent / abonement / abonowaś | |
| tag (keyword, eztags) / keyword | wobznamjenje / klucowe słowo | keyword (eztags) only; the words of a search are pytańske słowa |
| tag (HTML/XML) / custom tag | tag / swójski tag | XML-tag |
| move / copy / swap | pśesunuś / kopěrowaś / wuměniś | |
| embedding (vector) / chunk / vector | wektorowa reprezentacija / źěl teksta / wektor | |
| embed / embedded (object in text) | zasajźiś / zasajźony | the `<embed>` tag stays a tag name |
| prompt / model / token | zapódaśe / model / token | |
| AI | KI (kuntna inteligenca) | |
| deploy / deployment / release | nasajźiś / nasajźenje / wudaśe | |
| enable / disable | zmóžniś / znjemóžniś | enabled / disabled: zmóžnjony / znjemóžnjony |
| switch on / off; on / off (menu) | zašaltowaś / wótšaltowaś; zap. / wup. | |
| active / inactive | aktiwny / njeaktiwny | |
| warning / error / notice | warnowanje / zmólka / pokazka | |
| required / optional | trěbny / opcionalny | |
| default / priority | standardny / priorita | |
| sort / ascending / descending | sortěrowaś / stupajucy / padajucy | abbreviated: stup. / pad. |
| hide / show, reveal | schowaś / pokazaś | hidden / visible: schowany / widobny |
| expand / collapse | rozfałdowaś / zafałdowaś | |
| OK / Cancel | W pórěźe / Pśetergnuś | |
| Yes / No | Jo / Ně | |
| Back / Next / Previous / Close | Slědk / Dalej / Pjerwjejšny / Zacyniś | |
| select all / deselect all / select none | Wšykno wubraś / Wuběrk wótpóraś / Nic wubraś | |
| select / browse | wubraś / pśepytaś | |
| apply | nałožyś | Apply changes: Změny nałožyś |
| save / store | składowaś | |
| discard | zachyśiś | |
| edit | wobźěłaś | |
| remove | wótpóraś | take out of a list, group or place |
| delete | wulašowaś | destroy; a cache is emptied, not deleted: Pufrowak naglědow wuprozniś |
| new / create / add | nowy / napóraś / pśidaś | |
| refresh / update | aktualizěrowaś | reload a page: znowego zacytaś |
| reset | slědk stajiś | |
| run (verb / noun) | wuwjasć / běg | |
| dry run / self-test | testowy běg / samotest | |
| restore | wótnowiś | |
| assign / assignment | pśirědowaś / pśirědowanje | |
| approve / approval / approved / approver | pśizwóliś / pśizwólenje / pśizwólony / wósoba, kótaraž pśizwólijo | confirm (an action, an e-mail): wobkšuśiś, never pśizwóliś |
| deny, reject / rejected | wótpokazaś / wótpokazany | |
| accept / accepted | akceptěrowaś / akceptěrowany | |
| pending | cakajucy | pending an action: caka na ... |
| archive (place, menu item) | archiw | |
| archived (version, object, order) | archiwěrowany | |
| Archive / Unarchive (button) | Archiwěrowaś / Z archiwa wześ | |
| archive (file .tar/.zip/.phar/.ezpkg) | archiwna dataja | |
| statistics / system information | statistika / systemowe informacije | |
| database | datowa banka | |
| path / way | sćažka / droga | a file or URL path is sćažka; a way of doing something or a code path is droga (not puś) |
| handler / wizard | wobźěłowak / asistent | setup wizard (installer): instalaciski asistent, not "zarědowański asistent" (Setup is the admin tab Zarědowanje); installer: instalaciski program |
| backend / provider / endpoint | backend / póbitowaŕ / kóńcny dypk | payment provider: płaśeńska słužba; `%provider` stays a placeholder |
| method / listener / controller | metoda / pśisłuchaŕ / kontroler | |
| allowlist / read-only / argument | lisćina dowólonych / jano za cytanje / argument | |
| slot / collection / facets / elevate | městno / zběrka / fasety / wuzwignuś | |
| filter | filter | pl. filtry; verb: filtrowaś |
| tick (checkbox) | markěrowaś | checkbox: kontrolny kašćik; radio buttons: opciske tłocaški |
| container (content class, "Is container") | kontejner | |
| container (Bootstrap/CSS) | kontejner | |
| locale / localization | regionalne nastajenje / lokalizacija | |
| wildcard (URL; *, ?) | zastupne znamuško | |
| redirect / forward (URL) | dalej pósrědniś / dalejpósrědnjenje | |
| charset | kodowanje znamuškow | |
| license | licenca | |
| privilege / grant (database) | pšawo / pšawo pśipóznaś | |
| format / formatting | format / formatěrowanje | file or data format, or text formatting |
| linked / common | zwězany / zgromadny | a link itself is wótkaz |
| flags (class flags) / flag (command-line switch) | markěrowanja / šaltowak | language flags (images): chórgojcki |
| teaser | krotki tekst | |
| slug | URL-mě | |
| placeholder (token replaced by a value: %name, {1}, {v}) | zastupne městno | only a wildcard character (*, ?) is zastupne znamuško |
| placeholder (hint text of an input field) | zastupny tekst | only the HTML `placeholder` attribute |
| placeholder (dummy value in a configuration) | zastupna gódnota | |
| preset | pśednastajenje | |
| information collection / collector / collection (one submission) | zběranje informacijow / zběrak informacijow / zběrka | pl. zběrki; a MongoDB or Solr collection is also zběrka |
| tab | rejtarik | the keyboard key stays Tab |
| rich text | formatěrowany tekst | |
| feed / feed reader / enclosure | kanal / cytak kanalow / pśiłoga | |
| session / timed-out session | pósejźenje / pśepadnjone pósejźenje | |
| scope | wobceŕk | |
| thread / chain | nitka / rjeśaz | a conversation itself is rozmołwa |
| batch | kopica | |
| schema / registry / manifest | šema / register / manifest | |
| stylesheet / theme / icon | stilowa pśedłoga / drastwa / symbol | |
| port / digest / profile | port / zjeśe / profil | |
| core (Solr) | Solr-jědro | |
| kernel | jědro | |
| plugin | tykac | |
| script | skript | |
| editor (person) / editor (software) | redaktor / editor | |
| Editor (siteaccess type) | redakciski pśistup | |
| page / pages | bok / boki | |
| link | wótkaz | |
| button | tłocašk | |
| title / name / description | titel / mě / wopisanje | gen. of mě: mjenja |
| date / time | datum / cas | |
| type / group / list | typ / kupka / lisćina | |
| value / field | gódnota / pólo | |
| number / count | licba | |
| width / height / size | šyrokosć / wusokosć / wjelikosć | |
| server / client | serwer / klient | |
| request / response | napšašowanje / wótegrono | what a browser or client sends; a database or search query is wótpšašowanje |
| details | drobnosći | personal details: daty (Customer details: Daty kupowarja) |
| debug | pytanje zmólkow | debug output: diagnostiske informacije |
| rotation / rotate (log files) | rotacija / rotěrowaś | |
| pattern | wuzor | |
| repository / maintainer | repozitorium / hladaŕ | |
| install / uninstall / installation | instalěrowaś / wótinstalěrowaś / instalacija | |
| checksum / quarantine | kontrolna suma / karantena | |
| pool (of processes) / mapping | pul / pśirěźenje | |
| resolver / resolve | rozpušćak / rozpušćiś | resolvable: rozpušćujobny |
| checkpoint | kontrolny dypk | |
| child documents (Solr) | pódrědowane dokumenty | |
| revoke | wótwołaś | key, token, session, access |
| label | pópisanje | of a field, button or block |
| confirmation questions ("Delete ...?") | Cośo ... napšawdu wulašowaś? | always a full question with Wy |

## 5. Decisions after the translation

These terms were settled after the catalogue had been translated. They complement the glossary in section 4.

| English | Lower Sorbian | Note |
|---|---|---|
| expire / expired / expiry | pśepadnuś / pśepadnjony / pśepadnjenje | not "wótběžaś"; Wótběg means a process |
| step (Step 1/3) | kšac | not "krok" |
| poll / vote | anketa / wótgłosowaś | not "napšašowanje" |
| check (noun / verb) | kontrola / pśeglědaś | system check: kontrola systema; drift check: kontrola wótchylenja |
| validation / validation error | pśeglědowanje / zmólka pśeglědowanja | not "validěrowaś" |
| drop-down menu | wuběrański meni | |
| clipboard | mjazywótkład | |
| drag / drag handle (grip) | śěgnuś / pśimadło | not "pśimk", "pśimjeńko" |
| arrow keys | šypkowej tasće (dual) / šypkowe tasty | |
| row / column (table) | smužka / słup | a line of text is also smužka |
| string | rjeśazk znamuškow | a translatable text ("Strings" in the translation tools) is tekst |
| cache hit / miss / hit rate | trjefjenje / njetrjefjenje / kwota trjefjenjow | not "trjefaŕ" |
| issue / issue tracker / report | problem / slědowak problemow / rozpšawa | report an error: zmólku k wěsći daś |
| tool / toolbar | rěd / symbolowa lajsta | not "lejstwa"; "rěd" without an adjective is always a tool (queue: cakański rěd, sequence: pórěd) |
| shell / command / command line | konzola / pśikaz / pśikazowa smužka | `shell` stays only in code and command names |
| dead (entries, jobs, reference) / stale | mjertwy / zestarjony | not the Upper Sorbian "mortwy", not "mrětwy" |
| orphan(ed) | wósyrośony | |
| layer | warstwa | not "woršta" |
| front page, title page / start page, landing page | titelny bok / startowy bok | not "titulny" |
| maintenance mode / maintenance page | wótwardowański modus / bok wótwardowanja | |
| environment (dev/stage/prod) / environment variable | wobswět / wariabla wokoliny | Development / Staging / Production: Wuwiśe / Pśedprodukcija / Produkcija |
| generate / regenerate | generěrowaś / znowego generěrowaś | files, caches, exports; "names are generated" may stay twóriś |
| render / rendered / renderer | wuźěłaś / wuźěłany / renderowak | not "pśedstajiś" |
| interface (PHP) | interfejs | the user interface is pówjerch |
| upgrade | aktualizacija / aktualizěrowaś | |
| engine / driver | mašina / gónjak | not "wóźeński program" |
| memory (RAM) / disk | składowak / disk | not "skład" (a storage place), not "plata" |
| file extension | kóńcowka dataje | also a link suffix: kóńcowka; not "sufiks" |
| conversation | rozmołwa | not "rozgrono" |
| route / routing / HTTP header | ruta / rutowanje / HTTP-głowa | |
| operator (template) | operator | "wobsłužowaŕ" only for a person who operates a system |
| available | k dispoziciji | |
