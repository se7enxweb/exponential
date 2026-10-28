# Dogri (doi-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Dogri (डोगरी) of Jammu in Devanagari, the script of the J&K Academy of Art, Culture and Languages and
of Dogri newspapers, radio and television, in the polite, neutral register of those texts and of Dogri software
localisations. The locale is `doi-IN` (Dogri, India); no other locale falls back to it.

- Word order is subject - object - verb; postpositions follow the noun: च (in), गी (to, object marker), कोला (from,
  with a person), थमां (from), दे नाल (with), आस्तै (for), पर (on), तक (until), बगैर (without). The genitive agrees
  with the thing owned: **दा** (masculine singular), **दी** (feminine), **दे** (masculine plural and oblique); plural
  feminine दियां.
- **Dogri is not Hindi.** Hindi (`hin-IN`) is a relative and serves only to check terminology; never copy a Hindi
  form that Dogri does not use. The frequent differences:
  - is / are: **ऐ** / **न** (Hindi है / हैं); was: **हा** / **ही** (Hindi था / थी); I am: **आं**;
  - not / no: **नेईं** (Hindi नहीं); yes: **हां**; also: **बी** (Hindi भी); but: **पर**; if: **जेकर** (Hindi अगर / यदि);
    what: **के** (Hindi क्या); where: **कुत्थे** (Hindi कहाँ); how: **किʼयां** (Hindi कैसे); this / that: **एह्** / **ओह्**
    (Hindi यह / वह);
  - postposition को becomes **गी**, में becomes **च**, से becomes **थमां** or **कोला**, का/की/के become **दा/दी/दे**;
  - completed action: **कीता** / **कीती** / **कीते** (Hindi किया / की / किए); **होआ** / **होई** (Hindi हुआ / हुई);
    **गेआ** / **गेई** (Hindi गया / गई); **दित्ता** (Hindi दिया); **लेआ** (Hindi लिया);
  - polite imperative: **-ओ** and **-ओ जी** (करो, करो जी, चुनो, दिक्खो) where Hindi uses -इए (कीजिए);
  - future in **-ग** / **-गा** as in Hindi, but the present continuous is **-दा ऐ / -दी ऐ** (करदा ऐ);
  - the glottal sign **ʼ** (U+02BC) marks the Dogri tone in words such as किʼयां; in Devanagari Dogri the tone is
    written with the avagraha **ऽ** (U+093D) or the Dogri-specific spelling the J&K Academy uses (नांऽ name, पिऽ).
    Use the form given in the glossary and keep it the same everywhere.
- Technical words: where Dogri uses a Hindi technical loan (प्रयोक्ता, सामग्री, संपादन, खोज, सूची, सूचना, मुद्रा), it is
  kept. Well-known computing loanwords in Devanagari stay where Dogri software and media use them (फाइल, फोल्डर,
  पासवर्ड, सर्वर, डेटाबेस, कैश, टेंपलेट). Never leave the English word in Latin letters unless it is a product name or
  code.
- Short, plain sentences; no Sanskritised or literary forms where an everyday Dogri word exists.

## 2. Form of address

- The user is addressed politely with **तुस** (object तुसेंगी, genitive तुंदा / तुंदी / तुंदे; the J&K Academy also
  writes तुसें for the oblique plural); never the familiar तूं and never the Hindi आप. Where Dogri can leave the
  pronoun out, it does.
- **Buttons, menu items and links**: the polite imperative in -ओ, as Dogri interfaces do: सहेजो (save), रद्द करो
  (cancel), मेटो (delete), कड्ढो (remove), संपादन करो (edit), जोड़ो (add), बनाओ (create), खोजो (search), चुनो
  (select), भेजो (send), दिक्खो (view), प्रकाशत करो (publish), अपलोड करो (upload), डाउनलोड करो (download). Where a
  short noun is the established label of a tab or toolbar, the noun is used (खोज, मदद).
- **Titles, column headings and tabs**: nouns or noun phrases: सेटिंग, अनुवाद, मुक्खी थाह्र, नांऽ.
- **Instructions**: the polite imperative, with "मेहरबानी करियै" where the source has please: "इक ऑब्जेक्ट चुनो ते
  \"ठीक ऐ\" दबाओ।" / "मेहरबानी करियै अपना पासवर्ड भरो।"
