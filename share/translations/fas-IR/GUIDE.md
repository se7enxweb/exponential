# Persian (fas-IR): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard written Persian of Iran, spelled as the Academy of Persian Language and Literature recommends and as the
major software vendors write it. Dari speakers read the same catalogue: `fas-AF` has no translation of its own and
falls back to `fas-IR`. Write neutral standard Persian that reads naturally in Kabul as well as in Tehran: no
Tehran colloquial forms, no slang. The tone is factual and short.

Where Persian has no settled word for a technical term, use the loanword that Persian software already uses
(سرور، توکن، پلاگین) rather than a new coinage. A term may carry its English form in parentheses at its first
mention in a long text (`کار زمان‌بندی‌شده (cronjob)`), never in a label.

## 2. Form of address

The user is addressed with the formal **شما**, everywhere and without exception.

- **Buttons, menu items, links, tabs, column headings and titles** use a noun or verbal noun, with no address:
  ذخیره (Save), حذف (Delete), برداشتن (Remove), ویرایش (Edit), لغو (Cancel), اعمال (Apply), انتشار (Publish),
  بارگذاری (Upload), انتخاب همه (Select all), دور انداختن پیش‌نویس (Discard draft), افزودن به نشانک‌ها
  (Add to bookmarks).
- **Sentences, hints, questions and error messages** use the second person plural:
  - آیا مطمئن هستید که می‌خواهید این موارد را بردارید؟ (Are you sure you want to remove these items?)
  - لطفاً یک مکان انتخاب کنید (Please choose a location)
  - شما مجوز برداشتن کلاس‌ها را ندارید. (You do not have permission to remove classes.)
  - فایلی را که می‌خواهید بارگذاری کنید انتخاب کنید. (Select the file that you want to upload.)
- Possessives: «… شما» or «… خود» (گذرواژهٔ خود را وارد کنید).
- "Please" is لطفاً, followed by the plural imperative.
- **Not allowed**: the singular تو and its forms (تو، برایت in the sense "for you", داری، می‌خواهی، هستی) and the
  bare singular imperative in a sentence (انتخاب کن). برایتان is the correct plural.

## 3. Script and typography

- Persian letters only: **ی** (U+06CC) and **ک** (U+06A9), never Arabic ي (U+064A) or ك (U+0643). Avoid ة.
- The zero-width non-joiner (ZWNJ, U+200C) is required: the prefixes می/نمی (می‌خواهید، نمی‌توان), the plural ‌ها
  (فایل‌ها، نسخه‌ها), compounds (پیش‌نویس، پیش‌نمایش، تازه‌سازی، زمان‌بندی‌شده).
- The ezafe after a final ه is written **هٔ** throughout: حافظهٔ پنهان، نسخهٔ جدید، گذرواژهٔ خود.
- Punctuation: ، (comma), ؛ (semicolon), ؟ (question mark); the full stop and colon as in English. Quotation marks
  are « ». Parentheses are ordinary ( ).
