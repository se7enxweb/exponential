# Cebuano (ceb-PH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Cebuano (Sinugbuanong Binisaya) as used in Cebu, Bohol, the Visayas and Mindanao, in the Latin
alphabet, in the neutral, polite register of Cebuano news (Superbalita, Sun.Star Superbalita), the Cebuano
localisations of Google and Facebook and Cebuano Wikipedia. No other locale falls back to `ceb-PH`.

- Word order is verb - subject - object as in Cebuano prose; use the Cebuano function words: ug (and), o (or), apan
  / pero (but), kon (if), kini / niini (this), kana (that), tanan (all), ang / sa / sa mga (articles), dili (not),
  wala (none / there is no), aduna / adunay (there is), alang sa (for).
- Technical words: a Cebuano word where one is in use (tiggamit, pangita, pilia, tipigi); a Spanish or English loan
  in Cebuano spelling where Cebuano writers use it (file -> file, server, database, kompyuter, bersyon, seksyon,
  klase, opsyon). Filipino (fil-PH) is a related language and may help with structure, but write Cebuano, never
  Tagalog (ang mga -> ang mga, hindi -> dili, at -> ug, mag-save -> tipigi).
- Short, plain sentences.

## 2. Form of address

- The user is **ikaw / nimo / imong** (singular "you", as Cebuano software and news address the reader), polite by
  word choice ("palihug" for please).
