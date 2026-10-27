# Oromo (orm-ET): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Oromo (Afaan Oromoo) as written in Ethiopia, in the **Qubee** Latin alphabet, in the neutral, polite
register of the Oromia regional government, Oromo news services (BBC Afaan Oromoo, OBN) and Oromo Wikipedia (om).
No other locale falls back to `orm-ET`.

- Oromo word order is subject - object - verb; the verb comes last. Use Oromo function words: fi (and), yookiin
  (or), garuu (but), yoo ... ta'e (if), kun / kana (this), sun / sana (that), hunda (all), tokko tokko (some), kan
  (which, of), keessa (in), irraa (from), irratti (on), waliin (with), f / -fi / dhaaf (for), hin ... (not), jira /
  hin jiru (there is / is not), dha (is).
- Technical words: an Oromo word where one is in use (faayilii, fayyadamaa, jecha icciitii, barbaachisaa); an
  English loan adapted to Oromo spelling where Oromo writers use it (sarvarii, kuusaa deetaa). Never leave the
  English word unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **isin** (polite plural), with the matching verb forms (isin ... dandeessu, isin ... barbaaddu). In
  instructions the polite imperative in -aa / -i (plural): Filadhaa, Eegaa.
- **Buttons, menu items and links**: the singular imperative, short, as Oromo interfaces do: Olkaa'i (save),
  Dhiisi (cancel), Haqi (delete), Baasi (remove), Gulaali (edit), Ida'i (add), Uumi (create), Barbaadi (search),
  Fili (select), Ergi (send), Ilaali (view), Maxxansi (publish), Fe'i (upload), Buusi (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Qindaa'ina, Hiikkaawwan, Bakka ijoo, Maqaa.
- **Instructions**: the polite plural imperative, with "Maaloo" where English has "please": "Wanta filadhaa,
  achiis \"OK\" tuqaa.", "Maaloo hanga dursi olkaa'amutti eegaa.". A reference ("see ...") is "... ilaalaa".
- **Confirmations and questions**: "Dhuguma ... barbaadduu?": "Dhuguma kutaa kana haquu barbaadduu?".
- **What the user can or may do**: "... dandeessu", "... hin dandeessan", or impersonal: "Afaan ijoo jijjiiruun hin
  danda'amu ...". "You do not have permission" is "... hayyama hin qabdan".
- **Messages about a result**: the passive past in -ame: "Wixineen olkaa'ame.", "Faayiliin milkaa'inaan fe'ame.".

## 3. Script and typography

- **Qubee** (Latin): double vowels for long vowels (aa, ee, ii, oo, uu), double consonants for geminates (dd, mm,
  tt), the digraphs ch, dh, ny, ph, sh, and the apostrophe ' (hudhaa) for the glottal stop: ta'e, fe'i, olkaa'i.
  Always the straight apostrophe ' (U+0027), never ’.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A case ending cannot be written onto a placeholder (%namen would be
  a different placeholder): put the placeholder in quotation marks and the ending after the closing mark
  ("%name"n), or use a noun that carries it (faayilii %name). The same for code, file names, INI settings and
  product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9; a noun after a number stays singular: "wantoota %count" or "%count wanta".

## 4. Glossary

Binding for the kernel and every extension.

