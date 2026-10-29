# Sanskrit (san-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Modern standard Sanskrit (संस्कृतम्) in Devanagari, as used in Sanskrit software localisation and in Sanskrit news
(Sudharma, DD News Sanskrit): classical grammar, plain and short sentences, the neutral polite register. The locale's
`LanguageName` is `संस्कृत भाषा`. No other locale falls back to `san-IN`.

- Verbs follow Paninian grammar; nouns carry their case endings (the nominative for labels, the locative for "in",
  the ablative for "from", the instrumental for "with", the genitive for "of").
- Technical words: the established Sanskrit computing terms (सञ्चिका file, सञ्चिकाधानी folder, दत्तांशः data,
  गुप्तशब्दः password, जालस्थलम् website, विद्युत्पत्रम् e-mail). For a modern notion Sanskrit has no word for, Hindi
  (`hin-IN`) serves only to check the term; the Sanskrit original of the Hindi word is used, and a new compound is
  built from Sanskrit roots only when it is transparent. Never leave an English word unless it is a product name or
  code.
- Compounds (samasa) are kept short, two members where possible; a long compound is replaced by a phrase with case
  endings.
- **Sandhi is applied conservatively.** Inside a word and in the usual compounds sandhi is written; between a Sanskrit
  word and a placeholder, a Latin word, a number or a code it is not, and the two stand apart with a space. A final
  consonant before such a word stays as it is (उपयोक्ता, not a joined form). The word-final m is written as म् (not as an
  anusvara) in dictionary form; before a stop inside a word the class nasal is written (सञ्चिका, अङ्कः).

## 2. Form of address

- The user is addressed politely in the third person with भवान् / भवती only where a pronoun is unavoidable; most
  sentences have none.
- **Buttons, menu items and links**: the action noun (a kridanta in -अनम्, -तिः), in the nominative, as Sanskrit
  software does: संरक्षणम् (save), निरसनम् (delete), रद्दीकरणम् (cancel), सम्पादनम् (edit), योजनम् (add),
  निर्माणम् (create), अन्वेषणम् (search), चयनम् (select), प्रेषणम् (send), दर्शनम् (view), प्रकाशनम् (publish),
  आरोपणम् (upload), अवरोपणम् (download). This avoids the choice between a command and a request: a button names the
  action.
- **Titles, column headings and tabs**: nouns or noun phrases: विन्यासः, अनुवादः, मुख्यस्थानम्, नाम.
- **Instructions**: the polite imperative (लोट्, third person singular, भवान् understood) with कृपया where the source
  has please or gives an instruction: "कृपया वस्तु चिनोतु, ततः \"सम्मतम्\" इति नुदतु."
- **Confirmations and questions**: the gerundive with the question word किम्: "किम् एतद् विभागं निरसितुम् इच्छति?" or "किम्
  अयं विभागः निरसनीयः?"; the second form is the rule where no actor is named.
- **What the user can or may do**: "... शक्यते" (can), "... न शक्यते" (cannot); "You do not have permission" is
  "भवतः अनुमतिः नास्ति".
- **Messages about a result**: the passive past participle, in the neuter or agreeing with its subject: "प्रारूपं
  संरक्षितम्.", "सञ्चिका आरोपिता.". Messages in the passive are preferred to the active for results.

## 3. Script and typography

- **Devanagari** (U+0900-U+097F). Conjuncts, the virama, the anusvara, the visarga and the avagraha ऽ are written as
  Unicode defines them. Vedic accents are not used.
- **Sentence end**: the danda **।** (U+0964) where the source has a full stop, with no space before it and one after
  it inside a text. The double danda ॥ is not used. Question and exclamation marks are the Latin "?" and "!"; colons
  after labels and ellipses are kept as in the source.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder, so no case ending follows it
  directly ("सङ्ख्या: %count"). Sanskrit words are chosen so that the placeholder stands in a nominative or an
  uninflected position. The same for Latin words, code and product names inside a Sanskrit sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (Devanagari digits are not used in the interface).

## 4. Glossary

Binding for the kernel and every extension.

