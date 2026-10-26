# Bhojpuri (bho-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted and adapted from the Hindi catalogue (`hin-IN`) where the technical terms are
shared; a review by native speakers is welcome.

## 1. Language and register

Standard written Bhojpuri (भोजपुरी) as used in Bihar and eastern Uttar Pradesh, in the polite, plain register of
Bhojpuri news websites and public notices. No other locale falls back to `bho-IN`.

- Bhojpuri is not Hindi with a few words changed: it has its own verbs, copula, postpositions and pronouns. Use them
  throughout: बा / बाटे / बानी / रहे, ना / नइखे / ना होखे, कइल गइल, होखी, सकेनी, के (genitive and object), में,
  से, खातिर, पर, आ (and), भा / चाहे (or), बाकिर (but), अगर ... त, ई (this), ऊ (that), सभ (all), कुछ, जवन, कवनो.
  Adapting a Hindi sentence means rewriting it: "क्या आप वाकई इस वर्ग को हटाना चाहते हैं?" becomes "का रउआ सचहूँ एह
  वर्ग के मेटावल चाहत बानी?".
- Technical loan words are shared with Hindi in their Hindi spelling (फ़ाइल, सर्वर, डेटाबेस, लॉग इन, टेम्पलेट).
  Everyday words are Bhojpuri (नाँव not नाम where it reads naturally, पन्ना for page, जगह for place).
- Short, plain sentences, no village dialect forms of one district.

## 2. Form of address

- The user is **रउआ** (honorific), with the honorific verb forms (रउआ ... सकत बानी, रउआ ... चाहत बानी). Never तू or
  तें.
- **Buttons, menu items and links**: the polite imperative in **-ईं**: सहेजीं, रद्द करीं, मेटाईं, हटाईं, बदलीं
  (edit), जोड़ीं, बनाईं, खोजीं, चुनीं, भेजीं, देखीं, प्रकाशित करीं, अपलोड करीं.
- **Titles, column headings and tabs**: nouns or noun phrases: सेटिंग, अनुवाद, मुख्य जगह, नाँव.
- **Instructions**: the same polite imperative, with "किरिपा क के" where English has "please": "आइटम चुनीं आ
  \"OK\" दबाईं।", "किरिपा क के प्राथमिकता सहेजाए ले इंतजार करीं।". A reference ("see ...") is "... देखीं".
- **Confirmations and questions**: "का रउआ सचहूँ ... चाहत बानी?".
- **What the user can or may do**: "रउआ ... सकत बानी", "रउआ ... ना सकत बानी", or impersonal: "मुख्य भाषा बदलल ना
  जा सकेला ...". "You do not have permission" is "रउआ लगे ... के अनुमति नइखे".
- **Messages about a result**: "... हो गइल" / "... कइल गइल": "ड्राफ़्ट सहेजा गइल।", "फ़ाइल सफलता से अपलोड हो गइल।".

## 3. Script and typography

- **Devanagari**, Unicode. Nukta letters (फ़, ज़) in loan words as Hindi writes them; chandrabindu where Bhojpuri
  pronounces a nasal vowel (नाँव, हँ).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after: "%name में", "%count गो". The same for code, file names, INI
  settings and product names, which stay in Latin letters.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: the full stop is the danda "।" at the end of a Bhojpuri sentence; a source sentence that ends in
  ":" "?" "!" or "..." keeps that mark.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Bhojpuri | Note |
