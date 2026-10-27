# Aramaic in Syriac script (syr-IQ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Modern Aramaic in Syriac script (ܣܘܪܝܝܐ, Assyrian/Syriac), in the written standard of modern Assyrian
publishing in Iraq: the grammar of the modern language, the technical vocabulary taken from literary Syriac
(ܟܬܒܢܝܐ) wherever it has a word, so that readers of the East and the West Syriac tradition both understand it.
The locale is `syr-IQ` (CLDR `syr_IQ`); `syr-SY` (Syria) has no catalogue of its own and falls back to `syr-IQ`
through the automatic fallback of the same language (`AutomaticLanguageFallback=enabled` in `settings/i18n.ini`).

- **Unvocalized text.** No vowel points (neither the East nor the West Syriac system), no qushshaya or rukkakha
  dots. The only mark that is written is the **seyame** (two dots, U+0308) on plural nouns and adjectives:
  ܡܦܠܚ̈ܢܐ, ܦܢܩ̈ܝܬܐ. It sits on one letter of the word, as in the locale's CLDR names (ܓܙܝܖ̈ܐ).
- Script: Estrangela/Serto/Madnhaya are fonts of the same characters; the text is plain Unicode Syriac
  (U+0700 to U+074F) and needs a Syriac font (Noto Sans Syriac, Noto Sans Syriac Eastern, Estrangelo Edessa).
- Loanwords that the modern language uses for computing are written in Syriac letters (ܡܘܕܘܠ, ܣܟܪܝܦܬ, ܡܝܕܝܐ,
  ܦܘܪܡܐܛ, ܒܠܘܟ); product names, codes and abbreviations stay in Latin letters (Exponential, PHP, URL, ID, HTML).
- The Hebrew-script catalogues `arc-IQ` (Imperial Aramaic) and `huy-IL` (Jewish Neo-Aramaic) are made from this
  catalogue by letter-for-letter transliteration; a change here reaches them when they are written again (see
  their GUIDE.md).

## 2. Form of address

The interface addresses the user with the plural **ܐܚܬܘܢ** (you), **-ܘܟܘܢ** / **ܕܝܘܟܘܢ** (your), the polite form
that needs no gender: ܫܡܐ ܕܝܘܟܘܢ, ܚܘܫܒܢܐ ܕܝܘܟܘܢ.

- **Buttons, menu items and links**: the verbal noun (infinitive) of the verb, as in the glossary: ܢܛܪܐ (save),
  ܒܘܛܠܐ (cancel), ܫܝܦܐ (delete), ܬܘܩܢܐ (edit), ܫܕܪܐ ܠܦܘܪܣܐ (send for publishing), ܫܩܠܐ ܕܓܒ̈ܝܐ (remove
  selected). Not the imperative.
- **Titles, column headings and tabs**: nouns or noun phrases: ܛܘܝ̈ܒܐ, ܬܘܪ̈ܓܡܐ, ܕܘܟܬܐ ܪܫܝܬܐ.
- **Instructions**: ܒܒܥܘܬܐ (please) and the plural imperative: "ܒܒܥܘܬܐ ܓܒܘܢ ܚܕ ܡܢܬܐ.", "ܡܚܘܢ ܥܠ «ܛܒܐ».". A
  reference ("see ...") is "ܚܙܘܢ ...".
- **Confirmations and questions**: "ܒܥܝܬܘܢ ... ؟": "ܒܥܝܬܘܢ ܫܝܦܐ ܕܗܢܐ ܙܢܐ؟". Not "are you sure".
- **What the user can or may do**: "ܡܨܝܬܘܢ ...", "ܠܐ ܡܨܝܬܘܢ ...", or impersonal: "ܠܐ ܡܨܝܐ ܕܡܫܬܚܠܦ ...".
  "You do not have permission" is "ܠܝܬ ܠܘܟܘܢ ܐܦܣܢܘܬܐ ...".
- Not allowed: the singular ܐܢܬ / ܐܢܬܝ, slash forms for gender.

## 3. Script and typography

