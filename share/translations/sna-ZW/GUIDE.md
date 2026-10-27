# Shona (sna-ZW): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Shona (chiShona chakajairwa), the written standard of Zimbabwe based on the unified orthography of 1967,
in the neutral, technical register of current Shona software (the Shona localisations of Google and Mozilla) and
of the Shona Wikipedia. No other locale falls back to `sna-ZW`; English, the other language of Zimbabwean
offices, is not mixed into a sentence where Shona has a word.

- Shona words are used where they exist and are understood: chengetedza, dzima, bvisa, tsvaga, sarudza, gadzira,
  wedzera, mushandisi, boka, mutauro, mvumo. Technical words that have no Shona equivalent are adapted to Shona
  spelling and noun classes, as Shona does with loanwords: faira, foroda, sevha, webhusaiti, pasiwedhi, imeri,
  dhatabhesi, kodhi, tembureti.
- Product names, codes and acronyms stay in Latin as they are. A Shona prefix before an acronym is joined with a
  hyphen: ye-URL, re-PHP, mu-HTML. Often a wording without a prefix reads better: "faira re PHP".

## 2. Form of address

The interface addresses the user with the respectful plural **imi** (you), **yenyu** / **renyu** / **zvenyu**
(your), the form Shona uses for a person one does not know closely.

- **Buttons, menu items and links**: the plain verb stem (the imperative singular), as Shona software does and as
  is usual for commands in Bantu-language interfaces: Chengetedza, Kanzura, Dzima, Gadzirisa, Buritsa, Tumira
  kuti ribudiswe, Bvisa zvasarudzwa, Isa mumarara. Not the infinitive (Kuchengetedza) on a button.
- **Titles, column headings and tabs**: nouns or noun phrases: Zvigadziriso, Shanduro, Nzvimbo huru.
- **Instructions**: the polite plural imperative in -i (with the subject imi understood): "Sarudzai chinhu
  mobva madzvanya “Zvakanaka”.", "Mirai ...". A reference ("see ...") is "Onai ...".
- **Confirmations and questions**: not the familiar "Une chokwadi ...?", but "Muri
  nechokwadi here kuti munoda ...?": "Muri nechokwadi here kuti munoda kudzima kirasi iyi?".
- **What the user can or may do**: "Munogona ...", "Hamugoni ...", or impersonal: "Mutauro mukuru haugoni
  kuchinjwa ...". "You do not have permission" is "Hamuna mvumo ...".
- Not allowed: the familiar singular iwe / yako in instructions, slash forms.

## 3. Script and typography

- **Latin script** in the standard Shona orthography: the digraphs and trigraphs bh, dh, mh, nh, vh, ch, sh, zh, sv,
  zv, dzv, tsv, ng', ny as one sound; no diacritics in everyday writing (no tone marks).
- **Noun classes and agreement**: an adjective, possessive and verb agree with the class of the noun: faira
  (class 5) -> "faira rakachengetedzwa"; vashandisi (class 2) -> "vashandisi vose"; zvinhu (class 8) ->
  "zvinhu zvakasarudzwa". Plural prefixes: mushandisi / vashandisi, chinhu / zvinhu, boka / mapoka, faira /
  mafaira, peji / mapeji.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before
  and after them, or a punctuation mark right after. Never a Shona prefix or suffix glued to a placeholder:
  "Zvinhu: %count", not "%countzvinhu" or "ye%name". Shona joins its prefixes to the noun, so a placeholder that
  would need one goes after a colon or after the noun: "Faira %file rakaiswa.", "Zita: %name".
- **Quotation marks**: the curly double marks “...” where the source has its own quotation marks in text; where the
  source has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon. Capital letters as in English sentence case; Shona does not capitalise every word of a title.
- **Numbers**: digits 0-9; a count goes after the noun or after a colon ("Zvinhu: %count").

## 4. Glossary

Binding for the kernel and every extension.

