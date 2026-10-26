# Hindi (hin-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard Hindi (मानक हिन्दी) of India, written in Devanagari, as in the Hindi versions of common operating systems
and office software (Microsoft terminology and style guide for Hindi). The tone is neutral, concise and
professional. No other locale falls back to this catalogue.

- Prefer the established IT word over a coined Sanskrit term: "फ़ाइल", "फ़ोल्डर", "सर्वर", "डेटाबेस", "कैश",
  "टेम्पलेट" are what Hindi users read every day; "संचिका" or "द्रुतिका" are not used.
- Prefer a plain Hindi word where one is established in software: "सहेजें", "हटाएँ", "खोजें", "अनुमति",
  "उपयोगकर्ता", "भाषा", "सेटिंग्स".
- No colloquial forms, no Urdu-only or Hinglish forms ("डिलीट करो", "सेव करें" are wrong), no needless English in
  Latin script. Codes, file names, product names and abbreviations (URL, HTML, PHP, ID, AI, VAT in a code) stay in
  Latin script.

## 2. Form of address

The user is addressed with the respectful **आप**, everywhere.

- **Buttons, menu items, links and action labels** use the polite imperative (the आप form ending in -ें/-एँ),
  object first: "सहेजें", "हटाएँ", "संपादित करें", "जोड़ें", "रद्द करें", "नया बनाएँ", "परिवर्तन लागू करें",
  "प्रकाशन के लिए भेजें", "सभी का चयन करें", "चयनित निकालें", "ड्राफ़्ट सहेजें", "ट्रैश खाली करें".
- **Titles, column headings and tabs** are nouns or noun phrases: "सामग्री संरचना", "उपयोगकर्ता खाते",
  "लेआउट प्रकार", "सूचना सेटिंग्स", "मुख्य भाषा", "ऑब्जेक्ट अवस्थाएँ".
- **Requests and instructions in sentences**: "कृपया" with the polite imperative:
  - "कृपया एक स्थान चुनें।"
  - "कृपया एक मान्य ईमेल पता दर्ज करें।"
  - "कनेक्शन जाँचें और फिर से प्रयास करें।"
- **Questions and confirmations**: "क्या आप वाकई ... चाहते हैं?":
  - "क्या आप वाकई इस वर्ग को निकालना चाहते हैं?"
  - "क्या आप वाकई इस भाषा को निकालना चाहते हैं?"
  The verb agrees with आप in the respectful plural (चाहते हैं), which is the gender-neutral convention.
- **Statements about the user and error messages**: आप, आपका/आपकी/आपके:
  - "आपको वर्ग निकालने की अनुमति नहीं है।"
  - "आप उपयोगकर्ता खातों वाले अंतिम वर्ग को नहीं निकाल सकते।"
  - "“रद्द करें” बटन पर क्लिक करें और केवल वे स्थान निकालने का प्रयास करें जिन्हें निकालने की आपको अनुमति है।"
  - "आप content.ini में संस्करण इतिहास की सेटिंग्स बदल सकते हैं, ..."
- **Tooltips that describe a click**: "... के लिए क्लिक करें": "संदर्भ मेनू दिखाने के लिए आइकन पर क्लिक करें।"
- **Status messages** are passive or impersonal: "ड्राफ़्ट सफलतापूर्वक सहेजा गया।", "कोई क्रॉन जॉब रन नहीं मिला",
  "आइटम “%1” में ले जाया गया।", "अनुक्रमण पूरा हुआ।"
- Not allowed: तुम/तू and their verb forms (सहेजो, हटाओ, सहेज, करो), the bare stem or infinitive as a button
  ("सहेजना", "हटाना"), the extra-formal -इए form on buttons ("सहेजिए", "कीजिए"), English verbs with करें where a
  Hindi verb is established ("सेव करें", "डिलीट करें", "कैंसल करें").

## 3. Script and typography

