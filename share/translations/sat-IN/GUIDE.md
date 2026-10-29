# Santali (sat-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Santali (ᱥᱟᱱᱛᱟᱲᱤ) in the Ol Chiki script, in the neutral, polite register of Santali schoolbooks,
newspapers and the Santali localisations of software. The locale's `LanguageName` is `ᱥᱟᱱᱛᱟᱲᱤ`. No other locale falls
back to `sat-IN`.

- Word order is subject - object - verb; case markers and postpositions follow the noun (ᱟᱜ genitive, ᱨᱮ locative,
  ᱠᱷᱚᱱ from, ᱥᱟᱶᱛᱮ with, ᱠᱮ object marker). Santali is an Austroasiatic language with its own grammar; it is never
  written after the pattern of Bengali or Hindi.
- Technical words: where Santali has a settled term it is used; otherwise the English word is written in Ol Chiki as
  Santali speakers say it (ᱯᱷᱟᱭᱤᱞ file, ᱯᱷᱚᱞᱰᱟᱨ folder, ᱥᱟᱨᱵᱟᱨ server, ᱯᱟᱥᱣᱚᱨᱰ password, ᱴᱮᱢᱯᱞᱮᱴ template),
  with Santali suffixes. Bengali (`ben-BD`) and Hindi (`hin-IN`) serve only to check the meaning of a term; never copy
  their grammar. Never leave an English word in Latin letters unless it is a product name or code.
- Short, plain sentences; no honorific forms beyond the polite request.

## 2. Form of address

- The user is addressed politely without a pronoun where Santali can leave it out; where one is needed, ᱟᱢ (you).
- **Buttons, menu items and links**: the bare verb stem, as Santali interfaces do: ᱨᱟᱠᱷᱟᱣ (save), ᱵᱟᱛᱟᱞ (cancel),
  ᱢᱮᱴᱟᱣ (delete), ᱥᱟᱯᱲᱟᱣ (edit), ᱡᱚᱲᱟᱣ (add), ᱛᱮᱭᱟᱨ (create), ᱯᱟᱱᱛᱮ (search), ᱥᱟᱠᱟᱣ (select), ᱞᱟᱹᱛᱩ (send),
  ᱧᱮᱞ (view), ᱥᱟᱦᱴᱟ (publish), ᱞᱟᱫᱮ (upload), ᱛᱮᱞᱟ (download), without the imperative ending ᱢᱮ.
- **Titles, column headings and tabs**: nouns or noun phrases: ᱥᱮᱴᱤᱝ, ᱛᱚᱨᱡᱚᱢᱟ, ᱢᱩᱞ ᱴᱷᱮᱜᱟ, ᱧᱩᱛᱩᱢ.
- **Instructions**: the verb with the polite ending ᱢᱮ, and ᱫᱟᱭᱠᱟᱛᱮ before it where the source has please:
  "ᱢᱤᱫ ᱟᱭᱴᱮᱢ ᱵᱟᱪᱷᱟᱣ ᱢᱮ ᱟᱨ \"ᱴᱷᱤᱠ\" ᱛᱟᱞᱢᱮ."
- **Confirmations and questions**: the question particle ᱥᱮ at the end: "ᱱᱚᱶᱟ ᱥᱮᱠᱥᱚᱱ ᱢᱮᱴᱟᱣ ᱫᱟᱲᱮᱭᱟᱜ ᱠᱟᱱᱟ ᱥᱮ?".
- **What the user can or may do**: "... ᱫᱟᱲᱮᱭᱟᱜ ᱟ" (can), "... ᱫᱟᱲᱮᱭᱟᱜ ᱠᱟᱱᱟ ᱵᱟᱝ" (cannot); "You do not have
  permission" is "ᱟᱢᱟᱜ ᱵᱷᱟᱜᱟ ᱵᱟᱝ ᱢᱮᱱᱟᱜᱼᱟ" in the form "permission is missing".
