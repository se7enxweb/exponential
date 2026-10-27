# Khmer (khm-KH): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Khmer (ភាសាខ្មែរ) as used in Cambodia, in the **Khmer script**, in the neutral, polite register of
Cambodian government portals, the Khmer localisations of Windows, Android and Firefox (km) and Khmer Wikipedia. No
other locale falls back to `khm-KH`.

- Word order is subject - verb - object, modifiers after the noun (ឯកសារថ្មី, new file). Use the vocabulary of the
  Khmer software localisations; avoid royal or religious registers.
- Technical words: a Khmer word where one is established (ឯកសារ, អ្នកប្រើ, ពាក្យសម្ងាត់, ការកំណត់, ស្វែងរក,
  មូលដ្ឋានទិន្នន័យ); an English or French loan written in Khmer script where Khmer IT writing uses it (ម៉ាស៊ីនមេ for
  server, ស្គ្រីប, ម៉ូឌុល). Never leave the English word in Latin letters unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is addressed politely and impersonally; where a pronoun is needed, **អ្នក** (you).
- **Buttons, menu items and links**: the short verb, as Khmer interfaces do: រក្សាទុក (save), បោះបង់ (cancel), លុប
  (delete), ដកចេញ (remove), កែសម្រួល (edit), បន្ថែម (add), បង្កើត (create), ស្វែងរក (search), ជ្រើសរើស (select),
  ផ្ញើ (send), មើល (view), បោះពុម្ពផ្សាយ (publish), ផ្ទុកឡើង (upload), ទាញយក (download).
- **Titles, column headings and tabs**: nouns or noun phrases, often with ការ- or សេចក្តី-: ការកំណត់, ការបកប្រែ,
  ទីតាំងចម្បង, ឈ្មោះ.
- **Instructions**: the verb with polite សូម ("please") at the start of an instruction sentence: "សូមជ្រើសរើសធាតុ
  ហើយចុច \"OK\"។".
- **Confirmations and questions**: "តើអ្នកប្រាកដថាចង់ ... ទេ?": "តើអ្នកប្រាកដថាចង់លុបផ្នែកនេះទេ?".
- **What the user can or may do**: "អ្នកអាច ...", "អ្នកមិនអាច ...", "You do not have permission" is "អ្នកមិនមាន
  សិទ្ធិ ...".
- **Messages about a result**: "... ត្រូវបាន ... ហើយ": "សេចក្តីព្រាងត្រូវបានរក្សាទុក។", "ឯកសារត្រូវបានផ្ទុកឡើងដោយ
  ជោគជ័យ។".

## 3. Script and typography

- **Khmer script** (U+1780-U+17FF). Words in a phrase are written together; a space separates phrases and clauses,
  as Khmer text does, and always stands before and after Latin words, numbers and placeholders.
- Khmer has no capital letters; Latin product names keep theirs.
- **Full stop**: the sentence ends with ។ (khan) where the English ends with a full stop "."; the question mark ?,
  colon : and ellipsis ... stay as in the source. A row whose source has no final full stop gets none.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and always stand free, with a space
  before and after them or a punctuation mark right after. Khmer has no case endings, so a placeholder is simply
  placed in the sentence. The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (the placeholders carry numbers; written digits stay Western, as in software).

## 4. Glossary

Binding for the kernel and every extension.

