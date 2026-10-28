# Tibetan (bod-CN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Tibetan (བོད་སྐད་) in its modern form, close to the Lhasa standard, in the neutral, polite register of
Tibetan news, schoolbooks and the Tibetan localisations of operating systems and office software. No other locale falls
back to `bod-CN`.

- Word order is subject - object - verb; case particles follow the noun (གི་/ཀྱི་/གྱི་/འི་/ཡི་, ལ་/དུ་/ར་/སུ་,
  ནས་/ལས་, གིས་/ཀྱིས་/གྱིས་) in the form the preceding letter requires.
- Technical words: the established Tibetan computing terms (ཡིག་ཆ་ file, ཡིག་སྣོད་ folder, གཞི་གྲངས་ data,
  གསང་གྲངས་ password, དྲ་ཚིགས་ website, གློག་འཕྲིན་ e-mail). Chinese (`chi-CN`) serves only to check the meaning
  of a term; never transliterate a Chinese word. Never leave the English word unless it is a product name or code;
  where Tibetan has no settled term, a short descriptive phrase is better than an English word.
- Short, plain sentences; no honorific forms beyond the polite request.

## 2. Form of address

- The user is addressed politely without a pronoun where Tibetan can leave it out; where one is needed, ཁྱེད་རང་།
- **Buttons, menu items and links**: the bare verb or verbal noun, as Tibetan interfaces do: ཉར་ཚགས། (save),
  ཕྱིར་འཐེན། (cancel), བསུབ། (delete), ཕྱིར་འདོན། (remove), རྩོམ་སྒྲིག (edit), ཁ་སྣོན། (add), གསར་བཟོ། (create),
  འཚོལ་བཤེར། (search), འདེམས། (select), བསྐུར། (send), ལྟ་བ། (view), འགྲེམས་སྤེལ། (publish), ཡར་འཇུག (upload),
  ཕབ་ལེན། (download).
- **Titles, column headings and tabs**: nouns or noun phrases: སྒྲིག་འགོད། ཡིག་སྒྱུར། གནས་ས་གཙོ་བོ། མིང་།
- **Instructions**: the verb with རོགས། where the source has please or gives an instruction: "དངོས་པོ་ཞིག་འདེམས་ནས་
  \"ཆོག\" མནན་རོགས།".
- **Confirmations and questions**: "... ཐག་གཅོད་ཡིན་ནམ།" or "... འདོད་དམ།": "སྡེ་ཚན་འདི་བསུབ་རྒྱུ་ཐག་གཅོད་ཡིན་ནམ།".
- **What the user can or may do**: "... ཐུབ།", "... མི་ཐུབ།"; "You do not have permission" is "... ཆོག་མཆན་མེད།".
- **Messages about a result**: the completed form: "ཟིན་བྲིས་ཉར་ཚགས་བྱས་ཟིན།", "ཡིག་ཆ་ཡར་འཇུག་ལེགས་གྲུབ་བྱུང་།".

## 3. Script and typography

- **Tibetan script** (U+0F00-U+0FFF). Syllables are separated by the tsheg **་** (U+0F0B); words are not separated by
  spaces.
- **Sentence end**: the shad **།** (U+0F0D) where the source has a full stop; after the syllable ང the tsheg stays
  before the shad (ང་།); after ཀ and ག no shad is written (or the tsheg ends the syllable). A space follows the shad
  inside a text, never before it. A question ends with the question particle and the shad. Colons after labels,
  ellipses and exclamation marks are kept as in the source.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder. The same for Latin words, code and
  product names inside a Tibetan sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (Tibetan digits are not used in the interface).

## 4. Glossary

Binding for the kernel and every extension.

