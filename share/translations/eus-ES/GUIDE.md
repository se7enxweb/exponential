# Basque (eus-ES): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Basque (euskara) in the unified standard (euskara batua) of Euskaltzaindia, with the computing terms of the Basque
software localisations (Elhuyar, Euskalterm, the Basque Government's terminology), in a neutral, polite register. No
other locale falls back to `eus-ES`.

- Write real Basque, never Spanish words: "fitxategia", "pasahitza", "karpeta", "esteka", "erabiltzailea",
  "ezabatu", "gorde". Spanish (`esl-ES`) may help with the meaning and the terminology; the text itself is translated
  from the English.
- Word order is free but the verb usually comes last; the cases are suffixes (-a, -ak, -k, -ri, -ren, -n, -tik,
  -ra, -rekin, -rako). Nouns in a label carry the article (-a): Fitxategia, Izena, Egoera.
- Loan words in Basque spelling: zerbitzaria, cachea, modulua, paketea, txantiloia, interfazea, konfigurazioa,
  kategoria.
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **zu** (never hi), as the Basque localisations do: "Ziur zaude objektu hau ezabatu nahi
  duzula?", "Ez duzu baimenik ...".
- **Buttons, menu items and links**: the verb in its dictionary form (the participle), as Basque interfaces do: Gorde,
  Utzi, Ezabatu, Kendu, Editatu, Gehitu, Sortu, Bilatu, Hautatu, Bidali, Ikusi, Argitaratu, Igo, Deskargatu,
  Aplikatu, Leheneratu.
- **Titles, column headings and tabs**: nouns or noun phrases with the article: Ezarpenak, Itzulpenak, Kokapen
  nagusia, Izena.
- **Instructions**: the imperative of zu or the participle: "Hautatu objektu bat eta sakatu \"Ados\".".
- **Confirmations and questions**: "Ziur zaude ... nahi duzula?".
- **What the user can or may do**: "... dezakezu", "Ezin duzu ..."; "You do not have permission" is "Ez duzu
  baimenik ...".
- **Messages about a result**: the perfect: "Zirriborroa gorde da.", "Fitxategia behar bezala igo da.".

## 3. Script and typography

- **Latin** alphabet; ñ and ü only in names; tz, tx, ts, x, z as batua writes them.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. **Never glue a case suffix to a placeholder** (`%namek`,
  `%name-ren` are wrong): put a noun after the placeholder that takes the suffix ("%name objektua", "%name
  erabiltzailearen") or rephrase ("Izena: %name"). The same for code, file names, INI settings and product names;
  where a code or a product name would need a suffix, a noun after it takes the suffix ("PHP lengoaiaren", "Solr
  zerbitzarian").
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Basque | Note |
|---|---|---|
| content | edukia | content object: eduki-objektua |
| object | objektua | |
| node | nodoa | main node: nodo nagusia; parent node: nodo gurasoa |
| location | kokapena | main location: kokapen nagusia |
| item (list entry) | elementua | |
| tree | zuhaitza | tree menu: zuhaitz-menua; content structure: edukiaren egitura |
| class | klasea | content class: eduki-klasea; class group: klase-taldea |
| attribute | atributua | |
| datatype | datu-mota | |
| version | bertsioa | version history: bertsioen historia |
| draft | zirriborroa | store draft: Gorde zirriborroa |
| publish | argitaratu | published: argitaratua; publishing: argitalpena |
| send for publishing | bidali argitaratzeko | |
| discard | baztertu | discard draft: Baztertu zirriborroa |
| archived / pending / rejected | artxibatua / zain / baztertua | |
| siteaccess | gune-sarbidea | siteaccess as a setting name stays in code |
| site, website | gunea, webgunea | |
| section | atala | the Exponential section; an INI section: INI atala |
| role | rola | |
| policy | gidalerroa | |
| limitation | muga | |
| permission | baimena | access denied: sarbidea ukatuta |
| module | modulua | |
| function | funtzioa | |
| user / user group | erabiltzailea / erabiltzaile-taldea | |
| group | taldea | |
| anonymous | anonimoa | anonymous user: erabiltzaile anonimoa |
| administrator | administratzailea | administration interface: administrazio-interfazea |
| username | erabiltzaile-izena | |
| password | pasahitza | |
| log in / log out | hasi saioa / amaitu saioa | the act: saio-hasiera / saio-amaiera |
| e-mail | posta elektronikoa | e-mail address: helbide elektronikoa |
| cache | cachea | clear cache: Garbitu cachea |
| template | txantiloia | override template: gainidazte-txantiloia |
| override | gainidaztea | |
| fallback | ordezkoa | fallback language: ordezko hizkuntza |
| design | diseinua | |
| workflow | lan-fluxua | |
| trigger | abiarazlea | |
| event | gertaera | |
| trash | zakarrontzia | empty trash: Hustu zakarrontzia; move to trash: Bota zakarrontzira |
| bookmark | laster-marka | |
| settings | ezarpenak | configuration: konfigurazioa |
| setup | konfigurazioa | the Setup tab |
| layout | orri-diseinua | Exponential Layouts stays |
| block / zone | blokea / eremua | |
| extension | hedapena | |
| package | paketea | |
| search | bilaketa | to search: bilatu; search index: bilaketa-indizea |
| log | erregistroa | a log file: erregistro-fitxategia; never "log in" |
| queue | ilara | job queue: lan-ilara |
| job / task | lana / zeregina | |
| kernel, core | nukleoa | |
| server | zerbitzaria | |
| database | datu-basea | |
| folder / directory | karpeta / direktorioa | |
| file | fitxategia | |
| image | irudia | thumbnail: miniatura |
| preview | aurrebista | |
| upload / download | igo / deskargatu | |
| export / import | esportatu / inportatu | |
| remove | kendu | take out of a list or place |
| delete | ezabatu | destroy; deleted: ezabatua |
| edit | editatu | |
| editor | editorea | person or software |
| save / store | gorde | the verb "Store" of the forms |
| Store (shop tab) | Denda | the shop, as "Shop" |
| cancel | utzi | |
| OK | Ados | |
| apply | aplikatu | |
| back | atzera | |
| next / previous | hurrengoa / aurrekoa | |
| enable / disable | gaitu / desgaitu | enabled / disabled: gaituta / desgaituta |
| active / inactive | aktiboa / inaktiboa | |
| run | exekutatu | |
| start / stop | hasi / gelditu | |
| session | saioa | |
| hidden / visible | ezkutuan / ikusgai | hide / reveal: ezkutatu / erakutsi |
| archive | artxiboa | |
| order (sort) | ordena | sort order: ordenatzeko irizpidea |
| order (shop) | eskaera | |
| shop | denda | |
| basket | saskia | |
| product | produktua | |
| customer | bezeroa | |
| discount | deskontua | |
| VAT | BEZ | |
| price / currency | prezioa / moneta | |
| tag | etiketa | |
| translation | itzulpena | |
| language | hizkuntza | main language: hizkuntza nagusia |
| locale | eskualde-ezarpena | |
| state | egoera | the object state |
| status | egoera | |
| view (button) | ikusi | a module view (noun): ikuspegia |
| format | formatua | |
| sort | ordenatu | |
| filter | iragazkia | to filter: iragazi |
| select | hautatu | select all: Hautatu guztiak; selected: hautatua |
| copy / move | kopiatu / lekuz aldatu | |
| add / create / new | gehitu / sortu / berria | |
| restore | leheneratu | |
| install / uninstall | instalatu / desinstalatu | |
| related objects | lotutako objektuak | relation: erlazioa |
| dashboard | aginte-panela | |
| identifier / ID | identifikatzailea / IDa | |
| priority | lehentasuna | |
| owner | jabea | |
| modified / created | aldatua / sortua | |
| date / time | data / ordua | |
| error / warning | errorea / abisua | |
| help | laguntza | |
| notification | jakinarazpena | |
| account | kontua | |
| default | lehenetsia | |
| link | esteka | |
| none / all | bat ere ez / guztiak | |
| yes / no | bai / ez | |
| name / title / description | izena / izenburua / deskribapena | |
| value / type / list | balioa / mota / zerrenda | |
| page / path | orria / bidea | |
| required / optional | derrigorrezkoa / aukerakoa | |
| options | aukerak | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Denda, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  Gorde.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Hasi saioa).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL ...) are listed with their reason in
  `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
