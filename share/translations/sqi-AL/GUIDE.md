# Albanian (sqi-AL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Albanian (gjuha standarde shqipe, based on the Tosk standard of the 1972 orthography congress) as written
in Albania and Kosovo. No other locale falls back to this catalogue. The tone is neutral, concise and professional,
as in the Albanian versions of Windows and Microsoft Office; the Microsoft Albanian terminology is the first
reference, the Albanian localisations of Mozilla and LibreOffice the second. Avoid Gheg or dialect forms, colloquial
words and needless English (write "skedar", not "fajll"; "cilësime", not "setingje"). An English IT word stays only
where Albanian software uses it itself (server, email, cache in brackets, token).

## 2. Form of address

- **Buttons, menu items, links and action labels** use the imperative singular, as the Microsoft Albanian
  interface does ("Ruaj", "Anulo", "Hap", "Mbyll"): "Ruaj", "Fshi", "Redakto", "Shto", "Anulo", "Krijo të ri",
  "Zbato ndryshimet", "Dërgo për publikim", "Përzgjidhi të gjitha", "Hiq të përzgjedhurat". This is the fixed
  form of a command label, not a way of addressing the user, and it is the same throughout the interface.
- **Titles, column headings and tabs** are nouns, normally in the definite form: "Struktura e përmbajtjes",
  "Llogaritë e përdoruesve", "Lloji i faqosjes", "Cilësimet e njoftimeve", "Emri", "Lloji", "Krijuar",
  "Modifikuar".
- **Sentences, hints, questions and error messages** address the user with the formal **ju** (2nd person plural),
  written in lower case (ju, juaj, ju lutemi), and the plural imperative for instructions:
  - "Dëshironi vërtet ta hiqni këtë klasë?"
  - "Zgjidhni një vendndodhje" (Please choose a location)
  - "Nuk keni leje për të hequr klasa."
  - "Klikoni butonin „Anulo“ dhe provoni të hiqni vetëm vendndodhjet që keni të drejtë t'i hiqni."
  - "Cilësimet e historikut të versioneve mund t'i ndryshoni te skedari content.ini ..."
  - Error: "Ndodhi një gabim gjatë ruajtjes automatike të skicës"
- **Tooltips that describe a click** use "Klikoni ... për të ...": "Klikoni ikonën për të shfaqur menynë
  kontekstuale."
- **Status messages** are impersonal, with the passive aorist ("u" + verb): "Njoftimi u hoq.", "Ndryshimet u
  ruajtën me sukses.", "Nuk u hoq asgjë."
- **Gender**: Albanian adjectives agree with the person addressed. Avoid "Jeni i sigurt / e sigurt ...?" and
  similar; write a verb construction instead ("Dëshironi vërtet ...?", "Konfirmoni ...").
- Not allowed: the familiar form (ti, yt, jote, do, mund, zgjidh as an instruction inside a sentence), "Ju/Juaj"
  with a capital letter inside a sentence, slashed gender forms ("i/e sigurt"), and the imperative singular as an
  instruction inside a sentence ("Përdor këtë fushë për ..." is wrong; write "Kjo fushë shërben për ..." or
  "Përdoreni këtë fushë për ...").

## 3. Script and typography

- Latin script with the Albanian letters ë and ç (and the digraphs dh, gj, ll, nj, rr, sh, th, xh, zh). Never
  replace ë by e or ç by c. Sentence case: "Struktura e përmbajtjes", "Gjendjet e objekteve".
- Quotation marks in running text: „…“. Inside HTML attributes and JavaScript the straight quotes of the source
  stay. The apostrophe of the short forms is the straight one as typed in Albanian software: "t'i", "s'ka".
- Albanian has two number forms (one, more than one), the source only "item(s)". Never write "element(e)" or
  "objekt(e)". Use a label form: "Objekte: %count", "Procese punuese: %count", "Skica të hequra: %count."
- A placeholder is never inflected. Albanian marks definiteness and case with suffixes ("objekti", "objektit"),
  and a suffix glued to a placeholder breaks it (`%namei`); hyphenated suffixes ("%name-i") are only usual after
  abbreviations and are not used here either. Put the declined noun in front and let the placeholder stand as an
  apposition: "Redakto cilësimin %setting", "Redakto politikën <%policy_name> për rolin <%role_name>",
  "I identifikuar si %login.", "nga %login, qasja në sajt %siteaccess".
