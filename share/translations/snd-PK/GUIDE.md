# Sindhi (snd-PK): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted and adapted from the Urdu catalogue (`urd-PK`) where the Perso-Arabic and technical
terms are shared; a review by native speakers is welcome.

## 1. Language and register

Standard Sindhi (سنڌي) as written in Sindh, Pakistan, in the Perso-Arabic script, in the polite, plain register of
Sindhi newspapers, the Sindhi Language Authority and government websites. Written right to left. No other locale
falls back to `snd-PK`.

- Sindhi is not Urdu: it has its own verbs, postpositions, pronouns and endings, and its own letters. Use them
  throughout: آهي / آهن / هو / هئا، ناهي / ڪونهي، ڪيو ويو، ٿيندو، سگهو ٿا، جو / جي / جا (genitive)، کي، ۾، مان /
  کان، تي، لاءِ، ۽ (and)، يا (or)، پر (but)، جيڪڏهن ... ته (if ... then)، هي / هيءَ (this)، اهو / اها (that)،
  سڀ، ڪجهه، جيڪو، ڪوبه. Adapting an Urdu sentence means rewriting it: "کیا آپ واقعی اس کلاس کو حذف کرنا چاہتے ہیں؟"
  becomes "ڇا توهان واقعي هن ڪلاس کي ختم ڪرڻ چاهيو ٿا؟".
- Perso-Arabic words shared with Urdu (اجازت، ترتيب، نتيجو) take their Sindhi form and spelling; English technical
  loan words are written as Sindhi writes them (فائل، سرور، ڊيٽابيس، ٽيمپليٽ).
- Short, plain sentences.

## 2. Form of address

- The user is **توهان**, with the plural-polite verb forms (توهان ... سگهو ٿا، توهان ... چاهيو ٿا). Never تون.
- **Buttons, menu items and links**: the polite imperative in **-يو / ڪريو**: محفوظ ڪريو، رد ڪريو، ختم ڪريو
  (delete)، هٽايو (remove)، تبديل ڪريو (edit)، شامل ڪريو، ٺاهيو، ڳوليو، چونڊيو، موڪليو، ڏسو، شايع ڪريو، اپلوڊ ڪريو.
- **Titles, column headings and tabs**: nouns or noun phrases: سيٽنگون، ترجما، مکيه جڳهه، نالو.
- **Instructions**: the same polite imperative, with "مهرباني ڪري" where English has "please": "آئٽم چونڊيو ۽
  \"OK\" دٻايو.", "مهرباني ڪري ترجيح محفوظ ٿيڻ تائين انتظار ڪريو.". A reference ("see ...") is "... ڏسو".
- **Confirmations and questions**: "ڇا توهان واقعي ... چاهيو ٿا؟".
- **What the user can or may do**: "توهان ... سگهو ٿا"، "توهان ... نٿا سگهو"، or impersonal: "مکيه ٻولي تبديل نٿي
  ڪري سگهجي ...". "You do not have permission" is "توهان وٽ ... جي اجازت ناهي".
- **Messages about a result**: "... ٿي ويو" / "... ڪيو ويو": "مسودو محفوظ ٿي ويو.", "فائل ڪاميابي سان اپلوڊ ٿي
  وئي.".

## 3. Script and typography

