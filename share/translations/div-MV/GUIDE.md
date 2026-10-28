# Dhivehi (div-MV): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Dhivehi (ދިވެހި) in its modern form, as used in the Maldives, in the polite, neutral register of
Maldivian government texts, news media and the Dhivehi localisations of operating systems and office software. No other
locale falls back to `div-MV`.

- Word order is subject - object - verb; the verb stands last and postpositions follow the noun (ގައި, އިން, ގެ, އަށް,
  އާއި).
- Technical words: the established Dhivehi computing terms are mostly English loans written in Thaana (ފައިލް file,
  ފޯލްޑަރު folder, ޑޭޓާ data, ޕާސްވޯޑް password, ވެބްސައިޓް website, އީމެއިލް e-mail). Where Dhivehi has its own
  settled word, use it (ބަސް language, ހުއްދަ permission, ތަރުޖަމާ translation, އެހީ help, ފިހާރަ shop).
- Loanwords from Arabic (`ara-SA`) and Urdu (`urd-PK`) are used where Dhivehi itself uses them (ޝާއިޢު, ޙާލަތު, ލާޒިމު,
  އިޚްތިޔާރީ, ތަފްސީލު). Arabic and Urdu serve only to check the meaning of a term; never copy an Arabic or Urdu
  word that Dhivehi does not use.
- An English loan is written in Thaana only where it is the established Dhivehi word. Otherwise use the Dhivehi word or a
  short descriptive phrase. Never leave an English word in Latin letters unless it is a product name, a code or a
  format name.
- Short, plain sentences; no honorific forms beyond the polite request.

## 2. Form of address

- The user is addressed politely, without a pronoun where Dhivehi can leave it out. Where one is needed, use ތިޔައީ /
  ތިބާ in the polite form; never the familiar form.
- **Buttons, menu items and links**: the polite imperative or the bare verb stem, as Dhivehi interfaces do:
  ރައްކާކުރޭ (save), ކެންސަލްކުރޭ (cancel), ފޮހެލާ (delete), ނަގާ (remove), އެޑިޓްކުރޭ (edit), އިތުރުކުރޭ (add),
  އުފައްދާ (create), ހޯދާ (search), ހިޔާރުކުރޭ (select), ފޮނުވާ (send), ބަލާ (view), ޝާއިޢުކުރޭ (publish),
  އަޕްލޯޑްކުރޭ (upload), ޑައުންލޯޑްކުރޭ (download).
- **Titles, column headings and tabs**: nouns or noun phrases: ސެޓިންގްސް، ތަރުޖަމާ، މައި ތަން، ނަން.
- **Instructions**: the verb in the polite request form where the source has please or gives an instruction:
  "އެއް އޮބްޖެކްޓް ހިޔާރުކޮށް \"ރަނގަޅު\" ފިތާލައްވާ."
- **Confirmations and questions**: the question particle ހެއްޔެވެ or the question mark alone, never both:
  "މި ސެކްޝަން ފޮހެލަން ބޭނުންތޯ؟".
- **What the user can or may do**: "... ކުރެވޭނެ", "... ނުކުރެވޭނެ"; "You do not have permission" is "ތިޔަފަރާތަށް
  ހުއްދަ ނެތް".
- **Messages about a result**: the completed form: "ނޯޓް ރައްކާކުރެވިއްޖެ.", "ފައިލް އަޕްލޯޑްކުރެވިއްޖެ."

## 3. Script and typography

- **Thaana script** (U+0780-U+07BF), written **right to left**. Every vowel sign (fili) is written; a text without them
  is wrong. A message is never mixed with Arabic or Urdu letters.
- **Direction**: the locale sets `TextDirection=rtl`. Translators write the text in logical order and never insert
  direction marks (U+200E, U+200F, U+202A-U+202E) by hand unless the source has them; the page direction and the
  browser's bidirectional algorithm do the rest. Never reverse a string, never write a mirrored copy of the source
  order.
- **Mixed text**: Latin words, numbers, product names, codes and placeholders are embedded in the Thaana sentence and
  stand free with a space before and after them (or a punctuation mark right after). Nothing is glued to them, so
  that the bidirectional order stays unambiguous. A Latin name at the very end of a sentence keeps the full stop after
  it, separated by a space only where the sentence would otherwise read wrongly.
- **Punctuation**: the Arabic-script marks where Dhivehi uses them: the comma **،** (U+060C), the question mark **؟**
  (U+061F) and the semicolon **؛** (U+061B). The full stop is written as Dhivehi writes it, the dot **.** (U+002E) at
  the end of a sentence; the colon after a label is **:** and stays as in the source. Ellipses and exclamation marks
  stay as in the source. A space follows these marks, never precedes them. A message that has no final stop in the
  source gets none.
