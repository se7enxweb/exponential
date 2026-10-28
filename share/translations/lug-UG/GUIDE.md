# Ganda (lug-UG): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Luganda as used in Uganda, in the neutral, plain register of Luganda newspapers (Bukedde), radio
and the Luganda localisations of Google and Mozilla. No other locale falls back to `lug-UG`; Swahili (`swa-KE`) and
Kinyarwanda (`kin-RW`) are related Bantu languages and only help with terminology, as English does for loan words.

- Luganda words are used where they exist: tereka, sangula, ggyamu, longoosa, noonya, entegeka, fayiro, fooda,
  ekifaananyi, olulimi, omukozesa, ekigambo ky'ekyama.
- Computing words without a Luganda one are English loans in Luganda spelling, as Ugandan media write them: seeva,
  databeesi, kiraasi, moduli, sikulipiti, tempuleeti, pakeeji, bbulooka, zooni, nodi, kaasi, logi, tagi, lokale,
  dizayini, fomati, fankisoni, imeeyilo, bulawuza.
- Nouns take the class prefix and the words that agree with them follow it: ekintu ekipya, ebintu ebipya,
  fayiro empya, omukozesa omupya, abakozesa abapya.

## 2. Form of address

The interface addresses the user with the singular **ggwe** (you) and the possessive **-o** (wo, kyo, lyo ...), as
Luganda software does; politeness is carried by "Bambi" / "Nsaba" where a request needs it.

- **Buttons, menu items and links**: the short imperative singular: Tereka, Sazaamu, Sangula, Longoosa, Fulumya,
  Weereza okufulumizibwa, Ggyamu ebirondeddwa, Teeka mu kasasiro.
- **Titles, column headings and tabs**: nouns or noun phrases: Entegeka, Enzivuunula, Ekifo ekikulu.
- **Instructions**: the subjunctive or imperative, with "Bambi" for a request: "Londa ekintu onyige «OK».",
  "Bambi linda ...". A reference ("see ...") is "Laba ...".
- **Confirmations and questions**: "Oli mukakafu nti oyagala okusangula kiraasi eno?", "Oyagala ...?".
- **What the user can or may do**: "Osobola ...", "Tosobola ...", or impersonal: "Olulimi olukulu terusobola
  kukyusibwa ...". "You do not have permission" is "Tolina lukusa ...".
- Not allowed: slash forms (o/mu).

## 3. Script and typography

- **Latin**, the Luganda alphabet of the shipped locale (`share/locale/lug-UG.ini`, LanguageName Luganda) and of the
  1947 orthography in use today. Long vowels and geminate consonants are written double (okuteeka, ekkomo,
  ggwe); the velar nasal is **ŋ** (U+014B) and is written as ŋ, not "ng'": ŋŋaana. No tone marks.
- **Latin text inside Luganda**: product names, codes and acronyms stay as they are. Agreement after an acronym
  follows the noun before it: "fayiro ya PHP", "endagiriro ya URL", "koodi ya HTML".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "ebintu %count", not
  "%countebintu". Put the placeholder where it needs no prefix: "Ekintu: %name", "ebintu %count".
- **Numbers**: the noun comes first and the number after it, as Luganda does: "ebintu %count", "fayiro %count".
  Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source. A label
  keeps its colon.

## 4. Glossary

Binding for the kernel and every extension.

