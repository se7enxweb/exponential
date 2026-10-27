# Nepali (nep-NP): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026, machine-assisted, with the Hindi catalogue (`hin-IN`) as a reference for shared technical terms; a
review by native speakers is welcome.

## 1. Language and register

Standard Nepali (नेपाली) as written in Nepal, in the neutral, polite register of current Nepali software and
government websites. No other locale falls back to `nep-NP`.

- Nepali is not Hindi: the grammar, the verb forms and many everyday words differ, even where a technical loan word
  is shared. Use Nepali forms throughout: छ / छैन / हो / होइन, गर्नुहोस्, गरिएको छ, भएको, लाई, को / का / की, मा,
  बाट, सँग, र, वा, तर, यदि ... भने, यो / त्यो, सबै, केही. Never leave a Hindi word where Nepali has its own:
  सहेजें → सुरक्षित गर्नुहोस्, हटाएँ → हटाउनुहोस्, नहीं → छैन / होइन, है → छ, के लिए → का लागि, में → मा, से → बाट /
  भन्दा, और → र, या → वा, क्या → के.
- Short, plain sentences. Technical loan words in their established Nepali spelling (फाइल, फोल्डर, सर्भर, डाटाबेस,
  लग इन), Sanskrit-based words where Nepali software uses them (सम्पादन, संस्करण, अनुमति).

## 2. Form of address

- The user is **तपाईं**, with the honorific verb forms (तपाईं ... गर्न सक्नुहुन्छ). Never तँ, तिमी or हजुर.
- **Buttons, menu items and links**: the honorific imperative **-नुहोस्**: सुरक्षित गर्नुहोस्, रद्द गर्नुहोस्,
  मेटाउनुहोस्, हटाउनुहोस्, सम्पादन गर्नुहोस्, थप्नुहोस्, सिर्जना गर्नुहोस्, खोज्नुहोस्, चयन गर्नुहोस्, पठाउनुहोस्,
  हेर्नुहोस्, प्रकाशित गर्नुहोस्.
- **Titles, column headings and tabs**: nouns or noun phrases: सेटिङहरू, अनुवादहरू, मुख्य स्थान, नाम.
- **Instructions**: the same honorific imperative, with "कृपया" where English has "please": "आइटम चयन गर्नुहोस् र
  \"OK\" थिच्नुहोस्।", "कृपया प्राथमिकता सुरक्षित नभएसम्म पर्खनुहोस्।". A reference ("see ...") is "... हेर्नुहोस्".
- **Confirmations and questions**: "के तपाईं ... निश्चित हुनुहुन्छ?": "के तपाईं यो वर्ग मेटाउन निश्चित हुनुहुन्छ?".
- **What the user can or may do**: "तपाईं ... सक्नुहुन्छ", "तपाईं ... सक्नुहुन्न", or impersonal: "मुख्य भाषा
  परिवर्तन गर्न सकिँदैन ...". "You do not have permission" is "तपाईंसँग ... अनुमति छैन".
- **Messages about a result**: passive with "गरियो" / "गरिएको छ": "मस्यौदा सुरक्षित गरियो।", "फाइल सफलतापूर्वक अपलोड
  गरियो।".

## 3. Script and typography

- **Devanagari**, Unicode, in the standard spelling of the Nepal Academy (नेपाल प्रज्ञा-प्रतिष्ठान): चन्द्रबिन्दु
  where Nepali writes it (नयाँ, पाँच), the full nasal consonant before a stop where it is established (सम्पादन,
  सङ्ख्या), हलन्त at the end of a verb form (गर्नुहोस्).
- **Plural**: -हरू where the sentence needs it (सेटिङहरू, प्रयोगकर्ताहरू); not after a number: "%count वस्तु".
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after: "%name मा", "%count वटा". The same for code, file names, INI
  settings and product names, which stay in Latin letters.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: the full stop is the danda "।" at the end of a Nepali sentence; a source sentence that ends in
  ":" "?" "!" or "..." keeps that mark. Commas as in the source.
- **Numbers**: digits 0-9, not Devanagari digits, so that numbers from placeholders and written numbers look the
  same.

## 4. Glossary

Binding for the kernel and every extension.

