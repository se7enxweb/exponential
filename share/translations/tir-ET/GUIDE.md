# Tigrinya (tir-ET): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Tigrinya (ትግርኛ) as used in Tigray and in Eritrea, in the **Ethiopic (Geʽez) script**, in the
neutral, polite register of Tigrinya news services (BBC Tigrinya, Dimtsi Weyane), the Tigrinya localisations of
Google and Microsoft and Tigrinya Wikipedia. No other locale falls back to `tir-ET`; an Eritrean locale would.

- Word order is subject - object - verb; the verb comes last.
- Write Tigrinya, not Amharic. Amharic (amh-ET) shares the script and much vocabulary and may help with technical
  terms, but the grammar and many words differ: Tigrinya ኣ- (not አ-) at the start of most words (ኣስፍሕ, ኣቐምጥ),
  -ኩም / -ክን endings, ኣሎ / የለን (there is / is not), እዚ (this), ኩሉ (all), ን (for, to), ካብ (from), ኣብ (in, on),
  ምስ (with), ወይ (or), ግን (but), እንተ (if).
- Technical words: a Tigrinya word where one is in use (ፋይል, ተጠቃሚ, መሕለፊ ቃል, ቅጥዕታት, ምድላይ); an English loan in
  Ethiopic script where Tigrinya IT writing uses it (ሰርቨር, ስክሪፕት, ሞጁል, ዳታቤዝ). Never leave the English word in Latin
  letters unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **ንስኹም** (polite plural "you"), with the matching verb forms (ክትገብሩ ትኽእሉ ኢኹም, ትደልዩ ዶ).
- **Buttons, menu items and links**: the short singular imperative, as Ethiopic-script interfaces do: ኣቐምጥ (save),
  ሰርዝ (cancel), ደምስስ (delete), ኣውጽእ (remove), ኣርትዕ (edit), ወስኽ (add), ፍጠር (create), ድለ (search), ምረጽ (select),
  ስደድ (send), ርአ (view), ኣሕትም (publish), ስቐል (upload), ኣውርድ (download).
- **Titles, column headings and tabs**: nouns or noun phrases: ቅጥዕታት, ትርጉማት, ቀንዲ ቦታ, ስም.
- **Instructions**: the polite plural imperative: "ነቲ ኣቕሓ ምረጹ እሞ \"OK\" ጠውቑ።". "Please" is "በጃኹም".
- **Confirmations and questions**: "ብርግጽ ... ትደልዩ ዶ?": "ብርግጽ ነዚ ክፋል ክትድምስስዎ ትደልዩ ዶ?".
- **What the user can or may do**: "... ክትገብሩ ትኽእሉ ኢኹም", "... ክትገብሩ ኣይትኽእሉን"; "You do not have permission" is
  "... ፍቓድ የብልኩምን".
- **Messages about a result**: the passive past: "እቲ ንድፊ ተቐሚጡ።", "እቲ ፋይል ብዓወት ተሰቒሉ።".

## 3. Script and typography

- **Ethiopic script** (U+1200-U+139F), with the Tigrinya letters ቐ ቘ ኸ ዀ where the word has them.
- No capital letters; Latin product names keep theirs.
- **Punctuation**: the sentence ends with the full stop ። (U+1362) where the English ends with "."; the comma is ፣
  (U+1363) and the semicolon ፤ (U+1364) inside sentences; the question mark "?", the colon ":" and the ellipsis stay
  as in the source. A row whose source has no final full stop gets none. Words are separated by spaces.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A prefix or suffix is never written onto a placeholder (ን%name or
  %nameን): use a noun that carries it ("ንፋይል %name", "%count ኣቕሑ"). The same for code, file names, INI settings
  and product names.
- **Quotation marks**: straight double quotes "..." or «...»; `&quot;` and other entities are kept exactly.
- **Numbers**: digits 0-9, never the Ethiopic numerals.

## 4. Glossary

Binding for the kernel and every extension.

