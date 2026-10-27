# Filipino (fil-PH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Filipino, the national language of the Philippines, in the spelling of the Komisyon sa Wikang Filipino (KWF,
"Ortograpiyang Pambansa"), in the neutral, clear register of established Filipino software localisation (Google,
Microsoft, Mozilla, Facebook). The locale is `fil-PH`. No other locale falls back to it. Cebuano (`ceb-PH`) and
the other languages of the Philippines are different languages.

- **English loanwords where Filipino interfaces use them.** Filipino software keeps the common English computing
  terms that its users read every day (file, folder, server, database, cache, template, password, username,
  email, link, browser, session) and writes them in their English spelling, not respelled (not "payl", "server"
  stays "server"). Native words are used where they are established in the same interfaces: nilalaman (content),
  wika (language), larawan (image), paghahanap (search), pahintulot (permission), setting → mga setting.
- **Affixes on English verbs** take a hyphen: i-save, i-edit, i-upload, i-download, i-publish, na-publish,
  pag-publish, mag-log in, naka-enable. Native verbs are conjugated normally: burahin, alisin, piliin, idagdag.
- English words that are names or code stay unchanged (see TRANSLATING.md, rule 5).

## 2. Form of address

The interface addresses the user with **mo** / **ikaw** (you), **iyong** / **ng iyong** (your), as Filipino
software from Google, Microsoft and Facebook does, without "po".

- **Buttons, menu items and links**: the imperative verb form, as Filipino software writes its commands: I-save,
  Kanselahin, Burahin, I-edit, I-publish, Alisin, Idagdag, Ilipat sa basurahan. A command naming an object takes
  it after the verb: "Gumawa ng bagong class", "Mag-upload ng file", "Alisin ang mga napili".
- **Titles, column headings and tabs**: nouns or noun phrases: Mga setting, Mga salin, Pangunahing lokasyon,
  Pag-publish.
- **Instructions** in sentences: the imperative with "mo" where it reads naturally: "Piliin ang item at i-click ang
  “OK”.", "Hintaying ma-save ang priyoridad.". A reference ("see ...") is "Tingnan ang ...".
- **Confirmations and questions**: "Sigurado ka bang gusto mong ...?": "Sigurado ka bang gusto mong burahin ang
  class na ito?".
- **What the user can or may do**: "Maaari mong ...", "Hindi mo maaaring ...", "Hindi puwedeng ...". "You do not
  have permission" is "Wala kang pahintulot na ...".
- Not allowed: "po" / "opo" in interface text, slash forms, Taglish sentences in which only the grammar words are
  Filipino.

## 3. Script and typography

- **Latin script** without diacritics (the KWF spelling of software text writes no accents).
- **Ng / nang, ng mga**: "ng" before nouns, "nang" as a conjunction or adverb, as KWF sets them.
- **Plural** with "mga": "mga object", "mga file"; an English loan does not take an English -s ("mga user", not
  "mga users").
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before
  and after them, or a punctuation mark right after. No affix or linker glued to a placeholder: "%count na object",
  "pangalan: %name", never "i-%name" or "%name-ng". Code, file names, INI settings and product names stay unchanged
  too.
- **Quotation marks**: the typographic “...” (and ‘...’ inside them) where the source has its own quotation marks in
  text; where the source has `&quot;`, a straight " or ' that is code or an entity, keep it exactly.
- **Punctuation**: as in the source; no space before a colon or a question mark.
- **Numbers**: digits 0-9. The catalogue has no plural forms; a label with a count is used: "Mga object: %count".
- **Capitals**: only the first word of a button, title or heading starts with a capital, apart from names; after
  an affix hyphen the English verb stays lowercase (I-save, Na-publish).

## 4. Glossary

Binding for the kernel and every extension.

