# Pashto (pus-AF): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Pashto of Afghanistan, in the Kabul written norm used by the major software vendors and the large Pashto
news services. The tone is factual and easy to follow. Where Pashto has no settled technical word, take the
loanword that is usual in Afghanistan (often from Persian or English) rather than coin a new one. A long text may
add the English term in parentheses at its first mention (`مهالوېشلې دنده (cronjob)`); a label never does.

No other locale reads this catalogue.

## 2. Form of address

The user is addressed with the formal **تاسو**, everywhere and without exception.

- **Buttons, menu items, links, tabs, column headings and titles** use the infinitive or a noun, with no address:
  خوندي کول (Save), ړنګول (Delete), لرې کول (Remove), سمول (Edit), لغوه کول (Cancel), پلي کول (Apply),
  خپرول (Publish), پورته کول (Upload), ټول ټاکل (Select all), مسوده پرېښودل (Discard draft), نښو ته زیاتول
  (Add to bookmarks).
- **Sentences, hints, questions and error messages** use the second person plural in ـئ:
  - ایا تاسو ډاډه یاست چې غواړئ دغه توکي لرې کړئ؟ (Are you sure you want to remove these items?)
  - مهرباني وکړئ یو ځای وټاکئ (Please choose a location)
  - تاسو د کلاسونو د لرې کولو اجازه نه لرئ. (You do not have permission to remove classes.)
  - هغه دوتنه وټاکئ چې غواړئ یې پورته کړئ. (Select the file that you want to upload.)
- Possessive: ستاسو, or خپل (خپل پټنوم ولیکئ).
- "Please" is مهرباني وکړئ, followed by the plural imperative.
- **Not allowed**: the singular ته as the pronoun "you", ستا, verb forms in ـې (غواړې) and the singular
  imperative (کړه). The postposition ته ("to": ډشبورډ ته) and the demonstrative دې ("this": دې پاڼې ته) are
  ordinary Pashto and are used normally.

## 3. Script and typography

- The Pashto alphabet with ټ ډ ړ ږ ښ ګ ڼ ې ۍ ئ. **ک** (U+06A9), never Arabic ك (U+0643); **ګ** (U+06AB) for g, never
  گ. The five ya forms are each used where they belong: ی (U+06CC), ي (U+064A, plural and verb endings such as
  کوي), ې (U+06D0), ۍ (U+06CD), ئ (U+0626).
- The zero-width non-joiner (U+200C) in compounds that are written apart: کارن‌نوم، کثافت‌دانۍ، ځای‌نیونکی.
- Punctuation: ، (comma), ؛ (semicolon), ؟ (question mark); full stop and colon as in English. Quotation marks
  « » or "" as in the source.