| English | Nepali | Note |
|---|---|---|
| content | सामग्री | |
| content object / object | सामग्री वस्तु / वस्तु | |
| item (list entry) | आइटम | sub items: उप-आइटमहरू; kept apart from वस्तु (object) |
| node | नोड | main node: मुख्य नोड; parent node: अभिभावक नोड |
| location | स्थान | main location: मुख्य स्थान |
| class / content class | वर्ग / सामग्री वर्ग | class group: वर्ग समूह; a PHP class: PHP क्लास |
| attribute | विशेषता | |
| datatype | डाटा प्रकार | |
| version | संस्करण | version history: संस्करण इतिहास |
| draft | मस्यौदा | store draft: मस्यौदा सुरक्षित गर्नुहोस् |
| publish | प्रकाशित गर्नुहोस् | published: प्रकाशित; publishing: प्रकाशन |
| send for publishing | प्रकाशनका लागि पठाउनुहोस् | |
| discard | त्याग्नुहोस् | discard draft: मस्यौदा त्याग्नुहोस् |
| archived / pending / rejected | सङ्ग्रहित / विचाराधीन / अस्वीकृत | |
| siteaccess | साइट पहुँच | siteaccess as a setting name stays in code |
| site, website | साइट / वेबसाइट | |
| section | खण्ड | the Exponential section; an INI section: सेक्सन |
| role | भूमिका | |
| policy | नीति | one rule of a role |
| limitation | सीमा | |
| permission | अनुमति | access denied: पहुँच अस्वीकृत |
| access | पहुँच | |
| module | मोड्युल | |
| function | फङ्सन | a module or PHP function |
| user / user group | प्रयोगकर्ता / प्रयोगकर्ता समूह | |
| group | समूह | |
| anonymous | अज्ञात | anonymous user: अज्ञात प्रयोगकर्ता |
| administrator | प्रशासक | administration (interface): प्रशासन |
| username | प्रयोगकर्ता नाम | login (the name): लगइन नाम |
| password | पासवर्ड | |
| log in / log out | लग इन गर्नुहोस् / लग आउट गर्नुहोस् | the act (noun): लग इन |
| e-mail | इमेल | e-mail address: इमेल ठेगाना |
| cache | क्यास | clear cache: क्यास खाली गर्नुहोस् |
| template | टेम्प्लेट | override template: ओभरराइड टेम्प्लेट |
| override | ओभरराइड | |
| fallback | फलब्याक | fallback language: फलब्याक भाषा |
| design | डिजाइन | |
| workflow | कार्यप्रवाह | |
| trigger | ट्रिगर | |
| event | घटना | workflow event: कार्यप्रवाह घटना |
| trash | रद्दीटोकरी | empty trash: रद्दीटोकरी खाली गर्नुहोस्; move to trash: रद्दीटोकरीमा सार्नुहोस् |
| bookmark | पुस्तकचिनो | |
| settings | सेटिङहरू | a setting: सेटिङ; configuration: कन्फिगरेसन |
| setup | सेटअप | the Setup tab |
| layout | लेआउट | Exponential Layouts |
| block / zone | ब्लक / जोन | |
| extension | एक्सटेन्सन | |
| package | प्याकेज | |
| search | खोज | to search: खोज्नुहोस्; search index: खोज अनुक्रमणिका; indexing: अनुक्रमण |
| log | लग | a log file: लग फाइल; never "log in" |
| queue | लाइन | job queue: कार्य लाइन |
| job / task | काम / कार्य | |
| cron | क्रोन | a cron job: क्रोन काम |
| script | स्क्रिप्ट | |
| worker | वर्कर | |
| kernel, core | कर्नेल | |
| server | सर्भर | |
| database | डाटाबेस | |
| folder / directory | फोल्डर / डाइरेक्टरी | |
| file | फाइल | |
| image | तस्बिर | thumbnail: थम्बनेल |
| preview | पूर्वावलोकन | |
| upload / download | अपलोड / डाउनलोड | verbs: अपलोड गर्नुहोस् / डाउनलोड गर्नुहोस् |
| export / import | निर्यात / आयात | |
| remove | हटाउनुहोस् | take out of a list or place |
| delete | मेटाउनुहोस् | destroy; deleted: मेटाइयो |
| edit | सम्पादन गर्नुहोस् | editing: सम्पादन |
| editor | सम्पादक | the software: सम्पादक |
| save / store | सुरक्षित गर्नुहोस् | the verb "Store" of the forms |
| Store (shop tab) | पसल | the shop, as "Shop" |
| cancel | रद्द गर्नुहोस् | |
| OK | ठीक छ | |
| apply | लागू गर्नुहोस् | |
| back | पछाडि | |
| next / previous | अर्को / अघिल्लो | |
| enable / disable | सक्षम गर्नुहोस् / असक्षम गर्नुहोस् | enabled / disabled: सक्षम / असक्षम |
| active / inactive | सक्रिय / निष्क्रिय | |
| run | चलाउनुहोस् | a script, a job |
| start / stop | सुरु गर्नुहोस् / रोक्नुहोस् | |
| session | सत्र | |
| hidden / visible | लुकेको / देखिने | hide / reveal: लुकाउनुहोस् / देखाउनुहोस् |
| archive | सङ्ग्रह | a file archive: अभिलेख फाइल |
| order (sort) | क्रम | sort order: क्रमबद्ध गर्ने क्रम |
| order (shop) | अर्डर | |
| shop | पसल | |
| basket | टोकरी | |
| product | उत्पादन | |
| customer | ग्राहक | |
| discount | छुट | |
| VAT | मूल्य अभिवृद्धि कर | short: मू.अ.कर |
| price / currency | मूल्य / मुद्रा | |
| tag | ट्याग | a keyword tag; an HTML tag: HTML ट्याग |
| translation | अनुवाद | |
| language | भाषा | main language: मुख्य भाषा; interface language: इन्टरफेस भाषा |
| locale | लोकेल | |
| state | अवस्था | the object state; state group: अवस्था समूह |
| status | स्थिति | kept apart from अवस्था |
| view (button) | हेर्नुहोस् | a module view (noun): दृश्य |
| format | ढाँचा | |
| sort | क्रमबद्ध गर्नुहोस् | |
| filter | फिल्टर | |
| select | चयन गर्नुहोस् | select all: सबै चयन गर्नुहोस्; selected: चयन गरिएको |
| browse | ब्राउज गर्नुहोस् | |
| copy / move | प्रतिलिपि गर्नुहोस् / सार्नुहोस् | |
| add / create / new | थप्नुहोस् / सिर्जना गर्नुहोस् / नयाँ | |
| restore | पुनर्स्थापना गर्नुहोस् | |
| install / uninstall | स्थापना गर्नुहोस् / स्थापना हटाउनुहोस् | |
| subtree | उपवृक्ष | |
| related objects | सम्बन्धित वस्तुहरू | relation: सम्बन्ध |
| URL alias | URL उपनाम | |
| redirect | रिडाइरेक्ट | |
| dashboard | ड्यासबोर्ड | |
| content structure | सामग्री संरचना | |
| media library | मिडिया पुस्तकालय | |
| identifier / ID | पहिचानकर्ता / ID | |
| priority | प्राथमिकता | |
| owner | मालिक | |
| modified / created | परिमार्जित / सिर्जित | |
| date / time | मिति / समय | |
| error / warning | त्रुटि / चेतावनी | |
| help | सहायता | |
| notification | सूचना | |
| wizard | विजार्ड | |
| feed (RSS) | फिड | |
| tab | ट्याब | |
| account | खाता | |
| default | पूर्वनिर्धारित | |
| toolbar | उपकरणपट्टी | |
| container | कन्टेनर | |
| collection | सङ्कलन | |
| approve, approval | स्वीकृत गर्नुहोस्, स्वीकृति | |
| request / query | अनुरोध / क्वेरी | |
| backup | ब्याकअप | |
| maintenance | मर्मत | |
| link | लिङ्क | |
| registration | दर्ता | |
| device / browser | उपकरण / ब्राउजर | |
| none / all | कुनै पनि छैन / सबै | |
| yes / no | हो / होइन | |
| name / title / description | नाम / शीर्षक / विवरण | |
| value / type / list | मान / प्रकार / सूची | |
| page / path | पृष्ठ / पथ | |
| required / optional | आवश्यक / वैकल्पिक | |
| options | विकल्पहरू | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is पसल, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is सुरक्षित गर्नुहोस्.
- "Login" in the user, session and audit lists is the user's login name (लगइन नाम); the Login button of the error
  page is the act (लग इन गर्नुहोस्).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