- Written right to left; the administration turns right to left by itself (`share/locale/syr-IQ.ini`,
  `TextDirection=rtl`). A translation needs no direction marks.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`), code, file names, INI settings and product
  names stay in Latin letters, unchanged, and **stand free**: a space before and after them, or a punctuation mark
  right after. A Syriac letter is never glued to them. The one-letter proclitics ܕ (of), ܒ (in), ܠ (to), ܘ (and)
  before Latin text, digits or a placeholder are written as a free letter followed by a space: "ܕ %name",
  "ܒ PHP", "ܘ %2"; or the sentence is turned so they are not needed (ܓܘ %1, ܕܝܠ %1).
- **Punctuation**: the Arabic comma ، (U+060C), semicolon ؛ (U+061B) and question mark ؟ (U+061F), which modern
  Syriac printing uses (the CLDR date format of the locale has ،); the Latin full stop, colon and exclamation mark.
  The old Syriac points (܀ ܁ ܂ ܃ ...) are not used. Where the punctuation belongs to code, a URL or a placeholder
  (`%1.`, `?id=`, `a, b` in a list of codes) it stays as in the source.
- **Quotation marks**: « ... » where the source quotes in running text; `&quot;` and a straight " that is code or an
  entity stay exactly as in the source.
- **Numbers**: the digits 0-9. The catalogue has no plural forms: "%count ܨܒ̈ܘܬܐ" or a label with a count.
- No tatweel (U+0640), no zero-width joiners, no Syriac abbreviation mark.

## 4. Glossary

Binding for the kernel and every extension.

| English | Syriac | Note |
|---|---|---|
| content | ܡܠܝܘܬܐ | |
| content object | ܨܒܘܬܐ ܕܡܠܝܘܬܐ | plural ܨܒ̈ܘܬܐ ܕܡܠܝܘܬܐ |
| object | ܨܒܘܬܐ | plural ܨܒ̈ܘܬܐ |
| node | ܩܛܪܐ | plural ܩܛܪ̈ܐ; main node: ܩܛܪܐ ܪܫܝܐ; parent node: ܩܛܪܐ ܐܒܗܝܐ |
| location | ܕܘܟܬܐ | plural ܕܘ̈ܟܝܬܐ; main location: ܕܘܟܬܐ ܪܫܝܬܐ |
| item (list entry) | ܡܢܬܐ | plural ܡ̈ܢܘܬܐ; sub items: ܡ̈ܢܘܬܐ ܬܚܬܝ̈ܬܐ |
| class | ܙܢܐ | the content class, plural ܙ̈ܢܝܐ; class group: ܟܢܫܐ ܕܙ̈ܢܝܐ; a PHP class: ܙܢܐ ܕ PHP |
| content class | ܙܢܐ ܕܡܠܝܘܬܐ | |
| type | ܓܢܣܐ | plural ܓܢ̈ܣܐ |
| attribute | ܕܝܠܝܘܬܐ | plural ܕܝ̈ܠܝܘܬܐ |
| datatype | ܓܢܣܐ ܕܝܕ̈ܥܬܐ | |
| data | ܝܕ̈ܥܬܐ | |
| database | ܒܝܬ ܝܕ̈ܥܬܐ | |
| field | ܚܩܠܐ | plural ܚܩܠ̈ܐ |
| value | ܛܝܡܐ | also price |
| version | ܡܦܩܬܐ | plural ܡܦ̈ܩܬܐ; version history: ܬܫܥܝܬܐ ܕܡܦ̈ܩܬܐ; also a release |
| draft | ܣܘܪܛܐ | plural ܣܘܪ̈ܛܐ; store draft: ܢܛܪܐ ܕܣܘܪܛܐ |
| publish | ܦܪܣܐ | published: ܦܪܝܣܐ; publishing: ܦܘܪܣܐ |
| send for publishing | ܫܕܪܐ ܠܦܘܪܣܐ | |
| discard | ܫܕܝܐ | discard draft: ܫܕܝܐ ܕܣܘܪܛܐ |
| archive | ܒܝܬ ܓܢܙܐ | an archived version or a file archive; archived: ܓܢܝܙܐ |
| pending | ܡܣܟܝܐ | |
| rejected | ܡܣܠܝܐ | |
| site, website | ܐܬܪܐ | plural ܐܬܖ̈ܘܬܐ |
| siteaccess | ܡܥܠܢܐ ܕܐܬܪܐ | plural ܡܥܠ̈ܢܐ ܕܐܬܪܐ |
| section | ܦܠܓܘܬܐ | the Exponential section, plural ܦܠ̈ܓܘܬܐ; an INI section: ܦܣܩܐ |
| role | ܕܘܪܐ | plural ܕܘܪ̈ܐ |
| policy | ܦܘܩܕܢܐ | plural ܦܘܩ̈ܕܢܐ; one rule of a role |
| limitation | ܬܚܘܡܐ | plural ܬܚܘ̈ܡܐ |
| permission | ܐܦܣܢܘܬܐ | plural ܐܦܣ̈ܢܘܬܐ |
| access | ܡܥܠܢܐ | access denied: ܡܥܠܢܐ ܟܠܝܐ |
| module | ܡܘܕܘܠ | plural ܡܘܕܘ̈ܠܐ |
| function | ܦܘܠܚܢܐ | plural ܦܘܠ̈ܚܢܐ |
| user | ܡܦܠܚܢܐ | plural ܡܦܠ̈ܚܢܐ |
| user group | ܟܢܫܐ ܕܡܦܠ̈ܚܢܐ | plural ܟܢ̈ܫܐ ܕܡܦܠ̈ܚܢܐ |
| group | ܟܢܫܐ | plural ܟܢ̈ܫܐ |
| anonymous | ܠܐ ܝܕܝܥܐ | |
| administrator | ܡܕܒܪܢܐ | plural ܡܕܒܪ̈ܢܐ |
| administration (interface) | ܕܘܒܪܐ | |
| username, Login (the name) | ܫܡܐ ܕܡܦܠܚܢܐ | "Login" as a field or column is the user name |
| password | ܡܠܬܐ ܕܥܒܪܐ | plural ܡ̈ܠܐ ܕܥܒܪܐ |
| log in / sign in | ܥܠܠܐ | the act too; the Login button of the error page: ܥܠܠܐ |
| log out / sign out | ܦܠܛܐ | |
| e-mail | ܒܪܝܕܐ ܐܠܩܛܪܘܢܝܐ | short: ܒܪܝܕܐ; e-mail address: ܡܘܢܥܐ ܕܒܪܝܕܐ |
| address | ܡܘܢܥܐ | |
| cache | ܣܝܡܬܐ | clear cache: ܣܪܩܐ ܕܣܝܡܬܐ |
| template | ܛܘܦܣܐ | plural ܛܘ̈ܦܣܐ |
| override | ܚܠܘܦܐ | override template: ܛܘܦܣܐ ܚܠܘܦܐ |
| fallback | ܣܡܟܐ | fallback language: ܠܫܢܐ ܕܣܡܟܐ |
| design | ܬܨܒܝܬܐ | |
| workflow | ܪܕܝܐ ܕܥܒܕܐ | plural ܪ̈ܕܝܐ ܕܥܒܕܐ |
| trigger | ܡܙܝܥܢܐ | plural ܡܙܝܥ̈ܢܐ |
| event | ܓܕܫܐ | plural ܓܕ̈ܫܐ |
| trash | ܡܙܒܠܬܐ | empty trash: ܣܪܩܐ ܕܡܙܒܠܬܐ; move to trash: ܫܢܝܐ ܠܡܙܒܠܬܐ |
| bookmark | ܢܝܫܢܐ | plural ܢܝ̈ܫܢܐ |
| settings, configuration | ܛܘܝ̈ܒܐ | one setting: ܛܘܝܒܐ |
| setup | ܛܘܝܒܐ | the Setup tab |
| layout | ܣܕܝܪܘܬܐ | Exponential Layouts; plural ܣܕܝ̈ܪܘܬܐ |
| page layout (pagelayout template) | ܛܘܦܣܐ ܕܦܐܬܐ | kept apart from ܣܕܝܪܘܬܐ |
| page | ܦܐܬܐ | plural ܦܐܬ̈ܐ |
| block | ܒܠܘܟ | plural ܒܠܘ̈ܟܐ |
| zone | ܦܢܝܬܐ | plural ܦܢ̈ܝܬܐ |
| extension | ܬܘܣܦܬܐ | plural ܬܘ̈ܣܦܬܐ |
| package | ܨܪܪܐ | plural ܨܪ̈ܪܐ |
| search | ܒܨܝܐ | search index: ܡܚܘܝܢܐ ܕܒܨܝܐ; indexing: ܥܒܕܐ ܕܡܚܘܝܢܐ |
| index | ܡܚܘܝܢܐ | |
| log | ܪܘܫܡܐ | a log file, plural ܪ̈ܘܫܡܐ; log file: ܦܢܩܝܬܐ ܕܪܘܫܡܐ; never "log in" |
| list | ܪܫܝܡܬܐ | plural ܪ̈ܫܝܡܬܐ |
| queue | ܣܕܪܐ | job queue: ܣܕܪܐ ܕܥܒ̈ܕܐ |
| job / task | ܥܒܕܐ | plural ܥܒ̈ܕܐ |
| cron | cron | a cron job: ܥܒܕܐ ܕ cron |
| script | ܣܟܪܝܦܬ | plural ܣܟܪܝ̈ܦܬܐ |
| worker | ܦܠܚܐ | plural ܦܠܚ̈ܐ |
| kernel, core | ܥܩܪܐ | |
| server | ܡܫܡܫܢܐ | plural ܡܫܡ̈ܫܢܐ |
| folder, directory | ܬܝܩܐ | plural ܬܝ̈ܩܐ |
| file | ܦܢܩܝܬܐ | plural ܦܢܩ̈ܝܬܐ |
| image | ܨܘܪܬܐ | plural ܨܘܪ̈ܬܐ; thumbnail: ܨܘܪܬܐ ܙܥܘܪܬܐ |
| media library | ܒܝܬ ܣܦܪ̈ܐ ܕܡܝܕܝܐ | |
| preview | ܚܙܝܐ ܩܕܡܝܐ | |
| upload | ܡܣܩܐ | |
| download | ܡܚܬܐ | |
| export / import | ܡܦܩܐ / ܡܥܠܐ | |
| remove | ܫܩܠܐ | take out of a list or place |
| delete | ܫܝܦܐ | destroy |
| edit | ܬܘܩܢܐ | |
| editor | ܡܬܩܢܢܐ | a person, and the editing software |
| save / store | ܢܛܪܐ | the verb "Store" of the forms |
| Store (shop tab) | ܚܢܘܬܐ | the shop, as "Shop" |
| shop | ܚܢܘܬܐ | |
| cancel | ܒܘܛܠܐ | |
| OK | ܛܒܐ | |
| apply | ܡܦܠܚܐ | |
| back | ܠܒܣܬܪ | |
| next / previous | ܕܒܬܪ / ܕܩܕܡ | first / last: ܩܕܡܝܐ / ܐܚܪܝܐ |
| enable / disable | ܡܦܥܠܐ / ܡܒܛܠܐ | |
| enabled / disabled | ܦܥܝܠܐ / ܒܛܝܠܐ | |
| active / inactive | ܦܥܝܠܐ / ܠܐ ܦܥܝܠܐ | |
| run | ܡܪܗܛܐ | a script, a job |
| start / stop | ܡܫܪܝܐ / ܡܟܠܝܐ | |
| session | ܡܘܬܒܐ | plural ܡܘܬ̈ܒܐ |
| hidden / visible | ܛܫܝܐ / ܡܬܚܙܝܢܐ | hide / reveal: ܡܛܫܝܐ / ܡܚܘܝܐ |
| order (sort) | ܛܟܣܐ | sort order: ܛܟܣܐ ܕܣܕܪܐ; sort (verb): ܣܕܪܐ |
| order (shop) | ܛܠܒܬܐ | plural ܛܠܒ̈ܬܐ |
| basket | ܣܠܐ | |
| product | ܬܘܠܕܐ | plural ܬܘܠ̈ܕܐ |
| customer | ܙܒܘܢܐ | plural ܙܒܘ̈ܢܐ |
| discount | ܒܘܨܪܐ | |
| tax, VAT | ܡܕܐܬܐ, ܡܕܐܬܐ ܕܬܘܣܦܬ ܛܝܡܐ | |
| price | ܛܝܡܐ | |
| currency | ܛܒܥܐ | |
| tag | ܐܬܐ | a keyword tag, plural ܐܬܘ̈ܬܐ; an HTML tag: ܐܬܐ ܕ HTML |
| character (letter) | ܐܬܘܬܐ | plural ܐܬܘ̈ܬܐ |
| translation | ܬܘܪܓܡܐ | plural ܬܘܪ̈ܓܡܐ |
| language | ܠܫܢܐ | plural ܠܫ̈ܢܐ; main language: ܠܫܢܐ ܪܫܝܐ; interface language: ܠܫܢܐ ܕܡܨܥܝܘܬܐ |
| interface | ܡܨܥܝܘܬܐ | |
| locale | ܐܬܪܝܘܬܐ | |
| state | ܐܣܟܡܐ | the object state, plural ܐܣܟ̈ܡܐ |
| status | ܡܩܡܐ | |
| view (button) | ܚܙܝܐ | a module view (noun): ܚܙܘܐ |
| format | ܦܘܪܡܐܛ | |
| filter | ܡܨܠܠܢܐ | the act: ܨܘܠܠܐ |
| select | ܓܒܝܐ | select all: ܓܒܝܐ ܕܟܠ; selected: ܓܒܝܐ, plural ܓܒ̈ܝܐ |
| option, choice | ܓܒܝܬܐ | plural ܓܒ̈ܝܬܐ |
| browse | ܚܝܪܐ | |
| copy | ܢܣܚܐ | a copy: ܢܘܣܚܐ |
| move | ܫܢܝܐ | |
| add | ܡܘܣܦܐ | |
| create | ܒܪܝܐ | |
| new | ܚܕܬܐ | plural ܚܕ̈ܬܐ |
| restore | ܡܕܥܪܐ | |
| install / uninstall | ܡܩܝܡܐ / ܫܪܝܐ | |
| tree / subtree | ܐܝܠܢܐ / ܐܝܠܢܐ ܬܚܬܝܐ | |
| related objects | ܨܒ̈ܘܬܐ ܕܐܚܝܢܘܬܐ | relation: ܐܚܝܢܘܬܐ |
| URL alias | ܫܡܐ ܚܠܘܦܐ ܕ URL | |
| redirect | ܡܦܢܝܐ | |
| dashboard | ܦܬܘܪܐ | |
| structure | ܒܢܝܢܐ | content structure: ܒܢܝܢܐ ܕܡܠܝܘܬܐ |
| identifier / ID | ܡܫܘܕܥܢܐ / ID | |
| name | ܫܡܐ | plural ܫܡܗ̈ܐ |
| title | ܟܘܢܝܐ | |
| description | ܬܚܘܝܬܐ | |
| text | ܟܬܝܒܬܐ | |
| document | ܟܬܒܐ | |
| message | ܫܠܝܚܘܬܐ | plural ܫܠܝ̈ܚܘܬܐ |
| comment | ܦܘܫܩܐ | plural ܦܘܫ̈ܩܐ |
| number | ܡܢܝܢܐ | |
| size | ܪܒܘܬܐ | width / height: ܦܬܝܐ / ܪܘܡܐ |
| priority | ܩܕܝܡܘܬܐ | |
| owner | ܡܪܐ | |
| creator / modifier | ܥܒܘܕܐ / ܡܫܚܠܦܢܐ | |
| modified / created | ܡܫܘܚܠܦܐ / ܒܪܝܐ | |
| date / time | ܬܐܪܝܟܐ / ܙܒܢܐ | |
| error / warning | ܦܘܕܐ / ܙܘܗܪܐ | plural ܦܘ̈ܕܐ / ܙܘܗܪ̈ܐ |
| help | ܥܘܕܪܢܐ | |
| notification | ܡܘܕܥܢܘܬܐ | plural ܡܘܕܥ̈ܢܘܬܐ |
| wizard | ܡܥܕܪܢܐ | setup wizard: ܡܥܕܪܢܐ ܕܛܘܝܒܐ |
| feed (RSS) | ܡܪܕܝܬܐ | |
| tab | ܦܬܩܐ | plural ܦܬ̈ܩܐ |
| audit, audit trail | ܒܘܩܝܐ, ܥܩܒܐ ܕܒܘܩܝܐ | |
| repository | ܓܙܐ | |
| checksum | ܚܘܫܒܢܐ ܕܒܘܚܪܢܐ | |
| account | ܚܘܫܒܢܐ | plural ܚܘܫ̈ܒܢܐ |
| default | ܥܝܕܝܐ | default value: ܛܝܡܐ ܥܝܕܝܐ |
| toolbar | ܣܪܓܐ ܕܡܐܢ̈ܐ | tool: ܡܐܢܐ |
| container | ܡܚܣܢܐ | |
| AI | ܗܘܢܐ ܡܨܛܢܥܐ | |
| pattern (name pattern) | ܕܡܘܬܐ | not ܛܘܦܣܐ, which is the template |
| wildcard | ܐܬܘܬܐ ܟܠܢܝܬܐ | |
| remote ID | ID ܪܚܝܩܐ | remote: ܪܚܝܩܐ |
| placeholder | ܢܛܘܪ ܕܘܟܬܐ | |
| collection | ܟܢܘܫܝܐ | information collector: ܟܢܘܫܐ ܕܝܕ̈ܥܬܐ |
| approve, approval | ܡܫܪܪܐ, ܫܘܪܪܐ | |
| software | ܣܘܦܛܘܝܪ | |
| request (HTTP) | ܒܥܘܬܐ | plural ܒܥܘ̈ܬܐ |
| query | ܫܘܐܠܐ | |
| handler / provider / listener | ܡܥܒܕܢܐ / ܝܗܘܒܐ / ܫܡܘܥܐ | |
| engine | ܡܟܢܐ | |
| upgrade | ܡܪܡܐ | |
| backup | ܢܘܣܚܐ ܕܣܡܟܐ | |
| maintenance | ܢܛܘܪܘܬܐ | |
| header (HTTP) | ܪܫܐ | |
| key | ܩܠܝܕܐ | passkey: ܩܠܝܕܐ ܕܥܠܠܐ |
| two-step sign-in | ܥܠܠܐ ܒܬܪܬܝܢ ܕܪ̈ܓܐ | |
| device / browser | ܡܐܢܐ / ܚܝܪܢܐ | |
| link | ܐܣܘܪܐ | plural ܐܣܘܪ̈ܐ |
| registration, register | ܡܟܬܒܢܘܬܐ, ܟܬܒܐ ܕܫܡܐ | |
| payment gateway | ܬܪܥܐ ܕܦܘܪܥܢܐ | payment: ܦܘܪܥܢܐ |
| read / write | ܩܪܝܐ / ܟܬܒܐ | |
| click | ܡܚܝܐ | instruction: ܡܚܘܢ ܥܠ ... |
| open / close | ܦܬܚܐ / ܣܟܪܐ | |
| send | ܫܕܪܐ | |
| please | ܒܒܥܘܬܐ | |
| none | ܠܐ ܚܕ | |
| yes / no | ܐܝܢ / ܠܐ | |
| all | ܟܠ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ܚܢܘܬܐ, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is ܢܛܪܐ.
- "Login" as a field or column (user, session, audit and package lists) is ܫܡܐ ܕܡܦܠܚܢܐ; the Login button of the
  error page is ܥܠܠܐ.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the table in TRANSLATING.md, "Words that need their context").
- The setup text of design/standard/setup/init whose source holds a Qt Linguist `<byte value="x19"/>` element
  ("We're looking forward ...") keeps that source whole in the catalogue and is translated in full.
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
