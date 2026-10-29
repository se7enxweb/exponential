# Bambara (bam-ML): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Bambara (Bamanankan) of Mali, in the neutral, plain written register of Bambara newspapers (Kibaru, Jɛkabaara), adult
literacy books and the Bambara localisations of Wikipedia and Mozilla. N'Ko (`nqo-GN`) takes its terms from this
guide. French (`fre-FR`) gives the loan words for computing, as everyday Bambara does.

- Bambara words are used where they exist: mara, jɔsi, bɔsi, yɛlɛma, ɲini, sugandi, fara a kan, dilan, lajɛ, kan,
  baarakɛla, gundodaɲɛ, ja, nɔgɔsigiyɔrɔ.
- Computing words without a Bambara one are French loans in Bambara spelling, as Malian media write them: fisiye,
  dosiye, kasi, modili, kilasi, objɛ, wɛrisiyɔn, pake, sikiripiti, modɛli, bulɔki, zɔni, tagi, lokali, desɛn,
  fɔrima, imɛli, sɛriwɛri, porogaramu, motɛri. A word that has no settled Bambara spelling keeps the glossary's
  form; never a fresh French word in French spelling inside a Bambara sentence.
- The plural is the suffix **-w** written on the last word of the noun phrase: fisiyew, baarakɛlaw, objɛ kuraw.
- Possession: "i ka" (your, alienable), "i" (your, inalienable): i ka gundodaɲɛ, i tɔgɔ.

## 2. Form of address

The interface addresses the user with **i** (you, singular) and **i ka** (your), as Bambara software does; "aw"
(plural or polite) is not used.

- **Buttons, menu items and links**: the bare verb (imperative): Mara, Dabila, Jɔsi, Yɛlɛma, Bila kɛnɛ kan, Ci
  walasa ka bila kɛnɛ kan, Bɔsi sugandilenw, Bila nɔgɔsigiyɔrɔ la.
- **Titles, column headings and tabs**: nouns or noun phrases: Labɛnw, Bayɛlɛmaliw, Yɔrɔ kunba.
- **Instructions**: "I ka ..." (you should), softened with "Hakɛto," where a request needs it: "I ka objɛ dɔ
  sugandi ka «OK» digi.", "Hakɛto, i ka makɔnɔ ...". A reference ("see ...") is "A filɛ ...".
- **Confirmations and questions**: "Yala i b'a fɛ ka nin kilasi jɔsi tiɲɛ na wa?", "I b'a fɛ ka ... wa?".
- **What the user can or may do**: "I bɛ se ka ...", "I tɛ se ka ...", or impersonal: "Kan kunba tɛ se ka yɛlɛma
  ...". "You do not have permission" is "Yamaruya t'i bolo ...".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the official Bambara alphabet of Mali (1967, revised 1982), the script of the shipped locale
  (`share/locale/bam-ML.ini`, LanguageName Bamanakan). The special letters are written with their own code points:
  ɛ (U+025B), ɔ (U+0254), ɲ (U+0272) and ŋ (U+014B), capitals Ɛ, Ɔ, Ɲ, Ŋ. Long vowels are written double (baara,
  kuraw). Tones are not marked. The N'Ko and Ajami scripts are not used here.
- **Latin text inside Bambara**: product names, codes and acronyms stay as they are: "PHP fisiye", "URL", "HTML
  kode".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "objɛ %count", not
  "%countw" or "%namela"; write "%name la" with a space, or rephrase: "Objɛ: %name".
- **Numbers**: the number comes after the noun, which stays in the singular: "objɛ %count", "fisiye 3". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: comma, full stop, colon, semicolon, question and exclamation mark as in the source, without a
  space before them. A label keeps its colon. Elision with an apostrophe as in written Bambara: b'a, t'a, k'a.

## 4. Glossary

Binding for the kernel and every extension.