| English | Filipino | Note |
|---|---|---|
| content | nilalaman | |
| content object | object ng nilalaman | |
| object | object | plural mga object |
| node | node | main node: pangunahing node; parent node: parent node |
| location | lokasyon | main location: pangunahing lokasyon |
| item (list entry) | item | |
| class | class | the content class; class group: grupo ng class; a PHP class: PHP class |
| content class | class ng nilalaman | |
| attribute | attribute | |
| datatype | uri ng data | |
| version | bersyon | version history: kasaysayan ng bersyon |
| draft | draft | store draft: i-save ang draft |
| publish | i-publish | published: na-publish; publishing: pag-publish |
| send for publishing | ipadala para i-publish | |
| discard | itapon | discard draft: itapon ang draft |
| archived | naka-archive | |
| pending | nakabinbin | |
| rejected | tinanggihan | |
| siteaccess | siteaccess | the technical term of Exponential |
| site, website | site, website | |
| section | seksyon | the Exponential section |
| role | tungkulin | |
| policy | patakaran | |
| limitation | limitasyon | |
| permission | pahintulot | |
| module | module | |
| function | function | |
| user | user | plural mga user |
| user group | grupo ng user | |
| group | grupo | |
| anonymous | anonymous | |
| administrator | administrator | |
| administration (interface) | administrasyon | |
| username | username | |
| password | password | |
| log in / sign in | mag-log in | the noun: pag-log in |
| log out / sign out | mag-log out | |
| e-mail | email | e-mail address: email address |
| cache | cache | clear the cache: i-clear ang cache |
| template | template | |
| override | override | |
| fallback | fallback | fallback language: fallback na wika |
| design | disenyo | |
| workflow | workflow | |
| trigger | trigger | |
| event | event | |
| trash | basurahan | move to trash: ilipat sa basurahan |
| bookmark | bookmark | |
| settings | mga setting | |
| setup | setup | |
| layout | layout | Exponential Layouts |
| block | block | |
| zone | zone | |
| extension | extension | |
| package | package | |
| search | maghanap | the noun: paghahanap; search results: mga resulta ng paghahanap |
| log | log | a log file; never "log in" |
| queue | pila | |
| job / task | trabaho / gawain | |
| cron | cron | |
| script | script | |
| worker | worker | |
| kernel, core | kernel | |
| server | server | |
| database | database | |
| folder | folder | |
| file | file | |
| image | larawan | |
| preview | preview | |
| upload | i-upload | the noun: pag-upload |
| download | i-download | the noun: pag-download |
| export / import | i-export / i-import | |
| remove | alisin | take out of a list or place |
| delete | burahin | destroy |
| edit | i-edit | the noun: pag-edit |
| editor | editor | the person or the software |
| save / store | i-save | |
| Store (shop tab) | tindahan | |
| cancel | kanselahin | |
| OK | OK | |
| apply | ilapat | |
| back | bumalik | |
| next / previous | susunod / nakaraan | |
| enable / disable | i-enable / i-disable | |
| enabled / disabled | naka-enable / naka-disable | |
| active / inactive | aktibo / hindi aktibo | |
| run | patakbuhin | |
| start / stop | simulan / ihinto | |
| session | session | |
| hidden / visible | nakatago / nakikita | hide: itago; reveal: ipakita |
| archive | archive | |
| order (sort) | pagkakasunud-sunod | sort order: pagkakasunud-sunod ng pag-uuri |
| order (shop) | order | in the shop context only |
| shop | tindahan | |
| basket | basket | |
| product | produkto | |
| customer | customer | |
| discount | diskuwento | |
| VAT | VAT | |
| price | presyo | |
| currency | currency | |
| tag | tag | a keyword tag; an HTML tag: HTML tag |
| translation | salin / pagsasalin | plural mga salin |
| language | wika | |
| locale | locale | |
| state | estado | the object state |
| status | status | |
| view (button) | tingnan | the module view: view |
| format | format | text formatting: pag-format |
| sort | pagbukud-bukurin | the noun: pag-uuri |
| filter | i-filter | the noun: filter |
| select | piliin | selected: napili |
| browse | mag-browse | |
| copy | kopyahin | the noun: kopya |
| move | ilipat | |
| add | idagdag | |
| create | gumawa | |
| new | bago | |
| restore | ibalik | |
| install / uninstall | i-install / i-uninstall | |
| sub items | mga sub-item | |
| subtree | subtree | |
| related objects | mga kaugnay na object | |
| URL alias | URL alias | |
| redirect | redirect | |
| dashboard | dashboard | |
| content structure | istruktura ng nilalaman | |
| media library | media library | |
| identifier / ID | identifier / ID | |
| priority | priyoridad | |
| owner | may-ari | |
| modified / created | binago / ginawa | |
| date / time | petsa / oras | |
| error / warning | error / babala | |
| help | tulong | |
| notification | notification | |
| wizard | wizard | |
| feed (RSS) | feed | |
| tab | tab | |
| audit, audit trail | audit, audit trail | |
| repository | repository | |
| checksum | checksum | |
| account | account | |
| default | default | default value: default na value |
| toolbar | toolbar | |
| container | container | |
| AI | AI | |
| pattern (name pattern) | pattern | |
| wildcard | wildcard | |
| remote ID | remote ID | |
| placeholder | placeholder | |
| collection | koleksyon | information collector: tagakolekta ng impormasyon |
| approve, approval | aprubahan, pag-apruba | |
| software | software | |
| creator / modifier | gumawa / nagbago | |
| request (HTTP) | request | |
| query | query | |
| handler / provider / listener | handler / provider / listener | |
| engine | engine | |
| release / upgrade | release / upgrade | |
| backup | backup | |
| maintenance | maintenance | |
| directory | direktoryo | |
| header (HTTP) | header | |
| page layout (pagelayout template) | layout ng pahina | |
| page | pahina | |
| passkey | passkey | |
| two-step sign-in | dalawang-hakbang na pag-sign in | |
| device / browser | device / browser | |
| link | link | |
| registration, register | pagpaparehistro, magparehistro | |
| payment gateway | payment gateway | |
| none | wala | "no ...": walang ... |
| yes / no | oo / hindi | |
| name | pangalan | |
| description | paglalarawan | |
| title | pamagat | |
| type | uri | |
| value | value | |
| list | listahan | |
| close | isara | |
| open | buksan | |
| show | ipakita | |
| update | i-update | |
| confirm | kumpirmahin | |
| send | ipadala | |
| configuration | configuration | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is tindahan, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is i-save.
- A message that consists only of a loanword Filipino interfaces keep in English ("Template", "Cache", "Server")
  stays as it is and is listed with its reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json`;
  wherever a sentence surrounds such a word, the sentence is Filipino.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in the same allowlist when a whole message consists of them.
