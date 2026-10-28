# Manipuri / Meitei (mni-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Manipuri (Meiteilon, মৈতৈলোন্) as used in Manipur, India, in the **Bengali (Eastern Nagari) script**,
as the locale `mni-IN` sets it (LanguageName মৈতৈলোন্). The Meetei Mayek script is not used in this catalogue. The
register is the polite, neutral one of Manipur government notices, schoolbooks and news media. No other locale falls
back to `mni-IN`.

- Word order is subject - object - verb. Meitei is agglutinative: case and other relations are suffixes on the noun,
  written together with it: -না (agent, instrument), -দা (in, at, to), -গী (of, genitive), -দগী (from), -সু (also),
  -বু (but, contrastive), -গা (with), -দ (topic or contrast). Plurals are marked with -শিং (খুৎশিং, ফাইলশিং) or are
  left unmarked where the number is clear.
- Verbs end in suffixes, not in Bengali conjugations. The verbal noun ends in -বা/-পা (থম্বা, তৌবা, ফাংবা); the completed
  form in -ললি / -লে / -রে (থম্লি, তৌরে); the negative in -দ- (-দে), e.g. থম্দে; the polite request in -বিয়ু / -বীয়ু.
  Do not use Bengali or Assamese verb forms (-ন, -ক, -ছে, -ইল) in sentences.
- Technical words: where Manipuri has a settled word, use it; where Manipuri computing and media text normally takes an
  English loan, the loan is written in Bengali script (ফাইল, ফোল্ডর, সার্ভর, ডেটাবেজ, পাসওয়র্দ). Bengali (`ben-BD`)
  and Assamese (`asm-IN`) serve only to check the meaning of a borrowed term and its spelling in this script; never
  copy their grammar. Never leave the English word in Latin letters unless it is a product name or code.
- Short, plain sentences; no honorific forms beyond the polite request.

## 2. Form of address

- The user is addressed politely without a pronoun, which the Meitei verb does not need. Where a possessive is
  needed, use নঙ্গী ("your"), sparingly: "নঙ্গী একাউন্ট".
- **Buttons, menu items and links**: the verbal noun in -বা/-পা, the dictionary form: থম্বা (save), কেন্সেল তৌবা
  (cancel), ফাহনবা (delete), থোক্কনবা (remove), এডিট তৌবা (edit), যোগ তৌবা (add), সেম্বা (create), সার্চ তৌবা (search),
  সিলেক্ট তৌবা (select), পাঠাবা (send), ফংবা (view), প্রকাশ তৌবা (publish), আপলোদ তৌবা (upload), ডাউনলোদ তৌবা (download).
- **Titles, column headings and tabs**: nouns or noun phrases: সেটিংশিং, অনুবাদ, মরীক, মমিং.
- **Instructions**: the stem with the polite imperative -বিয়ু / -বীয়ু where the source says please or gives an
  instruction: "অমুক্তা অবজেক্ট অমা সিলেক্ট তৌবিয়ু, অদুগা \"ঠিক\" ফোংবিয়ু."
- **Confirmations and questions**: "... তৌগদ্রে ? " is not used; a statement that ends in the question particle -গা and the
  question mark of the source: "সেক্সন অসি ফাহনবনি হায়বসু ঠিকনি গা ?" ("Delete this section, are you sure?"); the
  wording follows the source.
- **What the user can or may do**: "... তৌজরি" / "... ফাংই" (can), "... ফাংদে" (cannot); "You do not have permission" is
  "নঙ্গী অনুমতি ঙমদে." (or "অনুমতি ঙমদে.").
- **Messages about a result**: the completed form: "ড্রাফ্ট থম্লে.", "ফাইল আপলোদ তৌরে.", "ফাহনখ্রে." (deleted).

## 3. Script and typography

- **Bengali (Eastern Nagari) script** (U+0980-U+09FF) with the Meitei spellings in use in Manipur: য়, ৱ, ৎ, ঙ, ঞ and
  the vowel signs as in the Bengali alphabet. Do not write a Meetei Mayek letter (U+ABC0-U+ABFF) anywhere.
