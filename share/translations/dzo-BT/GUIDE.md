# Dzongkha (dzo-BT): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Dzongkha (རྫོང་ཁ་), the national language of Bhutan, as the Dzongkha Development Commission (DDC)
fixes it and as Bhutanese government publications and the Dzongkha localisations of operating systems and office
software use it, in the neutral, polite register. Dzongkha is not Tibetan: it shares the script and the classical
spelling, but it has its own spoken-based grammar, its own verb endings and its own computing vocabulary. No other
locale falls back to `dzo-BT`.

- Word order is subject - object - verb; case particles follow the noun in the form the preceding letter requires:
  genitive གི་/ཀྱི་/གྱི་/འི་, locative ལུ་, agentive གིས་/ཀྱིས་/གྱིས་ (and the ergative ག་ spelling the DDC allows),
  ablative ལས་.
- Verbs end as spoken Dzongkha ends them: the copula ཨིན། (is), མེན། (is not), ཡོད། (there is), མིན་འདུག / མེདཔ་
  (there is not); the nominaliser པ་ / ཝ་ for verbal nouns; the completed form ཅི། / ཡི། / ཏེ། for results
  (བཞག་ཅི། saved, བཏོན་ཅི། removed). Where Tibetan (`bod-CN`) uses ཟིན། / བྱུང་། or ཡིན་ནམ། the Dzongkha form is used
  instead; do not carry a Tibetan verb ending over.
- Technical words: the established DDC ICT terms (the DDC English-Dzongkha ICT glossary and the Dzongkha localisations
  of GNOME, Ubuntu and LibreOffice). Tibetan (`bod-CN`) serves only to check the meaning of a term; where Dzongkha and
  Tibetan differ (ཆོག་ཚིག password against Tibetan གསང་གྲངས་; ཡིག་སྣོད་ file against Tibetan ཡིག་ཆ་; དྲྭ་ཚིགས་ website), the Dzongkha term is the one used here and the Tibetan one is not. Never
  transliterate an English word where a settled Dzongkha term exists; where none exists, a short descriptive phrase is
  better than an English word. Established loan transliterations in Dzongkha spelling (ཀེཤ cache, ཊེམ་པ་ལེཊ
  template) are accepted where the DDC lists them.
- Short, plain sentences; honorific (zhe-sa) forms only in the polite request, never for the machine.

## 2. Form of address

- The user is addressed politely, without a pronoun where Dzongkha can leave it out; where one is needed, ཁྱོད་རང་།
  (never the familiar ཁྱོད།).
- **Buttons, menu items and links**: the bare verb or verbal noun: སྲུང་བཞག (save), ཆ་མེད་གཏང་ (cancel),
  ཕྱིར་ལོག (back), བཏོན་གཏང་ (delete / remove), ཞུན་དག (edit), ཁ་སྐོང་ (add), གསར་བཟོ (create),
  འཚོལ་ (search), སེལ་འཐུ (select), གཏོང་ (send), མཐོང་ (view), དཔེ་སྐྲུན་ (publish), ཨཔ་ལོཌ (upload),
  ཌའུན་ལོཌ (download).
- **Titles, column headings and tabs**: nouns or noun phrases: སྒྲིག་བཀོད། (settings), ཡིག་སྒྱུར། (translation),
  ཁྲིམ་ཤོག (home page), མིང་།
- **Instructions**: the verb with གནང་ / ཞུ། where the source has please or gives an instruction: "དངོས་པོ་ཅིག་
  སེལ་འཐུ་འབད་ཞིནམ་ལས་ \"འགྲིག\" ལུ་ཨེབ་གནང་།".
- **Confirmations and questions**: the question particle ན་ / མ་ with the shad: "སྡེ་ཚན་འདི་བཏོན་གཏང་ནི་ངེས་ཏོག་ཏོ་
  ཨིན་ན།".
- **What the user can or may do**: "... འབད་ཚུགསཔ་ཨིན།", "... འབད་མི་ཚུགས།"; "You do not have permission" is
  "... གི་གནང་བ་མེདཔ་ཨིན།".
