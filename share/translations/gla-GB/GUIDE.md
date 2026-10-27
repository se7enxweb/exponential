# Scottish Gaelic (gla-GB): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Scottish Gaelic (Gàidhlig) in the spelling of the Gaelic Orthographic Conventions (GOC, Bòrd na Gàidhlig / SQA),
in the neutral, technical register of established Gaelic software localisation (the Akerbeltz terminology, used in
the Gaelic versions of Firefox, Thunderbird, LibreOffice and Microsoft products). The locale is `gla-GB`. No other
locale falls back to it. Irish (`gle-IE`) is a related but different language: its catalogue is no model for
words or spelling.

- **Grave accents only.** GOC writes à, è, ì, ò, ù; never the acute (á, é, ó) of older texts.
- Words of computing take the form of the Akerbeltz terminology (frithealaiche, stòr-dàta, faidhle, pasgan,
  tasgadan). English words stay only where they are names or code (see TRANSLATING.md, rule 5).
- Mutations follow the grammar: lenition after the article and in the genitive (buidheann chleachdaichean,
  ainm a' chleachdaiche), slenderisation where the genitive requires it.

## 2. Form of address

The interface addresses the user with the polite plural **sibh** (you), **ur** / **ur n-** (your), **leibh**,
**dhuibh**, **oirbh**, as in formal Gaelic writing.

- **Buttons, menu items and links**: the short imperative (the verbal root), as Gaelic software writes its
  commands: Sàbhail, Sguir dheth, Sguab às, Deasaich, Foillsich, Cuir dhan sgudal, "Thoir air falbh na chaidh a
  thaghadh" (impersonal, never "na thagh thu"). A command naming an object takes it as a noun phrase: "Cruthaich
  clas ùr", "Luchdaich suas faidhle".
- **Titles, column headings and tabs**: nouns or verbal nouns: Roghainnean, Eadar-theangachaidhean, Prìomh-ionad,
  Foillseachadh.
- **Instructions** in sentences: the polite plural imperative: "Taghaibh an nì agus briogaibh air “Ceart ma-thà”.",
  "Feithibh gus an tèid am prìomhachas a shàbhaladh.". A reference ("see ...") is "Faicibh ...".
- **Confirmations and questions**: "A bheil sibh cinnteach gu bheil sibh airson ... ?" or "A bheil sibh airson ...
  ?": "A bheil sibh airson an clas seo a sguabadh às?".
- **What the user can or may do**: "Faodaidh sibh ...", "Chan fhaod sibh ...", "Chan urrainn dhuibh ...". "You do
  not have permission" is "Chan eil cead agaibh ...".
- Not allowed: the familiar **thu** / **do** in sentences, slash forms.

## 3. Script and typography

- **Latin script** with the grave accents à è ì ò ù (and capitals À È Ì Ò Ù) as single precomposed characters
  (Unicode NFC).
- **Hyphens** as GOC sets them: after the article and particles (an t-ainm, a-steach, a-mach, a-nis), in
  compounds (facal-faire, stòr-dàta, ainm-cleachdaiche). A compound with Latin text, digits or a placeholder
  keeps the Latin part unchanged and puts the Gaelic noun before it: "faidhle PHP", "còd HTML", "alias URL".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before
  and after them, or a punctuation mark right after. Never a mutation or a letter glued to a placeholder: "%count
  oibseactan", "ainm: %name". Code, file names, INI settings and product names stay unchanged too.
- **Quotation marks**: the typographic “...” (and ‘...’ inside them) where the source has its own quotation marks
  in text; where the source has `&quot;`, a straight " or ' that is code or an entity, keep it exactly.
- **Apostrophes** of the grammar (a', 'S, 's, de'n) are the straight ASCII apostrophe, as Gaelic localisation
  writes them, so that they never break a placeholder or code.
- **Punctuation**: as in the source (comma, full stop, colon, semicolon, question and exclamation mark); no space
  before a colon or a question mark.
- **Numbers**: digits 0-9. The catalogue has no plural forms; a label with a count is used: "Oibseactan: %count".
- **Capitals**: only the first word of a button, title or heading starts with a capital, apart from names.

## 4. Glossary

Binding for the kernel and every extension.

| English | Scottish Gaelic | Note |
|---|---|---|
| content | susbaint | genitive susbainte |
| content object | oibseact susbainte | |
| object | oibseact | plural oibseactan |
| node | nòd | plural nòdan; main node: prìomh-nòd; parent node: nòd-pàraint |
| location | ionad | plural ionadan; main location: prìomh-ionad |
| item (list entry) | nì | plural nithean |
| class | clas | the content class, plural clasaichean; class group: buidheann chlasaichean; a PHP class: clas PHP |
| content class | clas susbainte | |
| attribute | buadh | plural buadhan |
| datatype | seòrsa dàta | |
| version | tionndadh | plural tionndaidhean; version history: eachdraidh nan tionndaidhean |
| draft | dreachd | plural dreachdan; store draft: sàbhail an dreachd |
| publish | foillsich | published: foillsichte; publishing: foillseachadh |
| send for publishing | cuir airson foillseachadh | |
| discard | tilg air falbh | discard draft: tilg air falbh an dreachd |
| archived | san tasglann | |
| pending | ri dhèiligeadh | |
| rejected | air a dhiùltadh | |
| siteaccess | inntrigeadh làraich | plural inntrigidhean làraich |
| site, website | làrach, làrach-lìn | |
| section | earrann | the Exponential section; plural earrannan |
| role | dreuchd | plural dreuchdan |
| policy | poileasaidh | plural poileasaidhean |
| limitation | cuingeachadh | plural cuingeachaidhean |
| permission | cead | plural ceadan |
| module | mòideal | plural mòidealan |
| function | foincsean | plural foincseanan |
| user | cleachdaiche | plural cleachdaichean |
| user group | buidheann chleachdaichean | |
| group | buidheann | plural buidhnean |
| anonymous | gun ainm | |
| administrator | rianaire | plural rianairean |
| administration (interface) | rianachd | |
| username | ainm-cleachdaiche | |
| password | facal-faire | plural faclan-faire |
| log in / sign in | clàraich a-steach | the noun: clàradh a-steach |
| log out / sign out | clàraich a-mach | |
| e-mail | post-d | e-mail address: seòladh puist-d |
| cache | tasgadan | clear the cache: falamhaich an tasgadan |
| template | teamplaid | plural teamplaidean |
| override | tar-àithne | an override template: teamplaid tar-àithne |
| fallback | roghainn-èiginn | fallback language: cànan-èiginn |
| design | dealbhadh | |
| workflow | sruth-obrach | plural sruthan-obrach |
| trigger | spreigeadh | plural spreigidhean |
| event | tachartas | plural tachartasan |
| trash | sgudal | move to trash: cuir dhan sgudal |
| bookmark | comharra-lìn | plural comharran-lìn |
| settings | roghainnean | |
| setup | suidheachadh | |
| layout | co-dhealbhachd | Exponential Layouts; kept apart from the page layout |
| block | bloca | plural blocaichean |
| zone | raon | plural raointean |
| extension | leudachan | plural leudachain |
| package | pacaid | plural pacaidean |
| search | lorg | the noun: lorg; search results: toraidhean an luirg |
| log | loga | plural logaichean; never "log in" |
| queue | ciutha | |
| job / task | obair / saothair | plural obraichean / saothraichean |
| cron | cron | |
| script | sgriobt | plural sgriobtaichean |
| worker | obraiche | plural obraichean |
| kernel, core | eitean | |
| server | frithealaiche | plural frithealaichean |
| database | stòr-dàta | plural stòran-dàta |
| folder | pasgan | plural pasganan |
| file | faidhle | plural faidhlichean |
| image | dealbh | plural dealbhan |
| preview | ro-shealladh | |
| upload | luchdaich suas | the noun: luchdadh suas |
| download | luchdaich a-nuas | the noun: luchdadh a-nuas |
| export / import | às-phortaich / ion-phortaich | nouns: às-phortadh / ion-phortadh |
| remove | thoir air falbh | take out of a list or place |
| delete | sguab às | destroy; the noun: sguabadh às |
| edit | deasaich | the noun: deasachadh |
| editor | deasaiche | the person or the software |
| save / store | sàbhail | |
| Store (shop tab) | bùth | |
| cancel | sguir dheth | |
| OK | Ceart ma-thà | |
| apply | cuir an sàs | |
| back | air ais | |
| next / previous | air adhart / air ais | the next one: an ath-fhear |
| enable / disable | cuir an comas / cuir à comas | |
| enabled / disabled | an comas / à comas | |
| active / inactive | gnìomhach / neo-ghnìomhach | |
| run | ruith | |
| start / stop | tòisich / cuir stad air | |
| session | seisean | plural seiseanan |
| hidden / visible | falaichte / ri fhaicinn | hide: falaich; reveal: seall |
| archive | tasglann | a file archive: tasglann fhaidhlichean |
| order (sort) | òrdugh | sort order: òrdugh an t-seòrsachaidh |
| order (shop) | òrdugh | plural òrdughan; in the shop context only |
| shop | bùth | |
| basket | basgaid | |
| product | bathar | an item of goods: nì bathair |
| customer | custamair | plural custamairean |
| discount | lasachadh | |
| VAT | VAT | cìs luach leasaichte where it is spelled out |
| price | prìs | plural prìsean |
| currency | airgeadra | plural airgeadraidhean |
| tag | taga | a keyword tag; an HTML tag: taga HTML |
| translation | eadar-theangachadh | plural eadar-theangachaidhean |
| language | cànan | plural cànanan |
| locale | sgeama ionadail | |
| state | staid | the object state; plural staidean |
| status | inbhe | |
| view (button) | seall | the module view: sealladh |
| format | fòrmat | text formatting: fòrmatadh |
| sort | seòrsaich | |
| filter | criathraich | the noun: criathrag |
| select | tagh | selected: air a thaghadh |
| browse | brabhsaich | |
| copy | dèan lethbhreac | the noun: lethbhreac |
| move | gluais | |
| add | cuir ris | |
| create | cruthaich | |
| new | ùr | |
| restore | aisig | |
| install / uninstall | stàlaich / dì-stàlaich | |
| sub items | fo-nithean | |
| subtree | fo-chraobh | |
| related objects | oibseactan co-cheangailte | |
| URL alias | alias URL | |
| redirect | ath-stiùireadh | |
| dashboard | deas-bhòrd | |
| content structure | structar na susbainte | |
| media library | leabharlann nam meadhanan | |
| identifier / ID | aithnichear / ID | |
| priority | prìomhachas | |
| owner | sealbhadair | |
| modified / created | atharraichte / cruthaichte | |
| date / time | ceann-là / àm | |
| error / warning | mearachd / rabhadh | |
| help | cobhair | |
| notification | brath | plural brathan |
| wizard | draoidh | |
| feed (RSS) | inbhir | feed reader: leughadair inbhirean |
| tab | taba | plural tabaichean |
| audit, audit trail | sgrùdadh, lorg sgrùdaidh | |
| repository | ionad-tasgaidh | |
| checksum | suim dhearbhaidh | |
| account | cunntas | plural cunntasan |
| default | bunaiteach | default value: luach bunaiteach |
| toolbar | bàr-inneal | |
| container | soitheach | plural soithichean |
| AI | IF | inntleachd fhuadain where it is spelled out |
| pattern (name pattern) | pàtran | |
| wildcard | saoragan | |
| remote ID | ID cèin | |
| placeholder | glèidheadair-àite | |
| collection | cruinneachadh | information collector: cruinniche fiosrachaidh |
| approve, approval | aontaich, aontachadh | |
| software | bathar-bog | |
| creator / modifier | cruthadair / atharraichear | |
| request (HTTP) | iarrtas | plural iarrtasan |
| query | ceist | a database query: ceist an stòir-dàta |
| handler / provider / listener | làimhsichear / solaraiche / èisteachdair | |
| engine | einnsean | |
| release / upgrade | sgaoileadh / àrdachadh | |
| backup | lethbhreac-glèidhidh | |
| maintenance | obair-ghlèidhidh | |
| directory | pasgan | as folder |
| header (HTTP) | bann-cinn | |
| page layout (pagelayout template) | co-dhealbhachd na duilleige | |
| page | duilleag | plural duilleagan |
| passkey | iuchair-faire | plural iuchraichean-faire |
| two-step sign-in | clàradh a-steach dà-cheumnach | |
| device / browser | uidheam / brabhsair | |
| link | ceangal | plural ceanglaichean |
| registration, register | clàradh, clàraich | |
| payment gateway | cachaileith pàighidh | |
| none | chan eil gin | "no ...": gun ... |
| yes / no | tha / chan eil | as Gaelic software answers |
| name | ainm | plural ainmean |
| description | tuairisgeul | |
| title | tiotal | |
| type | seòrsa | |
| value | luach | plural luachan |
| list | liosta | |
| close | dùin | |
| open | fosgail | |
| show | seall | |
| update | ùraich | |
| refresh | ath-nuadhaich | |
| confirm | dearbh | |
| send | cuir | |
| configuration | rèiteachadh | |
| permission denied | chan eil cead ann | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is bùth, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is sàbhail.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