- **Confirmations and questions**: "के तुस सच्चें ... करना चांहदे ओ?": "के तुस सच्चें एह् खंड मेटना चांहदे ओ?".
- **What the user can or may do**: "... करी सकदे ओ", "... नेईं करी सकदे"; "You do not have permission" is
  "तुसेंगी ... दी इजाजत नेईं ऐ।"
- **Messages about a result**: the completed form: "मसौदा सहेजेआ गेआ।", "फाइल अपलोड होई गेई।", "सेटिंग बदली गेइयां।".
  Errors: "... नेईं होई सकेआ।" / "... नेईं लब्भेआ।"

## 3. Script and typography

- **Devanagari** (U+0900-U+097F). Words are separated by spaces. Dogri uses the nukta (ज़, फ़ and the like) only in
  borrowed words; the ZWJ and ZWNJ are not used to force forms.
- **Sentence end**: the **danda "।"** (U+0964) where the source has a full stop, as Dogri texts and the J&K Academy
  write it. The double danda is not used. A question ends with "?" (the source's mark). Colons after labels,
  ellipses ("..." or "…") and exclamation marks are kept as in the source. A space follows the mark inside a text,
  never before it. A label that has no full stop in the source gets none.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON, INI, API, HTML).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder, so no Dogri suffix or
  postposition is attached to it: write "प्रयोक्ता: %name" or rephrase so that the postposition follows a Dogri
  word. The same for Latin words, code and product names inside a Dogri sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (Devanagari digits are not used in the interface).

## 4. Glossary

Binding for the kernel and every extension.

