# Assamese (asm-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Assamese (অসমীয়া) as used in Assam, in the **Assamese (Bengali-Assamese) script**, in the
neutral, polite register of the Government of Assam portals, Assamese newspapers (Asomiya Pratidin), the Assamese
localisations of Windows, Android and Firefox (as) and Assamese Wikipedia. No other locale falls back to `asm-IN`.

- Word order is subject - object - verb; the verb comes last.
- Write Assamese, not Bengali. Bengali (ben-BD) shares the script and much vocabulary and may help, but the letters
  and forms differ: Assamese **ৰ** (ra, U+09F0) and **ৱ** (wa, U+09F1), never Bengali র and ব for them; Assamese
  verb endings (-ক, -আক, কৰক, কৰিব পাৰে) and words (ব্যৱহাৰকাৰী, সঞ্চয় কৰক, বাতিল কৰক, বিচাৰক).
- Technical words: an Assamese word where one is established (ফাইল, ব্যৱহাৰকাৰী, পাছৱৰ্ড, ছেটিংছ, সন্ধান, ডাটাবেছ);
  an English loan written in Assamese script where Assamese IT writing uses it (ছাৰ্ভাৰ, স্ক্ৰিপ্ট, মডিউল, কেচ).
  Never leave the English word in Latin letters unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **আপুনি** (polite "you"), with the polite verb forms (আপুনি ... কৰিব পাৰে, আপুনি নিশ্চিতনে).
- **Buttons, menu items and links**: the polite imperative in -ক, as Assamese interfaces do: সঞ্চয় কৰক (save),
  বাতিল কৰক (cancel), মচক (delete), আঁতৰাওক (remove), সম্পাদনা কৰক (edit), যোগ কৰক (add), সৃষ্টি কৰক (create),
  সন্ধান কৰক (search), বাছনি কৰক (select), পঠিয়াওক (send), চাওক (view), প্ৰকাশ কৰক (publish), আপলোড কৰক
  (upload), ডাউনলোড কৰক (download).
- **Titles, column headings and tabs**: nouns or noun phrases: ছেটিংছ, অনুবাদ, মুখ্য অৱস্থান, নাম.
- **Instructions**: the polite -ক form, "অনুগ্ৰহ কৰি" for please: "বস্তুটো বাছনি কৰি \"OK\" ক্লিক কৰক।".
- **Confirmations and questions**: "আপুনি ... বিচাৰে বুলি নিশ্চিতনে?": "আপুনি এই শাখাটো মচিব বিচাৰে বুলি নিশ্চিতনে?".
- **What the user can or may do**: "আপুনি ... কৰিব পাৰে", "আপুনি ... কৰিব নোৱাৰে"; "You do not have permission" is
  "আপোনাৰ ... অনুমতি নাই".
- **Messages about a result**: "... হ'ল": "খচৰা সঞ্চয় কৰা হ'ল।", "ফাইলটো সফলতাৰে আপলোড কৰা হ'ল।".

## 3. Script and typography

- **Assamese script** (U+0980-U+09FF) with ৰ and ৱ.
- No capital letters; Latin product names keep theirs.
- **Full stop**: the sentence ends with the dari । where the English ends with a full stop "."; the question mark,
  colon and ellipsis stay as in the source. A row whose source has no final full stop gets none.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A case ending is never written onto a placeholder (%nameৰ is a
  different placeholder): use a noun that carries it ("%name ফাইলৰ", "%count টা বস্তু") or put the placeholder in
  quotation marks ("%name"ৰ). The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Assamese | Note |
