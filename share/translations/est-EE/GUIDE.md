# Estonian (est-EE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard written Estonian (eesti kirjakeel) as used in Estonia, following the current spelling norm (ÕS) and the
conventions of Estonian software localisation, in particular the Microsoft Estonian terminology and style guide.
No other locale falls back to this catalogue. The tone is neutral, concise and matter-of-fact. Avoid colloquial
forms, slang and needless English words; where Estonian IT has an established word (vahemälu, mall, sätted,
armatuurlaud), use it instead of the English one. Do not coin new words: if no established term exists, keep the
international word with Estonian inflection (atribuut, moodul, skript).

## 2. Form of address

- **Buttons, menu items, links and action labels** use the imperative singular (the short verb form), with the
  object after the verb: "Salvesta", "Kustuta", "Redigeeri", "Lisa", "Loobu", "Loo uus", "Rakenda muudatused",
  "Saada avaldamiseks", "Vali kõik", "Tühjenda vahemälu", "Eemalda valitud". Not the plural imperative ("Salvestage")
  and not the infinitive ("Salvestada") on a button.
- **Titles, column headings and tabs** are nouns or noun phrases: "Sisu struktuur", "Kasutajakontod",
  "Paigutuse tüüp", "Teatiste sätted", "Objektide olekud".
- **Sentences, hints, questions and error messages** address the user with the formal plural **teie**, written in
  lower case (teie, teid, teil, teie oma), and use the plural imperative for instructions:
  - "Kas soovite selle klassi kindlasti eemaldada?"
  - "Valige asukoht." (Please choose a location)
  - "Teil pole õigust klasse eemaldada."
  - "Klõpsake nuppu „Loobu“ ja proovige eemaldada ainult need asukohad, mida teil on lubatud eemaldada."
  - "Versiooniajaloo sätteid saate muuta failis content.ini ..."
  - Error: "Faili ei saanud üles laadida. Proovige uuesti või pöörduge saidi administraatori poole."
- **Tooltips that describe a click** put the purpose first: "Kontekstimenüü kuvamiseks klõpsake ikooni."
- **Status messages** are impersonal or passive: "Tsoon on kustutatud.", "Mustand on salvestatud.",
  "Cron-töö käivitusi ei leitud", "Indekseerimine on lõpetatud."
- "Palun" is used sparingly, only where the source insists on politeness ("Palun oodake ..."); the plural
  imperative is already polite.
- Not allowed: the familiar form (sina, sa, sinu, sind, soovid, saad), the singular imperative as an instruction
  inside a sentence ("Vali asukoht." in a message is wrong; write "Valige asukoht."), "Teie/Teil" with a capital
  letter inside a sentence, and the infinitive as an instruction ("Kasutada seda välja ..." is wrong; write
  "Seda välja kasutatakse ...").

## 3. Script and typography

- Latin script with the Estonian letters õ ä ö ü and, in loanwords, š ž. Sentence case: "Sisu struktuur",
  "Objektide olekud". No English title case.
- **Compounds** are written as one word: "kasutajarühm", "klassirühm", "objektiolek", "otsinguindeks",
  "logifail". A compound with an abbreviation, a code or a letter takes a hyphen: "URL-i alias", "PDF-fail",
  "cron-töö", "INI-fail", "XML-märgend", "e-posti aadress".
- **Abbreviations, codes and digits** take their case endings after a hyphen: "URL-i", "ID-ga", "PHP-st",
  "SQL-päring", "5-kohaline". A product name that ends like an Estonian word is inflected without a hyphen as it is
  pronounced: "Exponentiali teave", "Solri tuum".
- **Quotation marks** in running text: „…“ (U+201E, U+201C). Inside HTML attributes and JavaScript the straight
  quotes of the source stay.
- **Plural and number forms**: after a numeral Estonian uses the partitive singular ("5 objekti"), but "1 objekt"
  in the nominative, so "%count objekti" is wrong for 1. Never write "objekt(i)". Use a label form:
  "Objekte: %count", "Tööprotsesse: %count", "Eemaldatud mustandeid: %count." "%count objekti" is allowed only
  where the source makes clear the number is never 1.
