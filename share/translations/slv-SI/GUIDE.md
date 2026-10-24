# Slovenian (slv-SI): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Slovenian (knjižna slovenščina) as used in Slovenia, following Slovenski pravopis. No other locale falls
back to this catalogue. The tone is neutral, concise and professional, as in the Slovenian versions of common
operating systems and office software; the terminology follows the established Microsoft Slovenian terminology
wherever it has a term. Avoid colloquial forms, Serbo-Croatian loans (e.g. "zbrisati", "fajl", "brisanje" for
"remove" in a list) and needless English words. Use the dual (dvojina) where the grammar needs it; never replace it
with the plural.

## 2. Form of address

- **Buttons, menu items, links and action labels** use the imperative singular, as in Microsoft software:
  "Shrani", "Izbriši", "Prekliči", "Uredi", "Dodaj", "Odstrani", "Ustvari novo", "Uporabi spremembe",
  "Pošlji v objavo", "Izberi vse", "Shrani osnutek", "Zavrzi osnutek", "Izprazni koš". Not the plural imperative
  ("Shranite") and not the infinitive ("Shraniti") on a button.
- **Titles, column headings and tabs** are nouns: "Struktura vsebine", "Uporabniški računi", "Vrsta postavitve",
  "Nastavitve obvestil", "Stanja objektov", "Glavni jezik".
- **Sentences, hints, questions and error messages** address the user with the formal **vi**, written in lower
  case (vi, vas, vam, vaš, vaša, vaše), and the plural imperative for instructions:
  - Question: "Ali res želite odstraniti ta razred?" / "Ali res želite odstraniti jezik?"
  - Request: "Izberite lokacijo." / "Vnesite veljaven e-poštni naslov."
  - Statement: "Nimate dovoljenja za odstranjevanje razredov." / "Zadnjega razreda, ki vsebuje uporabniške račune,
    ne morete odstraniti."
  - Instruction: "Kliknite gumb „Prekliči“ in poskusite odstraniti samo lokacije, ki jih smete odstraniti."
  - Hint: "Nastavitve zgodovine različic lahko spremenite v datoteki content.ini, ..."
  - Error: "Osnutka ni bilo mogoče shraniti." / "Zahteva ni dosegla strežnika. Preverite povezavo in poskusite
    znova."
- **Tooltips that describe a click** use "Kliknite ..., da ...": "Kliknite ikono, da prikažete kontekstni meni."
- **Status messages** are passive or impersonal: "Objekt je premaknjen.", "Spremembe so bile uspešno shranjene.",
  "Ni bilo najdenega nobenega zagona opravil cron", "Indeksiranje je končano."
- **Not allowed**: the familiar form in sentences (ti, tvoj, želiš, lahko izbereš, izberi as an instruction inside a
  sentence); "Vi/Vas/Vaš" with a capital letter (that is the letter-writing style, not the interface style); the
  plural imperative or the infinitive on buttons; "se" constructions that hide who must act where the user must act
  ("Izbere se lokacija" is wrong; write "Izberite lokacijo").

## 3. Script and typography

- **Script**: Latin with the Slovenian letters č š ž (Č Š Ž). ć and đ appear only in proper names. Never replace
  č/š/ž with c/s/z.
- **Capitalization**: sentence case for everything, including titles, tabs and buttons: "Struktura vsebine",
  "Knjižnica medijev", "Počisti predpomnilnik". Names of languages, days and months are lower case
  ("slovenščina", "ponedeljek", "januar").
- **Quotation marks** in running text: „…“ (U+201E, U+201C), nested ‚…‘. The guillemets »…« are also correct
  Slovenian, but this catalogue uses „…“ only. Inside HTML attributes and JavaScript the straight quotes of the
  source stay.
- **Spacing**: no space before ":", ";", "?", "!"; a space between a number and its unit ("5 MB", "10 %"); the
  ellipsis follows the source ("..." or "…").
- **Plural and number forms**: Slovenian has four forms (1 / 2 / 3–4 / 5+, decided by the last two digits: 101
  like 1, 102 like 2, 103–104 like 3–4), the source only "item(s)". Never write "element(i)" or "objekt(ov)".
  Use a label form with the placeholder after a colon: "Objekti: %count", "Delovni procesi: %count",
  "Odstranjeni osnutki: %count.", "Zadetki: %count".
- **Placeholders and case**: a placeholder is never declined and nothing is glued to it ("%namea" would be a
  different placeholder). Put a noun in front that carries the case, and let the placeholder stand in apposition:
  "Urejanje razreda <%class_name>", "Odstranjevanje objekta %name", "Ponudnik %provider je zaseden ...",
  "Uporabnik %user je spremenil nastavitev %setting ...", or the label form "Modul: %module". Slovenian does not
  attach case endings to codes with a hyphen; never write "%name-a".
