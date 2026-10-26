# Yiddish (yid-US): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Yiddish (כּלל־ייִדיש) in the spelling of the YIVO rules, in the neutral, technical register of current
Yiddish software and of the Yiddish Wikipedia. The locale is `yid-US`: CLDR has Yiddish only as `yi_UA`, and the
largest Yiddish-speaking community lives in the United States (see
[doc/eng-us/languages.md](../../../doc/eng-us/languages.md)). No other locale falls back to `yid-US`. Hebrew
(`heb-IL`) is written in the same script but is a different language: its catalogue is no model for words.

- **No daytshmerish.** Words taken over from German where Yiddish has its own are avoided: באַשטימונגען, not
  אייַנשטעלונגען as a calque of "Einstellungen" where the glossary says otherwise; ענדערן, not פֿאַרענדערן; ניט, not
  נישט in running text; זייַן, not זיין.
- Internationalisms of computing take the form the Yiddish Wikipedia gives them (סערווער, סקריפּט, פֿונקציע,
  מאָדול, קאַש). Words from the Hebrew-Aramaic component keep their traditional spelling (הנחה, כּלל, תּנאַי,
  שׂרטוט).

## 2. Form of address

The interface addresses the user with the formal **איר** (you), **אייַער** (your).

- **Buttons, menu items and links**: the infinitive: אויפֿהיטן, אַנולירן, אויסמעקן, רעדאַגירן, שיקן צום
  פֿאַרעפֿנטלעכן, אַוועקנעמען די אויסגעקליבענע, אַרייַנלייגן אין מיסטקאַסטן. Not the imperative (היט אויף).
- **Titles, column headings and tabs**: nouns or noun phrases: באַשטימונגען, איבערזעצונגען, הויפּט־אָרט.
- **Instructions**: the formal imperative: "קלייַבט אויס דעם עלעמענט און דריקט אויף „גוט“.", "וואַרט ביז די
  פּריאָריטעט ווערט אויפֿגעהיט.". A reference ("see ...") is "זעט ...".
- **Confirmations and questions**: "צי ווילט איר ...?": "צי ווילט איר אַוועקנעמען דעם קלאַס?". Not "ביסטו
  זיכער".
- **What the user can or may do**: "איר קענט ...", "איר מעגט ניט ...", or impersonal with מען: "מען קען ניט
  ענדערן די הויפּטשפּראַך ...". "You do not have permission" is "איר האָט ניט קיין דערלויבעניש ...".
- Not allowed: the familiar דו / דייַן, gender forms, slash forms.

## 3. Script and typography

- **Hebrew script in the YIVO orthography**, written right to left. The administration turns right to left by
  itself (`share/locale/yid-US.ini`, `TextDirection=rtl`); a translation needs no direction marks.
- **The YIVO diacritics are written**: אַ, אָ, פּ, פֿ, כּ, תּ, בֿ, שׂ, וּ (only next to another ו: וווּ, צוּוואַרפֿן,
  else ו: פֿון, גוט), יִ (ייִדיש, פֿריִער), ייַ. The digraphs are written as two letters, not with the ligature
  characters: וו, וי, יי, and ייַ as יי with a patah (ייַ). The diacritics are combining marks after the base letter
  (Unicode NFC), never the precomposed presentation forms (U+FB1D to U+FB4F).
- **Final letters** (ך, ם, ן, ף, ץ) at the end of a word; ף only in words from the Hebrew-Aramaic component, else
  פֿ (בריוו, ניט בריף; but אויף).
- **Compounds** with Latin text, digits or a placeholder take the maqaf ־ (U+05BE): URL־אַליאַס, PHP־טעקע,
  HTML־קאָד. Compounds of two Yiddish words are written as one word or with the maqaf as the dictionaries do:
  באַניצער־גרופּע, הויפּטשפּראַך.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay in Latin letters, unchanged, and stand free:
  a space before and after them, or a punctuation mark right after. Never a Yiddish letter glued to a placeholder:
  "%count אָביעקטן", not "%countאָביעקטן". The same for code, file names, INI settings and product names.