| English | Dogri | Note |
|---|---|---|
| content | सामग्री | content object: सामग्री ऑब्जेक्ट |
| object | ऑब्जेक्ट | |
| node | नोड | main node: मुक्खी नोड parent node: मूल नोड |
| location | थाह्र | main location: मुक्खी थाह्र |
| item (list entry) | आइटम | |
| tree | रुक्ख | tree menu: रुक्ख मेन्यू content structure tree: सामग्री रुक्ख |
| class | क्लास | the content class: सामग्री क्लास class group: क्लास समूह |
| attribute | गुण | |
| datatype | डेटा-टाइप | |
| version | संस्करण | version history: संस्करण इतिहास |
| draft | मसौदा | store draft: मसौदा सहेजो |
| publish | प्रकाशत करो | published: प्रकाशत कीता गेआ |
| send for publishing | प्रकाशन आस्तै भेजो | |
| discard | छड्डो | discard draft: मसौदा छड्डो |
| archived / pending / rejected | संग्रहीत / लंबित / नामंजूर | |
| siteaccess | साइटएक्सेस | siteaccess as a setting name stays in code |
| site, website | साइट, वेबसाइट | |
| section | खंड | the Exponential section; an INI section: INI खंड |
| role | भूमका | |
| policy | नीति | |
| limitation | सीमा | |
| permission | इजाजत | access denied: पुज्जने दी इजाजत नेईं |
| module | मॉड्यूल | |
| function | फंक्शन | a PHP function: PHP function |
| user / user group | प्रयोक्ता / प्रयोक्ता समूह | |
| group | समूह | |
| anonymous | अनाम | anonymous user: अनाम प्रयोक्ता |
| administrator | प्रशासक | administration (interface): प्रशासन |
| username | प्रयोक्ता नांऽ | |
| password | पासवर्ड | |
| log in / log out | लॉगिन करो / लॉगआउट करो | |
| e-mail | ईमेल | e-mail address: ईमेल पता |
| cache | कैश | clear cache: कैश साफ करो |
| template | टेंपलेट | override template: ओवरराइड टेंपलेट |
| override | ओवरराइड | |
| fallback | फॉलबैक | fallback language: फॉलबैक भाशा |
| design | डिजाइन | |
| workflow | वर्कफ्लो | |
| trigger | ट्रिगर | |
| event | घटना | |
| trash | कूड़ादान | empty trash: कूड़ादान खाली करो move to trash: कूड़ादान च पाओ |
| bookmark | बुकमार्क | |
| settings | सेटिंग | configuration: संरचना |
| setup | सेटअप | the Setup tab |
| layout | लेआउट | Exponential Layouts stays |
| block / zone | ब्लॉक / क्षेत्तर | |
| extension | एक्सटेंशन | |
| package | पैकेज | |
| search | खोज | search index: खोज सूची |
| log | लॉग | a log file: लॉग फाइल never "log in" |
| queue | कतार | job queue: कम्म कतार |
| job / task | कम्म | |
| cron | cron | |
| script | script | |
| kernel, core | कर्नल | |
| server | सर्वर | |
| database | डेटाबेस | |
| folder / directory | फोल्डर | |
| file | फाइल | |
| image | तस्वीर | thumbnail: नन्नी तस्वीर |
| preview | पूर्वावलोकन | |
| upload / download | अपलोड / डाउनलोड | |
| export / import | एक्सपोर्ट / इंपोर्ट | |
| remove | कड्ढो | take out of a list or place |
| delete | मेटो | destroy; deleted: मेटी दित्ता गेआ |
| edit | संपादन करो | |
| editor | संपादक | person and software |
| save / store | सहेजो | the verb "Store" of the forms |
| Store (shop tab) | दुकान | the shop, as "Shop" |
| cancel | रद्द करो | |
| OK | ठीक ऐ | |
| apply | लागू करो | |
| back | पिच्छें | |
| next / previous | अगला / पिछला | |
| enable / disable | चालू करो / बंद करो | enabled / disabled: चालू / बंद |
| active / inactive | सक्रिय / निष्क्रिय | |
| run | चलाओ | |
| start / stop | शुरू करो / रोको | |
| session | सत्तर | |
| hidden / visible | लुकेआ दा / दिक्खने आह्ला | hide / reveal: लुकाओ / दस्सो |
| archive | संग्रह | |
| order (sort) | क्रम | sort order: क्रम |
| order (shop) | आर्डर | |
| shop | दुकान | |
| basket | टोकरी | |
| product | उत्पाद | |
| customer | ग्राहक | |
| discount | छूट | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | कीमत / मुद्रा | |
| tag | टैग | |
| translation | अनुवाद | |
| language | भाशा | main language: मुक्खी भाशा |
| locale | लोकेल | |
| state | हालत | the object state |
| status | स्थिति | |
| view (button) | दिक्खो | a module view (noun): दृश्य |
| format | फार्मेट | |
| sort | छांटो | |
| filter | फिल्टर | |
| select | चुनो | select all: सारे चुनो selected: चुने दे |
| copy / move | कापी करो / सरकाओ | |
| add / create / new | जोड़ो / बनाओ / नमां | |
| restore | बहाल करो | |
| install / uninstall | इंस्टाल करो / अनइंस्टाल करो | |
| related objects | संबंधत ऑब्जेक्ट | relation: संबंध |
| dashboard | डैशबोर्ड | |
| identifier / ID | पछान / ID | |
| priority | प्राथमिकता | |
| owner | मालक | |
| modified / created | बदलेआ दा / बनाया दा | |
| date / time | तरीक / समां | |
| error / warning | गलती / चेतावनी | |
| help | मदद | |
| notification | सूचना | |
| account | खाता | |
| default | डिफॉल्ट | |
| link | लिंक | |
| none / all | कोई नेईं / सारे | |
| yes / no | हां / नेईं | |
| name / title / description | नांऽ / शीर्षक / ब्योरा | |
| value / type / list | मान / किस्म / सूची | |
| page / path | सफा / रस्ता | |
| required / optional | जरूरी / वैकल्पिक | |
| options | विकल्प | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is दुकान, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is सहेजो.
- "Login" in the user, session and audit lists is the user's login name (लॉगिन नांऽ); the Login button of the error
  page is the act (लॉगिन करो).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Dogri differs from Hindi in the forms listed in section 1; a Hindi form that slipped in (है, नहीं, को, में, का/की/के,
  किया) is corrected to the Dogri one (ऐ, नेईं, गी, च, दा/दी/दे, कीता).
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
