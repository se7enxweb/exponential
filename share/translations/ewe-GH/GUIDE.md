# Ewe (ewe-GH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Ewe (Eʋegbe) of Ghana and Togo, in the neutral, plain register of the Ewe Bible, Ewe school books,
Ghana's Bureau of Ghana Languages and the Ewe localisations of Wikipedia and Mozilla. No other locale falls back to
`ewe-GH`. English (Ghana) gives the loan words for computing that have no Ewe word, as everyday Ewe does.

- Ewe words are used where they exist: dzra ɖo, tutu ɖa, ɖe ɖa, trɔ asi le eŋu, di, tia, tsɔ kpe ɖe eŋu, wɔ, kpɔ,
  gbe, zãla, mɔxenya, nɔnɔmetata, nugbegblẽƒe.
- Computing words without an Ewe one are English loans in Ewe spelling: faɛl, sɛva, kase, mɔdul, ʋɛsiɔn, skript,
  blɔk, dizain, fɔmat, e-meil, brauza, akɔnta. A word that has no settled Ewe spelling keeps the glossary's form;
  never a fresh English word in English spelling inside an Ewe sentence.
- The plural is **-wo** on the last word of the noun phrase, the definite article **-a** / **la**: faɛlwo,
  zãlawo, faɛla.

## 2. Form of address

The interface addresses the user with **wò** / **èle** (you, singular) and **wò** (your), as Ewe software does.

- **Buttons, menu items and links**: the bare verb (imperative): Dzra ɖo, Ɖe asi le eŋu, Tutu ɖa, Trɔ asi le eŋu,
  Ɖe ɖe dutoƒo, Ɖo ɖa be woaɖe ɖe dutoƒo, Ɖe esiwo wotia ɖa, Tsɔ yi nugbegblẽƒe.
- **Titles, column headings and tabs**: nouns or noun phrases: Ɖoɖowo, Gbegɔmeɖeɖewo, Teƒe vevitɔ.
- **Instructions**: the imperative, softened with "Taflatse," where a request needs it: "Tia nu ɖeka eye nàzi «OK»
  dzi.", "Taflatse, lala ...". A reference ("see ...") is "Kpɔ ...".
- **Confirmations and questions**: "Ɖe nèka ɖe edzi be yeadi be yeatutu hatsotso sia ɖa?", "Èdi be yeà ... a?".
- **What the user can or may do**: "Àte ŋu ...", "Màte ŋu ... o", or impersonal: "Womate ŋu atrɔ gbe vevitɔ o ...".
  "You do not have permission" is "Mɔɖeɖe mele asiwò be ... o".
- Not allowed: slash forms.

## 3. Script and typography

- **Latin**, the Ewe alphabet, the script of the shipped locale (`share/locale/ewe-GH.ini`, LanguageName Eʋegbe).
  The special letters are written with their own code points: ɖ (U+0256), ɛ (U+025B), ƒ (U+0192), ɣ (U+0263),
  ŋ (U+014B), ɔ (U+0254) and ʋ (U+028B), capitals Ɖ, Ɛ, Ƒ, Ɣ, Ŋ, Ɔ, Ʋ. Nasal vowels take the tilde (ẽ, ã, ɔ̃).
  Tones are not marked, except where the standard spelling writes them (wò, è).
- **Latin text inside Ewe**: product names, codes and acronyms stay as they are: "PHP faɛl", "URL", "HTML kɔd".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "nu %count", not
  "%countwo" or "%namea"; write "%name la" with a space, or rephrase: "Nu: %name".
- **Numbers**: the number comes after the noun, which stays in the singular: "nu %count", "faɛl 3". Digits 0-9.
- **Quotation marks**: the double quotes "..." of Ghanaian writing, or «...» where the source has its own quotation
  marks in text; where the source has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: as in the source. A label keeps its colon.

## 4. Glossary

Binding for the kernel and every extension.

