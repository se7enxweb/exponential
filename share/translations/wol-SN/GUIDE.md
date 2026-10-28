# Wolof (wol-SN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Urban Wolof of Senegal (Dakar), in the neutral, plain register of Wolof radio, newspapers and the Wolof
localisations of Google, Mozilla and Wikipedia. No other locale falls back to `wol-SN`; French (`fre-FR`) gives the
loan words for computing, as everyday Wolof does.

- Wolof words are used where they exist: denc, far, dindi, soppi, seet, tànn, yokk, sos, xool, làkk, jëfandikukat,
  baatu jàll, nataal, mbalit.
- Computing words without a Wolof one are French loans in Wolof spelling (CLAD orthography), as Wolof media write
  them: serwëer, fisie, dosie, kaas, modil, klaas, objee, wersiyoŋ, paket, skript, tampale, blokk, sonn, taag,
  lokal, disayn, foorma, fonksiyoŋ, imeel, nawigatëer. A word that has no settled Wolof spelling keeps the
  glossary's form; never a fresh French word in French spelling inside a Wolof sentence.
- Nouns take their class marker and determiner after them: fisie bi, fisie yi, jëfandikukat bi, jëfandikukat yi.

## 2. Form of address

The interface addresses the user with **yow** (you) and **sa** / **say** (your), as Wolof software does; there is
no separate polite pronoun.

- **Buttons, menu items and links**: the short imperative: Denc, Neenal, Far, Soppi, Siiwal, Yónne ngir siiwal,
  Dindi li ñu tànn, Yóbbu ci mbalit.
- **Titles, column headings and tabs**: nouns or noun phrases: Tànneef yi, Tekki yi, Bérab bu mag bi.
- **Instructions**: the imperative, softened with "Baal ma," or "Su la neexee," where a request needs it: "Tànnal
  benn objee te bësal «OK».", "Su la neexee, xaaral ...". A reference ("see ...") is "Xoolal ...".
- **Confirmations and questions**: "Ndax wóor na la ne bëgg nga far klaas bii?", "Ndax bëgg nga ...?".
- **What the user can or may do**: "Mën nga ...", "Mënuloo ...", or impersonal: "Mënu ñoo soppi làkk bu mag bi
  ...". "You do not have permission" is "Amuloo sañ-sañ ...".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the official Wolof alphabet of Senegal (decree of 1971, revised 2005, CLAD), the script of the shipped
  locale (`share/locale/wol-SN.ini`, LanguageName Wolof). The special letters are written with their own code
  points: à, é, ë, ó, ñ (U+00F1) and ŋ (U+014B). Long vowels and geminate consonants are written double (baax,
  dëkk, xool). The Wolofal (Arabic) script is not used here.
- **Latin text inside Wolof**: product names, codes and acronyms stay as they are, with a Wolof determiner after
  them where the sentence needs one: "fisie PHP bi", "adrees URL bi", "kood HTML bi".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "%count objee", not
  "%countobjee" or "%name bi" glued as "%namebi". Put the placeholder where it needs no determiner: "Objee: %name",
  "%count objee".
- **Numbers**: the number comes before the noun, which stays in the singular: "%count objee". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon.

## 4. Glossary

Binding for the kernel and every extension.

