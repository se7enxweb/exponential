# Māori (mri-NZ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Te reo Māori in the standard written form and the orthographic conventions of Te Taura Whiri i te Reo Māori (the
Māori Language Commission), in the neutral, plain register of New Zealand government websites and of established
Māori software localisation (Microsoft, Google, the Te Aka and Te Taura Whiri computing terms). The locale is
`mri-NZ`. No other locale falls back to it.

- **New words** take the form the Te Taura Whiri and Te Aka Māori dictionary lists give them (rorohiko, kōnae,
  kōpaki, tūmau, pātengi raraunga, kupuhipa). A transliteration is used only where it is the established word
  (īmēra, ota, karaehe).
- English words stay only where they are names or code (see TRANSLATING.md, rule 5).

## 2. Form of address

The interface addresses the user with **koe** (you), **tō** / **tāu** / **tōu** (your), as Māori government and
software texts do; te reo has no separate polite pronoun, politeness lies in the wording ("Tēnā koa", "koa").

- **Buttons, menu items and links**: the bare verb (imperative), as Māori software writes its commands: Tiaki,
  Whakakore, Muku, Whakatika, Whakaputa, Tango, Tāpiri. A command naming an object takes the passive imperative
  with the object: "Waihangatia he karaehe hou", "Tukuna ake he kōnae", "Mukua ngā mea kua kōwhiria".
- **Titles, column headings and tabs**: nouns or nominalised verbs (-nga, -tanga): Tautuhinga,
  Whakamāoritanga, Tauwāhi matua, Whakaputanga.
- **Instructions** in sentences: the passive imperative for a verb with an object, the bare verb otherwise:
  "Kōwhiria te tūemi, ka pāwhiri ai i “Āe”.", "Tatari kia tiakina te whakaarotau.". A reference ("see ...") is
  "Tirohia ...". "Please" is "koa" after the verb, or "Tēnā koa" at the start, used sparingly.
- **Confirmations and questions**: "Kei te hiahia koe ki te ...?" or "E tino hiahia ana koe ki te ...?": "E tino
  hiahia ana koe ki te muku i tēnei karaehe?".
- **What the user can or may do**: "Ka taea e koe te ...", "Kāore e taea e koe te ...". "You do not have
  permission" is "Kāore ō whakaaetanga ki te ...".
- Not allowed: slash forms, English verbs inside a Māori sentence.

## 3. Script and typography

- **Latin script with the macron** for every long vowel: ā ē ī ō ū and Ā Ē Ī Ō Ū, as single precomposed
  characters (Unicode NFC). Never the double vowel (aa, oo) of some regional writing, never a diaeresis or a
  circumflex. Macrons are written on capitals too (Āe, Ōna).
- The letters of the alphabet are a e h i k m n o p r t u w, ng and wh; loanwords are spelled in it (īmēra, ota,
  karaehe) unless they are names or code.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before
  and after them, or a punctuation mark right after. Nothing glued to a placeholder: "%count ahanoa", "ingoa:
  %name". Code, file names, INI settings and product names stay unchanged too.
- **Particles before a placeholder** that stands for a name: "a %name" for a personal name, "te %name" for a
  thing; where the gender of the noun is unknown, use a label: "Ingoa: %name".
- **Quotation marks**: the typographic “...” (and ‘...’ inside them) where the source has its own quotation marks
  in text; where the source has `&quot;`, a straight " or ' that is code or an entity, keep it exactly.
- **Punctuation**: as in the source; no space before a colon or a question mark.
- **Numbers**: digits 0-9. The catalogue has no plural forms; plurality is shown by the article (te / ngā) and a
  label with a count is used: "Ahanoa: %count".
- **Capitals**: only the first word of a button, title or heading starts with a capital, apart from names.

## 4. Glossary

Binding for the kernel and every extension.

