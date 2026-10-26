# Romanian (ron-RO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Romanian (limba română literară) as used in Romania, following the current spelling of the Romanian
Academy (DOOM3): "â" inside words ("român", "sunt", "pâine"), "î" at the start and end of words and after a prefix
("în", "a coborî", "neîncredere"). The tone is neutral, concise and professional, as in the Romanian versions of
Windows and Microsoft 365; the Microsoft terminology for Romanian is the reference for every IT term. No
colloquial forms and no needless English words; an English term stays only where it is the established Romanian IT
word (cache, link, token, server, script, plugin) or a product or code name. No other locale falls back to this
catalogue.

## 2. Form of address

The user is addressed formally, in the second person plural (the courtesy form of dumneavoastră), everywhere. The
pronoun itself is used sparingly; the verb form carries the address.

- **Buttons, menu items, links and action labels** use the long infinitive (a noun of action), as in Microsoft
  software: "Salvare", "Ștergere", "Editare", "Adăugare", "Anulare", "Creare", "Aplicare modificări", "Trimitere
  spre publicare", "Selectare totală", "Golire coș de reciclare". The object follows the noun without an article
  where the label is short ("Salvare ciornă", "Eliminare selecție"). Not the imperative ("Salvați") and not the
  short infinitive ("a salva").
- **Titles, column headings and tabs** are nouns or noun phrases: "Structura conținutului", "Conturi de
  utilizator", "Tip de aspect", "Setări notificări", "Limbă principală".
- **Sentences, hints and instructions** use the second person plural imperative and verb forms: "Selectați o
  locație.", "Introduceți o adresă de e-mail validă.", "Verificați conexiunea și încercați din nou.", "Puteți
  modifica setările istoricului versiunilor în content.ini ...". A polite request may start with "Vă
  rugăm să ...": "Vă rugăm să așteptați."
- **Questions and confirmations**: "Sigur doriți să ...?": "Sigur doriți să eliminați această clasă?", "Sigur doriți
  să ștergeți acest element?". A question with "Doriți să ...?" is also correct.
- **Error messages** state the fact, addressing the user only where they must act: "Nu aveți permisiunea de a
  elimina pachete.", "Fișierul nu a putut fi încărcat.", "Faceți clic pe butonul „Anulare” și încercați să eliminați
  doar locațiile pe care aveți permisiunea să le eliminați."
- **Tooltips that describe a click**: "Faceți clic pentru ...": "Faceți clic pe pictogramă pentru a afișa meniul
  contextual."
- **Status messages** are passive or impersonal: "O ciornă a fost eliminată.", "Modificările au fost salvate cu
  succes.", "Nu a fost găsită nicio rulare cron", "Se încarcă...".
- Not allowed: the familiar form (tu, ta, tău, vrei, poți, selectează as an instruction), the imperative on buttons,
  the pronoun "Dumneavoastră" with a capital letter inside a sentence, and mixed forms within one message.

## 3. Script and typography

- **Latin script with the full set of diacritics**: ă â î ș ț (Ă Â Î Ș Ț). The comma-below letters ș ț (U+0219,
  U+021B, U+0218, U+021A) are mandatory; the cedilla letters ş ţ (U+015F, U+0163) are wrong. Text without
  diacritics is wrong.
- **Sentence case**: only the first word and proper names start with a capital letter: "Structura conținutului",
  "Stări ale obiectelor", "Bibliotecă media". No English title case. Names of languages, months and days are lower
  case ("română", "luni", "ianuarie").
- **Quotation marks** in running text: Romanian quotes „…” (U+201E, U+201D): „%name”. Inside HTML attributes,
  JavaScript and code the straight quotes of the source stay. Nested quotes: «…».
- **No space** before ":", ";", "?" and "!". The space between a number and "%" follows the source.
- **Hyphen** joins pronoun clitics as in standard spelling ("v-ați conectat", "într-un", "s-a salvat"); an English
  abbreviation with a Romanian article takes a hyphen: "URL-ul", "ID-ul", "PDF-ul" — but never after a placeholder
  (see below).
- **Plural and number forms**: Romanian has three forms: one (1), few (0, 2–19, and numbers ending in 01–19 above
  100) and other (20 and more, with "de": "20 de obiecte"). The source only knows "item(s)". Never write
  "obiect(e)". Use a label form where the placeholder stands free: "Obiecte: %count", "Procese de lucru: %count",
  "Ciorne eliminate: %count.".