- **Numbers**: digits 0–9; written numbers come from the locale (decimal comma, thousands separator dot or
  non-breaking space). Dates in running text: "23. 10. 2026" (spaces after the dots). Time units after a number:
  "min", "h", "s"; "pred %n min", "pred %n h".
- **Abbreviations**: "npr.", "itd.", "oz.", "št." with a full stop; "ID", "URL", "DDV" without.
- **Hyphenated compounds with codes**: "e-pošta", "e-poštni naslov", "XML-značka" is avoided; write
  "značka XML", "datoteka INI", "naslov URL" (code after the noun).

## 4. Glossary

| English | Slovenian | Note |
|---|---|---|
| content | vsebina | |
| content object | vsebinski objekt | |
| object | objekt | |
| item (list entry, general) | element | items: elementi |
| node | vozlišče | main node: glavno vozlišče |
| location / main location | lokacija / glavna lokacija | the placement of a node in the tree |
| content class / class | razred vsebine / razred | a PHP class: razred PHP |
| class group | skupina razredov | |
| attribute | atribut | |
| datatype | podatkovni tip | |
| version | različica | version history: zgodovina različic |
| draft | osnutek | Store draft: Shrani osnutek; Discard draft: Zavrzi osnutek |
| publish | objavi | verb; publishing: objavljanje |
| published | objavljeno | |
| Send for publishing | Pošlji v objavo | |
| translation | prevod | |
| language | jezik | |
| main language | glavni jezik | |
| content language | jezik vsebine | interface language: jezik vmesnika |
| siteaccess | dostop do spletnega mesta | pl. dostopi do spletnega mesta |
| site / website | spletno mesto | |
| design (eZ design) | videz | design base: osnovni videz |
| structure | struktura | |
| Content structure | Struktura vsebine | |
| Media library | Knjižnica medijev | |
| Dashboard | Nadzorna plošča | |
| template | predloga | template editor: urejevalnik predlog |
| override | preglasitev | override template: preglasitvena predloga; overridden: preglašeno |
| fallback (language) | nadomestni jezik | fallback chain: veriga nadomestnih jezikov; fallback value: nadomestna vrednost |
| alias / URL alias | vzdevek / vzdevek URL | |
| wildcard (URL) | nadomestni znak | URL wildcard: nadomestni znak URL; wildcard alias: vzdevek z nadomestnimi znaki |
| redirect / forward (URL) | preusmeritev / posreduj | redirecting URL: preusmeritveni URL; URL forwarding: posredovanje URL |
| cache | predpomnilnik | Clear cache: Počisti predpomnilnik |
| workflow | potek dela | pl. poteki dela |
| workflow event | dogodek poteka dela | |
| trigger | sprožilec | |
| event | dogodek | |
| role | vloga | |
| policy | pravilnik | pl. pravilniki |
| limitation (policy) | omejitev | |
| permission | dovoljenje | |
| access / access denied | dostop / dostop zavrnjen | |
| user | uporabnik | |
| user group | skupina uporabnikov | |
| user account | uporabniški račun | |
| anonymous user | anonimni uporabnik | |
| administrator | skrbnik | |
| admin interface | skrbniški vmesnik | |
| user interface | uporabniški vmesnik | |
| log in / log out | prijavi se / odjavi se | nouns: prijava / odjava |
| login (field) / username | uporabniško ime | |
| password | geslo | |
| e-mail | e-pošta | e-mail address: e-poštni naslov |
| section | sekcija | always the Exponential section of an object; a section of an INI file: razdelek |
| object state / state | stanje objekta / stanje | state group: skupina stanj |
| relation | povezava | the datatype category "relation": povezava z objektom |
| related objects | povezani objekti | reverse related: objekti, ki se sklicujejo na ta objekt |
| trash | koš | Empty trash: Izprazni koš |
| bookmark | zaznamek | pl. zaznamki |
| sub-items / children | podrejeni elementi | |
| parent | nadrejeni element | parent node: nadrejeno vozlišče |
| subtree | poddrevo | |
| owner | lastnik | |
| creator | avtor | |
| modifier | avtor spremembe | |
| created / modified | ustvarjeno / spremenjeno | column headings |
| layout (Exponential Layouts) | postavitev | layout type: vrsta postavitve; the name Exponential Layouts stays |
| layout (directory / release structure) | struktura | e.g. struktura imenikov |
| block / zone | blok / območje | |
| setting / settings | nastavitev / nastavitve | |
| Setup (admin tab) | Nastavitve | |
| configuration / configure | konfiguracija / konfiguriraj | |
| extension | razširitev | |
| package | paket | |
| module | modul | |
| view (noun, module view) | pogled | module view: pogled modula |
| View (button) | Prikaži | |
| function (module function) | funkcija | |
| search | iskanje | button: Išči; search engine: iskalnik; any other engine: pogon (template engine: pogon predlog) |
| search index | iskalni indeks | |
| reindex | vnovično indeksiranje | verb: znova indeksiraj |
| index (verb) | indeksiraj | |
| index (database) | indeks | |
| index file | indeksna datoteka | |
| cronjob | opravilo cron | pl. opravila cron |
| schedule | urnik | scheduled: načrtovano; scheduler: razporejevalnik |
| job | opravilo | |
| queue | čakalna vrsta | job queue: čakalna vrsta opravil |
| lane (queue) | pas | |
| worker | delovni proces | |
| task | naloga | |
| log / log file | dnevnik / dnevniška datoteka | never the verb "log in" |
| rotation / rotate (log) | izmenjava / izmenjaj | rotated log: izmenjani dnevnik |
| audit / audit trail | revizija / revizijska sled | |
| notification | obvestilo | |
| collaboration | sodelovanje | |
| preview | predogled | |
| identifier | identifikator | |
| ID / remote ID | ID / oddaljeni ID | |
| file | datoteka | |
| folder / directory | mapa / imenik | folder (content class): mapa; directory on disk: imenik |
| image | slika | |
| media | mediji | |
| thumbnail | sličica | |
| upload / download | naloži / prenesi | |
| export / import | izvozi / uvozi | nouns: izvoz / uvoz |
| shop | trgovina | |
| Store (shop tab) | Trgovina | every other "Store" is the verb Shrani |
| basket | košarica | |
| order (shop) | naročilo | order status: stanje naročila |
| order (sorting) | vrstni red | sort order: vrstni red razvrščanja |
| customer | stranka | |
| product | izdelek | |
| checkout (shop) / checkout (git) | zaključek nakupa / delovna kopija Git | |
| VAT / VAT type | DDV / vrsta DDV | |
| discount / discount rule | popust / pravilo za popust | |
| rule | pravilo | |
| price / currency | cena / valuta | |
| wish list | seznam želja | |
| payment gateway / gateway | plačilni prehod / prehod | |
| newsletter | glasilo | |
| subscriber / subscription | naročnik / naročnina | subscribe: naroči se; unsubscribe: odjavi naročnino |
| tag (keyword, eztags) | oznaka | pl. oznake |
| keyword | ključna beseda | |
| tag (HTML/XML) | značka | custom tag: značka po meri |
| move | premakni | |
| copy | kopiraj | |
| swap | zamenjaj | |
| embed / embedded (object in text) | vdelaj / vdelano | embedded object: vdelani objekt; embed (noun): vdelava |
| embedding (vector) | vektorska predstavitev | |
| chunk / vector | odsek / vektor | |
| prompt | poziv | |
| model / token | model / žeton | |
| AI | umetna inteligenca (UI) | in labels often just "UI" |
| deploy / deployment / release / rollback | uvedi / uvedba / izdaja / povrnitev | symlink: simbolna povezava |
| enable / disable | omogoči / onemogoči | enabled / disabled: omogočeno / onemogočeno; "switch / turn on / off": vklopi / izklopi – keep the verb of the source |
| active / inactive | aktivno / neaktivno | |
| warning / error / notice | opozorilo / napaka / obvestilo | |
| required / optional | obvezno / izbirno | |
| default | privzeto | default value: privzeta vrednost |
| priority | prioriteta | |
| sort / ascending / descending | razvrsti / naraščajoče / padajoče | abbreviated "Asc." / "Desc.": Nar. / Pad. |
| visible / hidden | vidno / skrito | |
| hide / reveal / show | skrij / razkrij / prikaži | |
| expand / collapse | razširi / strni | |
| OK / Cancel | V redu / Prekliči | |
| Yes / No | Da / Ne | |
| Back / Next / Previous / Close | Nazaj / Naprej / Prejšnje / Zapri | previous item: prejšnji element |
| select | izberi | |
| select all / deselect all | Izberi vse / Počisti izbor | Select none: Ne izberi ničesar |
| browse | prebrskaj | |
| apply | uporabi | Apply changes: Uporabi spremembe |
| save / store | shrani | |
| discard | zavrzi | |
| edit | uredi | |
| remove | odstrani | take out of a list, group or place |
| delete | izbriši | destroy |
| new / create / add | novo / ustvari / dodaj | Create new: Ustvari novo |
| refresh / update / reset | osveži / posodobi / ponastavi | |
| run (verb) / run (noun) | zaženi / zagon | cron run: zagon cron |
| dry run | preizkusni zagon | |
| self-test | samopreizkus | |
| restore | obnovi | |
| assign / assignment | dodeli / dodelitev | |
| approve / approval / approved / approver | odobri / odobritev / odobreno / odobritelj | waiting for approval: čaka na odobritev |
| deny / reject / rejected | zavrni / zavrni / zavrnjeno | |
| accept / accepted | sprejmi / sprejeto | |
| pending | v čakanju | pending list: seznam v čakanju or seznam čakajočih |
| archive (archived version, object, order) | arhivirano | |
| Archive / Unarchive (button) | Arhiviraj / Odstrani iz arhiva | |
| archive (place, menu item, log archive) | arhiv | |
| archive (file .tar/.zip/.phar/.ezpkg) | arhivska datoteka | |
| statistics | statistika | |
| system information | sistemske informacije | |
| database | zbirka podatkov | |
| path | pot | |
| handler | rutina za obravnavo | |
| wizard | čarovnik | |
| backend / provider / endpoint | zaledje / ponudnik / končna točka | |
| method / listener / controller | metoda / poslušalec / krmilnik | |
| allowlist | seznam dovoljenih | |
| read-only | samo za branje | |
| argument | argument | |
| slot / collection | reža / zbirka | |
| facets / elevate | vidiki / izpostavi | Solr terms |
| filter | filter | verb: filtriraj |
| details | podrobnosti | |
| tick (checkbox) | potrdi | Tick the box: Potrdite polje |
| container (content class, "Is container") | vsebnik | class flag "Is container": Je razred vsebnika |
| container (Bootstrap/CSS) | vsebnik | |
| locale | področne nastavitve | locale code: koda področnih nastavitev; with a code: „%name (%locale)“ |
| localization | lokalizacija | |
| charset | nabor znakov | |
| license / licence | licenca | a permission stays dovoljenje |
| privilege (DB) / grant | pravica / dodeli pravico | |
| format (file/data) | oblika zapisa | |
| formatting (text) | oblikovanje | |
| linked / common | povezano / skupno | |
| flag / flags (class features, settings) | zastavica / zastavice | language flags (pictures): zastave; a command-line flag without a value: stikalo |
| teaser | napovednik | |
| slug | prijazni URL | |
| placeholder | ograda | |
| preset | prednastavitev | |
| information collection / collector | zbiranje informacij / zbiralnik informacij | one collection (one sending): zbirka; Collection ID: ID zbirke |
| overview / summary | pregled / povzetek | |
| tab | zavihek | |
| rich text | obogateno besedilo | |
| feed | vir | RSS feed: vir RSS; feed reader: bralnik virov |
| session | seja | |
| scope | obseg | |
| thread | nit | conversation thread or process thread |
| chain | veriga | |
| batch | serija | in a queue: serija opravil; never "paket" (= package) |
| pool | zaloga | pool of processes: zaloga procesov |
| schema / registry / manifest | shema / register / manifest | |
| stylesheet / theme / icon | slogovna predloga / tema / ikona | |
| port | vrata | |
| digest | povzetek | e-mail digest: zbirno obvestilo |
| profile | profil | |
| core (Solr) | jedro | |
| kernel | jedro | the kernel of Exponential |
| editor (person) | urednik | |
| editor (software) | urejevalnik | |
| Editor (siteaccess type) | urednik | |
| page / pages | stran / strani | |
| link | povezava | |
| title / name / description | naslov / ime / opis | |
| date / time | datum / čas | |
| status | status | |
| type | vrsta | |
| group | skupina | |
| list | seznam | |
| value / field | vrednost / polje | |
| number / count | številka / število | |
| width / height / size | širina / višina / velikost | |
| server / client | strežnik / odjemalec | |
| request / response | zahteva / odziv | |
| pattern | vzorec | |
| repository | skladišče | |
| maintainer | vzdrževalec | |
| install / uninstall / installation | namesti / odstrani namestitev / namestitev | uninstalling: odstranitev namestitve; keeps it apart from removing a package from a list |
| checksum | kontrolna vsota | |
| quarantine | karantena | |
| mapping | preslikava | |
| resolver / resolve / resolvable | razreševalnik / razreši / razrešljivo | |
| checkpoint | kontrolna točka | |
| child documents (Solr) | podrejeni dokumenti | |
| script | skript | |
| revoke | prekliči | revoked: preklicano |
| label | oznaka | label of a field: oznaka polja; where it could be read as a keyword tag: napis |
| plugin | vtičnik | |
| debug | odpravljanje napak | debug output: izpis za odpravljanje napak |
| confirmation questions ("Delete ...?") | Ali res želite ...? | "Delete this item?": "Ali res želite izbrisati ta element?" |