- **Buttons, menu items and links**: the short imperative (the -a / -i / -on forms), as Cebuano interfaces do:
  Tipigi (save), Kanselaha (cancel), Papasa / Wagtanga (delete: Papasa), Kuhaa (remove), Usba (edit), Idugang (add),
  Paghimo (create), Pangitaa (search), Pilia (select), Ipadala (send), Tan-awa (view), I-publish (publish), I-upload
  (upload), I-download (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Mga setting, Mga hubad, Panguna nga lokasyon, Ngalan.
- **Instructions**: "Palihug ..." with the imperative: "Palihug pilia ang butang ug i-klik ang \"OK\".".
- **Confirmations and questions**: "Sigurado ka ba nga gusto nimong ...?": "Sigurado ka ba nga gusto nimong papason
  kini nga seksyon?".
- **What the user can or may do**: "Mahimo nimong ...", "Dili nimo mahimo ...", "You do not have permission" is
  "Wala kay pagtugot sa ...".
- **Messages about a result**: the past passive: "Natipigan ang draft.", "Malampusong na-upload ang file.".

## 3. Script and typography

- **Latin** alphabet; the hyphen of the prefixes before a loan (i-upload, na-save, gi-publish) and of reduplications
  (tan-awa).
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder. The same for code, file names, INI
  settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Cebuano | Note |
|---|---|---|
| content | sulod | content object: butang sa sulod |
| object | butang | plural: mga butang |
| node | node | main node: panguna nga node; parent node: ginikanan nga node |
| location | lokasyon | main location: panguna nga lokasyon |
| item (list entry) | butang | |
| class | klase | the content class; class group: grupo sa klase |
| attribute | kinaiya | |
| datatype | matang sa datos | |
| version | bersyon | version history: kasaysayan sa bersyon |
| draft | draft | store draft: Tipigi ang draft |
| publish | i-publish | published: na-publish; publishing: pagpublikar |
| send for publishing | ipadala aron i-publish | |
| discard | isalikway | discard draft: Isalikway ang draft |
| archived / pending / rejected | na-archive / naghulat / gisalikway | |
| siteaccess | access sa site | siteaccess as a setting name stays in code |
| site, website | site, website | |
| section | seksyon | the Exponential section; an INI section: seksyon sa INI |
| role | papel | |
| policy | palisiya | |
| limitation | limitasyon | |
| permission | pagtugot | access denied: gidili ang pag-access |
| module | module | |
| function | function | |
| user / user group | tiggamit / grupo sa tiggamit | plural: mga tiggamit |
| group | grupo | |
| anonymous | anonimo | anonymous user: anonimo nga tiggamit |
| administrator | administrador | administration (interface): administrasyon |
| username | ngalan sa tiggamit | login (the name): ngalan sa pag-login |
| password | password | |
| log in / log out | mag-login / mag-logout | the act: pag-login / pag-logout |
| e-mail | e-mail | e-mail address: e-mail address |
| cache | cache | clear cache: Limpyohi ang cache |
| template | template | override template: puli nga template |
| override | puli | |
| fallback | alternatibo | fallback language: alternatibo nga pinulongan |
| design | disenyo | |
| workflow | dagan sa trabaho | |
| trigger | trigger | |
| event | panghitabo | |
| trash | basurahan | empty trash: Hawani ang basurahan; move to trash: Ibalhin sa basurahan |
| bookmark | bookmark | |
| settings | mga setting | configuration: konfigurasyon |
| setup | pag-setup | the Setup tab |
| layout | layout | |
| block / zone | block / sona | |
| extension | extension | |
| package | pakete | |
| search | pagpangita | to search: pangitaa; search index: indeks sa pagpangita |
| log | log | a log file: log file; never "log in" |
| queue | linya | job queue: linya sa trabaho |
| job / task | trabaho / buluhaton | |
| script | script | |
| kernel, core | kinauyokan | |
| server | server | |
| database | database | |
| folder / directory | folder / direktoryo | |
| file | file | |
| image | hulagway | thumbnail: gamay nga hulagway |
| preview | preview | |
| upload / download | i-upload / i-download | |
| export / import | i-export / i-import | |
| remove | kuhaa | take out of a list or place |
| delete | papasa | destroy; deleted: napapas |
| edit | usba | |
| editor | editor | |
| save / store | tipigi | the verb "Store" of the forms |
| Store (shop tab) | tindahan | the shop, as "Shop" |
| cancel | kanselaha | |
| OK | OK | allowlisted |
| apply | i-apply | |
| back | balik | |
| next / previous | sunod / miaging | |
| enable / disable | i-enable / i-disable | enabled / disabled: naka-enable / naka-disable |
| active / inactive | aktibo / dili aktibo | |
| run | padagana | |
| start / stop | sugdi / hunonga | |
| session | sesyon | |
| hidden / visible | tinago / makita | hide / reveal: tagoa / ipakita |
| archive | archive | |
| order (sort) | han-ay | sort order: han-ay sa paghan-ay |
| order (shop) | order | |
| shop | tindahan | |
| basket | basket | |
| product | produkto | |
| customer | kustomer | |
| discount | diskwento | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | presyo / kwarta | |
| tag | tag | |
| translation | hubad | plural: mga hubad |
| language | pinulongan | main language: panguna nga pinulongan |
| locale | lokal | |
| state | kahimtang | the object state |
| status | status | |
| view (button) | tan-awa | a module view (noun): talan-awon |
| format | pormat | |
| sort | han-aya | |
| filter | salaa | the filter: salaan |
| select | pilia | select all: Pilia tanan; selected: napili |
| copy / move | kopyaha / ibalhin | |
| add / create / new | idugang / paghimo / bag-o | |
| restore | ibalik | |
| install / uninstall | i-install / i-uninstall | |
| related objects | may kalabotan nga mga butang | relation: relasyon |
| dashboard | dashboard | |
| identifier / ID | identifier / ID | |
| priority | prayoridad | |
| owner | tag-iya | |
| modified / created | giusab / gihimo | |
| date / time | petsa / oras | |
| error / warning | sayop / pasidaan | |
| help | tabang | |
| notification | pahibalo | |
| account | account | |
| default | default | |
| link | link | |
| none / all | wala / tanan | |
| yes / no | oo / dili | |
| name / title / description | ngalan / titulo / paghulagway | |
| value / type / list | bili / matang / lista | |
| page / path | panid / dalan | |
| required / optional | gikinahanglan / opsyonal | |
| options | mga opsyon | |
| custom | pinasibo | |
| landscape / portrait | pahigda / patindog | |
| redirect | i-redirect | the noun: pag-redirect |

A whole message is left in English only when it is a product name, a code, a file name or an acronym; ordinary
words, also loans like workflow or draft, follow the glossary (dagan sa trabaho, draft in a sentence).

## 5. Decisions after the translation

- The header tab "Store" of the shop is tindahan, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is tipigi.
- "Login" in the user, session and audit lists is the user's login name (ngalan sa pag-login); the Login button of the
  error page is the act (Mag-login).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Loans that Cebuano writes like English (file, server, database, cache, template, OK, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