- **Quotation marks**: the YIVO marks „...“ where the source has its own quotation marks in text; where the source
  has `&quot;` or a straight " that is code or an entity, keep it exactly.
- **Punctuation**: Latin comma, full stop, colon, semicolon, question and exclamation mark, as in the source.
- **Numbers**: digits 0-9. The catalogue has no plural forms: "%count אָביעקטן" or a label with a count.

## 4. Glossary

Binding for the kernel and every extension.

| English | Yiddish | Note |
|---|---|---|
| content | אינהאַלט | |
| content object | אינהאַלט־אָביעקט | |
| object | אָביעקט | plural אָביעקטן |
| node | קנופּ | plural קנופּן; main node: הויפּט־קנופּ; parent node: עלטערן־קנופּ |
| location | אָרט | plural ערטער; main location: הויפּט־אָרט |
| item (list entry) | עלעמענט | plural עלעמענטן |
| class | קלאַס | the content class, plural קלאַסן; class group: קלאַסן־גרופּע; a PHP class: PHP־קלאַס |
| content class | אינהאַלט־קלאַס | |
| attribute | אַטריבוט | plural אַטריבוטן |
| datatype | דאַטן־טיפּ | |
| version | ווערסיע | plural ווערסיעס; version history: ווערסיע־געשיכטע |
| draft | שׂרטוט | plural שׂרטוטים; store draft: אויפֿהיטן דעם שׂרטוט |
| publish | פֿאַרעפֿנטלעכן | published: פֿאַרעפֿנטלעכט; publishing: פֿאַרעפֿנטלעכונג |
| send for publishing | שיקן צום פֿאַרעפֿנטלעכן | |
| discard | אָפּוואַרפֿן | discard draft: אָפּוואַרפֿן דעם שׂרטוט |
| archived | אַרכיווירט | |
| pending | וואַרטנדיק | |
| rejected | אָפּגעוואָרפֿן | |
| siteaccess | וועבזייַטל־צוטריט | plural וועבזייַטל־צוטריטן |
| site, website | וועבזייַטל | plural וועבזייַטלעך |
| section | אָפּטייל | the Exponential section, plural אָפּטיילן; an INI section: אָפּשניט |
| role | ראָלע | plural ראָלעס |
| policy | פּאָליטיק | plural פּאָליטיקן; one rule of a role |
| limitation | באַגרענעצונג | plural באַגרענעצונגען |
| permission | דערלויבעניש | plural דערלויבענישן; access denied: צוטריט אָפּגעזאָגט |
| module | מאָדול | plural מאָדולן |
| function | פֿונקציע | plural פֿונקציעס |
| user | באַניצער | plural the same |
| user group | באַניצער־גרופּע | plural באַניצער־גרופּעס |
| group | גרופּע | plural גרופּעס |
| anonymous | אַנאָנים | |
| administrator | אַדמיניסטראַטאָר | |
| administration (interface) | אַדמיניסטראַציע | |
| username | באַניצער־נאָמען | login (the name): באַניצער־נאָמען |
| password | פּאַראָל | plural פּאַראָלן |
| log in / sign in | אַרייַנלאָגירן | the act: אַרייַנלאָגירונג |
| log out / sign out | אַרויסלאָגירן | |
| e-mail | בליצפּאָסט | an e-mail message: בליצבריוו; e-mail address: בליצפּאָסט־אַדרעס |
| cache | קאַש | clear cache: אויסליידיקן דעם קאַש |
| template | מוסטער | plural מוסטערן |
| override | איבערשרייַב | override template: איבערשרייַב־מוסטער |
| fallback | אויסוועג | fallback language: אויסוועג־שפּראַך |
| design | דיזיין | |
| workflow | אַרבעטס־גאַנג | plural אַרבעטס־גענג |
| trigger | אויסלייזער | |
| event | געשעעניש | plural געשעענישן |
| trash | מיסטקאַסטן | empty trash: אויסליידיקן דעם מיסטקאַסטן; move to trash: אַרייַנלייגן אין מיסטקאַסטן |
| bookmark | בוכצייכן | plural בוכצייכנס |
| settings | באַשטימונגען | configuration |
| setup | אייַנריכטונג | the Setup tab |
| layout | אויסלייג | Exponential Layouts; plural אויסלייגן |
| block | בלאָק | plural בלאָקן |
| zone | זאָנע | plural זאָנעס |
| extension | פֿאַרברייטערונג | plural פֿאַרברייטערונגען |
| package | פּעקל | plural פּעקלעך |
| search | זוכן | search (noun): זוך; search index: זוך־אינדעקס; indexing: אינדעקסירונג |
| log | זשורנאַל | a log file; never "log in" |
| queue | ריי | job queue: ריי פֿון אויפֿגאַבעס |
| job / task | אויפֿגאַבע | plural אויפֿגאַבעס |
| cron | cron | a cron job: cron־אויפֿגאַבע |
| script | סקריפּט | plural סקריפּטן |
| worker | אַרבעטער־פּראָצעס | plural אַרבעטער־פּראָצעסן |
| kernel, core | קערן | |
| server | סערווער | |
| database | דאַטנבאַזע | |
| folder | מאַפּע | plural מאַפּעס |
| file | טעקע | plural טעקעס |
| image | בילד | plural בילדער; thumbnail: מיניאַטור |
| preview | פֿאָרויסבליק | |
| upload | אַרויפֿלאָדן | |
| download | אַראָפּלאָדן | |
| export / import | עקספּאָרטירן / אימפּאָרטירן | |
| remove | אַוועקנעמען | take out of a list or place |
| delete | אויסמעקן | destroy |
| edit | רעדאַגירן | |
| editor | רעדאַקטאָר | a person; the editing software: רעדאַקטאָר too |
| save / store | אויפֿהיטן | the verb "Store" of the forms |
| Store (shop tab) | קראָם | the shop, as "Shop" |
| cancel | אַנולירן | |
| OK | גוט | |
| apply | צולייגן | |
| back | צוריק | |
| next / previous | ווייַטער / פֿריִער | |
| enable / disable | אַקטיווירן / דעאַקטיווירן | |
| enabled / disabled | אַקטיווירט / דעאַקטיווירט | |
| active / inactive | אַקטיוו / ניט־אַקטיוו | |
| run | אויספֿירן | a script, a job |
| start / stop | אָנהייבן / אָפּשטעלן | |
| session | סעסיע | plural סעסיעס |
| hidden / visible | באַהאַלטן / קענטיק | hide / reveal: באַהאַלטן / ווייַזן |
| archive | אַרכיוו | a file archive too |
| order (sort) | סדר | sort order: סאָרטיר־סדר |
| order (shop) | באַשטעלונג | plural באַשטעלונגען |
| shop | קראָם | |
| basket | קויש | |
| product | פּראָדוקט | plural פּראָדוקטן |
| customer | קונה | plural קונים |
| discount | הנחה | plural הנחות |
| VAT | מוסף־ווערט־שטייַער | |
| price | פּרייַז | |
| currency | וואַלוטע | |
| tag | עטיקעט | a keyword tag, plural עטיקעטן; an HTML tag: HTML־עטיקעט |
| translation | איבערזעצונג | plural איבערזעצונגען |
| language | שפּראַך | plural שפּראַכן; main language: הויפּטשפּראַך; interface language: אינטערפֿייס־שפּראַך |
| locale | לאָקאַל | |
| state | צושטאַנד | the object state, plural צושטאַנדן |
| status | סטאַטוס | |
| view (button) | אָנקוקן | a module view (noun): אָנבליק |
| format | פֿאָרמאַט | |
| sort | סאָרטירן | |
| filter | פֿילטער | the act: פֿילטרירן |
| select | אויסקלייַבן | select all: אויסקלייַבן אַלץ; selected: אויסגעקליבן |
| browse | דורכבלעטערן | |
| copy | קאָפּירן | |
| move | אַריבערפֿירן | |
| add | צוגעבן | |
| create | שאַפֿן | |
| new | נייַ | |
| restore | צוריקשטעלן | |
| install / uninstall | אינסטאַלירן / דעאינסטאַלירן | |
| sub items | אונטער־עלעמענטן | |
| subtree | אונטערבוים | |
| related objects | פֿאַרבונדענע אָביעקטן | relation: פֿאַרבינדונג |
| URL alias | URL־אַליאַס | |
| redirect | ווייַטערפֿירן | |
| dashboard | קאָנטראָל־ברעט | |
| content structure | אינהאַלט־סטרוקטור | |
| media library | מעדיע־ביבליאָטעק | |
| identifier / ID | אידענטיפֿיקאַטאָר / ID | |
| priority | פּריאָריטעט | |
| owner | באַזיצער | |
| modified / created | געענדערט / געשאַפֿן | |
| date / time | דאַטע / צייַט | |
| error / warning | טעות / וואָרענונג | plural טעותים / וואָרענונגען |
| help | הילף | |
| notification | אָנזאָג | plural אָנזאָגן |
| wizard | אַסיסטענט | setup wizard: אייַנריכטונג־אַסיסטענט |
| feed (RSS) | קאַנאַל | feed reader: קאַנאַל־לייענער |
| tab | קוויטל | neuter (דאָס קוויטל), plural קוויטלעך |
| audit, audit trail | רעוויזיע, רעוויזיע־שפּור | |
| repository | רעפּאָזיטאָריע | |
| checksum | קאָנטראָל־סומע | |
| account | קאָנטע | |
| default | סטאַנדאַרט־ | default value: סטאַנדאַרט־ווערט |
| toolbar | געצייג־פּאַס | |
| container | קאָנטיינער | plural קאָנטיינערס |
| AI | קינסטלעכע אינטעליגענץ | |
| pattern (name pattern, wildcard) | שאַבלאָן | not מוסטער, which is the template |
| wildcard | וויילדקאַרד | |
| remote ID | דרויסנדיקער ID | |
| placeholder | פּלאַצהאַלטער | |
| collection | זאַמלונג | information collector: אינפֿאָרמאַציע־זאַמלער |
| approve, approval | באַשטעטיקן, באַשטעטיקונג | |
| software | ווייכוואַרג | |
| creator / modifier | שאַפֿער / ענדערער | |
| request (HTTP) | בקשה | plural בקשות |
| query | אָנפֿרעג | |
| handler / provider / listener | באַהאַנדלער / צושטעלער / צוהערער | |
| engine | מאָטאָר | |
| release / upgrade | אַרויסגאַבע / אַפּגרייד | |
| backup | רעזערוו־קאָפּיע | |
| maintenance | אויפֿהאַלטונג | |
| directory | מאַפּע | as folder |
| header (HTTP) | קעפּל | |
| page layout (pagelayout template) | בלאַט־אויסשטעל | kept apart from אויסלייג (Exponential Layouts) |
| passkey | צוטריט־שליסל | plural צוטריט־שליסלען |
| two-step sign-in | צוויי־שטאַפּלדיקע אַרייַנלאָגירונג | |
| device / browser | מכשיר / בלעטערער | plural מכשירים |
| link | לינק | plural לינקען |
| registration, register | פֿאַרשרייַבונג, פֿאַרשרייַבן | |
| payment gateway | צאָלונג־טויער | |
| none | קיינער | "no ...": קיין ... |
| yes / no | יאָ / ניין | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is קראָם, the word the shop menu uses for "Shop"; the verb "Store" of the
  forms is אויפֿהיטן.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