| English | Tibetan | Note |
|---|---|---|
| content | ནང་དོན། | content object: ནང་དོན་དངོས་པོ། |
| object | དངོས་པོ། | |
| node | མདུད་ཚེག | main node: མདུད་ཚེག་གཙོ་བོ། parent node: ཕ་མདུད་ཚེག |
| location | གནས་ས། | main location: གནས་ས་གཙོ་བོ། |
| item (list entry) | རྣམ་གྲངས། | |
| tree | སྡོང་པོ། | tree menu: སྡོང་པོའི་ཐོ་གཞུང་། content structure tree: ནང་དོན་སྡོང་པོ། |
| class | རིགས། | the content class: ནང་དོན་རིགས། class group: རིགས་ཚོ། |
| attribute | ཁྱད་ཆོས། | |
| datatype | གཞི་གྲངས་རིགས། | |
| version | པར་གཞི། | version history: པར་གཞིའི་ལོ་རྒྱུས། |
| draft | ཟིན་བྲིས། | store draft: ཟིན་བྲིས་ཉར་ཚགས། |
| publish | འགྲེམས་སྤེལ། | published: འགྲེམས་སྤེལ་བྱས་ཟིན། |
| send for publishing | འགྲེམས་སྤེལ་ལ་བསྐུར། | |
| discard | དོར། | discard draft: ཟིན་བྲིས་དོར། |
| archived / pending / rejected | ཡིག་མཛོད་དུ་བཞག / སྒུག་བཞིན་པ། / ངོས་ལེན་མ་བྱས། | |
| siteaccess | དྲ་ཚིགས་འཛུལ་སྒོ། | siteaccess as a setting name stays in code |
| site, website | དྲ་ཚིགས། | |
| section | སྡེ་ཚན། | the Exponential section; an INI section: INI སྡེ་ཚན། |
| role | འགན་ཁུར། | |
| policy | སྲིད་ཇུས། | |
| limitation | ཚད་བཀག | |
| permission | ཆོག་མཆན། | access denied: འཛུལ་ཆོག་མེད། |
| module | ཚན་པ། | |
| function | ནུས་པ། | a PHP function: PHP function |
| user / user group | སྤྱོད་མཁན། / སྤྱོད་མཁན་ཚོ་ཆུང་། | |
| group | ཚོ་ཆུང་། | |
| anonymous | མིང་མེད། | anonymous user: མིང་མེད་སྤྱོད་མཁན། |
| administrator | དོ་དམ་པ། | administration (interface): དོ་དམ། |
| username | སྤྱོད་མཁན་མིང་། | |
| password | གསང་གྲངས། | |
| log in / log out | ནང་འཛུལ། / ཕྱིར་ཐོན། | |
| e-mail | གློག་འཕྲིན། | e-mail address: གློག་འཕྲིན་ཁ་བྱང་། |
| cache | མྱུར་ཉར། | clear cache: མྱུར་ཉར་གཙང་སེལ། |
| template | མ་དཔེ། | override template: ཚབ་བྱེད་མ་དཔེ། |
| override | ཚབ་བྱེད། | |
| fallback | རྒྱབ་རྟེན། | fallback language: རྒྱབ་རྟེན་སྐད་ཡིག |
| design | ཇུས་འགོད། | |
| workflow | ལས་རིམ། | |
| trigger | སྐུལ་སློང་། | |
| event | བྱུང་རིམ། | |
| trash | གད་སྙིགས་སྒམ། | empty trash: གད་སྙིགས་སྒམ་གཙང་སེལ། move to trash: གད་སྙིགས་སྒམ་དུ་སྤོ། |
| bookmark | དེབ་རྟགས། | |
| settings | སྒྲིག་འགོད། | configuration: སྒྲིག་འགོད། |
| setup | སྒྲིག་བཀོད། | the Setup tab |
| layout | བཀོད་སྒྲིག | Exponential Layouts stays |
| block / zone | ཚན་ཁག / ཁུལ། | |
| extension | རྒྱ་བསྐྱེད། | |
| package | ཐུམ་སྒྲིལ། | |
| search | འཚོལ་བཤེར། | search index: འཚོལ་བཤེར་དཀར་ཆག |
| log | ཟིན་ཐོ། | a log file: ཟིན་ཐོའི་ཡིག་ཆ། never "log in" |
| queue | གྲལ་རིམ། | job queue: ལས་འགན་གྲལ་རིམ། |
| job / task | ལས་འགན། | |
| cron | cron | |
| script | script | |
| kernel, core | ནང་སྙིང་། | |
| server | ཞབས་ཞུ་འཕྲུལ་ཆས། | |
| database | གཞི་གྲངས་མཛོད། | |
| folder / directory | ཡིག་སྣོད། | |
| file | ཡིག་ཆ། | |
| image | པར་རིས། | thumbnail: པར་རིས་ཆུང་བ། |
| preview | སྔོན་ལྟ། | |
| upload / download | ཡར་འཇུག / ཕབ་ལེན། | |
| export / import | ཕྱིར་འདྲེན། / ནང་འདྲེན། | |
| remove | ཕྱིར་འདོན། | take out of a list or place |
| delete | བསུབ། | destroy; deleted: བསུབ་ཟིན། |
| edit | རྩོམ་སྒྲིག | |
| editor | རྩོམ་སྒྲིག་པ། / རྩོམ་སྒྲིག་ཆས། | person / software |
| save / store | ཉར་ཚགས། | the verb "Store" of the forms |
| Store (shop tab) | ཚོང་ཁང་། | the shop, as "Shop" |
| cancel | ཕྱིར་འཐེན། | |
| OK | ཆོག | |
| apply | ལག་བསྟར། | |
| back | ཕྱིར་ལོག | |
| next / previous | རྗེས་མ། / སྔོན་མ། | |
| enable / disable | སྤྱོད་འགོ་ཚུགས། / སྤྱོད་མཚམས་འཇོག | enabled / disabled: སྤྱོད་བཞིན་པ། / སྤྱོད་མཚམས་བཞག |
| active / inactive | འཁོར་སྐྱོད་བཞིན་པ། / འཁོར་སྐྱོད་མེད་པ། | |
| run | འཁོར་སྐྱོད། | |
| start / stop | འགོ་འཛུགས། / མཚམས་འཇོག | |
| session | ཐེངས་ཚན། | |
| hidden / visible | སྦས་པ། / མཐོང་ཐུབ། | hide / reveal: སྦས། / མངོན། |
| archive | ཡིག་མཛོད། | |
| order (sort) | གོ་རིམ། | sort order: གོ་རིམ་སྒྲིག་སྟངས། |
| order (shop) | ཉོ་འཆར། | |
| shop | ཚོང་ཁང་། | |
| basket | ཉོ་སྒམ། | |
| product | ཐོན་རྫས། | |
| customer | ཉོ་མཁན། | |
| discount | རིན་གཅོག | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | རིན་གོང་། / དངུལ་ལོར། | |
| tag | རྟགས་བྱང་། | |
| translation | ཡིག་སྒྱུར། | |
| language | སྐད་ཡིག | main language: སྐད་ཡིག་གཙོ་བོ། |
| locale | ས་གནས་སྐད་ཡིག | |
| state | རྣམ་པ། | the object state |
| status | གནས་སྟངས། | |
| view (button) | ལྟ་བ། | a module view (noun): མཐོང་སྣང་། |
| format | རྣམ་གཞག | |
| sort | གོ་རིམ་སྒྲིག | |
| filter | འཚག་འདེམས། | |
| select | འདེམས། | select all: ཚང་མ་འདེམས། selected: བདམས་ཟིན། |
| copy / move | འདྲ་བཤུས། / སྤོ། | |
| add / create / new | ཁ་སྣོན། / གསར་བཟོ། / གསར་པ། | |
| restore | སླར་གསོ། | |
| install / uninstall | སྒྲིག་འཇུག / སྒྲིག་འཇུག་མེད་པ་བཟོ། | |
| related objects | འབྲེལ་ཡོད་དངོས་པོ། | relation: འབྲེལ་བ། |
| dashboard | ལས་ཀའི་ངོས་ལེབ། | |
| identifier / ID | ངོས་འཛིན་རྟགས། / ID | |
| priority | སྔོན་འགྲོ། | |
| owner | བདག་པོ། | |
| modified / created | བཟོ་བཅོས་བྱས། / གསར་བཟོ་བྱས། | |
| date / time | ཚེས་གྲངས། / དུས་ཚོད། | |
| error / warning | ནོར་འཁྲུལ། / ཉེན་བརྡ། | |
| help | རོགས་རམ། | |
| notification | བརྡ་ཐོ། | |
| account | ཐོ་ཁོངས། | |
| default | སོར་བཞག | |
| link | སྦྲེལ་ཐག | |
| none / all | མེད། / ཚང་མ། | |
| yes / no | ཡིན། / མིན། | |
| name / title / description | མིང་། / ཁ་བྱང་། / འགྲེལ་བཤད། | |
| value / type / list | གྲངས་ཐང་། / རིགས་དབྱེ། / ཐོ་གཞུང་། | |
| page / path | ཤོག་ངོས། / ལམ་ཐིག | |
| required / optional | ངེས་པར་དགོས། / རང་མོས། | |
| options | འདེམས་ཚན། | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ཚོང་ཁང་། the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is ཉར་ཚགས།
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (ནང་འཛུལ།).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
