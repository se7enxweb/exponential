# Standard Moroccan Tamazight (zgh-MA): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Moroccan Tamazight (ⵜⴰⵎⴰⵣⵉⵖⵜ), the standard language of the Royal Institute of Amazigh Culture (IRCAM),
in the neutral, plain written register of IRCAM's school books and terminology lists, Moroccan public signage and the
Tamazight pages of Moroccan administrations. No other locale falls back to `zgh-MA`. Kabyle (`kab-DZ`) is a close
relative and can help with the meaning of a Berber term, but zgh-MA follows the standard Moroccan vocabulary and is
written in Tifinagh.

- Tamazight words are used where they exist: ⵃⴹⵓ, ⵙⵔ, ⵙⵙⴼⴹ, ⴽⴽⵙ, ⵙⵏⴼⵍ, ⵔⵣⵓ, ⵙⵜⵉ, ⵔⵏⵓ, ⵙⵏⵓⵍⴼⵓ, ⵥⵕ, ⴰⵏⵙⵙⵎⵔⵙ,
  ⵜⴰⵡⵍⴰⴼⵜ, ⴰⴼⴰⵢⵍⵓ, ⴰⴽⴰⵔⴰⵎ, ⵜⵓⵜⵍⴰⵢⵜ.
- A loan word without a Tamazight one is written in Tifinagh with a Tamazight article: ⴰⵎⵓⵜⵓⵔ, ⴰⵙⴽⵔⵉⴱⵜ,
  ⵜⴰⴱⵍⵓⴽⵜ. Never a French or Arabic word in its own script inside a Tamazight sentence.
- Nouns have a free and an annexed state: ⴰⴼⴰⵢⵍⵓ -> ⵏ ⵓⴼⴰⵢⵍⵓ, ⴰⵏⵙⵙⵎⵔⵙ -> ⵏ ⵓⵏⵙⵙⵎⵔⵙ, ⵜⴰⵡⵍⴰⴼⵜ -> ⵏ ⵜⵡⵍⴰⴼⵜ.

## 2. Form of address

The interface addresses the user in the second person singular (**-ⴽ**, **ⵏⵏⴽ**): ⵜⴰⴳⵓⵔⵉ ⵏⵏⴽ ⵏ ⵓⵣⵔⴰⵢ (your
password).

- **Buttons, menu items and links**: the imperative singular: ⵃⴹⵓ, ⵙⵔ, ⵙⵙⴼⴹ, ⵙⵏⴼⵍ, ⵙⵙⵓⴼⵖ, ⴰⵣⵏ ⵉ ⵓⵙⵙⵓⴼⵖ, ⴽⴽⵙ
  ⵎⴰⵢⵏ ⵉⵜⵜⵓⵙⵜⴰⵏ, ⴰⵣⵏ ⵖⵔ ⵉⴹⵓⵎⵎⴰⵏ.
- **Titles, column headings and tabs**: nouns or noun phrases: ⵜⵉⵙⵖⴰⵍ, ⵉⵙⵙⵓⵖⵍⵏ, ⴰⴷⵖⴰⵔ ⴰⴳⵍⴷⴰⵏ.
- **Instructions**: the imperative, softened with "ⵎⴰ ⵜⵅⵙⴷ," where a request needs it: "ⵙⵜⵉ ⵢⴰⵜ ⵜⵖⴰⵡⵙⴰ ⵜⵙⵜⵜⵉⵜ
  «ⵡⴰⵅⵅⴰ».". A reference ("see ...") is "ⵥⵕ ...".
- **Confirmations and questions**: "ⵉⵙ ⵜⵅⵙⴷ ⵙ ⵜⵉⴷⵜ ⴰⴷ ⵜⵙⵙⴼⴹⴷ ⴰⵙⵎⵉⵍ ⴰ?", "ⵉⵙ ⵜⵅⵙⴷ ⴰⴷ ...?".
- **What the user can or may do**: "ⵜⵣⵎⵔⴷ ⴰⴷ ...", "ⵓⵔ ⵜⵣⵎⵉⵔⴷ ⴰⴷ ...", or impersonal. "You do not have permission" is
  "ⵓⵔ ⵖⵓⵔⴽ ⵜⵓⵔⴰⴳⵜ ...".
- Not allowed: slash forms.

## 3. Script and typography

