# Uyghur (uig-CN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Uyghur (ئۇيغۇرچە) in the **Uyghur Arabic alphabet** (UEY, ئۇيغۇر ئەرەب يېزىقى), written right to
left, in the neutral, polite register of Uyghur software localisations (Windows, Android, Firefox and LibreOffice
ug) and Uyghur Wikipedia. No other locale falls back to `uig-CN`.

- Word order is subject - object - verb; the verb comes last. Suffixes follow vowel harmony (-لار/-لەر,
  -دا/-دە, -دىن/-دىن, -نى).
- Technical words: the Uyghur word where one is established (ھۆججەت file, ئىشلەتكۈچى user, ئىم password,
  تەڭشەك setting, ئىزدەش search, ساندان database); an international loan in Uyghur spelling where Uyghur IT writing
  uses it (مۇلازىمېتىر server, قوليازما script, بۆلەك module). Turkish (tur-TR) and Uzbek (uzb-UZ) are relatives and may
  help with structure, but write Uyghur words and spelling. Never leave the English word in Latin letters unless it
  is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **سىز** (polite "you") with the matching verb forms (قىلالايسىز, خالامسىز).
- **Buttons, menu items and links**: the short imperative, as Uyghur interfaces do: ساقلا (save), ۋاز كەچ (cancel),
  ئۆچۈر (delete), چىقىرىۋەت (remove), تەھرىرلە (edit), قوش (add), قۇر (create), ئىزدە (search), تاللا (select),
  يوللا (send), كۆرسەت / كۆر (view: كۆرۈش), ئېلان قىل (publish), يۈكلە (upload), چۈشۈر (download).
- **Titles, column headings and tabs**: nouns or noun phrases: تەڭشەكلەر, تەرجىمىلەر, ئاساسىي ئورۇن, ئىسىم.
- **Instructions**: the polite imperative in -ڭ / -ىڭ: "تۈرنى تاللاپ \"OK\" نى چېكىڭ.". "Please" is "ئەرزىمەس"
  only where natural; usually "... قىلىڭ".
- **Confirmations and questions**: "راستىنلا ... نى خالامسىز؟": "راستىنلا بۇ بۆلەكنى ئۆچۈرەمسىز؟".
- **What the user can or may do**: "... قىلالايسىز", "... قىلالمايسىز"; "You do not have permission" is "... ھوقۇقىڭىز
  يوق".
- **Messages about a result**: the passive past: "ئارگىنال ساقلاندى.", "ھۆججەت مۇۋەپپەقىيەتلىك يۈكلەندى.".

## 3. Script and typography

- **Uyghur Arabic script**: every vowel is written (ا ە و ۇ ۆ ۈ ې ى), a word beginning with a vowel takes the hamza
  carrier ئ (ئىشلەتكۈچى). Use the Uyghur letters (ە U+06D5, ۇ ۆ ۈ ۋ ې ى, ڭ, گ, ك, پ, چ, ژ), never Persian or Arabic
  look-alikes (ه for ە, ي for ى).
- **Direction**: the text is right to left; Latin words, numbers and placeholders inside it stay as they are, the
  browser places them.
- **Punctuation**: the Arabic comma ، (U+060C), semicolon ؛ (U+061B) and question mark ؟ (U+061F); the full stop is
  "." and the colon ":" as in the source.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A suffix is never written onto a placeholder (%nameنى is a different
  placeholder): use a noun that carries it ("%name ھۆججىتىنى", "%count تۈر") or put the placeholder in quotation
  marks with the suffix after them ("%name" نى). The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source, or «...»; `&quot;` and other entities
  are kept exactly.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Uyghur | Note |