- **Messages about a result**: the completed form: "དྲན་ཐོ་སྲུང་བཞག་ཅི།", "ཡིག་སྣོད་ཨཔ་ལོཌ་འབད་ཚར་ཡི།".

## 3. Script and typography

- **Tibetan script** (U+0F00-U+0FFF), in the Joyig / Uchen style the DDC fixes for Dzongkha. Syllables are separated
  by the tsheg **་** (U+0F0B); words are not separated by spaces. The non-breaking tsheg (U+0F0C) is kept only where
  the existing locale file (`share/locale/dzo-BT.ini`) uses it. The Dzongkha-specific letters and stacks used for
  transliteration (ཧ, ཀྲ, ཊ, ཌ, བྷ, གྷ and the like) are written as the DDC writes them, never replaced by
  Latin.
- **Sentence end**: the shad **།** (U+0F0D) where the source has a full stop; the tsheg stays before the shad except
  after the syllable ག, where the shad follows directly (ག།); after ང the tsheg stays (ང་།). A space follows the
  shad inside a text, never before it. A question ends with the question particle and the shad. Colons after labels,
  ellipses and exclamation marks are kept as in the source.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder. The same for Latin words, code and
  product names inside a Dzongkha sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (the Dzongkha digits ༠-༩ are not used in the interface, although the month names in
  `dzo-BT.ini` still show them for the calendar).

## 4. Glossary

Binding for the kernel and every extension. Terms follow the DDC ICT glossary where it has one; the rows marked
"check" in the note are the ones native reviewers are asked to confirm first.