- **Placeholders** are never inflected and never take an attached article or ending: the Latin letter after
  "%name" would lengthen the placeholder. Put a noun with the article in front of it or a colon: "obiectului <%name>",
  "clasa <%class_name>", "parola utilizatorului %login". Not "%name-ul", not "%nameul". The noun before the placeholder
  carries the case: "Editarea secțiunii %name", "Eliminarea clasei <%1>".
- **Numbers**: digits 0–9; decimal comma in prose ("1,5 MB"); thousands with a dot ("10.000"); keep the source
  number format in code, sizes taken from the program and dates formatted by the locale.
- **Time units** after a number: "s", "min", "h", "zile"; "acum %n min".
- **Dashes**: the en dash with spaces – for a break in a sentence; otherwise as in the source.

## 4. Glossary

Binding for the kernel and every extension. Long-infinitive forms in the Romanian column are the button and menu
form; in sentences use the conjugated verb ("Salvare" on a button, "Salvați modificările" in a hint).

| English | Romanian | Note |
|---|---|---|
| content | conținut | |
| content object / object | obiect de conținut / obiect | |
| item (list entry, general) | element | items: elemente |
| node | nod | main node: nod principal |
| location (node placement) | locație | main location: locație principală |
| content class / class | clasă de conținut / clasă | a PHP class is named "clasă PHP" |
| class group | grup de clase | |
| attribute | atribut | |
| datatype | tip de date | |
| version | versiune | version history: istoricul versiunilor |
| draft | ciornă | Store draft: Salvare ciornă; Discard draft: Renunțare la ciornă |
| publish / published / publishing | publicare / publicat / publicare | Send for publishing: Trimitere spre publicare |
| translation / language | traducere / limbă | interface language: limba interfeței |
| main language / content language | limbă principală / limba conținutului | |
| siteaccess | acces la site | pl. accesuri la site; before a placeholder: accesul la site %siteaccess; `siteaccess`/SiteAccess stays only in paths, INI keys and code |
| site / website | site / site web | |
| design (eZ design) | design | |
| structure | structură | |
| Content structure | Structura conținutului | |
| Media library | Bibliotecă media | |
| Dashboard | Tablou de bord | |
| Setup (admin tab) | Configurare | |
| setting / settings | setare / setări | |
| configuration / configure | configurație / configurare | |
| template | șablon | template file: fișier șablon; template editor: editor de șabloane |
| override / overridden / override template | suprascriere / suprascris / șablon de suprascriere | a setting that replaces another |
| fallback | rezervă | fallback language: limbă de rezervă; fallback chain: lanț de rezervă; English only inside INI keys (FallbackRules[], FallbackLanguages[]) |
| alias / URL alias / image alias | alias / alias URL / alias de imagine | pl. aliasuri; image aliases: aliasuri de imagine |
| wildcard (URL) | metacaracter | pl. metacaractere; URL wildcards: Metacaractere URL |
| redirect / forward (URL wildcard type) / direct | redirecționare / redirecționare permanentă / direct | "forward slash" is bară oblică |
| cache | memorie cache | short: cache; Clear cache: Golire memorie cache |
| workflow | flux de lucru | |
| workflow event / trigger / event | eveniment de flux de lucru / declanșator / eveniment | |
| role | rol | pl. roluri |
| policy | politică | pl. politici |
| limitation (policy) | limitare | |
| permission / access | permisiune / acces | access denied: acces refuzat |
| privilege (DB) / grant | privilegiu / acordare | |
| user / user group | utilizator / grup de utilizatori | |
| user account / user accounts | cont de utilizator / conturi de utilizator | |
| anonymous user | utilizator anonim | |
| administrator / admin interface | administrator / interfață de administrare | |
| user interface | interfață cu utilizatorul | |
| log in, sign in / log out, sign out | conectare / deconectare | in sentences: conectați-vă; failed sign-ins: conectări eșuate; never "logare" |
| login (field) / username / password | nume de conectare / nume de utilizator / parolă | |
| e-mail | e-mail | e-mail address: adresă de e-mail |
| section | secțiune | always the Exponential section of an object |
| object state / state | stare a obiectului / stare | state group: grup de stări |
| status | stare | order status: starea comenzii; HTTP status and exit status stay technical: HTTP %status, codul de ieșire |
| relation / related objects | relație / obiecte asociate | reverse related: obiecte care fac trimitere la acesta |
| trash | coș de reciclare | Empty trash: Golire coș de reciclare |
| bookmark / bookmarks | marcaj / marcaje | |
| sub-items / children | subelemente / elemente subordonate | |
| parent | element părinte | parent node: nod părinte |
| subtree | subarbore | |
| owner / creator / modifier | proprietar / creator / modificat de | Last modifier: Ultima modificare de; in sentences: persoana care îl/o modifică; never "modificator" |
| created / modified | creat / modificat | column headings |
| layout (Exponential Layouts) | aspect | layout type: tip de aspect; the name Exponential Layouts stays |
| page layout (pagelayout.tpl) | machetă de pagină | the frame every page is drawn in; not aspect |
| layout (directory or release structure) | structură | |
| block / zone | bloc / zonă | |
| extension | extensie | |
| package | pachet | |
| module / view / function | modul / vizualizare / funcție | module view: vizualizare de modul |
| View (button) | Vizualizare | in sentences: vizualizați |
| search | căutare | search engine: motor de căutare |
| search index / reindex / index (verb) | index de căutare / reindexare / indexare | |
| index (database) / index file | index / fișier index | |
| cronjob | activitate cron | |
| schedule | planificare | scheduled: planificat |
| job / queue / worker | activitate / coadă / proces de lucru | pl. activități, procese de lucru; job queue: coadă de activități; worker pool: grup de procese de lucru; only a CI (GitHub Actions) job stays "job" |
| lane (queue) | bandă | pl. benzi; vector lane: banda vectorială |
| run (noun) | rulare | a cron or index run: rulare cron, rulare de indexare |
| dry run / self-test | rulare de probă / autotestare | |
| log / log file | jurnal / fișier jurnal | never the verb "log in" |
| rotation / rotate (log) | rotire / rotire | rotated log: jurnal rotit |
| audit / audit trail | audit / pistă de audit | |
| notification | notificare | notification settings: setări notificări |
| collaboration | colaborare | |
| preview | previzualizare | |
| ID / remote ID / identifier | ID / ID la distanță / identificator | |
| file / folder / directory | fișier / folder / director | |
| image / media | imagine / media | media files: fișiere media |
| thumbnail | miniatură | |
| upload / download | încărcare / descărcare | |
| export / import | export / import | buttons: Exportare / Importare |
| shop | magazin | |
| basket | coș de cumpărături | short: coș, where the shop context is clear |
| order (shop) / order item | comandă / articol | pl. comenzi; Order item remove: Eliminare articol din comandă; Item total: Total articole |
| order (sorting, sequence) / ordering | ordine / ordonare | sort order: ordine de sortare |
| customer / product | client / produs | |
| checkout | finalizarea comenzii | |
| VAT / VAT type | TVA / tip de TVA | |
| discount / discount rule | reducere / regulă de reducere | |
| rule | regulă | |
| wish list | listă de dorințe | |
| currency / price | monedă / preț | |
| payment gateway / gateway | gateway de plată / gateway | |
| newsletter | buletin informativ | |
| subscriber / subscription / subscribe | abonat / abonament / abonare | unsubscribe: dezabonare |
| tag (keyword, eztags) | etichetă | pl. etichete |
| keyword / keywords | cuvânt cheie / cuvinte cheie | |
| tag (HTML/XML) / custom tag | tag / tag personalizat | an HTML tag: tag HTML |
| label (of a field, button, block) | etichetă | where it collides with a keyword tag: text afișat |
| move / copy / swap | mutare / copiere / interschimbare | move in the tree: mutare |
| embedding (vector) / to embed (vector) | reprezentare vectorială / a transforma în reprezentare vectorială | pl. reprezentări vectoriale; not încorporare |
| chunk / vector | fragment / vector | |
| embed / embedded (object in text) | încorporare / încorporat | linked to its page: cu link către; Linked from: Cu link din |
| prompt / model / token | prompt / model / token | |
| AI | IA | inteligență artificială in full where useful |
| deploy / deployment / release / rollback | implementare / implementare / versiune lansată / revenire | release directory: director de versiune; `deploy.sh` stays |
| enable / disable | activare / dezactivare | |
| enabled / disabled | activat / dezactivat | |
| active / inactive | activ / inactiv | |
| warning / error / notice | avertisment / eroare / notificare | |
| required / optional | obligatoriu / opțional | |
| default | implicit | default value: valoare implicită |
| priority | prioritate | |
| sort / ascending / descending | sortare / ascendent / descendent | short: Asc. / Desc. |
| visible / hidden | vizibil / ascuns | |
| hide / show / reveal | ascundere / afișare / afișare | |
| expand / collapse | extindere / restrângere | |
| OK / Yes / No | OK / Da / Nu | |
| Cancel | Anulare | Undo is Anulare acțiune |
| Back / Next / Previous / Close | Înapoi / Următorul / Anteriorul / Închidere | paging: Pagina următoare / Pagina anterioară |
| select / browse | selectare / răsfoire | |
| Select all / Deselect all / Select none | Selectare totală / Deselectare totală / Deselectare totală | |
| path | cale | |
| apply | aplicare | Apply changes: Aplicare modificări |
| save | salvare | |
| store (verb: save) / store (technical: keep data) | salvare / stocare | Store draft: Salvare ciornă; stored XML: XML stocat |
| Store (shop tab) | Magazin | |
| discard | renunțare | Discard changes: Renunțare la modificări |
| edit | editare | |
| remove | eliminare | take out of a list, group or place; the object survives |
| delete | ștergere | destroy; the object is gone |
| new / create / add | nou / creare / adăugare | Create new: Creare nou |
| refresh / update / reset | reîmprospătare / actualizare / resetare | |
| restore / assign / assignment | restaurare / atribuire / atribuire | |
| approve / approval / approved / approver | aprobare / aprobare / aprobat / aprobator | |
| deny / reject / rejected | refuzare / respingere / respins | |
| accept / accepted | acceptare / acceptat | |
| pending | în așteptare | "pending an external action": așteaptă o acțiune externă |
| archived (version, object, order) | arhivat | |
| Archive / Unarchive (button) | Arhivare / Dezarhivare | |
| archive (place, menu item, log archive) | arhivă | |
| archive (file .tar/.tgz/.zip/.phar/.ezpkg) | arhivă | archive file: fișier arhivă |
| statistics / system information | statistici / informații despre sistem | |
| database | bază de date | pl. baze de date |
| handler / wizard | handler / expert | pl. experți; policy wizard: expertul pentru politici; setup wizard: expertul de configurare |
| backend / provider / endpoint | backend / furnizor / punct final | the API of %provider: API-ul furnizorului %provider |
| method / listener / controller | metodă / ascultător / controler | |
| allowlist / read-only / argument | listă de permisiuni / doar în citire / argument | |
| slot / collection / facets / elevate | poziție / colecție / fațete / promovare | Solr elevate: promovare în rezultate |
| filter / details | filtru / detalii | |
| tick (checkbox) | bifare | in sentences: bifați |
| container (content class, "Is container") | container | |
| container (Bootstrap/CSS) | container | |
| locale | setări regionale | Locale code: Codul setărilor regionale; the INI key `Locale` and the path share/locale stay |
| localization | localizare | |
| charset | set de caractere | |
| license | licență | |
| format / formatting | format / formatare | |
| linked / common | legat / comun | |
| flags (class flags) / flags (language pictures) | indicatori / steaguri | |
| teaser | rezumat | |
| slug | segment URL | |
| placeholder | substituent | pl. substituenți |
| preset | presetare | pl. presetări; Save current as...: Salvare configurație curentă ca... |
| information collection / collected information / a collection (record) / information collector | colectare de informații / informații colectate / colectare / colector de informații | pl. colectări; ID colectare; Colectări: %count |
| overview / summary | prezentare generală / rezumat | overview is never rezumat |
| tab (UI) / Tab (key) | filă / Tab | pl. file; the %tab menu: meniul %tab |
| rich text | text formatat | |
| feed | flux | RSS feed: flux RSS |
| session | sesiune | |
| scope | domeniu | |
| thread (conversation) / thread (DB, process) | fir de discuție / fir de execuție | the shared string "Thread" alone: Fir |
| chain | lanț | |
| batch | lot | |
| schema / registry / manifest | schemă / registru / manifest | |
| stylesheet / theme / icon | foaie de stil / temă / pictogramă | |
| port | port | |
| digest | rezumat periodic | |
| profile | profil | |
| core (Solr) | core Solr | not "nucleu", which is the kernel |
| kernel | nucleu | |
| plugin | plugin | |
| script | script | |
| editor (person) / editor (software) | redactor / editor | template editor: editor de șabloane |
| Editor (siteaccess kind) | editare | the editing siteaccess: acces la site pentru editare |
| page / pages | pagină / pagini | |
| link | link | |
| title / name / description | titlu / nume / descriere | the name of a thing: denumire where nume reads as a person's name |
| date / time | dată / oră | duration: timp |
| type | tip | |
| group | grup | |
| list | listă | |
| value / field | valoare / câmp | |
| number / count | număr / număr | count in a label: Număr: %count |
| width / height / size | lățime / înălțime / dimensiune | file size: dimensiune fișier |
| server / client / request / response | server / client / solicitare / răspuns | |
| task | sarcină | |
| debug | depanare | debug output: ieșire de depanare |
| pattern | tipar | not "model", which is the AI model |
| repository | depozit | |
| maintainer | persoană care întreține | short: responsabil |
| install / uninstall / installation | instalare / dezinstalare / instalare | |
| checksum | sumă de control | |
| quarantine | carantină | |
| pool | grup | pool of workers: grup de procese de lucru |
| mapping | mapare | |
| resolver / resolve / resolvable | mecanism de rezolvare / rezolvare / rezolvabil | |
| checkpoint | punct de control | |
| child documents (Solr) | documente copil | |
| revoke | revocare | |
| confirmation questions ("Delete …?", "Cancel this …?") | „Sigur doriți să ștergeți …?”, „Sigur doriți să anulați …?” | |

