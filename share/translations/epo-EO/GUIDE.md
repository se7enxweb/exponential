# Esperanto (epo-EO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Esperanto following the Fundamento and the established computing vocabulary of the Esperanto software localisations
(Mozilla, LibreOffice, GNOME, KDE, Komputeko), in a neutral, polite register. No other locale falls back to `epo-EO`.

- Use the established Esperanto terms, never the English word: "dosiero", "dosierujo", "pasvorto", "ligilo",
  "uzanto", "forigi", "konservi", "servilo", "datumbazo", "retpoŝto".
- Grammar of the Fundamento: nouns in -o, adjectives in -a, plural -j, accusative -n (Elektu objekton.), adverbs in
  -e, the infinitive in -i, the past participle passive -ita (konservita, forigita).
- Word building with the standard affixes (mal-, re-, -ilo, -ujo, -ejo, -ado, -igi, -iĝi): malŝalti, reŝargi,
  redaktilo, rubujo, retejo, ordigado.
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **vi**: "Ĉu vi certe volas forigi ĉi tiun objekton?", "Vi ne havas permeson ...".
- **Buttons, menu items and links**: the infinitive: Konservi, Nuligi, Forigi, Forpreni, Redakti, Aldoni, Krei,
  Serĉi, Elekti, Sendi, Vidi, Publikigi, Alŝuti, Elŝuti, Apliki, Restarigi.
- **Titles, column headings and tabs**: nouns: Agordoj, Tradukoj, Ĉefa loko, Nomo.
- **Instructions**: the volitive (-u): "Elektu objekton kaj alklaku \"Bone\".".
- **Confirmations and questions**: "Ĉu vi certe volas ...?".
- **What the user can or may do**: "Vi povas ...", "Vi ne povas ...".
- **Messages about a result**: the passive participle: "La malneto estis konservita.", "Dosiero alŝutita.".

## 3. Script and typography

- **Latin** alphabet with the Esperanto letters ĉ ĝ ĥ ĵ ŝ ŭ (and capitals), as composed characters; never the
  x-system or h-system substitutes (cx, ch).
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder: no accusative -n and no plural
  -j on a placeholder; put a noun before it that carries the ending ("la uzanton %name", "Nomo: %name").
- **Product names and acronyms** take no ending; with a hyphen only where Esperanto practice writes one
  (URL-adreso, INI-dosiero, XML-etikedo).
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Esperanto | Note |
|---|---|---|
| content | enhavo | content object: enhavobjekto |
| object | objekto | |
| node | nodo | main node: ĉefa nodo; parent node: patra nodo |
| location | loko | main location: ĉefa loko |
| item (list entry) | ero | |
| tree | arbo | content structure: enhava strukturo |
| class | klaso | content class: enhavoklaso; class group: klasgrupo |
| attribute | atributo | |
| datatype | datumtipo | |
| version | versio | version history: versihistorio |
| draft | malneto | store draft: Konservi malneton |
| publish | publikigi | published: publikigita |
| send for publishing | sendi por publikigo | |
| discard | forĵeti | |
| siteaccess | retejaliro | siteaccess as a setting name stays in code |
| site, website | retejo | |
| section | sekcio | an INI section: INI-sekcio |
| role | rolo | |
| policy | politiko | |
| limitation | limigo | |
| permission | permeso | access denied: aliro rifuzita |
| module | modulo | |
| function | funkcio | |
| user / user group | uzanto / uzantogrupo | |
| anonymous | anonima | |
| administrator | administranto | administration interface: administra interfaco |
| username | uzantnomo | |
| password | pasvorto | |
| log in / log out | ensaluti / elsaluti | |
| e-mail | retpoŝto | e-mail address: retpoŝtadreso |
| cache | kaŝmemoro | clear cache: Malplenigi kaŝmemoron |
| template | ŝablono | override template: anstataŭiga ŝablono |
| override | anstataŭigo | to override: anstataŭigi |
| fallback | rezerva | fallback language: rezerva lingvo |
| design | fasono | |
| workflow | laborfluo | |
| trigger | ekigilo | |
| event | evento | |
| trash | rubujo | empty trash: Malplenigi rubujon |
| bookmark | legosigno | |
| settings | agordoj | configuration: agordaro |
| setup | agordado | the Setup tab: Agordado |
| layout | aranĝo | Exponential Layouts stays |
| block / zone | bloko / zono | |
| extension | etendaĵo | |
| package | pako | |
| search | serĉi | search index: serĉindekso |
| log | protokolo | log file: protokoldosiero |
| queue | atendovico | |
| job / task | tasko | cronjob: cron-tasko |
| handler | traktilo | |
| gateway (payment) | pagpordego | |
| feed (RSS) | fluo | |
| worker | laboranto | |
| kernel, core | kerno | |
| server | servilo | |
| database | datumbazo | |
| folder / directory | dosierujo | |
| file | dosiero | |
| image | bildo | thumbnail: miniaturo |
| preview | antaŭrigardo | |
| upload / download | alŝuti / elŝuti | |
| export / import | eksporti / importi | |
| remove | forpreni | take out of a list or place |
| delete | forigi | destroy |
| edit | redakti | |
| editor | redaktanto | person; the software: redaktilo |
| save / store | konservi | the verb "Store" of the forms |
| Store (shop tab) | Butiko | the shop, as "Shop" |
| cancel | nuligi | |
| OK | Bone | |
| apply | apliki | |
| back | reen | |
| next / previous | sekva / antaŭa | |
| enable / disable | ŝalti / malŝalti | enabled / disabled: ŝaltita / malŝaltita |
| active / inactive | aktiva / malaktiva | |
| run | ruli | |
| start / stop | starti / haltigi | |
| session | seanco | |
| hidden / visible | kaŝita / videbla | hide / reveal: kaŝi / montri |
| archive | arkivo | |
| order (sort) | ordo | sort order: ordiga ordo |
| order (shop) | mendo | |
| shop | butiko | |
| basket | korbo | |
| product | produkto | |
| customer | kliento | |
| discount | rabato | |
| VAT | AVI | aldonvalora imposto |
| price / currency | prezo / valuto | |
| tag | etikedo | |
| translation | traduko | |
| language | lingvo | main language: ĉefa lingvo |
| locale | lokaĵaro | |
| state | stato | the object state |
| status | statuso | |
| view (button) | vidi | a module view (noun): vido |
| sort | ordigi | |
| filter | filtrilo | to filter: filtri |
| select | elekti | selected: elektita |
| copy / move | kopii / movi | |
| add / create / new | aldoni / krei / nova | |
| restore | restarigi | |
| install / uninstall | instali / malinstali | |
| related objects | rilataj objektoj | relation: rilato |
| dashboard | panelo | |
| identifier / ID | identigilo / ID | |
| priority | prioritato | |
| owner | posedanto | |
| modified / created | modifita / kreita | |
| date / time | dato / tempo | |
| error / warning | eraro / averto | |
| help | helpo | |
| notification | sciigo | |
| account | konto | |
| default | defaŭlta | |
| link | ligilo | |
| none / all | neniu / ĉiuj | |
| yes / no | jes / ne | |
| name / title / description | nomo / titolo / priskribo | |
| value / type / list | valoro / tipo / listo | |
| page / path | paĝo / vojo | |
| required / optional | deviga / nedeviga | |
| options | opcioj | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Butiko, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Konservi.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Ensaluti).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