- Latin terms (Solr, URL, PHP, INI keys) are embedded as they are.
- Numbers: Western digits 0–9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Pashto | Note |
|---|---|---|
| content | منځپانګه | |
| content object / object | د منځپانګې څیز / څیز | plural څیزونه |
| node | غوټه | |
| location | ځای | main location = اصلي ځای |
| content class / class | د منځپانګې کلاس / کلاس | class group = د کلاسونو ډله |
| attribute | ځانګړنه | plural ځانګړنې |
| datatype | د ډېټا ډول | |
| version | نسخه | |
| draft | مسوده | My drafts = زما مسودې |
| publish / published | خپرول / خپور شوی | |
| translation / language | ژباړه / ژبه | main language = اصلي ژبه |
| siteaccess | د سایټ لاسرسی | plural د سایټ لاسرسي |
| site | سایټ | |
| design | ډیزاین | |
| section | برخه | the Exponential section only |
| object state / state group | د څیز حالت / د حالت ډله | |
| role / policy | رول / تګلاره | |
| permission / access | اجازه / لاسرسی | Access denied = لاسرسی رد شو |
| limitation | محدودیت | |
| user / user group | کارن / د کارنانو ډله | anonymous user = ناپېژندل شوی کارن |
| user account / My account | د کارن حساب / زما حساب | |
| log in / log out | ننوتل / وتل | sentence: ننوځئ / ووځئ |
| password / username | پټنوم / کارن‌نوم | the label "Login" = کارن‌نوم |
| e-mail | برېښنالیک | |
| cache / clear cache | کش / کش پاکول | |
| template | کېنډۍ | |
| override | بدیل | override templates = د کېنډۍ بدیلونه |
| fallback | احتیاطي | fallback language = احتیاطي ژبه |
| workflow / workflow event | کاري بهیر / د کاري بهیر پېښه | |
| trigger | محرک | |
| trash / empty trash | کثافت‌دانۍ / کثافت‌دانۍ تشول | |
| bookmark | نښه | plural نښې |
| settings / setting | امستنې / امستنه | |
| Setup (admin tab) | جوړونه | |
| Dashboard | ډشبورډ | |
| Content structure / Media library | د منځپانګې جوړښت / د رسنیو کتابتون | |
| layout / block / zone | ترتیب / بلاک / سیمه | Exponential Layouts stays as is |
| extension | غځونه | plural غځونې |
| plugin | پلګ‌ان | never غځونه |
| package | بسته | |
| module / view / function | ماډیول / لید / فنکشن | View (button) = کتل |
| search / search engine | لټون / د لټون انجن | |
| search index / index / reindex | د لټون لړلیک / لړلیک / بیا لړلیک کول | |
| log / log file | لاګ / د لاګ دوتنه | |
| audit / audit trail | پلټنه / د پلټنې رېکارډ | |
| cronjob | مهالوېشلې دنده | |
| job / queue / worker | دنده / کتار / کارګر | |
| preview | مخکتنه | |
| upload / download | پورته کول / ښکته کول | |
| export / import | صادرول / واردول | |
| remove | لرې کول | take out of a list or place |
| delete | ړنګول | destroy |
| edit / save / store | سمول / خوندي کول / خوندي کول | "Store" is the save button; only the shop tab of the header is هټۍ |
| discard | پرېښودل | |
| apply / cancel / OK | پلي کول / لغوه کول / سمه ده | |
| back / next / previous | شاته / بل / مخکینی | |
| yes / no | هو / نه | |
| add / create / new | زیاتول / جوړول / نوی | |
| move / copy | لېږدول / کاپي کول | |
| close / refresh, update | تړل / تازه کول | |
| reset / restore | بېرته تنظیمول / بېرته راګرځول | |
| select / select all | ټاکل / ټول ټاکل | |
| enable / disable | فعالول / غیرفعالول | enabled = فعال, disabled = غیرفعال |
| visible / hidden | ښکاره / پټ | hide = پټول, reveal = ښکاره کول |
| archive | آرشیف | |
| order (shop) / order (sorting) | فرمایش / ترتیب | sorting = اوډنه |
| sort / ascending / descending | اوډل / پورته کېدونکی / ښکته کېدونکی | |
| tag / custom tag | ټګ / ځانګړی ټګ | |
| keyword | کلیدي ټکی | |
| owner / creator | خاوند / جوړونکی | |
| created / modified | جوړ شوی / بدل شوی | |
| relation / related objects | اړیکه / اړوند څیزونه | reverse related = معکوسه اړیکه |
| parent / sub-items | پورتنی توکی / فرعي توکي | item = توکی |
| subtree | فرعي ونه | |
| ID / identifier / remote ID | پېژند / پېژندونکی / لرې پېژند | node ID = د غوټې پېژند |
| URL alias / URL wildcard | د URL بل نوم / د URL وایلډکارډ | |
| priority / default / required | لومړیتوب / تلواله / اړین | |
| status, state | حالت | next to object states on one screen, status = وضعیت |
| warning / error | خبرداری / تېروتنه | |
| notification | خبرتیا | |
| collaboration | همکاري | |
| information collection / collected information | د معلوماتو راټولول / راټول شوي معلومات | |
| file / folder / image / media | دوتنه / فولډر / انځور / رسنۍ | |
| path / browse | مسیر / لټول | |
| database | ډېټابېس | |
| statistics / system information | شمېرنې / د سیستم معلومات | |
| install / uninstall | نصبول / نصب لرې کول | |
| shop / basket / customer / product | هټۍ / ټوکرۍ / پېرودونکی / محصول | |
| VAT / discount / price / currency | د ارزښت زیاتې مالیه / تخفیف / بیه / اسعار | |
| wish list | د هیلو لېست | |
| newsletter / subscription | خبرپاڼه / ګډون | |
| session | ناسته | |
| field | ساحه | |
| format | بڼه | |
| feed | فیډ | |
| tab | ټب | |
| theme | تیم | |
| menu / toolbar | مینو / د وسیلو پټه | |
| name / title / description / type / value / date | نوم / سرلیک / تشریح / ډول / ارزښت / نېټه | |
| AI / model / prompt / token | مصنوعي ځیرکتیا / ماډل / پرامپټ / ټوکن | |
| embedding / chunk / vector | امبېډینګ / ټوټه / وېکتور | |
| provider | چمتو کوونکی | |
| deploy, deployment | ځای پر ځای کول | |
| mapping | تړاو | |
| editor (software) | اډیټر | |
| interface language | د انټرفېس ژبه | |
| admin interface | د مدیریت پینل | |

