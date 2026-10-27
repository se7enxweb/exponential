# Western Punjabi (pnb-PK, Shahmukhi): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted: the Punjabi catalogue in Gurmukhi (`pan-IN`) was transliterated into Shahmukhi
letter by letter and then adapted to the spelling and vocabulary of Punjabi as it is written in Pakistan, with the
Urdu catalogue (`urd-PK`) as the reference for Perso-Arabic and technical words. A review by native speakers is
welcome.

## 1. Language and register

Punjabi (پنجابی) as written in Pakistani Punjab, in the Shahmukhi script, right to left, in the plain, polite
register of Punjabi newspapers, the Punjabi Wikipedia (pnb) and literature. No other locale falls back to `pnb-PK`.

- **Grammar and everyday words are Punjabi**, not Urdu: اے / نیں / سی / سن (is, are, was, were)، نہیں، کیتا گیا،
  ہووے گا، سکدے او، دا / دی / دے / دیاں (genitive)، نوں، وچ، توں، اُتے / تے، لئی، تے / اتے (and)، یا (or)، پر
  (but)، جے ... تے (if ... then)، ایہہ (this)، اوہ (that)، سارے، کجھ، جیہڑا، کوئی. Never leave an Urdu sentence:
  "کیا آپ واقعی اس کلاس کو حذف کرنا چاہتے ہیں؟" becomes "کیہ تسیں سچی ایس کلاس نوں مٹاݨا چاہندے او؟".
- **Vocabulary**: Pakistani Punjabi uses Perso-Arabic words where Indian Punjabi in Gurmukhi uses Sanskrit-based
  ones. Replace the Sanskrit-based words of the Gurmukhi catalogue by the word used in Pakistan (often the Urdu one in
  Punjabi form): ਸਮੱਗਰੀ → مواد، ਸੂਚੀ → لسٹ، ਸਿਰਲੇਖ → سرناواں، ਜਨਤਕ → عوامی، ਆਕਾਰ → سائز، ਅਨੁਮਤੀ → اجازت، ਸਮਾਂ →
  ویلا، ਭਾਸ਼ਾ → بولی، ਤਰੁੱਟੀ → غلطی، ਖੋਜ → لبھت. English loan words as Pakistani Punjabi writes them (فائل، سرور،
  ڈیٹابیس، ٹیمپلیٹ، لاگ ان).
- Short, plain sentences.

## 2. Form of address

- The user is **تسیں**, with the plural-polite verb forms (تسیں ... سکدے او، تسیں ... چاہندے او). Never توں.
- **Buttons, menu items and links**: the polite imperative in **-و / کرو**: محفوظ کرو، رد کرو، مٹاؤ (delete)، ہٹاؤ
  (remove)، سودھو (edit)، جوڑو، بݨاؤ، لبھو، چݨو، گھلو، ویکھو، شائع کرو، اپلوڈ کرو.
- **Titles, column headings and tabs**: nouns or noun phrases: سیٹنگاں، ترجمے، مکھ تھاں، ناں.
- **Instructions**: the same polite imperative, with "مہربانی کر کے" where English has "please": "آئٹم چݨو تے
  \"OK\" دباؤ۔", "مہربانی کر کے ترجیح محفوظ ہوݨ تیکر اڈیکو۔". A reference ("see ...") is "... ویکھو".
- **Confirmations and questions**: "کیہ تسیں سچی ... چاہندے او؟".
- **What the user can or may do**: "تسیں ... سکدے او"، "تسیں ... نہیں سکدے"، or impersonal: "مکھ بولی بدلی نہیں جا
  سکدی ...". "You do not have permission" is "تہاڈے کول ... دی اجازت نہیں".
- **Messages about a result**: "... ہو گیا" / "... کیتا گیا": "مسودہ محفوظ ہو گیا۔", "فائل کامیابی نال اپلوڈ ہو
  گئی۔".

## 3. Script and typography

