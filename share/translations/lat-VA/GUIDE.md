# Latin (lat-VA): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Latin in the plain prose of modern Latin use, as the Holy See and modern Latin software translations write it:
classical grammar, words from the classical and ecclesiastical stock, and new terms formed the way modern Latin
software translations form them (versio, fasciculus, nexus, tessera, situs interretialis). Prefer an established
Latin word with an extended sense over a borrowed English one; where a term is coined, keep it once chosen. No
other locale falls back to `lat-VA`; Vatican City stands as the country only because Latin has none of its own
(see `doc/eng-us/languages.md`, section 3).

## 2. Form of address

- **Buttons, menu items and links**: the infinitive, first letter capital: Servare, Abrogare, Removere, Recensere,
  Ad publicandum mittere, Selecta removere, In corbem movere. Not the imperative (serva).
- **Titles, column headings and tabs**: nouns in the nominative: Optiones, Translationes, Locus principalis.
- **Instructions**: the imperative singular, the user addressed with *tu*: "Rem quam selectionem praedefinitam esse
  vis selige, deinde preme "Fiat"." "Click" is *preme*.
- **Requests**: the imperative with *quaeso*: "Locum, quaeso, elige", "Exspecta, quaeso, dum prioritas servatur."
- **Confirmations**: *Visne vere ... ?* — "Visne vere hanc classem removere?". A plain question: *Visne ... ?*
  ("Visne classem <%class_name> exscribere?").
- **Statements about the user**: second person singular: "Linguam principalem mutare non potes ...", "Nulla
  signacula adhuc habes."; permissions with *licet*: "Tibi non licet classes removere."
- Not allowed: the plural *vos* (vultis, potestis), the polite third person, English loan words where the glossary
  has a Latin term.

## 3. Script and typography

- Latin letters without macrons, accents or ligatures (ae, oe; not æ, œ). Consonantal i is written i (iam, not
  jam); u and v are kept apart as in modern editions (Versio, Novum).
- **Capitals**: the first word of a button, title or sentence and proper names only; common nouns are lower case
  inside a sentence.
- **Quotation marks** as in the source: straight "..." where the source has them, “...” where it has those. The
  name of a button quoted in a sentence is the button's own translation: pugam "Abrogare" preme.
- **Punctuation**: as in English, comma before a relative or subordinate clause where it helps reading.
- **Numbers**: digits 0–9, not Roman numerals.
- **Counts**: the catalogue has no plural forms, and Latin agreement after a number is hard to keep with a
  placeholder. Write a label with the count: "Res: %count", "Res in summa: %count.", "Versiones in singulis paginis
  ostendere: %count."
- Abbreviations of the source that are not names (VAT) take a Latin form (VPA); format and product names stay.

## 4. Glossary

Binding for the kernel and every extension. Nouns are given in the nominative; the note gives the genitive
where the declension is not obvious.

| English | Latin | Note |
|---|---|---|
| content | contenta | plural noun; genitive contentorum; content object: res contenti |
| object | res | rei; the content object |
| node | nodus | |
| location | locus | main location: locus principalis |
| class | classis | the content class; class group: grex classium |
| attribute | attributum | |
| datatype | genus datorum | |
| version | versio | |
| draft | adumbratio | store draft: adumbrationem servare |
| publish | publicare | published: publicatum |
| send for publishing | ad publicandum mittere | |
| discard | abicere | |
| archived | in archivum relatum | |
| rejected | reiectum | |
| pending | pendens | |
| siteaccess | aditus situs | plural aditus situs |
| site / website | situs / situs interretialis | |
| section | sectio | the Exponential section |
| role | munus | muneris |
| policy | regula | |
| limitation | limitatio | |
| module | modulus | |
| function | functio | |
| user | usor | usoris; users: usores |
| user group | grex usorum | |
| anonymous users | usores anonymi | |
| login (the name) | nomen usoris | the field; username the same |
| log in / sign in | inire | |
| log out | exire | |
| password | tessera | |
| e-mail | epistula electronica | |
| cache | promptuarium | clear cache: promptuarium purgare |
| template | formula | |
| override | substitutio | override template: formula substitutionis |
| fallback | subsidium | adjective subsidiarius: catena subsidiaria |
| workflow | cursus operum | |
| trigger | excitator | |
| event | eventus | |
| trash | corbis | empty trash: corbem vacuefacere |
| bookmarks | signacula | |
| settings | optiones | configuration |
| setup | configuratio | the Setup tab |
| layout | dispositio | Exponential Layouts |
| block | segmentum | |
| zone | zona | |
| extension | extensio | |
| package | sarcina | |
| search index | index quaestionis | |
| search | quaerere | the noun: quaestio |
| search engine | machina quaerendi | |
| log | acta | plural noun; a log file; never "log in" |
| queue | ordo exspectationis | |
| job | opus | operis; jobs: opera; a cron job: opus statutum |
| script | scriptum | |
| worker | operarius | worker processes: processus operarii |
| kernel | nucleus | |
| folder | capsa | |
| file | fasciculus | |
| image | imago | thumbnail: imaguncula |
| preview | praevisio | |
| upload | imponere | |
| download | depromere | |
| export / import | exportare / importare | |
| remove | removere | take out of a list or place |
| delete | delere | destroy |
| edit | recensere | |
| editor | editor | the editing software; a person: redactor |
| save / store | servare | the verb "Store" of the forms |
| Store (shop tab) | taberna | the shop, as "Shop" |
| cancel | abrogare | |
| OK | fiat | |
| back | redire | |
| next / previous | sequens / prior | |
| enable / disable | activare / inactivare | |
| enabled / disabled | activum / inactivum | |
| run | exsequi | |
| hidden / visible | occultum / visibile | |
| archive | archivum | |
| order (sort) | ordo | |
| order (shop) | mandatum | orders: mandata |
| shop | taberna | webshop: taberna interretialis |
| basket | corbis emptionis | |
| product | merx | mercis |
| customer | emptor | |
| discount | deductio | |
| VAT | VPA | VAT type: genus VPA |
| tag | pittacium | keyword tag; an HTML tag the same |
| translation | translatio | to translate: vertere |
| language | lingua | main language: lingua principalis |
| locale | localizatio | |
| state | status | the object state; object states: status rerum |
| status | status | |
| view (button) | inspicere | |
| format | forma | |
| sort | ordinare | |
| filter | filtrare | |
| select | seligere | select all: omnia seligere |
| browse | perlustrare | |
| copy | exscribere | |
| move | movere | |
| add | addere | |
| create | creare | |
| new | novum | agrees with its noun: nova classis |
| restore | restituere | |
| install | instituere | |
| sub items | res subiectae | |
| subtree | subarbor | |
| related objects | res coniunctae | |
| URL alias | cognomen URL | |
| dashboard | tabula moderatoria | |
| content structure | structura contentorum | |
| media library | mediatheca | |
| database | basis datorum | |
| server | servitrum | |
| session | sessio | |
| identifier / ID | identificator / ID | |
| priority | prioritas | |
| owner | possessor | |
| modified / created | mutatum / creatum | |
| none | nullum | agrees with its noun |
| yes / no | ita / non | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is *Taberna*, the word the shop menu uses for "Shop". The verb "Store" of the
  forms (a button that saves) is *Servare*, never *Taberna*.