## 5. Decisions after the translation

Terms settled when the translations were compared, mostly to keep two English words from sharing one Pashto word.

| English | Pashto | Note |
|---|---|---|
| Approve / Approval | منظورول / منظوري | approved = منظور شوی; not تایید, which is confirm |
| Confirm | تاییدول | |
| OK | سمه ده | never تایید or منظور |
| Deny / Reject | ردول | rejected = رد شوی |
| Pending | په تمه | |
| Container | کانټېنر | |
| Locale | سیمه‌ییز تنظیم (locale) | locale code = د locale کوډ |
| Charset | کرکټر سیټ (charset) | |
| Wildcard / placeholder | وایلډکارډ / ځای‌نیونکی | |
| Redirect | د مسیر بدلون | not لېږد, which is transfer |
| License / permission | جواز / اجازه | never swapped; database privilege = صلاحیت |
| Theme / format / template | تیم / بڼه / کېنډۍ | three different words |
| Embed / embedded | دننه ایښودل / دننه ایښودل شوی | ځای پر ځای کول stays deploy |
| Link / linked | لینک / لینک شوی | |
| Thumbnail | کوچنی انځور | |
| Flags (class properties) | نښانونه | bookmark stays نښه |
| Summary / teaser | لنډیز / لنډه سریزه | digest = لنډیز (digest) |
| Preset | مخکې ټاکل شوې امستنه | |
| Overview | عمومي کتنه | |
| Rich text | بډای متن | |
| Load / loading order | بارول / د بارولو ترتیب | not پورته کول, which is upload |
| Elevate (search) | لوړول | |
| Batch | دسته | بسته is package |
| Indent / outdent | انډنټ زیاتول / انډنټ کمول | |
| Component | کمپوننټ | برخه is section |
| Assign | ګمارل | ټاکل is select |
| Scope | حوزه | ساحه is field |
| Key (INI, API) | کیلي | never کلی |
| Secret | راز | |
| Repository / storage / registry | زېرمتون / زېرمه / ثبتلیک | |
| Checkout / payment gateway / endpoint | د تادیې پړاو / د تادیې ګېټوې / پای‌ټکی | |
| Kernel / release | کرنل / ریلیز | release notes = د ریلیز یادښتونه |
| Template operator | د کېنډۍ عملګر | |
| Thread (discussion) / thread (process) | بحث / تار | |
| Column / row / line | ستون / قطار / کرښه | |
| Interface | انټرفېس | |
| Dependency | انحصار | تړاو is mapping |
| Register (an account) | نوم لیکل | registration = نوم‌لیکنه |
| Preferences | غوره‌توبونه | |
| Staging / production | ازمېښتي چاپېریال / تولیدي چاپېریال | |
| Maintenance mode / lock | د ساتنې حالت / قلف | |
| Comment / feedback / poll | تبصره / نظر / نظرپوښتنه | |
| Description / intro | تشریح / سریزه | |
| Untouched draft | نه سمه شوې مسوده | |
| Host / domain | کوربه / ډومېن | |
