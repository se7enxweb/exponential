# Kashmiri (kas-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Kashmiri (کٲشُر, Koshur) in the Perso-Arabic script (CLDR `ks_Arab`), as used in Jammu and Kashmir
(India), in the neutral, polite register of Kashmiri newspapers, schoolbooks and the Kashmiri localisations of
operating systems and office software. No other locale falls back to `kas-IN`.

- Kashmiri grammar, not Urdu grammar. Word order is subject - object - verb with the finite verb in the second
  position of a main clause where Kashmiri puts it there; postpositions and case endings follow the noun. The
  genitive markers agree with the thing owned: ہٕنز (feminine singular), ہُنٛد (masculine singular), ہِنٛد
  (plural), and the dative ends in -س / -َس (سیکشنس "to the section"). Do not replace them with the Urdu کا / کی / کے
  or کو.
- Verbs are the Kashmiri ones (کٔرُن to do, کرُن in the verbal noun, چھُ / چھِ / چھِو to be, آو "came" for the
  passive past). The polite imperative ends in -ِو (کٔرِو, وُچھِو, ژُنِو).
- Orthography: the Kashmiri vowel letters and diacritics of the standard orthography are written where the word needs
  them, for instance ٲ (long open a), ٚ (short a), ۄ (rounded o), ۆ (long rounded o), ێ (long e), ٕ (the
  central vowel mark), ٔ, ٟ and the vowel marks zabar, zer, pesh. A word whose vowel sign is part of its identity
  (ہٕنز, ژٕٹھ, کٔرِو) is not written without it. Do not drop them for convenience, and do not add them to words
  that do not carry them.
- Technical words: Kashmiri uses English loans written in Perso-Arabic script for most computing terms (فایِل file,
  فولڈَر folder, ڈیٹا data, پاس ورڈ password, ویب سایِٹ website, ای میل e-mail). Where Kashmiri has its own settled
  word, use it (زَبان language, اِجازَت permission, ترجمہ translation, مَدَد help, دُکان shop).
- Loanwords from Urdu (`urd-PK`) and, through it, Arabic and Persian are used only where Kashmiri itself uses them
  (مَصنوعات, رعایت, شایع, منسوٗخ, مٔنظوٗر). Urdu serves to check the meaning of a term and to see a settled loan;
  never copy an Urdu word that Kashmiri does not use, and never take Urdu grammar (کیا, ہے, کریں, کا / کی / کے).
- An English loan is written in Perso-Arabic letters only where it is the established Kashmiri word. Otherwise use the
  Kashmiri word or a short descriptive phrase. Never leave an English word in Latin letters unless it is a product
  name, a code or a format name.
- Short, plain sentences; no honorific forms beyond the polite request.

## 2. Form of address

- The user is addressed politely in the plural form, توٚہہ, without the pronoun where Kashmiri can leave it out; never
  the familiar form تُہ / چھُ.
- **Buttons, menu items and links**: the polite imperative in -ِو, as Kashmiri interfaces do: محفوظ کٔرِو (save),
  منسوٗخ کٔرِو (cancel), مٹٲوِو (delete), ہٹٲوِو (remove), تَرمیم کٔرِو (edit), شامِل کٔرِو (add), بناوِو (create),
  ژھارِو (search), ژُنِو (select), روانہ کٔرِو (send), وُچھِو (view), شایع کٔرِو (publish), اَپ لوڈ کٔرِو (upload),
  ڈاوُن لوڈ کٔرِو (download).
- **Titles, column headings and tabs**: nouns or noun phrases: ترتیبات، ترجمہ، مَرکٔزی جاے، ناو.
- **Instructions**: the polite imperative, with مہربانی کٔرِتھ where the source has please: "اکھ آبجیکٹ ژُنِو تہٕ
  \"ٹھیک چھُ\" دَباوِو."
- **Confirmations and questions**: the question particle چھا or the question mark alone, never both pointing twice:
  "یہ سیکشن مٹاوُن چھا؟". The question mark is ؟.
- **What the user can or may do**: "... کٔرِتھ ہیکِو", "... کٔرِتھ ہیکِو نہ"; "You do not have permission" is
  "توٚہہ چھنہ اِجازَت".
- **Messages about a result**: the completed passive form with آو: "نوٹ محفوظ کرنہٕ آو.", "فایِل اَپ لوڈ کرنہٕ آو."

## 3. Script and typography

- **Perso-Arabic script** (U+0600-U+06FF and the Kashmiri letters of U+0750-U+077F where needed), written **right to
  left**, in the Nastaliq-compatible letter forms of the standard Kashmiri orthography. A message is never mixed with
  Devanagari, Sharada or Latin transliteration.
