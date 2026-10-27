# Maithili (mai-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted and adapted from the Hindi catalogue (`hin-IN`) where the technical terms are
shared; a review by native speakers is welcome.

## 1. Language and register

Standard Maithili (मैथिली) as written in Mithila (Bihar and the Terai of Nepal), in the polite, plain register of
Maithili publications and the Sahitya Akademi standard. No other locale falls back to `mai-IN`.

- Maithili is not Hindi: it has its own verb endings, copula, postpositions and pronouns. Use them throughout:
  अछि / छथि / छल / छैक, नहि / नहि अछि, कयल गेल, होयत, सकैत छी, क / केर (genitive), केँ (object), मे, सँ, लेल,
  पर, आ (and), वा / अथवा (or), मुदा (but), जँ ... तँ (if ... then), ई (this), ओ (that), सभ (all; also the plural:
  प्रयोक्ता सभ), किछु, जे, कोनो. Adapting a Hindi sentence means rewriting it: "क्या आप वाकई इस वर्ग को हटाना चाहते
  हैं?" becomes "की अहाँ सत्ते एहि वर्ग केँ मेटाबय चाहैत छी?".
- Technical loan words are shared with Hindi in their Hindi spelling (फ़ाइल, सर्वर, डेटाबेस, लॉग इन, टेम्पलेट);
  Sanskrit-based terms where Maithili writing uses them (प्रयोक्ता, संस्करण, अनुमति).
- Short, plain sentences.

## 2. Form of address

- The user is **अहाँ**, with the matching verb forms (अहाँ ... सकैत छी, अहाँ ... चाहैत छी). Never तोँ.
- **Buttons, menu items and links**: the polite imperative in **-ू / -उ**: सहेजू, रद्द करू, मेटाउ, हटाउ, सम्पादित
  करू, जोड़ू, बनाउ, खोजू, चुनू, पठाउ, देखू, प्रकाशित करू, अपलोड करू.
- **Titles, column headings and tabs**: nouns or noun phrases: सेटिंग, अनुवाद, मुख्य स्थान, नाम.
- **Instructions**: the same polite imperative, with "कृपया" where English has "please": "आइटम चुनू आ \"OK\"
  दबाउ।", "कृपया प्राथमिकता सहेजल जाय धरि प्रतीक्षा करू।". A reference ("see ...") is "... देखू".
- **Confirmations and questions**: "की अहाँ सत्ते ... चाहैत छी?".
- **What the user can or may do**: "अहाँ ... सकैत छी", "अहाँ ... नहि सकैत छी", or impersonal: "मुख्य भाषा नहि बदलल
  जा सकैत अछि ...". "You do not have permission" is "अहाँ लग ... क अनुमति नहि अछि".
- **Messages about a result**: "... भ' गेल" / "... कयल गेल": "ड्राफ़्ट सहेजल गेल।", "फ़ाइल सफलतापूर्वक अपलोड भ' गेल।".

## 3. Script and typography

- **Devanagari**, Unicode (not Tirhuta). Nukta letters (फ़, ज़) in loan words as Hindi writes them; chandrabindu
  where Maithili writes a nasal vowel (अहाँ, सँ, केँ). The apostrophe of the elided vowel is the plain ' (भ' गेल).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after: "%name मे", "%count टा". The same for code, file names, INI
  settings and product names, which stay in Latin letters.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: the full stop is the danda "।" at the end of a Maithili sentence; a source sentence that ends in
  ":" "?" "!" or "..." keeps that mark.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Maithili | Note |
