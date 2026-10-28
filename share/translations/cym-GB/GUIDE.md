# Welsh (cym-GB): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Welsh (Cymraeg) in its standard written form, with the computing terms of the Welsh Government's term bank (Porth
Termau Cenedlaethol Cymru, TermCymru) and of the Welsh software localisations, in a neutral, polite register. No
other locale falls back to `cym-GB`.

- Write real Welsh, never leave the English word where Welsh has a term: "ffeil", "cyfrinair", "ffolder", "dolen",
  "defnyddiwr", "dileu", "cadw". English loan words only in their Welsh spelling where Welsh uses one (gweinydd,
  modiwl, pecyn, fersiwn, templed).
- **Mutations** follow the grammar (ei ffeil, ei ffeil hi, yn y ffeil, dau ddefnyddiwr, i'r wefan, o gynnwys). A
  placeholder, a product name or code never mutates; place it where no mutation is needed (after a colon, after a
  noun: "y defnyddiwr %name", "Enw: %name").
- Word order verb - subject - object; adjectives after the noun (cynnwys newydd, prif leoliad is the fixed exception
  with prif).
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **chi** (polite plural), as Welsh public-service and software texts do: "Ydych chi'n
  siŵr eich bod am ddileu'r gwrthrych hwn?", "Nid oes gennych ganiatâd i ...".
- **Buttons, menu items and links**: the verb-noun, as Welsh interfaces do: Cadw, Canslo, Dileu, Tynnu, Golygu,
  Ychwanegu, Creu, Chwilio, Dewis, Anfon, Gweld, Cyhoeddi, Llwytho i fyny, Llwytho i lawr, Gweithredu, Adfer.
- **Titles, column headings and tabs**: nouns or noun phrases: Gosodiadau, Cyfieithiadau, Prif leoliad, Enw.
- **Instructions**: the imperative of chi: "Dewiswch wrthrych a chliciwch \"Iawn\".", "os gwelwch yn dda" only where
  the source insists.
- **Confirmations and questions**: "Ydych chi'n siŵr eich bod am ...?".
- **What the user can or may do**: "Gallwch ...", "Ni allwch ..."; "You do not have permission" is "Nid oes gennych
  ganiatâd i ...".
- **Messages about a result**: the impersonal past: "Cadwyd y drafft.", "Llwythwyd y ffeil i fyny yn llwyddiannus.".

## 3. Script and typography

- **Latin** alphabet with the Welsh digraphs (ch, dd, ff, ng, ll, ph, rh, th) and the circumflex on vowels (â ê î ô
  û ŵ ŷ) and other diacritics where the standard writes them (caniatâd, siŵr, rhagolwg, crëwyd, copïo), as composed
  characters.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder and a placeholder is never mutated.
  The same for code, file names, INI settings and product names.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Apostrophe**: the straight ' in the elisions of Welsh (i'r, o'r, mae'n, a'u).
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Welsh | Note |
|---|---|---|
| content | cynnwys | content object: gwrthrych cynnwys |
| object | gwrthrych | plural: gwrthrychau |
| node | nod | plural: nodau; main node: prif nod; parent node: nod rhiant |
| location | lleoliad | main location: prif leoliad |
| item (list entry) | eitem | |
| tree | coeden | tree menu: dewislen coeden; content structure: strwythur cynnwys |
| class | dosbarth | content class: dosbarth cynnwys; class group: grŵp dosbarthiadau |
| attribute | priodoledd | plural: priodoleddau |
| datatype | math o ddata | |
| version | fersiwn | version history: hanes fersiynau |
| draft | drafft | store draft: Cadw'r drafft |
| publish | cyhoeddi | published: wedi'i gyhoeddi; publishing: cyhoeddi |
| send for publishing | anfon i'w gyhoeddi | |
| discard | gwaredu | discard draft: Gwaredu'r drafft |
| archived / pending / rejected | wedi'i archifo / yn aros / wedi'i wrthod | |
| siteaccess | mynediad i'r wefan | siteaccess as a setting name stays in code |
| site, website | gwefan | |
| section | adran | the Exponential section; an INI section: adran INI |
| role | rôl | plural: rolau |
| policy | polisi | plural: polisïau |
| limitation | cyfyngiad | |
| permission | caniatâd | access denied: gwrthodwyd mynediad |
| module | modiwl | |
| function | swyddogaeth | |
| user / user group | defnyddiwr / grŵp defnyddwyr | plural: defnyddwyr |
| group | grŵp | plural: grwpiau |
| anonymous | dienw | anonymous user: defnyddiwr dienw |
| administrator | gweinyddwr | administration interface: rhyngwyneb gweinyddu |
| username | enw defnyddiwr | |
| password | cyfrinair | |
| log in / log out | mewngofnodi / allgofnodi | |
| e-mail | e-bost | e-mail address: cyfeiriad e-bost |
| cache | storfa | clear cache: Clirio'r storfa |
| template | templed | override template: templed diystyru |
| override | diystyru | |
| fallback | wrth gefn | fallback language: iaith wrth gefn |
| design | dyluniad | |
| workflow | llif gwaith | |
| trigger | sbardun | |
| event | digwyddiad | |
| trash | sbwriel | empty trash: Gwagio'r sbwriel; move to trash: Symud i'r sbwriel |
| bookmark | nod tudalen | |
| settings | gosodiadau | configuration: ffurfweddiad |
| setup | gosod | the Setup tab: Gosod |
| layout | cynllun | Exponential Layouts stays |
| block / zone | bloc / parth | |
| extension | estyniad | |
| package | pecyn | |
| search | chwilio | search index: mynegai chwilio |
| log | cofnod | a log file: ffeil gofnodion; never "log in" |
| queue | ciw | job queue: ciw tasgau |
| job / task | tasg | |
| kernel, core | craidd | |
| server | gweinydd | |
| database | cronfa ddata | |
| folder / directory | ffolder / cyfeiriadur | |
| file | ffeil | |
| image | delwedd | thumbnail: bawdlun |
| preview | rhagolwg | |
| upload / download | llwytho i fyny / llwytho i lawr | |
| export / import | allforio / mewnforio | |
| remove | tynnu | take out of a list or place |
| delete | dileu | destroy; deleted: wedi'i ddileu |
| edit | golygu | |
| editor | golygydd | person or software |
| save / store | cadw | the verb "Store" of the forms |
| Store (shop tab) | Siop | the shop, as "Shop" |
| cancel | canslo | |
| OK | Iawn | |
| apply | gweithredu | |
| back | yn ôl | |
| next / previous | nesaf / blaenorol | |
| enable / disable | galluogi / analluogi | enabled / disabled: wedi'i alluogi / wedi'i analluogi |
| active / inactive | gweithredol / anweithredol | |
| run | rhedeg | |
| start / stop | cychwyn / atal | |
| session | sesiwn | |
| hidden / visible | cudd / gweladwy | hide / reveal: cuddio / datguddio |
| archive | archif | |
| order (sort) | trefn | sort order: trefn didoli |
| order (shop) | archeb | |
| shop | siop | |
| basket | basged | |
| product | cynnyrch | |
| customer | cwsmer | |
| discount | gostyngiad | |
| VAT | TAW | |
| price / currency | pris / arian cyfred | |
| tag | tag | allowlisted where it stands alone |
| translation | cyfieithiad | |
| language | iaith | main language: prif iaith |
| locale | locale | as a setting; in prose: gosodiadau lleol |
| state | cyflwr | the object state |
| status | statws | |
| view (button) | gweld | a module view (noun): golwg |
| format | fformat | |
| sort | didoli | |
| filter | hidlydd | to filter: hidlo |
| select | dewis | select all: Dewis y cyfan; selected: wedi'i ddewis |
| copy / move | copïo / symud | |
| add / create / new | ychwanegu / creu / newydd | |
| restore | adfer | |
| install / uninstall | gosod / dadosod | |
| related objects | gwrthrychau cysylltiedig | relation: perthynas |
| dashboard | dangosfwrdd | |
| identifier / ID | dynodydd / ID | |
| priority | blaenoriaeth | |
| owner | perchennog | |
| modified / created | addaswyd / crëwyd | |
| date / time | dyddiad / amser | |
| error / warning | gwall / rhybudd | |
| help | cymorth | |
| notification | hysbysiad | |
| account | cyfrif | |
| default | rhagosodedig | |
| link | dolen | |
| none / all | dim / y cyfan | |
| yes / no | ie / na | |
| name / title / description | enw / teitl / disgrifiad | |
| value / type / list | gwerth / math / rhestr | |
| page / path | tudalen / llwybr | |
| required / optional | gofynnol / dewisol | |
| options | opsiynau | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Siop, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  Cadw.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Mewngofnodi).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
