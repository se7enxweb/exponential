# Central Kurdish (ckb-IQ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Central Kurdish (Sorani, کوردیی ناوەندی) as written in the Kurdistan Region of Iraq, in the **Kurdish Arabic
alphabet**, written right to left, in the neutral, polite register of the Kurdistan Regional Government portals, Kurdish
news (Rudaw, Kurdistan24) and the Sorani localisations of Windows, Android and Firefox (ckb) and Sorani Wikipedia.
No other locale falls back to `ckb-IQ`. Kurmanji (kur-TR, Latin script) is a sister language with its own catalogue.

- Word order is subject - object - verb; the verb comes last. Use the Sorani clitics and suffixes (ـەکە definite,
  ـێک indefinite, ـەکان plural, ـی ezafe).
- Technical words: the Kurdish word where one is established (فایل, بەکارهێنەر, وشەی نهێنی, ڕێکخستنەکان, گەڕان,
  بنکەدراوە); an international loan in Kurdish spelling where Kurdish IT writing uses it (سێرڤەر, سکریپت, مۆدیول).
  Arabic (ara-SA) and Persian (fas-IR) may help with meaning, but write Kurdish words and Kurdish spelling, not Arabic
  or Persian. Never leave the English word in Latin letters unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **تۆ** / **ئێوە**: the interface uses the polite plural ئێوە forms in sentences (دەتوانن, دڵنیان) and
  the singular imperative on buttons.
- **Buttons, menu items and links**: the short imperative, as Sorani interfaces do: پاشەکەوت بکە (save), هەڵوەشاندنەوە
  (cancel), سڕینەوە (delete), لابردن (remove), دەستکاری بکە (edit), زیادکردن (add), دروستکردن (create), گەڕان
  (search), هەڵبژاردن (select), ناردن (send), بینین (view), بڵاوکردنەوە (publish), بارکردن (upload), داگرتن
  (download). The verbal noun is the button form, as in Sorani localisations: پاشەکەوتکردن is also fine, but stay
  consistent with this list.
- **Titles, column headings and tabs**: nouns or noun phrases: ڕێکخستنەکان, وەرگێڕانەکان, شوێنی سەرەکی, ناو.
- **Instructions**: the polite imperative: "بڕگەکە هەڵبژێرن و کرتە لە \"OK\" بکەن.". "Please" is "تکایە".
- **Confirmations and questions**: "دڵنیان کە دەتانەوێت ...؟": "دڵنیان کە دەتانەوێت ئەم بەشە بسڕنەوە؟".
- **What the user can or may do**: "دەتوانن ...", "ناتوانن ..."; "You do not have permission" is "مۆڵەتتان نییە
  بۆ ...".
- **Messages about a result**: the passive past: "ڕەشنووسەکە پاشەکەوت کرا.", "فایلەکە بە سەرکەوتوویی بار کرا.".

## 3. Script and typography

- **Kurdish Arabic script**: every vowel is written (ا ە ێ ۆ و وو ی)، with the Kurdish letters ڕ ڵ ێ ۆ ە ڤ پ چ ژ گ
  ک; ە is U+06D5, never ه (U+0647) or ة; ی is U+06CC and ک is U+06A9, never the Arabic ي and ك. A word beginning with
  a vowel takes ئ.
- **Direction**: the text is right to left; Latin words, numbers and placeholders inside it stay as they are.
- **Punctuation**: the Arabic comma ، (U+060C), semicolon ؛ (U+061B) and question mark ؟ (U+061F); the full stop
  is "." and the colon ":" as in the source.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A suffix or clitic is never written onto a placeholder (%nameەکە is
  a different placeholder): use a noun that carries it ("فایلی %name", "%count بڕگە") or put the placeholder in
  quotation marks ("%name"). The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or «...»; `&quot;` and other entities are kept exactly.
- **Numbers**: digits 0-9 (the Western digits, as Kurdish software does).

## 4. Glossary

Binding for the kernel and every extension.

