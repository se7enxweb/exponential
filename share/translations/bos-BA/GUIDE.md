# Bosnian (bos-BA): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Bosnian as written in Bosnia and Herzegovina, in Latin script and in its ijekavian form (vrijeme, mjesto,
uslov, primijeniti, obavještenje). No other locale falls back to this catalogue; Croatian (`cro-HR`) and Serbian
have catalogues of their own. The guide leans on the Croatian one for structure and many terms, but the vocabulary
is Bosnian, as in the Bosnian versions of common operating systems and office software (Microsoft terminology for
bs-Latn-BA):

| Use (Bosnian) | Not (Croatian or ekavian) |
|---|---|
| historija, sedmica, hiljada, tačka | povijest, tjedan, tisuća, točka |
| sačuvaj, kreiraj, otkaži, greška, uslov | spremi, stvori, odustani, pogreška, uvjet |
| meni, lista, folder, interfejs | izbornik, popis, mapa, sučelje |
| saradnja, obavještenje, prodavnica, korpa | suradnja, obavijest, trgovina, košarica |
| jezički, opšti, hiljadu | jezični, opći, tisuću |
| ijekavian: vrijeme, sljedeći, mijenjati | ekavian: vreme, sledeći, menjati |

The tone is factual, neutral and short. No colloquial words (fajl, sejvati, kliknuti na "OK" dugme ...), no
Turkisms that are not standard in technical writing, and no English where a Bosnian technical word is established.

## 2. Form of address

- **Buttons, menu items, links and action labels** use the imperative singular, as in the Bosnian Microsoft
  interface: "Sačuvaj", "Otkaži", "Ukloni", "Izbriši", "Uredi", "Objavi", "Primijeni", "Otpremi", "Preuzmi",
  "Pretraži", "Odaberi sve", "Nazad", "Kreiraj novi", "Pošalji na objavljivanje", "Isprazni smeće".
  Never the infinitive ("Sačuvati") and never the plural imperative on a button ("Sačuvajte").
- **Titles, column headings and tabs** are nouns or noun phrases: "Struktura sadržaja", "Korisnički računi",
  "Tip rasporeda", "Postavke obavještenja", "Stanja objekata".
- **Sentences, hints, questions and error messages** address the user with the formal plural **vi**, written in
  lower case (vi, vas, vaš, vaša, vaše), and the plural imperative for instructions:
  - Question: "Da li ste sigurni da želite ukloniti ovu klasu?"
  - Request: "Odaberite lokaciju." (Please choose a location), "Unesite važeću adresu e-pošte."
  - Instruction: "Provjerite vezu i pokušajte ponovo."
  - Error: "Nemate dozvolu za uklanjanje klasa.", "Datoteka nije pronađena."
  - Statement: "Postavke historije verzija možete promijeniti u datoteci content.ini ..."
  - Tooltip that describes a click: "Kliknite ikonu za prikaz kontekstnog menija."
- **Questions** start with "Da li ...?" ("Da li želite ...?", "Da li ste sigurni ...?"), the usual Bosnian
  form; do not mix with "Jeste li ...?". After "želite" use the infinitive: "želite izbrisati", not
  "želite da izbrišete".
- **Status messages** are impersonal or passive: "Objavljeno.", "Unaprijed definisana postavka je poništena.",
  "Nije pronađeno nijedno pokretanje cron-zadataka".