- **Script**: Devanagari. Use the nukta in loanwords as Microsoft does: फ़ाइल, फ़ोल्डर, फ़िल्टर, ज़ोन, डिज़ाइन,
  रीफ़्रेश. Use ऑ for the English open o: ऑब्जेक्ट, ऑर्डर, ऑडिट. Chandrabindu where no vowel sign stands
  above the line (हटाएँ, जाएँ, हाँ, ऊँचाई), anusvara otherwise (करें, में, हैं). Standard spelling of the Central
  Hindi Directorate: गए/गई/लिए, not गये/गयी/लिये.
- **No capital letters** in Devanagari. Latin product names and codes keep their spelling (Exponential, Solr, PHP,
  URL). Do not imitate English title case by any means.
- **Punctuation**: the sentence ends with the पूर्ण विराम "।" (U+0964), directly after the last word with no space.
  "?" and "!" as in English. Comma, colon and semicolon as in English, no space before them. Never a Latin full
  stop "." at the end of a Hindi sentence; inside codes, file names, URLs and numbers the "." stays.
- **Quotation marks**: “…” in running text: “%name”. Straight quotes only inside HTML attributes, JavaScript and
  where the source quotes code or a value that is typed exactly.
- **Postpositions** are written as separate words after a noun or placeholder: "%name को ले जाएँ",
  "%name का शीर्षक", "%count ऑब्जेक्ट". A placeholder is never inflected and never glued to a following word; Hindi needs no case
  ending on it, so always a space after it.
- **Gender and agreement with a placeholder**: the gender of %name is unknown. Put a known noun in front so the
  verb agrees with that noun: "सामग्री ऑब्जेक्ट %id मौजूद नहीं है, या हटा दिया गया है।",
  "फ़ाइल %filename सहेजना विफल रहा।"
- **Plural forms**: the source only has "item(s)". Never write "आइटम(एँ)". Use a label form: "ऑब्जेक्ट: %count",
  "मिले ऑब्जेक्ट: %count", "विराम पर भाग: %count"; or a construction in which the noun does not change after a number
  ("%count आइटम").
- **Numbers**: Western digits 0–9 (no Devanagari digits), a decimal point, not a comma. Units stay as in the source
  (MB, KB, px). Time after a number: "सेकंड", "मिनट", "घंटे", "दिन"; "%n मिनट पहले".
- **Word order**: Hindi is subject-object-verb. Rearrange the sentence; do not keep the English order. Do not
  split a sentence across placeholders in a way the source does not.
- **Compounds with a code or English word**: separate words, no hyphen ("URL उपनाम", "INI फ़ाइल", "XML टैग",
  "HTML टैग"). A hyphen only where Hindi writes one (उप-आइटम, केवल-पढ़ने योग्य).

## 4. Glossary

Binding for the kernel and every extension. Gender is given for loanwords where agreement depends on it
(m = masculine, f = feminine).

