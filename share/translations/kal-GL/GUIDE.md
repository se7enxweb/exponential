# Greenlandic (kal-GL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard West Greenlandic (Kalaallisut) in the orthography of 1973, as used by Naalakkersuisut, Oqaasileriffik
(the Greenland Language Secretariat), KNR and the Greenlandic versions of public websites. No other locale falls
back to this catalogue. The tone is neutral, short and factual. Avoid dialect forms, the pre-1973 spelling (ĸ,
accented vowels, "-ss-" written as "-ʃ-") and needless English words.

Greenlandic has no long tradition of software terminology. The order of preference for a term is:

1. an established Greenlandic word (Oqaasileriffik term lists, public administration): "toqqoruk", "peeruk",
   "atuisoq", "isissutissaq", "nittartagaq";
2. a Danish loan word adapted to Greenlandic spelling and inflection, where no established word exists:
   "fiili", "mappi", "klassi", "objekti", "versioni", "skabeloni", "moduli", "serveri";
3. the English or international form only for codes, product names and abbreviations (URL, HTML, PHP, ID, OK,
   Solr, cron, cache, AI, e-mail).

Do not coin new derivations where 1. or 2. gives a usable word.

## 2. Form of address

The user is addressed in the **singular** (illit), everywhere. Greenlandic has no formal/informal distinction
between "you" forms; the plural (ilissi) addresses several people and is not used for the single reader of the
interface. Politeness comes from wording, not from a pronoun.

- **Buttons, menu items, links and action labels** use the transitive imperative singular ("do it"), with the
  object first where there is one: "Toqqoruk" (Save), "Peeruk" (Delete), "Aaqqissoruk" (Edit), "Ilaguk" (Add),
  "Taamaatiguk" (Cancel), "Pilersiguk" (Create), "Saqqummiuguk" (Publish), "Missingiut toqqoruk" (Store draft),
  "Missingiut igiguk" (Discard draft), "Eqqaavik saliguk" (Empty trash), "Tamaasa toqqakkit" (Select all).
  With a plural object the ending is -kkit: "Toqqakkat piiakkit" (Remove selected), "Paasissutissat
  saqqummiukkit" (Publish data). Intransitive actions take the intransitive imperative: "Iserit" (Log in),
  "Anigit" (Logout), "Uterit" (Back), "Ujarlerit" (Search).
- **Titles, column headings and tabs** are nouns or noun phrases: "Aaqqissuussinerit" (Settings), "Nutserinerit"
  (Translations), "Oqaatsit pingaarnersaat" (Main language), "Objektip qanoq innera" (Object state),
  "Pilersinneqarfia" (Created), "Allanngortinneqarfia" (Modified).
- **Requests and instructions** use the imperative singular in a full sentence. A softer request may use
  -niar- ("Objektip »%class« tassannga pilersinneqartussap inissisimaffissaa toqqarniaruk."). Do not add
  "please" words of other languages.
  - "Inissisimaffimmik toqqaagit" (Please choose a location)
  - "E-mail-imut najugaq atorsinnaasoq allaguk." (Please enter a valid email address.)
  - "Parnaarneqarnera atorunnaarnissaa utaqqiuk misileqqillugulu." (Wait until the lock expires and try again.)
- **Questions and confirmations** use the interrogative mood, 2nd person singular:
  - "Qanoq innera una peerusuppiuk?" (Remove this state?)
  - "Oqaatsit uku piiarusuppigit?" (Remove these languages?)
  - "Missingiutit tamaasa piiarusuppigit?" (Are you sure you want to remove all drafts?)
- **Error messages** state the fact; where they speak of the user, 2nd person singular:
  - "Klassinik piiaanissamut akuersissuteqanngilatit." (You do not have permission to remove classes.)
  - "Fiili nassaarineqanngilaq." (The file could not be found.)
- **Status messages** are passive or impersonal: "Sumiiffik peerneqarpoq." (Zone deleted.), "Allannguutit
  iluatsilluni toqqorneqarput." (Changes were stored successfully.), "Cron-ip suliassaanik ingerlatsisoqanngilaq"
  (No cronjob run was found).