| English | Central Kurdish | Note |
|---|---|---|
| content | ناوەڕۆک | content object: ئۆبجێکتی ناوەڕۆک |
| object | ئۆبجێکت | |
| tree | دار | tree menu: لیستی داری; content structure tree: داری پێکهاتەی ناوەڕۆک |
| node | گرێ | main node: گرێی سەرەکی; parent node: گرێی باوان |
| location | شوێن | main location: شوێنی سەرەکی |
| item (list entry) | بڕگە | |
| class | پۆل | the content class; class group: کۆمەڵەی پۆل |
| attribute | تایبەتمەندی | |
| datatype | جۆری داتا | |
| version | وەشان | version history: مێژووی وەشانەکان |
| draft | ڕەشنووس | store draft: ڕەشنووس پاشەکەوت بکە |
| publish | بڵاوکردنەوە | published: بڵاوکراوە |
| send for publishing | ناردن بۆ بڵاوکردنەوە | |
| discard | فڕێدان | |
| archived / pending / rejected | ئەرشیفکراو / چاوەڕوان / ڕەتکراوە | |
| siteaccess | دەستگەیشتنی ماڵپەڕ | siteaccess as a setting name stays in code |
| site, website | ماڵپەڕ | |
| section | بەش | the Exponential section; an INI section: بەشی INI |
| role | ڕۆڵ | |
| policy | سیاسەت | |
| limitation | سنووردارکردن | |
| permission | مۆڵەت | access denied: دەستگەیشتن ڕەتکرایەوە |
| module | مۆدیول | |
| function | فەنکشن | |
| user / user group | بەکارهێنەر / کۆمەڵەی بەکارهێنەران | |
| group | کۆمەڵە | |
| anonymous | نەناسراو | anonymous user: بەکارهێنەری نەناسراو |
| administrator | بەڕێوەبەر | administration (interface): بەڕێوەبردن |
| username | ناوی بەکارهێنەر | |
| Login (the name in user, session, audit lists) | ناوی چوونەژوورەوە | the act (button): چوونەژوورەوە |
| password | وشەی نهێنی | |
| log in / log out | چوونەژوورەوە / چوونەدەرەوە | |
| e-mail | ئیمەیڵ | e-mail address: ناونیشانی ئیمەیڵ |
| cache | کاش | clear cache: پاککردنەوەی کاش |
| template | قاڵب | override template: قاڵبی جێگرەوە |
| override | جێگرتنەوە | |
| fallback | یەدەگ | fallback language: زمانی یەدەگ |
| design | دیزاین | |
| workflow | ڕەوتی کار | |
| trigger | هاندەر | |
| event | ڕووداو | |
| trash | سەبەتەی خۆڵ | empty trash: بەتاڵکردنەوەی سەبەتەی خۆڵ |
| bookmark | نیشانە | |
| settings | ڕێکخستنەکان | configuration: ڕێکخستن |
| setup | دامەزراندن | the Setup tab |
| layout | داڕشتن | Exponential Layouts stays |
| block / zone | بلۆک / ناوچە | |
| extension | پێوەکراو | |
| package | پاکێج | |
| search | گەڕان | search index: پێڕستی گەڕان |
| log | تۆمار | a log file: فایلی تۆمار; never "log in" |
| queue | ڕیز | job queue: ڕیزی ئەرکەکان |
| job / task | ئەرک | |
| cron | cron | |
| script | سکریپت | |
| kernel, core | ناوک | |
| server | سێرڤەر | |
| database | بنکەدراوە | |
| folder / directory | بوخچە | |
| file | فایل | |
| image | وێنە | thumbnail: وێنۆچکە |
| preview | پێشبینین | |
| upload / download | بارکردن / داگرتن | |
| export / import | هەناردن / هاوردن | |
| remove | لابردن | take out of a list or place |
| delete | سڕینەوە | destroy; deleted: سڕایەوە |
| edit | دەستکاری بکە | the noun: دەستکاریکردن |
| editor | دەستکاریکەر | |
| save / store | پاشەکەوت بکە | the verb "Store" of the forms |
| Store (shop tab) | فرۆشگا | the shop, as "Shop" |
| cancel | هەڵوەشاندنەوە | |
| OK | باشە | |
| apply | جێبەجێکردن | |
| back | گەڕانەوە | |
| next / previous | دواتر / پێشوو | |
| enable / disable | چالاککردن / ناچالاککردن | enabled / disabled: چالاکە / ناچالاکە |
| active / inactive | چالاک / ناچالاک | |
| run | جێبەجێکردن | |
| start / stop | دەستپێکردن / وەستاندن | |
| session | دانیشتن | |
| hidden / visible | شاراوە / دیار | hide / reveal: شاردنەوە / پیشاندان |
| archive | ئەرشیف | |
| order (sort) | ڕیزبەندی | sort order: ڕیزبەندی |
| order (shop) | داواکاری | |
| shop | فرۆشگا | |
| basket | سەبەتە | |
| product | بەرهەم | |
| customer | کڕیار | |
| discount | داشکاندن | |
| VAT | باجی بەهای زیادکراو | |
| price / currency | نرخ / دراو | |
| tag | تاگ | |
| translation | وەرگێڕان | |
| language | زمان | main language: زمانی سەرەکی |
| locale | ناوچەیی | |
| state | دۆخ | the object state |
| status | بارودۆخ | |
| view (button) | بینین | a module view (noun): پیشاندان |
| format | فۆرمات | |
| sort | ڕیزکردن | |
| filter | پاڵاوتن | |
| select | هەڵبژاردن | select all: هەموو هەڵبژێرە; selected: هەڵبژێردراو |
| copy / move | لەبەرگرتنەوە / گواستنەوە | |
| add / create / new | زیادکردن / دروستکردن / نوێ | |
| restore | گەڕاندنەوە | |
| install / uninstall | دامەزراندن / لابردنی دامەزراندن | |
| related objects | ئۆبجێکتە پەیوەندیدارەکان | relation: پەیوەندی |
| dashboard | داشبۆرد | |
| identifier / ID | ناسێنەر / ID | |
| priority | پێشینەیی | |
| owner | خاوەن | |
| modified / created | گۆڕدراو / دروستکراو | |
| date / time | بەروار / کات | |
| error / warning | هەڵە / ئاگاداری | |
| help | یارمەتی | |
| notification | ئاگادارکردنەوە | |
| account | هەژمار | |
| default | بنەڕەت | |
| link | بەستەر | |
| none / all | هیچ / هەموو | |
| yes / no | بەڵێ / نەخێر | |
| name / title / description | ناو / ناونیشان / وەسف | |
| value / type / list | بەها / جۆر / لیست | |
| page / path | پەڕە / ڕێڕەو | |
| required / optional | پێویست / ئارەزوومەندانە | |
| options | هەڵبژاردەکان | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is فرۆشگا, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  پاشەکەوت بکە.
- "Login" in the user, session and audit lists is the user's login name (ناوی چوونەژوورەوە); the Login button of the
  error page is the act (چوونەژوورەوە). The registration test checks both.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
