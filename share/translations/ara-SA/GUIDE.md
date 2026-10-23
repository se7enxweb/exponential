# Arabic (ara-SA): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Modern Standard Arabic (الفصحى), neutral and technical, as in current Arabic software. Not the dialect of any
country: `ara-SA` is the one Arabic catalogue, and every other Arabic locale falls back to it (`settings/i18n.ini`,
`FallbackLanguages[]`): `ara-AE`, `ara-BH`, `ara-DJ`, `ara-DZ`, `ara-EG`, `ara-ER`, `ara-IQ`, `ara-JO`, `ara-KM`,
`ara-KW`, `ara-LB`, `ara-LY`, `ara-MA`, `ara-MR`, `ara-OM`, `ara-PS`, `ara-QA`, `ara-SD`, `ara-SO`, `ara-SY`,
`ara-TD`, `ara-TN`, `ara-YE`. Choose words that read naturally from Morocco to Iraq; avoid words that belong to
one country.

## 2. Form of address

- **Buttons, menu items and links**: the verbal noun (مصدر), not the imperative: حفظ, إلغاء, إزالة, تحرير,
  إرسال للنشر, إزالة المحدد, نقل إلى سلة المحذوفات.
- **Titles, column headings and tabs**: nouns, usually with the article: الإعدادات, الترجمات, الموقع الرئيسي.
- **Instructions in hints and help texts**: the imperative, second person masculine singular, the generic form of
  Arabic software: "حدد العنصر الذي تريده أن يكون التحديد الافتراضي ثم انقر على "موافق"."
- **Requests**: يرجى with a verbal noun: "يرجى اختيار موقع", "يرجى الانتظار حتى تُحفظ الأولوية." Not الرجاء, not
  من فضلك.
- **Confirmations**: هل تريد بالتأكيد ...؟ — "هل تريد بالتأكيد إزالة هذه الفئة؟". A plain question: هل تريد ...؟
  ("هل تريد نسخ الفئة <%class_name>؟").
- **Statements about the user**: second person masculine singular: "ليس لديك إذن بإزالة الفئات.", "لا يمكنك تغيير
  اللغة الرئيسية ...". Where the subject is the system, prefer the passive or an impersonal form: "لا يُسمح ...".
- Not allowed: feminine or plural forms of address, هل أنت متأكد من أنك ... (use هل تريد بالتأكيد), mixing
  imperative and verbal noun on buttons.

## 3. Script and typography

- Arabic script, written right to left. The administration turns right to left by itself when the interface
  language is Arabic; a translation needs no direction marks (no U+200E/U+200F, no isolates). Placeholders (`%name`,
  `%1`) stay as they are inside the Arabic sentence, and the browser places them. The name an editor typed is
  isolated by the templates, not by the translation.
- **Latin terms in Arabic text**: names, formats, settings, paths and code stay in Latin letters (TRANSLATING.md,
  rule 5). Put them where the Arabic grammar needs them; with the article, write الـ and a space before the Latin
  word: "في هذا الـ siteaccess", "حسب الـ siteaccess". Before a placeholder the preposition is joined with a tatweel:
  "لـ '%1'".
- **Punctuation**: the Arabic comma ، , semicolon ؛ and question mark ؟. The full stop, the colon and the
  exclamation mark are the Latin ones. Quotation marks as in the source: straight "..." where the source has them,
  “...” where the source has them.
- **Numbers**: Western digits 0–9, as the locale and the catalogue use them; not Arabic-Indic digits (٠–٩).
- **Diacritics**: only where a word would otherwise be ambiguous or to mark a passive: مفعّل, معطّل, أُنشئ, عُدّل,
  تُحفظ; the tanwin of adverbs (مثلًا, دائمًا). No full vocalization.
- **Counts**: the catalogue has no plural forms. Write the count with a neutral wording: "%count كائن", "العدد:
  %count".
- The ellipsis is "..." or "…" as in the source.

## 4. Glossary

Binding for the kernel and every extension.