- Latin terms (Solr, URL, PHP, INI keys) are embedded as they are; the text direction is handled by the page.
- Numbers: Western digits 0–9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Persian | Note |
|---|---|---|
| content | محتوا | |
| content object / object | شیء محتوا / شیء | plural اشیا |
| node | گره | |
| location | مکان | main location = مکان اصلی |
| content class / class | کلاس محتوا / کلاس | class group = گروه کلاس |
| attribute | ویژگی | |
| datatype | نوع داده | |
| version | نسخه | |
| draft | پیش‌نویس | My drafts = پیش‌نویس‌های من |
| publish / published | انتشار، منتشر کردن / منتشرشده | |
| translation / language | ترجمه / زبان | main language = زبان اصلی |
| siteaccess | دسترسی سایت | plural دسترسی‌های سایت |
| site | سایت | |
| design | طراحی | |
| section | بخش | the Exponential section only |
| object state / state group | وضعیت شیء / گروه وضعیت | |
| role / policy | نقش / خط‌مشی | |
| permission / access | مجوز / دسترسی | Access denied = دسترسی رد شد |
| limitation | محدودیت | |
| user / user group | کاربر / گروه کاربری | anonymous user = کاربر ناشناس |
| user account / My account | حساب کاربری / حساب من | |
| log in / log out | ورود / خروج | in a sentence: وارد شوید / خارج شوید |
| password / username | گذرواژه / نام کاربری | the label "Login" = نام کاربری |
| e-mail | ایمیل | |
| cache / clear cache | حافظهٔ پنهان / پاک کردن حافظهٔ پنهان | |
| template | قالب | |
| override | جایگزینی | override template = قالب جایگزین; overridden = جایگزین‌شده |
| fallback | پشتیبان | fallback language = زبان پشتیبان; fall back on = بازگشت به |
| workflow / workflow event | گردش کار / رویداد گردش کار | |
| trigger | راه‌انداز | |
| trash / empty trash | سطل زباله / خالی کردن سطل زباله | |
| bookmark | نشانک | |
| settings / setting | تنظیمات / تنظیم | |
| Setup (admin tab) | راه‌اندازی | |
| Dashboard | پیشخوان | |
| Content structure / Media library | ساختار محتوا / کتابخانهٔ رسانه | |
| layout / block / zone | چیدمان / بلوک / ناحیه | Exponential Layouts stays as is |
| extension | افزونه | |
| plugin | پلاگین | never افزونه |
| package | بسته | |
| module / view / function | ماژول / نما / تابع | View (button) = مشاهده |
| search / search engine | جستجو / موتور جستجو | |
| search index / index / reindex | نمایهٔ جستجو / نمایه، نمایه‌سازی / نمایه‌سازی مجدد | database index = شاخص |
| log / log file | گزارش / فایل گزارش | |
| audit / audit trail | ممیزی / ردگیری ممیزی | |
| cronjob | کار زمان‌بندی‌شده | cronjob part = قسمت کار زمان‌بندی‌شده |
| job / queue / worker | کار / صف / پردازش‌گر | |
| preview | پیش‌نمایش | |
| upload / download | بارگذاری / دانلود | |
| export / import | برون‌بری / درون‌ریزی | |
| remove | برداشتن | take out of a list or place |
| delete | حذف | destroy |
| edit / save / store | ویرایش / ذخیره / ذخیره | "Store" is the save button; only the shop tab of the header is فروشگاه |
| discard | صرف‌نظر | Discard draft = دور انداختن پیش‌نویس |
| apply / cancel / OK | اعمال / لغو / تأیید | |
| back / next / previous | بازگشت / بعدی / قبلی | |
| yes / no | بله / خیر | |
| add / create / new | افزودن / ایجاد / جدید | |
| move / copy | انتقال / کپی | |
| close / refresh | بستن / تازه‌سازی | |
| update / reset / restore | به‌روزرسانی / بازنشانی / بازیابی | |
| select / select all | انتخاب / انتخاب همه | |
| enable / disable | فعال کردن / غیرفعال کردن | enabled = فعال, disabled = غیرفعال |
| visible / hidden | قابل مشاهده / پنهان | hide = پنهان کردن, reveal = آشکار کردن |
| archive | بایگانی | |
| order (shop) / order (sorting) | سفارش / ترتیب | |
| sort / ascending / descending | مرتب‌سازی / صعودی / نزولی | |
| tag (keyword, eztags) | برچسب | |
| tag (HTML, XML) / custom tag | تگ / تگ سفارشی | |
| keyword | کلیدواژه | |
| owner / creator | مالک / ایجادکننده | |
| created / modified (column) | تاریخ ایجاد / تاریخ تغییر | in a sentence: ایجاد شد / تغییر کرد |
| relation / related objects | ارتباط / اشیای مرتبط | reverse related = ارتباط معکوس |
| parent / children / sub-items | والد / فرزندان / زیرموردها | item = مورد |
| subtree | زیردرخت | |
| ID / identifier / remote ID | شناسه / شناسه / شناسهٔ راه دور | node ID = شناسهٔ گره |
| URL alias / URL wildcard | نام مستعار URL / الگوی عام URL | |
| priority / default / required | اولویت / پیش‌فرض / الزامی | |
| status / state | وضعیت | |
| warning / error | هشدار / خطا | |
| notification | اعلان | |
| collaboration | همکاری | |
| information collection / collected information | گردآوری اطلاعات / اطلاعات گردآوری‌شده | |
| file / folder / image / media | فایل / پوشه / تصویر / رسانه | |
| thumbnail | تصویر بندانگشتی | |
| path / browse | مسیر / مرور | |
| database | پایگاه داده | |
| statistics / system information | آمار / اطلاعات سیستم | |
| install / uninstall | نصب / حذف نصب | |
| shop / basket / customer / product | فروشگاه / سبد خرید / مشتری / محصول | |
| VAT / discount / price / currency | مالیات بر ارزش افزوده / تخفیف / قیمت / واحد پول | |
| wish list | فهرست علاقه‌مندی‌ها | |
| newsletter / subscription | خبرنامه / اشتراک | |
| session | نشست | |
| field | فیلد | |
| format | قالب | see section 5 |
| feed | خوراک | |
| tab | زبانه | |
| theme | پوسته | |
| name / title / description / type / value / date | نام / عنوان / توضیحات / نوع / مقدار / تاریخ | |
| AI / model / prompt / token | هوش مصنوعی / مدل / پرامپت / توکن | |
| embedding / chunk / vector | بردار تعبیه / قطعه / بردار | |
| provider | ارائه‌دهنده | |
| deploy, deployment | استقرار | |
| mapping | نگاشت | |
| editor (software) / editor (person) | ویرایشگر / ویراستار | |
| interface language | زبان رابط کاربری | |
| admin interface | پنل مدیریت | |