| English | Luganda | Note |
|---|---|---|
| content | ebirimu | |
| content object | ekintu ky'ebirimu | |
| object | ekintu | plural ebintu |
| node | nodi | main node: nodi enkulu; parent node: nodi ey'oku ntikko; child: nodi ento |
| location | ekifo | main location: ekifo ekikulu |
| item (list entry) | ekiyingizo | plural ebiyingizo |
| class | kiraasi | the content class; class group: ekibinja kya kiraasi; a PHP class: kiraasi ya PHP |
| content class | kiraasi y'ebirimu | |
| attribute | ekikula | plural ebikula |
| datatype | ekika kya data | |
| version | enkyusa | version history: ebyafaayo by'enkyusa |
| draft | ddulaafuti | store draft: Tereka ddulaafuti |
| publish | okufulumya | button: Fulumya; published: ekyafulumizibwa |
| send for publishing | Weereza okufulumizibwa | |
| discard | okusuula | button: Suula; discard draft: Suula ddulaafuti |
| archived | ekiri mu tterekero | |
| pending | ekirindiridde | |
| rejected | ekyagaanibwa | |
| siteaccess | omulyango gw'omukutu | the configuration unit |
| site, website | omukutu | |
| section | ekitundu | the Exponential section |
| role | obuvunaanyizibwa | |
| policy | enkola | |
| limitation | ekkomo | |
| permission | olukusa | access rights: eddembe ly'okuyingira |
| module | moduli | |
| function | fankisoni | |
| user | omukozesa | plural abakozesa |
| user group | ekibinja ky'abakozesa | |
| group | ekibinja | |
| anonymous | atamanyiddwa | anonymous user: omukozesa atamanyiddwa |
| administrator | omuddukanya | |
| administration (interface) | ekitundu ky'okuddukanya | |
| username | erinnya ly'omukozesa | the column "Login" too |
| password | ekigambo ky'ekyama | |
| log in / sign in | okuyingira | button: Yingira |
| log out / sign out | okufuluma | button: Fuluma |
| e-mail | imeeyilo | e-mail address: endagiriro ya imeeyilo |
| cache | kaasi | clear cache: Yonja kaasi |
| template | tempuleeti | |
| override | okusikiza | override template: tempuleeti esikiza |
| fallback | ekiddako | fallback language: olulimi oluddako |
| design | dizayini | |
| workflow | enkola y'emirimu | |
| trigger | ekitandika | |
| event | ekibaddewo | |
| trash | kasasiro | empty trash: Yonja kasasiro |
| bookmark | akabonero | |
| settings | entegeka | |
| setup | okuteekateeka | the Setup tab; installation: okuteekamu |
| layout | enteekateeka | Exponential Layouts |
| block | bbulooka | |
| zone | zooni | |
| extension | ekyongerezo | |
| package | pakeeji | |
| search | okunoonya | button: Noonya |
| log | logi | log file: fayiro ya logi |
| queue | olunyiriri | |
| job / task | omulimu | plural emirimu |
| cron | cron | cron job: omulimu gwa cron |
| script | sikulipiti | |
| worker | omukozi | |
| kernel, core | omusingi | |
| server | seeva | |
| database | databeesi | |
| folder | fooda | |
| file | fayiro | |
| image | ekifaananyi | |
| preview | okusooka okulaba | button: Sooka olabe |
| upload | okutikka | button: Tikka |
| download | okuwanula | button: Wanula |
| export / import | okusindika ebweru / okuyingiza | |
| remove | okuggyamu | take out of a list or a place; button: Ggyamu |
| delete | okusangula | destroy; button: Sangula |
| edit | okulongoosa | button: Longoosa |
| editor | omulongoosa | |
| save / store | okutereka | button: Tereka |
| Store (shop tab) | Edduuka | the word the shop menu uses for "Shop" |
| cancel | okusazaamu | button: Sazaamu |
| OK | OK | |
| apply | okukozesa | button: Kozesa |
| back | emabega | |
| next / previous | ekiddako / ekyayita | |
| enable / disable | okukoleeza / okuzikiza | a setting or feature |
| enabled / disabled | kikoleezeddwa / kizikiziddwa | |
| active / inactive | ekikola / ekitakola | |
| run | okuddusa | a script or job; button: Ddusa |
| start / stop | okutandika / okuyimiriza | |
| session | olutuula | |
| hidden / visible | ekikwekeddwa / ekirabika | hide / reveal: Kweka / Laga |
| archive | etterekero | |
| order (sort) | ensengeka | sort order: ensengeka |
| order (shop) | oda | |
| shop | edduuka | |
| basket | akasero | kept apart from kasasiro |
| product | ekyamaguzi | plural ebyamaguzi |
| customer | kasitoma | |
| discount | ekisaliddwako | |
| VAT | VAT | the abbreviation used in Uganda |
| price | bbeeyi | |
| currency | ensimbi | |
| tag | tagi | keyword tag; an HTML tag: tagi ya HTML |
| translation | enzivuunula | to translate: okuvvuunula |
| language | olulimi | main language: olulimi olukulu |
| locale | lokale | |
| state | embeera | the object state; state group: ekibinja ky'embeera |
| status | omutendera | |
| view (button) | Laba | a module view: endabika |
| format | fomati | |
| sort | okusengeka | |
| filter | ekisengejja | |
| select | okulonda | button: Londa; selected: ekirondeddwa |
| browse | okulambula | button: Lambula |
| copy | okukoppa | button: Koppa |
| move | okukyusa ekifo | button: Kyusa ekifo |
| add | okwongerako | button: Yongerako |
| create | okutonda | button: Tonda |
| new | -pya | ekipya, empya, omupya |
| restore | okuzzaawo | button: Zzaawo |
| install / uninstall | okuteekamu / okusimbula | |
| sub items | ebiyingizo ebitono | |
| subtree | ettabi | |
| related objects | ebintu ebikwatagana | relation: enkolagana |
| URL alias | erinnya erirala erya URL | |
| redirect | okusindika awalala | |
| dashboard | olubaawo lw'okulondoola | |
| content structure | ensengeka y'ebirimu | |
| media library | etterekero ly'emikutu | |
| identifier / ID | ekimanyisa / ID | |
| priority | obukulu | |
| owner | nnannyini | |
| modified / created | ekyakyusibwa / ekyatondebwa | |
| date / time | ennaku z'omwezi / essaawa | |
| error / warning | ensobi / okulabula | |
| help | obuyambi | |
| notification | okumanyisa | |
| wizard | omuyambi | |
| feed (RSS) | omukutu gw'amawulire | RSS feed: omukutu gwa RSS |
| tab | tabu | |
| audit, audit trail | okwekenneenya, ebyafaayo by'okwekenneenya | |
| repository | etterekero ly'ebintu | |
| checksum | omugatte ogukebera | |
| account | akawunti | |
| default | ekyabulijjo | default value: omuwendo ogwabulijjo |
| toolbar | olubaawo lw'ebikozesebwa | |
| container | ekibikka | |
| AI | AI | the abbreviation, with amagezi ag'ekikugu where it is spelt out |
| pattern (name pattern) | ekyokulabirako | kept apart from tempuleeti (template) |
| wildcard | akabonero akakiikirira | |
| remote ID | ID ey'ewala | |
| placeholder | ekikiikirira | |
| collection | ekibinja ky'ebintu | information collector: ekikunnganya amawulire |
| approve, approval | okukkiriza, okukkirizibwa | |
| software | pulogulaamu | |
| creator / modifier | eyatonda / eyakyusa | |
| request (HTTP) | okusaba | |
| query | ekibuuzo | search query: ekibuuzo ky'okunoonya |
| handler / provider / listener | ekikwata / omugabi / ekiwuliriza | |
| engine | yingini | |
| release / upgrade | okufulumizibwa / okulongoosa | update: okuzza obuggya |
| backup | kkopi ey'obukuumi | |
| maintenance | okuddaabiriza | |
| directory | fooda | |
| header (HTTP) | omutwe | |
| page layout (pagelayout template) | tempuleeti y'olupapula | kept apart from enteekateeka (Exponential Layouts) |
| passkey | ekisumuluzo ky'okuyingira | |
| two-step sign-in | okuyingira mu mitendera ebiri | |
| device / browser | ekyuma / bulawuza | |
| link | enkolagana | a hyperlink: lingi |
| registration, register | okwewandiisa | |
| payment gateway | omulyango gw'okusasula | |
| form | foomu | |
| comment | endowooza | |
| newsletter / subscription | ebbaluwa y'amawulire / okwewandiisa | |
| field | ekifo ky'okuwandiika | |
| value | omuwendo | |
| list | olukalala | |
| page | olupapula | |
| title / name | omutwe / erinnya | the column "Name": Erinnya |
| description | ennyinnyonnyola | |
| type | ekika | |
| path | ekkubo | |
| width / height | obugazi / obuwanvu | |
| size | obunene | |
| none | tewali | |
| yes / no | yee / nedda | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Edduuka, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Tereka.
- "Delete" is Sangula and "cancel" is Sazaamu, so that the two buttons are never confused.
- A hyperlink is lingi; enkolagana is a relation between objects.
