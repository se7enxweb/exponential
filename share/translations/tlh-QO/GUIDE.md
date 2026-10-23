# Klingon (tlh-QO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Klingon (tlhIngan Hol) as described in Marc Okrand's *The Klingon Dictionary* and its later word lists, in the
style of the Klingon Language Institute. The vocabulary has no words for most software concepts; newer concepts are
descriptive compounds and verb-derived nouns built from existing words (`polwI'` "keeper", `nejmeH tetlh` "list for
searching", `Suy qach` "merchant building"). A coined term, once in the glossary, is used everywhere. No other
locale falls back to `tlh-QO`; `QO` is a private-use region and claims no country (see `doc/eng-us/languages.md`,
section 3). The translation is offered for fun and as a test of the interface, but it follows the grammar
seriously.

## 2. Form of address

Klingon is direct; there is no word for "please" and no polite form.

- **Buttons, menu items and links**: the imperative. `yI-` for no object or a singular object, `tI-` for a plural
  object: `yIpol` Save, `yIqIl` Cancel, `yIchoH` Edit, `yIteq` Remove, `wIvlu'ta'bogh tIteq` Remove selected,
  `veQDaq yIvIHmoH` Move to trash. The object comes before the verb.
- **Titles, column headings and tabs**: nouns: `DuHmey` Settings, `mughghachmey` Translations, `Daq'a'` Main
  location.
- **Instructions and requests**: the imperative alone; "Please" is left out: "Please choose a location" → `Daq
  yIwIv`; "Please wait until the priority is saved." → `potlh mI' pollu'meH yIloS.`
- **Confirmations**: the verb with `Da-` (you, singular object), `-qang` (willing) and the question suffix `-'a'`:
  "Are you sure you want to remove this class?" → `buvvam Dateqqang'a'?`. A plain question takes `-'a'` alone.
- **Statements about the user**: second person singular prefixes (`Da-`, `bI-`): "You do not have permission to
  remove classes." → `buvmey DateqmeH chaw' Daghajbe'.` Where no one is named as doing something, use the
  indefinite subject `-lu'`: `qonqa'lu'pu'`, `teqlu'pu'`.
- Not allowed: plural address (`Su-`, `pe-`), English "please", subject-verb-object order.

## 3. Script and typography

- **The Latin transcription only.** The Klingon script pIqaD is never used, not even for decoration in a text.
- **Letter case is part of the spelling**: `q` and `Q`, `h` and `H`, `D`, `I`, `S` are different letters or are
  always capital. Never capitalize the first letter of a button, title or sentence, and never lower-case a capital
  letter: `yIpol`, `De' pa'`, `Qagh`.
- **The apostrophe is a letter** (the glottal stop): always the straight ASCII `'` (U+0027), never `’`. It counts
  as part of the word: `lo'wI'`, `'ay'`. Next to a placeholder in single quotes, keep both: `'%class' DochvaD`.
- **Quotation marks** as in the source: straight "..." where the source has them, “...” where it has those.
- **Punctuation**: as in English.
- **Numbers**: digits 0–9, as in `eng-US`.
- **Plurals**: `-mey` for things, `-pu'` for beings who can speak (`lo'wI'pu'` users). A noun after a number takes no
  plural suffix: `%count Doch` (%count objects).
- Names, formats and settings stay as in the source (TRANSLATING.md, rule 5), with Klingon suffixes added directly
  where the grammar needs them: `cronvaD`, `site.ini ghItlhDaq`.

## 4. Glossary

Binding for the kernel and every extension.

| English | Klingon | Note |
|---|---|---|
| content | De' | content object: De' Doch |
| object | Doch | |
| node | Sor Daq | "tree place" |
| location | Daq | main location: Daq'a' |
| class | buv | the content class; class group: buv ghom |
| attribute | buv tlhegh | |
| datatype | tlhegh Segh | |
| version | chovnatlh | |
| draft | nab | store draft: nab yIpol |
| publish | HochvaD yI'ang | published: 'anglu'ta' |
| send for publishing | 'anglu'meH yIngeH | |
| discard | yIwoD | |
| archived | qunDaq pollu'ta' | |
| rejected | Qo'lu'ta' | |
| pending | loStaH | |
| siteaccess | yoS lojmIt | "site door" |
| site / website | yoS | |
| section | 'ay' | the Exponential section |
| role | patlh | |
| policy | chaw' chut | |
| limitation | veH | |
| module | ngoq 'ay' | |
| function | vangghach | |
| user | lo'wI' | users: lo'wI'pu' |
| user group | lo'wI' ghom | |
| anonymous | pong Hutlh | anonymous users: pong Hutlhbogh lo'wI'pu' |
| administrator | loHwI' | |
| login (the name) | 'elmeH pong | the field |
| username | lo'wI' pong | |
| log in / sign in | yI'el | |
| log out | yImej | |
| password | pegh mu' | |
| e-mail | De'wI' QIn | |
| cache | De' polwI' | clear cache: De' polwI' yIchImmoH |
| template | mIw 'echlet | |
| override | nIvwI' | override template: mIw 'echlet nIvwI' |
| fallback | tam | a fallback language: tam Hol |
| workflow | Qu' mIw | |
| trigger | taghwI' | |
| event | wanI' | |
| trash | veQ | empty trash: veQ yIchImmoH |
| bookmarks | qawmeH Daqmey | |
| settings | DuHmey | configuration |
| setup | SeHlaw | the Setup tab |
| layout | mIch mIw | Exponential Layouts; layouts: mIch mIwmey |
| block | ngogh | |
| zone | mIch | |
| extension | chelwI' | |
| package | 'aplo' | |
| search index | nejmeH tetlh | |
| search | yInej | the noun: nejghach |
| search engine | nejmeH QuQ | |
| log | wanI' tetlh | "list of events"; never "log in" |
| queue | loSwI' tetlh | job queue: Qu' loSwI' tetlh |
| job | Qu' | a cron job: poH Qu' |
| script | ngoq ghItlh | |
| worker | vumwI' | worker processes: vumwI'mey |
| kernel | ngoq botlh | |
| folder | ghItlh pa' | |
| file | ghItlh | |
| image | mIllogh | thumbnail: mIlloghHom |
| preview | nungbogh mIllogh | |
| upload | yIlab | |
| download | yIHev | |
| export / import | yIlel / yIqem | |
| remove | yIteq | take out of a list or place |
| delete | yIQaw' | destroy |
| edit | yIchoH | |
| editor | choHwI' | |
| save / store | yIpol | the verb "Store" of the forms |
| Store (shop tab) | Suy qach | the shop, as "Shop" |
| cancel | yIqIl | |
| OK | luq | |
| back | yIchegh | |
| next / previous | yIghoS / nungbogh | |
| enable / disable | yIchu' / yIchu'Ha' | |
| enabled / disabled | chu'lu'ta' / chu'Ha'lu'ta' | |
| run | yIQapmoH | running: QaptaH |
| hidden / visible | So'lu' / So'lu'be' | |
| archive | polmeH pa' | |
| order (sort) | tetlh mIw | |
| order (shop) | je'meH ghItlh | orders: je'meH ghItlhmey |
| shop | Suy qach | |
| basket | je'meH 'aplo' | |
| product | ngevlu'bogh Doch | |
| customer | je'wI' | |
| discount | DIlmeH mI' nup | |
| VAT | VAT | stays as in the source |
| tag | per | keyword tag and HTML tag alike |
| translation | mughghach | to translate: yImugh |
| language | Hol | main language: Hol'a' |
| locale | Hol Sep | |
| state | Dotlh | the object state; object states: Doch Dotlhmey |
| status | Dotlh | |
| view (button) | yIlegh | |
| format | ghItlh Segh | |
| sort | tetlh mIw yIchoH | |
| filter | yISay'moH | |
| select | yIwIv | select all: Hoch yIwIv |
| browse | yIleng | |
| copy | yIqonqa' | |
| move | yIvIHmoH | |
| add | yIchel | |
| create | yIchenmoH | |
| new | chu' | after its noun: buv chu' |
| restore | yIcheghmoH | |
| install / uninstall | yIjom / yIjomHa' | |
| sub items | puq Dochmey | |
| subtree | SorHom | |
| related objects | rarlu'bogh Dochmey | |
| URL alias | URL pong latlh | |
| dashboard | jIH | |
| content structure | De' qach | |
| media library | mIllogh qach | |
| database | De' pa' | |
| server | jabwI' | |
| session | poH | |
| identifier | ngu'wI' | ID: ngu'meH mI' |
| priority | potlh mI' | |
| owner | ghajwI' | |
| modified / created | choHlu'ta' / chenmoHlu'ta' | |
| none | pagh | |
| yes / no | HISlaH / ghobe' | |
| error | Qagh | |
| warning | ghuHmoHghach | |
| help | QaH | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is `Suy qach`, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is `yIpol`.
- An extension is named `chelwI'` everywhere, also on the help dashboard.