- **Placeholders with case endings**: a placeholder must never be glued to a letter ("%namei" is a different
  placeholder). Preferred: a construction in which the placeholder stays in the nominative, usually after a
  declined noun and in quotes: "Klassi „%class_name“ koopia", "Klassi „%class“ pole olemas.", "sisestage kasutaja
  %login parool". Where an ending is unavoidable, separate it with a hyphen as for abbreviations ("%name-i",
  "%count-st"), but only if the stem is predictable (a code, a number, an abbreviation); for free names use the
  construction above.
- **Numbers**: digits 0-9; in prose the decimal comma ("1,5 MB") and a (non-breaking) space as thousands separator
  ("10 000"). Ordinals with a full stop ("3. versioon"). Time units after a number: "min", "h", "s";
  "%n min tagasi".
- **Dates** come from the locale through placeholders; written dates use "23.10.2026".
- **Punctuation**: no space before ":", ";", "?" and "!". The en dash with spaces – for a break in a sentence;
  the ellipsis as in the source.

## 4. Glossary

| English | Estonian | Note |
|---|---|---|
| content | sisu | |
| content object | sisuobjekt | |
| object | objekt | |
| item (list entry, general) | üksus | items: üksused |
| node | sõlm | main node: põhisõlm |
| location | asukoht | the place of an object in the tree |
| main location | põhiasukoht | |
| content class / class | sisuklass / klass | a PHP class: PHP-klass |
| class group | klassirühm | |
| attribute | atribuut | |
| datatype | andmetüüp | |
| version | versioon | version history: versiooniajalugu |
| draft | mustand | Store draft: Salvesta mustand; Discard draft: Hülga mustand |
| publish / published | avalda / avaldatud | |
| publishing | avaldamine | Send for publishing: Saada avaldamiseks |
| translation | tõlge | |
| language | keel | |
| main language | põhikeel | |
| content language | sisu keel | interface language: liidese keel |
| siteaccess | saidi juurdepääs | pl. saidi juurdepääsud; siteaccess settings: saidi juurdepääsu sätted |
| site / website | sait / veebisait | |
| design | kujundus | the Exponential design (a set of templates) |
| structure | struktuur | |
| template | mall | template file: mallifail |
| override | alistus | override template: alistusmall; overridden: alistatud |
| fallback | varu- | fallback language: varukeel; fallback chain: varuahel; fallback template: varumall |
| alias / URL alias | alias / URL-i alias | |
| wildcard (URL) | metamärk | the rule itself: metamärgiga URL (pl. metamärgiga URL-id) |
| redirect / forward | ümbersuunamine / edasisuunamine | verb: suuna ümber |
| cache | vahemälu | |
| clear cache | tühjenda vahemälu | |
| workflow | töövoog | |
| workflow event | töövoo sündmus | |
| trigger / event | päästik / sündmus | |
| role | roll | |
| policy | poliitika | pl. poliitikad |
| permission | õigus | pl. õigused; never "luba" |
| access / access denied | juurdepääs / juurdepääs keelatud | |
| limitation (policy) | piirang | |
| limit / time limit | piirang / ajapiirang | statement time limit: lause ajapiirang; not limiit |
| user / user group | kasutaja / kasutajarühm | |
| user account | kasutajakonto | user accounts: kasutajakontod |
| anonymous user | anonüümne kasutaja | |
| administrator | administraator | |
| admin interface | haldusliides | |
| user interface | kasutajaliides | |
| log in / log out | logi sisse / logi välja | in a sentence: logige sisse |
| login (field) / username | kasutajanimi | |
| password | parool | |
| e-mail | e-post | e-mail address: e-posti aadress; a message: meil (not e-kiri) |
| section | jaotis | the Exponential section of an object; an INI section is also jaotis (INI-jaotis, "[Section]": [Jaotis]); the context shows which |
| object state / state | objekti olek / olek | |
| state group | olekurühm | |
| relation | seos | |
| related objects | seotud objektid | |
| reverse related objects | sellele objektile viitavad objektid | |
| trash | prügikast | Empty trash: Tühjenda prügikast |
| bookmark | järjehoidja | |
| subtree | alampuu | |
| parent | ülemüksus | parent node: ülemsõlm |
| children / sub-items | alamüksused | |
| owner | omanik | |
| creator / author | looja / autor | |
| modifier | muutja | |
| created / modified | loodud / muudetud | column headings |
| layout (Exponential Layouts) | paigutus | layout type: paigutuse tüüp; the name Exponential Layouts stays; reserved for Layouts |
| page layout / pagelayout (classic frame template) | lehemall | not lehepaigutus, which collides with paigutus |
| layout (directory/release structure) | ülesehitus | |
| block / zone | plokk / tsoon | |
| setting / settings | säte / sätted | configuration in general |
| Setup (admin tab) | Häälestus | |
| configuration / configure | konfiguratsioon / konfigureeri | |
| Content structure | Sisu struktuur | |
| Media library | Meediumiteek | |
| Dashboard | Armatuurlaud | |
| extension | laiendus | |
| package | pakett | |
| module | moodul | |
| view (noun) | vaade | module view: mooduli vaade |
| View (button) | Kuva | |
| function | funktsioon | |
| search (verb / noun) | otsi / otsing | search engine: otsingumootor |
| search index | otsinguindeks | |
| index (verb) / reindex | indekseeri / indekseeri uuesti | noun: taasindekseerimine |
| index (database) | indeks | index file: indeksfail |
| cronjob / cronjob part | cron-töö / cron-töö osa | crontab stays crontab (crontab-fail) |
| schedule | ajakava | |
| job | töö | |
| queue | järjekord | job queue: tööde järjekord; lane: rada |
| worker | tööprotsess | |
| task | ülesanne | |
| log / log file | logi / logifail | always the noun; never the verb "log in" |
| rotation / rotate (log) | rotatsioon / roteeri | rotated log: roteeritud logi |
| audit / audit trail | audit / auditijälg | |
| notification | teatis | notification settings: teatiste sätted |
| collaboration | koostöö | |
| preview | eelvaade | |
| ID / remote ID | ID / kaug-ID | |
| identifier | identifikaator | class identifier: klassi identifikaator |
| file / folder / directory | fail / kaust / kataloog | |
| image / media | pilt / meedium | |
| thumbnail | pisipilt | |
| upload / download | laadi üles / laadi alla | noun: üleslaadimine / allalaadimine |
| export / import | ekspordi / impordi | noun: eksport / import (PDF-eksport, RSS-eksport) |
| shop | pood | |
| Store (shop tab) | Pood | the tab of the administration header |
| basket | ostukorv | |
| order (shop) | tellimus | order status: tellimuse olek |
| order (sorting) | järjestus | sort order: sortimisjärjestus |
| sort / ascending / descending | sordi / kasvav / kahanev | |
| customer / product | klient / toode | |
| checkout | ostu vormistamine | |
| VAT / VAT type | käibemaks / käibemaksu tüüp | short: KM |
| discount / discount rule | allahindlus / allahindlusreegel | |
| rule | reegel | |
| price / currency | hind / valuuta | |
| payment gateway / gateway | makselüüs / lüüs | |
| wish list | soovinimekiri | |
| newsletter | uudiskiri | |
| subscriber / subscribe / unsubscribe | tellija / telli / tühista tellimine | unsubscribe link: tellimise tühistamise link |
| subscription | tellimine | never "tellimus", which is the shop order |
| tag (keyword, eztags) | silt | pl. sildid |
| keyword | märksõna | |
| tag (HTML/XML) | märgend | custom tag: kohandatud märgend |
| label (of a field, button, block) | pealdis | silt is the eztags tag |
| move | teisalda | in the tree, into a group, to another status |
| copy / swap | kopeeri / vaheta | |
| embedding (vector) | vektoresitus | "embedded once" (vectors): vektoresitus luuakse |
| chunk / vector | tekstilõik / vektor | |
| embed / embedded (object in text) | manusta / manustatud | |
| prompt / model / token | viip / mudel / token | token (LLM) stays token |
| AI | tehisintellekt | short: TI; AI assistant: TI-assistent; AI suggestion: TI soovitus |
| deploy / deployment | juuruta / juurutus | |
| release | väljalase | |
| enable / disable | luba / keela | |
| enabled / disabled | lubatud / keelatud | |
| active / inactive | aktiivne / mitteaktiivne | |
| error / warning / notice | tõrge / hoiatus / teade | error message: tõrketeade |
| required / optional | kohustuslik / valikuline | |
| default | vaikimisi / vaike- | default value: vaikeväärtus |
| priority | prioriteet | |
| visible / hidden | nähtav / peidetud | |
| hide / show / reveal | peida / kuva / kuva | |
| expand / collapse | laienda / ahenda | |
| OK / Yes / No | OK / Jah / Ei | |
| Cancel | Loobu | |
| Back / Next / Previous | Tagasi / Edasi / Eelmine | in a page list: Järgmine / Eelmine |
| Close | Sule | |
| select / Select all | vali / Vali kõik | |
| Deselect all / Select none | Tühista valik | |
| tick (checkbox) | märgi | untick: tühjenda märkeruut |
| apply | rakenda | Apply changes: Rakenda muudatused |
| save / store | salvesta | every "Store" except the shop tab; a store for data (explanation store, secret store): hoidla |
| discard | hülga | |
| edit | redigeeri | |
| remove | eemalda | take out of a list, group or place; "kustuta" only where files are really destroyed |
| delete | kustuta | destroy |
| new / create / add | uus / loo / lisa | Create new: Loo uus |
| refresh / update | värskenda / uuenda | |
| reset | lähtesta | |
| restore | taasta | |
| assign / assignment | määra / määramine | |
| statistics | statistika | |
| system information | süsteemiteave | |
| database | andmebaas | |
| browse / path | sirvi / tee | |
| handler / wizard | töötleja / viisard | |
| backend / provider / endpoint | taustsüsteem / pakkuja / lõpp-punkt | |
| method / listener / controller | meetod / kuular / kontroller | |
| allowlist | lubamisloend | |
| read-only | kirjutuskaitstud | |
| argument | argument | |
| dry run | proovikäivitus | |
| self-test | enesetest | |
| run (noun: program, cron, index run) | käivitus | verb: käivita |
| debug | silumine | debug output: silumisväljund |
| slot / collection | pesa / kollektsioon | collection = Solr or MongoDB collection; kogum is a pool |
| facets / elevate | tahud / esiletõstmine | Solr |
| core (Solr) | tuum | Solri tuum |
| kernel | kernel | the Exponential kernel |
| child documents (Solr) | alamdokumendid | |
| filter | filter | verb: filtreeri |
| details | üksikasjad | |
| container (content class, "Is container") | konteiner | Is container: On konteiner |
| container (Bootstrap/CSS) | konteiner | |
| locale | lokaat | %locale may stand after "keeles" ("keeles %locale") |
| localization | lokaliseerimine | |
| approve / approval / approved | kinnita / kinnitamine / kinnitatud | approver: kinnitaja |
| deny / denied (approval request) | lükka tagasi / tagasi lükatud | never keela/keelatud, which is disable/disabled; Access/Permission denied: Juurdepääs keelatud; a count of refused preloads: Keeldutud: %count |
| reject / rejected | lükka tagasi / tagasi lükatud | |
| accept / accepted | nõustu / aktsepteeritud | |
| pending / queued | ootel / järjekorras | |
| archive (place, menu item, log archive) | arhiiv | |
| archived (version, object, order) | arhiivitud | |
| Archive / Unarchive (button) | Arhiveeri / Taasta arhiivist | |
| archive (file .tar, .zip, .phar, .ezpkg) | arhiivifail | |
| charset | märgistik | |
| license | litsents | |
| privilege (DB) / grant | privileeg / anna õigus | |
| format / formatting | vorming / vormindus | |
| linked / common | lingitud / ühine | |
| flags (class properties) | lipud | a command-line switch without value: lüliti |
| teaser | lühitutvustus | |
| slug | URL-i lühinimi | |
| placeholder | kohatäide | also {1} in a wildcard destination; never metamärk |
| preset | eelseadistus | |
| information collection / collector | teabe kogumine / teabe koguja | one submission: kogumiskirje (pl. kogumiskirjed) |
| overview / summary | ülevaade / kokkuvõte | |
| Desc. / Asc. | Kah. / Kasv. | |
| tab | vahekaart | tabs as whitespace: tabeldusmärgid |
| rich text | vormindatud tekst | |
| feed | voog | RSS export: RSS-eksport |
| session | seanss | |
| scope | ulatus | |
| thread | lõim | conversation and process thread |
| chain | ahel | |
| batch | pakk | in batches: pakkidena |
| schema / registry / manifest | skeem / register / manifest | |
| stylesheet / theme / icon | laaditabel / teema / ikoon | |
| port | port | |
| digest | koondteade | |
| profile | profiil | |
| editor (person) | toimetaja | |
| editor (software) | redaktor | template editor: mallide redaktor |
| Editor (siteaccess kind) | toimetaja | |
| page / pages | leht / lehed | |
| link | link | |
| name / title / description | nimi / pealkiri / kirjeldus | |
| date / time | kuupäev / kellaaeg | |
| status / type | olek / tüüp | |
| group / list | rühm / loend | |
| value / field | väärtus / väli | |
| number / count | number / arv | |
| width / height / size | laius / kõrgus / suurus | |
| server / client | server / klient | |
| request / response | päring / vastus | |
| pattern | muster | |
| repository | hoidla | |
| maintainer | hooldaja | |
| install / uninstall | installi / desinstalli | "installi" is never a noun |
| installation (the act) / installation (the installed system) | installimine / installatsioon | "selle installatsiooni" |
| checksum | kontrollsumma | |
| quarantine | karantiin | |
| pool | kogum | |
| mapping | vastendus | |
| resolve / resolver | lahenda / lahendaja | resolvable: lahendatav; Resolves to: Lahendub (… teeks, aadressiks, objektiks) |
| checkpoint | kontrollpunkt | |
| script | skript | |
| revoke | tühista | revoke a key or session: tühista võti, tühista seanss |
| plugin | lisandmoodul | |