- Not allowed: the familiar form in a sentence (ti, tvoj, možeš, da li si); "Vi/Vaš" with a capital letter inside a
  sentence; the singular imperative inside a sentence ("Odaberi" is a button, in a sentence it is "Odaberite");
  the infinitive as an instruction in a sentence ("Koristiti ovo polje za ..." is wrong; write "Ovo polje služi
  za ...").

## 3. Script and typography

- **Script**: Latin script with č, ć, dž, đ, š, ž. Never Cyrillic, never "dj" for "đ". Names of products, formats,
  commands and settings stay as in TRANSLATING.md.
- **Capitals**: sentence case; only the first word and proper names are capitalised ("Korisnički računi", not
  "Korisnički Računi"). Names of languages, months and days are lower case inside a sentence ("bosanski",
  "ponedjeljak").
- **Quotation marks** in running text: „…“ (U+201E, U+201C). Inside HTML attributes and JavaScript the straight
  quotes of the source stay; a quoted code value may keep straight quotes.
- **Punctuation**: no space before ":", ";", "?" and "!". The ellipsis and the final punctuation as in the source.
- **Plural forms**: Bosnian has three forms (1 / 2–4 / 5 and more: "1 objekat", "3 objekta", "5 objekata"); the
  source has only "item(s)". Never write "objekat(a)". Use a label form in which the number stands alone:
  "Broj objekata: %count", "Radni procesi: %count", "Uklonjeni nacrti: %count."
- **Placeholders are never declined and never get an ending glued on.** "%namea" or "%count-a" is a different or a
  broken placeholder. Put the placeholder after a noun that carries the case, or in a label form:
  "Uredi klasu <%class_name>.", "Odaberi %name za uklanjanje", "Uredi <%class_name> (broj objekata:
  %object_count)". A punctuation mark right after a placeholder is fine.
- **Compounds with a code or an abbreviation** take a hyphen, as usual in Bosnian: "cron-zadatak", "web-stranica",
  "XML-tag", "INI-datoteka"; or stand apart where the abbreviation is an attribute: "PDF stranice", "URL adresa".
- **Numbers**: digits 0-9; decimal comma, a dot as thousands separator in prose ("1,5 MB", "10.000"). Time units
  after a number: "s", "min", "h"; dates as "23. 10. 2026." with a full stop after the year in running text when
  the source does not prescribe a format.

## 4. Glossary

Binding for the kernel and every extension. Nouns are given in the nominative singular; the button form is the
imperative singular.