- **Shahmukhi** (Perso-Arabic, Nastaliq-style letters of Urdu plus ݨ): ݨ (U+0768) for the retroflex n, ڑ ٹ ڈ, ں for
  the final nasal, ے for the final e, ی for i, ہ / ھ as Urdu writes them (ھ only in the aspirates: بھ، کھ، دھ). Short
  vowels are not written, except a zer / pesh where a word would otherwise be misread. The transliteration draft
  spells loan words by their Gurmukhi sound: correct them to their usual Shahmukhi / Urdu spelling (ایکسپورٹ → برآمد
  where Pakistani usage has it, پھائل → فائل، سمگری → مواد).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`), code, file names, INI settings and product names
  stay in Latin letters, unchanged, and stand free: a space before and after them, or a punctuation mark right after:
  "%name وچ"، "%count شیواں". The interface lays them out right to left; do not add direction marks.
- **Punctuation**: the Urdu full stop "۔", the Arabic comma "،", question mark "؟" and semicolon "؛". A source ending
  in ":" or "..." keeps that mark.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Western Punjabi | Note |
|---|---|---|
| content | مواد | |
| content object / object | مواد آبجیکٹ / آبجیکٹ | |
| item (list entry) | آئٹم | sub items: ذیلی آئٹم |
| node | نوڈ | main node: مکھ نوڈ; parent node: پیرنٹ نوڈ |
| location | تھاں | main location: مکھ تھاں |
| class / content class | کلاس / مواد کلاس | class group: کلاس گروپ |
| attribute | خاصیت | |
| datatype | ڈیٹا دی قسم | |
| version | ورژن | version history: ورژن دی تریخ |
| draft | مسودہ | store draft: مسودہ محفوظ کرو |
| publish | شائع کرو | published: شائع شدہ; publishing: اشاعت |
| send for publishing | اشاعت لئی گھلو | |
| discard | چھڈو | discard draft: مسودہ چھڈو |
| archived / pending / rejected | آرکائیو شدہ / زیر التوا / رد شدہ | |
| siteaccess | سائٹ رسائی | siteaccess as a setting name stays in code |
| site, website | سائٹ / ویب سائٹ | |
| section | سیکشن | the Exponential section; an INI section: حصہ |
| role | کردار | |
| policy | پالیسی | one rule of a role |
| limitation | حد | |
| permission | اجازت | access denied: رسائی رد |
| access | رسائی | |
| module | ماڈیول | |
| function | فنکشن | |
| user / user group | ورتݨ والا / ورتݨ والیاں دا گروپ | plural: ورتݨ والے |
| group | گروپ | |
| anonymous | گمنام | |
| administrator | منتظم | administration (interface): انتظامیہ |
| username | ورتݨ والے دا ناں | login (the name): لاگ ان ناں |
| password | پاس ورڈ | |
| log in / log out | لاگ ان کرو / لاگ آؤٹ کرو | |
| e-mail | ای میل | e-mail address: ای میل پتہ |
| cache | کیش | clear cache: کیش صاف کرو |
| template | ٹیمپلیٹ | override template: اووررائیڈ ٹیمپلیٹ |
| override | اووررائیڈ | |
| fallback | متبادل | fallback language: متبادل بولی |
| design | ڈیزائن | |
| workflow | ورک فلو | |
| trigger | ٹرگر | |
| event | واقعہ | |
| trash | کوڑےدان | empty trash: کوڑےدان خالی کرو; move to trash: کوڑےدان وچ گھلو |
| bookmark | بک مارک | |
| settings | سیٹنگاں | a setting: سیٹنگ; configuration: ترتیب |
| setup | سیٹ اپ | the Setup tab |
| layout | لے آؤٹ | Exponential Layouts |
| block / zone | بلاک / زون | |
| extension | ایکسٹینشن | |
| package | پیکج | |
| search | لبھت | to search: لبھو; search index: لبھت انڈیکس |
| log | لاگ | a log file: لاگ فائل; never "log in" |
| queue | قطار | job queue: کماں دی قطار |
| job / task | کم | |
| cron | کرون | |
| script | سکرپٹ | |
| worker | ورکر | |
| kernel, core | کرنل | |
| server | سرور | |
| database | ڈیٹابیس | |
| folder / directory | فولڈر / ڈائریکٹری | |
| file | فائل | |
| image | تصویر | thumbnail: نکی تصویر |
| preview | پیش منظر | |
| upload / download | اپلوڈ / ڈاؤن لوڈ | verbs: اپلوڈ کرو / ڈاؤن لوڈ کرو |
| export / import | برآمد / درآمد | |
| remove | ہٹاؤ | take out of a list or place |
| delete | مٹاؤ | destroy; deleted: مٹا دتا گیا |
| edit | سودھو | editing: سودھائی |
| editor | ایڈیٹر | |
| save / store | محفوظ کرو | the verb "Store" of the forms |
| Store (shop tab) | ہٹی | the shop, as "Shop" |
| cancel | رد کرو | |
| OK | ٹھیک اے | |
| apply | لاگو کرو | |
| back | پچھے | |
| next / previous | اگلا / پچھلا | |
| enable / disable | چالو کرو / بند کرو | enabled / disabled: چالو / بند |
| active / inactive | سرگرم / غیر سرگرم | |
| run | چلاؤ | |
| start / stop | شروع کرو / روکو | |
| session | سیشن | |
| hidden / visible | لکیا / دسدا | hide / reveal: لکاؤ / وکھاؤ |
| archive | آرکائیو | |
| order (sort) | ترتیب | |
| order (shop) | آرڈر | |
| shop | ہٹی | |
| basket | ٹوکری | |
| product | پراڈکٹ | |
| customer | گاہک | |
| discount | رعایت | |
| VAT | وی اے ٹی | |
| price / currency | قیمت / کرنسی | |
| tag | ٹیگ | an HTML tag: HTML ٹیگ |
| translation | ترجمہ | plural: ترجمے |
| language | بولی | main language: مکھ بولی |
| locale | لوکیل | |
| state | حالت | the object state; state group: حالت گروپ |
| status | سٹیٹس | kept apart from حالت |
| view (button) | ویکھو | a module view (noun): منظر |
| format | فارمیٹ | |
| sort | ترتیب دیو | |
| filter | فلٹر | |
| select | چݨو | select all: سارے چݨو; selected: چݨے ہوئے |
| browse | براؤز کرو | |
| copy / move | نقل کرو / منتقل کرو | |
| add / create / new | جوڑو / بݨاؤ / نواں | |
| restore | بحال کرو | |
| install / uninstall | انسٹال کرو / ان انسٹال کرو | |
| subtree | ذیلی رکھ | |
| related objects | جڑے آبجیکٹ | relation: تعلق |
| URL alias | URL عرف | |
| redirect | ری ڈائریکٹ | |
| dashboard | ڈیش بورڈ | |
| content structure | مواد دی بݨتر | |
| media library | میڈیا لائبریری | |
| identifier / ID | شناخت کار / ID | |
| priority | ترجیح | |
| owner | مالک | |
| modified / created | بدلیا / بݨایا | |
| date / time | تریخ / ویلا | |
| error / warning | غلطی / چتاونی | |
| help | مدد | |
| notification | اطلاع | |
| wizard | وزرڈ | |
| feed (RSS) | فیڈ | |
| tab | ٹیب | |
| account | کھاتہ | |
| default | ڈیفالٹ | |
| toolbar | ٹول بار | |
| container | کنٹینر | |
| collection | مجموعہ | |
| approve, approval | منظور کرو، منظوری | |
| request / query | درخواست / کوئری | |
| backup | بیک اپ | |
| maintenance | سانبھ سنبھال | |
| link | لنک | |
| registration | رجسٹریشن | |
| device / browser | ڈیوائس / براؤزر | |
| none / all | کوئی نہیں / سارے | |
| yes / no | ہاں / نہیں | |
| name / title / description | ناں / سرناواں / ویروا | |
| value / type / list | مُل / قسم / لسٹ | |
| page / path | صفحہ / راہ | |
| required / optional | لازمی / اختیاری | |
| options | چوݨاں | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is ہٹی, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is محفوظ کرو.
- "Login" in the user, session and audit lists is the user's login name (لاگ ان ناں); the Login button of the error
  page is the act (لاگ ان کرو).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