- **Tifinagh**, the IRCAM alphabet (Unicode block Tifinagh, U+2D30-U+2D7F), the script of the shipped locale
  (`share/locale/zgh-MA.ini`, LanguageName ⵜⴰⵎⴰⵣⵉⵖⵜ). The 33 letters of IRCAM: ⴰ ⴱ ⴳ ⴳⵯ ⴷ ⴹ ⴻ ⴼ ⴽ ⴽⵯ ⵀ ⵃ ⵄ ⵅ ⵇ
  ⵉ ⵊ ⵍ ⵎ ⵏ ⵓ ⵔ ⵕ ⵖ ⵙ ⵚ ⵛ ⵜ ⵟ ⵡ ⵢ ⵣ ⵥ, with the labialisation mark ⵯ (U+2D6F). Tifinagh has no capital
  letters. The neutral vowel ⴻ is written only where the IRCAM spelling writes it. Neither the Berber Latin alphabet
  nor the Arabic script is used for Tamazight words here.
- **Latin text inside Tamazight**: product names, codes and acronyms stay in Latin letters as they are: "ⴰⴼⴰⵢⵍⵓ
  PHP", "URL", "ⵜⴰⵏⴳⵍⵜ HTML".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Never a letter glued to a placeholder: "%count ⵏ ⵜⵖⴰⵡⵙⵉⵡⵉⵏ" or
  "ⵜⵉⵖⴰⵡⵙⵉⵡⵉⵏ: %count".
- **Numbers**: the number comes before the noun, joined by "ⵏ": "%count ⵏ ⵉⴼⵓⵢⵍⴰ". Digits 0-9.
- **Quotation marks**: the guillemets «...» where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: the Latin marks, as in the source: comma, full stop, colon, question and exclamation mark, without
  a space before them. A label keeps its colon. Text runs left to right.

## 4. Glossary

Binding for the kernel and every extension.