| English | Māori | Note |
|---|---|---|
| content | ihirangi | |
| content object | ahanoa ihirangi | |
| object | ahanoa | plural with ngā |
| node | pona | main node: pona matua; parent node: pona mātua |
| location | tauwāhi | main location: tauwāhi matua |
| item (list entry) | tūemi | |
| class | karaehe | the content class; class group: rōpū karaehe; a PHP class: karaehe PHP |
| content class | karaehe ihirangi | |
| attribute | āhuatanga | |
| datatype | momo raraunga | |
| version | putanga | version history: hītori putanga |
| draft | hukihuki | store draft: tiaki i te hukihuki |
| publish | whakaputa | published: kua whakaputaina; publishing: whakaputanga |
| send for publishing | tuku kia whakaputaina | |
| discard | whakarere | discard draft: whakarere i te hukihuki |
| archived | kua pūrangatia | |
| pending | e tatari ana | |
| rejected | kua whakakāhoretia | |
| siteaccess | urunga pae | |
| site, website | pae, paetukutuku | |
| section | wāhanga | the Exponential section |
| role | tūranga | |
| policy | kaupapahere | |
| limitation | herenga | |
| permission | whakaaetanga | |
| module | kōwae | |
| function | taumahi | |
| user | kaiwhakamahi | |
| user group | rōpū kaiwhakamahi | |
| group | rōpū | |
| anonymous | ingoamuna | |
| administrator | kaiwhakahaere | |
| administration (interface) | whakahaere | the administration interface: atanga whakahaere |
| username | ingoa kaiwhakamahi | |
| password | kupuhipa | |
| log in / sign in | takiuru | the noun: takiurunga |
| log out / sign out | takiputa | |
| e-mail | īmēra | e-mail address: wāhitau īmēra |
| cache | keteroki | clear the cache: ūkui i te keteroki |
| template | tātauira | kept apart from "pattern" |
| override | whakakapinga | an override template: tātauira whakakapi |
| fallback | taunaki | fallback language: reo taunaki |
| design | hoahoa | |
| workflow | rerenga mahi | |
| trigger | keu | |
| event | takahanga | |
| trash | ipupara | move to trash: nuku ki te ipupara |
| bookmark | tohuwāhi | |
| settings | tautuhinga | |
| setup | whakaritenga | |
| layout | tahora | Exponential Layouts; kept apart from the page layout |
| block | paraka | |
| zone | rohe | |
| extension | toronga | |
| package | mōkihi | |
| search | rapu | the noun: rapunga; search results: hua rapu |
| log | rangitaki | a log file; never "log in" |
| queue | tūtira | |
| job / task | mahi / tūmahi | |
| cron | cron | |
| script | hōtuhi | |
| worker | kaimahi | |
| kernel, core | iho | |
| server | tūmau | |
| database | pātengi raraunga | |
| folder | kōpaki | |
| file | kōnae | |
| image | atahanga | |
| preview | arokite | |
| upload | tukuake | |
| download | tikiake | |
| export / import | kaweake / kawemai | |
| remove | tango | take out of a list or place |
| delete | muku | destroy |
| edit | whakatika | the noun: whakatikatika |
| editor | ētita | the person or the software |
| save / store | tiaki | |
| Store (shop tab) | toa | |
| cancel | whakakore | |
| OK | Āe | |
| apply | whakahāngai | |
| back | hoki | |
| next / previous | panuku / o mua | |
| enable / disable | whakahohe / whakaweto | |
| enabled / disabled | kua whakahohea / kua whakawetohia | |
| active / inactive | hohe / kāore i te hohe | |
| run | whakahaere | |
| start / stop | tīmata / whakamutu | |
| session | wātū | |
| hidden / visible | huna / kitea | hide: huna; reveal: whakaatu |
| archive | pūranga | a file archive: pūranga kōnae |
| order (sort) | raupapa | sort order: raupapa kōmaka |
| order (shop) | ota | in the shop context only |
| shop | toa | |
| basket | kete | |
| product | hua | |
| customer | kiritaki | |
| discount | heke utu | |
| VAT | tāke | tāke hokohoko where it is spelled out |
| price | utu | |
| currency | moni | currency code: waehere moni |
| tag | tūtohu | a keyword tag; an HTML tag: tūtohu HTML |
| translation | whakamāoritanga | |
| language | reo | |
| locale | rohe reo | |
| state | tūāhua | the object state |
| status | tūnga | |
| view (button) | tiro | the module view: tirohanga |
| format | hōputu | text formatting: whakahōputu |
| sort | kōmaka | |
| filter | tātari | the noun: tātari |
| select | kōwhiri | selected: kua kōwhiria |
| browse | pūtirotiro | |
| copy | tārua | |
| move | nuku | |
| add | tāpiri | |
| create | waihanga | |
| new | hou | |
| restore | whakaora | |
| install / uninstall | tāuta / tango tāuta | |
| sub items | tūemi iti | |
| subtree | peka iti | |
| related objects | ahanoa hāngai | |
| URL alias | ingoa kē URL | |
| redirect | tuku anō | the noun: tukunga anō |
| dashboard | papatohu | |
| content structure | hanganga ihirangi | |
| media library | pātaka pāpāho | |
| identifier / ID | pūtohu / ID | |
| priority | whakaarotau | |
| owner | kaipupuri | |
| modified / created | kua whakarerekētia / kua waihangatia | |
| date / time | rā / wā | |
| error / warning | hapa / whakatūpato | |
| help | āwhina | |
| notification | pānui | |
| wizard | ruānuku | |
| feed (RSS) | whāngai | |
| tab | ripa | |
| audit, audit trail | arotake, ara arotake | |
| repository | pūtahi | |
| checksum | tapeke taki | |
| account | pūkete | |
| default | taunoa | default value: uara taunoa |
| toolbar | paeutauta | |
| container | ipu | |
| AI | atamai hangahanga | |
| pattern (name pattern) | tauira | kept apart from "template" |
| wildcard | tohu kāri | |
| remote ID | ID mamao | |
| placeholder | puriwāhi | |
| collection | kohinga | information collector: kaikohi mōhiohio |
| approve, approval | whakamana, whakamanatanga | |
| software | pūmanawa | |
| creator / modifier | kaihanga / kaiwhakarerekē | |
| request (HTTP) | tono | |
| query | uiui | |
| handler / provider / listener | kaiwhakarite / kaiwhakarato / kaiwhakarongo | |
| engine | pūkaha | |
| release / upgrade | tukunga / whakahōu | |
| backup | tārua taunaki | |
| maintenance | tiakitanga | |
| directory | whaiaronga | |
| header (HTTP) | pane | |
| page layout (pagelayout template) | tahora whārangi | |
| page | whārangi | |
| passkey | kī uru | |
| two-step sign-in | takiuru takirua | |
| device / browser | pūrere / pūtirotiro | |
| link | hono | |
| registration, register | rēhitatanga, rēhita | |
| payment gateway | kūaha utu | |
| none | kore | "no ...": kāore he ... |
| yes / no | āe / kāo | |
| name | ingoa | |
| description | whakaahuatanga | |
| title | taitara | |
| type | momo | |
| value | uara | |
| list | rārangi | |
| close | kati | |
| open | huaki | |
| show | whakaatu | |
| update | whakahōu | |
| confirm | whakaū | |
| send | tuku | |
| configuration | whirihoranga | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is toa, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is tiaki.
- "Template" is **tātauira** and "pattern" is **tauira**, so that the two never meet in one screen; "draft" is
  **hukihuki**.
- The passive of whakatika is **whakatikaina** (not whakatikahia).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