| English | Sanskrit | Note |
|---|---|---|
| content | विषयवस्तु | content object: विषयवस्तुपदार्थः |
| object | पदार्थः | |
| node | ग्रन्थिः | main node: मुख्यग्रन्थिः parent node: मूलग्रन्थिः |
| location | स्थानम् | main location: मुख्यस्थानम् |
| item (list entry) | अङ्कनम् | |
| tree | वृक्षः | tree menu: वृक्षसूची content structure tree: विषयवृक्षः |
| class | वर्गः | the content class: विषयवर्गः class group: वर्गसमूहः |
| attribute | गुणधर्मः | |
| datatype | दत्तांशप्रकारः | |
| version | संस्करणम् | version history: संस्करणेतिहासः |
| draft | प्रारूपम् | store draft: प्रारूपसंरक्षणम् |
| publish | प्रकाशनम् | published: प्रकाशितम् |
| send for publishing | प्रकाशनाय प्रेषणम् | |
| discard | परित्यागः | discard draft: प्रारूपपरित्यागः |
| archived / pending / rejected | अभिलेखागारस्थम् / प्रतीक्षमाणम् / प्रत्याख्यातम् | |
| siteaccess | जालस्थलप्रवेशः | siteaccess as a setting name stays in code |
| site, website | जालस्थलम् | |
| section | विभागः | the Exponential section; an INI section: INI विभागः |
| role | भूमिका | |
| policy | नीतिः | |
| limitation | सीमा | |
| permission | अनुमतिः | access denied: प्रवेशः निषिद्धः |
| module | घटकः | |
| function | कार्यम् | a PHP function: PHP function |
| user / user group | उपयोक्ता / उपयोक्तृसमूहः | |
| group | समूहः | |
| anonymous | अज्ञातः | anonymous user: अज्ञातोपयोक्ता |
| administrator | प्रशासकः | administration (interface): प्रशासनम् |
| username | उपयोक्तृनाम | |
| password | गुप्तशब्दः | |
| log in / log out | प्रवेशः / निर्गमः | |
| e-mail | विद्युत्पत्रम् | e-mail address: विद्युत्पत्रसङ्केतः |
| cache | सञ्चयस्मृतिः | clear cache: सञ्चयस्मृतिशुद्धिः |
| template | प्रतिमानम् | override template: अधिलेखनप्रतिमानम् |
| override | अधिलेखनम् | |
| fallback | विकल्पः | fallback language: विकल्पभाषा |
| design | रूपकल्पना | |
| workflow | कार्यप्रवाहः | |
| trigger | प्रेरकः | |
| event | घटना | |
| trash | कचरापेटिका | empty trash: कचरापेटिकाशुद्धिः move to trash: कचरापेटिकां प्रति नयनम् |
| bookmark | पृष्ठचिह्नम् | |
| settings | विन्यासः | configuration: विन्यासः |
| setup | स्थापना | the Setup tab |
| layout | विन्यासरचना | Exponential Layouts stays |
| block / zone | खण्डः / क्षेत्रम् | |
| extension | विस्तारः | |
| package | सङ्कुलम् | |
| search | अन्वेषणम् | search index: अन्वेषणसूचिः |
| log | अभिलेखः | a log file: अभिलेखसञ्चिका never "log in" |
| queue | पङ्क्तिः | job queue: कार्यपङ्क्तिः |
| job / task | कार्यम् | |
| cron | cron | |
| script | script | |
| kernel, core | अन्तःसारः | |
| server | सेवकः | |
| database | दत्तांशकोशः | |
| folder / directory | सञ्चिकाधानी | |
| file | सञ्चिका | |
| image | चित्रम् | thumbnail: लघुचित्रम् |
| preview | पूर्वावलोकनम् | |
| upload / download | आरोपणम् / अवरोपणम् | |
| export / import | निर्यातः / आयातः | |
| remove | अपसारणम् | take out of a list or place |
| delete | निरसनम् | destroy; deleted: निरस्तम् |
| edit | सम्पादनम् | |
| editor | सम्पादकः | person / software: सम्पादकः / सम्पादनसाधनम् |
| save / store | संरक्षणम् | the verb "Store" of the forms |
| Store (shop tab) | आपणः | the shop, as "Shop" |
| cancel | रद्दीकरणम् | |
| OK | सम्मतम् | |
| apply | प्रयोगः | |
| back | पुनः | |
| next / previous | अग्रिमम् / पूर्वम् | |
| enable / disable | सक्रियीकरणम् / निष्क्रियीकरणम् | enabled / disabled: सक्षमम् / अक्षमम् |
| active / inactive | सक्रियम् / निष्क्रियम् | |
| run | सञ्चालनम् | |
| start / stop | प्रारम्भः / विरामः | |
| session | सत्रम् | |
| hidden / visible | गुप्तम् / दृश्यम् | hide / reveal: गोपनम् / प्रकटनम् |
| archive | अभिलेखागारः | |
| order (sort) | क्रमः | sort order: क्रमव्यवस्था |
| order (shop) | आदेशः | |
| shop | आपणः | |
| basket | पेटिका | |
| product | उत्पादः | |
| customer | क्रेता | |
| discount | मूल्यन्यूनता | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | मूल्यम् / मुद्रा | |
| tag | सङ्केतपदम् | |
| translation | अनुवादः | |
| language | भाषा | main language: मुख्यभाषा |
| locale | स्थानीयभाषा | |
| state | अवस्था | the object state |
| status | स्थितिः | |
| view (button) | दर्शनम् | a module view (noun): दृश्यम् |
| format | प्रारूपः | |
| sort | क्रमणम् | |
| filter | छानकम् | |
| select | चयनम् | select all: सर्वचयनम् selected: चयनितम् |
| copy / move | प्रतिलिपिः / स्थानान्तरणम् | |
| add / create / new | योजनम् / निर्माणम् / नूतनम् | |
| restore | पुनःस्थापनम् | |
| install / uninstall | स्थापनम् / अस्थापनम् | |
| related objects | सम्बद्धपदार्थाः | relation: सम्बन्धः |
| dashboard | नियन्त्रणफलकम् | |
| identifier / ID | अभिज्ञापकम् / ID | |
| priority | प्राथमिकता | |
| owner | स्वामी | |
| modified / created | परिवर्तितम् / निर्मितम् | |
| date / time | दिनाङ्कः / समयः | |
| error / warning | त्रुटिः / चेतावनी | |
| help | साहाय्यम् | |
| notification | सूचना | |
| account | खातम् | |
| default | पूर्वनिर्धारितम् | |
| link | सम्पर्कसूत्रम् | |
| none / all | कोऽपि नास्ति / सर्वम् | |
| yes / no | आम् / न | |
| name / title / description | नाम / शीर्षकम् / वर्णनम् | |
| value / type / list | मूल्यम् / प्रकारः / सूचिः | |
| page / path | पृष्ठम् / मार्गः | |
| required / optional | आवश्यकम् / ऐच्छिकम् | |
| options | विकल्पाः | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is आपणः, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  संरक्षणम्.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (प्रवेशः).
- Buttons are action nouns in the nominative, not imperatives; instructions use the polite imperative with कृपया;
  results use the passive past participle. These three forms apply to every catalogue of the language.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