## 5. Decisions after the translation

These decisions complete the glossary; where a term appears in both, the glossary row above is the current one.

| English | Estonian | Note |
|---|---|---|
| menu arrangement / menu positioning | menüükorraldus (title: Menüüde korraldus) / Menüüde asend | not paigutus, not menüüvariant |
| fetch function / fetch alias | toomisfunktsioon / toomisalias | |
| Sticky (forum topic) | Püsiteema | "Pasted image" stays Kleebitud pilt |
| Inbox (collaboration) | postkast | not sisendkaust |
| production / staging (environment) | tootmiskeskkond (tootmissait) / eelkeskkond | the badges DEV/TEST/STAGE/PROD stay codes |
| symlink / symbolic link | sümbolink | not nimeviit, not sümlink |
| credentials | juurdepääsuandmed | not mandaat |
| redact / redacted | varja / varjatud | log-viewer mask: maskitud / maskimata |
| scheduler | ajasti | |
| Pause / Resume / Paused / Stop | Pane pausile / Jätka / Pausil / Peata | |
| Repeat (status) / Repeats | Korduv / Kordused | |
| cache hits / misses / hit rate | tabamused / möödalasud / tabamusmäär | search hits (matches): vasted |
| alert / sink / actor / severity (audit) | häire / sihtkoht / tegija / raskusaste | |
| deprecations | iganemisteated | count label: Iganenud: %count |
| view mode | vaaterežiim | |
| orphan file / orphaned | orbfail / orvuks jäänud | |
| split (PDF pages) / split directory | tükeldamine / tükelduskataloog | |
| storage key / stub (OER import) | salvestusvõti / toorik | |
| viewer (document viewer) | vaatur | |
| commit (git) / commit (Solr) | sissekanne / kinnitamine | |
| replica / read replica / slave DB | koopiaserver / lugemiskoopia / alluv andmebaas | master DB: põhiserver; PHP-FPM master: põhiprotsess |
| escape / escaping (output) | varjesta / varjestamine | |
| engine (Velocity, PHP, database) | mootor | default engine: vaikemootor |
| autoloads | automaatlaadimisfailid | autoload arrays: automaatlaadimise massiivid |
| clipboard / maintenance mode | lõikelaud / hooldusrežiim | |
| header (HTTP, page) / footer | päis / jalus | the HTML head of the page layout: head-osa |
| Pin / Pinned / Unpinned | Kinnita ülaossa / Ülaossa kinnitatud / Ülaosast eemaldatud | |
| help topic / help center / audience | abiteema / abikeskus / sihtrühm | |
| object state as a mark of the object | tähis | |
| GitHub issue / pull request / fork | probleemiteade / tõmbetaotlus / hark | |
| asset (images, files, media) | vara (pl. varad) | |
| relative time "in %1" / "%1 ago" | %1 pärast / %1 tagasi | |

- Where one English word has two senses, the sense decides, not the frequency: section (Exponential section and
  INI section, both jaotis), installation (installimine / installatsioon), collection (kogumiskirje /
  kollektsioon), deny (lükka tagasi for an approval, juurdepääs keelatud for access).
- "keela / keelatud" belongs to disable / disabled and to access denied. A button that refuses an approval request
  is never "Keela".
- "paigutus" is reserved for Exponential Layouts; the classic page frame is lehemall, menu placement is asend or
  korraldus. "plokk" is the Layouts block and the template cache block, never an INI section.