| English | Bosnian | Note |
|---|---|---|
| content | sadržaj | |
| content object | objekat sadržaja | gen. objekta; pl. objekti |
| object | objekat | the established Bosnian form; not "objekt" |
| item (list entry, general) | stavka | pl. stavke |
| node | čvor | main node: glavni čvor |
| location (node placement) | lokacija | main location: glavna lokacija |
| content class / class | klasa sadržaja / klasa | a PHP class is "PHP klasa" |
| class group | grupa klasa | |
| attribute | atribut | |
| datatype | tip podataka | |
| version | verzija | version history: historija verzija |
| draft | nacrt | Store draft: Sačuvaj nacrt; Discard draft: Odbaci nacrt |
| publish / published | objavi / objavljeno | Send for publishing: Pošalji na objavljivanje; noun: objavljivanje |
| translation | prijevod | |
| language | jezik | adjective: jezički |
| main language / content language | glavni jezik / jezik sadržaja | interface language: jezik interfejsa |
| siteaccess | pristup web-stranici | pl. pristupi web-stranici |
| site / website | web-stranica | sitemap: mapa web-stranice |
| design (Exponential design) | dizajn | |
| structure | struktura | |
| template | šablon | pl. šabloni |
| override (template, setting) | nadjačavanje | override template: šablon za nadjačavanje; overridden: nadjačano |
| fallback (language) | rezervni jezik | fallback chain: lanac rezervnih jezika; fallback template: rezervni šablon |
| alias / URL alias | alias / URL alias | |
| wildcard (URL) | zamjenski znak | URL wildcard: URL sa zamjenskim znakom; Wildcards: Zamjenski znakovi |
| redirect / forward | preusmjeri / proslijedi | noun: preusmjeravanje |
| cache | keš memorija | short form "keš" in compounds: keš šablona; Clear cache: Očisti keš memoriju |
| workflow | tok rada | pl. tokovi rada |
| workflow event / event | događaj toka rada / događaj | |
| trigger | okidač | |
| role | uloga | |
| policy | pravilo | pl. pravila |
| limitation (policy) | ograničenje | |
| permission | dozvola | Permission denied: Dozvola je odbijena; file mode (owner and mode): prava pristupa |
| access / access denied | pristup / pristup odbijen | |
| user | korisnik | |
| user group | grupa korisnika | |
| user account | korisnički račun | pl. korisnički računi |
| anonymous user | anonimni korisnik | |
| administrator / admin interface | administrator / administratorski interfejs | |
| log in / log out | prijavi se / odjavi se | noun: prijava / odjava |
| login (field) / username | korisničko ime | |
| password | lozinka | |
| e-mail | e-pošta | e-mail address: adresa e-pošte |
| section | sekcija | always the Exponential section of an object; any other section: see section 5 |
| object state / state | stanje objekta / stanje | state group: grupa stanja |
| relation | veza | |
| related objects / reverse related | povezani objekti / obrnuto povezani objekti | |
| trash | smeće | Empty trash: Isprazni smeće |
| bookmark | favorit | pl. favoriti; My bookmarks: Moji favoriti |
| sub-items / children | podstavke / podređene stavke | |
| parent | nadređeni element | parent node: nadređeni čvor |
| subtree | podstablo | |
| owner | vlasnik | |
| creator / modifier | autor / izmijenio | column heading "Modifier": Izmijenio |
| created / modified | kreirano / izmijenjeno | column headings |
| layout (Exponential Layouts) | raspored | layout type: tip rasporeda; the name Exponential Layouts stays; page layout: see section 5 |
| layout (directory or release structure) | struktura | |
| block / zone | blok / zona | block type: tip bloka |
| setting / settings | postavka / postavke | |
| Setup (admin tab) | Podešavanje | |
| configuration / configure | konfiguracija / konfiguriši | |
| Content structure | Struktura sadržaja | |
| Media library | Biblioteka medija | |
| Dashboard | Kontrolna tabla | |
| extension | proširenje | |
| package | paket | |
| module | modul | |
| view (noun, module view) | prikaz | Module view: prikaz modula |
| view (button) | Prikaži | |
| function (module function) | funkcija | |
| search | pretraga | button: Pretraži; search engine: pretraživač; other engines: see section 5 |
| search index | indeks pretrage | |
| reindex / index (verb) | ponovno indeksiranje / indeksiraj | |
| index (database) | indeks | |
| cronjob | cron-zadatak | |
| schedule | raspored izvršavanja | scheduled: zakazano |
| job | posao | pl. poslovi |
| queue | red čekanja | job queue: red čekanja poslova |
| worker | radni proces | persistent workers: trajni radni procesi |
| task | zadatak | |
| log / log file | zapisnik / datoteka zapisnika | never the verb "log in"; a single log entry: unos |
| audit / audit trail | revizija / revizijski trag | |
| notification / notify | obavještenje / obavijestiti | the noun is always "obavještenje" |
| collaboration | saradnja | |
| preview | pretpregled | |
| ID / remote ID / identifier | ID / udaljeni ID / identifikator | |
| file | datoteka | |
| folder / directory | folder / direktorij | |
| image / media | slika / mediji | |
| thumbnail | sličica | |
| upload | otpremi | noun: otpremanje |
| download | preuzmi | noun: preuzimanje |
| export / import | izvezi / uvezi | noun: izvoz / uvoz |
| shop | prodavnica | |
| Store (shop tab) | Prodavnica | every other "Store" is the verb "Sačuvaj" |
| basket | korpa | |
| order (shop) | narudžba | pl. narudžbe; order status: status narudžbe |
| order (sorting) | redoslijed | sort order: redoslijed sortiranja |
| customer / product | kupac / proizvod | |
| checkout | plaćanje | Proceed to checkout: Nastavi na plaćanje |
| VAT / VAT type | PDV / vrsta PDV-a | |
| discount / discount rule | popust / pravilo popusta | |
| rule | pravilo | |
| wish list | lista želja | |
| price / currency | cijena / valuta | |
| payment gateway / gateway | platni prolaz / prolaz | |
| newsletter | bilten | |
| subscriber / subscription / subscribe | pretplatnik / pretplata / pretplati se | |
| tag (keyword, eztags) | oznaka | pl. oznake |
| keyword | ključna riječ | pl. ključne riječi |
| tag (HTML/XML) / custom tag | tag / prilagođeni tag | "HTML tag", "XML-tag" |
| move | premjesti | move (in the tree, to a group): premjesti |
| copy | kopiraj | |
| swap | zamijeni | |
| embed / embedded (object in text) | ugradi / ugrađeno | "embeds the question" (vector): pretvara u vektorsku reprezentaciju |
| embedding (vector) | vektorska reprezentacija | |
| chunk / vector | segment / vektor | |
| prompt / model / token | upit / model / token | |
| AI | umjetna inteligencija | short: AI stays in Latin letters as an abbreviation |
| deploy / deployment / release | isporuči / isporuka / izdanje | |
| enable / disable | omogući / onemogući | |
| enabled / disabled | omogućeno / onemogućeno | |
| active / inactive | aktivno / neaktivno | |
| warning / error / notice | upozorenje / greška / napomena | |
| required / optional | obavezno / neobavezno | |
| default | zadano | default value: zadana vrijednost |
| priority | prioritet | |
| sort / ascending / descending | sortiraj / rastuće / opadajuće | |
| visible / hidden | vidljivo / skriveno | |
| hide / show / reveal | sakrij / prikaži / otkrij | |
| expand / collapse | proširi / sažmi | |
| OK / Yes / No | U redu / Da / Ne | |
| Cancel | Otkaži | |
| Back / Close | Nazad / Zatvori | |
| Next / Previous | Sljedeća / Prethodna | pagination; a wizard step button is "Dalje" |
| select / browse | odaberi / pregledaj | |
| select all / deselect all / select none | Odaberi sve / Poništi odabir / Ne odaberi ništa | |
| apply | primijeni | Apply changes: Primijeni promjene |
| save / store | sačuvaj | |
| discard | odbaci | |
| edit | uredi | |
| remove | ukloni | take out of a list, group or place; Remove selected: Ukloni odabrano |
| delete | izbriši | destroy; Delete selected: Izbriši odabrano |
| new / create / add | novi / kreiraj / dodaj | Create new: Kreiraj novi |
| refresh / reset | osvježi / resetuj | |
| update | ažuriraj | |
| run (verb / noun) | pokreni / pokretanje | a cron or index run: pokretanje |
| dry run | probno pokretanje | |
| self-test | samotestiranje | |
| restore | vrati | restore from trash: vrati iz smeća |
| assign / assignment | dodijeli / dodjela | |
| approve / approval / approved / approver | odobri / odobrenje / odobreno / odobravatelj | |
| deny / reject / rejected | odbij / odbij / odbijeno | |
| accept / accepted | prihvati / prihvaćeno | |
| pending | na čekanju | |
| statistics / system information | statistika / informacije o sistemu | |
| system | sistem | not "sustav" |
| database | baza podataka | |
| path | putanja | |
| handler | rukovatelj | |
| wizard | čarobnjak | |
| backend / provider / endpoint | pozadinski sistem / pružalac usluge / krajnja tačka | provider: gen. pružaoca, pl. pružaoci; scope provider: pružalac opsega |
| method / listener / controller | metoda / osluškivač / kontroler | |
| allowlist | lista dozvoljenih | |
| read-only | samo za čitanje | |
| argument | argument | |
| slot / collection | utor / kolekcija | |
| facets / elevate | faseti / izdvajanje | Solr terms |
| filter | filter | button: Filtriraj |
| tick (checkbox) | označi | |
| container (class flag "Is container") | kontejner | |
| container (CSS/Bootstrap) | kontejner | |
| locale | lokalne postavke | locale code: kod lokalnih postavki; locale values (ger-DE) stay as code |
| localization | lokalizacija | |
| charset | skup znakova | |
| license | licenca | |
| privilege (DB) / grant | privilegija / dodijeli | |
| format (file/data) | format | |
| formatting (text) | oblikovanje | |
| linked / common | povezano / zajedničko | |
| flags (class flags) / flag (country, language) | oznake klase / zastava | |
| teaser | najava | |
| slug | URL naziv | |
| placeholder | rezervisano mjesto | input hint: tekst rezervisanog mjesta |
| preset | unaprijed definisana postavka | tag preset: unaprijed definisane oznake |
| information collection / collector / collection (one submission) | prikupljanje informacija / prikupljač informacija / kolekcija | collected information: prikupljene informacije |
| overview / review / summary | pregled / pregled / sažetak | review as a verb in a sentence: pregledajte or uporedite |
| tab (UI) | kartica | the Tab key: tipka Tab; tab character: tabulator |
| rich text | obogaćeni tekst | |
| feed | feed | pl. feedovi; RSS feed stays |
| session | sesija | |
| scope | opseg | |
| thread (conversation, process) | nit | conversation thread: nit razgovora |
| chain | lanac | |
| batch | serija | |
| schema / registry / manifest | shema / registar / manifest | |
| stylesheet / theme / icon | stilska datoteka / tema / ikona | |
| port | port | |
| digest | sažetak | e-mail digest: sažetak e-pošte |
| profile | profil | |
| core (Solr) | jezgro | |
| lane (queue) | traka | |
| editor (person) | urednik | |
| editor (software) | uređivač | online editor: mrežni uređivač; template editor: uređivač šablona |
| Editor (siteaccess kind) | uređivački pristup | |
| user interface | korisnički interfejs | |
| page / pages | stranica / stranice | |
| link | veza | hyperlink: veza; "link" only inside code |
| title / name / description | naslov / naziv / opis | a person's name: ime |
| date / time | datum / vrijeme | |
| status / type | status / tip | |
| group | grupa | |
| list | lista | |
| value / field | vrijednost / polje | |
| number / count | broj / broj | |
| width / height / size | širina / visina / veličina | |
| server / client | server / klijent | |
| request / response | zahtjev / odgovor | |
| archive (archived version, object, order) | arhivirano | |
| Archive / Unarchive (button) | Arhiviraj / Vrati iz arhive | |
| archive (place, menu item, log archive) | arhiva | |
| archive (file .tar/.zip/.phar/.ezpkg) | arhivska datoteka | |
| rotation / rotate (log) | rotacija / rotiraj | |
| pattern | uzorak | |
| repository | repozitorij | |
| maintainer | održavatelj | |
| install / uninstall / installation | instaliraj / deinstaliraj / instalacija | |
| checksum | kontrolni zbir | |
| quarantine | karantin | |
| pool | skup | pool of processes: skup procesa |
| mapping | mapiranje | |
| resolve / resolvable | razriješi / razrješivo | |
| resolver | razrješivač | |
| checkpoint | kontrolna tačka | |
| index file | indeksna datoteka | |
| child documents (Solr) | podređeni dokumenti | |
| script | skripta | |
| revoke | opozovi | revoke a key, token, session |
| label | oznaka polja | the caption of a field, button or block |
| plugin | dodatak | |
| kernel | jezgro | the Exponential kernel: jezgro sistema |
| debug | otklanjanje grešaka | debug output: izlaz za otklanjanje grešaka |
| details | detalji | |
| history | historija | |
| week / thousand | sedmica / hiljada | |
| menu | meni | context menu: kontekstni meni |
| service | usluga | |
| condition | uslov | |