- Abbreviations take their suffix with a hyphen in running text when written by hand: "URL-ja", "TVSH-ja",
  "ID-ja", "email-i"; product names do not decline: "me Exponential", "serveri Solr".
- Numbers come in through placeholders. Written digits stay 0-9; Albanian uses a decimal comma and a space or a
  dot as the thousands separator, but format codes and values from the system are not changed. Time units after a
  number: "min", "orë", "s", "ditë"; "%n min më parë".

## 4. Glossary

| English | Albanian | Note |
|---|---|---|
| content | përmbajtje | |
| content object | objekt përmbajtjeje | |
| object | objekt | pl. objekte |
| item (list entry, general) | element | pl. elemente; a shop product line: artikull |
| node | nyje | main node: nyja kryesore |
| location | vendndodhje | main location: vendndodhja kryesore |
| content class | klasë përmbajtjeje | |
| class | klasë | a PHP class: klasë PHP |
| class group | grup klasash | |
| attribute | atribut | |
| datatype | tip të dhënash | |
| version | version | version history: historiku i versioneve |
| draft | skicë | Store draft: Ruaj skicën; Discard draft: Hidh poshtë skicën |
| publish | publiko | |
| published / publishing | i publikuar / publikim | Send for publishing: Dërgo për publikim |
| translation | përkthim | |
| language | gjuhë | interface language: gjuha e ndërfaqes |
| main language | gjuha kryesore | |
| content language | gjuha e përmbajtjes | |
| siteaccess | qasje në sajt | pl. qasje në sajt; definite: qasja në sajt; admin siteaccess: qasje administrimi në sajt; Current siteaccess: Qasja aktuale në sajt |
| site / website | sajt | in running text also "faqe interneti" |
| design (eZ design) | dizajn | |
| structure | strukturë | |
| template | shabllon | |
| override | mbivendosje | override template: shabllon mbivendosës; overridden: i mbivendosur |
| fallback | rezervë | fallback language: gjuhë rezervë; fallback chain: zinxhir rezervë |
| alias / URL alias | pseudonim / pseudonim URL | never for an image alias (section 5) |
| wildcard (URL) | karakter i përgjithshëm | wildcard alias: pseudonim URL me karakter të përgjithshëm; New URL wildcard: Karakter i ri i përgjithshëm URL |
| redirect / forward (URL) | ridrejto, ridrejtim / përcill, përcjellje | redirected: ridrejtohen |
| cache | memorie e përkohshme | Clear cache: Pastro memorien e përkohshme |
| workflow | rrjedhë pune | pl. rrjedha pune |
| workflow event / event | ngjarje e rrjedhës së punës / ngjarje | |
| trigger | shkrehës | |
| role | rol | |
| policy | politikë | pl. politika |
| permission | leje | |
| access / access denied | qasje / qasja u refuzua | |
| limitation | kufizim | |
| user | përdorues | |
| user group | grup përdoruesish | |
| user account | llogari përdoruesi | user accounts: llogaritë e përdoruesve |
| anonymous user | përdorues anonim | |
| administrator | administrator | |
| admin interface | ndërfaqja e administrimit | |
| log in / log out | identifikohu / dil | the noun: identifikimi / dalja |
| login (field) / username | emri i përdoruesit | |
| password | fjalëkalim | |
| e-mail | email | declined with a hyphen: email-i, email-it, email-et |
| section | seksion / bllok | the Exponential section of an object: seksion; an INI section [Name]: bllok |
| object state / state | gjendje objekti / gjendje | |
| state group | grup gjendjesh | |
| relation | ndërlidhje | |
| related objects | objekte të ndërlidhura | reverse related: objekte që lidhen me këtë objekt |
| link | lidhje | |
| trash | kosh | Empty trash: Zbraz koshin |
| bookmark | faqeshënues | pl. faqeshënues |
| subtree | nënpemë | |
| parent | prind | parent node: nyja prind |
| sub-items / children | nënelemente | child process: proces bir; Solr child documents: dokumente bijë |
| owner | pronar | |
| creator | krijues | |
| modifier | modifikues | last modifier: modifikuesi i fundit |
| created / modified | krijuar / modifikuar | column headings |
| layout (Exponential Layouts) | faqosje | layout type: lloji i faqosjes; the name Exponential Layouts stays |
| layout (directory / release structure) | strukturë | |
| block / zone | bllok / zonë | |
| setting / settings | cilësim / cilësime | the Settings page: Cilësimet |
| Setup (admin tab) | Konfigurimi | |
| configuration / configure | konfigurim / konfiguro | |
| Content structure | Struktura e përmbajtjes | |
| Media library | Biblioteka e medias | |
| Dashboard | Paneli | |
| extension | zgjerim | pl. zgjerime |
| plugin | shtojcë | |
| package | paketë | |
| module | modul | |
| view (noun, module view) | pamje | Module view: pamja e modulit |
| View (button) | Shiko | |
| function (module/policy) | funksion | |
| search (verb / noun) | kërko / kërkim | search engine: motor kërkimi |
| search index | indeks kërkimi | |
| index (verb) / reindex | indekso / riindekso | the noun: indeksim / riindeksim |
| index (DB) / index file | indeks / skedar indeksi | |
| cronjob | punë cron | |
| schedule | planifiko | the noun: orar |
| job | punë | |
| queue | radhë | job queue: radhë punësh |
| worker | proces punues | %count worker processes: Procese punuese: %count |
| task | detyrë | |
| run (noun) | ekzekutim | Run (button): Ekzekuto |
| log | regjistër | always a log file, never "log in" |
| log file | skedar regjistri | |
| rotation / rotate | rrotullim / rrotullo | rotated log: regjistër i rrotulluar |
| audit / audit trail | auditim / gjurmë auditimi | |
| notification | njoftim | |
| notice | shënim | |
| collaboration | bashkëpunim | |
| preview | pamje paraprake | |
| ID / remote ID | ID / ID e largët | |
| identifier | identifikues | |
| file | skedar | |
| folder | dosje | |
| directory | direktori | |
| path | shteg | |
| image | imazh | |
| media | media | |
| thumbnail | miniaturë | Thumbnail view: Pamja me miniatura |
| upload / download | ngarko / shkarko | |
| export / import | eksporto / importo | |
| move | zhvendos | |
| copy | kopjo | |
| swap | shkëmbe | |
| shop | dyqan | |
| Store (shop tab) | Dyqani | every other "Store" is the verb "Ruaj" |
| basket | shportë | |
| order (shop) | porosi | order status: statusi i porosisë |
| order (sorting) | renditje | sort order: rendi i renditjes |
| customer | klient | |
| product | produkt | |
| checkout | përfundimi i blerjes | |
| VAT / VAT type | TVSH / lloj TVSH-je | |
| discount / discount rule | zbritje / rregull zbritjeje | |
| rule | rregull | |
| price / currency | çmim / monedhë | |
| wish list | listë dëshirash | |
| payment gateway | portë pagese | |
| newsletter | buletin | |
| subscription / subscribe | abonim / abonohu | |
| subscriber | abonent | |
| tag (keyword, eztags) | etiketë | pl. etiketa |
| keywords | fjalë kyçe | |
| tag (HTML/XML) | etiketë HTML / etiketë XML | always with HTML/XML; custom tag: etiketë e personalizuar |
| label | emërtim | the caption of a field, button or block |
| embedding (vector) | përfaqësim vektorial | |
| chunk / vector | copë / vektor | |
| embed / embedded (object in text) | ngulit / i ngulitur | embedding content into another framework: ngulitje |
| prompt | udhëzim | the model's input text |
| model / token | model / token | |
| AI | IA | inteligjencë artificiale |
| deploy / deployment | vendos / vendosje | |
| release (deploy) | lëshim | |
| enable / disable | aktivizo / çaktivizo | |
| enabled / disabled | i aktivizuar / i çaktivizuar | |
| active / inactive | aktiv / joaktiv | |
| warning | paralajmërim | |
| error | gabim | |
| required / optional | i detyrueshëm / opsional | |
| default | i parazgjedhur | as a noun: parazgjedhje |
| priority | përparësi | |
| sort / ascending / descending | rendit / në rritje / në zbritje | Asc. / Desc.: Rr. / Zbr. |
| visible / hidden | i dukshëm / i fshehur | |
| hide / reveal / show | fshih / shfaq / shfaq | |
| expand / collapse | zgjero / palos | Fold/Unfold: Palos/Shpalos |
| OK / Cancel | OK / Anulo | |
| Yes / No | Po / Jo | |
| Back / Next / Previous | Prapa / Tjetri / I mëparshmi | wizard: Prapa / Para |
| Close | Mbyll | |
| select / browse | përzgjidh / shfleto | in a sentence: zgjidhni |
| select all / deselect all | Përzgjidhi të gjitha / Hiq përzgjedhjen nga të gjitha | Select none: Asnjë |
| tick (checkbox) | shëno | |
| apply | zbato | Apply changes: Zbato ndryshimet |
| save / store | ruaj | |
| discard | hidh poshtë | |
| edit | redakto | |
| remove | hiq | take out of a list, group or place; the button Remove is always Hiq, Fshi only where the text destroys data (Remove existing data in the setup wizard) |
| delete | fshi | destroy |
| new / create / add | i ri / krijo / shto | Create new: Krijo të ri |
| restore | rikthe | |
| assign / assignment | cakto / caktim | |
| approve / approval / approved | mirato / miratim / i miratuar | awaiting approval: pret miratim; was approved: u miratua |
| approver | miratues | |
| deny / reject / rejected | moho / refuzo / i refuzuar | |
| accept / accepted | prano / i pranuar | |
| pending | në pritje | pending list: lista e elementeve në pritje |
| archive (place, menu, log archive) | arkiv | |
| archived (version, object, order) | i arkivuar | |
| Archive / Unarchive (button) | Arkivo / Hiq nga arkivi | |
| archive (file .tar/.zip/.phar/.ezpkg) | skedar arkivi | |
| refresh / update / reset | rifresko / përditëso / rivendos | update is never upgrade (section 5) |
| run (verb) | ekzekuto | |
| dry run | ekzekutim provë | |
| self-test | vetëtestim | |
| debug | korrigjim | debug output: dalja e korrigjimit |
| statistics | statistika | |
| system information | informacione të sistemit | |
| database | bazë të dhënash | |
| handler | trajtues | |
| wizard | magjistar | |
| backend | sistem në prapavijë | |
| provider | ofrues | scope provider: ofrues shtrirjesh |
| endpoint | pikë fundore | |
| method / listener / controller | metodë / dëgjues / kontrollues | |
| allowlist | listë e lejuar | |
| read-only | vetëm për lexim | |
| argument | argument | |
| slot / collection | vend / koleksion | |
| facets / elevate (Solr) | aspekte / ngritje | |
| core (Solr) | bërthamë Solr | |
| kernel | kerneli | the CMS kernel |
| filter / details | filtër / detaje | |
| container (content class, Bootstrap/CSS) | kontejner | Is container class: Është klasë kontejner |
| locale / locale code | cilësime rajonale / kod cilësimesh rajonale | also the list in setup/languages (Cilësimet rajonale); the setting key Locale stays |
| localization | lokalizim | |
| charset | grup karakteresh | definite: grupi i karaktereve; pl. grupe karakteresh |
| license | licencë | never mixed with permission (leje) |
| privilege / grant (DB) | privilegj / jep privilegj | |
| format / formatting | format / formatim | |
| linked / common | i lidhur / i përbashkët | |
| flags | shenja / flamuj / flamur | class flags: Shenjat; flags of languages: flamujt; a boolean option flag: flamur |
| teaser | përmbledhje hyrëse | |
| slug | segment URL-je | |
| placeholder | vendmbajtës | |
| preset | paracaktim | |
| information collection / collector | mbledhje informacioni / mbledhës informacioni | collected information: informacione të mbledhura |
| overview / summary | pasqyrë / përmbledhje | |
| tab | skedë | the toggler of a row of tabs: çelësi |
| rich text | tekst i pasur | |
| feed | burim | only RSS/ATOM feeds; RSS feed: burim RSS |
| session | seancë | |
| scope | shtrirje | |
| thread | fill / fije | AI conversation thread: fill (pl. fije); OS, PHP or database thread: fije |
| chain | zinxhir | |
| batch | grup | in a batch: në grup |
| schema / manifest | skemë / manifest | |
| registry | regjistër komponentësh | kept apart from log |
| stylesheet / theme / icon | fletë stilesh / temë / ikonë | |
| port | port | |
| digest | përmbledhje periodike | |
| profile | profil | |
| lane | korsi | vector lane: korsia vektoriale |
| editor (person) | redaktor | |
| editor (software) | redaktues | template editor: redaktuesi i shablloneve |
| Editor (siteaccess kind) | Editor | stays as the name of the kind |
| user interface | ndërfaqe përdoruesi | |
| page / pages | faqe | |
| title / name / description | titull / emër / përshkrim | |
| date / time | datë / orë | |
| status | status | kept apart from state (gjendje) |
| type | lloj | |
| group | grup | |
| list | listë | |
| value / field | vlerë / fushë | |
| number / count | numër / sasi | |
| width / height / size | gjerësi / lartësi / madhësi | |
| server / client | server / klient | |
| request / response | kërkesë / përgjigje | |
| pattern | mostër | Object name pattern: Mostra e emrit të objektit |
| repository | depo | |
| maintainer | mirëmbajtës | |
| install / uninstall / installation | instalo / çinstalo / instalim | |
| checksum | shumë kontrolli | |
| quarantine | karantinë | |
| pool | grumbull | pool of processes: grumbull procesesh |
| mapping | hartëzim | |
| resolver / resolve (path, name) | zgjidhës / përcakto | resolvable: i përcaktueshëm |
| checkpoint | pikë kontrolli | |
| script | skript | |
| revoke | revoko | revoked: i revokuar |
| confirmation questions ("Remove …?", "Delete …?") | "Dëshironi vërtet ta hiqni …?" / "Dëshironi vërtet ta fshini …?" | never "Jeni i sigurt …?" |