| English | Shona | Note |
|---|---|---|
| content | zvirimo | |
| content object | chinhu chezvirimo | plural zvinhu zvezvirimo |
| object | chinhu | plural zvinhu |
| node | nodhi | plural manodhi; main node: nodhi huru; parent node: nodhi mubereki |
| location | nzvimbo | main location: nzvimbo huru |
| item (list entry) | chinhu | in a list; sub items: zvinhu zviri pasi |
| class | kirasi | the content class; plural makirasi; class group: boka remakirasi; a PHP class: kirasi ye PHP |
| content class | kirasi yezvirimo | |
| attribute | hunhu | plural hunhu; one attribute: chimwe chehunhu |
| datatype | rudzi rwedhata | |
| version | vhezheni | version history: nhoroondo yevhezheni |
| draft | dhirafiti | plural madhirafiti; store draft: Chengetedza dhirafiti |
| publish | buritsa | published: rakabudiswa / zvakabudiswa |
| send for publishing | tumira kuti ribudiswe | |
| discard | rasa | discard draft: Rasa dhirafiti |
| archived | rakachengetwa mudura | |
| pending | rakamirira | |
| rejected | rakarambwa | |
| siteaccess | nzira yekupinda pasaiti | the configuration unit |
| site, website | saiti, webhusaiti | |
| section | chikamu | the Exponential section; plural zvikamu |
| role | chinzvimbo | plural zvinzvimbo |
| policy | mutemo | plural mitemo |
| limitation | muganhu | plural miganhu |
| permission | mvumo | |
| module | modhiyuru | plural mamodhiyuru |
| function | fanikishoni | |
| user | mushandisi | plural vashandisi |
| user group | boka revashandisi | |
| group | boka | plural mapoka |
| anonymous | asingazivikanwi | anonymous user: mushandisi asingazivikanwi |
| administrator | maneja | the site administrator; plural vamaneja |
| administration (interface) | nzvimbo yekutungamira | |
| username | zita remushandisi | the column "Login" too |
| password | pasiwedhi | |
| log in / sign in | pinda | |
| log out / sign out | buda | |
| e-mail | imeri | e-mail address: kero yeimeri |
| cache | cache | clear cache: Chenesa cache |
| template | tembureti | plural matembureti |
| override | kutsiva | override template: tembureti inotsiva |
| fallback | inotsiva kana isipo | fallback language: mutauro wekudzokera |
| design | dhizaini | |
| workflow | nhanho dzebasa | |
| trigger | chinotanga | |
| event | chiitiko | plural zviitiko |
| trash | marara | empty trash: Chenesa marara; move to trash: Isa mumarara |
| bookmark | bhukumaki | plural mabhukumaki |
| settings | zvigadziriso | |
| setup | gadziriro | the Setup tab; installation: kuisa |
| layout | marongerwo | Exponential Layouts |
| block | bhuroka | plural mabhuroka |
| zone | nharaunda | |
| extension | chiwedzero | plural zviwedzero |
| package | pakeji | plural mapakeji |
| search | tsvaga | |
| log | rekodhi | log file: faira rerekodhi |
| queue | mutsara | |
| job / task | basa | plural mabasa |
| cron | cron | |
| script | sikiripiti | |
| worker | chishandi | a worker process |
| kernel, core | musimboti | |
| server | sevha | |
| database | dhatabhesi | |
| folder | foroda | plural maforoda |
| file | faira | plural mafaira |
| image | mufananidzo | plural mifananidzo |
| preview | ongororo yekutanga | |
| upload | kwidza | |
| download | dhaunirodha | |
| export / import | buritsa kunze / pinza | |
| remove | bvisa | take out of a list or a place |
| delete | dzima | destroy |
| edit | gadzirisa | |
| editor | mugadziri | the person; the tool: chishandiso chekugadzirisa |
| save / store | chengetedza | |
| Store (shop tab) | Chitoro | the word the shop menu uses for "Shop" |
| cancel | kanzura | |
| OK | Zvakanaka | |
| apply | shandisa | |
| back | dzokera | |
| next / previous | inotevera / yapfuura | |
| enable / disable | batidza / dzima | a setting: Batidza / Dzima |
| enabled / disabled | yakabatidzwa / yakadzimwa | |
| active / inactive | inoshanda / isingashandi | |
| run | mhanyisa | |
| start / stop | tanga / mira | |
| session | sesheni | |
| hidden / visible | rakavanzwa / rinooneka | hide / reveal: vanza / ratidza |
| archive | dura | an archived version; a file archive: faira rakamanikidzwa |
| order (sort) | marongerwo | sort order: marongerwo ekuronga |
| order (shop) | odha | plural maodha |
| shop | chitoro | |
| basket | bhasikiti | |
| product | chigadzirwa | plural zvigadzirwa |
| customer | mutengi | plural vatengi |
| discount | dhisikaundi | |
| VAT | VAT | |
| price | mutengo | |
| currency | mari | the currency of a country: mari yenyika |
| tag | tegi | keyword tag; an HTML tag: tegi re HTML |
| translation | shanduro | plural shanduro |
| language | mutauro | main language: mutauro mukuru |
| locale | nzvimbo yemutauro | locale code: kodhi yemutauro nenyika |
| state | mamiriro | the object state; state group: boka remamiriro |
| status | chimiro | |
| view (button) | tarisa | a module view: maonero |
| format | fomati | |
| sort | ronga | |
| filter | sefa | |
| select | sarudza | selected: zvakasarudzwa |
| browse | ongorora | |
| copy | kopa | |
| move | tamisa | |
| add | wedzera | |
| create | gadzira | |
| new | itsva | agrees with the noun: chitsva, nyowani |
| restore | dzorera | |
| install / uninstall | isa / bvisa | |
| sub items | zvinhu zviri pasi | |
| subtree | bazi | |
| related objects | zvinhu zvakabatana | relation: hukama |
| URL alias | rimwe zita re URL | |
| redirect | tumira kune imwe nzvimbo | |
| dashboard | dhashibhodhi | |
| content structure | marongerwo ezvirimo | |
| media library | raibhurari yemidhiya | |
| identifier / ID | chiziviso / ID | |
| priority | kukosha | |
| owner | muridzi | |
| modified / created | yakachinjwa / yakagadzirwa | |
| date / time | zuva / nguva | |
| error / warning | kukanganisa / yambiro | |
| help | rubatsiro | |
| notification | chiziviso | |
| wizard | mubatsiri | a step-by-step assistant |
| feed (RSS) | chiteshi | RSS feed: chiteshi che RSS |
| tab | tebhu | |
| audit, audit trail | ongororo, rekodhi yeongororo | |
| repository | dura rekodhi | |
| checksum | checksum | |
| account | akaundi | |
| default | zvakajairwa | default value: kukosha kwakajairwa |
| toolbar | bhawa rezvishandiso | |
| container | mudziyo | |
| AI | AI | the full term: hungwaru hwekugadzirwa |
| pattern (name pattern) | pateni | |
| wildcard | chiratidzo chinotsiva | |
| remote ID | ID yekure | |
| placeholder | chinotsiva | |
| collection | muunganidzwa | information collector: muunganidzi weruzivo |
| approve, approval | bvumira, mvumo | |
| software | purogiramu | |
| creator / modifier | mugadziri / mushanduri | |
| request (HTTP) | chikumbiro | |
| query | mubvunzo | search query: mubvunzo wekutsvaga |
| handler / provider / listener | mubati / mupi / muteereri | |
| engine | injini | |
| release / upgrade | kuburitswa / kusimudzira | update: kuvandudza |
| backup | kopi yekuchengetedza | |
| maintenance | kuchengetedza hurongwa | |
| directory | dhairekitori | |
| header (HTTP) | musoro | |
| page layout (pagelayout template) | tembureti yepeji | kept apart from marongerwo (Exponential Layouts) |
| passkey | kiyi yekupinda | |
| two-step sign-in | kupinda nenhanho mbiri | |
| device / browser | mudziyo / bhurawuza | |
| link | link | plural malink; the verb: batanidza |
| registration, register | kunyoresa, nyoresa | |
| payment gateway | gedhi rekubhadhara | |
| form | fomu | |
| comment | mhinduro | |
| newsletter / subscription | tsamba yenhau / kunyorera | |
| field | munda | |
| value | kukosha | |
| list | rondedzero | |
| page | peji | plural mapeji |
| title / name | musoro / zita | the column "Name": Zita |
| description | tsananguro | |
| type | rudzi | |
| path | nzira | |
| width / height | upamhi / urefu | |
| size | saizi | |
| none | hapana | |
| yes / no | hongu / kwete | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Chitoro, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is chengetedza.
- "Delete" (dzima) and "disable" (dzima, switch off) are the same verb in Shona; where both stand side by side the
  setting is "Mira kushandisa".
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