## 5. Decisions after the translation

This section completes the glossary for terms that turned out to need a fixed form or a split by meaning. Where
one English word has several Bosnian equivalents, the meaning in the source decides, not the word.

| English | Bosnian | Distinction |
|---|---|---|
| action (menu, column, button, result) | radnja | Actions: Radnje; More actions: Više radnji; field action: radnja polja. Never "akcija" (in a shop it means a sale) |
| alert (monitoring, audit, AI, admin) | alarm | keeps it apart from warning = upozorenje; alert rule: pravilo alarma. Security e-mails to users may say "sigurnosna upozorenja" |
| orphan / orphaned | napušten / napušteno | Napuštene datoteke, Napušteni objekti, Napušteni direktoriji podjele |
| receipt (shop) | račun za kupovinu | pl. računi za kupovinu; not "potvrda" (= confirmation), not "priznanica" |
| page layout (pagelayout.tpl) | izgled stranice | "raspored" is reserved for Exponential Layouts |
| storage (place, var/storage, file or session storage) | skladište | storage key: ključ skladišta; storage directory: direktorij skladišta; the act of storing: čuvanje; MySQL storage engine: mehanizam za pohranu; secret store: pohrana tajni |
| engine (database, SQL, template, chain) | mehanizam | mehanizam baze podataka, mehanizam šablona |
| engine (Velocity server runtime, online editor) | pogon | Zadani pogon; Pogoni uređivača |
| backup | sigurnosna kopija | not "rezervna kopija" (reads as a spare copy) |
| checkbox | polje za potvrdu | not "polje za označavanje" |
| render / renderer | iscrtati / modul za iscrtavanje | noun: iscrtavanje. A whole page built on request may be "generisati". Never "renderovati" |
| proxy | proksi | reverse proxy: obrnuti proksi |
| fetch (data, URL, feed) | dohvatiti / dohvatanje | "preuzmi" is a download to the user |
| fetch function / fetch alias | fetch-funkcija / fetch-alias | with hyphen |
| store / keep (verb in prose) | čuvati / sačuvati | never "spremati" (Croatian); "spreman" (= ready) is fine |
| section (INI [Section], part of a page or report) | odjeljak | "u odjeljku [VATSettings]"; never "sekcija". The settings editor, whose source calls it "block", keeps "blok" |
| staging (environment) / stage (step) | pretprodukcija / faza | adj. pretprodukcijski; DEV/TEST/STAGE/PROD badges stay |
| purge (cache, pages) | očistiti / čišćenje | |
| purge (objects, trash, audit, orders) | trajno izbrisati / trajno brisanje | |
| console (./console) | konzola | konzolna naredba; the command itself stays code |
| landscape / portrait (device) | vodoravno / uspravno | |
| entry (log, cache, list, archive) | unos | "stavka" only for items of a visible list |
| report (document) / report (on a tracker) | izvještaj / prijava | issues found by a check: problemi |
| credentials / secret | pristupni podaci / tajna | |
| dependency | zavisnost | not "ovisnost" |
| timeout | vremensko ograničenje | execution timeout: prekoračenje vremena izvršavanja |
| threshold / budget | prag / budžet | time budget: vremenski budžet |
| level | nivo | never "razina" |
| hit / miss / hit rate | pogodak / promašaj / stopa pogodaka | |
| shell / command line | ljuska / komandna linija | shell access: pristup ljusci; "from a shell" as the origin of an action: iz komandne linije |
| toggle | Uključi/isključi | for an on/off switch; Toggle selection: Promijeni odabir |
| generate / regenerate | generiši / ponovo generiši | |
| vendor (package) / changelog / dataset | dobavljač / dnevnik promjena / skup podataka | |
| connection (database, server) | veza | Test connection: Testiraj vezu |
| key (keyboard) / key (secret, cache) | tipka / ključ | |
| machine, computer | računar | |

### Rules

1. **Croatian forms do not belong here**: "spremati" (→ čuvati), "općenit" (→ opšti), "rezervna kopija"
   (→ sigurnosna kopija), "ovisnost" (→ zavisnost), "razina" (→ nivo).
2. **Verbs of foreign origin end in -isati / -ovati**, as in the glossary (konfiguriši, resetuj): definisati,
   generisati, registrovati, sinhronizovati, in every form (definisano, definišite); not "definirati",
   "generirati".
3. **One English term, one Bosnian term per meaning**: where a table splits a term by meaning (engine, section,
   purge, report), the meaning in the source decides.
4. **Code stays code**: INI keys, file names, commands (./console, deploy.sh, purge_orphans.php), placeholders and
   module/view names (section/assign, content/view) are not translated, even when the word also has a table entry.