## 5. Decisions after the translation

Where two English terms first fell onto one Persian word, they are kept apart as follows.

| English | Persian | Note |
|---|---|---|
| Approve / Approval | تأیید / تأیید انتشار | OK is also تأیید; where both stand on one page, Approve becomes تأیید انتشار. Approved = تأییدشده |
| Confirm | تأیید | |
| Deny / Reject | رد | rejected = ردشده |
| Pending | در انتظار | not معلق |
| Container (class property) | دربرگیرنده | the CSS container of the layout editor = کانتینر |
| Locale | کد locale | `locale` stays Latin; localization = بومی‌سازی |
| Charset | مجموعهٔ نویسه | |
| License | مجوز نشر | never bare مجوز, which is permission |
| privilege (database) | امتیاز | not مجوز |
| Format | قالب | only with a data or file format (در قالب JSON); next to templates write فرمت, so it is not read as template |
| embed (object in text) | جاسازی | never for embedding, which is بردار تعبیه |
| fallback | پشتیبان | never جایگزین, which is override |
| profile | پروفایل | not نمایه, which is index |
| release (software) | نسخهٔ انتشار | never bare انتشار, which is publish |
| redirect / forward (URL) | تغییر مسیر / هدایت | |
| alert | اخطار | not هشدار, which is warning |
| flags (class properties) | مشخصه‌ها | not ویژگی‌ها, which is attributes |
| segment (search) / navigation part | سگمنت / قسمت ناوبری | not بخش, which is section |
| lookup | یافتن | not جستجو, which is search |
| assign / scope / batch | تخصیص / دامنه / دسته | |
| overview / rich text / preset | نمای کلی / متن غنی / پیش‌تنظیم | |
| thread (conversation) | رشته | |
| payment gateway / gateway | درگاه پرداخت / دروازه | |
| checkout (shop) | تسویه‌حساب | |
| comment (content) / comment (collaboration) / comment (code) | دیدگاه / نظر / توضیح | |
| staging / production | پیش‌تولید / تولید | |