- **Brackets and quotes**: parentheses and brackets stay as in the source (they mirror automatically in a right to
  left text); quotation marks are the straight double quotes "..." or the ones of the source; `&quot;` and other
  entities are kept exactly.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free, as above. HTML tags
  stay exactly and may move with the sentence; their order inside a right to left sentence follows the logical order of
  the Dhivehi text.
- **Numbers**: digits 0-9 (Thaana has no digits of its own in everyday use; Arabic-Indic digits are not used in the
  interface). Numbers keep their reading order inside the right to left text.

## 4. Glossary

Binding for the kernel and every extension.

| English | Dhivehi | Note |
|---|---|---|
| content | ކޮންޓެންޓް | content object: ކޮންޓެންޓް އޮބްޖެކްޓް |
| object | އޮބްޖެކްޓް | |
| node | ނޯޑް | main node: މައި ނޯޑް; parent node: ބައްޕަ ނޯޑް |
| location | ތަން | main location: މައި ތަން |
| item (list entry) | އައިޓަމް | |
| tree | ގަސް | the tree menu: ގަހުގެ މެނޫ; content structure tree: ކޮންޓެންޓް ގަސް |
| class | ކްލާސް | the content class: ކޮންޓެންޓް ކްލާސް; class group: ކްލާސް ގްރޫޕް |
| attribute | ސިފަ | |
| datatype | ޑޭޓާ ޓައިޕް | |
| version | ވާޖަން | version history: ވާޖަންގެ ތާރީޚް |
| draft | ޑްރާފްޓް | store draft: ޑްރާފްޓް ރައްކާކުރޭ |
| publish | ޝާއިޢުކުރޭ | published: ޝާއިޢުކުރެވިފައި |
| send for publishing | ޝާއިޢުކުރުމަށް ފޮނުވާ | |
| discard | ދޫކޮށްލާ | discard draft: ޑްރާފްޓް ދޫކޮށްލާ |
| archived / pending / rejected | އާކައިވްކުރެވިފައި / މެދުގައި / ރަދުކުރެވިފައި | |
| siteaccess | ސައިޓްއެކްސެސް | siteaccess as a setting name stays in code |
| site, website | ސައިޓް / ވެބްސައިޓް | |
| section | ސެކްޝަން | the Exponential section; an INI section: INI ސެކްޝަން |
| role | ރޯލް | |
| policy | ޕޮލިސީ | |
| limitation | ހުރަސް | |
| permission | ހުއްދަ | access denied: އެކްސެސް ނުލިބޭ |
| module | މޮޑިއުލް | |
| function | ފަންކްޝަން | a PHP function: PHP function |
| user / user group | ޔޫޒަރު / ޔޫޒަރުންގެ ގްރޫޕް | |
| group | ގްރޫޕް | |
| anonymous | ނަންނޭނގޭ | anonymous user: ނަންނޭނގޭ ޔޫޒަރު |
| administrator | އެޑްމިނިސްޓްރޭޓަރު | administration (interface): އެޑްމިނިސްޓްރޭޝަން |
| username | ޔޫޒަރުނަން | |
| password | ޕާސްވޯޑް | |
| log in / log out | ލޮގިންވޭ / ލޮގްއައުޓްވޭ | the act, as a button; login name: ލޮގިންނަން |
| e-mail | އީމެއިލް | e-mail address: އީމެއިލް އެޑްރެސް |
| cache | ކޭޝް | clear cache: ކޭޝް ސާފުކުރޭ |
| template | ޓެމްޕްލޭޓް | override template: އޯވަރރައިޑް ޓެމްޕްލޭޓް |
| override | އޯވަރރައިޑް | |
| fallback | ފޯލްބެކް | fallback language: ފޯލްބެކް ބަސް |
| design | ޑިޒައިން | |
| workflow | ވޯކްފްލޯ | |
| trigger | ޓްރިގަރ | |
| event | އިވެންޓް | |
| trash | ކުނިކޮށް | empty trash: ކުނިކޮށް ހުސްކުރޭ; move to trash: ކުނިކޮށަށް ބަދަލުކުރޭ |
| bookmark | ބުކްމާކް | |
| settings | ސެޓިންގްސް | configuration: ކޮންފިގްރޭޝަން |
| setup | ސެޓަޕް | the Setup tab |
| layout | ލޭއައުޓް | Exponential Layouts stays |
| block / zone | ބްލޮކް / ޒޯން | |
| extension | އެކްސްޓެންޝަން | |
| package | ޕެކޭޖް | |
| search | ހޯދާ | search index: ހޯދުމުގެ އިންޑެކްސް |
| log | ލޮގް | a log file: ލޮގް ފައިލް; never "log in" |
| queue | ކިއު | job queue: ޖޮބް ކިއު |
| job / task | ޖޮބް / ވާޖިބު | |
| cron | cron | |
| script | script | |
| kernel, core | ކާނަލް | |
| server | ސާރވަރު | |
| database | ޑޭޓާބޭސް | |
| folder / directory | ފޯލްޑަރު | |
| file | ފައިލް | |
| image | ފޮޓޯ | thumbnail: ކުޑަ ފޮޓޯ |
| preview | ޕްރިވިއު | |
| upload / download | އަޕްލޯޑްކުރޭ / ޑައުންލޯޑްކުރޭ | |
| export / import | އެކްސްޕޯޓްކުރޭ / އިމްޕޯޓްކުރޭ | |
| remove | ނަގާ | take out of a list or place |
| delete | ފޮހެލާ | destroy; deleted: ފޮހެލެވިފައި |
| edit | އެޑިޓްކުރޭ | |
| editor | އެޑިޓަރު | person / software |
| save / store | ރައްކާކުރޭ | the verb "Store" of the forms |
| Store (shop tab) | ފިހާރަ | the shop, as "Shop" |
| cancel | ކެންސަލްކުރޭ | |
| OK | ރަނގަޅު | |
| apply | ހިންގާ | |
| back | ފަހަތަށް | |
| next / previous | ދެން / ކުރީގެ | |
| enable / disable | ޖައްސާ / ނިއްވާ | enabled / disabled: ޖެހިފައި / ނިއްވާފައި |
| active / inactive | އެކްޓިވް / އިނެކްޓިވް | |
| run | ހިންގާ | |
| start / stop | ފަށާ / ހުއްޓުވާ | |
| session | ސެޝަން | |
| hidden / visible | ފޮރުވިފައި / ފެންނަ | hide / reveal: ފޮރުވާ / ދައްކާ |
| archive | އާކައިވް | |
| order (sort) | ތަރުތީބު | sort order: ތަރުތީބު |
| order (shop) | އޯޑަރު | |
| shop | ފިހާރަ | |
| basket | ބާސްކެޓް | |
| product | މުދާ | |
| customer | ކަސްޓަމަރު | |
| discount | ޑިސްކައުންޓް | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | އަގު / ކަރަންސީ | |
| tag | ޓެގް | |
| translation | ތަރުޖަމާ | |
| language | ބަސް | main language: މައި ބަސް |
| locale | ލޮކޭލް | |
| state | ހާލަތު | the object state |
| status | ޙާލަތު | |
| view (button) | ބަލާ | a module view (noun): ވިއު |
| format | ފޯމެޓް | |
| sort | ތަރުތީބުކުރޭ | |
| filter | ފިލްޓަރު | |
| select | ހިޔާރުކުރޭ | select all: ހުރިހާ އެއްޗެއް ހިޔާރުކުރޭ; selected: ހިޔާރުކުރެވިފައި |
| copy / move | ކޮޕީކުރޭ / ބަދަލުކުރޭ | |
| add / create / new | އިތުރުކުރޭ / އުފައްދާ / އާ | |
| restore | ރިސްޓޯރުކުރޭ | |
| install / uninstall | އިންސްޓޯލްކުރޭ / އަންއިންސްޓޯލްކުރޭ | |
| related objects | ގުޅިފައިވާ އޮބްޖެކްޓްތައް | relation: ގުޅުން |
| dashboard | ޑޭޝްބޯޑް | |
| identifier / ID | އައިޑެންޓިފަޔަރު / ID | |
| priority | އިސްކަން | |
| owner | ވެރިފަރާތް | |
| modified / created | ބަދަލުކުރެވިފައި / އުފެދިފައި | |
| date / time | ތާރީޚް / ގަޑި | |
| error / warning | މައްސަލަ / ޚަބަރުދާރީ | |
| help | އެހީ | |
| notification | ނޯޓިފިކޭޝަން | |
| account | އެކައުންޓް | |
| default | ޑިފޯލްޓް | |
| link | ލިންކް | |
| none / all | ނެތް / ހުރިހާ | |
| yes / no | އާނ / ނޫން | |
| name / title / description | ނަން / ސުރުޚީ / ތަފްސީލު | |
| value / type / list | ވެލިއު / ބާވަތް / ލިސްޓް | |
| page / path | ޞަފްޙާ / ޕާތު | |
| required / optional | ލާޒިމު / އިޚްތިޔާރީ | |
| options | އޮޕްޝަންސް | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ފިހާރަ, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is ރައްކާކުރޭ.
- "Login" in the user, session and audit lists is the user's login name, a noun: ލޮގިންނަން. The Login button of the
  error page (`design/admin/error/kernel`) is the act of logging in: ލޮގިންވޭ.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
