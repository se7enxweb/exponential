# Hungarian (hun-HU): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Hungarian as used in Hungary, following the current rules of the Hungarian Academy of Sciences (AkH., 12th
edition) and the Hungarian Microsoft terminology and style guide. No other locale falls back to this catalogue. The
tone is neutral, concise and professional, as in the Hungarian versions of common operating systems and office
software. Avoid colloquial forms, slang and needless English words; where an established Hungarian IT term exists
(gyorsítótár, sablon, bővítmény, munkamenet), use it.

## 2. Form of address

- **Buttons, menu items, links and action labels** use the verbal noun (‑ás/‑és), as in Hungarian Microsoft
  software: "Mentés", "Törlés", "Szerkesztés", "Hozzáadás", "Mégse", "Új létrehozása", "Módosítások alkalmazása",
  "Küldés közzétételre", "Az összes kijelölése". With an object, the object comes first: "Piszkozat mentése",
  "Gyorsítótár ürítése", "Osztály szerkesztése".
- **Titles, column headings and tabs** are nouns or noun phrases: "Tartalomszerkezet", "Felhasználói fiókok",
  "Elrendezéstípus", "Értesítési beállítások".
- **Sentences, hints, questions and error messages** address the user formally (Ön), with the verb in the third
  person singular. The pronoun itself is left out wherever the verb already carries the person, which is almost
  always; where it is needed, it is written with a capital letter (Ön, Önt, Önnek, Öné):
  - "Biztosan el szeretné távolítani ezt az osztályt?"
  - "Válasszon helyet" (Please choose a location)
  - "Nincs jogosultsága osztályok eltávolításához."
  - "Kattintson a „Mégse” gombra, és próbálja meg csak azokat a helyeket eltávolítani, amelyeket eltávolíthat."
  - "A verzióelőzmények beállításait a content.ini fájlban módosíthatja ..."
- **Tooltips that describe a click** use "Kattintson ... a(z) ...-hoz/hez": "Kattintson az ikonra a helyi menü
  megjelenítéséhez."
- **Status messages** are impersonal, with the ‑va/‑ve participle or an impersonal verb: "Az összes munkamenet törölve.",
  "Az értesítési beállítások mentése megtörtént.", "Nem található cron-futás", "Nincs megjeleníthető elem".