## 5. Decisions after the translation

These settle terms outside the glossary and the collisions between meanings; they take precedence over section 4.

| English | Slovenian | Distinction |
|---|---|---|
| conflict | spor | not konflikt; „Spori“, „spor za razrešitev“ |
| commit (git, Solr, database) | uveljavitev | merge commit: uveljavitev združevanja; running commit: nameščena uveljavitev; not potrditev (= confirmation) |
| issue (GitHub) / issue (problem) | zadeva / težava | issue tracker: sledilnik zadev |
| pull request / fork / branch | zahteva za združitev / razcep / veja | "(pull request)" in brackets at most once per text |
| markup | označevalna koda | never oznake (= tags) |
| render / renderer | izriši, izrisovanje / izrisovalnik | rendered views: izrisani pogledi; not upodobi |
| transport (mail, HTTP, stdio) | prenos | Mail transport: Prenos e-pošte; not prenosnik; the INI key Transport stays |
| placement (node assignment) | umestitev | the trash column "Original Placement" is the old location: Izvirna lokacija |
| purge | počisti (cache, pages) / trajno izbriši (object, order, audit) | noun: čiščenje / trajni izbris; "Remove for good", "Purge from the trash": Trajno odstrani (adverb first) |
| timeout / timed out | časovna omejitev / potekel | "Session timeout": Časovna omejitev seje |
| expire / expired | poteči / poteklo | |
| stale | zastarelo | |
| lock / locked | zaklep / zaklenjeno | |
| stage (pass of a process) | faza | the numbered MySQL privilege stages stay stopnja; staging environment: predprodukcija (adjective predprodukcijski, also "staging site") |
| probe | preverjanje | not preizkus (= test): "Test and probe": Preizkus in preverjanje |
| health / health check | stanje (sistema) / preverjanje stanja | |
| sink (audit) | cilj zapisovanja | not ponor: Cilji zapisovanja revizije |
| turn (conversation) / round (review) | izmenjava / krog | "turn %n": izmenjava %n; "round %n": krog %n |
| engine | pogon | search engine: iskalnik; template engine: pogon predlog; not mehanizem |
| True / False (setting values) | Drži / Ne drži | literal values in code (true, false, AllowWrite=true) stay |
| Searchable / not searchable | Omogoča iskanje / Ne omogoča iskanja | |
| checkbox / radio buttons | potrditveno polje / izbirni gumbi | |
| grip (drag handle) | ročica | |
| receipt (shop) | potrdilo o naročilu | not potrdilo o plačilu |
| hash / hash chain | zgoščena vrednost / veriga zgoščenih vrednosti | |
| staged / staged review | pripravljeno / pregled pred zapisom | |
| compile / compiled / compiler | prevedi / prevedeno / prevajalnik | only for templates and code; a translation is prevod |
| entry / record | vnos / zapis | |
| header (HTTP, file) | glava | a heading in text: naslov |
| secret / credentials | skrivnost / poverilnice | |
| machine / workstation | računalnik / delovna postaja | |
| Breadcrumb | Drobtinice | |
| Landscape / Italic | Ležeče / Ležeče | both are the established terms; the context separates them |
| Undo / Redo | Razveljavi / Uveljavi | |
| hit / miss / hit rate | zadetek / zgrešitev / delež zadetkov | |
| cronjob part | del opravila cron | pl. deli opravil cron |
| image alias | vzdevek slike | pl. vzdevki slik |
| "May %action" (role summary) | Sme %action | „Sme %action, %conditions“ |
| "New %1" (new object of a class) | Novo: %1 | |

### Forms with counts and times

- **"%n … ago"**: the short unit after the number: "pred %n min", "pred %n h", "pred %n s", "pred %n d". A declined
  noun ("pred %count dnevi") is wrong for 1 and 101. Where the whole duration is a placeholder ("%1 ago",
  "%time ago"), write "pred %1", "pred %time".
- **Standalone units** (year, years … minutes) are in the instrumental (letom, leti …) because they are inserted
  into "pred %1". "čez %1" (in %1) is then grammatically imperfect; this limitation of the source is accepted.
- **"%days days"** and similar counts with a second noun: the label form, „Dni: %days“, „Ni spremenjeno (dni:
  %days)“.
- **Column headings for a person** are gender-neutral: „Avtor zadnje spremembe“, „Premaknjeno v koš (uporabnik)“.