| English | Hindi | Note |
|---|---|---|
| content | सामग्री | f |
| content object / object | सामग्री ऑब्जेक्ट / ऑब्जेक्ट | m; pl. ऑब्जेक्ट (unchanged) |
| item / items | आइटम | m; pl. आइटम; list entry in general |
| sub items / children | उप-आइटम / चाइल्ड आइटम | |
| parent | पैरेंट | parent node: पैरेंट नोड |
| node | नोड | m; main node: मुख्य नोड |
| location / main location | स्थान / मुख्य स्थान | the placement of a node |
| content class / class | सामग्री वर्ग / वर्ग | m; a PHP class: PHP क्लास |
| class group | वर्ग समूह | |
| attribute | विशेषता | f; pl. विशेषताएँ |
| datatype | डेटा प्रकार | m |
| version | संस्करण | m; version history: संस्करण इतिहास |
| draft | ड्राफ़्ट | m; Store draft: ड्राफ़्ट सहेजें; Discard draft: ड्राफ़्ट त्यागें |
| publish / published / publishing | प्रकाशित करें / प्रकाशित / प्रकाशन | Send for publishing: प्रकाशन के लिए भेजें |
| translation / language | अनुवाद / भाषा | |
| main language / content language | मुख्य भाषा / सामग्री भाषा | interface language: इंटरफ़ेस भाषा |
| fallback (language) | फ़ॉलबैक भाषा | fallback chain: फ़ॉलबैक श्रृंखला |
| fallback (template, setting) | फ़ॉलबैक | the one used when the first is missing |
| override / overridden | ओवरराइड / ओवरराइड किया गया | m; override template: ओवरराइड टेम्पलेट |
| siteaccess | साइट एक्सेस | m; pl. साइट एक्सेस |
| site / website | साइट / वेबसाइट | f |
| design (eZ design) | डिज़ाइन | m |
| structure | संरचना | f |
| Content structure | सामग्री संरचना | |
| Media library | मीडिया लाइब्रेरी | |
| Dashboard | डैशबोर्ड | |
| template | टेम्पलेट | m |
| alias / URL alias | उपनाम / URL उपनाम | |
| wildcard (URL) | वाइल्डकार्ड | |
| redirect / forward (URL) | रीडायरेक्ट / अग्रेषित करें | noun "forward": अग्रेषण; redirection: रीडायरेक्शन |
| cache | कैश | m; Clear cache: कैश साफ़ करें |
| workflow | वर्कफ़्लो | m |
| workflow event / event | वर्कफ़्लो ईवेंट / ईवेंट | m |
| trigger | ट्रिगर | m |
| role | भूमिका | f |
| policy | नीति | f; pl. नीतियाँ |
| limitation (policy) | सीमा | f; pl. सीमाएँ |
| permission | अनुमति | f |
| access / access denied | पहुँच / पहुँच अस्वीकृत | f |
| user / user group | उपयोगकर्ता / उपयोगकर्ता समूह | |
| user account / user accounts | उपयोगकर्ता खाता / उपयोगकर्ता खाते | |
| anonymous user | अनाम उपयोगकर्ता | |
| administrator / admin interface | व्यवस्थापक / व्यवस्थापन इंटरफ़ेस | |
| user interface | उपयोगकर्ता इंटरफ़ेस | |
| log in / log out | लॉग इन करें / लॉग आउट करें | never confused with "log" (a log file) |
| login (field) / username | लॉगिन नाम / उपयोगकर्ता नाम | |
| password | पासवर्ड | m |
| e-mail | ईमेल | m; e-mail address: ईमेल पता |
| section | अनुभाग | the Exponential section, a setting of each object; an INI section is सेक्शन (section 5) |
| object state / state | ऑब्जेक्ट अवस्था / अवस्था | f; state group: अवस्था समूह; only the Exponential object state, never "स्थिति" here. A technical state (of a job, process, index) is स्थिति |
| status | स्थिति | f |
| relation | संबंध | m |
| related objects / reverse related | संबंधित ऑब्जेक्ट / इस ऑब्जेक्ट से संबंधित ऑब्जेक्ट | |
| trash | ट्रैश | m; Empty trash: ट्रैश खाली करें |
| bookmark / bookmarks | बुकमार्क | m |
| owner / creator / modifier | स्वामी / निर्माता / संशोधक | |
| created / modified | बनाया गया / संशोधित | column headings |
| layout (Exponential Layouts) | लेआउट | m; layout type: लेआउट प्रकार; the name Exponential Layouts stays |
| layout (directory or release structure) | संरचना | |
| block / zone | ब्लॉक / ज़ोन | m |
| setting / settings | सेटिंग / सेटिंग्स | f; configuration values |
| Setup (tab) | सेटअप | |
| configuration / configure | कॉन्फ़िगरेशन / कॉन्फ़िगर करें | |
| extension | एक्सटेंशन | m |
| package | पैकेज | m |
| module | मॉड्यूल | m |
| view (module view, noun) | दृश्य | "Module view": मॉड्यूल दृश्य |
| View (button) | देखें | |
| function (module, PHP) | फ़ंक्शन | m |
| search (noun / verb) | खोज / खोजें | search engine: खोज इंजन |
| search index | खोज अनुक्रमणिका | f |
| index (verb) / reindex | अनुक्रमित करें / फिर से अनुक्रमित करें | noun "reindexing": पुनः अनुक्रमण |
| index (database) | इंडेक्स | m |
| index file | इंडेक्स फ़ाइल | |
| cronjob | क्रॉन जॉब | m |
| schedule | शेड्यूल | m; verb: शेड्यूल करें |
| job / queue | जॉब / कतार | job queue: जॉब कतार |
| worker | वर्कर | m |
| task | कार्य | m |
| lane (queue) | लेन | f |
| log / log file | लॉग / लॉग फ़ाइल | m; never the verb "log in" |
| rotation / rotate (log) | रोटेशन / रोटेट करें | |
| audit / audit trail | ऑडिट / ऑडिट ट्रेल | |
| notification | सूचना | f |
| notice | नोटिस | m |
| collaboration | सहयोग | |
| preview | पूर्वावलोकन | |
| ID / remote ID / identifier | ID / रिमोट ID / पहचानकर्ता | |
| file / folder / directory | फ़ाइल / फ़ोल्डर / निर्देशिका | फ़ाइल f, फ़ोल्डर m |
| image / media | छवि / मीडिया | |
| thumbnail | थंबनेल | m |
| upload / download | अपलोड करें / डाउनलोड करें | nouns: अपलोड / डाउनलोड |
| export / import | निर्यात करें / आयात करें | nouns: निर्यात / आयात |
| shop | शॉप | f |
| Store (shop tab) | स्टोर | every other "Store" is the verb सहेजें |
| basket | बास्केट | f |
| order (shop) | ऑर्डर | m; order status: ऑर्डर स्थिति |
| order (sorting) | क्रम | m; sort order: सॉर्ट क्रम |
| customer / product | ग्राहक / उत्पाद | |
| checkout | चेकआउट | |
| VAT / VAT type | वैट / वैट प्रकार | |
| discount / discount rule | छूट / छूट नियम | |
| rule | नियम | |
| price / currency | मूल्य / मुद्रा | |
| payment gateway / gateway | भुगतान गेटवे / गेटवे | |
| wish list | इच्छा सूची | |
| newsletter | न्यूज़लेटर | m |
| subscriber / subscription / subscribe | सदस्य / सदस्यता / सदस्यता लें | unsubscribe: सदस्यता समाप्त करें |
| tag (keyword, eztags) / keyword | टैग / कीवर्ड | m |
| tag (HTML/XML) / custom tag | HTML टैग, XML टैग / कस्टम टैग | always with the qualifier, to keep it apart from a keyword tag |
| locale | लोकेल | m |
| localization | स्थानीयकरण | |
| subtree | सबट्री | f |
| identifier (class, attribute) | पहचानकर्ता | |
| move / copy / swap | ले जाएँ / कॉपी करें / अदला-बदली करें | move in tree, group or state: ले जाएँ |
| embedding / chunk / vector | एम्बेडिंग / खंड / वेक्टर | |
| embed / embedded (object in text) | एम्बेड करें / एम्बेड किया गया | |
| prompt / model / token | प्रॉम्प्ट / मॉडल / टोकन | |
| AI | AI | |
| provider | प्रदाता | |
| deploy / deployment / release | परिनियोजित करें / परिनियोजन / रिलीज़ | |
| enable / disable | सक्षम करें / अक्षम करें | enabled / disabled: सक्षम / अक्षम |
| active / inactive | सक्रिय / निष्क्रिय | |
| error / warning | त्रुटि / चेतावनी | |
| required / optional | आवश्यक / वैकल्पिक | |
| default | डिफ़ॉल्ट | |
| priority | प्राथमिकता | |
| sort / ascending / descending | सॉर्ट करें / आरोही / अवरोही | |
| visible / hidden | दृश्यमान / छिपा हुआ | |
| hide / reveal, show | छिपाएँ / दिखाएँ | |
| expand / collapse | विस्तृत करें / संक्षिप्त करें | |
| OK / Cancel | ठीक है / रद्द करें | |
| Yes / No | हाँ / नहीं | |
| Back / Next / Previous / Close | वापस / अगला / पिछला / बंद करें | |
| select / browse | चुनें / ब्राउज़ करें | |
| select all / deselect all, select none | सभी का चयन करें / सभी का चयन हटाएँ | |
| apply / Apply changes | लागू करें / परिवर्तन लागू करें | |
| save / store | सहेजें | |
| discard | त्यागें | |
| edit | संपादित करें | |
| remove | निकालें | take out of a list, group or place; for records that are destroyed see section 5 |
| delete | हटाएँ | destroy |
| new / create / add | नया / बनाएँ / जोड़ें | Create new: नया बनाएँ |
| refresh / update / reset | रीफ़्रेश करें / अपडेट करें / रीसेट करें | |
| run (verb / noun) | चलाएँ / रन | a program, cron or index run: रन (m) |
| dry run | परीक्षण रन | |
| self-test | स्व-परीक्षण | |
| debug | डीबग | |
| restore | पुनर्स्थापित करें | |
| assign / assignment | असाइन करें / असाइनमेंट | |
| approve / approval / approved / approver | अनुमोदित करें / अनुमोदन / अनुमोदित / अनुमोदक | |
| accept / accepted | स्वीकार करें / स्वीकृत | |
| reject, deny / rejected | अस्वीकार करें / अस्वीकृत | |
| pending | लंबित | |
| archived (version, object, order) | संग्रहीत | |
| Archive / Unarchive (button) | संग्रहीत करें / संग्रहण से निकालें | |
| archive (place, menu item, log archive) | संग्रहण | m |
| archive (file .tar, .zip, .phar, .ezpkg) | आर्काइव | m |
| collection | संग्रह | m; kept apart from संग्रहण (archive) |
| statistics / system information | आँकड़े / सिस्टम जानकारी | |
| database | डेटाबेस | m |
| path | पथ | |
| handler / wizard | हैंडलर / विज़ार्ड | |
| backend / endpoint | बैकएंड / एंडपॉइंट | |
| method / listener / controller | विधि / लिसनर / कंट्रोलर | |
| allowlist | अनुमति सूची | |
| read-only | केवल-पढ़ने योग्य | |
| argument | तर्क | |
| slot / facets / elevate (Solr) | स्लॉट / फ़ेसेट / एलिवेट | |
| filter | फ़िल्टर | m |
| details | विवरण | |
| description | वर्णन | kept apart from विवरण (details) |
| title / name | शीर्षक / नाम | |
| tick (checkbox) | चिह्नित करें | |
| container (class flag "Is container") | कंटेनर | m |
| container (CSS, Bootstrap) | कंटेनर | |
| charset | वर्ण सेट | |
| license | लाइसेंस | m; the file name LICENSE stays |
| privilege / grant (database) | विशेषाधिकार / प्रदान करें | |
| format (file, data) | फ़ॉर्मेट | m |
| formatting (text) | स्वरूपण | |
| linked / common | लिंक किया गया / सामान्य | |
| flags (class flags) | फ़्लैग | |
| teaser / slug | टीज़र / स्लग | |
| placeholder / preset | प्लेसहोल्डर / प्रीसेट | |
| information collection / collector | जानकारी संग्रह / जानकारी संग्राहक | collected information: एकत्र की गई जानकारी (not एकत्रित) |
| overview / summary | अवलोकन / सारांश | |
| tab | टैब | m |
| rich text | रिच टेक्स्ट | |
| feed | फ़ीड | f |
| session | सत्र | |
| scope | दायरा | |
| thread / chain | थ्रेड / श्रृंखला | |
| batch | बैच | m |
| pool | पूल | |
| schema / registry / manifest | स्कीमा / रजिस्ट्री / मैनिफ़ेस्ट | |
| stylesheet / theme / icon | स्टाइलशीट / थीम / आइकन | |
| port / digest / profile | पोर्ट / डाइजेस्ट / प्रोफ़ाइल | |
| core (Solr) | कोर | |
| child documents (Solr) | चाइल्ड दस्तावेज़ | |
| editor (person) | संपादक | |
| editor (software) | एडिटर | template editor: टेम्पलेट एडिटर |
| Editor (siteaccess type) | संपादक साइट एक्सेस | |
| page / pages | पृष्ठ | |
| link | लिंक | m |
| date / time | दिनांक / समय | |
| type | प्रकार | |
| group | समूह | |
| list | सूची | |
| value / field | मान / फ़ील्ड | |
| number / count | संख्या / गिनती | |
| width / height / size | चौड़ाई / ऊँचाई / आकार | |
| server / client | सर्वर / क्लाइंट | |
| request / response | अनुरोध / प्रतिसाद | |
| pattern | पैटर्न | |
| repository / maintainer | रिपॉज़िटरी / अनुरक्षक | |
| install / uninstall / installation | इंस्टॉल करें / अनइंस्टॉल करें / इंस्टॉलेशन | |
| checksum / quarantine | चेकसम / क्वारंटीन | |
| mapping / resolver / checkpoint | मैपिंग / रिज़ॉल्वर / चेकपॉइंट | |
| resolve (path, name) / resolvable | रिज़ॉल्व करें / रिज़ॉल्व करने योग्य | |
| script | स्क्रिप्ट | f |
| revoke | निरस्त करें | |
| label | लेबल | m |
| plugin / kernel | प्लग-इन / कर्नेल | |