## 5. Decisions after the translation

These terms were settled after the first translation. They complete the glossary in section 4.

| English | Albanian | Note |
|---|---|---|
| query | pyetësor / pyetje | SQL and database queries: pyetësor (memoria e përkohshme e pyetësorëve, regjistri i pyetësorëve të ngadaltë); search, Solr and kNN queries: pyetje (pyetje kërkimi, pyetja te Solr) |
| upgrade | përmirëso / përmirësim | upgrading PHP or Exponential; never përditësim, which is update |
| entry (cache, list, INI, log) | hyrje | pl. hyrje; dead entries: hyrje të vdekura; never regjistrim |
| record (data row) | regjistrim | audit records may be të dhëna |
| image alias | variant imazhi | pl. variante imazhesh; never pseudonim |
| render | prodho / shfaq | the server builds a page or output: prodhohet, riprodhohet, pamjet e prodhuara; shown in a page or by a template: shfaq |
| claim (cache-entry lock) | rezervim | Gives up the claim: Heq dorë nga rezervimi; never kërkesë, which is a request |
| validate / authenticate | vleftëso, vleftësim / vërteto identitetin, vërtetim | Validation failed: Vleftësimi dështoi; authenticator app: aplikacion vërtetimi |
| purge | spastro / spastrim | Purge from the trash: Spastro nga koshi; the purge cronjob: puna cron e spastrimit; to clear is pastro |
| prune / compress | krasit / ngjesh | |
| lock | kyçje / bllokim | edit lock on a class, object or export: i kyçur, shkyç; an account locked after failed sign-ins: i bllokuar, zhblloko; database row and table locks: bllokim; mutex file: skedar bllokimi |
| mode | modalitet | maintenance, edit, SQL, import, deployment, worker and file mode (modaliteti i redaktimit, Modaliteti SQL); the view mode of a template (full, line, embed): mënyra e shfaqjes |
| checkbox | kuti zgjedhjeje | pl. kutitë e zgjedhjes; the datatype Checkbox: Kuti zgjedhjeje |
| drop-down | rënës | Listë rënëse, Pemë rënëse, Menyja rënëse |
| toggle | aktivizo/çaktivizo / shfaq/fshih / përmbys | on and off: Aktivizo/çaktivizo; visibility: Shfaq/fshih; Toggle selection: Përmbys përzgjedhjen; Toggle all: Përmbys të gjitha |
| asset | element media / skedar statik | content check of images, files and media: element media; design assets of a theme (CSS, JS, fonts, images): skedarë statikë; never burim, which is a feed |
| destination | destinacion | alias, export and audit sink; target file format: formati i synuar |
| fetch | fetch / merr | template fetch function and alias: funksion fetch, pseudonim fetch; to fetch data: merr |
| hit | goditje / rezultat | cache hit, hit rate: goditje, shkalla e goditjeve; search hits: rezultate |
| stale | i vjetruar | |
| key | çelës / tast | API key, primary key, INI key: çelës; keyboard key: tast |
| secret / masked, redacted | sekret / i maskuar | Unredacted: Pa maskim |
| command line / console | rreshti i komandave / konsolë | ./console and CLI stay in commands and names |
| available | i disponueshëm / në dispozicion | the status of an object: i disponueshëm; offered, at hand: në dispozicion |
| backup | kopje rezervë | |
| poll / feedback | sondazh / komente | |
| drag / grip / arrange | tërhiq / dorezë / rregullo | |
| Landscape / Portrait | Horizontalisht / Vertikalisht | |
| Repeat / Queued | Përsëritje / Në radhë | |
