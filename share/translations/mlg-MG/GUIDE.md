# Malagasy (mlg-MG): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Official Malagasy (teny malagasy ofisialy, based on Merina) as written in Madagascar, in the Latin alphabet, in the
neutral, polite register of Malagasy government portals, Malagasy news (Midi Madagasikara, Gazetiko) and Malagasy
Wikipedia. No other locale falls back to `mlg-MG`.

- Word order is verb - object - subject; the subject comes last ("Voatahiry ny drafitra."). Function words: sy / ary
  (and), na (or), fa (but), raha (if), ity / ireto (this, these), io (that), rehetra (all), ao amin'ny (in), avy
  amin'ny (from), ho an'ny (for), tsy (not), tsy misy (none, there is no), misy (there is).
- Technical words: a Malagasy word where one is in use (mpampiasa, teny miafina, fikarohana, fikirana, tahiry); a
  French loan in Malagasy spelling where Malagasy IT writing uses it (fichier -> rakitra, serveur -> mpizara, modely,
  kilasy, version -> dikan-teny). French (fre-FR) may help with the loans, but translate from the English source.
  Never leave the English word unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **ianao** (you), polite by word choice ("azafady" for please).
- **Buttons, menu items and links**: the imperative, as Malagasy interfaces do: Tehirizo (save), Foano / Aoka
  (cancel: Foano), Fafao (delete), Esory (remove), Ovay (edit), Ampio (add), Mamorona (create), Mikaroha (search),
  Safidio (select), Alefaso (send), Jereo (view), Avoaky (publish), Ampakaro (upload), Alaivo (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Fikirana, Fandikana, Toerana fototra, Anarana.
- **Instructions**: the imperative with "azafady" where the source has please: "Safidio ilay zavatra ary tsindrio ny
  \"OK\".".
- **Confirmations and questions**: "Tena tianao ve ny ...?": "Tena tianao ve ny hamafa ity fizarana ity?".
- **What the user can or may do**: "Afaka ... ianao", "Tsy afaka ... ianao"; "You do not have permission" is "Tsy
  manana alalana hanao ... ianao".
- **Messages about a result**: the passive: "Voatahiry ny drafitra.", "Tafakatra soa aman-tsara ny rakitra.".

## 3. Script and typography

- **Latin** alphabet as Malagasy writes it (no c, q, u, w, x; y at the end of words, i inside), the apostrophe of
  the genitive (amin'ny, an'ilay, ho an'ny) as the straight ' (U+0027), and the hyphen of compounds (dikan-teny,
  tenimiafina).
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder ("an'i %name" with a space). The
  same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Malagasy | Note |
|---|---|---|
| content | votoaty | content object: zavatra votoaty |
| object | zavatra | |
| node | fihaonana | main node: fihaonana fototra; parent node: fihaonana ray |
| location | toerana | main location: toerana fototra |
| item (list entry) | singa | |
| class | kilasy | the content class; class group: vondron-kilasy |
| attribute | toetra | |
| datatype | karazana angona | |
| version | dikan-teny | version history: tantaran'ny dikan-teny |
| draft | drafitra | store draft: Tehirizo ny drafitra |
| publish | avoaka | to publish: avoahy; published: navoaka |
| send for publishing | alefaso hivoaka | |
| discard | ario | discard draft: Ario ny drafitra |
| archived / pending / rejected | voatahiry an-tahiry / miandry / nolavina | |
| siteaccess | fidirana amin'ny tranonkala | siteaccess as a setting name stays in code |
| site, website | tranonkala | |
| section | fizarana | the Exponential section; an INI section: fizarana INI |
| role | anjara asa | |
| policy | politika | |
| limitation | fetra | |
| permission | alalana | access denied: nolavina ny fidirana |
| module | môdily | |
| function | asa | a PHP function: fonction PHP -> asa PHP |
| user / user group | mpampiasa / vondron'ny mpampiasa | |
| group | vondrona | |
| anonymous | tsy fantatra anarana | anonymous user: mpampiasa tsy fantatra anarana |
| administrator | mpitantana | administration (interface): fitantanana |
| username | anaran'ny mpampiasa | login (the name): anarana fidirana |
| password | teny miafina | |
| log in / log out | hiditra / hivoaka | the act: fidirana / fivoahana |
| e-mail | mailaka | e-mail address: adiresy mailaka |
| cache | tahiry vonjimaika | clear cache: Diovy ny tahiry vonjimaika |
| template | modely | override template: modely mpisolo |
| override | fisoloana | |
| fallback | solony | fallback language: fiteny solony |
| design | endrika | |
| workflow | fizotran'asa | |
| trigger | mpanetsika | |
| event | zava-mitranga | |
| trash | fako | empty trash: Foano ny fako; move to trash: Afindrao any amin'ny fako |
| bookmark | marika | |
| settings | fikirana | configuration: fandrindrana |
| setup | fametrahana | the Setup tab |
| layout | firafitry ny pejy | Exponential Layouts stays |
| block / zone | bloka / faritra | |
| extension | fanitarana | |
| package | fonosana | |
| search | fikarohana | to search: mikaroha; search index: fanondroana fikarohana |
| log | diary | a log file: rakitra diary; never "log in" |
| queue | filaharana | job queue: filaharan'asa |
| job / task | asa | |
| cron | cron | |
| script | script | |
| kernel, core | fototra | |
| server | mpizara | |
| database | tahiry angona | |
| folder / directory | lahatahiry | |
| file | rakitra | |
| image | sary | thumbnail: sary kely |
| preview | topi-maso | |
| upload / download | ampakaro / alaivo | |
| export / import | avoahy / ampidiro | |
| remove | esory | take out of a list or place |
| delete | fafao | destroy; deleted: voafafa |
| edit | ovay | |
| editor | mpanova / fitaovana fanovana | person / software |
| save / store | tehirizo | the verb "Store" of the forms |
| Store (shop tab) | fivarotana | the shop, as "Shop" |
| cancel | foano | |
| OK | Eny | |
| apply | ampiharo | |
| back | miverina | |
| next / previous | manaraka / teo aloha | |
| enable / disable | alefaso / atsaharo | enabled / disabled: mandeha / tsy mandeha |
| active / inactive | mavitrika / tsy mavitrika | |
| run | alefaso | |
| start / stop | atombohy / ajanony | |
| session | fotoam-pidirana | |
| hidden / visible | miafina / hita | hide / reveal: afeno / asehoy |
| archive | arisiva | |
| order (sort) | filaharana | sort order: filaharana |
| order (shop) | kaomandy | |
| shop | fivarotana | |
| basket | harona | |
| product | vokatra | |
| customer | mpanjifa | |
| discount | fihenam-bidy | |
| VAT | TVA | |
| price / currency | vidiny / vola | |
| tag | tenifototra | an HTML tag: tag HTML |
| translation | fandikana | |
| language | fiteny | main language: fiteny fototra |
| locale | toeran-piteny | |
| state | toetry ny zavatra | the object state: toetra |
| status | sata | |
| view (button) | jereo | a module view (noun): fijery |
| format | endrika | |
| sort | alaharo | |
| filter | sivana | to filter: sivano |
| select | safidio | select all: Safidio daholo; selected: voafidy |
| copy / move | adikao / afindrao | |
| add / create / new | ampio / mamorona / vaovao | |
| restore | avereno | |
| install / uninstall | apetraho / esory | |
| related objects | zavatra mifandray | relation: fifandraisana |
| dashboard | tabilao fanaraha-maso | |
| identifier / ID | famantarana / ID | |
| priority | laharam-pahamehana | |
| owner | tompony | |
| modified / created | novaina / noforonina | |
| date / time | daty / ora | |
| error / warning | hadisoana / fampitandremana | |
| help | fanampiana | |
| notification | fampandrenesana | |
| account | kaonty | |
| default | mahazatra | |
| link | rohy | |
| none / all | tsy misy / rehetra | |
| yes / no | eny / tsia | |
| name / title / description | anarana / lohateny / famaritana | |
| value / type / list | sanda / karazana / lisitra | |
| page / path | pejy / lalana | |
| required / optional | ilaina / tsy voatery | |
| options | safidy | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is fivarotana, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is tehirizo.
- "Login" in the user, session and audit lists is the user's login name (anarana fidirana); the Login button of the
  error page is the act (Hiditra).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