- Not allowed: the familiar form (te, téged, töröld, szeretnéd, kattints); the polite imperative on a button
  ("Mentse", "Törölje" as a button label is wrong; write "Mentés", "Törlés"); "maga" as the address; lower-case
  "ön" when the pronoun is written; and the verbal noun as an instruction inside a sentence ("Mező használata ...
  céljára" is wrong; write "Ez a mező ... szolgál" or "Ezzel a mezővel ... adhatja meg").

## 3. Script and typography

- Latin script with the full set of Hungarian letters (á é í ó ö ő ú ü ű). Ő and ű are the double acute letters,
  never õ or û. Sentence case everywhere, also in titles and buttons: "Tartalomszerkezet", "Objektumállapotok",
  "Felhasználói fiókok". Language, month and day names are written in lower case ("magyar", "október").
- Quotation marks in running text: „…” (low 99, high 99); a quotation inside a quotation: »…«. Inside HTML
  attributes and JavaScript the straight quotes of the source stay.
- **Number and plural**: after a number or a quantity word Hungarian uses the singular noun. The source's
  "item(s)" is therefore simply "elem": "%count objektum", "%count munkafolyamat". Never
  write "elem(ek)". A label form is also fine: "Osztályok ezen az oldalon: %count".
- **Placeholders and case endings**: the suffix vowel cannot be chosen for a value that is unknown,
  so a placeholder should stand free. Use a head noun that takes the ending instead: "A(z) %name szakasz
  szerkesztése", "A(z) %state állapot szerkesztése", "Áthelyezve ide: %position / %count". Before a placeholder the definite
  article is written "a(z)". Only where no free construction works, separate the ending with a hyphen
  (`%name-t`, `%count-ból`) and pick the back-vowel form; never glue a letter to a placeholder (`%namet` is a
  different placeholder).
- **Abbreviations, numbers and foreign names in compounds** take a hyphen: URL-álnév, HTML-tag, XML-fájl,
  PHP-osztály, cron-feladat, Solr-mag, 5-ös verzió.
- **Numbers**: decimal comma (3,5); numbers of five or more digits are grouped with a (non-breaking) space
  (12 500); a percentage is written without a space in running text (25%). Dates: 2026. 10. 23. (year, month, day,
  each followed by a full stop); times 14:05.
- Time units after a number: "mp", "perc", "óra", "nap"; "%n perce", "%n órája".
- **Exponential** in running text, where Hungarian grammar needs a case ending: "az Exponential rendszer",
  "az Exponential rendszerben".

## 4. Glossary

| English | Hungarian | Note |
|---|---|---|
| content | tartalom | |
| content object / object | tartalomobjektum / objektum | |
| item (list entry, general) / item (basket, order, invoice, plan) | elem / tétel | items: elemek; after a number singular: 3 elem; shop and plan lines: Terméktételek, Rendelési tétel |
| node | csomópont | main node: fő csomópont |
| location / main location | hely / fő hely | the placement of a node in the tree |
| content class / class | tartalomosztály / osztály | a PHP class: PHP-osztály |
| class group | osztálycsoport | |
| attribute | attribútum | |
| datatype | adattípus | |
| version | verzió | version history: verzióelőzmények |
| draft | piszkozat | Store draft: Piszkozat mentése; Discard draft: Piszkozat elvetése |
| publish / published / publishing | közzététel / közzétéve / közzététel | Send for publishing: Küldés közzétételre |
| translation / language | fordítás / nyelv | |
| main language / content language | fő nyelv / tartalom nyelve | interface language: felület nyelve |
| siteaccess | webhely-hozzáférés | pl. webhely-hozzáférések; always with the hyphen |
| site / website | webhely | |
| design (eZ design) | arculat | design base: alaparculat |
| structure | szerkezet | |
| Content structure | Tartalomszerkezet | tab and title |
| template | sablon | template editor: sablonszerkesztő |
| override / overridden / override template | felülbírálás / felülbírált / felülbíráló sablon | Override in the sense of TRANSLATING.md: replaces another template or setting |
| fallback | tartalék | fallback language: tartalék nyelv; fallback chain: tartaléklánc; the one used when the first is missing |
| alias / URL alias | álnév / URL-álnév | |
| wildcard (URL) | URL-helyettesítő minta | a wildcard character: helyettesítő karakter |
| redirect / forward (URL) | átirányítás / továbbítás | |
| cache / clear cache | gyorsítótár / gyorsítótár ürítése | |
| workflow / workflow event / workflow process | munkafolyamat / munkafolyamat-esemény / munkafolyamat-példány | process alone in the same view: folyamat |
| trigger / event | eseményindító / esemény | |
| role | szerepkör | |
| policy (role) / policy (data, security, privacy) | házirend / szabályzat | pl. házirendek; Adatkezelési szabályzat |
| limitation (policy) | korlátozás | |
| permission / access / access denied | engedély / hozzáférés / hozzáférés megtagadva | the object of the permission system (role, policy); file owner and mode: jogosultságok |
| privilege (DB) / grant | jogosultság / jogosultság megadása | "Nincs jogosultsága ..." for a missing permission in a sentence |
| user / user group / user account | felhasználó / felhasználócsoport / felhasználói fiók | |
| anonymous user | névtelen felhasználó | |
| administrator / admin interface | rendszergazda / adminisztrációs felület | |
| log in / log out | bejelentkezés / kijelentkezés | verb in a sentence: bejelentkezik / kijelentkezik |
| login (field) / username / password | bejelentkezési név / felhasználónév / jelszó | |
| e-mail | e-mail | e-mail address: e-mail-cím |
| section (Exponential) / INI section / section of a help text, document or page | szakasz / blokk / rész | INI-blokk; Hide section: %title = Rész elrejtése: %title |
| object state / state / state group | objektumállapot / állapot / állapotcsoport | |
| status | állapot | order status: rendelés állapota |
| relation / related objects | kapcsolat / kapcsolódó objektumok | reverse related: erre hivatkozó objektumok |
| trash | Lomtár | as in Windows; move to trash: áthelyezés a Lomtárba |
| bookmark | könyvjelző | |
| sub-items / children / parent | alelemek / gyermekelemek / szülőelem | parent node: szülő csomópont |
| subtree | részfa | |
| owner / creator / modifier | tulajdonos / létrehozó / módosító | |
| created / modified | létrehozva / módosítva | column headings |
| layout (Exponential Layouts) | elrendezés | layout type: elrendezéstípus; the product name Exponential Layouts stays |
| layout (directory or release structure) | felépítés | |
| block / zone | blokk / zóna | |
| setting / settings | beállítás / beállítások | Layout / Settings: configuration is always beállítás |
| Setup (admin tab) / setup (installer) / setup wizard | Rendszer / telepítő / telepítővarázsló | tab name, keeps it apart from "Beállítások"; System as group label inside the Setup menu: Rendszerkezelés |
| configuration / configure | konfiguráció / konfigurálás | |
| Media library | Médiatár | |
| Dashboard | Irányítópult | |
| extension | bővítmény | |
| plugin | beépülő modul | |
| package | csomag | |
| module / view / function | modul / nézet / funkció | Module view: modulnézet |
| View (button) | Megtekintés | to look at something; the noun "view" of a module: nézet |
| search | keresés | search engine: keresőmotor; engine (storage, web server) alone: motor; the editor engine of the Online Editor: szerkesztőprogram |
| search index / reindex / index (verb) | keresési index / újraindexelés / indexelés | |
| index (DB) / index file | index / indexfájl | |
| cronjob / cronjob part | cron-feladat / cron-feladatrész | |
| schedule | ütemezés | |
| job / queue / worker / process | feladat / várólista / feldolgozó / folyamat | job queue: feladat-várólista; worker process: feldolgozófolyamat |
| task | feladat | |
| lane (queue) / retrieval lane (vector, keyword) | sáv / ág | |
| batch | köteg | |
| log / log file | napló / naplófájl | Log is never the verb "log in" |
| rotation / rotate (log file) | naplóváltás / váltás | rotated log: régi naplófájl |
| audit (feature, menu, audit trail) / audit (the act of recording) | auditnapló / naplózás | compounds: Auditnapló-riasztások, Auditnapló-archívumok |
| notification | értesítés | |
| collaboration | együttműködés | |
| preview | előnézet | |
| ID / remote ID / identifier | azonosító / távoli azonosító / azonosító | the short label "ID" may stay in narrow columns |
| file / folder / directory | fájl / mappa / könyvtár | |
| image / media | kép / média | |
| thumbnail | miniatűr | |
| upload / download | feltöltés / letöltés | |
| export / import | exportálás / importálás | the noun of a thing: export (PDF-export, RSS-export) |
| shop | webáruház | |
| Store (shop tab) | Áruház | every other Store is "Mentés" |
| basket | kosár | |
| order (shop) | rendelés | order status: rendelés állapota; order list: rendelések |
| order (sorting) | sorrend | sort order: rendezési sorrend |
| customer / product | vásárló / termék | |
| checkout | pénztár | |
| VAT / VAT type | áfa / áfatípus | lower case, as in Hungarian usage |
| discount / discount rule | kedvezmény / kedvezményszabály | |
| rule | szabály | |
| wish list | kívánságlista | |
| currency / price | pénznem / ár | |
| payment gateway / gateway | fizetési átjáró / átjáró | |
| newsletter | hírlevél | |
| subscriber / subscription / subscribe | feliratkozó / feliratkozás / feliratkozás | |
| tag (keyword, eztags) / keyword | címke / kulcsszó | |
| tag (HTML/XML) / custom tag | tag / egyéni tag | HTML-tag, XML-tag; never "címke" for markup |
| move / copy / swap | áthelyezés / másolás / csere | |
| move (in tree, into a group, to a status) | áthelyezés | |
| embedding (vector) / chunk / vector | vektoros beágyazás / szövegrészlet / vektor | |
| embed / embedded (object in text) | beágyazás / beágyazott | |
| prompt / model / token | utasítás / modell / token | system prompt: rendszerutasítás |
| AI | MI | mesterséges intelligencia, as in Hungarian Microsoft products |
| provider | szolgáltató | |
| deploy / deployment / release / rollback | üzembe helyezés / üzembe helyezés / kiadás / visszaállítás | release notes: kibocsátási megjegyzések |
| install / uninstall / installation | telepítés / eltávolítás / telepítés | |
| enable / disable | engedélyezés / letiltás | enabled / disabled: engedélyezve / letiltva |
| active / inactive | aktív / inaktív | |
| warning / error / notice | figyelmeztetés / hiba / megjegyzés | |
| required / optional | kötelező / nem kötelező | |
| default / priority | alapértelmezett / prioritás | |
| sort / ascending / descending | rendezés / növekvő / csökkenő | abbreviated (Asc. / Desc.): növ. / csökk. |
| visible / hidden | látható / rejtett | |
| hide / show / reveal | elrejtés / megjelenítés / felfedés | |
| expand / collapse | kibontás / összecsukás | |
| OK / Cancel | OK / Mégse | |
| Yes / No | Igen / Nem | |
| Back / Next / Previous / Close | Vissza / Tovább / Előző / Bezárás | Next page in a list: Következő |
| select / choose | kijelölés / kiválasztás | marking items in a list: kijelölés; picking one option: kiválasztás |
| select all / deselect all / select none | az összes kijelölése / kijelölés megszüntetése / kijelölés megszüntetése | |
| browse / path | tallózás / elérési út | |
| apply | alkalmazás | Apply changes: Módosítások alkalmazása |
| save / store | mentés | store as data storage in running text: tárolás |
| discard | elvetés | |
| edit | szerkesztés | |
| remove | eltávolítás | take out of a list, group or place; the item itself remains |
| delete | törlés | destroy; Remove formatting: Formázás törlése (nothing remains) |
| new / create / add | új / létrehozás / hozzáadás | Create new: Új létrehozása |
| refresh / update | frissítés / frissítés | update of a record in a sentence: módosítás |
| reset (settings) / reset (password) / reset (counter, attempts) | alaphelyzetbe állítás / visszaállítás / nullázás | reset a password: jelszó visszaállítása |
| restore | visszaállítás | |
| assign / assignment | hozzárendelés / hozzárendelés | |
| approve / approval / approved / approver | jóváhagyás / jóváhagyás / jóváhagyva / jóváhagyó | |
| deny / reject / rejected | megtagadás / elutasítás / elutasítva | |
| accept / accepted | elfogadás / elfogadva | |
| pending | függőben | "pending an external action": külső műveletre vár |
| archived (version, object, order) | archivált | |
| Archive / Unarchive (button) | Archiválás / Archiválás visszavonása | |
| archive (place, menu item, log archive) | archívum | |
| archive (file .tar/.tgz/.zip/.phar/.ezpkg) | archívumfájl | a packed file, not the archived version |
| run (verb) / run (noun) | futtatás / futás | cron run: cron-futás |
| dry run | próbafuttatás | |
| self-test | önteszt | |
| debug | hibakeresés | debug output: hibakeresési kimenet |
| statistics / system information | statisztika / rendszerinformáció | |
| database | adatbázis | |
| handler / wizard | kezelő / varázsló | |
| backend / endpoint | háttérrendszer / végpont | |
| method / listener / controller | metódus / figyelő / vezérlő | |
| allowlist / read-only / argument | engedélyezési lista / csak olvasható / argumentum | |
| slot / collection / facets / elevate | hely / gyűjtemény / szempontok / kiemelés | search facets: keresési szempontok; collection also for MongoDB and Layouts blocks (Dinamikus gyűjtemény, Kézi gyűjtemény); cache collections: gyorsítótár-csoportok |
| filter | szűrő | the action: szűrés |
| tick (checkbox) | bejelölés | "Jelölje be ..." in a sentence |
| container (content class, "Is container") | tároló | "Tároló" as a class flag |
| container (Bootstrap/CSS) | konténer | |
| locale / locale code | területi beállítás / területibeállítás-kód | where %locale names a language: "%locale nyelv" |
| localization | honosítás | |
| charset | karakterkészlet | |
| license | licenc | never for a permission |
| format / formatting | formátum / formázás | file or data format vs. text formatting |
| linked / common | csatolt / közös | "linked from" a page: hivatkozó; link a layout or zone: csatolás |
| flags (class flags) / flag (country) | jelzők / zászló | |
| teaser | bevezető | |
| slug | URL-név | |
| placeholder | helyőrző | |
| preset | előbeállítás | |
| information collection / information collector | információgyűjtés / információgyűjtő | collected information: gyűjtött információ; one submission: gyűjtés (pl. gyűjtések) |
| overview / summary | áttekintés / összegzés | |
| tab | lap | the keyboard key Tab stays Tab |
| rich text / rich text editor | formázott szöveg / formázottszöveg-szerkesztő | |
| feed | hírcsatorna | |
| session | munkamenet | |
| scope | hatókör | |
| thread (conversation, process) / chain | szál / lánc | beszélgetés only where the source itself speaks of a conversation |
| pool | készlet | |
| schema / registry / manifest | séma / nyilvántartás / jegyzékfájl | MANIFEST.txt and setting names stay |
| stylesheet / theme / icon | stíluslap / téma / ikon | |
| port | port | |
| digest | összesítő | |
| profile | profil | |
| core (Solr) | mag | Solr-mag |
| child documents (Solr) | gyermekdokumentumok | |
| editor (person) / editor (software) | szerkesztő / szerkesztő | the software, where it must be told apart: szerkesztőprogram |
| Editor (siteaccess type) | szerkesztői felület | |
| user interface | felhasználói felület | |
| page / pages | oldal / oldalak | |
| link | hivatkozás | |
| title / name / description | cím / név / leírás | |
| date / time | dátum / idő | |
| type | típus | |
| group | csoport | |
| list | lista | |
| value / field | érték / mező | |
| number / count | szám / darabszám | |
| width / height / size | szélesség / magasság / méret | |
| server / client | kiszolgáló / ügyfél | |
| request / response | kérés / válasz | |
| details | részletek | |
| context menu | helyi menü | |
| pattern | minta | |
| repository | adattár | |
| maintainer | karbantartó | |
| checksum | ellenőrzőösszeg | |
| quarantine | karantén | |
| mapping | leképezés | |
| resolve / resolvable / resolver | feloldás / feloldható / feloldó | path, name, realpath |
| checkpoint | ellenőrzőpont | |
| script | szkript | |
| revoke | visszavonás | key, token, session, access |
| label | felirat | the label of a field, button or block |
| kernel | kernel | |
| confirmation questions ("Delete …?", "Cancel this …?") | "Törli a(z) …?" / "Megszakítja ezt a(z) …?" | or "Biztosan törölni szeretné ...?" |

## 5. Decisions after the translation

These terms were fixed after the first complete translation. They take precedence over any older string in the
catalogue that still uses another form.

| English | Hungarian | Note |
|---|---|---|
| queued | várólistán / várólistás | status label Várólistán; attributive: várólistás üzenet |
| Repeat (version or workflow status) | Ismételt | |
| language mask / bit %bit | nyelvi maszk / %bit. bit | |
| cancel (workflow process, job) | megszakítás | "Megszakítja ezt a munkafolyamat-példányt?" |
| image alias | képálnév | the source's "image variation" may stay képváltozat |
| view cache | nézet-gyorsítótár | |
| fetch function / fetch alias / a fetch | lekérőfunkció / lekérési álnév / lekérés | not lekérőfüggvény |
| persistent object / persistent class | perzisztens objektum / perzisztens osztály | a persistent worker (PHP runtime) is állandó feldolgozó |
| syndication (RSS module, navigation part, datatype Syndicate) | tartalomterjesztés | Really Simple Syndication stays |
| audit sink | naplócél | Naplócélok |
| alert / severity / actor | riasztás / súlyosság / végrehajtó | |
| hash / hash chain | hash-érték / hash-lánc | not kivonat |
| fingerprint / redact / mask | ujjlenyomat / kitakarás (kitakarva) / maszkolás (maszkolva) | |
| pseudonymise / anonymise / consent | álnevesítés / anonimizálás / hozzájárulás | |
| purge (trash, objects, orders, cluster, audit) | végleges törlés | Végleges törlés a Lomtárból |
| purge (cached pages, cache tags) | törlés (a gyorsítótárból) | clearing a whole cache stays ürítés |
| commit (git, database, Solr) | véglegesítés | as in Visual Studio; code such as commit() stays |
| staging / production | átmeneti / éles | environment badges Átmeneti / Éles |
| mail transport (site.ini Transport) / transport (HTTP, stdio) | levélküldési mód / átvitel | |
| turn (chat, expai) | forduló | not kör |
| plan item / step / steer (expai) | tétel / lépés / irányítás | |
| staged (expai) | előkészített | |
| budget / retention | keret / megőrzés | |
| secret (stored value, setting) / secret key (site, form, OAuth client) | titkos érték / titkos kulcs | titkosérték-tároló; Client secret: Titkos ügyfélkulcs; not titok |
| option (choice in a datatype or product) / option (configuration, PHP, job) / command-line option | lehetőség / beállítás / kapcsoló | Multi-option: Többszörös lehetőség |
| preload / warm (caches) | előtöltés / előtölt | "a gyorsítótárak fel legyenek töltve" is fine in a sentence |
| hits / misses / hit rate | találat / tévesztés / találati arány | |
| broken link / link check | hibás hivatkozás / hivatkozás-ellenőrzés | |
| vendor (package) | szállító | |
| scan / orphan / finding / stub | vizsgálat / árva / megállapítás / csonk | |
| asset (images, files, media) / design assets (themes) | médiaelem / erőforrások | |
| markup / markup language | jelölőkód / jelölőnyelv | |
| node picker / content finder | csomópontválasztó / tartalomkereső | |
| landing page / front page / start page | nyitóoldal / címlap / kezdőlap | |
| watchdog | felügyelő | |
| issue (bug tracker) / issue (problem found) / pull request | hibajegy / probléma / lekéréses kérelem | |
| two-step sign-in / two-factor authentication | kétlépéses bejelentkezés / kétlépéses hitelesítés | |
| Redo / Undo | Mégis / Visszavonás | as in Microsoft Office |
| N/A | n. a. | |

- **Same term, two meanings**: decide by the context path, not by the English word. Section in
  `design/admin/section`, `role`, `content` is szakasz; in `settings`, `ini`, `menuconfig`, `rad` blokk; in help
  topics rész. Policy in `role`/`setup` is házirend; in `expai`, `privacy`, `security` szabályzat.
- **Setting names stay English** in code style (`[MailSettings] Transport`, `ManifestFile`, `Option[name]`); only
  the surrounding text uses the Hungarian term.
- **"Nincs jogosultsága"** remains correct for a missing permission in a sentence; the noun permission as an
  object of the permission system (role, policy, "%policy permission") is engedély.
