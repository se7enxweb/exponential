# Dari (fas-AF): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Dari (دری), the Persian of Afghanistan, in the standard written form used by the Afghan government, the media and
the universities. The catalogue was adapted from the complete Iranian Persian one, `fas-IR`, row by row: what both
standards write alike stays as it is, and the words, spellings and expressions in which Afghan usage differs from
Iranian usage are changed. `fas-AF` has its own catalogue and no longer falls back to `fas-IR`.

The tone is factual, polite and short, as in Afghan official and business software. Where Dari uses the English
loanword in computing (ویب، انترنت، کمپیوتر، لینک، فولدر، آدرس), that loanword is used rather than an Iranian
Persian coinage (وب، رایانه، پیوند، پوشه، نشانی). Iranian Persian purisms that are not current in Afghanistan
(گذرواژه، رایانامه) are replaced. Pashto-influenced words are used where they are the standard Afghan term:
ولایت (province, not استان), ولسوالی (district, not شهرستان), پوهنتون (university, not دانشگاه), شاروالی
(municipality, not شهرداری).

## 2. Form of address

As in `fas-IR`: the user is addressed with the formal **شما** everywhere.

- **Buttons, menu items, links, tabs, column headings and titles** use a noun or verbal noun, with no address:
  ذخیره، حذف، برداشتن، ویرایش، لغو، اعمال، انتشار، انتخاب همه.
- **Sentences, hints, questions and error messages** use the second person plural: «لطفاً یک مکان انتخاب کنید»،
  «آیا مطمئن هستید که می‌خواهید این موارد را بردارید؟».
- "Please" is لطفاً, followed by the plural imperative.
- Not allowed: the singular تو and its verb forms; Iranian colloquial forms.

## 3. Script and typography

- **Arabic script, written right to left.** The locale `fas-AF` sets `TextDirection=rtl`; the page sets the
  direction, so Latin terms (Solr, URL, PHP, INI keys) are embedded as they are.
- Persian letters only: **ی** (U+06CC) and **ک** (U+06A9), never Arabic ي or ك.
- The zero-width non-joiner (U+200C) as in `fas-IR`: می‌خواهید، فایل‌ها، پیش‌نمایش. After a letter that does not
  join to the left (ر، د، و) none is needed: فولدرها، فولدری.
- The ezafe after a final ه is written **هٔ**, as in `fas-IR`: صفحهٔ اصلی.
- Punctuation: ، ؛ ؟; the full stop and the colon as in English; quotation marks « ».
- Numbers: Western digits 0–9, as in the source; the locale formats values.
- **Calendar and months**: the catalogue holds no month names. Dates are formatted by the locale `fas-AF`, which uses
  the Afghan names of the Gregorian months (جنوری، فبروری، مارچ، اپریل، می، جون، جولای، اگست، سپتمبر، اکتوبر،
  نومبر، دسمبر). Where a text names a month of the solar year, it uses the Afghan names (حمل، ثور، جوزا، سرطان، اسد،
  سنبله، میزان، عقرب، قوس، جدی، دلو، حوت), never the Iranian ones (فروردین … اسفند).

## 4. Glossary

The glossary of [fas-IR/GUIDE.md](../fas-IR/GUIDE.md) applies, with these Dari terms, which are binding.

| English | fas-IR | fas-AF (Dari) | Note |
|---|---|---|---|
| information | اطلاعات | معلومات | system information: معلومات سیستم; information collection: گردآوری معلومات |
| yes / no | بله / خیر | بلی / نخیر | |
| web / website | وب / وب‌سایت | ویب / ویب‌سایت | web server: ویب‌سرور |
| internet | اینترنت | انترنت | |
| computer | رایانه، کامپیوتر | کمپیوتر | |
| telephone | تلفن | تیلفون | |
| folder | پوشه | فولدر | directory stays دایرکتوری where fas-IR has it |
| order (shop) | سفارش | فرمایش | orders: فرمایش‌ها; custom stays سفارشی |
| password | گذرواژه | رمز عبور | passwords: رمزهای عبور |
| address (web, e-mail, postal) | نشانی | آدرس | e-mail address: آدرس ایمیل |
| link | پیوند | لینک | unlink: برداشتن لینک; symbolic link: لینک نمادین |
| keyboard | صفحه‌کلید | کیبورد | |
| e-mail | ایمیل، پست الکترونیکی | ایمیل | |
| online / offline | برخط / برون‌خط | آنلاین / آفلاین | |
| last name | نام خانوادگی | تخلص | |
| street | خیابان | سرک | |
| car | خودرو، ماشین | موتر | |
| province / district | استان / شهرستان | ولایت / ولسوالی | |
| faculty (university) | دانشکده | پوهنحی | |
| university / municipality | دانشگاه / شهرداری | پوهنتون / شاروالی | |

## 5. Terms kept from fas-IR

These Iranian Persian terms are also current in Afghan writing and stay: فایل، صفحه، کاربر، ذخیره، ویرایش،
حذف، تنظیمات، جستجو، دانلود، بارگذاری (upload)، مرورگر، پایگاه داده، نرم‌افزار، هشدار (warning) and اخطار
(alert), which stay apart as in `fas-IR`. A review by native speakers is welcome, above all for بارگذاری
(often آپلود in speech), مرورگر (often براوزر) and پایگاه داده (often دیتابیس).

## 6. Shop tab

The header tab "Store" of the shop is named like the shop menu (فروشگاه); every other "Store" is the button ذخیره.
"Login" in the user, session and audit lists is the user name (نام کاربری), as in `fas-IR`; the button on the
error page is the act of logging in (ورود).