| English | Tigrinya | Note |
|---|---|---|
| content | ትሕዝቶ | content object: ናይ ትሕዝቶ ኣቕሓ |
| object | ኣቕሓ | |
| tree | ኦም | tree menu: ዝርዝር ኦም; content structure tree: ኦም ኣወዳድባ ትሕዝቶ |
| node | መስቀለኛ | main node: ቀንዲ መስቀለኛ; parent node: ወላዲ መስቀለኛ |
| location | ቦታ | main location: ቀንዲ ቦታ |
| item (list entry) | ኣቕሓ | |
| class | ክፍሊ | the content class; class group: ጉጅለ ክፍሊ |
| attribute | መለለዪ ባህርይ | short: ባህርይ |
| datatype | ዓይነት ዳታ | |
| version | ሕታም | version history: ታሪኽ ሕታማት |
| draft | ንድፊ | store draft: ንድፊ ኣቐምጥ |
| publish | ኣሕትም | published: ዝተሓትመ |
| send for publishing | ንሕትመት ስደድ | |
| discard | ደርብ | |
| archived / pending / rejected | ዝተዓቀበ / ዝጽበ / ዝተነጽገ | |
| siteaccess | ምእታው መርበብ | siteaccess as a setting name stays in code |
| site, website | መርበብ ሓበሬታ | short: መርበብ |
| section | ክፋል | the Exponential section; an INI section: ክፋል INI |
| role | ተራ | |
| policy | ፖሊሲ | |
| limitation | ገደብ | |
| permission | ፍቓድ | access denied: ምእታው ተኸልኪሉ |
| module | ሞጁል | |
| function | ተግባር | |
| user / user group | ተጠቃሚ / ጉጅለ ተጠቀምቲ | plural: ተጠቀምቲ |
| group | ጉጅለ | |
| anonymous | ስም ዘይብሉ | anonymous user: ስም ዘይብሉ ተጠቃሚ |
| administrator | ኣመሓዳሪ | administration (interface): ምሕደራ |
| username | ስም ተጠቃሚ | login (the name): ስም መእተዊ |
| password | መሕለፊ ቃል | |
| log in / log out | እቶ / ውጻእ | the act: ምእታው / ምውጻእ |
| e-mail | ኢመይል | e-mail address: ኣድራሻ ኢመይል |
| cache | ካሽ | clear cache: ካሽ ኣጽሪ |
| template | ቅጥዒ | override template: ተካኢ ቅጥዒ |
| override | ምትካእ | |
| fallback | ተተካኢ | fallback language: ተተካኢ ቋንቋ |
| design | ዲዛይን | |
| workflow | ውሕጅ ስራሕ | |
| trigger | ኣለዓዒሊ | |
| event | ፍጻመ | |
| trash | መጓሓፊ | empty trash: መጓሓፊ ኣጽሪ; move to trash: ናብ መጓሓፊ ስደድ |
| bookmark | ምልክት ገጽ | |
| settings | ቅጥዕታት | configuration: ውቅር |
| setup | ምድላው | the Setup tab |
| layout | ኣቀማምጣ | Exponential Layouts stays |
| block / zone | ብሎክ / ዞባ | |
| extension | መስፋሕቲ | |
| package | ጥቕላል | |
| search | ምድላይ | to search: ድለ; search index: መዘርዝር ምድላይ |
| log | መዝገብ | a log file: ፋይል መዝገብ; never "log in" |
| queue | መስርዕ | job queue: መስርዕ ስራሓት |
| job / task | ስራሕ | |
| cron | cron | |
| script | ስክሪፕት | |
| kernel, core | ማእከል | |
| server | ሰርቨር | |
| database | ዳታቤዝ | |
| folder / directory | ማህደር | |
| file | ፋይል | |
| image | ስእሊ | thumbnail: ንእሽቶ ስእሊ |
| preview | ቅድመ ርእይቶ | |
| upload / download | ስቐል / ኣውርድ | |
| export / import | ስደድ ናብ ወጻኢ / ኣእቱ | |
| remove | ኣውጽእ | take out of a list or place |
| delete | ደምስስ | destroy; deleted: ተደምሲሱ |
| edit | ኣርትዕ | |
| editor | ኣርታዒ | |
| save / store | ኣቐምጥ | the verb "Store" of the forms |
| Store (shop tab) | ድኳን | the shop, as "Shop" |
| cancel | ሰርዝ | |
| OK | ሕራይ | |
| apply | ተግብር | |
| back | ተመለስ | |
| next / previous | ዝቕጽል / ዝሓለፈ | |
| enable / disable | ኣንቅሕ / ኣጥፍእ | enabled / disabled: ንጡፍ / ዘይንጡፍ |
| active / inactive | ንጡፍ / ዘይንጡፍ | |
| run | ኣካይድ | |
| start / stop | ጀምር / ኣቋርጽ | |
| session | ክፍለ ግዜ | |
| hidden / visible | ሕቡእ / ዝርአ | hide / reveal: ሕባእ / ኣርኢ |
| archive | መዝገብ ቤት | |
| order (sort) | ስርዓት | sort order: ስርዓት ምድርዳር |
| order (shop) | ትእዛዝ | |
| shop | ድኳን | |
| basket | ዘንቢል | |
| product | ፍርያት | |
| customer | ዓሚል | |
| discount | ቅናሽ | |
| VAT | ቫት | |
| price / currency | ዋጋ / ባጤራ | |
| tag | ምልክት | |
| translation | ትርጉም | |
| language | ቋንቋ | main language: ቀንዲ ቋንቋ |
| locale | ከባቢ | |
| state | ኩነት | the object state |
| status | ኩነታት | |
| view (button) | ርአ | a module view (noun): ትርኢት |
| format | ቅርጺ | |
| sort | ደርድር | |
| filter | መጻረዪ | to filter: ኣጻሪ |
| select | ምረጽ | select all: ኩሉ ምረጽ; selected: ዝተመርጸ |
| copy / move | ቅዳሕ / ኣግዕዝ | |
| add / create / new | ወስኽ / ፍጠር / ሓድሽ | |
| restore | ምለስ | |
| install / uninstall | ጸዓን / ኣልግስ | installation: ምጽዓን |
| related objects | ተዛመድቲ ኣቕሑ | relation: ርክብ |
| dashboard | ሰሌዳ ምቁጽጻር | |
| identifier / ID | መለለዪ / ID | |
| priority | ቀዳምነት | |
| owner | ወናኒ | |
| modified / created | ዝተቐየረ / ዝተፈጥረ | |
| date / time | ዕለት / ግዜ | |
| error / warning | ጌጋ / መጠንቀቕታ | |
| help | ሓገዝ | |
| notification | መፍለጢ | |
| account | ሕሳብ | |
| default | ነባሪ | |
| link | መላግቦ | |
| none / all | ዋላ ሓደ / ኩሉ | |
| yes / no | እወ / ኣይፋል | |
| name / title / description | ስም / ኣርእስቲ / መግለጺ | |
| value / type / list | ዋጋ / ዓይነት / ዝርዝር | |
| page / path | ገጽ / መንገዲ | |
| required / optional | ኣድላዪ / ኣማራጺ | |
| options | ኣማራጺታት | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ድኳን, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  ኣቐምጥ.
- "Login" in the user, session and audit lists is the user's login name (ስም መእተዊ); the Login button of the error page
  is the act (እቶ).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