- **Tooltips that describe a click** use the imperative with -niarukku ("Ikoni tooruk menu attuumassutilik
  takutinniarukku." – Click on the icon to display a context-sensitive menu.) or the contemporative toorlugu
  ("Allattugaatit taakku ilaat toorlugu qaffasissutsit qulliit taarseraakkit." – Switch top levels by clicking
  one of these items.).
- Not allowed: the plural "ilissi" and its verb forms (-si, -tsi) addressing the reader; noun forms on buttons
  ("Toqqorneq" for Save); Danish or English sentences; the old orthography.

## 3. Script and typography

- **Script**: Latin, with the letters of the 1973 orthography (a e i o u, f g j k l m n p q r s t v, and in loan
  words b c d h w x y z æ ø å as in the Danish source word). No ĸ, no accents. Write e/o only before q and r
  (and in loan words): "eqqaavik", "toqqoruk".
- **Capitals**: sentence case. Only the first word of a button, title or heading, and proper names, start with a
  capital: "Imarisat aaqqissugaanerat", "Objektip qanoq innera". No English title case.
- **Quotation marks**: »…« (U+00BB, U+00AB) in running text, as in Greenlandic and Danish print: »%name«.
  Inside HTML attributes and JavaScript the straight quotes of the source stay.
- **Endings on foreign words, codes and placeholders** are joined with a hyphen, as Greenlandic spelling does
  for abbreviations and unadapted foreign words: "URL-imik", "PHP-mi", "cache-imik", "Solr-imi", "e-mail-imut".
  A placeholder is never glued to an ending: `%name-mik`, `%count-nik`. Where the hyphen reads badly, let the
  placeholder stand free with a colon: "Objekti: %objectname".
- **Plural and number forms**: Greenlandic marks singular and plural, and after a number the noun usually stands
  in the plural. The source has "item(s)"; never write "objekti(t)". Use a label form: "Objektit: %count",
  "Suliassat: %count", "Objektit: %count, pigiinnarneqartussat".
- **Numbers**: digits 0–9; Danish conventions: decimal comma ("1,5 MB"), thousands with a full stop or a thin
  space ("100.000"), dates day-month-year ("23-10-2026"), time with a colon ("14:30"). Time units after a
  number: "min", "t" (hours, tiimi), "s": "%n min matuma siornagut".
- **Dashes**: the en dash with spaces – for a break in a sentence.
- Long Greenlandic words are normal; do not break them with hyphens to save space. If a button label grows too
  long, choose a shorter verb rather than an abbreviation.

## 4. Glossary

Binding for the kernel and every extension. Verbs are given as the button form (imperative singular, transitive
where the action has an object); the 3rd person form in the note is the dictionary form.

| English | Greenlandic (Kalaallisut) | Note |
|---|---|---|
| content | imarisat | "imai" = its content; content structure: imarisat aaqqissugaanerat |
| content object / object | objekti | Danish loan; pl. objektit; "imarisat objektiat" only where the contrast matters |
| item (list entry, general) | allattugaq | pl. allattugaatit; sub-items: ataaniittut |
| node | inissisimaffik | in Exponential node and location are the same place in the tree |
| location / main location | inissisimaffik / inissisimaffik pingaarneq | |
| content class / class | imarisat klassiat / klassi | a PHP class: PHP-klassi |
| class group | klassit eqimattaat | |
| attribute | attributi | pl. attributit |
| datatype | paasissutissap suussusaa | short: datatypi only in technical lists |
| version | versioni | pl. versionit |
| draft | missingiut | Store draft: Missingiut toqqoruk; Discard draft: Missingiut igiguk |
| publish / published / publishing | saqqummiuguk / saqqummiunneqarsimasoq / saqqummiussineq | saqqummiuppaa; Send for publishing: Saqqummiussassanngorlugu nassiuguk |
| translation / translate | nutserneq / nutseruk | nutserpaa; pl. nutserinerit |
| language | oqaatsit | always plural in Greenlandic |
| main language | oqaatsit pingaarnersaat | |
| content language / interface language | imarisat oqaasii / atuisup saqqaata oqaasii | |
| siteaccess | nittartakkamut isaariaq | pl. nittartakkamut isaariaatit |
| site / website | nittartagaq | |
| design (Exponential design) | ilusilersuut | |
| structure | aaqqissugaaneq | |
| template | skabeloni | Danish skabelon; template file: skabelonip fiilia |
| override / overridden / override template | taarsiut / taarserneqarsimasoq / taarsiutaasoq skabeloni | taarserpaa: replaces it |
| fallback / fallback language | uterfissaq / oqaatsit uterfissat | fallback chain: uterfissat tulleriiaarnerat |
| alias / URL alias | aliasi / URL-alias | |
| wildcard (URL) | wildcard | the name of the URL wildcard function; in a sentence: URL-imi wildcard; with endings wildcard-imik, pl. wildcard-it |
| redirect / forward (URL) | allamut ingerlatitsineq / allamut ingerlatiguk | |
| cache / clear cache | cache / cache saliguk | salippaa: cleans it; with endings cache-imik |
| workflow | suliap ingerlanera | pl. suliap ingerlaneri |
| workflow event / event | suliap ingerlanerani pisoq / pisoq | |
| trigger | aallarnisaat | |
| role | atuuffik | pl. atuuffiit |
| policy | maleruagassaq | pl. maleruagassat |
| permission / access | akuersissut / iserfigisinnaaneq | access denied: iserfigisinnaanngilat |
| limitation (policy) | killilersuut | killilersorpaa: restricts it |
| user | atuisoq | pl. atuisut |
| user group | atuisut eqimattaat | |
| user account | atuisup kontoa | pl. atuisut kontoi |
| anonymous user | atuisoq ilisimaneqanngitsoq | |
| administrator / admin interface | aqutsisoq / aqutsinermut saqqaq | |
| user interface | atuisup saqqaa | |
| log in / log out | iserit / anigit | isertoq: logged in |
| login (field) / username | atuisup aqqa | |
| password | isissutissaq | |
| e-mail | e-mail | with endings e-mail-imut; e-mail address: e-mail-imut najugaq |
| section | immikkoortoq | always the Exponential section |
| object state / state | objektip qanoq innera / qanoq innera | state group: qanoq innerit eqimattaat |
| relation / related objects | attaveqaat / objektit attaveqartut | |
| reverse related | objektit uunga innersuussisut | objects that refer to this object |
| trash | eqqaavik | Empty trash: Eqqaavik saliguk |
| bookmark | nalunaaqutaq | pl. nalunaaqutat |
| sub-items / children | ataaniittut | |
| parent | qulaaniittoq | |
| subtree | ataaniittut tamarmik | |
| owner | piginnittoq | |
| creator / modifier | pilersitsisoq / allanngortitsisoq | |
| created / modified | pilersinneqarfia / allanngortinneqarfia | column headings |
| layout (Exponential Layouts) | inissiineq | layout type: inissiinerup suussusaa; the name Exponential Layouts stays |
| block / zone | blokki / sumiiffik | |
| setting / settings | aaqqissuussineq / aaqqissuussinerit | configuration, not the page layout |
| Setup (admin tab) | Aaqqissuussineq | |
| configuration / configure | aaqqissuussineq / aaqqissuuguk | aaqqissuuppaa |
| Content structure | Imarisat aaqqissugaanerat | |
| Media library | Mediat katersugaat | |
| Dashboard | Aqutsivik | |
| overview | takussutissiaq | |
| summary / digest | eqikkaaneq | |
| extension | ilassut | pl. ilassutit |
| plugin | ilanngussaq | |
| package | poortugaq | pl. poortukkat |
| module | moduli | pl. modulit |
| view (module view, noun) | takutitsineq | |
| view (button) | takuuk | takuvaa: sees it |
| function (module function) | atortussaq | a programming function: funktioni |
| search (verb / noun) | ujarlerit / ujarlerneq | search engine: ujarlerfik |
| search index / index (verb) / reindex | ujarlernermut indeksi / indeksiliuguk / indeksiliuteqqinneq | |
| index (database) | indeksi | |
| cronjob | cron-ip suliassaa | pl. cron-ip suliassai |
| schedule | pilersaarut | |
| job / task | suliassaq | pl. suliassat |
| queue / job queue | utaqqiffik / suliassat utaqqiffiat | |
| worker | suliaqartoq | pl. suliaqartut |
| lane (queue) | utaqqiffiup aqqutaa | |
| log / log file | nalunaarsuiffik / nalunaarsuiffiup fiilia | never the verb "log in" |
| audit / audit trail | misissuineq / misissuinermut malunnaatit | |
| rotation (log) / rotate | kaajallaaneq / kaajallaaguk | |
| notification | nalunaarut | pl. nalunaarutit |
| collaboration | suleqatigiinneq | |
| preview | siumut takuneq | Preview (button): Siumut takuuk |
| ID / remote ID | ID / ungasianit ID | |
| identifier | ilisarnaat | pl. ilisarnaatit |
| file | fiili | pl. fiilit |
| folder / directory | mappi | pl. mappit |
| image | assi | pl. assit |
| media | mediat | |
| thumbnail | assikinnguaq | pl. assikinnguit |
| upload / download | nassiuguk / aaguk | nassiuppaa; aavaa: fetches it |
| shop | pisiniarfik | Store (shop tab of the header): Pisiniarfik; every other Store is toqqoruk |
| basket | kurvi | Danish kurv |
| order (shop) | inniminniineq | pl. inniminniinerit; order status: inniminniinerup qanoq innera |
| order (sorting) / sort | tulleriiaarneq / tulleriiaakkit | tulleriiaarpai: puts them in order |
| ascending / descending | qummut / ammut | |
| customer | pisisartoq | pl. pisisartut |
| product | nioqqutissiaq | pl. nioqqutissiat |
| checkout | akiliineq | |
| VAT / VAT type | moms / momsip suussusaa | Danish moms |
| discount / discount rule | akikillisaaneq / akikillisaanermut malittarisassaq | |
| rule | malittarisassaq | pl. malittarisassat |
| price / currency | aki / valuta | its price: akia |
| payment gateway / gateway | akiliinermut aqqutissaq / aqqutissaq | |
| wish list | kissaatigisat allattorsimaffiat | |
| newsletter | paasissutissiaq | pl. paasissutissiat |
| subscriber / subscription / subscribe | abonnementeqartoq / abonnementi / abonnementiliorit | |
| tag (keyword, eztags) | taaguut | pl. taaguutit |
| keyword | ujarlernermi oqaaseq | pl. ujarlernermi oqaatsit |
| tag (HTML/XML) / custom tag | HTML-tag / immikkut tag | |
| label (of a field, button, block) | allagartaq | |
| export / import | anitsiguk / isertiguk | anitsippaa / isertippaa; nouns: anitsineq / isertitsineq; PDF export: PDF-imik anitsineq |
| move | nuuguk | nuuppaa; also in the tree, between groups and states |
| copy | assiliuk | assilivaa |
| swap | taarseraakkit | taarseraavai |
| embedding (vector) / chunk / vector | embedding / immikkoortunnguaq / vektori | embedding is the technical term of the AI providers |
| embed / embedded (object in text) | ilanngutiguk / ilanngunneqarsimasoq | |
| prompt / model / token | piumasaq / modeli / token | |
| AI | AI | |
| deploy / deployment / release / rollback | atulersiguk / atulersitsineq / saqqummersitaq / utertitsineq | |
| enable / disable | atuutilersiguk / atorunnaarsiguk | |
| enabled / disabled | atuuttoq / atuutinngitsoq | |
| active / inactive | ingerlasoq / ingerlanngitsoq | |
| warning / error / notice | mianersoqqussut / kukkuneq / nalunaarut | |
| required / optional | pisariaqartoq / piumassuseq malillugu | |
| default | aallaavittut | default value: naleq aallaavittut |
| priority | salliutitaq | |
| visible / hidden | takuneqarsinnaasoq / isertorneqarsimasoq | |
| show / hide / reveal | takutiguk / isertoruk / takutiguk | |
| expand / collapse | siammarteruk / eqiteruk | |
| OK / Cancel | OK / Taamaatiguk | taamaatippaa |
| Yes / No | Aap / Naamik | |
| Back / Next / Previous / Close | Uterit / Tullia / Siulia / Matuguk | |
| open | ammaruk | |
| select / select all / deselect all | toqqaruk / tamaasa toqqakkit / toqqakkat tamaasa peerukkit | toqqarpaa: chooses it; do not confuse with toqqorpaa: saves it |
| select none | toqqaasoqanngilaq | |
| browse | qinerit | qinerpoq: looks for |
| path | aqqut | |
| apply / apply changes | atuutsiguk / allannguutit atuutsikkit | |
| save / store | toqqoruk | toqqorpaa; the shop tab Store: see shop |
| discard | igiguk | igippaa: throws it away |
| edit | aaqqissoruk | aaqqissorpaa |
| remove | piiaruk | take out of a list, group, assignment or place; piiarpaa; Remove selected: Toqqakkat piiakkit |
| delete | peeruk | destroy; peerpaa; a »Remove« that destroys also takes peeruk |
| new / create / add | nutaaq / pilersiguk / ilaguk | |
| refresh / update | nutarteruk | nutarterpaa |
| reset | aallaqqaataanut utertiguk | |
| restore | utertiguk | utertippaa: brings it back |
| run (verb / noun) | aallartiguk / ingerlatsineq | a cron or index run: ingerlatsineq |
| dry run | misileraalluni ingerlatsineq | |
| self-test | imminut misileraaneq | |
| debug | kukkunersiuineq | |
| statistics | kisitsisitigut paasissutissat | |
| system information | systemimut paasissutissat | |
| database | paasissutissaateqarfik | |
| handler | suliarinnittoq | |
| wizard | ilitsersuut | |
| backend / provider / endpoint | backend / pilersuisoq / endpoint | pl. pilersuisut |
| method / listener / controller | periaaseq / tusaanniaasoq / aqutsiut | |
| allowlist | akuerisat allattorsimaffiat | |
| read-only | atuarneqarsinnaannaq | |
| argument | argumenti | |
| slot | inissaq | |
| collection | katersugaq | |
| facets | immikkoortiterinerit | Solr facets |
| elevate | qummukartiguk | Solr elevation |
| filter | filteri | Filter (button): Filterigit |
| details | itisiliineq | |
| tick (checkbox) | nalunaaqutsiguk | the checkbox itself: see section 5 |
| container (content class, "Is container") | imaqarsinnaasoq | |
| container (Bootstrap/CSS) | container | |
| locale | nunap ileqqui | INI keys (Locale, SiteLanguageList) and %locale stay as code |
| localization | nunap ileqquinut naleqqussaaneq | |
| approve / approval / approved / approver | akueriuk / akuerineqarnissaa / akuerineqarsimasoq / akuersisoq | approval workflow: akuerinninnermut suliap ingerlanera |
| deny / reject / rejected | itigartiguk / itigartiguk / itigartitaasoq | |
| accept / accepted | akueriuk / akuerineqarsimasoq | |
| pending | utaqqisoq | pending list: utaqqisut allattorsimaffiat; the order status code Pending stays |
| charset | naqinnerit katersugaat | |
| license | atuinissamut akuersissut | not akuersissut alone, which is permission; the file name LICENSE stays |
| privilege (DB) / grant | pisinnaatitaaffik / tunniuguk | |
| assign / assignment | tunniuguk / tunniussineq | |
| format / formatting | formati / allakkap ilusilersornera | text formatting is not the Exponential design |
| linked / common | attavilik / ataatsimoortoq | |
| flags (class flags) | nalunaaqutsiinerit | |
| teaser | siulequt | |
| slug | URL-imi aqqa | |
| placeholder | sinniisoq | pl. sinniisut |
| preset | aallaavittut aaqqissuussaq | pl. aallaavittut aaqqissuussat |
| information collection / collector | paasissutissanik katersineq / katersisoq | a collected entry: katersugaq, pl. katersukkat |
| tab | tabi | pl. tabit; not menu |
| rich text | allagaq ilusilersugaq | |
| feed | ingerlaavartumik nutaarsiassat | short RSS where the context is RSS |
| session | isersimaneq | |
| scope | annertussusaa | |
| thread (conversation / process) | oqaloqatigiinneq / thread | |
| chain | tulleriiaat | |
| batch | katiterisaq | pl. katiterisat |
| schema / registry / manifest | skema / registeri / manifesti | |
| stylesheet / theme / icon | CSS-fiili / isikkoq / ikoni | |
| port | port | |
| profile | profili | |
| core (Solr) | core | |
| editor (person) / editor (software) | aaqqissuisoq / aaqqissuisarfik | template editor: skabelonit aaqqissuisarfiat |
| Editor (siteaccess type) | Aaqqissuisarfik | |
| page / pages | qupperneq / quppernerit | |
| link | innersuussut | pl. innersuussutit |
| title / name / description | qulequtaq / ateq / nassuiaat | a person's name: ateq; its name: aqqa |
| date / time | ulloq / nalunaaqutaq | |
| status | killiffik | |
| type | suussuseq | its type: suussusaa |
| group | eqimattaq | pl. eqimattat |
| list | allattorsimaffik | |
| value / field | naleq / immersugassaq | |
| number / count | kisitsit / amerlassusaa | |
| width / height / size | silissusaa / portussusaa / angissusaa | |
| server / client / request / response | serveri / klienti / qinnut / akissut | |
| archived (version, object, order) | toqqorsivimmiittoq | |
| Archive / Unarchive (button) | toqqorsivimmut nuuguk / toqqorsivimmit anisiguk | |
| archive (storage place, menu item, log archive) | toqqorsivik | |
| archive (file .tar/.zip/.phar/.ezpkg) | arkivfiili | |
| pattern | ilusissaq | a search or match pattern |
| repository | repository | with endings repository-mi |
| maintainer | aserfallatsaaliuisoq | |
| install / uninstall / installation | ikkuguk / piiaruk / ikkussineq | ikkuppaa |
| checksum | checksum | |
| quarantine | avissaartitsineq | |
| pool | pool | of processes or threads |
| mapping | atassusiineq | |
| resolver / resolve (path) / resolvable | aaqqiisoq / nassaaruk / nassaarineqarsinnaasoq | |
| checkpoint | misissuiffik | |
| index file | indeks-fiili | |
| child documents (Solr) | ataani dokumentit | |
| script | scripti | |
| revoke | atorunnaarsiguk | key, token, session, access |
| kernel | qitiusoq | the Exponential kernel |
| layout (directory or release structure) | aaqqissugaaneq | not inissiineq, which is the page layout |
| confirmation questions ("Delete …?", "Cancel this …?") | …peerusuppiuk? / …taamaatirusuppiuk? | interrogative 2nd person singular |

## 5. Decisions after the translation

Binding; this section takes precedence over the glossary.

| English | Greenlandic (Kalaallisut) | Note |
|---|---|---|
| button | toortagaq | pl. toortakkat |
| click | tooruk | toorpaa; contemporative toorlugu, noun toorneq; not tooqqallugu |
| checkbox | nalunaaqutsiiffik | pl. nalunaaqutsiiffiit; ticking it: nalunaaqutsiguk |
| radio button | toortagaq ataasiinnarmik toqqaaffik | pl. toortakkat ataasiinnarmik toqqaaffiit; in a running instruction the short »toortakkat« may follow |
| drag | nusuguk | nusuppaa; pl. nusukkit, contemporative nusullugu |
| grip (drag handle) | tigummivik | |
| arrow / arrow key / key (keyboard) / keyboard | qarsoq / tasti qarsoq / tasti / tastaturi | pl. qarsut, tastit qarsut; arrow buttons: qarsut toortakkat |
| clipboard | assilisat toqqorsiviat | |
| column (table) / row (table, DB) | kolonni / titarneq | pl. kolonnit / titarnerit |
| Find X (filter field) | X ujaruk / X ujakkit | the Search button is Ujarlerit |
| key (API key, activation key, INI array key) | kodi | pl. kodit, its key: kodia |
| key (signing, encryption) | matuersaat | signing key: atsiornermut matuersaat |
| key (primary, storage, sort key) | ilisarnaat | primary key: ilisarnaat pingaarneq; storage key: toqqorsivimmut ilisarnaat; sort key: tulleriiaarnermut ilisarnaat |
| memory (RAM) | eqqaamasaq | shared memory: eqqaamasaq avitseqatigiiffiusoq; memory limit: eqqaamasap killissaa; not toqqorsivik (archive) |
| cronjob part | cron-ip suliassaata ilaa | pl. cron-ip suliassaata ilai, also in labels |
| SQL statement / query (DB) | inassut / apeqqut | statement time limit: inassummut piffissaliussaq; query cache: apeqqutinut cache |
| command (shell) / command line | inassut / kommandolinji | |
| search query (what the user typed) | ujarlerneq | |
| string (programming) | naqinnerit tulleriiaat | UI texts to translate: allagaq, pl. allakkat |
| interface (UI) / interface (API, REST, HTTP) / interface (PHP) | atuisup saqqaa, aqutsinermut saqqaq / atassut / interface | REST interface: REST-imut atassut; PHP interface: interface-ia, pl. interface-it |
| application (OAuth/REST client) | atortussiaq | pl. atortussiat; Application name: Atortussiap aqqa |
| tool / tools | sakku / sakkut | not atortoq, which is too close to atortussaq (function) |
| engine (database, template, server) | motori | pl. motorit; search engine stays ujarlerfik |
| machine (server, workstation) | maskiina | workstation: suliffimmi qarasaasiaq |
| form (to fill in) | immersugassiaq | pl. immersugassiat; a field is immersugassaq |
| visitor (site) | tikeraaq | pl. tikeraat |
| channel | kanaali | pl. kanaalit |
| secret | isertugaq | pl. isertukkat |
| issue / problem | ajornartorsiut | pl. ajornartorsiutit |
| generate / regenerate | pilersiguk / pilersiteqqiguk | |
| source / destination / target | aallaavik / tikiffissaq / anguniagaq | destination of move, copy, redirect: tikiffissaq |
| tree (content tree) / Tree (subtree option) | imarisat aaqqissugaanerat / ataaniittut tamarmik | |
| state (technical: of a process, a file) | killiffik | the Exponential object state stays qanoq innera |
| Never (value: never happened) | Taamaattoqarsimanngilaq | not Naamik; »never expires« as a sentence: atorunnaartanngilaq |
| menu / table | menu / tabeli | pl. menuit / tabelit |
| environment (Development/Test/Staging/Production) | avatangiisit: Ineriartortitsineq / Misileraaneq / Piareersaaneq / Atuutsitsineq | the badges DEV/TEST/STAGE/PROD stay |
| hit / miss / hit rate | naammattuukkat / naammattuunngitsut / naammattuukkat procentii | |
| pause / resume | unitsikkallarguk / ingerlaqqittiguk | |
| transaction / replica | transaktioni / replika | |
| alert / chart | alarmi / diagrammi | |

- **remove vs. delete**: piiaruk/piiarneqarpoq when something is taken out of a list, a group, an assignment or a
  place; peeruk/peerneqarpoq when it is destroyed. Confirmations follow the action, not the English word: a
  dialogue »Remove …?« that deletes takes peerusuppiuk.
- **Two meanings, two words**: toqqorsivik is the archive only (not RAM); attaveqaat is a relation only (not an
  interface); immersugassaq is a field, immersugassiaq a form; maskiina is a machine, motori an engine;
  inassut is a command or SQL statement; kodi is an access or array key, matuersaat a cryptographic key,
  ilisarnaat an identifier key.