| English | Dzongkha | Note |
|---|---|---|
| content | ནང་དོན། | content object: ནང་དོན་དངོས་པོ། |
| object | དངོས་པོ། | |
| node | མདུད་ཚིག | main node: མདུད་ཚིག་གཙོ་བོ། parent node: ཕ་མདུད་ཚིག |
| location | གནས་ཁོངས། | main location: གནས་ཁོངས་གཙོ་བོ། |
| item (list entry) | རྣམ་གྲངས། | |
| tree | ཤིང་གི་བཟོ་རྣམ། | tree menu: ཤིང་གི་བཟོ་རྣམ་ཐོ་ཡིག content structure tree: ནང་དོན་ཤིང་བཟོ། (check) |
| class | རིགས། | the content class: ནང་དོན་རིགས། class group: རིགས་ཚོགས། |
| attribute | ངོ་བོའི་ཁྱད་ཆོས། | |
| datatype | གནད་སྡུད་རིགས། | |
| version | ཐོན་རིམ། | version history: ཐོན་རིམ་ལོ་རྒྱུས། |
| draft | ཟིན་བྲིས། | store draft: ཟིན་བྲིས་སྲུང་བཞག |
| publish | དཔེ་སྐྲུན། | published: དཔེ་སྐྲུན་འབད་ཡོདཔ། |
| send for publishing | དཔེ་སྐྲུན་གྱི་དོན་ལུ་གཏང་། | |
| discard | བསྐྱུར་བཏང་། | discard draft: ཟིན་བྲིས་བསྐྱུར་བཏང་། |
| archived / pending / rejected | ཡིག་མཛོད་ནང་བཞག་ཡོདཔ། / བསྒུགས་བཞིནམ། / ངོས་ལེན་མ་འབད་བའི། | |
| siteaccess | དྲྭ་ཚིགས་འཛུལ་སྒོ། | siteaccess as a setting name stays in code |
| site, website | དྲྭ་ཚིགས། | |
| section | སྡེ་ཚན། | the Exponential section; an INI section: INI སྡེ་ཚན། |
| role | འགན་ཁུར། | |
| policy | སྲིད་བྱུས། | |
| limitation | ཚད་བཀག | |
| permission | གནང་བ། | access denied: འཛུལ་ཞུགས་གནང་མ་སྟེར། |
| module | ཚན་པ། | |
| function | ལས་ཀ། | a PHP function: PHP function |
| user / user group | ལག་ལེན་པ། / ལག་ལེན་པའི་ཚོགས་སྡེ། | |
| group | ཚོགས་སྡེ། | |
| anonymous | མིང་མེད། | anonymous user: མིང་མེད་ལག་ལེན་པ། |
| administrator | འཛིན་སྐྱོང་པ། | administration (interface): འཛིན་སྐྱོང་། |
| username | ལག་ལེན་པའི་མིང་། | |
| password | ཆོག་ཚིག | Tibetan གསང་གྲངས་ is not used |
| log in / log out | ནང་འཛུལ། / ཕྱིར་འཐོན། | |
| e-mail | གློག་འཕྲིན། | e-mail address: གློག་འཕྲིན་ཁ་བྱང་། |
| cache | ཀེཤ | clear cache: ཀེཤ་གཙང་སྦྲ་འབད། (check) |
| template | ཊེམ་པ་ལེཊ | override template: ཚབ་བརྒྱབ་ཊེམ་པ་ལེཊ |
| override | ཚབ་བརྒྱབ། | |
| fallback | ཕྱིར་འབྲེལ། | fallback language: ཕྱིར་འབྲེལ་སྐད་ཡིག (check) |
| design | བཀོད་ཇུས། | |
| workflow | ལཱ་གི་རིམ་པ། | |
| trigger | སྐུལ་སློང་། | |
| event | བྱུང་རིམ། | |
| trash | ཁུག་མ། | empty trash: ཁུག་མ་བཀྲོང་གཏང་། move to trash: ཁུག་མ་ནང་སྤོ། (check; the DDC word for the recycle bin) |
| bookmark | དཔེ་རྟགས། | |
| settings | སྒྲིག་བཀོད། | configuration: རིམ་སྒྲིག |
| setup | སྒྲིག་བཙུགས། | the Setup tab |
| layout | བཀོད་ཐབས། | Exponential Layouts stays |
| block / zone | སྡེ་ཚན་གྱི་ཆ། / ས་ཁོངས། | |
| extension | རྒྱ་སྐྱེད། | |
| package | ཐུམ་སྒྲིལ། | |
| search | འཚོལ་ཞིབ། | search index: འཚོལ་ཞིབ་དཀར་ཆག |
| log | དྲན་ཐོ། | a log file: དྲན་ཐོ་ཡིག་སྣོད། never "log in" |
| queue | གྲལ་རིམ། | job queue: ལཱ་གི་གྲལ་རིམ། |
| job / task | ལཱ། | |
| cron | cron | |
| script | script | |
| kernel, core | ཀེར་ནཱལ། | the DDC accepts the transliteration; core: ལྟེ་བའི་ཆ། |
| server | སར་བར། | |
| database | གནད་སྡུད་མཛོད། | |
| folder / directory | སྣོད་ཐུམ། | directory: སྣོད་ཐོ། (check) |
| file | ཡིག་སྣོད། | Tibetan ཡིག་ཆ་ is not used |
| image | པར། | thumbnail: པར་ཆུང་ཀུ། |
| preview | སྔོན་མཐོང་། | |
| upload / download | ཨཔ་ལོཌ། / ཌའུན་ལོཌ། | |
| export / import | ཕྱིར་གཏང་། / ནང་འཁྱེར། | |
| remove | བཏོན་གཏང་། | take out of a list or place |
| delete | བཏོན་གཏང་། | destroy; deleted: བཏོན་གཏང་ཡི། (remove is told apart by its object: remove from the list: ཐོ་ཡིག་ནང་ལས་བཏོན།) |
| edit | ཞུན་དག | |
| editor | ཞུན་དག་པ། / ཞུན་དག་ཡིག་ཆས། | person / software |
| save / store | སྲུང་བཞག | the verb "Store" of the forms |
| Store (shop tab) | ཚོང་ཁང་། | the shop, as "Shop" |
| cancel | ཆ་མེད་གཏང་། | |
| OK | འགྲིག | |
| apply | ལག་ལེན་འཐབ། | |
| back | ཕྱིར་ལོག | |
| next / previous | རྗེས་མ། / སྔོན་མ། | |
| enable / disable | ལག་ལེན་འཐབ་བཅུག / ལག་ལེན་མ་འཐབ་བཅུག | enabled / disabled: ལག་ལེན་འཐབ་བཞིནམ། / ལག་ལེན་མ་འཐབ་པའི། |
| active / inactive | ཤུགས་ཅན། / ཤུགས་མེད། | |
| run | གཡོག་བཀོལ། | |
| start / stop | འགོ་བཙུགས། / མཚམས་བཞག | |
| session | ལཱ་ཚོགས། | |
| hidden / visible | གསང་བའི། / མཐོང་ཚུགསཔ། | hide / reveal: སྦ། / སྟོན། |
| archive | ཡིག་མཛོད། | |
| order (sort) | གོ་རིམ། | sort order: གོ་རིམ་སྒྲིག་ཐངས། |
| order (shop) | བཙོང་བཀོད། | |
| shop | ཚོང་ཁང་། | |
| basket | ཚོང་ཁུག | |
| product | ཐོན་རྫས། | |
| customer | ཉོ་མི། | |
| discount | རིན་གོང་ཕབ་ཆ། | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | རིན་གོང་། / དངུལ་ལོར། | |
| tag | རྟགས། | |
| translation | ཡིག་སྒྱུར། | |
| language | སྐད་ཡིག | main language: སྐད་ཡིག་གཙོ་བོ། |
| locale | ས་གནས་སྐད་ཡིག | |
| state | རྣམ་པ། | the object state |
| status | གནས་སྟངས། | |
| view (button) | མཐོང་། | a module view (noun): མཐོང་སྣང་། |
| format | རྣམ་གཞག | |
| sort | གོ་རིམ་སྒྲིག | |
| filter | འཚག་འདེམས། | |
| select | སེལ་འཐུ། | select all: ཆ་མཉམ་སེལ་འཐུ། selected: སེལ་འཐུ་འབད་ཡོདཔ། |
| copy / move | འདྲ་བཤུས། / སྤོ། | |
| add / create / new | ཁ་སྐོང་། / གསར་བཟོ། / གསརཔ། | |
| restore | ཕྱིར་གསོ། | |
| install / uninstall | གཞི་བཙུགས། / གཞི་བཙུགས་བཏོན་གཏང་། | |
| related objects | འབྲེལ་ཡོད་དངོས་པོ། | relation: འབྲེལ་བ། |
| dashboard | ལཱ་གི་ངོས། | |
| identifier / ID | ངོས་འཛིན་རྟགས། / ID | |
| priority | དམིགས་རིམ། | |
| owner | བདག་པོ། | |
| modified / created | བསྒྱུར་བཅོས་འབད་ཡོདཔ། / གསར་བཟོ་འབད་ཡོདཔ། | |
| date / time | ཚེས་གྲངས། / ཆུ་ཚོད། | |
| error / warning | འཛོལ་བ། / ཉེན་བརྡ། | |
| help | རོགས་པ། | |
| notification | བརྡ་ཐོ། | |
| account | རྩིས་ཐོ། | |
| default | སྔོན་སྒྲིག | |
| link | འབྲེལ་ཐག | |
| none / all | མེདཔ། / ཆ་མཉམ། | |
| yes / no | ཨིན། / མེན། | |
| name / title / description | མིང་། / མགོ་མཚན། / འགྲེལ་བཤད། | |
| value / type / list | གྲངས་ཐང་། / རིགས། / ཐོ་ཡིག | |
| page / path | ཤོག་ལེབ། / ལམ་ཐིག | |
| required / optional | ངེས་པར་དགོཔ། / རང་འདོད། | |
| options | གདམ་ཁ། | |
| subtree | ཡན་ལག་ཤིང་། | whole subtree: ཡན་ལག་ཤིང་ཧྲིལ་བུ། |
| browse | བརྡ་འཚོལ། | |
| feed (RSS) | ཕིཌི། | |
| form | འགེངས་ཤོག | tab (of a page): ཤོག་ཁྲ། |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ཚོང་ཁང་། the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is སྲུང་བཞག
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (ནང་འཛུལ།).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
- The catalogues were translated in October 2026 from the English source, machine-assisted. A review by native
  speakers, ideally against the current DDC ICT glossary, is welcome; the rows marked "check" in the glossary and the
  verb endings of section 1 are the first to review.