- **Messages about a result**: the completed form with ᱟᱠᱟᱫ / ᱠᱟᱱᱟ: "ᱜᱷᱟᱛᱤ ᱨᱟᱠᱷᱟᱣ ᱟᱠᱟᱫᱼᱟ", "ᱯᱷᱟᱭᱤᱞ ᱞᱟᱫᱮ ᱥᱟᱯᱲᱟᱣ
  ᱟᱠᱟᱫᱼᱟ".

## 3. Script and typography

- **Ol Chiki** (U+1C50-U+1C7F), the script the locale names. The letters ᱚ ᱛ ᱜ ᱝ ᱞ ᱟ ᱠ ᱡ ᱢ ᱣ ᱤ ᱥ ᱦ ᱧ ᱨ ᱩ ᱪ ᱫ ᱬ ᱭ ᱮ
  ᱯ ᱰ ᱱ ᱲ ᱳ ᱴ ᱵ ᱷ ᱶ and the marks ᱸ ᱹ ᱺ ᱻ ᱼ ᱽ are written as Unicode defines them; Ol Chiki has no capital letters.
  Words are separated by spaces.
- **Sentence end**: the Ol Chiki full stop **᱾** (U+1C7E) where the source has a full stop, with no space before it.
  The double mark **᱿** (U+1C7F) is not used in the interface (it ends a paragraph or verse). Question and exclamation
  marks, the comma, colons after labels and ellipses are kept as in the source (the Latin "?", "!", "," and ":" are
  what Santali text uses).
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON). English loanwords that are not names are written in Ol Chiki, as the guide's glossary shows.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder, so no Santali suffix follows it
  directly ("ᱞᱮᱠᱟ: %count" and not "%countᱟᱜ"). The same for Latin words, code and product names inside a Santali
  sentence: a space on each side.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (the Ol Chiki digits ᱐-᱙ are not used in the interface, because numbers come in through
  placeholders and the rest of the interface shows Latin digits).

## 4. Glossary

Binding for the kernel and every extension. Where a term is an English loan, it is written in Ol Chiki.

