# Sinhala (sin-LK): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Sinhala (සිංහල) as used in Sri Lanka, in the **Sinhala script**, in the neutral, polite register of
Sri Lankan government portals (gov.lk), the Sinhala localisations of Windows, Android and Firefox (si) and Sinhala
Wikipedia. No other locale falls back to `sin-LK`.

- Word order is subject - object - verb; the verb comes last. Use the written forms of interfaces, not the
  colloquial spoken forms, and not the heavy literary forms either.
- Technical words: a Sinhala word where one is established (ගොනුව, පරිශීලක, මුරපදය, සැකසුම්, සෙවීම, දත්ත
  සමුදාය); an English loan written in Sinhala script where Sinhala IT writing uses it (සර්වරය, ස්ක්‍රිප්ටය, මොඩියුලය,
  කෑෂ්). Never leave the English word in Latin letters unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is addressed politely and impersonally, as Sinhala software does; where a pronoun is needed, **ඔබ**
  (ඔබට, ඔබේ), with the polite verb forms (ඔබට ... කළ හැක, ඔබට ... අවශ්‍යද).
- **Buttons, menu items and links**: the short verbal noun or the polite imperative stem, as Sinhala interfaces do:
  සුරකින්න (save), අවලංගු කරන්න (cancel), මකන්න (delete), ඉවත් කරන්න (remove), සංස්කරණය කරන්න (edit), එක් කරන්න
  (add), සාදන්න (create), සොයන්න (search), තෝරන්න (select), යවන්න (send), බලන්න (view), ප්‍රකාශනය කරන්න
  (publish), උඩුගත කරන්න (upload), බාගන්න (download).
- **Titles, column headings and tabs**: nouns or noun phrases: සැකසුම්, පරිවර්තන, ප්‍රධාන ස්ථානය, නම.
- **Instructions**: the polite -න්න form: "අයිතමය තෝරා \"OK\" ක්ලික් කරන්න.". "Please" is "කරුණාකර".
- **Confirmations and questions**: "ඔබට ... අවශ්‍ය බව විශ්වාසද?": "ඔබට මෙම කොටස මැකීමට අවශ්‍ය බව විශ්වාසද?".
- **What the user can or may do**: "ඔබට ... කළ හැක", "ඔබට ... කළ නොහැක"; "You do not have permission" is
  "ඔබට ... අවසරය නැත".
- **Messages about a result**: the passive past: "කෙටුම්පත සුරකින ලදී.", "ගොනුව සාර්ථකව උඩුගත කරන ලදී.".

## 3. Script and typography

- **Sinhala script** (U+0D80-U+0DFF), with the zero-width joiner where the conjunct needs it (ශ්‍රී, ප්‍ර, ක්‍ර) as
  standard Sinhala text writes it.