| English | Wolof | Note |
|---|---|---|
| content | li ci biir | content (noun in a phrase): li ci biir bi |
| content object | objeeu li ci biir | |
| object | objee | |
| node | noo | main node: noo bu mag bi; parent node: noo baay; child: noo doom |
| location | bérab | main location: bérab bu mag bi |
| item (list entry) | elemaa | |
| class | klaas | the content class; class group: mbooloou klaas; a PHP class: klaas PHP |
| content class | klaasu li ci biir | |
| attribute | atribi | |
| datatype | xeetu done | |
| version | wersiyoŋ | version history: jaar-jaaru wersiyoŋ yi |
| draft | brouyoŋ | store draft: Denc brouyoŋ bi |
| publish | siiwal | published: siiwalees na |
| send for publishing | Yónne ngir siiwal | |
| discard | sànni | discard draft: Sànni brouyoŋ bi |
| archived | ci ar sifu | |
| pending | mu ngi xaar | |
| rejected | ñu gàntu ko | |
| siteaccess | buntu sit | the configuration unit |
| site, website | sit, sit web | |
| section | xaaj | the Exponential section |
| role | wàll | |
| policy | politig | |
| limitation | dig | |
| permission | sañ-sañ | access rights: sañ-sañu dugg |
| module | modil | |
| function | fonksiyoŋ | |
| user | jëfandikukat | |
| user group | mbooloou jëfandikukat | |
| group | mbooloo | |
| anonymous | ku ñu xamul | anonymous user: jëfandikukat bu ñu xamul |
| administrator | saytukat | |
| administration (interface) | wàllu saytu | |
| username | turu jëfandikukat | the column "Login" too |
| password | baatu jàll | |
| log in / sign in | dugg | button: Dugg |
| log out / sign out | génn | button: Génn |
| e-mail | imeel | e-mail address: adreesu imeel |
| cache | kaas | clear cache: Setal kaas bi |
| template | tampale | |
| override | wuutu | override template: tampale buy wuutu |
| fallback | wuutukat | fallback language: làkk buy wuutu |
| design | disayn | |
| workflow | doxinu liggéey | |
| trigger | daldikat | |
| event | xew-xew | |
| trash | mbalit | empty trash: Setal mbalit mi |
| bookmark | màndarga | |
| settings | tànneef | |
| setup | defar | the Setup tab; installation: samp |
| layout | tëralin | Exponential Layouts |
| block | blokk | |
| zone | sonn | |
| extension | yokkute | |
| package | paket | |
| search | seet | button: Seet |
| log | téereb xew-xew | log file: fisieu xew-xew |
| queue | toog-toog | |
| job / task | liggéey | |
| cron | cron | cron job: liggéeyu cron |
| script | skript | |
| worker | liggéeykat | |
| kernel, core | xol | |
| server | serwëer | |
| database | dénkukaayu done | |
| folder | dosie | |
| file | fisie | |
| image | nataal | |
| preview | xool lu jiitu | |
| upload | yebal | button: Yebal |
| download | yebbi | button: Yebbi |
| export / import | génne / dugal | |
| remove | dindi | take out of a list or a place |
| delete | far | destroy |
| edit | soppi | |
| editor | soppikat | |
| save / store | denc | |
| Store (shop tab) | Bitig | the word the shop menu uses for "Shop" |
| cancel | neenal | |
| OK | OK | |
| apply | jëfandikoo | |
| back | dellu | |
| next / previous | ci topp / ci jiitu | |
| enable / disable | taal / fey | a setting or feature |
| enabled / disabled | taal na / fey na | |
| active / inactive | dox na / doxul | |
| run | doxal | a script or job |
| start / stop | tambali / taxawal | |
| session | sesiyoŋ | |
| hidden / visible | nëbbu / feeñ | hide / reveal: nëbb / wone |
| archive | ar sif | |
| order (sort) | toftaleef | sort order: toftaleefu tànn |
| order (shop) | komand | |
| shop | bitig | |
| basket | pañe | kept apart from mbalit |
| product | marsandiis | |
| customer | kiliyaan | |
| discount | wàññi | |
| VAT | TVA | the abbreviation used in Senegal |
| price | njëg | |
| currency | xaalis | |
| tag | taag | keyword tag; an HTML tag: taag HTML |
| translation | tekki | |
| language | làkk | main language: làkk bu mag bi |
| locale | lokal | |
| state | xaalaat | the object state; state group: mbooloou xaalaat |
| status | sitiwaasiyoŋ | |
| view (button) | xool | a module view: wone |
| format | foorma | |
| sort | toftale | |
| filter | sef | |
| select | tànn | selected: li ñu tànn |
| browse | wëndeelu | |
| copy | sotti | |
| move | tox | |
| add | yokk | |
| create | sos | |
| new | bees | |
| restore | delloo | |
| install / uninstall | samp / dindi samp | |
| sub items | elemaa yu ci suuf | |
| subtree | car | |
| related objects | objee yu ko jege | relation: lëkkalook |
| URL alias | turu URL bu wuute | |
| redirect | jubluwaat | |
| dashboard | tabalu saytu | |
| content structure | tëralinu li ci biir | |
| media library | dénkukaayu medya | |
| identifier / ID | màndarga xammee / ID | |
| priority | li jiitu | |
| owner | boroom | |
| modified / created | soppi nañu ko / sos nañu ko | |
| date / time | bés / waxtu | |
| error / warning | njuumte / artu | |
| help | ndimbal | |
| notification | yégle | |
| wizard | gindikat | |
| feed (RSS) | sóob | RSS feed: sóobu RSS |
| tab | tabb | |
| audit, audit trail | saytu, téereb saytu | |
| repository | dénkukaay | |
| checksum | xaymay seetlu | |
| account | kont | |
| default | li nekk ci suuf | default value: njëgu cosaan |
| toolbar | tabalu jumtukaay | |
| container | ndab | |
| AI | IA | the French abbreviation used in Senegal |
| pattern (name pattern) | royukaay | kept apart from tampale (template) |
| wildcard | màndarga wuutukat | |
| remote ID | ID bu sore | |
| placeholder | wuutukat | |
| collection | dajale | information collector: dajalekatu xibaar |
| approve, approval | nangu, nangoo | |
| software | prograam | |
| creator / modifier | sooskat / soppikat | |
| request (HTTP) | laaj | |
| query | laajte | search query: laajteb seet |
| handler / provider / listener | jëfekat / joxekat / déglukat | |
| engine | motëer | |
| release / upgrade | génne / yeesal | update: yeesal |
| backup | denc bu wér | |
| maintenance | topptoo | |
| directory | dosie | |
| header (HTTP) | bopp | |
| page layout (pagelayout template) | tampaleu xët | kept apart from tëralin (Exponential Layouts) |
| passkey | caabiy dugg | |
| two-step sign-in | dugg ci ñaari tappal | |
| device / browser | jumtukaay / nawigatëer | |
| link | lëkkalekaay | |
| registration, register | bind sa tur | |
| payment gateway | bunt fey | |
| form | formileer | |
| comment | xalaat | |
| newsletter / subscription | lëttu xibaar / abonmaa | |
| field | tool | |
| value | njëg | |
| list | lim | |
| page | xët | |
| title / name | tur / tur | the column "Name": Tur; title (heading): kàddu bopp |
| description | melokaan | |
| type | xeet | |
| path | yoon | |
| width / height | yaatuwaay / kawewaay | |
| size | dayo | |
| none | dara | |
| yes / no | waaw / déedéet | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Bitig, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Denc.
- "Delete" is Far (destroy) and "remove" is Dindi (take out), so that the two buttons are never confused.
- "Price" and "value" are both njëg in Wolof; the context tells them apart.