- **Sindhi Perso-Arabic script** (52 letters), Unicode: ڪ for k (never ک), ک for kh, گ ڳ ڱ, ٻ ڀ ٽ ٺ ٿ ڄ ڃ ڇ ڊ ڌ ڍ ڏ
  ڙ ڦ ڻ, ه / ھ as the Sindhi Language Authority writes them, ي (not ی) for the final y, ءِ for the izafat-like
  ending (لاءِ). Not the Devanagari or Khudabadi script.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`), code, file names, INI settings and product names
  stay in Latin letters, unchanged, and stand free: a space before and after them, or a punctuation mark right after:
  "%name ۾"، "%count شيون". The interface lays them out right to left; do not add direction marks.
- **Punctuation**: the Arabic comma "،", the Arabic question mark "؟" and semicolon "؛"; the full stop is "." as in
  the Urdu catalogue (or "۔" consistently - use "." everywhere). A source ending in ":" or "..." keeps that mark.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9, so that numbers from placeholders and written numbers look the same.

## 4. Glossary

Binding for the kernel and every extension.

| English | Sindhi | Note |
|---|---|---|
| content | مواد | |
| content object / object | مواد جي شيءِ / شيءِ | plural: شيون |
| item (list entry) | آئٽم | sub items: ذيلي آئٽم |
| node | نوڊ | main node: مکيه نوڊ; parent node: والدين نوڊ |
| location | جڳهه | main location: مکيه جڳهه |
| class / content class | ڪلاس / مواد جو ڪلاس | class group: ڪلاس گروپ |
| attribute | خاصيت | |
| datatype | ڊيٽا جو قسم | |
| version | ورزن | version history: ورزن جي تاريخ |
| draft | مسودو | store draft: مسودو محفوظ ڪريو |
| publish | شايع ڪريو | published: شايع ٿيل; publishing: اشاعت |
| send for publishing | اشاعت لاءِ موڪليو | |
| discard | ڇڏي ڏيو | discard draft: مسودو ڇڏي ڏيو |
| archived / pending / rejected | آرڪائيو ٿيل / التوا ۾ / رد ٿيل | |
| siteaccess | سائيٽ رسائي | siteaccess as a setting name stays in code |
| site, website | سائيٽ / ويب سائيٽ | |
| section | سيڪشن | the Exponential section; an INI section: حصو |
| role | ڪردار | |
| policy | پاليسي | one rule of a role |
| limitation | حد | |
| permission | اجازت | access denied: رسائي رد ٿي وئي |
| access | رسائي | |
| module | ماڊيول | |
| function | فنڪشن | |
| user / user group | استعمال ڪندڙ / استعمال ڪندڙن جو گروپ | |
| group | گروپ | |
| anonymous | گمنام | |
| administrator | منتظم | administration (interface): انتظام |
| username | استعمال ڪندڙ جو نالو | login (the name): لاگ ان نالو |
| password | پاسورڊ | |
| log in / log out | لاگ ان ڪريو / لاگ آئوٽ ڪريو | |
| e-mail | اي ميل | e-mail address: اي ميل پتو |
| cache | ڪيش | clear cache: ڪيش صاف ڪريو |
| template | ٽيمپليٽ | override template: اوور رائيڊ ٽيمپليٽ |
| override | اوور رائيڊ | |
| fallback | متبادل | fallback language: متبادل ٻولي |
| design | ڊيزائن | |
| workflow | ورڪ فلو | |
| trigger | ٽرگر | |
| event | واقعو | |
| trash | ڪچرادان | empty trash: ڪچرادان خالي ڪريو; move to trash: ڪچرادان ۾ موڪليو |
| bookmark | بُڪ مارڪ | |
| settings | سيٽنگون | a setting: سيٽنگ; configuration: ترتيب |
| setup | سيٽ اپ | the Setup tab |
| layout | لي آئوٽ | Exponential Layouts |
| block / zone | بلاڪ / زون | |
| extension | ايڪسٽينشن | |
| package | پيڪيج | |
| search | ڳولا | to search: ڳوليو; search index: ڳولا انڊيڪس |
| log | لاگ | a log file: لاگ فائل; never "log in" |
| queue | قطار | job queue: ڪمن جي قطار |
| job / task | ڪم | |
| cron | ڪرون | |
| script | اسڪرپٽ | |
| worker | ورڪر | |
| kernel, core | ڪرنل | |
| server | سرور | |
| database | ڊيٽابيس | |
| folder / directory | فولڊر / ڊائريڪٽري | |
| file | فائل | |
| image | تصوير | thumbnail: ننڍي تصوير |
| preview | پيش منظر | |
| upload / download | اپلوڊ / ڊائونلوڊ | verbs: اپلوڊ ڪريو / ڊائونلوڊ ڪريو |
| export / import | برآمد / درآمد | |
| remove | هٽايو | take out of a list or place |
| delete | ختم ڪريو | destroy; deleted: ختم ڪيو ويو |
| edit | تبديل ڪريو | editing: ترميم |
| editor | ايڊيٽر | |
| save / store | محفوظ ڪريو | the verb "Store" of the forms |
| Store (shop tab) | دڪان | the shop, as "Shop" |
| cancel | رد ڪريو | |
| OK | ٺيڪ | |
| apply | لاڳو ڪريو | |
| back | واپس | |
| next / previous | اڳيون / پويون | |
| enable / disable | فعال ڪريو / غير فعال ڪريو | enabled / disabled: فعال / غير فعال |
| active / inactive | سرگرم / غير سرگرم | |
| run | هلايو | |
| start / stop | شروع ڪريو / روڪيو | |
| session | سيشن | |
| hidden / visible | لڪيل / ظاهر | hide / reveal: لڪايو / ڏيکاريو |
| archive | آرڪائيو | |
| order (sort) | ترتيب | |
| order (shop) | آرڊر | |
| shop | دڪان | |
| basket | ٽوڪري | |
| product | پراڊڪٽ | |
| customer | گراهڪ | |
| discount | رعايت | |
| VAT | وي اي ٽي | |
| price / currency | قيمت / ڪرنسي | |
| tag | ٽيگ | an HTML tag: HTML ٽيگ |
| translation | ترجمو | plural: ترجما |
| language | ٻولي | main language: مکيه ٻولي |
| locale | لوڪيل | |
| state | حالت | the object state; state group: حالت گروپ |
| status | اسٽيٽس | kept apart from حالت |
| view (button) | ڏسو | a module view (noun): منظر |
| format | فارميٽ | |
| sort | ترتيب ڏيو | |
| filter | فلٽر | |
| select | چونڊيو | select all: سڀ چونڊيو; selected: چونڊيل |
| browse | براؤز ڪريو | |
| copy / move | نقل ڪريو / منتقل ڪريو | |
| add / create / new | شامل ڪريو / ٺاهيو / نئون | |
| restore | بحال ڪريو | |
| install / uninstall | انسٽال ڪريو / ان انسٽال ڪريو | |
| subtree | ذيلي وڻ | |
| related objects | لاڳاپيل شيون | relation: لاڳاپو |
| URL alias | URL عرف | |
| redirect | ري ڊائريڪٽ | |
| dashboard | ڊيش بورڊ | |
| content structure | مواد جي بناوت | |
| media library | ميڊيا لائبريري | |
| identifier / ID | سڃاڻپ ڪندڙ / ID | |
| priority | ترجيح | |
| owner | مالڪ | |
| modified / created | تبديل ٿيل / ٺاهيل | |
| date / time | تاريخ / وقت | |
| error / warning | غلطي / خبرداري | |
| help | مدد | |
| notification | اطلاع | |
| wizard | وزرڊ | |
| feed (RSS) | فيڊ | |
| tab | ٽيب | |
| account | اڪائونٽ | |
| default | ڊفالٽ | |
| toolbar | ٽول بار | |
| container | ڪنٽينر | |
| collection | مجموعو | |
| approve, approval | منظور ڪريو، منظوري | |
| request / query | گذارش / ڪوئري | |
| backup | بيڪ اپ | |
| maintenance | سار سنڀال | |
| link | لنڪ | |
| registration | رجسٽريشن | |
| device / browser | ڊوائيس / براؤزر | |
| none / all | ڪوبه نه / سڀ | |
| yes / no | ها / نه | |
| name / title / description | نالو / عنوان / وضاحت | |
| value / type / list | قدر / قسم / فهرست | |
| page / path | صفحو / رستو | |
| required / optional | لازمي / اختياري | |
| options | اختيار | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is دڪان, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is محفوظ ڪريو.
- "Login" in the user, session and audit lists is the user's login name (لاگ ان نالو); the Login button of the error
  page is the act (لاگ ان ڪريو).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