- **Sentence end**: a full stop "." (U+002E), as Manipuri written in the Bengali script commonly does and as the source
  has it. The danda "।" is not used in this catalogue, so that a message ends the way the English source ends and the
  quality test finds the same final punctuation (TRANSLATING.md, rule 6). A question ends in the question
  particle -গা and "?". Colons after labels, ellipses and exclamation marks are kept as in the source. A space follows
  a full stop inside a text, never before it.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A Meitei suffix is never glued to a placeholder; write the sentence so
  that the placeholder is followed by a space and the suffix word ("%name দা" is not allowed; rephrase: "মমিং: %name").
  The same for Latin words, code and product names inside a Manipuri sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (Bengali digits ০-৯ and Meetei Mayek digits are not used in the interface).
- Zero-width joiners and non-joiners are not used to force a conjunct; write the conjuncts in the usual way.

## 4. Glossary

Binding for the kernel and every extension. Where a term is an English loan written in Bengali script, that is the
settled Manipuri usage.

| English | Manipuri | Note |
|---|---|---|
| content | কন্টেন্ট | content object: কন্টেন্ট অবজেক্ট |
| object | অবজেক্ট | |
| node | নোদ | main node: মরুওইবা নোদ; parent node: মা-নোদ |
| location | মখোয় | main location: মরুওইবা মখোয় |
| item (list entry) | আইটেম | |
| tree | তৃ | tree menu: তৃ মেনু; content structure tree: কন্টেন্ট তৃ |
| class | ক্লাস | the content class: কন্টেন্ট ক্লাস; class group: ক্লাস মরুপ |
| attribute | মখোয়-খুদিংমক | attribute is also written এট্রিবিউট |
| datatype | ডেটা মরীক | |
| version | ভর্সন | version history: ভর্সনগী মীৎ-ইতিহাস |
| draft | ড্রাফ্ট | store draft: ড্রাফ্ট থম্বা |
| publish | প্রকাশ তৌবা | published: প্রকাশ তৌরবা |
| send for publishing | প্রকাশগী ৱাংবা পাঠাবা | |
| discard | ফাহনবা | discard draft: ড্রাফ্ট ফাহনবা |
| archived / pending / rejected | আর্কাইভ তৌরবা / ঙাক্লিবা / ফংদবা | |
| siteaccess | সাইট একসেস | siteaccess as a setting name stays in code |
| site, website | সাইট, ৱেবসাইট | |
| section | সেক্সন | the Exponential section; an INI section: INI সেক্সন |
| role | ভূমিকা | |
| policy | পলিসি | |
| limitation | চাঙ্গোয়বা | |
| permission | অনুমতি | access denied: একসেস ফংদে |
| module | মডিউল | |
| function | ফাংকসন | a PHP function: PHP function |
| user / user group | ইউজর / ইউজর মরুপ | |
| group | মরুপ | |
| anonymous | মমিং-মখা-ফংবা | anonymous user: মমিং ফংদবা ইউজর |
| administrator | এদমিনিষ্ট্রেটর | administration (interface): এদমিনিষ্ট্রেসন |
| username | ইউজর মমিং | |
| password | পাসওয়র্দ | |
| log in / log out | লগইন তৌবা / লগআউট তৌবা | |
| e-mail | ই-মেইল | e-mail address: ই-মেইল ঠিকানা |
| cache | কেশ | clear cache: কেশ ফাহনবা |
| template | টেম্প্লেট | override template: ওভারৈদ টেম্প্লেট |
| override | ওভারৈদ | |
| fallback | ফলব্যাক | fallback language: ফলব্যাক লোন্ |
| design | ডিজাইন | |
| workflow | ৱর্কফ্লো | |
| trigger | ট্রিগর | |
| event | ঘটনা | |
| trash | ট্রেশ | empty trash: ট্রেশ ফাহনবা; move to trash: ট্রেশদা সন্নবা |
| bookmark | বুকমার্ক | |
| settings | সেটিংশিং | configuration: কনফিগরেসন |
| setup | সেটআপ | the Setup tab |
| layout | লেয়ৈট | Exponential Layouts stays |
| block / zone | ব্লক / জোন | |
| extension | এক্সটেন্সন | |
| package | প্যাকেজ | |
| search | সার্চ | to search: সার্চ তৌবা; search index: সার্চ ইন্দেক্স |
| log | লগ | a log file: লগ ফাইল; never "log in" |
| queue | কিউ | job queue: ৱারোল কিউ |
| job / task | ৱারোল | |
| cron | cron | |
| script | স্ক্রিপ্ট | |
| kernel, core | কর্ | |
| server | সার্ভর | |
| database | ডেটাবেজ | |
| folder / directory | ফোল্দর / ডাইরেক্টরি | |
| file | ফাইল | |
| image | ইমেজ | thumbnail: খুনৈ ইমেজ |
| preview | প্রিভিউ | |
| upload / download | আপলোদ তৌবা / ডাউনলোদ তৌবা | |
| export / import | এক্সপোর্ট তৌবা / ইম্পোর্ট তৌবা | |
| remove | থোক্কনবা | take out of a list or place |
| delete | ফাহনবা | destroy; deleted: ফাহনখ্রে |
| edit | এডিট তৌবা | |
| editor | এডিটর | person and software |
| save / store | থম্বা | the verb "Store" of the forms |
| Store (shop tab) | শপ | the shop, as "Shop" |
| cancel | কেন্সেল তৌবা | |
| OK | ঠিক | |
| apply | লাগ্নবা | |
| back | লেপ্পা | |
| next / previous | মখা / মখল | |
| enable / disable | চালু তৌবা / বন্দ তৌবা | enabled / disabled: চালু / বন্দ |
| active / inactive | চালু / চালু ঙমদবা | |
| run | চলাবা | |
| start / stop | শুরু তৌবা / থোকপা | |
| session | সেসন | |
| hidden / visible | লুমবা / ফংবা | hide / reveal: লুম্বা / থোক্কনবা |
| archive | আর্কাইভ | |
| order (sort) | ক্রম | sort order: সর্ট ক্রম |
| order (shop) | অর্দর | |
| shop | শপ | |
| basket | বাস্কেট | |
| product | প্রোডাক্ট | |
| customer | কাস্টমর | |
| discount | ডিসকাউন্ট | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | দাম / কারেন্সি | |
| tag | ট্যাগ | |
| translation | অনুবাদ | |
| language | লোন্ | main language: মরুওইবা লোন্ |
| locale | লোকেল | |
| state | স্টেট | the object state |
| status | স্থিতি | |
| view (button) | ফংবা | a module view (noun): ভিউ |
| format | ফর্মেট | |
| sort | সর্ট তৌবা | |
| filter | ফিল্টর | |
| select | সিলেক্ট তৌবা | select all: অসিবু সিলেক্ট তৌবা (all: খুদিংমক); selected: সিলেক্ট তৌরবা |
| copy / move | কপি তৌবা / সন্নবা | |
| add / create / new | যোগ তৌবা / সেম্বা / অনৌবা | |
| restore | রিস্টোর তৌবা | |
| install / uninstall | ইনস্টল তৌবা / আনইনস্টল তৌবা | |
| related objects | মনুং-মনুংদা ইনদ্রবা অবজেক্ট | relation: সম্বন্ধ |
| dashboard | ড্যাশবোর্দ | |
| identifier / ID | পরিচয় / ID | |
| priority | অগ্রাধিকার | |
| owner | মওংবা | |
| modified / created | সুধরবা / সেম্খিবা | |
| date / time | তারিখ / মসময় | |
| error / warning | ভুল / সতর্ক | |
| help | ফংজেল | |
| notification | ৱাংবা | |
| account | একাউন্ট | |
| default | ডিফল্ট | |
| link | লিংক | |
| none / all | অমুক ফংদে / খুদিংমক | |
| yes / no | অমুক / মতোক | |
| name / title / description | মমিং / মৈখুম / বর্ণনা | |
| value / type / list | মান / মরীক / তালিকা | |
| page / path | পেজ / পথ | |
| required / optional | ঙমবা / ইচ্ছাধীন | |
| options | বিকল্পশিং | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is শপ, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  থম্বা.
- "Login" in the user, session and audit lists is the user's login name (লগইন মমিং); the Login button of the error page
  is the act (লগইন তৌবা).
- The sentence-final mark is the full stop "." everywhere, never the danda "।" (see section 3).
- Terms in the glossary that are English loans in Bengali script are kept as they are even where a native word exists,
  so that a term reads the same in the kernel and in every extension; a native speaker's review may replace a loan by a
  settled native word, and then the guide is changed first.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