- **Direction**: the locale sets `TextDirection=rtl`. Translators write the text in logical order and never insert
  direction marks (U+200E, U+200F, U+202A-U+202E) by hand unless the source has them; the page direction and the
  browser's bidirectional algorithm do the rest. Never reverse a string, never write a mirrored copy of the source
  order.
- **Mixed text**: Latin words, numbers, product names, codes and placeholders are embedded in the Kashmiri sentence and
  stand free with a space before and after them (or a punctuation mark right after). Nothing is glued to them, so
  that the bidirectional order stays unambiguous.
- **Punctuation**: the Arabic-script marks: the comma **،** (U+060C), the question mark **؟** (U+061F), the
  semicolon **؛** (U+061B) and the full stop **۔** (U+06D4) at the end of a sentence. The colon after a label is
  **:** and stays as in the source. Ellipses and exclamation marks stay as in the source. A space follows these marks,
  never precedes them. A message that has no final stop in the source gets none.
- **Brackets and quotes**: parentheses and brackets stay as in the source (they mirror automatically in a right to
  left text); quotation marks are the straight double quotes "..." or the ones of the source; `&quot;` and other
  entities are kept exactly.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free, as above. HTML tags
  stay exactly and may move with the sentence; their order inside a right to left sentence follows the logical order
  of the Kashmiri text.
- **Numbers**: digits 0-9 (Arabic-Indic and Extended Arabic-Indic digits are not used in the interface). Numbers keep
  their reading order inside the right to left text.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kashmiri | Note |
