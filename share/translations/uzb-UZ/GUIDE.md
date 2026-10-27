# Uzbek (uzb-UZ): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted, with the Turkish catalogue (`tur-TR`) as a reference for
the structure of Turkic technical terms; a review by native speakers is welcome.

## 1. Language and register

Standard literary Uzbek (oʻzbek adabiy tili) as written in Uzbekistan, in the **Latin alphabet of 1995**, in the
neutral, polite register of current Uzbek software, the government portals (my.gov.uz) and Uzbek Wikipedia. No other
locale falls back to `uzb-UZ`.

- Turkish is a relative, not a model: the words often differ (Turkish kaydet / Uzbek saqlash, sil / oʻchirish,
  kullanıcı / foydalanuvchi). Use only Uzbek words and Uzbek endings.
- Russian loan words are used where Uzbek has made them its own (fayl, server, parol, sinf is Uzbek, arxiv,
  format); spell them in Uzbek Latin (obyekt, versiya, kesh), not in Cyrillic.
- Short, plain sentences.

## 2. Form of address

- The user is **Siz** (capitalised in sentences addressed to the user), with the -siz verb forms. Never sen.
- **Buttons, menu items and links**: the **verbal noun in -sh / -ish**, as Uzbek software does: Saqlash, Bekor
  qilish, Oʻchirish, Olib tashlash, Tahrirlash, Qoʻshish, Yaratish, Qidirish, Tanlash, Yuborish, Koʻrish, Nashr
  qilish, Yuklash.
- **Titles, column headings and tabs**: nouns or noun phrases: Sozlamalar, Tarjimalar, Asosiy joylashuv, Nom.
- **Instructions**: the polite imperative in **-ing**, with "iltimos" where English has "please": "Elementni
  tanlang va \"OK\" tugmasini bosing.", "Iltimos, ustuvorlik saqlanguncha kuting.". A reference ("see ...") is
  "... ga qarang".
- **Confirmations and questions**: "Haqiqatan ham ...moqchimisiz?": "Haqiqatan ham bu sinfni oʻchirmoqchimisiz?".
- **What the user can or may do**: "Siz ... olasiz", "Siz ... olmaysiz", or impersonal: "Asosiy tilni oʻzgartirib
  boʻlmaydi ...". "You do not have permission" is "Sizda ... uchun ruxsat yoʻq".
- **Messages about a result**: the passive past: "Qoralama saqlandi.", "Fayl muvaffaqiyatli yuklandi.".

## 3. Script and typography

- **Latin script** (lotin yozuvi): the letters **oʻ** and **gʻ** are written with the modifier letter turned comma
  **ʻ (U+02BB)**, and the tutuq belgisi (glottal stop) with the modifier letter apostrophe **ʼ (U+02BC)**: maʼlumot,
  taʼrif, sanʼat. Never the plain apostrophe ' or the quotation marks ‘ ’ for these letters. sh, ch, ng as digraphs.
  Not the Cyrillic script.
- **Capitals**: only the first word of a button, title or sentence and proper names: "Qoralamani tahrirlash", not
  "Qoralamani Tahrirlash".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free. Uzbek suffixes
  cannot be written onto a placeholder (%nameni would be a different placeholder): put the placeholder in quotation
  marks and the suffix after the closing mark ("%name"ni, "%name" faylini), use a noun that carries the suffix
  (%name nomli obyektni), or a construction without a suffix (Fayl: %name). A number placeholder takes "ta" as a
  separate word: "%count ta obyekt". The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source; no space before colon, question or exclamation mark.
- **Numbers**: digits 0-9. A noun after a number stays singular: "%count ta element".

## 4. Glossary

Binding for the kernel and every extension.