## 5. Decisions after the translation

These rows complete the glossary for terms that came up in the extensions and take precedence over any older
usage. Where a word has two meanings, the meaning decides, not the English word: "aspect", "stare", "verificare",
"revizuire" and "colectare" each also occur in their other, correct sense.

| English | Romanian | Distinction |
|---|---|---|
| credentials | date de autentificare | not acreditări, not date de acces |
| review (check before applying) / review (editorial) / review (product) | verificare / revizuire / recenzie | Review changes: Verificare modificări; Mark for review: Marcare pentru revizuire |
| staging, STAGE / staged (proposed changes) / staged review | preproducție, PREPROD / pregătit / revizuire prealabilă | staging site: site de preproducție; a pipeline stage is etapă |
| undo / redo | anulare acțiune / refacere | the button Cancel stays Anulare |
| purge (page cache) / purge (cluster, DFS) / purge (trash, object, order, audit) | golire / purjare / eliminare definitivă | never epurare |
| warm / preload (cache) | preîncărcare / preîncărcat | |
| hits / misses / hit rate | accesări reușite / ratări / rată de accesare | |
| cronjob part | parte a activității cron | pl. părți ale activităților cron |
| menu arrangement | dispunere | pl. dispuneri; Menu arrangement: Dispunere meniu; not aranjare |
| extension point | punct de extensie | pl. puncte de extensie; not de extindere |
| commit (git) / merge commit | commit / commit de îmbinare | pl. commituri; to commit: a face commit; not validare |
| issue (GitHub) / pull request | problemă / cerere de extragere (pull request) | |
| turn (conversation) | tură | pl. ture; turn %n: tura %n |
| drop-down | meniu vertical | not listă verticală |
| checkbox | casetă de selectare | in sentences: bifați caseta |
| backup | copie de rezervă | |
| fail closed | blocare la eșec | |
| dead entries / dead reference | intrări moarte / referință moartă | a dead job in a queue: activitate eșuată definitiv |
| escape (output) / Escape (key) | codificarea caracterelor speciale / Esc | |
| keyboard shortcut | comandă rapidă de la tastatură | |
| breadcrumb / syndication / drag handle | traseu de navigare / sindicalizare / mâner | |
| hostname | nume gazdă | with the article: numele gazdei |
| data map | harta de date | |