|---|---|---|
| content | सामग्री | |
| content object / object | सामग्री ऑब्जेक्ट / ऑब्जेक्ट | |
| item (list entry) | आइटम | sub items: उप-आइटम |
| node | नोड | main node: मुख्य नोड; parent node: मूल नोड |
| location | स्थान | main location: मुख्य स्थान |
| class / content class | वर्ग / सामग्री वर्ग | class group: वर्ग समूह; a PHP class: PHP क्लास |
| attribute | विशेषता | |
| datatype | डेटा प्रकार | |
| version | संस्करण | version history: संस्करण इतिहास |
| draft | ड्राफ़्ट | store draft: ड्राफ़्ट सहेजू |
| publish | प्रकाशित करू | published: प्रकाशित; publishing: प्रकाशन |
| send for publishing | प्रकाशनक लेल पठाउ | |
| discard | त्यागू | discard draft: ड्राफ़्ट त्यागू |
| archived / pending / rejected | संग्रहित / लंबित / अस्वीकृत | |
| siteaccess | साइट एक्सेस | siteaccess as a setting name stays in code |
| site, website | साइट / वेबसाइट | |
| section | खण्ड | the Exponential section; an INI section: सेक्शन |
| role | भूमिका | |
| policy | नीति | one rule of a role |
| limitation | सीमा | |
| permission | अनुमति | access denied: पहुँच अस्वीकृत |
| access | पहुँच | |
| module | मॉड्यूल | |
| function | फ़ंक्शन | |
| user / user group | प्रयोक्ता / प्रयोक्ता समूह | plural: प्रयोक्ता सभ |
| group | समूह | |
| anonymous | अनाम | |
| administrator | प्रशासक | administration (interface): प्रशासन |
| username | प्रयोक्ता नाम | login (the name): लॉगिन नाम |
| password | पासवर्ड | |
| log in / log out | लॉग इन करू / लॉग आउट करू | |
| e-mail | ईमेल | e-mail address: ईमेल पता |
| cache | कैश | clear cache: कैश साफ़ करू |
| template | टेम्पलेट | override template: ओवरराइड टेम्पलेट |
| override | ओवरराइड | |
| fallback | फ़ॉलबैक | fallback language: फ़ॉलबैक भाषा |
| design | डिज़ाइन | |
| workflow | वर्कफ़्लो | |
| trigger | ट्रिगर | |
| event | घटना | |
| trash | कूड़ादान | empty trash: कूड़ादान खाली करू; move to trash: कूड़ादान मे पठाउ |
| bookmark | बुकमार्क | |
| settings | सेटिंग | configuration: कॉन्फ़िगरेशन |
| setup | सेटअप | the Setup tab |
| layout | लेआउट | Exponential Layouts |
| block / zone | ब्लॉक / ज़ोन | |
| extension | एक्सटेंशन | |
| package | पैकेज | |
| search | खोज | to search: खोजू; search index: खोज अनुक्रमणिका |
| log | लॉग | a log file: लॉग फ़ाइल; never "log in" |
| queue | पंक्ति | job queue: जॉब पंक्ति |
| job / task | जॉब / कार्य | |
| cron | क्रॉन | |
| script | स्क्रिप्ट | |
| worker | वर्कर | |
| kernel, core | कर्नेल | |
| server | सर्वर | |
| database | डेटाबेस | |
| folder / directory | फ़ोल्डर / डायरेक्टरी | |
| file | फ़ाइल | |
| image | छवि | thumbnail: थंबनेल |
| preview | पूर्वावलोकन | |
| upload / download | अपलोड / डाउनलोड | verbs: अपलोड करू / डाउनलोड करू |
| export / import | निर्यात / आयात | |
| remove | हटाउ | take out of a list or place |
| delete | मेटाउ | destroy; deleted: मेटाओल गेल |
| edit | सम्पादित करू | editing: सम्पादन |
| editor | सम्पादक | |
| save / store | सहेजू | the verb "Store" of the forms |
| Store (shop tab) | दोकान | the shop, as "Shop" |
| cancel | रद्द करू | |
| OK | ठीक अछि | |
| apply | लागू करू | |
| back | पाछाँ | |
| next / previous | आगाँ / पाछाँक | next item: अगिला; previous item: पछिला |
| enable / disable | सक्रिय करू / निष्क्रिय करू | enabled / disabled: सक्रिय / निष्क्रिय |
| active / inactive | सक्रिय / निष्क्रिय | |
| run | चलाउ | |
| start / stop | शुरू करू / रोकू | |
| session | सत्र | |
| hidden / visible | नुकायल / देखाइ पड़ैत | hide / reveal: नुकाउ / देखाउ |
| archive | संग्रह | a file archive: आर्काइव फ़ाइल |
| order (sort) | क्रम | |
| order (shop) | ऑर्डर | |
| shop | दोकान | |
| basket | टोकरी | |
| product | उत्पाद | |
| customer | ग्राहक | |
| discount | छूट | |
| VAT | वैट | |
| price / currency | मूल्य / मुद्रा | |
| tag | टैग | an HTML tag: HTML टैग |
| translation | अनुवाद | |
| language | भाषा | main language: मुख्य भाषा |
| locale | लोकेल | |
| state | अवस्था | the object state; state group: अवस्था समूह |
| status | स्थिति | kept apart from अवस्था |
| view (button) | देखू | a module view (noun): दृश्य |
| format | प्रारूप | |
| sort | क्रमबद्ध करू | |
| filter | फ़िल्टर | |
| select | चुनू | select all: सभ चुनू; selected: चुनल |
| browse | ब्राउज़ करू | |
| copy / move | प्रतिलिपि बनाउ / स्थानांतरित करू | |
| add / create / new | जोड़ू / बनाउ / नव | |
| restore | पुनर्स्थापित करू | |
| install / uninstall | इंस्टॉल करू / अनइंस्टॉल करू | |
| subtree | सबट्री | |
| related objects | सम्बन्धित ऑब्जेक्ट | relation: सम्बन्ध |
| URL alias | URL उपनाम | |
| redirect | रीडायरेक्ट | |
| dashboard | डैशबोर्ड | |
| content structure | सामग्री संरचना | |
| media library | मीडिया पुस्तकालय | |
| identifier / ID | पहिचानकर्ता / ID | |
| priority | प्राथमिकता | |
| owner | स्वामी | |
| modified / created | संशोधित / बनाओल | |
| date / time | तिथि / समय | |
| error / warning | त्रुटि / चेतावनी | |
| help | सहायता | |
| notification | सूचना | |
| wizard | विज़ार्ड | |
| feed (RSS) | फ़ीड | |
| tab | टैब | |
| account | खाता | |
| default | पूर्वनिर्धारित | |
| toolbar | टूलबार | |
| container | कंटेनर | |
| collection | संग्रह | |
| approve, approval | स्वीकृत करू, स्वीकृति | |
| request / query | अनुरोध / क्वेरी | |
| backup | बैकअप | |
| maintenance | रखरखाव | |
| link | लिंक | |
| registration | पंजीकरण | |
| device / browser | डिवाइस / ब्राउज़र | |
| none / all | कोनो नहि / सभ | |
| yes / no | हँ / नहि | |
| name / title / description | नाम / शीर्षक / विवरण | |
| value / type / list | मान / प्रकार / सूची | |
| page / path | पृष्ठ / पथ | |
| required / optional | आवश्यक / वैकल्पिक | |
| options | विकल्प | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is दोकान, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is सहेजू.
- "Login" in the user, session and audit lists is the user's login name (लॉगिन नाम); the Login button of the error
  page is the act (लॉग इन करू).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