|---|---|---|
| content | সমল | content object: সমল বস্তু |
| object | বস্তু | |
| node | নোড | main node: মুখ্য নোড; parent node: মূল নোড |
| location | অৱস্থান | main location: মুখ্য অৱস্থান |
| item (list entry) | বস্তু | |
| class | শ্ৰেণী | the content class; class group: শ্ৰেণী গোট |
| attribute | বৈশিষ্ট্য | |
| datatype | ডাটাৰ প্ৰকাৰ | |
| version | সংস্কৰণ | version history: সংস্কৰণৰ ইতিহাস |
| draft | খচৰা | store draft: খচৰা সঞ্চয় কৰক |
| publish | প্ৰকাশ কৰক | published: প্ৰকাশিত; publishing: প্ৰকাশন |
| send for publishing | প্ৰকাশৰ বাবে পঠিয়াওক | |
| discard | বাদ দিয়ক | |
| archived / pending / rejected | সংৰক্ষিত / বিবেচনাধীন / প্ৰত্যাখ্যাত | |
| siteaccess | ছাইট প্ৰৱেশ | siteaccess as a setting name stays in code |
| site, website | ছাইট, ৱেবছাইট | |
| section | শাখা | the Exponential section; an INI section: INI শাখা |
| role | ভূমিকা | |
| policy | নীতি | |
| limitation | সীমাবদ্ধতা | |
| permission | অনুমতি | access denied: প্ৰৱেশ অস্বীকাৰ কৰা হ'ল |
| module | মডিউল | |
| function | ফাংচন | |
| user / user group | ব্যৱহাৰকাৰী / ব্যৱহাৰকাৰী গোট | |
| group | গোট | |
| anonymous | বেনামী | anonymous user: বেনামী ব্যৱহাৰকাৰী |
| administrator | প্ৰশাসক | administration (interface): প্ৰশাসন |
| username | ব্যৱহাৰকাৰীৰ নাম | login (the name): লগইন নাম |
| password | পাছৱৰ্ড | |
| log in / log out | লগ ইন কৰক / লগ আউট কৰক | the act: লগইন / লগআউট |
| e-mail | ই-মেইল | e-mail address: ই-মেইল ঠিকনা |
| cache | কেচ | clear cache: কেচ পৰিষ্কাৰ কৰক |
| template | টেমপ্লেট | override template: অভাৰৰাইড টেমপ্লেট |
| override | অভাৰৰাইড | |
| fallback | বিকল্প | fallback language: বিকল্প ভাষা |
| design | ডিজাইন | |
| workflow | কাৰ্যপ্ৰবাহ | |
| trigger | ট্ৰিগাৰ | |
| event | ঘটনা | |
| trash | আবৰ্জনা | empty trash: আবৰ্জনা খালী কৰক; move to trash: আবৰ্জনালৈ পঠিয়াওক |
| bookmark | বুকমাৰ্ক | |
| settings | ছেটিংছ | configuration: কনফিগাৰেচন |
| setup | ছেটআপ | the Setup tab |
| layout | লেআউট | Exponential Layouts stays |
| block / zone | ব্লক / অঞ্চল | |
| extension | এক্সটেনচন | |
| package | পেকেজ | |
| search | সন্ধান | to search: সন্ধান কৰক; search index: সন্ধান সূচী |
| log | লগ | a log file: লগ ফাইল; never "log in" |
| queue | শাৰী | job queue: কামৰ শাৰী |
| job / task | কাম | |
| cron | cron | |
| script | স্ক্ৰিপ্ট | |
| kernel, core | কেন্দ্ৰ | |
| server | ছাৰ্ভাৰ | |
| database | ডাটাবেছ | |
| folder / directory | ফোল্ডাৰ / ডাইৰেক্টৰী | |
| file | ফাইল | |
| image | ছবি | thumbnail: সৰু ছবি |
| preview | পূৰ্বদৰ্শন | |
| upload / download | আপলোড কৰক / ডাউনলোড কৰক | |
| export / import | ৰপ্তানি কৰক / আমদানি কৰক | |
| remove | আঁতৰাওক | take out of a list or place |
| delete | মচক | destroy; deleted: মচা হ'ল |
| edit | সম্পাদনা কৰক | |
| editor | সম্পাদক | |
| save / store | সঞ্চয় কৰক | the verb "Store" of the forms |
| Store (shop tab) | দোকান | the shop, as "Shop" |
| cancel | বাতিল কৰক | |
| OK | ঠিক আছে | |
| apply | প্ৰয়োগ কৰক | |
| back | উভতি যাওক | |
| next / previous | পৰৱৰ্তী / পূৰ্বৱৰ্তী | |
| enable / disable | সক্ষম কৰক / অক্ষম কৰক | enabled / disabled: সক্ষম / অক্ষম |
| active / inactive | সক্ৰিয় / নিষ্ক্ৰিয় | |
| run | চলাওক | |
| start / stop | আৰম্ভ কৰক / বন্ধ কৰক | |
| session | ছেছন | |
| hidden / visible | লুকুৱা / দৃশ্যমান | hide / reveal: লুকুৱাওক / দেখুৱাওক |
| archive | সংৰক্ষণাগাৰ | |
| order (sort) | ক্ৰম | sort order: সজোৱাৰ ক্ৰম |
| order (shop) | অৰ্ডাৰ | |
| shop | দোকান | |
| basket | বাস্কেট | |
| product | সামগ্ৰী | |
| customer | গ্ৰাহক | |
| discount | ৰেহাই | |
| VAT | ভেট | |
| price / currency | মূল্য / মুদ্ৰা | |
| tag | টেগ | |
| translation | অনুবাদ | |
| language | ভাষা | main language: মুখ্য ভাষা |
| locale | লোকেল | |
| state | অৱস্থা | the object state |
| status | স্থিতি | |
| view (button) | চাওক | a module view (noun): দৰ্শন |
| format | ফৰ্মেট | |
| sort | সজাওক | |
| filter | ফিল্টাৰ | |
| select | বাছনি কৰক | select all: সকলো বাছনি কৰক; selected: বাছনি কৰা |
| copy / move | কপি কৰক / স্থানান্তৰ কৰক | |
| add / create / new | যোগ কৰক / সৃষ্টি কৰক / নতুন | |
| restore | পুনৰুদ্ধাৰ কৰক | |
| install / uninstall | ইনষ্টল কৰক / আনইনষ্টল কৰক | |
| related objects | সম্পৰ্কিত বস্তু | relation: সম্পৰ্ক |
| dashboard | ডেশ্বব'ৰ্ড | |
| identifier / ID | চিনাক্তকাৰী / ID | |
| priority | অগ্ৰাধিকাৰ | |
| owner | মালিক | |
| modified / created | সলনি কৰা / সৃষ্টি কৰা | |
| date / time | তাৰিখ / সময় | |
| error / warning | ত্ৰুটি / সতৰ্কবাণী | |
| help | সহায় | |
| notification | জাননী | |
| account | একাউণ্ট | |
| default | ডিফল্ট | |
| link | লিংক | |
| none / all | একো নাই / সকলো | |
| yes / no | হয় / নহয় | |
| name / title / description | নাম / শিৰোনাম / বিৱৰণ | |
| value / type / list | মান / প্ৰকাৰ / তালিকা | |
| page / path | পৃষ্ঠা / পথ | |
| required / optional | আৱশ্যকীয় / ঐচ্ছিক | |
| options | বিকল্পসমূহ | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is দোকান, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is সঞ্চয় কৰক.
- "Login" in the user, session and audit lists is the user's login name (লগইন নাম); the Login button of the error
  page is the act (লগ ইন কৰক).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