| English | Tamazight | Note |
|---|---|---|
| content | ⴰⴳⴱⵓⵔ | |
| content object | ⵜⴰⵖⴰⵡⵙⴰ ⵏ ⵓⴳⴱⵓⵔ | |
| object | ⵜⴰⵖⴰⵡⵙⴰ | plural ⵜⵉⵖⴰⵡⵙⵉⵡⵉⵏ |
| node | ⵜⴰⴽⵔⵔⵉⵙⵜ | main node: ⵜⴰⴽⵔⵔⵉⵙⵜ ⵜⴰⴳⵍⴷⴰⵏⵜ; parent: ⵜⴰⴽⵔⵔⵉⵙⵜ ⵜⴰⵎⴰⵔⴰⵡⵜ; child: ⵜⴰⴽⵔⵔⵉⵙⵜ ⵜⴰⵔⵡⵉⵡⵜ |
| location | ⴰⴷⵖⴰⵔ | main location: ⴰⴷⵖⴰⵔ ⴰⴳⵍⴷⴰⵏ |
| item (list entry) | ⴰⴼⵔⴷⵉⵙ | |
| class | ⴰⵙⵎⵉⵍ | the content class; class group: ⵜⴰⵔⴰⴱⴱⵓⵜ ⵏ ⵉⵙⵎⵉⵍⵏ; a PHP class: ⴰⵙⵎⵉⵍ PHP |
| content class | ⴰⵙⵎⵉⵍ ⵏ ⵓⴳⴱⵓⵔ | |
| attribute | ⴰⵢⵍⴰ | |
| datatype | ⴰⵏⴰⵡ ⵏ ⵉⵙⴼⴽⴰ | |
| version | ⵜⵓⵏⵖⵉⵍⵜ | version history: ⴰⵎⵣⵔⵓⵢ ⵏ ⵜⵓⵏⵖⵉⵍⵉⵏ |
| draft | ⴰⵔⵡⵡⴰⵢ | store draft: ⵃⴹⵓ ⴰⵔⵡⵡⴰⵢ |
| publish | ⵙⵙⵓⴼⵖ | published: ⵉⵜⵜⵓⵙⵓⴼⵖ |
| send for publishing | ⴰⵣⵏ ⵉ ⵓⵙⵙⵓⴼⵖ | |
| discard | ⴰⵊⵊ | discard draft: ⴰⵊⵊ ⴰⵔⵡⵡⴰⵢ |
| archived | ⵉⵜⵜⵓⵃⵔⵣ | |
| pending | ⵉⵜⵜⵔⴰⵊⵓ | |
| rejected | ⵓⵔ ⵉⵜⵜⵓⵇⴱⵍ | |
| siteaccess | ⴰⴽⵛⵛⵓⵎ ⵖⵔ ⵓⵙⵎⵍ | the configuration unit |
| site, website | ⴰⵙⵎⵍ, ⴰⵙⵎⵍ ⵡⵉⴱ | |
| section | ⵜⵉⴳⵣⵎⵉ | the Exponential section |
| role | ⴰⴷⵡⵡⵓⵔ | |
| policy | ⵜⴰⵙⵔⵜⵉⵜ | |
| limitation | ⵜⴰⵍⵉⵙⵜ | |
| permission | ⵜⵓⵔⴰⴳⵜ | access rights: ⵜⵓⵔⴰⴳⵉⵏ ⵏ ⵓⴽⵛⵛⵓⵎ |
| module | ⴰⵎⵓⴷⵉⵍ | |
| function | ⵜⴰⵡⵓⵔⵉ | |
| user | ⴰⵏⵙⵙⵎⵔⵙ | plural ⵉⵏⵙⵙⵎⵔⵙⵏ |
| user group | ⵜⴰⵔⴰⴱⴱⵓⵜ ⵏ ⵉⵏⵙⵙⵎⵔⵙⵏ | |
| group | ⵜⴰⵔⴰⴱⴱⵓⵜ | |
| anonymous | ⴰⵔⵉⵙⵎ | anonymous user: ⴰⵏⵙⵙⵎⵔⵙ ⴰⵔⵉⵙⵎ |
| administrator | ⴰⵏⴱⴰⴹ | |
| administration (interface) | ⵜⴰⵏⴱⴰⴹⵜ | |
| username | ⵉⵙⵎ ⵏ ⵓⵏⵙⵙⵎⵔⵙ | the column "Login" too |
| password | ⵜⴰⴳⵓⵔⵉ ⵏ ⵓⵣⵔⴰⵢ | |
| log in / sign in | ⴽⵛⵎ | button: ⴽⵛⵎ |
| log out / sign out | ⴼⴼⵖ | button: ⴼⴼⵖ |
| e-mail | ⵉⵎⴰⵢⵍ | e-mail address: ⵜⴰⵏⵙⴰ ⵏ ⵉⵎⴰⵢⵍ |
| cache | ⵜⴰⵣⵔⴽⴰⵜⵓⵜ | clear cache: ⵙⵙⴼⴹ ⵜⴰⵣⵔⴽⴰⵜⵓⵜ |
| template | ⵜⴰⵎⵓⴷⵎⵜ | |
| override | ⴰⵙⵏⴼⵍ ⵏ ⵓⵎⴽⴰⵏ | override template: ⵜⴰⵎⵓⴷⵎⵜ ⵏ ⵓⵙⵏⴼⵍ ⵏ ⵓⵎⴽⴰⵏ |
| fallback | ⵜⵓⵖⴰⵍⵉⵏ | fallback language: ⵜⵓⵜⵍⴰⵢⵜ ⵏ ⵜⵓⵖⴰⵍⵉⵏ |
| design | ⴰⵙⵓⵏⵖ | |
| workflow | ⴰⴱⵔⵉⴷ ⵏ ⵓⵎⵀⵍ | |
| trigger | ⴰⵎⵙⵏⵜⵉ | |
| event | ⵜⴰⴷⵢⴰⵏⵜ | |
| trash | ⵉⴹⵓⵎⵎⴰⵏ | empty trash: ⵙⵙⴼⴹ ⵉⴹⵓⵎⵎⴰⵏ |
| bookmark | ⵜⴰⵛⵔⴹⵜ | |
| settings | ⵜⵉⵙⵖⴰⵍ | |
| setup | ⴰⵙⵔⵙ | the Setup tab; installation too |
| layout | ⴰⵙⵓⴷⵙ | Exponential Layouts |
| block | ⵜⴰⴱⵍⵓⴽⵜ | |
| zone | ⵜⴰⵎⵏⴰⴹⵜ | |
| extension | ⴰⵙⵉⵖⵣⴼ | |
| package | ⴰⴽⵎⵎⵓⵙ | |
| search | ⵔⵣⵓ | button: ⵔⵣⵓ |
| log | ⴰⵖⵎⵉⵙ | log file: ⴰⴼⴰⵢⵍⵓ ⵏ ⵓⵖⵎⵉⵙ |
| queue | ⴰⴼⵔⴷ | |
| job / task | ⴰⵎⵀⵍ | |
| cron | cron | cron job: ⴰⵎⵀⵍ cron |
| script | ⴰⵙⴽⵔⵉⴱⵜ | |
| worker | ⴰⵎⵙⵡⵓⵔⵉ | kept apart from ⴰⵏⵙⵙⵎⵔⵙ (user) |
| kernel, core | ⴰⵥⴰⵕ | |
| server | ⴰⵇⴷⴷⴰⵛ | |
| database | ⵜⴰⵙⴰⵏⴷⵓⵇⵜ ⵏ ⵉⵙⴼⴽⴰ | |
| folder | ⴰⴽⴰⵔⴰⵎ | |
| file | ⴰⴼⴰⵢⵍⵓ | plural ⵉⴼⵓⵢⵍⴰ |
| image | ⵜⴰⵡⵍⴰⴼⵜ | |
| preview | ⴰⵙⴽⴰⵏ | |
| upload | ⵙⴰⵍⵉ | button: ⵙⴰⵍⵉ |
| download | ⴰⴳⵎ | button: ⴰⴳⵎ |
| export / import | ⵙⵙⵉⴼⴹ / ⵙⴽⵛⵎ | |
| remove | ⴽⴽⵙ | take out of a list or a place |
| delete | ⵙⵙⴼⴹ | destroy |
| edit | ⵙⵏⴼⵍ | |
| editor | ⴰⵎⵙⵏⴼⵍ | |
| save / store | ⵃⴹⵓ | |
| Store (shop tab) | ⵜⴰⵃⴰⵏⵓⵜ | the word the shop menu uses for "Shop" |
| cancel | ⵙⵔ | |
| OK | ⵡⴰⵅⵅⴰ | |
| apply | ⵙⵙⵎⵔⵙ | |
| back | ⵓⵖⵓⵍ | |
| next / previous | ⵓⴹⴼⵉⵔ / ⴰⵣⵡⴰⵔ | |
| enable / disable | ⵔⵎⴷ / ⵙⵏⵙ | a setting or feature |
| enabled / disabled | ⵢⵓⵔⵎⴷ / ⵢⵏⵙⴰ | |
| active / inactive | ⵢⵓⵔⵎⴷ / ⵓⵔ ⵢⵓⵔⵎⵉⴷ | |
| run | ⵙⴷⵓ | a script or job |
| start / stop | ⵙⵏⵜⵉ / ⵙⴱⴷⴷ | |
| session | ⵜⵉⵖⵉⵎⵉⵜ | |
| hidden / visible | ⵉⵏⵜⵍ / ⵉⵜⵜⴱⴰⵏ | hide / reveal: ⵙⵏⵜⵍ / ⵙⴽⵏ |
| archive | ⴰⵃⵔⴰⵣ | a file archive (.zip): ⴰⵃⵔⴰⵣ ⵏ ⵉⴼⵓⵢⵍⴰ |
| order (sort) | ⴰⵙⵎⵣⵡⵔ | sort: ⵙⵎⵣⵡⵔ |
| order (shop) | ⵜⴰⵙⵓⵜⵔⵜ | |
| shop | ⵜⴰⵃⴰⵏⵓⵜ | |
| basket | ⵜⴰⵇⴼⵓⴼⵜ | |
| product | ⴰⴼⴰⵔⵉⵙ | |
| customer | ⴰⵎⵙⴰⵖ | |
| discount | ⴰⵙⵏⵇⵙ | |
| VAT | TVA | the abbreviation used in Morocco |
| price | ⴰⵜⵉⴳ | |
| currency | ⵜⴰⴷⵔⵉⵎⵜ | |
| tag | ⵜⴰⵎⴰⵜⴰⵔⵜ | keyword tag; an HTML tag: ⵜⴰⵎⴰⵜⴰⵔⵜ HTML |
| translation | ⴰⵙⵙⵓⵖⵍ | |
| language | ⵜⵓⵜⵍⴰⵢⵜ | main language: ⵜⵓⵜⵍⴰⵢⵜ ⵜⴰⴳⵍⴷⴰⵏⵜ |
| locale | ⴰⵙⵖⵍ ⴰⴷⵖⴰⵔⴰⵏ | |
| state | ⴰⴷⴷⴰⴷ | the object state; state group: ⵜⴰⵔⴰⴱⴱⵓⵜ ⵏ ⵡⴰⴷⴷⴰⴷⵏ |
| status | ⴰⵙⵡⵉⵔ | |
| view (button) | ⵥⵕ | a module view: ⵜⴰⵎⵥⵕⵉ |
| format | ⵜⴰⵍⵖⴰ | |
| sort | ⵙⵎⵣⵡⵔ | |
| filter | ⴰⵙⵣⴷⴳ | |
| select | ⵙⵜⵉ | selected: ⵉⵜⵜⵓⵙⵜⴰⵏ |
| browse | ⵉⵏⵉⴳ | |
| copy | ⵙⵏⵖⵍ | a copy: ⵜⴰⵏⵖⵍⵜ |
| move | ⵙⵎⵓⵜⵜⵉ | |
| add | ⵔⵏⵓ | |
| create | ⵙⵏⵓⵍⴼⵓ | |
| new | ⴰⵎⴰⵢⵏⵓ | |
| restore | ⵔⵓⵔ | |
| install / uninstall | ⵙⵔⵙ / ⴽⴽⵙ ⴰⵙⵔⵙ | |
| sub items | ⵉⴼⵔⴷⵉⵙⵏ ⵏ ⴷⴷⴰⵡ | |
| subtree | ⴰⴼⵔⴰⴽ ⵏ ⴷⴷⴰⵡ | |
| related objects | ⵜⵉⵖⴰⵡⵙⵉⵡⵉⵏ ⵉⵇⵇⵏⵏ | relation: ⴰⵙⵖⵏ |
| URL alias | ⵢⴰⵏ ⵢⵉⵙⵎ ⵏ URL | |
| redirect | ⴰⵙⵏⴼⵍ ⵏ ⵓⴱⵔⵉⴷ | |
| dashboard | ⵜⴰⴼⵍⵡⵉⵜ ⵏ ⵜⵏⴱⴰⴹⵜ | |
| content structure | ⵜⴰⵙⵙⵓⴷⵙⵜ ⵏ ⵓⴳⴱⵓⵔ | |
| media library | ⵜⴰⵙⴷⵍⵉⵙⵜ ⵏ ⵉⵎⵉⴷⵢⴰⵜⵏ | |
| identifier / ID | ⴰⵙⵓⵍⴰⵢ / ID | |
| priority | ⵜⴰⵣⵡⴰⵔⵜ | |
| owner | ⴱⴰⴱ | |
| modified / created | ⵉⵜⵜⵓⵙⵏⴼⵍ / ⵉⵜⵜⵓⵙⵏⵓⵍⴼⴰ | |
| date / time | ⴰⵙⴰⴽⵓⴷ / ⴰⴽⵓⴷ | |
| error / warning | ⵜⴰⵣⴳⵍⵜ / ⴰⵍⵖⵓ | |
| help | ⵜⵉⵡⵉⵙⵉ | |
| notification | ⴰⵏⵖⵎⵉⵙ | |
| wizard | ⴰⵎⴰⵔⴰⴳ | |
| feed (RSS) | ⴰⵙⵓⴷⴷⵎ | RSS feed: ⴰⵙⵓⴷⴷⵎ RSS |
| tab | ⵉⵛⵛⵔ | |
| audit, audit trail | ⴰⵙⵏⵇⴷ, ⴰⵖⵎⵉⵙ ⵏ ⵓⵙⵏⵇⴷ | |
| repository | ⴰⴽⵓⴼⵉ | |
| checksum | ⴰⵙⵎⵉⴹⵏ ⵏ ⵓⵙⵏⵇⴷ | |
| account | ⴰⵎⵉⴹⴰⵏ | |
| default | ⴰⵎⵣⵡⴰⵔⵓ | default value: ⴰⵣⴰⵍ ⴰⵎⵣⵡⴰⵔⵓ |
| toolbar | ⴰⴼⴳⴳⴰⴳ ⵏ ⵉⵎⴰⵙⵙⵏ | |
| container | ⴰⵎⴰⴳⴱⴰⵔ | |
| AI | AI | stays as the acronym |
| pattern (name pattern) | ⴰⵎⴷⵢⴰ | kept apart from ⵜⴰⵎⵓⴷⵎⵜ (template) |
| wildcard | ⴰⵊⵓⴽⵔ | |
| remote ID | ⴰⵙⵓⵍⴰⵢ ⴰⴳⴳⵓⴳ | |
| placeholder | ⴰⵎⴹⵉⵇ | |
| collection | ⵜⴰⴳⵔⵓⵎⵎⴰ | information collector: ⴰⵎⵙⴳⵔⵡ ⵏ ⵉⵏⵖⵎⵉⵙⵏ |
| approve, approval | ⵇⴱⵍ, ⴰⵇⴱⴰⵍ | |
| software | ⴰⵙⵖⵣⴰⵏ | |
| creator / modifier | ⴰⵎⵙⵏⵓⵍⴼⵓ / ⵡⵏⵏⴰ ⵉⵙⵏⴼⵍⵏ | |
| request (HTTP) | ⴰⵙⵓⵜⵔ | |
| query | ⴰⵙⵇⵙⵉ | search query: ⴰⵙⵇⵙⵉ ⵏ ⵓⵔⵣⵣⵓ |
| handler / provider / listener | ⴰⵎⵙⴼⵔⴰⴽ / ⴰⵙⴰⴳⴳⴰⵡ / ⴰⵎⵙⴼⵍⴷ | |
| engine | ⴰⵎⵓⵜⵓⵔ | |
| release / upgrade | ⴰⵙⵙⵓⴼⵖ / ⴰⵙⵎⴰⵢⵏⵓ | update: ⵙⵎⴰⵢⵏⵓ |
| backup | ⵜⴰⵏⵖⵍⵜ ⵏ ⵓⵃⴹⵓ | |
| maintenance | ⴰⵙⵖⵓⴷⵓ | |
| directory | ⴰⴽⴰⵔⴰⵎ | |
| header (HTTP) | ⴰⵇⵔⵔⵓ | |
| page layout (pagelayout template) | ⵜⴰⵎⵓⴷⵎⵜ ⵏ ⵜⵙⵏⴰ | kept apart from ⴰⵙⵓⴷⵙ (Exponential Layouts) |
| passkey | ⵜⴰⵙⴰⵔⵓⵜ ⵏ ⵓⴽⵛⵛⵓⵎ | |
| two-step sign-in | ⴰⴽⵛⵛⵓⵎ ⵙ ⵙⵏⴰⵜ ⵜⵉⴽⴽⴰⵍ | |
| device / browser | ⴰⵎⴰⵙⵙ / ⵉⵎⵉⵏⵉⴳ | |
| link | ⴰⵥⴷⴰⵢ | |
| registration, register | ⴰⵣⵎⵎⵎ, ⵣⵎⵎⵎ | |
| payment gateway | ⵜⴰⵡⵡⵓⵔⵜ ⵏ ⵓⴼⵔⵓ | |
| form | ⵜⴰⴼⵔⴽⵉⵜ | |
| comment | ⵜⴰⵡⵏⵏⵉⵜ | |
| newsletter / subscription | ⵜⴰⴱⵔⴰⵜ ⵏ ⵉⵏⵖⵎⵉⵙⵏ / ⴰⵎⵓⵍⵜⵖ | |
| field | ⴰⵏⵙⴰ | |
| value | ⴰⵣⴰⵍ | |
| list | ⵜⴰⵍⴳⴰⵎⵜ | |
| page | ⵜⴰⵙⵏⴰ | |
| title / name | ⴰⵣⵡⵍ / ⵉⵙⵎ | the column "Name": ⵉⵙⵎ |
| description | ⴰⴳⵍⴰⵎ | |
| type | ⴰⵏⴰⵡ | |
| path | ⴰⴱⵔⵉⴷ | |
| width / height | ⵜⵉⵀⵔⵉ / ⵜⵉⵖⵣⵉ | |
| size | ⵜⵉⴷⴷⵉ | |
| none | ⵓⵍⴰ ⵢⴰⵏ | |
| yes / no | ⵢⴰⵀ / ⵓⵀⵓ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ⵜⴰⵃⴰⵏⵓⵜ, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is ⵃⴹⵓ.
- "Delete" is ⵙⵙⴼⴹ (destroy) and "remove" is ⴽⴽⵙ (take out), so that the two buttons are never confused; "publish"
  is ⵙⵙⵓⴼⵖ.
- "OK" is ⵡⴰⵅⵅⴰ.
