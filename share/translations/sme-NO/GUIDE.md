# Northern Sami (sme-NO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Northern Sami (davvisámegiella) in the common written standard used in Norway, Sweden and Finland, with the computing
terms of the Sami software localisations (Divvun/Giellatekno, the Sami Parliament's term work, Risten) in a neutral,
friendly register. No other locale falls back to `sme-NO`.

- Write real Sami, never leave the Norwegian, Finnish or English word where Sami has a term: "fiila", "beassansátni",
  "máhppa", "liŋka", "geavaheaddji", "sihkkut", "vurket", "bálvá", "diehtovuođđu". Norwegian forms are wrong here
  ("fil", "bruker", "passord", "lagre", "slett").
- Inflection follows the grammar (fiila, fiilla, fiillas; geavaheaddji, geavaheaddjit, geavaheddjiid), with the
  consonant gradation of the standard. A placeholder, a product name or code is never inflected; place it where no
  case ending is needed (after a colon, after a noun: "geavaheaddji %name", "Namma: %name").
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **don** (singular), as Sami software does: "Háliidatgo duođas sihkkut dán objeavtta?",
  "Dus ii leat lohpi ...".
- **Buttons, menu items and links**: the infinitive: Vurket, Gaskkalduhttit, Sihkkut, Váldit eret, Rievdadit,
  Lasihit, Ráhkadit, Ohcat, Válljet, Sáddet, Čájehit, Almmuhit, Luđet bajás, Viežžat, Geavahit, Máhcahit.
- **Titles, column headings and tabs**: nouns: Heivehusat, Jorgalusat, Váldosajádat, Namma.
- **Instructions**: the imperative singular: "Vállje objeavtta ja coahkkal \"OK\".".
- **Confirmations and questions**: "Háliidatgo duođas ...?".
- **What the user can or may do**: "Don sáhtát ...", "Don it sáhte ..." (the negation verb inflects: in, it, ii).
- **Messages about a result**: the passive: "Luonos vurkejuvvui.", "Fiila luđejuvvui bajás.".

## 3. Script and typography

- **Latin** alphabet with the Sami letters á č đ ŋ š ŧ ž (and capitals Á Č Đ Ŋ Š Ŧ Ž), as composed characters; never
  substitutes such as "c'" or "d-".
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder and no case ending is added.
- **Compounds**: a Sami compound is one word (geavaheaddjinamma, váldosajádat); with a code or abbreviation a hyphen
  and the case on the abbreviation with a colon where the standard writes it: URL-čujuhus, XML-gilkor, INI-fiila.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Northern Sami | Note |
|---|---|---|
| content | sisdoallu | content object: sisdoalloobjeakta |
| object | objeakta | plural: objeavttat |
| node | čuolbma | plural: čuolmmat; main node: váldočuolbma; parent node: váhnenčuolbma |
| location | sajádat | main location: váldosajádat |
| item (list entry) | elemeanta | |
| tree | muorra | content structure: sisdoallostruktuvra |
| class | luohkká | content class: sisdoalloluohkká; class group: luohkkájoavku |
| attribute | attribuhtta | |
| datatype | dáhtašládja | |
| version | veršuvdna | version history: veršuvdnahistorjá |
| draft | luonos | store draft: Vurket luonosa |
| publish | almmuhit | published: almmuhuvvon |
| send for publishing | sáddet almmuhussii | |
| discard | hilgut | |
| siteaccess | neahttabáikebeassan | siteaccess as a setting name stays in code |
| site, website | neahttabáiki | |
| section | oassi | an INI section: INI-oassi |
| role | rolla | |
| policy | njuolggadus | |
| limitation | ráddjen | |
| permission | lohpi | access denied: beassan biehttaluvvon |
| module | moduvla | |
| function | doaibma | |
| user / user group | geavaheaddji / geavaheaddjijoavku | plural: geavaheaddjit |
| group | joavku | |
| anonymous | anonyma | |
| administrator | hálddašeaddji | administration interface: hálddašanlakta |
| username | geavaheaddjinamma | |
| password | beassansátni | |
| log in / log out | čálihit sisa / čálihit olggos | |
| e-mail | e-poasta | e-mail address: e-poastačujuhus |
| cache | gaskavuorká | clear cache: Guorrut gaskavuorká |
| template | málle | override template: badjelčállinmálle |
| override | badjelčállit | |
| fallback | várre- | fallback language: várregiella |
| design | hábmen | |
| workflow | bargogoansta | |
| trigger | álggaheaddji | |
| event | dáhpáhus | |
| trash | ruskalihtti | empty trash: Guorrut ruskalihtti |
| bookmark | girjemearka | |
| settings | heivehusat | configuration: konfigurašuvdna |
| setup | ásaheapmi | the Setup tab: Ásaheapmi |
| layout | hápmi | Exponential Layouts stays |
| block / zone | blohkka / guovlu | |
| extension | lasáhus | |
| package | páhkka | |
| search | ohcat | search index: ohcanindeaksa |
| log | logga | log file: loggafiila |
| queue | vuordinláhkki | |
| job / task | bargu | cronjob: cron-bargu |
| handler | gieđahalli | never the English word |
| gateway (payment) | máksinuksa | |
| feed (RSS) | fiidda | |
| worker | bargoproseassa | |
| hit / miss (cache) | deaividus / boasttuvuohta | never Finnish osuma |
| kernel, core | váibmu | |
| server | bálvá | |
| database | diehtovuođđu | |
| folder / directory | máhppa | |
| file | fiila | |
| image | govva | thumbnail: unnagovva |
| preview | ovdačájeheapmi | |
| upload / download | luđet bajás / viežžat | |
| export / import | olggosfievrridit / sisafievrridit | |
| remove | váldit eret | take out of a list or place |
| delete | sihkkut | destroy |
| edit | rievdadit | |
| editor | doaimmaheaddji | person; the software: doaimmaheaddjiprográmma |
| save / store | vurket | the verb "Store" of the forms |
| Store (shop tab) | Gávpi | the shop, as "Shop" |
| cancel | gaskkalduhttit | |
| OK | OK | |
| apply | geavahit | |
| back | ruovttoluotta | |
| next / previous | boahtte / ovddit | |
| enable / disable | doaibmahit / heaittihit | enabled / disabled: doaibmá / ii doaimma |
| active / inactive | aktiivvalaš / passiivvalaš | |
| run | vuodjit | |
| start / stop | álggahit / bisuhit | |
| session | sešuvdna | |
| hidden / visible | čihkosis / oidnosis | hide / reveal: čiehkat / čájehit |
| archive | arkiiva | |
| order (sort) | ortnet | sort order: sorterenortnet |
| order (shop) | diŋgojupmi | |
| shop | gávpi | |
| basket | oastinkorga | |
| product | buvtta | |
| customer | oasti | |
| discount | hálbideapmi | |
| VAT | MVD | meirárvodivat |
| price / currency | haddi / valuhtta | |
| tag | gilkor | |
| translation | jorgalus | |
| language | giella | main language: váldogiella |
| locale | locale | as a setting; in prose: guovlluheivehusat |
| state | dilli | the object state |
| status | stáhtus | |
| view (button) | čájehit | a module view (noun): oaidnu |
| sort | sorteret | |
| filter | sillu | to filter: silludit |
| select | válljet | selected: válljejuvvon |
| copy / move | máŋget / sirdit | |
| add / create / new | lasihit / ráhkadit / ođđa | |
| restore | máhcahit | |
| install / uninstall | sajáiduhttit / sihkkut sajáiduhttima | |
| related objects | čadnon objeavttat | relation: oktavuohta |
| dashboard | dieđáhusbeavdi | |
| identifier / ID | identifiseaddji / ID | |
| priority | vuoruheapmi | |
| owner | eaiggát | |
| modified / created | rievdaduvvon / ráhkaduvvon | |
| date / time | beaivemearri / áigi | |
| error / warning | meattáhus / várrehus | |
| help | veahkki | |
| notification | dieđáhus | |
| account | konto | |
| default | standárda | |
| link | liŋka | |
| none / all | ii oktage / buot | |
| yes / no | juo / ii | |
| name / title / description | namma / bajilčála / válddahus | |
| value / type / list | árvu / šládja / listu | |
| page / path | siidu / bálggis | |
| required / optional | bákkolaš / eaktodáhtolaš | |
| options | molssaeavttut | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Gávpi, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Vurket.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Čálihit sisa).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, OK ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