| English | Uzbek | Note |
|---|---|---|
| content | kontent | content object: kontent obyekti |
| object | obyekt | |
| node | tugun | main node: asosiy tugun; parent node: ota tugun |
| location | joylashuv | main location: asosiy joylashuv |
| item (list entry) | element | sub items: ichki elementlar |
| class | sinf | the content class; class group: sinflar guruhi; a PHP class: PHP klassi |
| content class | kontent sinfi | |
| attribute | atribut | |
| datatype | maʼlumot turi | |
| version | versiya | version history: versiyalar tarixi |
| draft | qoralama | store draft: Qoralamani saqlash |
| publish | nashr qilish | published: nashr qilingan; publishing: nashr |
| send for publishing | nashrga yuborish | |
| discard | bekor qilish | discard draft: Qoralamani bekor qilish |
| archived / pending / rejected | arxivlangan / kutilmoqda / rad etilgan | |
| siteaccess | sayt kirish nuqtasi | siteaccess as a setting name stays in code |
| site, website | sayt / veb-sayt | |
| section | boʻlim | the Exponential section; an INI section: seksiya |
| role | rol | |
| policy | siyosat | one rule of a role |
| limitation | cheklov | |
| permission | ruxsat | access denied: kirish taqiqlangan |
| access | kirish | |
| module | modul | |
| function | funksiya | |
| user / user group | foydalanuvchi / foydalanuvchilar guruhi | |
| group | guruh | |
| anonymous | anonim | anonymous user: anonim foydalanuvchi |
| administrator | administrator | allowlisted where it stands alone; administration: boshqaruv |
| username | foydalanuvchi nomi | login (the name): login nomi |
| password | parol | |
| log in / log out | kirish / chiqish | |
| e-mail | elektron pochta | e-mail address: elektron pochta manzili |
| cache | kesh | clear cache: Keshni tozalash |
| template | shablon | override template: qayta belgilovchi shablon |
| override | qayta belgilash | |
| fallback | zaxira | fallback language: zaxira til |
| design | dizayn | |
| workflow | ish jarayoni | |
| trigger | trigger | |
| event | hodisa | |
| trash | savat | empty trash: Savatni boʻshatish; move to trash: Savatga koʻchirish |
| bookmark | xatchoʻp | |
| settings | sozlamalar | configuration: konfiguratsiya |
| setup | sozlash | the Setup tab |
| layout | maket | Exponential Layouts |
| block / zone | blok / zona | |
| extension | kengaytma | |
| package | paket | |
| search | qidiruv | to search: qidirish; search index: qidiruv indeksi; indexing: indekslash |
| log | jurnal | a log file: jurnal fayli; never "log in" |
| queue | navbat | job queue: vazifalar navbati |
| job / task | vazifa | |
| cron | cron | |
| script | skript | |
| worker | ishchi jarayon | |
| kernel, core | yadro | |
| server | server | |
| database | maʼlumotlar bazasi | |
| folder / directory | jild / katalog | |
| file | fayl | |
| image | rasm | thumbnail: kichik rasm |
| preview | oldindan koʻrish | |
| upload / download | yuklash / yuklab olish | |
| export / import | eksport / import qilish | |
| remove | olib tashlash | take out of a list or place |
| delete | oʻchirish | destroy; deleted: oʻchirildi |
| edit | tahrirlash | |
| editor | muharrir | |
| save / store | saqlash | the verb "Store" of the forms |
| Store (shop tab) | doʻkon | the shop, as "Shop" |
| cancel | bekor qilish | |
| OK | OK | kept (allowlisted) |
| apply | qoʻllash | |
| back | orqaga | |
| next / previous | keyingi / oldingi | |
| enable / disable | yoqish / oʻchirib qoʻyish | enabled / disabled: yoqilgan / oʻchirilgan |
| active / inactive | faol / nofaol | |
| run | ishga tushirish | |
| start / stop | boshlash / toʻxtatish | |
| session | seans | |
| hidden / visible | yashirin / koʻrinadigan | hide / reveal: yashirish / koʻrsatish |
| archive | arxiv | |
| order (sort) | tartib | sort order: saralash tartibi |
| order (shop) | buyurtma | |
| shop | doʻkon | |
| basket | savatcha | |
| product | mahsulot | |
| customer | mijoz | |
| discount | chegirma | |
| VAT | QQS | value added tax: qoʻshilgan qiymat soligʻi |
| price / currency | narx / valyuta | |
| tag | teg | an HTML tag: HTML tegi |
| translation | tarjima | |
| language | til | main language: asosiy til; interface language: interfeys tili |
| locale | lokal | |
| state | holat | the object state; state group: holatlar guruhi |
| status | maqom | kept apart from holat |
| view (button) | koʻrish | a module view (noun): koʻrinish |
| format | format | |
| sort | saralash | |
| filter | filtr | |
| select | tanlash | select all: Hammasini tanlash; selected: tanlangan |
| browse | koʻrib chiqish | |
| copy / move | nusxalash / koʻchirish | |
| add / create / new | qoʻshish / yaratish / yangi | |
| restore | tiklash | |
| install / uninstall | oʻrnatish / oʻchirib tashlash | |
| subtree | quyi daraxt | |
| related objects | bogʻliq obyektlar | relation: bogʻliqlik |
| URL alias | URL taxallusi | |
| redirect | yoʻnaltirish | |
| dashboard | boshqaruv paneli | |
| content structure | kontent tuzilmasi | |
| media library | media kutubxona | |
| identifier / ID | identifikator / ID | |
| priority | ustuvorlik | |
| owner | egasi | |
| modified / created | oʻzgartirilgan / yaratilgan | |
| date / time | sana / vaqt | |
| error / warning | xato / ogohlantirish | |
| help | yordam | |
| notification | bildirishnoma | |
| wizard | sehrgar | setup wizard: sozlash sehrgari |
| feed (RSS) | lenta | |
| tab | yorliq | |
| account | hisob | |
| default | standart | default value: standart qiymat |
| toolbar | asboblar paneli | |
| container | konteyner | |
| collection | toʻplam | |
| approve, approval | tasdiqlash | |
| request / query | soʻrov | |
| backup | zaxira nusxa | |
| maintenance | texnik xizmat | |
| link | havola | |
| registration | roʻyxatdan oʻtish | |
| device / browser | qurilma / brauzer | |
| none / all | yoʻq / hammasi | |
| yes / no | ha / yoʻq | |
| name / title / description | nom / sarlavha / tavsif | |
| value / type / list | qiymat / tur / roʻyxat | |
| page / path | sahifa / yoʻl | |
| required / optional | majburiy / ixtiyoriy | |
| options | parametrlar | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is doʻkon, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is saqlash.
- "Login" in the user, session and audit lists is the user's login name (Login nomi); the Login button of the error page
  is the act (Kirish).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID, OK ...) and words spelled
  the same in Uzbek (server, format, administrator) are listed with their reason in
  `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
