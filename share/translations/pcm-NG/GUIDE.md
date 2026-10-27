# Nigerian Pidgin (pcm-NG): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Nigerian Pidgin (Naijá) as written by the BBC News Pidgin service and Nigerian websites: an English-based spelling
that every reader of English can follow, with Pidgin grammar and words. Clear, friendly and respectful, not street
slang and not comic. No other locale falls back to `pcm-NG`.

- **Grammar, not just spelling.** A Pidgin sentence uses the Pidgin system, never English with a few words changed:
  - tense and aspect: **dey** (is ...ing, habitual: "di system dey save am"), **don** (has/have, done: "di file don
    upload"), **go** (will: "e go delete am"), **bin** (past, only where needed);
  - **na** (it is, focus: "Na di main language be dis"), **be** (is, with a noun: "Dis one be draft"), **dey** (is,
    located or exists: "E dey for di trash"); **no** before the verb for "not" (e no dey, you no fit);
  - **di** (the), **dis / dat** (this / that), **dem** after a noun for the plural where English needs it ("di users
    dem"), **wey** (which, who, that: "di file wey you choose"), **am** (him, her, it, them as object), **e** (he,
    she, it as subject), **una** (you, plural), **for** (in, at, on), **wit** (with), **from**, **go** (to a place);
  - **fit** (can: "you fit change am"), **no fit** (cannot), **wan** (want), **make** (let, should: "make you
    choose"), **abeg** (please), **wetin** (what), **sey / say** (that, after say/know/sure: "you sure say ...").
- **Words**: a word stays the English word where Nigerians use it in Pidgin, which is most technical vocabulary
  (file, folder, password, server, database, template, cache, email, upload, download, settings). Prefer the Pidgin
  word where there is a common one: comot (remove, take out), carry (take, move: "carry am go"), sabi (know), wahala
  (problem, trouble), plenty (many), small (a little, few), well well (fully, very), sharp sharp (quickly),
  shey (question opener: is it?), chop (only in its food sense: never in the interface).
- Do not write a whole sentence in English. Do not overdo it either: one Pidgin marker in each clause is enough to
  make the sentence Pidgin; an extra "o" or "sha" at the end is not used.

## 2. Form of address

- The user is **you** (one person). Messages to the user say **you**; "una" only where English speaks to all
  users ("una fit ...").
- **Buttons, menu items and links**: the short verb or verb phrase, as people say it: Save, Cancel, Delete, Edit,
  Add, Create, Search, Choose, Send, Publish, Upload, Download. Where the button has an object, use Pidgin order and
  words: "Save di draft", "Comot am from list", "Carry go trash", "Choose all". A one-word button that is the same
  English word is right in Pidgin and is marked identical_ok ("the word Nigerian Pidgin uses").
- **Titles, column headings and tabs**: nouns or noun phrases. A plural heading may stay the plain noun (Users,
  Settings) or take dem ("Di users dem") - choose the short form for headings: "Users", "Settings". A heading that is
  a phrase follows Pidgin order: "Content wey dem relate", "Wetin dey your basket".
- **Instructions**: the bare verb, with **abeg** where English has "please": "Choose di item, den press \"OK\".",
  "Abeg wait make e finish save di priority.". A reference ("see ...") is "check ...".
- **Confirmations and questions**: "You sure say you wan ...?": "You sure say you wan delete dis class?".
- **What the user can or may do**: "You fit ...", "You no fit ...", "Dem no allow you ...". "You do not have
  permission" is "You no get permission ...".
- **Messages about a result**: "Di draft don save.", "Di file don upload well well.", "E no work: ...".
- **Errors**: say plainly what happened: "Wahala dey: di file no dey.", "Something go wrong wen e dey save di
  object.".

## 3. Script and typography

- **Latin script**, the English-based spelling of BBC News Pidgin: dey, don, wey, wetin, dem, dis, dat, di, wit,
  sabi, comot, abeg, una, sey. Not the phonetic Naijá orthography (dé, kom), no tone marks. An English word keeps
  its English spelling (file, folder, password).
- **Capitals**: as English does it in the interface: the first word of a button, title or sentence and proper
  names: "Save di draft".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after: "%count objects". The same for code, file names, INI settings and
  product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9. A plural after a number is the English plural or the plain noun: "%count items".

## 4. Glossary

Binding for the kernel and every extension. Where the Pidgin column is the English word, the word is the one
Nigerian Pidgin uses; it is used as it is in every sentence.

| English | Nigerian Pidgin | Note |
|---|---|---|
| content | content | content object: content object |
| object | object | |
| node | node | main node: main node |
| location | location | main location: main location; where something dey: wia |
| item (list entry) | item | |
| class | class | the content class; a PHP class: PHP class |
| attribute | attribute | |
| version | version | version history: di history of versions |
| draft | draft | store draft: Save di draft |
| publish | publish | published: e don publish / published; send for publishing: Send am make dem publish am |
| discard | throw away | discard draft: Throw di draft away |
| archived / pending / rejected | archived / e still dey wait / dem reject am | as column values: Archived / Pending / Rejected stay |
| siteaccess | siteaccess | |
| site, website | site / website | |
| section | section | the Exponential section |
| role / policy / limitation | role / policy / limitation | |
| permission | permission | access denied: dem no allow you enter |
| user / user group | user / user group | the person: di person wey dey use am |
| anonymous | anonymous | anonymous user: user wey no get name |
| administrator | administrator | admin |
| username / password | username / password | login (the name): login name |
| log in / log out | log in / log out | |
| e-mail | email | |
| cache | cache | clear cache: Clear di cache |
| template / override / fallback | template / override / fallback | |
| workflow / trigger / event | workflow / trigger / event | |
| trash | trash | empty trash: Empty di trash; move to trash: Carry go trash |
| bookmark | bookmark | |
| settings | settings | |
| layout / block / zone | layout / block / zone | |
| extension / package / module | extension / package / module | |
| search | search | search index: search index |
| log | log | a log file; never "log in" |
| queue / job / task | queue / job / work | |
| script / server / database | script / server / database | |
| folder / file / image | folder / file / picture | thumbnail: small picture |
| preview | preview | the act: see how e go look |
| upload / download | upload / download | |
| export / import | export / import | |
| remove | comot | take out of a list or place: comot am from ... |
| delete | delete | destroy: e don delete |
| edit | edit | change: change am |
| save / store | save | the verb "Store" of the forms |
| Store (shop tab) | shop | the shop, as "Shop" |
| cancel | cancel | |
| OK | OK | |
| back | go back | |
| next / previous | next / previous | |
| enable / disable | on / off | switch am on / switch am off; enabled / disabled: e dey on / e dey off |
| active / inactive | active / no active | |
| run / start / stop | run / start / stop | |
| hidden / visible | hidden / people fit see am | hide / reveal: hide / show |
| order (sort) | order | sort order: di order wey dem arrange am |
| order (shop) | order | |
| shop / basket | shop / basket | |
| product / customer / price | product / customer / price | |
| discount / VAT / currency | discount / VAT / currency | |
| tag | tag | |
| translation / language | translation / language | main language: main language |
| state / status | state / status | |
| view (button) | view | a module view (noun): view |
| select | choose | select all: choose all; selected: wey you choose |
| copy / move | copy / carry go | |
| add / create / new | add / create / new | |
| restore | bring back | |
| install / uninstall | install / uninstall | |
| related objects | objects wey join am | relation: relation |
| dashboard | dashboard | |
| owner | owner | |
| modified / created | changed / created | |
| date / time | date / time | |
| error / warning | wahala / warning | as a heading: Error stays |
| help | help | |
| none / all | nothing / all | |
| yes / no | yes / no | |
| name / title / description | name / title / description | |
| required / optional | you must fill am / you fit leave am | as a field label: Required / Optional stay |

## 5. Decisions after the translation

- The header tab "Store" of the shop is the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  Save.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Log in).
- Words spelled the same in Nigerian Pidgin as in English (Save, Cancel, File, Settings ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them,
  as are product names, codes and acronyms.