|---|---|---|
| content | مَواد | content object: مَواد آبجیکٹ |
| object | آبجیکٹ | |
| node | نوڈ | main node: مَرکٔزی نوڈ; parent node: والِد نوڈ |
| location | جاے | main location: مَرکٔزی جاے |
| item (list entry) | آیِٹَم | |
| tree | ٹری | the tree menu: ٹری مینیو; content structure tree: مَواد ٹری |
| class | کلاس | the content class: مَواد کلاس; class group: کلاس گروپ |
| attribute | وَصف | |
| datatype | ڈیٹا قِسِم | |
| version | ؤرژَن | version history: ؤرژَنہٕ ہُنٛد تٲریٖخ |
| draft | مَسوٗدہٕ | store draft: مَسوٗدہٕ محفوظ کٔرِو |
| publish | شایع کٔرِو | published: شایع کرنہٕ آمُت |
| send for publishing | شایع کرنہٕ خاطرٕ روانہ کٔرِو | |
| discard | رَد کٔرِو | discard draft: مَسوٗدہٕ رَد کٔرِو |
| archived / pending / rejected | آرکایِو کرنہٕ آمُت / زیٖر غور / رَد کرنہٕ آمُت | |
| siteaccess | سایِٹ ایکسیس | siteaccess as a setting name stays in code |
| site, website | سایِٹ / ویب سایِٹ | |
| section | سیکشَن | the Exponential section; an INI section: INI سیکشَن |
| role | کردار | |
| policy | پالیسی | |
| limitation | حَد | |
| permission | اِجازَت | access denied: رَسایی مَنع |
| module | ماڈیوٗل | |
| function | فَنکشَن | a PHP function: PHP function |
| user / user group | صارِف / صارِف گروپ | |
| group | گروپ | |
| anonymous | گُمنام | anonymous user: گُمنام صارِف |
| administrator | مُنتظِم | administration (interface): اَنتِظام |
| username | صارِف ناو | |
| password | پاس ورڈ | |
| log in / log out | لاگ اِن کٔرِو / لاگ آوُٹ کٔرِو | the act, as a button; login name: لاگ اِن ناو |
| e-mail | ای میل | e-mail address: ای میل پَتہٕ |
| cache | کیش | clear cache: کیش صاف کٔرِو |
| template | ٹیمپلیٹ | override template: اوورراِیڈ ٹیمپلیٹ |
| override | اوورراِیڈ | |
| fallback | فال بیک | fallback language: فال بیک زَبان |
| design | ڈیزایِن | |
| workflow | ورک فلو | |
| trigger | ٹرِگَر | |
| event | واقعہٕ | |
| trash | ردی | empty trash: ردی خالی کٔرِو; move to trash: ردی منز تَرجُم کٔرِو |
| bookmark | بُک مارک | |
| settings | ترتیبات | configuration: کنفِگرِیشَن |
| setup | سیٹ اَپ | the Setup tab |
| layout | لے آوُٹ | Exponential Layouts stays |
| block / zone | بلاک / زون | |
| extension | ایکسٹینشَن | |
| package | پیکیج | |
| search | ژھارُن | search index: ژھارنہٕ ہُنٛد اِنڈیکس |
| log | لاگ | a log file: لاگ فایِل; never "log in" |
| queue | قطار | job queue: کٲم قطار |
| job / task | کٲم / ٹاسک | |
| cron | cron | |
| script | script | |
| kernel, core | کرنَل | |
| server | سَروَر | |
| database | ڈیٹا بیس | |
| folder / directory | فولڈَر | |
| file | فایِل | |
| image | تَصویر | thumbnail: ہوٚٹھ تَصویر |
| preview | پریویو | |
| upload / download | اَپ لوڈ کٔرِو / ڈاوُن لوڈ کٔرِو | |
| export / import | بَرآمَد کٔرِو / دَرآمَد کٔرِو | |
| remove | ہٹٲوِو | take out of a list or place |
| delete | مٹٲوِو | destroy; deleted: مٹاونہٕ آمُت |
| edit | تَرمیم کٔرِو | |
| editor | مُدیر / اێڈِٹَر | person / software |
| save / store | محفوظ کٔرِو | the verb "Store" of the forms |
| Store (shop tab) | دُکان | the shop, as "Shop" |
| cancel | منسوٗخ کٔرِو | |
| OK | ٹھیک چھُ | |
| apply | لاگوٗ کٔرِو | |
| back | واپَس | |
| next / previous | اَگلہٕ / گۄڈنیک | |
| enable / disable | چالوٗ کٔرِو / بَنٛد کٔرِو | enabled / disabled: چالوٗ / بَنٛد |
| active / inactive | سَرگَرم / غَیر سَرگَرم | |
| run | چلٲوِو | |
| start / stop | شُروٗع کٔرِو / روٗزِو | |
| session | سیشَن | |
| hidden / visible | چھُپٲوُن / ظاہِر | hide / reveal: چھُپٲوِو / ظاہِر کٔرِو |
| archive | آرکایِو | |
| order (sort) | ترتیٖب | sort order: ترتیٖب |
| order (shop) | آرڈَر | |
| shop | دُکان | |
| basket | ٹوکرۍ | |
| product | مَصنوعات | |
| customer | گراہَک | |
| discount | رعایت | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | قیمَت / کرنسی | |
| tag | ٹیگ | |
| translation | ترجمہ | |
| language | زَبان | main language: مَرکٔزی زَبان |
| locale | لوکیل | |
| state | حالَت | the object state |
| status | صورتحال | |
| view (button) | وُچھِو | a module view (noun): ویو |
| format | فارمیٹ | |
| sort | ترتیٖب دِیُن | |
| filter | فِلٹَر | |
| select | ژُنِو | select all: سٲرِنی ژُنِو; selected: ژُنمُت |
| copy / move | کاپی کٔرِو / تَرجُم کٔرِو | |
| add / create / new | شامِل کٔرِو / بناوِو / نو | |
| restore | بحال کٔرِو | |
| install / uninstall | اِنسٹال کٔرِو / اَن اِنسٹال کٔرِو | |
| related objects | مُتَعَلِق آبجیکٹ | relation: تَعَلُق |
| dashboard | ڈیش بورڈ | |
| identifier / ID | شَناخت / ID | |
| priority | ترجیٖح | |
| owner | مالِک | |
| modified / created | تَرمیم شُدٕہ / بناوُن آمُت | |
| date / time | تٲریٖخ / وَقت | |
| error / warning | خرٲبی / خَبردار | |
| help | مَدَد | |
| notification | اِطلاع | |
| account | اَکاوُنٹ | |
| default | طٔے شُدٕہ | |
| link | لِنک | |
| none / all | کانٛہہ نہٕ / سٲرِنی | |
| yes / no | آ / نہ | |
| name / title / description | ناو / عُنوان / تَفصیٖل | |
| value / type / list | قیمَت / قِسِم / فِہرِسٹ | |
| page / path | صَفحہ / راہ | |
| required / optional | لازِمی / اِختِیاری | |
| options | آپشَن | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is دُکان, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is محفوظ کٔرِو.
- "Login" (no comment) in the user, session and audit lists is the user's login name, a noun: لاگ اِن ناو. "Login"
  with the comment Button, also on the error page (`design/admin/error/kernel`), is the act of logging in: لاگ اِن
  کٔرِو.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