|---|---|---|
| content | सामग्री | |
| content object / object | सामग्री ऑब्जेक्ट / ऑब्जेक्ट | |
| item (list entry) | आइटम | sub items: उप-आइटम |
| node | नोड | main node: मुख्य नोड; parent node: पैरेंट नोड |
| location | जगह | main location: मुख्य जगह |
| class / content class | वर्ग / सामग्री वर्ग | class group: वर्ग समूह; a PHP class: PHP क्लास |
| attribute | विशेषता | |
| datatype | डेटा प्रकार | |
| version | संस्करण | version history: संस्करण इतिहास |
| draft | ड्राफ़्ट | store draft: ड्राफ़्ट सहेजीं |
| publish | प्रकाशित करीं | published: प्रकाशित; publishing: प्रकाशन |
| send for publishing | प्रकाशन खातिर भेजीं | |
| discard | छोड़ दीं | discard draft: ड्राफ़्ट छोड़ दीं |
| archived / pending / rejected | संग्रहित / बाकी / नामंजूर | |
| siteaccess | साइट एक्सेस | siteaccess as a setting name stays in code |
| site, website | साइट / वेबसाइट | |
| section | खंड | the Exponential section; an INI section: सेक्शन |
| role | भूमिका | |
| policy | नीति | one rule of a role |
| limitation | सीमा | |
| permission | अनुमति | access denied: पहुँच नामंजूर |
| access | पहुँच | |
| module | मॉड्यूल | |
| function | फ़ंक्शन | |
| user / user group | उपयोगकर्ता / उपयोगकर्ता समूह | |
| group | समूह | |
| anonymous | अनाम | |
| administrator | प्रशासक | administration (interface): प्रशासन |
| username | उपयोगकर्ता नाँव | login (the name): लॉगिन नाँव |
| password | पासवर्ड | |
| log in / log out | लॉग इन करीं / लॉग आउट करीं | |
| e-mail | ईमेल | e-mail address: ईमेल पता |
| cache | कैश | clear cache: कैश साफ़ करीं |
| template | टेम्पलेट | override template: ओवरराइड टेम्पलेट |
| override | ओवरराइड | |
| fallback | फ़ॉलबैक | fallback language: फ़ॉलबैक भाषा |
| design | डिज़ाइन | |
| workflow | वर्कफ़्लो | |
| trigger | ट्रिगर | |
| event | घटना | |
| trash | कूड़ादान | empty trash: कूड़ादान खाली करीं; move to trash: कूड़ादान में डालीं |
| bookmark | बुकमार्क | |
| settings | सेटिंग | configuration: कॉन्फ़िगरेशन |
| setup | सेटअप | the Setup tab |
| layout | लेआउट | Exponential Layouts |
| block / zone | ब्लॉक / ज़ोन | |
| extension | एक्सटेंशन | |
| package | पैकेज | |
| search | खोज | to search: खोजीं; search index: खोज इंडेक्स |
| log | लॉग | a log file: लॉग फ़ाइल; never "log in" |
| queue | कतार | job queue: जॉब कतार |
| job / task | जॉब / काम | |
| cron | क्रॉन | |
| script | स्क्रिप्ट | |
| worker | वर्कर | |
| kernel, core | कर्नेल | |
| server | सर्वर | |
| database | डेटाबेस | |
| folder / directory | फ़ोल्डर / डायरेक्टरी | |
| file | फ़ाइल | |
| image | फोटो | thumbnail: थंबनेल |
| preview | पूर्वावलोकन | |
| upload / download | अपलोड / डाउनलोड | verbs: अपलोड करीं / डाउनलोड करीं |
| export / import | निर्यात / आयात | |
| remove | हटाईं | take out of a list or place |
| delete | मेटाईं | destroy; deleted: मेटा दिहल गइल |
| edit | बदलीं | editing: संपादन |
| editor | संपादक | |
| save / store | सहेजीं | the verb "Store" of the forms |
| Store (shop tab) | दोकान | the shop, as "Shop" |
| cancel | रद्द करीं | |
| OK | ठीक बा | |
| apply | लागू करीं | |
| back | पाछे | |
| next / previous | अगिला / पिछिला | |
| enable / disable | चालू करीं / बंद करीं | enabled / disabled: चालू / बंद |
| active / inactive | सक्रिय / निष्क्रिय | |
| run | चलाईं | |
| start / stop | सुरू करीं / रोकीं | |
| session | सत्र | |
| hidden / visible | लुकावल / देखाई देवे वाला | hide / reveal: लुकाईं / देखाईं |
| archive | संग्रह | a file archive: आर्काइव फ़ाइल |
| order (sort) | क्रम | |
| order (shop) | ऑर्डर | |
| shop | दोकान | |
| basket | टोकरी | |
| product | सामान | |
| customer | ग्राहक | |
| discount | छूट | |
| VAT | वैट | |
| price / currency | दाम / मुद्रा | |
| tag | टैग | an HTML tag: HTML टैग |
| translation | अनुवाद | |
| language | भाषा | main language: मुख्य भाषा |
| locale | लोकेल | |
| state | अवस्था | the object state; state group: अवस्था समूह |
| status | स्थिति | kept apart from अवस्था |
| view (button) | देखीं | a module view (noun): दृश्य |
| format | फ़ॉर्मेट | |
| sort | क्रम से लगाईं | |
| filter | फ़िल्टर | |
| select | चुनीं | select all: सभ चुनीं; selected: चुनल |
| browse | ब्राउज़ करीं | |
| copy / move | कॉपी करीं / ले जाईं | |
| add / create / new | जोड़ीं / बनाईं / नया | |
| restore | फेर से बहाल करीं | |
| install / uninstall | इंस्टॉल करीं / अनइंस्टॉल करीं | |
| subtree | सबट्री | |
| related objects | जुड़ल ऑब्जेक्ट | relation: संबंध |
| URL alias | URL उपनाम | |
| redirect | रीडायरेक्ट | |
| dashboard | डैशबोर्ड | |
| content structure | सामग्री संरचना | |
| media library | मीडिया लाइब्रेरी | |
| identifier / ID | पहचानकर्ता / ID | |
| priority | प्राथमिकता | |
| owner | मालिक | |
| modified / created | बदलल / बनावल | |
| date / time | तारीख / समय | |
| error / warning | गड़बड़ी / चेतावनी | |
| help | मदद | |
| notification | सूचना | |
| wizard | विज़ार्ड | |
| feed (RSS) | फ़ीड | |
| tab | टैब | |
| account | खाता | |
| default | डिफ़ॉल्ट | |
| toolbar | टूलबार | |
| container | कंटेनर | |
| collection | संग्रह | |
| approve, approval | मंजूर करीं, मंजूरी | |
| request / query | अनुरोध / क्वेरी | |
| backup | बैकअप | |
| maintenance | रखरखाव | |
| link | लिंक | |
| registration | पंजीकरण | |
| device / browser | डिवाइस / ब्राउज़र | |
| none / all | कवनो ना / सभ | |
| yes / no | हँ / ना | |
| name / title / description | नाँव / शीर्षक / विवरण | |
| value / type / list | मान / प्रकार / सूची | |
| page / path | पन्ना / पथ | |
| required / optional | जरूरी / वैकल्पिक | |
| options | विकल्प | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is दोकान, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is सहेजीं.
- "Login" in the user, session and audit lists is the user's login name (लॉगिन नाँव); the Login button of the error
  page is the act (लॉग इन करीं).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
