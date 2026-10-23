# Danish (dan-DK): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Danish as used in Denmark, in current spelling (Retskrivningsordbogen). No other locale falls back to this
catalogue. The tone is plain and direct, as in the Danish versions of common operating systems and office software.

## 2. Form of address

- **Buttons, menu items, links and commands** use the imperative: "Gem", "Slet", "Fjern", "Rediger", "Tilføj",
  "Annuller", "Opret ny", "Vælg", "Søg", "Anvend ændringer", "Publicer", "Upload", "Hent", "Luk", "Tilbage".
- **Titles, column headings and tabs** are nouns: "Indholdsstruktur", "Brugerkonti", "Layouttype",
  "Notifikationsindstillinger".
- **Sentences, hints, questions and error messages** address the user with **du** (dig, din, dit, dine), and
  instructions use the imperative:
  - "Er du sikker på, at du vil fjerne denne klasse?"
  - "Vælg en placering" (Please choose a location)
  - "Du har ikke tilladelse til at fjerne klasser."
  - "Klik på knappen "Annuller", og prøv kun at fjerne de placeringer, du har tilladelse til at fjerne."
  - "Du kan redigere"
- "Please" is not translated: "Please try again" is "Prøv igen".
- Not allowed: the formal De, Dem, Deres as a form of address. ("De" and "Deres" as the plural "they, their" are
  fine.)

## 3. Script and typography

- Latin script with æ, ø, å. Sentence case: "Indholdsstruktur", "Brugere og grupper".
- Quotation marks as in the source; the catalogue keeps the source's straight or curly quotes.
- Commas before subordinate clauses and between main clauses, as in the catalogue: "Er du sikker på, at du vil
  ...", "Vælg den CSS-fil for sitet, der skal medtages i pakken.", "Godkend, og fortsæt".
- Compounds are written as one word ("klassegruppe", "jobkø", "søgeindeks"); after a code or a name with a hyphen:
  "INI-fil", "PHP-version", "Solr-kerne", "URL-alias", "knude-id", "REST-udbyder".
- Imperatives of verbs in -ere have no accent: "Kopier", "Publicer", "Rediger", "Sorter", "Importer", "Marker".
- Counts: "%count elementer", or a label form: "Elementer: %count".

## 4. Glossary

| English | Danish | Note |
|---|---|---|
| content | indhold | |
| content object / object | indholdsobjekt / objekt | |
| node | knude | pl. knuder; node ID: knude-id; main node: hovedknude |
| location / main location | placering / hovedplacering | |
| content class / class | indholdsklasse / klasse | class group: klassegruppe |
| attribute | attribut | |
| datatype | datatype | |
| version | version | |
| draft | udkast | Store draft: Gem udkast; Discard draft: Kasser udkast |
| publish / published | publicer / publiceret | Send for publishing: Send til publicering |
| siteaccess | siteaccess | en siteaccess, pl. siteaccesses |
| site | site | the site: sitet; sitemap: webstedsoversigt |
| section | sektion | |
| object state / state | objekttilstand / tilstand | |
| role | rolle | |
| policy | politik | pl. politikker |
| limitation | begrænsning | |
| permission / access denied | rettighed / adgang nægtet | "You do not have permission": Du har ikke tilladelse |
| user / user group | bruger / brugergruppe | |
| user accounts | brugerkonti | |
| owner / creator | ejer / opretter | |
| created / modified | oprettet / ændret | column headings |
| cache | cache | Clear cache: Ryd cache |
| template | skabelon | |
| override | tilsidesættelse | override template: tilsidesættelsesskabelon |
| fallback | reserve | fallback chain: reservekæde |
| design | design | |
| workflow | arbejdsgang | |
| trigger / event | udløser / hændelse | |
| trash | papirkurv | |
| bookmarks | bogmærker | |
| settings | indstillinger | |
| Setup (tab) | Opsætning | |
| Dashboard | Dashboard | |
| Content structure | Indholdsstruktur | |
| Media library | Mediebibliotek | |
| layout | layout | layout type: layouttype |
| block / zone | blok / zone | |
| extension | udvidelse | |
| package | pakke | |
| module / view | modul / visning | the button "View": Vis |
| search | søg | noun: søgning; search engine: søgemaskine |
| search index / reindex | søgeindeks / genindeksering | verb: genindekser |
| log / log file | log / logfil | |
| audit / audit trail | revision / revisionsspor | |
| job / queue | job / kø | job queue: jobkø |
| worker | worker | pl. workers |
| cronjob | cronjob | |
| preview | forhåndsvisning | |
| upload / download | upload / hent | verbs in a sentence: uploade / downloade |
| export / import | eksporter / importer | |
| remove | fjern | Remove selected: Fjern valgte |
| delete | slet | |
| edit | rediger | |
| save / store | gem | Store (shop tab): Butik |
| discard | kasser | |
| apply | anvend | Apply changes: Anvend ændringer |
| cancel | annuller | |
| OK / yes / no | OK / ja / nej | |
| back / next / previous | tilbage / næste / forrige | |
| create / add | opret / tilføj | |
| select / browse | vælg / gennemse | Select all: Vælg alle; Deselect all: Fravælg alle |
| move / copy | flyt / kopier | |
| hide / reveal | skjul / vis | hidden / visible: skjult / synlig |
| enable / disable | aktiver / deaktiver | enabled / disabled: aktiveret / deaktiveret |
| restore / assign | gendan / tildel | |
| approve / reject / pending | godkend / afvis / afventer | |
| archive | arkiv | |
| order (shop) | ordre | pl. ordrer; order status: ordrestatus |
| order (sorting) | rækkefølge | |
| shop / basket / customer / product | butik / indkøbskurv / kunde / produkt | |
| VAT / discount / price / currency | moms / rabat / pris / valuta | |
| wish list | ønskeliste | |
| newsletter / subscription | nyhedsbrev / abonnement | |
| tag / keywords | tag / nøgleord | |
| URL alias / wildcard | URL-alias / jokertegn | URL wildcard: jokertegn-URL |
| translation / language | oversættelse / sprog | main language: hovedsprog; interface language: grænsefladesprog |
| locale | landestandard | |
| relation / related objects | relation / relaterede objekter | reverse related: omvendt relaterede objekter |
| subtree / parent / sub items | undertræ / overordnet / underelementer | |
| notification / collaboration | notifikation / samarbejde | |
| identifier / remote ID | identifikator / fjern-id | |
| login (field) / password | login / adgangskode | username: brugernavn |
| log in / log out | log ind / log ud | |
| e-mail | e-mail | |
| file / folder / image | fil / mappe / billede | |
| item / items | element / elementer | in the shop: vare / varer |
| name / title / description | navn / titel / beskrivelse | |
| type / status | type / status | |
| default / required | standard / påkrævet | |
| error / warning | fejl / advarsel | |
| filter / details | filter / detaljer | |
| run / refresh / update / reset | kør / opdater / opdater / nulstil | |
| dry run | prøvekørsel | |
| debug | fejlfinding | |
| editor (software) / editor (person) | editor / redaktør | template editor: skabeloneditor |
| provider | udbyder | |
| prompt / model / token | prompt / model / token | |
| embedding | embedding | |
| thread / chain | tråd / kæde | |

## 5. Decisions after the translation

- **Store**: the shop tab of the administration header is "Butik"; every other "Store" is the verb "Gem".
- **Audit** is "revision" throughout ("Revision", "revisionsspor", "Revision: genindeksering").