| English | Arabic | Note |
|---|---|---|
| content | المحتوى | |
| content object | كائن المحتوى | |
| object | كائن | |
| node | عقدة | |
| location | الموقع | the place of an object in the tree |
| main location | الموقع الرئيسي | |
| site | الموقع | the website; الموقع الإلكتروني where "location" could be meant |
| class | الفئة | the content class; a PHP class is صنف PHP |
| content class | فئة المحتوى | |
| class group | مجموعة الفئات | |
| attribute | السمة | |
| datatype | نوع البيانات | |
| version | الإصدار | |
| draft | مسودة | |
| publish | نشر | |
| published | منشور | |
| send for publishing | إرسال للنشر | |
| store draft | تخزين المسودة | |
| discard | تجاهل | تجاهل المسودة |
| archived | مؤرشف | an archived version |
| rejected | مرفوض | |
| pending | معلّق | |
| siteaccess | siteaccess | stays in Latin letters, see section 5 |
| section | القسم | always the Exponential section |
| role | الدور | |
| policy | السياسة | |
| limitation | التقييد | |
| module | الوحدة | |
| function | الوظيفة | |
| user | المستخدم | |
| user group | مجموعة مستخدمين | |
| anonymous users | المستخدمون المجهولون | |
| login (the name) | اسم المستخدم | the field |
| log in | تسجيل الدخول | |
| log out | تسجيل الخروج | |
| password | كلمة المرور | |
| e-mail | البريد الإلكتروني | |
| cache | ذاكرة التخزين المؤقت | clear cache: مسح ذاكرة التخزين المؤقت |
| template | القالب | |
| override | التجاوز | override template: قالب التجاوز |
| fallback | بديل احتياطي | as an adjective: احتياطي (السلسلة الاحتياطية) |
| workflow | سير العمل | |
| trigger | مشغّل | |
| event | حدث | |
| trash | سلة المحذوفات | empty trash: إفراغ سلة المحذوفات |
| bookmarks | الإشارات المرجعية | |
| settings | الإعدادات | configuration |
| setup | الإعداد | the Setup tab |
| layout | التخطيط | Exponential Layouts; layouts: التخطيطات |
| block | الكتلة | a block of a layout |
| zone | المنطقة | |
| extension | الامتداد | |
| package | حزمة | |
| search index | فهرس البحث | indexing: فهرسة |
| search engine | محرك البحث | |
| log | السجل | a log file; never "log in" |
| queue | قائمة الانتظار | job queue: قائمة انتظار المهام |
| job | مهمة | |
| cron | cron | stays Latin; a cron job: مهمة cron |
| script | برنامج نصي | see section 5 |
| worker | عملية عاملة | see section 5 |
| kernel | نواة النظام | see section 5 |
| folder | مجلد | see section 5 |
| file | الملف | |
| image | صورة | thumbnail: صورة مصغرة |
| preview | معاينة | |
| upload | رفع | |
| download | تنزيل | |
| export | تصدير | |
| import | استيراد | |
| remove | إزالة | take out of a list or place |
| delete | حذف | destroy |
| edit | تحرير | |
| editor | المحرر | |
| save / store | حفظ | the verb "Store" of the forms |
| Store (shop tab) | المتجر | the shop, as "Shop" |
| cancel | إلغاء | |
| OK | موافق | |
| back | رجوع | |
| next / previous | التالي / السابق | |
| enable / disable | تفعيل / تعطيل | |
| enabled / disabled | مفعّل / معطّل | |
| active / inactive | نشط / غير نشط | |
| hidden / visible | مخفي / مرئي | |
| archive | الأرشيف | |
| order (sort) | الترتيب | |
| order (shop) | الطلب | orders: الطلبات |
| shop | المتجر | webshop: المتجر الإلكتروني |
| basket | السلة | |
| product | المنتج | |
| customer | العميل | |
| discount | الخصم | |
| VAT | ضريبة القيمة المضافة | |
| tag (keyword) | وسم | tags: الوسوم |
| tag (HTML/XML) | وسم HTML | |
| translation | الترجمة | |
| language | اللغة | main language: اللغة الرئيسية |
| locale | الإعدادات المحلية | |
| state | الحالة | the object state; object states: حالات الكائنات |
| status | الحالة | |
| view (button) | عرض | |
| format | الصيغة | |
| sort | الفرز | |
| search | بحث | |
| filter | تصفية | |
| select | تحديد | select all: تحديد الكل |
| browse | استعراض | |
| copy | نسخ | |
| move | نقل | |
| add | إضافة | |
| create | إنشاء | |
| new | جديد | |
| restore | استعادة | |
| install / uninstall | تثبيت / إلغاء التثبيت | |
| sub items | العناصر الفرعية | |
| subtree | شجرة فرعية | |
| related objects | الكائنات المرتبطة | |
| URL alias | اسم URL المستعار | |
| dashboard | لوحة التحكم | |
| content structure | بنية المحتوى | |
| media library | مكتبة الوسائط | |
| session | الجلسة | |
| identifier / ID | المعرّف | |
| priority | الأولوية | |
| owner | المالك | |
| modified / created | عُدّل / أُنشئ | |
| none | لا شيء | |
| yes / no | نعم / لا | |

## 5. Decisions after the translation

- **siteaccess** stays in Latin letters (with the article: الـ siteaccess). It is a configuration term that appears
  in `site.ini` and in the documentation, and a translated form (وصول الموقع) was not recognizable.
- **folder** is مجلد everywhere, in the file system and in the content tree; حافظة is the clipboard.
- **script** is برنامج نصي (not سكربت or نص برمجي): a command-line script, also "script" inside an SVG image.
- **worker** is عملية عاملة (plural عمليات عاملة): a process of the web server or of Velocity that serves
  requests.
- **kernel** is نواة النظام, not النواة alone, so that it is not read as a general "core".
- **installation** is التثبيت in its standard form.
- A greeting such as "%greeting, %name" takes the Arabic comma: "%greeting، %name".