| English | Oromo | Note |
|---|---|---|
| content | qabiyyee | content object: wanta qabiyyee |
| object | wanta | plural: wantoota |
| node | noodii | main node: noodii ijoo; parent node: noodii maatii |
| location | bakka | main location: bakka ijoo |
| item (list entry) | wanta | |
| class | gita | the content class; class group: garee gitaa |
| attribute | amala | |
| datatype | gosa deetaa | |
| version | fooyya'iinsa | version history: seenaa fooyya'iinsaa |
| draft | wixinee | store draft: Wixinee olkaa'i |
| publish | maxxansi | published: maxxanfame; publishing: maxxansa |
| send for publishing | maxxansaaf ergi | |
| discard | gatadhu | discard draft: Wixinee gatadhu |
| archived / pending / rejected | kuufame / eegaa jira / didame | |
| siteaccess | seensa marsariitii | siteaccess as a setting name stays in code |
| site, website | marsariitii | |
| section | kutaa | the Exponential section; an INI section: kutaa INI |
| role | gahee | |
| policy | imaammata | one rule of a role |
| limitation | daangaa | |
| permission | hayyama | access denied: seensi dhorkameera |
| module | moojulii | |
| function | hojii | a module function; a PHP function: faankishinii |
| user / user group | fayyadamaa / garee fayyadamtootaa | plural: fayyadamtoota |
| group | garee | |
| anonymous | maqaa hin qabne | anonymous user: fayyadamaa maqaa hin qabne |
| administrator | bulchaa | administration (interface): bulchiinsa |
| username | maqaa fayyadamaa | login (the name): maqaa seensaa |
| password | jecha icciitii | |
| log in / log out | seeni / ba'i | the act: seensa / ba'uu |
| e-mail | imeelii | e-mail address: teessoo imeelii |
| cache | kuusaa yeroo | clear cache: Kuusaa yeroo qulqulleessi |
| template | unka | override template: unka bakka bu'aa |
| override | bakka bu'aa | |
| fallback | filannoo lammaffaa | fallback language: afaan lammaffaa |
| design | dizaayinii | |
| workflow | adeemsa hojii | |
| trigger | kakaasaa | |
| event | taatee | |
| trash | kuusaa kosii | empty trash: Kuusaa kosii duwwaasi; move to trash: Gara kuusaa kosiitti ergi |
| bookmark | mallattoo | |
| settings | qindaa'ina | configuration: qindeessa |
| setup | qopheessa | the Setup tab |
| layout | caasaa fuulaa | Exponential Layouts |
| block / zone | bilookii / zoonii | |
| extension | dabalata | |
| package | paakeejii | |
| search | barbaacha | to search: barbaadi; search index: indeeksii barbaachaa |
| log | galmee | a log file: faayilii galmee; never "log in" |
| queue | tarree eegumsaa | job queue: tarree hojiiwwanii |
| job / task | hojii | |
| cron | cron | |
| script | iskiriptii | |
| kernel, core | handhuura | |
| server | sarvarii | |
| database | kuusaa deetaa | |
| folder / directory | kuusaa faayilii | |
| file | faayilii | |
| image | suuraa | thumbnail: suuraa xiqqaa |
| preview | dursee ilaali | |
| upload / download | fe'i / buusi | |
| export / import | al ergi / galchi | |
| remove | baasi | take out of a list or place |
| delete | haqi | destroy; deleted: haqameera |
| edit | gulaali | |
| editor | gulaalaa | |
| save / store | olkaa'i | the verb "Store" of the forms |
| Store (shop tab) | suuqii | the shop, as "Shop" |
| cancel | dhiisi | |
| OK | Tole | |
| apply | hojiirra oolchi | |
| back | duubatti | |
| next / previous | itti aanu / kan duraa | |
| enable / disable | dandeessisi / dhaabi | enabled / disabled: hojii irra jira / dhaabbateera |
| active / inactive | hojii irra / hojii irra hin jiru | |
| run | hojjechiisi | |
| start / stop | jalqabi / dhaabi | |
| session | sashaa | |
| hidden / visible | dhokataa / mul'ataa | hide / reveal: dhoksi / agarsiisi |
| archive | kuusaa | |
| order (sort) | tartiiba | sort order: tartiiba qindaa'inaa |
| order (shop) | ajaja | |
| shop | suuqii | |
| basket | gaaguraa | |
| product | oomisha | |
| customer | maamila | |
| discount | hir'isa gatii | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | gatii / maallaqa | |
| tag | mallattoo jechaa | an HTML tag: tag HTML |
| translation | hiikkaa | plural: hiikkaawwan |
| language | afaan | main language: afaan ijoo |
| locale | naannoo | |
| state | haala | the object state |
| status | sadarkaa | kept apart from haala |
| view (button) | ilaali | a module view (noun): mul'ata |
| format | bifa | |
| sort | tartiibessi | |
| filter | calaqqisi | the filter: calalaa |
| select | fili | select all: Hunda fili; selected: filatame |
| copy / move | garagalchi / sochoosi | |
| add / create / new | ida'i / uumi / haaraa | |
| restore | deebisi | |
| install / uninstall | fe'i / baasi | install software: diriirsi; uninstall: diriirsa haqi |
| related objects | wantoota walqabatan | relation: walitti dhufeenya |
| dashboard | gabatee to'annoo | |
| identifier / ID | eenyummeessaa / ID | |
| priority | dursa | |
| owner | abbaa | |
| modified / created | jijjiirame / uumame | |
| date / time | guyyaa / yeroo | |
| error / warning | dogoggora / akeekkachiisa | |
| help | gargaarsa | |
| notification | beeksisa | |
| account | herrega | |
| default | durtii | |
| link | hidhaa | |
| none / all | homaa / hunda | |
| yes / no | eeyyee / lakki | |
| name / title / description | maqaa / mata duree / ibsa | |
| value / type / list | gatii / gosa / tarree | |
| page / path | fuula / karaa | |
| required / optional | dirqama / filannoo | |
| options | filannoowwan | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is suuqii, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is olkaa'i.
- "Login" in the user, session and audit lists is the user's login name (maqaa seensaa); the Login button of the
  error page is the act (Seeni).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