| English | Khmer | Note |
|---|---|---|
| content | មាតិកា | content object: វត្ថុមាតិកា |
| object | វត្ថុ | |
| node | ថ្នាំង | main node: ថ្នាំងចម្បង; parent node: ថ្នាំងមេ |
| location | ទីតាំង | main location: ទីតាំងចម្បង |
| item (list entry) | ធាតុ | |
| class | ថ្នាក់ | the content class; class group: ក្រុមថ្នាក់ |
| attribute | លក្ខណៈ | |
| datatype | ប្រភេទទិន្នន័យ | |
| version | កំណែ | version history: ប្រវត្តិកំណែ |
| draft | សេចក្តីព្រាង | store draft: រក្សាទុកសេចក្តីព្រាង |
| publish | បោះពុម្ពផ្សាយ | published: បានបោះពុម្ពផ្សាយ |
| send for publishing | ផ្ញើដើម្បីបោះពុម្ពផ្សាយ | |
| discard | បោះចោល | |
| archived / pending / rejected | បានទុកក្នុងបណ្ណសារ / កំពុងរង់ចាំ / បានបដិសេធ | |
| siteaccess | ការចូលគេហទំព័រ | siteaccess as a setting name stays in code |
| site, website | គេហទំព័រ | |
| section | ផ្នែក | the Exponential section; an INI section: ផ្នែក INI |
| role | តួនាទី | |
| policy | គោលការណ៍ | |
| limitation | ការកំណត់ព្រំដែន | |
| permission | សិទ្ធិ | access denied: ការចូលត្រូវបានបដិសេធ |
| module | ម៉ូឌុល | |
| function | មុខងារ | |
| user / user group | អ្នកប្រើ / ក្រុមអ្នកប្រើ | |
| group | ក្រុម | |
| anonymous | អនាមិក | anonymous user: អ្នកប្រើអនាមិក |
| administrator | អ្នកគ្រប់គ្រង | administration (interface): ការគ្រប់គ្រង |
| username | ឈ្មោះអ្នកប្រើ | login (the name): ឈ្មោះចូល |
| password | ពាក្យសម្ងាត់ | |
| log in / log out | ចូល / ចាកចេញ | the act: ការចូល / ការចាកចេញ |
| e-mail | អ៊ីមែល | e-mail address: អាសយដ្ឋានអ៊ីមែល |
| cache | ឃ្លាំងសម្ងាត់ | clear cache: សម្អាតឃ្លាំងសម្ងាត់ |
| template | គំរូ | override template: គំរូជំនួស |
| override | ការជំនួស | |
| fallback | ជម្រើសបម្រុង | fallback language: ភាសាបម្រុង |
| design | រចនា | |
| workflow | លំហូរការងារ | |
| trigger | កត្តាបង្ក | |
| event | ព្រឹត្តិការណ៍ | |
| trash | ធុងសំរាម | empty trash: សម្អាតធុងសំរាម; move to trash: ផ្លាស់ទីទៅធុងសំរាម |
| bookmark | ចំណាំ | |
| settings | ការកំណត់ | configuration: ការកំណត់រចនាសម្ព័ន្ធ |
| setup | ការរៀបចំ | the Setup tab |
| layout | ប្លង់ | Exponential Layouts stays |
| block / zone | ប្លុក / តំបន់ | |
| extension | ផ្នែកបន្ថែម | |
| package | កញ្ចប់ | |
| search | ការស្វែងរក | to search: ស្វែងរក; search index: លិបិក្រមស្វែងរក |
| log | កំណត់ហេតុ | a log file: ឯកសារកំណត់ហេតុ; never "log in" |
| queue | ជួរ | job queue: ជួរការងារ |
| job / task | ការងារ | |
| cron | cron | |
| script | ស្គ្រីប | |
| kernel, core | ស្នូល | |
| server | ម៉ាស៊ីនមេ | |
| database | មូលដ្ឋានទិន្នន័យ | |
| folder / directory | ថត | |
| file | ឯកសារ | |
| image | រូបភាព | thumbnail: រូបភាពតូច |
| preview | មើលជាមុន | |
| upload / download | ផ្ទុកឡើង / ទាញយក | |
| export / import | នាំចេញ / នាំចូល | |
| remove | ដកចេញ | take out of a list or place |
| delete | លុប | destroy; deleted: បានលុប |
| edit | កែសម្រួល | |
| editor | កម្មវិធីកែសម្រួល / អ្នកកែសម្រួល | software / person |
| save / store | រក្សាទុក | the verb "Store" of the forms |
| Store (shop tab) | ហាង | the shop, as "Shop" |
| cancel | បោះបង់ | |
| OK | យល់ព្រម | |
| apply | អនុវត្ត | |
| back | ត្រឡប់ក្រោយ | |
| next / previous | បន្ទាប់ / មុន | |
| enable / disable | បើក / បិទ | enabled / disabled: បានបើក / បានបិទ |
| active / inactive | សកម្ម / អសកម្ម | |
| run | ដំណើរការ | |
| start / stop | ចាប់ផ្តើម / បញ្ឈប់ | |
| session | វគ្គ | |
| hidden / visible | លាក់ / មើលឃើញ | hide / reveal: លាក់ / បង្ហាញ |
| archive | បណ្ណសារ | |
| order (sort) | លំដាប់ | sort order: លំដាប់តម្រៀប |
| order (shop) | ការបញ្ជាទិញ | |
| shop | ហាង | |
| basket | កន្ត្រក | |
| product | ផលិតផល | |
| customer | អតិថិជន | |
| discount | ការបញ្ចុះតម្លៃ | |
| VAT | អាករលើតម្លៃបន្ថែម | |
| price / currency | តម្លៃ / រូបិយប័ណ្ណ | |
| tag | ស្លាក | an HTML tag: ស្លាក HTML |
| translation | ការបកប្រែ | |
| language | ភាសា | main language: ភាសាចម្បង |
| locale | មូលដ្ឋានភាសា | |
| state | ស្ថានភាព | the object state |
| status | ស្ថានភាព | |
| view (button) | មើល | a module view (noun): ទិដ្ឋភាព |
| format | ទម្រង់ | |
| sort | តម្រៀប | |
| filter | តម្រង | to filter: ត្រង |
| select | ជ្រើសរើស | select all: ជ្រើសរើសទាំងអស់ |
| copy / move | ចម្លង / ផ្លាស់ទី | |
| add / create / new | បន្ថែម / បង្កើត / ថ្មី | |
| restore | ស្តារ | |
| install / uninstall | ដំឡើង / លុបការដំឡើង | |
| related objects | វត្ថុពាក់ព័ន្ធ | relation: ទំនាក់ទំនង |
| dashboard | ផ្ទាំងគ្រប់គ្រង | |
| identifier / ID | អត្តសញ្ញាណ / ID | |
| priority | អាទិភាព | |
| owner | ម្ចាស់ | |
| modified / created | បានកែប្រែ / បានបង្កើត | |
| date / time | កាលបរិច្ឆេទ / ពេលវេលា | |
| error / warning | កំហុស / ការព្រមាន | |
| help | ជំនួយ | |
| notification | ការជូនដំណឹង | |
| account | គណនី | |
| default | លំនាំដើម | |
| link | តំណ | |
| none / all | គ្មាន / ទាំងអស់ | |
| yes / no | បាទ/ចាស / ទេ | yes: ព្រម in buttons |
| name / title / description | ឈ្មោះ / ចំណងជើង / ការពិពណ៌នា | |
| value / type / list | តម្លៃ / ប្រភេទ / បញ្ជី | |
| page / path | ទំព័រ / ផ្លូវ | |
| required / optional | ចាំបាច់ / ស្រេចចិត្ត | |
| options | ជម្រើស | |
| alias (URL alias, image alias) | ឈ្មោះក្លែង | URL alias: ឈ្មោះក្លែង URL |
| wizard | អ្នកជំនួយការ | |
| tree / subtree | មែកធាង / មែកធាងរង | |
| feed (RSS, Atom) | មតិព័ត៌មាន | |
| worker (process) | កម្មករ | |
| colon | : | the Latin colon as in the source, never ៖ |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ហាង, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  រក្សាទុក.
- "Login" in the user, session and audit lists is the user's login name (ឈ្មោះចូល); the Login button of the error
  page is the act (ចូល).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