| English | Santali | Note |
|---|---|---|
| content | ᱨᱮᱫ | content object: ᱨᱮᱫ ᱡᱤᱱᱤᱥ |
| object | ᱡᱤᱱᱤᱥ | |
| node | ᱱᱳᱰ | loan; main node: ᱢᱩᱞ ᱱᱳᱰ parent node: ᱵᱟᱵᱟ ᱱᱳᱰ |
| location | ᱴᱷᱮᱜᱟ | main location: ᱢᱩᱞ ᱴᱷᱮᱜᱟ |
| item (list entry) | ᱟᱭᱴᱮᱢ | loan |
| tree | ᱫᱟᱨᱮ | tree menu: ᱫᱟᱨᱮ ᱢᱮᱱᱩ content structure tree: ᱨᱮᱫ ᱫᱟᱨᱮ |
| class | ᱠᱞᱟᱥ | loan; the content class: ᱨᱮᱫ ᱠᱞᱟᱥ class group: ᱠᱞᱟᱥ ᱫᱚᱞ |
| attribute | ᱜᱩᱱ | |
| datatype | ᱰᱟᱴᱟ ᱞᱮᱠᱟ | |
| version | ᱵᱷᱟᱨᱥᱚᱱ | loan; version history: ᱵᱷᱟᱨᱥᱚᱱ ᱤᱛᱷᱟᱥ |
| draft | ᱠᱟᱪᱷᱟ ᱜᱷᱟᱛᱤ | |
| publish | ᱥᱟᱦᱴᱟ | published: ᱥᱟᱦᱴᱟ ᱟᱠᱟᱫ |
| send for publishing | ᱥᱟᱦᱴᱟ ᱞᱟᱹᱜᱤᱫ ᱞᱟᱹᱛᱩ | |
| discard | ᱪᱷᱟᱰᱟᱣ | discard draft: ᱠᱟᱪᱷᱟ ᱜᱷᱟᱛᱤ ᱪᱷᱟᱰᱟᱣ |
| archived / pending / rejected | ᱟᱨᱠᱟᱭᱤᱵ / ᱡᱟᱜᱟᱣ ᱢᱮᱱᱟᱜ / ᱵᱟᱛᱟᱞ | |
| siteaccess | ᱥᱟᱭᱤᱴ ᱥᱟᱵᱷᱤᱥ | siteaccess as a setting name stays in code |
| site, website | ᱥᱟᱭᱤᱴ, ᱣᱮᱵᱥᱟᱭᱤᱴ | |
| section | ᱥᱮᱠᱥᱚᱱ | loan; the Exponential section; an INI section: INI ᱥᱮᱠᱥᱚᱱ |
| role | ᱵᱷᱩᱢᱤᱠᱟ | |
| policy | ᱱᱤᱥᱟᱱ ᱱᱤᱭᱚᱢ | |
| limitation | ᱵᱚᱸᱫᱷᱚᱱ | |
| permission | ᱵᱷᱟᱜᱟ | access denied: ᱥᱟᱵᱷᱤᱥ ᱵᱟᱝ ᱵᱷᱟᱜᱟ |
| module | ᱢᱚᱰᱭᱩᱞ | loan |
| function | ᱠᱟᱢᱤ | a PHP function: PHP function |
| user / user group | ᱵᱮᱣᱦᱟᱨ ᱠᱟᱹᱢᱤ / ᱵᱮᱣᱦᱟᱨ ᱫᱚᱞ | |
| group | ᱫᱚᱞ | |
| anonymous | ᱵᱟᱝ ᱪᱤᱱᱦᱟᱹ | anonymous user: ᱵᱟᱝ ᱪᱤᱱᱦᱟᱹ ᱵᱮᱣᱦᱟᱨ ᱠᱟᱹᱢᱤ |
| administrator | ᱮᱰᱢᱤᱱᱤᱥᱴᱨᱮᱴᱚᱨ | administration (interface): ᱯᱨᱚᱥᱟᱥᱚᱱ |
| username | ᱵᱮᱣᱦᱟᱨ ᱧᱩᱛᱩᱢ | |
| password | ᱯᱟᱥᱣᱚᱨᱰ | loan |
| log in / log out | ᱞᱚᱜ ᱤᱱ / ᱞᱚᱜ ᱟᱣᱴ | |
| e-mail | ᱤ-ᱢᱮᱞ | e-mail address: ᱤ-ᱢᱮᱞ ᱴᱷᱤᱠᱟᱱᱟ |
| cache | ᱠᱮᱥ | loan; clear cache: ᱠᱮᱥ ᱪᱟᱯᱷ |
| template | ᱴᱮᱢᱯᱞᱮᱴ | loan; override template: ᱚᱵᱷᱟᱨᱨᱟᱭᱤᱰ ᱴᱮᱢᱯᱞᱮᱴ |
| override | ᱚᱵᱷᱟᱨᱨᱟᱭᱤᱰ | |
| fallback | ᱯᱷᱚᱞᱵᱮᱠ | fallback language: ᱯᱷᱚᱞᱵᱮᱠ ᱯᱟᱨᱥᱤ |
| design | ᱰᱤᱡᱟᱭᱤᱱ | |
| workflow | ᱠᱟᱹᱢᱤ ᱨᱟᱦᱟ | |
| trigger | ᱴᱨᱤᱜᱟᱨ | |
| event | ᱯᱟᱱᱛᱮ ᱵᱮᱱᱟᱣ | |
| trash | ᱵᱷᱟᱜᱟᱱ ᱯᱷᱚᱞᱟ | empty trash: ᱵᱷᱟᱜᱟᱱ ᱯᱷᱚᱞᱟ ᱪᱟᱯᱷ move to trash: ᱵᱷᱟᱜᱟᱱ ᱯᱷᱚᱞᱟ ᱨᱮ ᱪᱟᱞᱟᱣ |
| bookmark | ᱵᱩᱠᱢᱟᱨᱠ | |
| settings | ᱥᱮᱴᱤᱝ | configuration: ᱠᱚᱱᱯᱷᱤᱜᱟᱨᱮᱥᱚᱱ |
| setup | ᱥᱮᱴᱟᱯ | the Setup tab |
| layout | ᱞᱮᱟᱣᱴ | Exponential Layouts stays |
| block / zone | ᱵᱞᱚᱠ / ᱡᱚᱱ | |
| extension | ᱮᱠᱥᱴᱮᱱᱥᱚᱱ | |
| package | ᱯᱮᱠᱮᱡ | |
| search | ᱯᱟᱱᱛᱮ | search index: ᱯᱟᱱᱛᱮ ᱤᱱᱰᱮᱠᱥ |
| log | ᱞᱚᱜ | a log file: ᱞᱚᱜ ᱯᱷᱟᱭᱤᱞ never "log in" |
| queue | ᱞᱟᱭᱤᱱ | job queue: ᱠᱟᱹᱢᱤ ᱞᱟᱭᱤᱱ |
| job / task | ᱠᱟᱹᱢᱤ | |
| cron | cron | |
| script | script | |
| kernel, core | ᱠᱟᱨᱱᱮᱞ | |
| server | ᱥᱟᱨᱵᱟᱨ | |
| database | ᱰᱟᱴᱟᱵᱮᱥ | |
| folder / directory | ᱯᱷᱚᱞᱰᱟᱨ | |
| file | ᱯᱷᱟᱭᱤᱞ | |
| image | ᱪᱤᱛᱟᱨ | thumbnail: ᱪᱤᱪᱟᱹ ᱪᱤᱛᱟᱨ |
| preview | ᱢᱩᱪᱟᱹᱫ ᱧᱮᱞ | |
| upload / download | ᱞᱟᱫᱮ / ᱛᱮᱞᱟ | |
| export / import | ᱮᱠᱥᱯᱚᱨᱴ / ᱤᱢᱯᱚᱨᱴ | |
| remove | ᱚᱪᱚᱜ | take out of a list or place |
| delete | ᱢᱮᱴᱟᱣ | destroy; deleted: ᱢᱮᱴᱟᱣ ᱟᱠᱟᱫ |
| edit | ᱥᱟᱯᱲᱟᱣ | |
| editor | ᱥᱟᱯᱲᱟᱣ ᱠᱟᱹᱢᱤ / ᱮᱰᱤᱴᱚᱨ | person / software |
| save / store | ᱨᱟᱠᱷᱟᱣ | the verb "Store" of the forms |
| Store (shop tab) | ᱫᱩᱠᱟᱱ | the shop, as "Shop" |
| cancel | ᱵᱟᱛᱟᱞ | |
| OK | ᱴᱷᱤᱠ | |
| apply | ᱞᱟᱜᱟᱣ | |
| back | ᱛᱟᱭᱚᱢ | |
| next / previous | ᱤᱱᱟᱹ / ᱢᱟᱲᱟᱝ | |
| enable / disable | ᱪᱟᱞᱟᱜ ᱠᱟᱛᱮ / ᱵᱚᱸᱫᱚ | enabled / disabled: ᱪᱟᱞᱟᱜ ᱟᱠᱟᱫ / ᱵᱚᱸᱫᱚ ᱟᱠᱟᱫ |
| active / inactive | ᱪᱟᱞᱟᱜ / ᱵᱟᱝ ᱪᱟᱞᱟᱜ | |
| run | ᱪᱟᱞᱟᱣ | |
| start / stop | ᱮᱛᱠᱟᱣ / ᱛᱷᱟᱢᱟᱣ | |
| session | ᱥᱮᱥᱚᱱ | loan |
| hidden / visible | ᱪᱟᱯᱟᱜ / ᱧᱮᱞ ᱫᱟᱲᱮᱭᱟᱜ | hide / reveal: ᱪᱟᱯᱟᱣ / ᱧᱮᱞᱟᱣ |
| archive | ᱟᱨᱠᱟᱭᱤᱵ | |
| order (sort) | ᱨᱟᱦᱟ | sort order: ᱨᱟᱦᱟ ᱥᱚᱨ |
| order (shop) | ᱚᱨᱰᱟᱨ | |
| shop | ᱫᱩᱠᱟᱱ | |
| basket | ᱵᱟᱥᱠᱮᱴ | |
| product | ᱡᱤᱱᱤᱥ ᱵᱮᱱᱟᱣ | |
| customer | ᱠᱟᱥᱴᱚᱢᱚᱨ | |
| discount | ᱥᱚᱥᱛᱟ | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | ᱫᱟᱢ / ᱴᱟᱠᱟ ᱵᱷᱮᱰ | |
| tag | ᱴᱮᱜ | loan |
| translation | ᱛᱚᱨᱡᱚᱢᱟ | |
| language | ᱯᱟᱨᱥᱤ | main language: ᱢᱩᱞ ᱯᱟᱨᱥᱤ |
| locale | ᱞᱚᱠᱮᱞ | |
| state | ᱟᱣᱛᱟ | the object state |
| status | ᱦᱟᱞᱛ | |
| view (button) | ᱧᱮᱞ | a module view (noun): ᱵᱷᱤᱭᱩ |
| format | ᱯᱷᱚᱨᱢᱮᱴ | |
| sort | ᱥᱚᱨ | |
| filter | ᱯᱷᱤᱞᱴᱟᱨ | |
| select | ᱵᱟᱪᱷᱟᱣ | select all: ᱡᱚᱛᱚ ᱵᱟᱪᱷᱟᱣ selected: ᱵᱟᱪᱷᱟᱣ ᱟᱠᱟᱫ |
| copy / move | ᱱᱚᱠᱚᱞ / ᱪᱟᱞᱟᱣ | |
| add / create / new | ᱡᱚᱲᱟᱣ / ᱛᱮᱭᱟᱨ / ᱱᱟᱣᱟ | |
| restore | ᱟᱨᱦᱚᱸ ᱥᱟᱯᱲᱟᱣ | |
| install / uninstall | ᱤᱱᱥᱴᱚᱞ / ᱟᱱᱤᱱᱥᱴᱚᱞ | |
| related objects | ᱵᱚᱸᱫᱷᱟᱣ ᱡᱤᱱᱤᱥ | relation: ᱵᱚᱸᱫᱷᱟᱣ |
| dashboard | ᱰᱮᱥᱵᱚᱨᱰ | |
| identifier / ID | ᱪᱤᱱᱦᱟᱹ / ID | |
| priority | ᱢᱟᱲᱟᱝ ᱜᱟᱱ | |
| owner | ᱢᱟᱞᱤᱠ | |
| modified / created | ᱵᱚᱫᱚᱞ ᱟᱠᱟᱫ / ᱛᱮᱭᱟᱨ ᱟᱠᱟᱫ | |
| date / time | ᱢᱟᱦᱟ / ᱚᱠᱛᱚ | |
| error / warning | ᱵᱷᱩᱞ / ᱪᱷᱚᱵᱷᱮᱱ | |
| help | ᱜᱚᱲᱚ | |
| notification | ᱠᱷᱚᱵᱚᱨ | |
| account | ᱮᱠᱟᱩᱱᱴ | |
| default | ᱰᱤᱯᱷᱚᱞᱴ | |
| link | ᱞᱤᱝᱠ | |
| none / all | ᱪᱮᱫᱚᱜ ᱵᱟᱝ / ᱡᱚᱛᱚ | |
| yes / no | ᱦᱚᱭ / ᱵᱟᱝ | |
| name / title / description | ᱧᱩᱛᱩᱢ / ᱵᱟᱰᱟᱭ / ᱵᱤᱵᱨᱚᱬ | |
| value / type / list | ᱢᱟᱱ / ᱞᱮᱠᱟ / ᱞᱤᱥᱴᱤ | |
| page / path | ᱯᱷᱚᱛᱟ / ᱯᱚᱛ | |
| required / optional | ᱞᱟᱹᱠᱛᱤᱭᱟᱱ / ᱨᱚᱥᱚᱠ | |
| options | ᱵᱟᱪᱷᱟᱣ ᱠᱚ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ᱫᱩᱠᱟᱱ, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  ᱨᱟᱠᱷᱟᱣ.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (ᱞᱚᱜ ᱤᱱ).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- The sentence end is ᱾ (U+1C7E) and the interface digits are 0-9, as section 3 states; both are decisions of this
  guide and apply to every catalogue of the language.
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