| English | Bambara | Note |
|---|---|---|
| content | kɔnɔkow | |
| content object | kɔnɔkow objɛ | |
| object | objɛ | |
| node | nɔdi | main node: nɔdi kunba; parent node: nɔdi bangebaga; child: nɔdi den |
| location | yɔrɔ | main location: yɔrɔ kunba |
| item (list entry) | fɛn | |
| class | kilasi | the content class; class group: kilasi jɛkulu; a PHP class: PHP kilasi |
| content class | kɔnɔkow kilasi | |
| attribute | atiribi | |
| datatype | kunnafoni suguya | |
| version | wɛrisiyɔn | version history: wɛrisiyɔnw ka tariku |
| draft | bruyɔn | store draft: Bruyɔn mara |
| publish | ka bila kɛnɛ kan | button: Bila kɛnɛ kan; published: a bilalen bɛ kɛnɛ kan |
| send for publishing | Ci walasa ka bila kɛnɛ kan | |
| discard | ka fili | discard draft: Bruyɔn fili |
| archived | maralen | |
| pending | a bɛ makɔnɔni na | |
| rejected | a banna | |
| siteaccess | siti donda | the configuration unit |
| site, website | siti, wɛbu siti | |
| section | sekisiyɔn | the Exponential section |
| role | jɔyɔrɔ | |
| policy | sariya | |
| limitation | dancɛ | |
| permission | yamaruya | access rights: donni yamaruya |
| module | modili | |
| function | baarakɛcogo | |
| user | baarakɛla | |
| user group | baarakɛlaw jɛkulu | |
| group | jɛkulu | |
| anonymous | tɔgɔbali | anonymous user: baarakɛla tɔgɔbali |
| administrator | marabaga | |
| administration (interface) | marali | |
| username | baarakɛla tɔgɔ | the column "Login" too |
| password | gundodaɲɛ | |
| log in / sign in | ka don | button: Don |
| log out / sign out | ka bɔ | button: Bɔ |
| e-mail | imɛli | e-mail address: imɛli adiresi |
| cache | kasi | clear cache: Kasi saniya |
| template | modɛli | |
| override | nɔnabila | override template: nɔnabila modɛli |
| fallback | jigiyɔrɔ | fallback language: jigiyɔrɔ kan |
| design | desɛn | |
| workflow | baarasira | |
| trigger | daminɛbaga | |
| event | kɛwale | |
| trash | nɔgɔsigiyɔrɔ | empty trash: Nɔgɔsigiyɔrɔ lankolon |
| bookmark | taamasiyɛn | |
| settings | labɛnw | |
| setup | labɛnni | the Setup tab; installation: sigili |
| layout | sigicogo | Exponential Layouts |
| block | bulɔki | |
| zone | zɔni | |
| extension | farafɛn | |
| package | pake | |
| search | ka ɲini | button: Ɲini |
| log | kɛwale sɛbɛn | log file: kɛwale sɛbɛn fisiye |
| queue | ɲɔgɔnkɔ | |
| job / task | baara | |
| cron | cron | cron job: cron baara |
| script | sikiripiti | |
| worker | baaraden | kept apart from baarakɛla (user) |
| kernel, core | kisɛ | |
| server | sɛriwɛri | |
| database | kunnafoniso | |
| folder | dosiye | |
| file | fisiye | |
| image | ja | |
| preview | ɲɛfɛlajɛ | |
| upload | ka lakɔrɔta | button: Lakɔrɔta |
| download | ka lajigin | button: Lajigin |
| export / import | ka labɔ / ka ladon | |
| remove | ka bɔsi | take out of a list or a place |
| delete | ka jɔsi | destroy |
| edit | ka yɛlɛma | |
| editor | yɛlɛmabaga | |
| save / store | ka mara | |
| Store (shop tab) | Butiki | the word the shop menu uses for "Shop" |
| cancel | ka dabila | |
| OK | OK | |
| apply | ka waleya | |
| back | ka segin kɔ | |
| next / previous | nata / tɛmɛnen | |
| enable / disable | ka bila baara la / ka bɔ baara la | a setting or feature |
| enabled / disabled | a bɛ baara la / a tɛ baara la | |
| active / inactive | a bɛ baara la / a tɛ baara la | |
| run | ka boli | a script or job |
| start / stop | ka daminɛ / ka jɔ | |
| session | sesiyɔn | |
| hidden / visible | dogolen / yeli | hide / reveal: dogo / jira |
| archive | marayɔrɔ | a file archive (.zip): arsiwu |
| order (sort) | bolodacogo | sort order: bolodacogo |
| order (shop) | komandi | |
| shop | butiki | |
| basket | sɛgɛ | |
| product | feerefɛn | |
| customer | sannikɛla | |
| discount | dɔgɔyali | |
| VAT | TVA | the abbreviation used in Mali |
| price | sɔngɔ | |
| currency | wari | |
| tag | tagi | keyword tag; an HTML tag: HTML tagi |
| translation | bayɛlɛmali | |
| language | kan | main language: kan kunba |
| locale | lokali | |
| state | cogoya | the object state; state group: cogoya jɛkulu |
| status | hali | |
| view (button) | ka lajɛ | a module view: lajɛyɔrɔ |
| format | fɔrima | |
| sort | ka bolo da | |
| filter | sɛnsɛn | |
| select | ka sugandi | selected: sugandilen |
| browse | ka yaala | |
| copy | kopi | button: Kopi kɛ |
| move | ka yɔrɔ falen | |
| add | ka fara a kan | |
| create | ka dilan | |
| new | kura | |
| restore | ka segin a nɔ na | |
| install / uninstall | ka sigi / ka sigili bɔ | |
| sub items | duguma fɛnw | |
| subtree | jiribolo | |
| related objects | objɛ jɛɲɔgɔnw | relation: jɛɲɔgɔnya |
| URL alias | URL tɔgɔ wɛrɛ | |
| redirect | sirayɛlɛma | |
| dashboard | kunnafoni tablo | |
| content structure | kɔnɔkow labɛncogo | |
| media library | mediya marayɔrɔ | |
| identifier / ID | taamasere / ID | |
| priority | ɲɛfɛta | |
| owner | tigi | |
| modified / created | yɛlɛmana / dilanna | |
| date / time | don / waati | |
| error / warning | fili / lasɔmini | |
| help | dɛmɛ | |
| notification | kunnafonidi | |
| wizard | ɲɛminɛbaga | |
| feed (RSS) | kunnafoni sira | RSS feed: RSS kunnafoni sira |
| tab | tabu | |
| audit, audit trail | sɛgɛsɛgɛli, sɛgɛsɛgɛli sɛbɛn | |
| repository | marayɔrɔ | |
| checksum | sɛgɛsɛgɛ jate | |
| account | konti | |
| default | sariyalen | default value: hakɛ sariyalen |
| toolbar | minɛnw sira | |
| container | daga | |
| AI | IA | the French abbreviation used in Mali |
| pattern (name pattern) | misali | kept apart from modɛli (template) |
| wildcard | jɔkɛri | |
| remote ID | yɔrɔjan ID | |
| placeholder | yɔrɔmara | |
| collection | ladɛnni | information collector: kunnafoni ladɛnbaga |
| approve, approval | ka sɔn, sɔnni | |
| software | porogaramu | |
| creator / modifier | dilanbaga / yɛlɛmabaga | |
| request (HTTP) | delili | |
| query | ɲininkali | search query: ɲinini ɲininkali |
| handler / provider / listener | ɲɛnabɔbaga / dibaga / lamɛnbaga | |
| engine | motɛri | |
| release / upgrade | bɔli / kuraya | update: kuraya |
| backup | lakana kopi | |
| maintenance | ladonni | |
| directory | dosiye | |
| header (HTTP) | kunkolo | |
| page layout (pagelayout template) | ɲɛ modɛli | kept apart from sigicogo (Exponential Layouts) |
| passkey | donni kile | |
| two-step sign-in | donni sen fila la | |
| device / browser | minɛn / wɛbu yaalalan | |
| link | sirilan | |
| registration, register | tɔgɔsɛbɛn, i tɔgɔ sɛbɛn | |
| payment gateway | sarali da | |
| form | sɛbɛnfura | |
| comment | hakilina | |
| newsletter / subscription | kunnafonisɛbɛn / abonɛ | |
| field | sɛbɛnyɔrɔ | |
| value | hakɛ | |
| list | lisi | |
| page | ɲɛ | |
| title / name | kuntilenna / tɔgɔ | the column "Name": Tɔgɔ |
| description | ɲɛfɔli | |
| type | suguya | |
| path | sira | |
| width / height | ɲɛbonya / janya | |
| size | bonya | |
| none | foyi | |
| yes / no | ɔwɔ / ayi | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Butiki, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is Mara.
- "Delete" is Jɔsi (destroy) and "remove" is Bɔsi (take out), so that the two buttons are never confused; "log out"
  is Bɔ.
- "User" is baarakɛla and a background "worker" is baaraden, so that the two are never confused.