- Sinhala has no capital letters; Latin product names keep theirs.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. A case ending is never written onto a placeholder (%nameට is a
  different placeholder): use a noun that carries it ("%name ගොනුවට", "අයිතම %count") or put the placeholder in
  quotation marks ("%name" ට). The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: Western punctuation as in the source (full stop, comma, question mark, colon).
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Sinhala | Note |
|---|---|---|
| content | අන්තර්ගතය | content object: අන්තර්ගත වස්තුව |
| object | වස්තුව | plural: වස්තු |
| node | නෝඩය | main node: ප්‍රධාන නෝඩය; parent node: මාපිය නෝඩය |
| location | ස්ථානය | main location: ප්‍රධාන ස්ථානය |
| item (list entry) | අයිතමය | |
| class | පන්තිය | the content class; class group: පන්ති සමූහය |
| attribute | ගුණාංගය | |
| datatype | දත්ත වර්ගය | |
| version | අනුවාදය | version history: අනුවාද ඉතිහාසය |
| draft | කෙටුම්පත | store draft: කෙටුම්පත සුරකින්න |
| publish | ප්‍රකාශනය කරන්න | published: ප්‍රකාශිත; publishing: ප්‍රකාශනය |
| send for publishing | ප්‍රකාශනයට යවන්න | |
| discard | ඉවතලන්න | discard draft: කෙටුම්පත ඉවතලන්න |
| archived / pending / rejected | සංරක්ෂිත / පොරොත්තුවෙන් / ප්‍රතික්ෂේපිත | |
| siteaccess | අඩවි ප්‍රවේශය | siteaccess as a setting name stays in code |
| site, website | අඩවිය, වෙබ් අඩවිය | |
| section | කොටස | the Exponential section; an INI section: INI කොටස |
| role | භූමිකාව | |
| policy | ප්‍රතිපත්තිය | one rule of a role |
| limitation | සීමාව | |
| permission | අවසරය | access denied: ප්‍රවේශය ප්‍රතික්ෂේප විය |
| module | මොඩියුලය | |
| function | ශ්‍රිතය | |
| user / user group | පරිශීලකයා / පරිශීලක සමූහය | plural: පරිශීලකයින් |
| group | සමූහය | |
| anonymous | නිර්නාමික | anonymous user: නිර්නාමික පරිශීලකයා |
| administrator | පරිපාලක | administration (interface): පරිපාලනය |
| username | පරිශීලක නාමය | login (the name): පිවිසුම් නාමය |
| password | මුරපදය | |
| log in / log out | පිවිසෙන්න / ඉවත් වන්න | the act: පිවිසුම / ඉවත්වීම |
| e-mail | ඊමේල් | e-mail address: ඊමේල් ලිපිනය |
| cache | හැඹිලිය | clear cache: හැඹිලිය හිස් කරන්න |
| template | අච්චුව | override template: ප්‍රතිස්ථාපන අච්චුව |
| override | ප්‍රතිස්ථාපනය | |
| fallback | විකල්පය | fallback language: විකල්ප භාෂාව |
| design | සැලසුම | |
| workflow | කාර්ය ප්‍රවාහය | |
| trigger | ප්‍රේරකය | |
| event | සිදුවීම | |
| trash | කුණු බඳුන | empty trash: කුණු බඳුන හිස් කරන්න; move to trash: කුණු බඳුනට යවන්න |
| bookmark | පිටු සලකුණ | |
| settings | සැකසුම් | configuration: වින්‍යාසය |
| setup | පිහිටුවීම | the Setup tab |
| layout | පිරිසැලසුම | Exponential Layouts stays |
| block / zone | කොටස් කුට්ටිය / කලාපය | |
| extension | දිගුව | |
| package | පැකේජය | |
| search | සෙවීම | to search: සොයන්න; search index: සෙවුම් දර්ශකය |
| log | ලොගය | a log file: ලොග් ගොනුව; never "log in" |
| queue | පෝලිම | job queue: කාර්ය පෝලිම |
| job / task | කාර්යය | |
| cron | cron | |
| script | ස්ක්‍රිප්ටය | |
| kernel, core | හරය | |
| server | සේවාදායකය | |
| database | දත්ත සමුදාය | |
| folder / directory | ෆෝල්ඩරය / නාමාවලිය | |
| file | ගොනුව | |
| image | රූපය | thumbnail: සිඟිති රූපය |
| preview | පෙරදසුන | |
| upload / download | උඩුගත කරන්න / බාගන්න | |
| export / import | අපනයනය / ආයාත කරන්න | |
| remove | ඉවත් කරන්න | take out of a list or place |
| delete | මකන්න | destroy; deleted: මකා දමන ලදී |
| edit | සංස්කරණය කරන්න | |
| editor | සංස්කාරක | |
| save / store | සුරකින්න | the verb "Store" of the forms |
| Store (shop tab) | වෙළඳසැල | the shop, as "Shop" |
| cancel | අවලංගු කරන්න | |
| OK | හරි | |
| apply | යොදන්න | |
| back | ආපසු | |
| next / previous | ඊළඟ / පෙර | |
| enable / disable | සබල කරන්න / අබල කරන්න | enabled / disabled: සබලයි / අබලයි |
| active / inactive | සක්‍රිය / අක්‍රිය | |
| run | ධාවනය කරන්න | |
| start / stop | අරඹන්න / නවත්වන්න | |
| session | සැසිය | |
| hidden / visible | සැඟවුණු / දෘශ්‍යමාන | hide / reveal: සඟවන්න / පෙන්වන්න |
| archive | සංරක්ෂණය | |
| order (sort) | අනුපිළිවෙළ | sort order: වර්ග කිරීමේ අනුපිළිවෙළ |
| order (shop) | ඇණවුම | |
| shop | වෙළඳසැල | |
| basket | කූඩය | |
| product | නිෂ්පාදනය | |
| customer | පාරිභෝගිකයා | |
| discount | වට්ටම | |
| VAT | වැට් | |
| price / currency | මිල / මුදල් ඒකකය | |
| tag | ටැගය | an HTML tag: HTML ටැගය |
| translation | පරිවර්තනය | |
| language | භාෂාව | main language: ප්‍රධාන භාෂාව |
| locale | ප්‍රාදේශීය සැකසුම | |
| state | තත්ත්වය | the object state |
| status | තත්ත්වය | |
| view (button) | බලන්න | a module view (noun): දසුන |
| format | ආකෘතිය | |
| sort | වර්ග කරන්න | |
| filter | පෙරහන | to filter: පෙරන්න |
| select | තෝරන්න | select all: සියල්ල තෝරන්න; selected: තෝරාගත් |
| copy / move | පිටපත් කරන්න / ගෙන යන්න | |
| add / create / new | එක් කරන්න / සාදන්න / නව | |
| restore | ප්‍රතිසාධනය කරන්න | |
| install / uninstall | ස්ථාපනය කරන්න / අස්ථාපනය කරන්න | |
| related objects | සම්බන්ධිත වස්තු | relation: සම්බන්ධතාව |
| dashboard | උපකරණ පුවරුව | |
| identifier / ID | හඳුන්වනය / ID | |
| priority | ප්‍රමුඛතාව | |
| owner | හිමිකරු | |
| modified / created | වෙනස් කළ / සාදන ලද | |
| date / time | දිනය / වේලාව | |
| error / warning | දෝෂය / අවවාදය | |
| help | උදව් | |
| notification | දැනුම්දීම | |
| account | ගිණුම | |
| default | පෙරනිමි | |
| link | සබැඳිය | |
| none / all | කිසිවක් නැත / සියල්ල | |
| yes / no | ඔව් / නැත | |
| name / title / description | නම / මාතෘකාව / විස්තරය | |
| value / type / list | අගය / වර්ගය / ලැයිස්තුව | |
| page / path | පිටුව / මාර්ගය | |
| required / optional | අවශ්‍ය / විකල්ප | |
| options | විකල්ප | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is වෙළඳසැල, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is සුරකින්න.
- "Login" in the user, session and audit lists is the user's login name (පිවිසුම් නාමය); the Login button of the
  error page is the act (පිවිසෙන්න).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