|---|---|---|
| content | مەزمۇن | content object: مەزمۇن ئوبيېكتى |
| object | ئوبيېكت | |
| tree | دەرەخ | tree menu: دەرەخسىمان تىزىملىك; content structure tree: مەزمۇن قۇرۇلما دەرىخى |
| node | تۈگۈن | main node: ئاساسىي تۈگۈن; parent node: ئانا تۈگۈن |
| location | ئورۇن | main location: ئاساسىي ئورۇن |
| item (list entry) | تۈر | |
| class | تىپ | the content class; class group: تىپ گۇرۇپپىسى |
| attribute | خاسلىق | |
| datatype | سانلىق مەلۇمات تىپى | |
| version | نەشر | version history: نەشر تارىخى |
| draft | ئارگىنال | store draft: ئارگىنالنى ساقلا |
| publish | ئېلان قىل | published: ئېلان قىلىنغان |
| send for publishing | ئېلان قىلىشقا يوللا | |
| discard | تاشلا | |
| archived / pending / rejected | ئارخىپلانغان / كۈتۈۋاتىدۇ / رەت قىلىنغان | |
| siteaccess | تور بېكەت زىيارىتى | siteaccess as a setting name stays in code |
| site, website | تور بېكەت | |
| section | بۆلەك | the Exponential section; an INI section: INI بۆلىكى |
| role | رول | |
| policy | سىياسەت | |
| limitation | چەكلىمە | |
| permission | ھوقۇق | access denied: زىيارەت رەت قىلىندى |
| module | مودۇل | |
| function | فۇنكسىيە | |
| user / user group | ئىشلەتكۈچى / ئىشلەتكۈچى گۇرۇپپىسى | |
| group | گۇرۇپپا | |
| anonymous | نامسىز | anonymous user: نامسىز ئىشلەتكۈچى |
| administrator | باشقۇرغۇچى | administration (interface): باشقۇرۇش |
| username | ئىشلەتكۈچى ئىسمى | |
| Login (the name in user, session, audit lists) | كىرىش ئىسمى | the act (button): كىرىش |
| password | ئىم | |
| log in / log out | كىرىش / چىقىش | |
| e-mail | ئېلخەت | e-mail address: ئېلخەت ئادرېسى |
| cache | غەملەك | clear cache: غەملەكنى تازىلا |
| template | قېلىپ | override template: ئورنىنى ئالىدىغان قېلىپ |
| override | ئورنىنى ئېلىش | |
| fallback | زاپاس | fallback language: زاپاس تىل |
| design | لايىھە | |
| workflow | خىزمەت ئېقىمى | |
| trigger | قوزغاتقۇچ | |
| event | ھادىسە | |
| trash | ئەخلەت چېلىكى | empty trash: ئەخلەت چېلىكىنى بوشات |
| bookmark | خەتكۈچ | |
| settings | تەڭشەكلەر | configuration: سەپلىمە |
| setup | ئورنىتىش | the Setup tab |
| layout | ئورۇنلاشتۇرۇش | Exponential Layouts stays |
| block / zone | بۆلەكچە / رايون | |
| extension | كېڭەيتىلمە | |
| package | بوغچا | |
| search | ئىزدەش | to search: ئىزدە; search index: ئىزدەش ئىندېكسى |
| log | خاتىرە | a log file: خاتىرە ھۆججىتى; never "log in" |
| queue | ئۆچرەت | job queue: ۋەزىپە ئۆچرىتى |
| job / task | ۋەزىپە | |
| cron | cron | |
| script | قوليازما | |
| kernel, core | يادرو | |
| server | مۇلازىمېتىر | |
| database | ساندان | |
| folder / directory | قىسقۇچ / مۇندەرىجە | |
| file | ھۆججەت | |
| image | رەسىم | thumbnail: كىچىك رەسىم |
| preview | ئالدىن كۆرۈش | |
| upload / download | يۈكلە / چۈشۈر | |
| export / import | چىقار / ئەكىر | |
| remove | چىقىرىۋەت | take out of a list or place |
| delete | ئۆچۈر | destroy; deleted: ئۆچۈرۈلدى |
| edit | تەھرىرلە | |
| editor | تەھرىرلىگۈچ | |
| save / store | ساقلا | the verb "Store" of the forms |
| Store (shop tab) | دۇكان | the shop, as "Shop" |
| cancel | ۋاز كەچ | |
| OK | جەزملە | |
| apply | قوللان | |
| back | قايت | |
| next / previous | كېيىنكى / ئالدىنقى | |
| enable / disable | قوزغات / چەكلە | enabled / disabled: قوزغىتىلغان / چەكلەنگەن |
| active / inactive | ئاكتىپ / ئاكتىپ ئەمەس | |
| run | ئىجرا قىل | |
| start / stop | باشلا / توختات | |
| session | سۆزلىشىش | |
| hidden / visible | يوشۇرۇن / كۆرۈنىدىغان | hide / reveal: يوشۇر / كۆرسەت |
| archive | ئارخىپ | |
| order (sort) | تەرتىپ | sort order: تىزىش تەرتىپى |
| order (shop) | زاكاز | |
| shop | دۇكان | |
| basket | سېۋەت | |
| product | مەھسۇلات | |
| customer | خېرىدار | |
| discount | ئېتىبار | |
| VAT | قوشۇلما قىممەت بېجى | |
| price / currency | باھا / پۇل بىرلىكى | |
| tag | بەلگە | |
| translation | تەرجىمە | |
| language | تىل | main language: ئاساسىي تىل |
| locale | رايون تەڭشىكى | |
| state | ھالەت | the object state |
| status | ھالەت | |
| view (button) | كۆرۈش | a module view (noun): كۆرۈنۈش |
| format | فورمات | |
| sort | تىز | |
| filter | سۈزگۈچ | to filter: سۈز |
| select | تاللا | select all: ھەممىنى تاللا; selected: تاللانغان |
| copy / move | كۆچۈر / يۆتكە | |
| add / create / new | قوش / قۇر / يېڭى | |
| restore | ئەسلىگە كەلتۈر | |
| install / uninstall | ئورنات / ئۆچۈر | |
| related objects | مۇناسىۋەتلىك ئوبيېكتلار | relation: مۇناسىۋەت |
| dashboard | باشقۇرۇش تاختىسى | |
| identifier / ID | بەلگىلىگۈچ / ID | |
| priority | ئالدىنلىق | |
| owner | ئىگىسى | |
| modified / created | ئۆزگەرتىلگەن / قۇرۇلغان | |
| date / time | چېسلا / ۋاقىت | |
| error / warning | خاتالىق / ئاگاھلاندۇرۇش | |
| help | ياردەم | |
| notification | ئۇقتۇرۇش | |
| account | ھېسابات | |
| default | كۆڭۈلدىكى | |
| link | ئۇلانما | |
| none / all | يوق / ھەممىسى | |
| yes / no | ھەئە / ياق | |
| name / title / description | ئىسىم / ماۋزۇ / چۈشەندۈرۈش | |
| value / type / list | قىممەت / تىپ / تىزىملىك | |
| page / path | بەت / يول | |
| required / optional | زۆرۈر / ئىختىيارى | |
| options | تاللانمىلار | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is دۇكان, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  ساقلا.
- "Login" in the user, session and audit lists is the user's login name (كىرىش ئىسمى); the Login button of the error
  page is the act (كىرىش). The registration test checks both.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