| English | Ewe | Note |
|---|---|---|
| content | emenuwo | |
| content object | emenu | |
| object | nu | |
| node | kadodoƒe | main node: kadodoƒe vevitɔ; parent node: dzila kadodoƒe; child: vi kadodoƒe |
| location | teƒe | main location: teƒe vevitɔ |
| item (list entry) | nuŋɔŋlɔ | |
| class | hatsotso | the content class; class group: hatsotso ƒuƒoƒo; a PHP class: PHP hatsotso |
| content class | emenu ƒe hatsotso | |
| attribute | atribiut | |
| datatype | nyatakaka ƒomevi | |
| version | ʋɛsiɔn | version history: ʋɛsiɔnwo ƒe ŋutinya |
| draft | ŋɔŋlɔ gbãtɔ | store draft: Dzra ŋɔŋlɔ gbãtɔ ɖo |
| publish | ɖe ɖe dutoƒo | button: Ɖe ɖe dutoƒo; published: woɖe ɖe dutoƒo |
| send for publishing | Ɖo ɖa be woaɖe ɖe dutoƒo | |
| discard | tsɔ ƒu gbe | discard draft: Tsɔ ŋɔŋlɔ gbãtɔ ƒu gbe |
| archived | wodzra ɖo ɖe ŋutinyadzraɖoƒe | |
| pending | le lalam | |
| rejected | wogbe | |
| siteaccess | nyatakakaɖoƒe ƒe mɔnu | the configuration unit |
| site, website | nyatakakaɖoƒe | |
| section | akpa | the Exponential section |
| role | dɔdeasi | |
| policy | sedede | |
| limitation | seɖoƒe | |
| permission | mɔɖeɖe | access rights: geɖeeme mɔɖeɖe |
| module | mɔdul | |
| function | dɔwɔwɔ | |
| user | zãla | |
| user group | zãlawo ƒe ƒuƒoƒo | |
| group | ƒuƒoƒo | |
| anonymous | ŋkɔmanɔsitɔ | anonymous user: zãla ŋkɔmanɔsitɔ |
| administrator | dziɖula | |
| administration (interface) | dziɖuɖu | |
| username | zãla ƒe ŋkɔ | the column "Login" too |
| password | mɔxenya | |
| log in / sign in | ge ɖe eme | button: Ge ɖe eme |
| log out / sign out | do le eme | button: Do le eme |
| e-mail | e-meil | e-mail address: e-meil adrɛs |
| cache | kase | clear cache: Ŋlɔ kase ɖa |
| template | kpɔɖeŋu | |
| override | ɖɔlinu | override template: kpɔɖeŋu ɖɔlinu |
| fallback | megbedenu | fallback language: megbedegbe |
| design | dizain | |
| workflow | dɔwɔwɔmɔ | |
| trigger | nudzɔdzɔ-dzela | |
| event | nudzɔdzɔ | |
| trash | nugbegblẽƒe | empty trash: Ŋlɔ nugbegblẽƒe ɖa |
| bookmark | dzesiɖoƒe | |
| settings | ɖoɖowo | |
| setup | ɖoɖowɔwɔ | the Setup tab; installation: ɖoɖoɖi |
| layout | ɖoɖonɔnɔme | Exponential Layouts |
| block | blɔk | |
| zone | nutome | |
| extension | tsɔtsɔkpe | |
| package | agba | |
| search | di | button: Di |
| log | ŋutinya | log file: ŋutinya faɛl |
| queue | fli | |
| job / task | dɔ | |
| cron | cron | cron job: cron dɔ |
| script | skript | |
| worker | dɔwɔla | kept apart from zãla (user) |
| kernel, core | dzime | |
| server | sɛva | |
| database | nyatakakadzraɖoƒe | |
| folder | faɛldzraɖoƒe | |
| file | faɛl | |
| image | nɔnɔmetata | |
| preview | kpɔe do ŋgɔ | |
| upload | tsɔ de dzi | button: Tsɔ de dzi |
| download | ɖe ɖe anyi | button: Ɖe ɖe anyi |
| export / import | tsɔ yi go / tsɔ va eme | |
| remove | ɖe ɖa | take out of a list or a place |
| delete | tutu ɖa | destroy |
| edit | trɔ asi le eŋu | |
| editor | asitrɔla | |
| save / store | dzra ɖo | |
| Store (shop tab) | Fiase | the word the shop menu uses for "Shop" |
| cancel | ɖe asi le eŋu | |
| OK | OK | |
| apply | zãe | |
| back | trɔ yi megbe | |
| next / previous | esi kplɔe ɖo / esi do ŋgɔ | |
| enable / disable | na wòawɔ dɔ / tɔ te | a setting or feature |
| enabled / disabled | le dɔ wɔm / mele dɔ wɔm o | |
| active / inactive | le dɔ wɔm / mele dɔ wɔm o | |
| run | ƒu du | a script or job |
| start / stop | dze egɔme / tɔ te | |
| session | zãɣi | |
| hidden / visible | ɣaɣla / dze | hide / reveal: ɣlae / ɖee fia |
| archive | ŋutinyadzraɖoƒe | a file archive (.zip): faɛl-agba |
| order (sort) | ɖoɖomɔ | sort order: ɖoɖomɔ |
| order (shop) | nudidi | |
| shop | fiase | |
| basket | kusi | |
| product | adzɔnu | |
| customer | nuƒla | |
| discount | asiɖeɖeɖa | |
| VAT | VAT | the abbreviation used in Ghana |
| price | asi | |
| currency | ga | |
| tag | dzesi | keyword tag; an HTML tag: HTML dzesi |
| translation | gbegɔmeɖeɖe | |
| language | gbe | main language: gbe vevitɔ |
| locale | nutome ɖoɖo | |
| state | nɔnɔme | the object state; state group: nɔnɔme ƒuƒoƒo |
| status | ɖoƒe | |
| view (button) | kpɔ | a module view: kpɔƒe |
| format | fɔmat | |
| sort | ɖo ɖe ɖoɖo nu | |
| filter | tsotsonu | |
| select | tia | selected: esiwo wotia |
| browse | tsa | |
| copy | kɔpi | button: Kɔpi |
| move | ɖe ɖe teƒe bubu | |
| add | tsɔ kpe ɖe eŋu | |
| create | wɔ | |
| new | yeye | |
| restore | gbugbɔe ɖo | |
| install / uninstall | ɖoe ɖi / ɖee ɖa | |
| sub items | nu siwo le ete | |
| subtree | atilɔ | |
| related objects | nu siwo do ƒome kplii | relation: ƒomedodo |
| URL alias | URL ŋkɔ bubu | |
| redirect | mɔtrɔ | |
| dashboard | dɔwɔƒe | |
| content structure | emenuwo ƒe ɖoɖo | |
| media library | mediadzraɖoƒe | |
| identifier / ID | dzesidenu / ID | |
| priority | vevienyenye | |
| owner | nutɔ | |
| modified / created | wotrɔ asi le eŋu / wowɔe | |
| date / time | ŋkeke / ɣeyiɣi | |
| error / warning | vodada / nuxlɔ̃ame | |
| help | kpekpeɖeŋu | |
| notification | gbeƒãɖeɖe | |
| wizard | mɔfiala | |
| feed (RSS) | nyadzɔdzɔwo | RSS feed: RSS nyadzɔdzɔwo |
| tab | tabu | |
| audit, audit trail | dzodzro, dzodzro ŋutinya | |
| repository | dzraɖoƒe | |
| checksum | xexlẽme-kpɔkplɔ | |
| account | akɔnta | |
| default | gɔmeɖoanyi | default value: asixɔxɔ gɔmeɖoanyi |
| toolbar | dɔwɔnuwo ƒe fli | |
| container | nugoe | |
| AI | AI | artificial intelligence: nunya si wowɔ |
| pattern (name pattern) | kpɔɖeŋumɔ | kept apart from kpɔɖeŋu (template) |
| wildcard | dzesi-ɖesiaɖe | |
| remote ID | ID tso adzɔge | |
| placeholder | teƒenɔla | |
| collection | nuƒoƒu | information collector: nyatakaka-ƒola |
| approve, approval | lɔ̃ ɖe edzi, lɔlɔ̃ ɖe edzi | |
| software | kɔmpiuta dɔwɔnu | |
| creator / modifier | wɔla / tɔtrɔla | |
| request (HTTP) | biabia | |
| query | nyabiabia | search query: didi ƒe nyabiabia |
| handler / provider / listener | kpɔla / nanala / toɖola | |
| engine | mɔ̃ | |
| release / upgrade | ɖeɖe ɖe go / wɔe yeye | update: wɔe yeye |
| backup | kɔpi takpɔnu | |
| maintenance | dzikpɔkpɔ | |
| directory | faɛldzraɖoƒe | |
| header (HTTP) | ta | |
| page layout (pagelayout template) | axa ƒe kpɔɖeŋu | kept apart from ɖoɖonɔnɔme (Exponential Layouts) |
| passkey | mɔxe-safui | |
| two-step sign-in | geɖeeme le afɔɖeɖe eve me | |
| device / browser | mɔ̃ɖaŋu / brauza | |
| link | kadodo | |
| registration, register | ŋkɔŋlɔŋlɔ, ŋlɔ wò ŋkɔ | |
| payment gateway | fexexemɔnu | |
| form | agbalẽvi | |
| comment | nyaɖeɖe | |
| newsletter / subscription | nyadzɔdzɔgbalẽ / nuxɔxɔ ɖaa | |
| field | nuŋɔŋlɔteƒe | |
| value | asixɔxɔ | |
| list | lisiti | |
| page | axa | |
| title / name | tanya / ŋkɔ | the column "Name": Ŋkɔ |
| description | numeɖeɖe | |
| type | ƒomevi | |
| path | mɔ | |
| width / height | keke / kɔkɔme | |
| size | lolome | |
| none | naneke | |
| yes / no | ɛ̃ / ao | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Fiase, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Dzra ɖo.
- "Delete" is Tutu ɖa (destroy) and "remove" is Ɖe ɖa (take out), so that the two buttons are never confused.
- "User" is zãla and a background "worker" is dɔwɔla, so that the two are never confused.