## 5. Decisions after the translation

These decisions complete the glossary; where a term appears in both, the two agree.

| English | Hindi | Distinction |
|---|---|---|
| position (place in an ordered list: bookmarks, policies, states, extensions, blocks in a zone, drag and drop) | क्रमांक | m; "Position %position of %count": "क्रमांक %count में से %position"; "Set position": "क्रमांक सेट करें". Never स्थिति |
| position (on screen, at the cursor, geographic) | स्थान | "चयनित स्थान पर तालिका सम्मिलित करें।"; never स्थिति |
| INI section (`[Section]` of an .ini file) | सेक्शन | m; "INI सेक्शन"; kept apart from the Exponential section अनुभाग. A `[Section]` written as code stays literal |
| state (technical: of a job, process, index, release, maintenance, link) | स्थिति | the Exponential object state stays अवस्था |
| storage (var/storage, storage directory, storage files, file storage) | स्टोरेज | m; storage key: स्टोरेज कुंजी. Not संग्रहण (that is archive) and not भंडारण; "संग्रहीत" for a stored file stays |
| split directory (storage/…-split), split check, split backend | स्प्लिट निर्देशिका / स्प्लिट जाँच / स्प्लिट बैकएंड | not विभाजन |
| transaction (database) | ट्रांज़ैक्शन | m |
| transaction (payment) | लेनदेन | written without a hyphen |
| statement (SQL) | स्टेटमेंट | m; कथन only for a statement in prose (license statement) |
| variable (template, PHP, server, environment) | चर | m; environment variable: परिवेश चर; global variable: ग्लोबल चर. Not वेरिएबल outside code |
| environment (dev/test/prod, server environment) | परिवेश | m; not वातावरण or पर्यावरण |
| development (environment, server, machine, settings, Rapid Application Development) | डेवलपमेंट | also the badge label "Development" |
| development (the activity: extension, PHP, CMS development) | विकास | |
| production / staging / test (environment) | उत्पादन / स्टेजिंग / परीक्षण | the badges DEV, TEST, STAGE, PROD stay in Latin |
| fetch (template fetch, in prose) | फ़ेच | m; fetch function: फ़ेच फ़ंक्शन; fetch alias: फ़ेच उपनाम. Latin `fetch` only inside code |
| extension point | एक्सटेंशन पॉइंट | m; "extends X" stays "X का विस्तार करता है" |
| implements (interface) | ... को लागू करता है | not कार्यान्वित |
| secret (OAuth client secret) | सीक्रेट | m; client secret: क्लाइंट सीक्रेट |
| secret (secret values in settings) | गोपनीय मान | not गुप्त मान |
| redacted / masked (output) | छिपाया गया | `[redacted]` stays literal |
| purge (from the trash; objects, orders, audit entries) | स्थायी रूप से हटाएँ | "Audit purge": "ऑडिट प्रविष्टियाँ स्थायी रूप से हटाई गईं"; not शुद्धिकरण |
| purge (cached pages) | साफ़ करें | "Purge all pages": "सभी पृष्ठ साफ़ करें" |
| purge (cluster purge cronjob, InnoDB purge) | पर्ज | m; the technical name of a process |
| column (data: lists and tables in the admin, database, CSV) | कॉलम | m |
| column (layout: text, table in the editor, grid, menu, matrix) | स्तंभ | m; "2 columns": "2 स्तंभ" |
| variant / variation (image, file) | वेरिएंट | m; image variants: छवि वेरिएंट. A language or region variation: भिन्नता |
| timeout / timed out | समय-सीमा / समय-सीमा समाप्त | f; not टाइमआउट |
| probe (live probe, health probe) | जाँच | f |
| check (noun / verb) | जाँच / जाँचें | link check: लिंक जाँच; consistency check: संगति जाँच (not संगतता) |
| test (noun) | परीक्षण | kept apart from जाँच (probe, check) |
| validation / validate / valid / invalid | सत्यापन / सत्यापित करें / मान्य / अमान्य | |
| on the fly (PDF export) | आवश्यकता पर | not तुरंत |
| generate / regenerate (files, keys, PDFs, autoloads, caches, image aliases) | जनरेट करें / फिर से जनरेट करें | बनाएँ / बनाया गया only where the source says "create", or for a name made automatically |
| sign in / sign out | साइन इन करें / साइन आउट करें | follows the source: "Log in" stays लॉग इन करें; one screen uses only one of the two |
| two-step sign-in | दो-चरणीय साइन इन | |
| last name | अंतिम नाम | never उपनाम (that is alias) |
| ZIP code | पिन कोड | |
| Desc. / Asc. | अवरो. / आरो. | the dot marks the abbreviation |
| entry / record / row / table | प्रविष्टि / रिकॉर्ड / पंक्ति / तालिका | |
| passage / query (embeddings) | अंश / क्वेरी | the e5 prefixes `query:` / `passage:` stay literal |
| process (operating system, workflow) / child process | प्रक्रिया / चाइल्ड प्रक्रिया | f; not प्रोसेस |
| maintenance / maintenance mode / maintenance window | रखरखाव / रखरखाव मोड / रखरखाव अवधि | |
| cronjob part | क्रॉन जॉब भाग | |
| Pause / Paused / Resume (cronjob parts, queue) | विराम लगाएँ / विराम पर / फिर से शुरू करें | not रोकें |
| finding (check result) | निष्कर्ष | |
| API key | API कुंजी | pl. API कुंजियाँ |
| orphaned / orphan files | अनाथ / अनाथ फ़ाइलें | |
| submit / submission | सबमिट करें / सबमिशन | प्रेषण only for a form sent to the information collector, where the source says "sending" |
| grip (drag handle) | हैंडल | |
| hit / miss / hit rate | हिट / मिस / हिट दर | |

- **Remove and delete**: remove is निकालें, delete is हटाएँ. Where "Remove" destroys a record that cannot be put
  back (sessions, users, orders, VAT types, states, discount rules), हटाएँ is accepted. The button, its tooltip and
  its confirmation on one screen use the same verb: "Remove section?" is "अनुभाग निकालें?".
- **Siteaccess** in running text is "साइट एक्सेस"; the Latin word stays only in paths and setting names
  (`settings/siteaccess/<siteaccess>/`).
