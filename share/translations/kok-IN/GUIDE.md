# Konkani (kok-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Konkani (कोंकणी) of Goa in Devanagari, the official script of Goa, in the polite, neutral register of
Goan government texts, schoolbooks and the Konkani localisations of operating systems and office software. The
locale is `kok-IN` (Konkani, India); no other locale falls back to it.

- Word order is subject - object - verb; postpositions follow the noun (मेरेन, खातीर, कडेन, भितर, मदीं, वयर,
  सकयल). The genitive agrees with the thing owned: -चो (masculine), -ची (feminine), -चें (neuter), plural -चे/-च्यो/-चीं.
- **Konkani is not Marathi.** Marathi (`mar-IN`) is a close relative and serves only to check terminology and
  typography; never copy a Marathi form that Konkani does not use. The frequent differences:
  - and: आनी (Marathi आणि); no / not: ना (Marathi नाही); is / are: आसा / आसात (Marathi आहे / आहेत); if: जाल्यार
    (Marathi जर); but: पूण (Marathi पण); also: लेगीत / भी (Marathi सुद्धा); what: कितें (Marathi काय); where: खंय
    (Marathi कुठे); how: कसो / कशें (Marathi कसे); only: फक्त stays, but "that" is जें / म्हणून (Marathi की).
  - Genitive and neuter: -चें (Marathi -चे); verbal noun in -प (करप, काडप, सोदप; Marathi -णे / -ा).
  - Past of a finished action: जालें / केलें / जालो / जाली (Marathi झाले / केले / झाला / झाली).
  - Konkani nouns often end in -ो (मसुदो draft, वापरपी user); Marathi uses -ा.
- Technical words: the established Konkani computing terms where there is one (आशय content, मसुदो draft, वापरपी user,
  सोदप search); well-known computing loanwords in Devanagari where the Konkani localisations use them (फायल,
  फोल्डर, पासवर्ड, सर्व्हर, डेटाबेस, कॅश, टेम्प्लेट). Never leave the English word in Latin letters unless it is a
  product name or code.
- Short, plain sentences; no Sanskritised or literary forms where an everyday Goan word exists.

## 2. Form of address

- The user is addressed politely with **तुमी** (object तुमकां, genitive तुमचो / तुमची / तुमचें); never the familiar तूं
  and never the Marathi आपण. Where Konkani can leave the pronoun out, it does.
- **Buttons, menu items and links**: the polite plural imperative in -ात, as Konkani interfaces do: जतन करात (save),
  रद्द करात (cancel), मेटयात (delete), काडात (remove), संपादन करात (edit), जोडात (add), तयार करात (create),
  सोदात (search), निवडात (select), धाडात (send), पळयात (view), प्रकाशीत करात (publish), अपलोड करात (upload),
  डाउनलोड करात (download). Where a short noun is the established label of a tab or toolbar, the noun is used
  (सोद, मदत).
- **Titles, column headings and tabs**: nouns or noun phrases: सेटिंग्ज, भाशांतर, मुखेल स्थान, नांव.
- **Instructions**: the polite imperative, with "कृपया" where the source has please: "एक ऑब्जेक्ट निवडात आनी \"ओके\"
  दाबात." / "कृपया तुमचो पासवर्ड घालात."
- **Confirmations and questions**: "तुमकां खरेंच ... करपाक जाय?": "तुमकां खरेंच हो विभाग मेटपाक जाय?".
- **What the user can or may do**: "... करूं येता", "... करूं येना"; "You do not have permission" is
  "तुमकां ... परवानगी ना."
- **Messages about a result**: the completed form: "मसुदो जतन जालो.", "फायल अपलोड जाली.", "सेटिंग्ज बदलल्यो.".
  Errors: "... करपाक जालें ना." / "... सोदून मेळना."

## 3. Script and typography

- **Devanagari** (U+0900-U+097F). Words are separated by spaces. Konkani Devanagari uses the nukta (ऩ, ऱ not used;
  ज़, फ़ only in borrowed words) sparingly; the ZWJ and ZWNJ are not used to force forms.
- **Sentence end**: the **full stop "."** (U+002E), as in the English source and as Konkani software uses it. The
  danda "।" (U+0964) is **not** used in the interface. A question ends with "?" (the source's mark). Colons after
  labels, ellipses ("..." or "…") and exclamation marks are kept as in the source. A space follows the mark inside a
  text, never before it.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON, INI, API, HTML).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder, so no Konkani suffix or
  postposition is attached to it: write "वापरपी: %name" or rephrase so that the postposition follows a Konkani
  word. The same for Latin words, code and product names inside a Konkani sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (Devanagari digits ०-९ are not used in the interface).
- **Chandra and anusvara**: the chandra-e and chandra-o (ॅ, ॉ) are written in loanwords (ऑब्जेक्ट, कॅश, ब्लॉक). A
  nasal before a consonant is written with the anusvara (कोंकणी, संपादन), not the half nasal letter.

## 4. Glossary

Binding for the kernel and every extension. Where the Marathi glossary has a different word, the Konkani one in this
table wins.

| English | Konkani | Note |
|---|---|---|
| content | आशय | content object: आशय ऑब्जेक्ट |
| object | ऑब्जेक्ट | |
| node | नोड | main node: मुखेल नोड; parent node: मुळावो नोड |
| location | स्थान | main location: मुखेल स्थान |
| item (list entry) | आयटम | |
| tree | झाड | tree menu: झाडाचो मेनू; content structure tree: आशय रचनेचें झाड |
| class | वर्ग | the content class: आशय वर्ग; class group: वर्ग गट |
| attribute | गुणधर्म | |
| datatype | डेटा प्रकार | |
| version | आवृत्ती | version history: आवृत्तींचो इतिहास |
| draft | मसुदो | store draft: मसुदो जतन करात |
| publish | प्रकाशीत करप | published: प्रकाशीत |
| send for publishing | प्रकाशनाक धाडप | |
| discard | सोडून दिवप | discard draft: मसुदो सोडून दियात |
| archived / pending / rejected | संग्रहीत / प्रलंबीत / ना-मंजूर | |
| siteaccess | साइटअॅक्सेस | as a setting name stays in code |
| site, website | साईट, वेबसाईट | |
| section | विभाग | the Exponential section; an INI section: INI विभाग |
| role | भूमिका | |
| policy | धोरण | |
| limitation | मर्यादा | |
| permission | परवानगी | access denied: प्रवेश ना |
| module | मॉड्यूल | |
| function | फंक्शन | a PHP function: PHP function |
| user / user group | वापरपी / वापरपी गट | plural: वापरपी |
| group | गट | |
| anonymous | अनामिक | anonymous user: अनामिक वापरपी |
| administrator | प्रशासक | administration (interface): प्रशासन |
| username | वापरपी नांव | |
| password | पासवर्ड | |
| log in / log out | लॉग इन करप / लॉग आऊट करप | button: लॉग इन करात / लॉग आऊट करात |
| e-mail | ई-मेल | e-mail address: ई-मेल पत्तो |
| cache | कॅश | clear cache: कॅश सोपो करात |
| template | टेम्प्लेट | override template: ओव्हरराइड टेम्प्लेट |
| override | ओव्हरराइड | |
| fallback | फॉलबॅक | fallback language: फॉलबॅक भास |
| design | डिझाइन | |
| workflow | कामाचो प्रवाह | |
| trigger | ट्रिगर | |
| event | घडणूक | |
| trash | कचरेपेटी | empty trash: कचरेपेटी रिकामी करात; move to trash: कचरेपेटयेंत हालयात |
| bookmark | बुकमार्क | |
| settings | सेटिंग्ज | configuration: कॉन्फिगरेशन |
| setup | सेटअप | the Setup tab |
| layout | मांडावळ | Exponential Layouts stays |
| block / zone | ब्लॉक / झोन | |
| extension | विस्तार | |
| package | पॅकेज | |
| search | सोद | search index: सोद अनुक्रमणिका; verb: सोदप |
| log | लॉग | a log file: लॉग फायल; never "log in" |
| queue | रांग | job queue: जॉब रांग |
| job / task | जॉब / काम | |
| cron | cron | |
| script | script | |
| kernel, core | कर्नेल | |
| server | सर्व्हर | |
| database | डेटाबेस | |
| folder / directory | फोल्डर / डिरेक्टरी | |
| file | फायल | |
| image | प्रतिमा | thumbnail: ल्हान प्रतिमा |
| preview | पूर्वदृश्य | |
| upload / download | अपलोड / डाउनलोड | |
| export / import | निर्यात / आयात | verbs: निर्यात करप / आयात करप |
| remove | काडप | take out of a list or place; imperative: काडात |
| delete | मेटप | destroy; imperative: मेटयात; deleted: मेटलें |
| edit | संपादन करप | noun: संपादन |
| editor | संपादक | person / software |
| save / store | जतन करप | the verb "Store" of the forms |
| Store (shop tab) | दुकान | the shop, as "Shop" |
| cancel | रद्द करप | |
| OK | ओके | |
| apply | लागू करप | |
| back | फाटीं | |
| next / previous | फुडें / फाटलें | |
| enable / disable | सक्षम करप / अक्षम करप | enabled / disabled: सक्षम / अक्षम |
| active / inactive | सक्रिय / निष्क्रिय | |
| run | चलोवप | |
| start / stop | सुरू करप / बंद करप | |
| session | सत्र | |
| hidden / visible | लिपयलें / दिसपी | hide / reveal: लिपयात / दाखयात |
| archive | संग्रह | a file archive: संग्रह फायल |
| order (sort) | क्रम | sort order: क्रमवारी |
| order (shop) | ऑर्डर | |
| shop | दुकान | webshop: वेबदुकान |
| basket | बास्केट | |
| product | उत्पादन | |
| customer | ग्राहक | |
| discount | सुटावो | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | दर / चलन | |
| tag | टॅग | a keyword tag; an HTML tag: HTML टॅग |
| translation | भाशांतर | |
| language | भास | main language: मुखेल भास |
| locale | लोकॅल | |
| state | स्थिती | the object state; an address state: राज्य |
| status | स्थिती | |
| view (button) | पळयात | a module view (noun): दृश्य |
| format | स्वरूप | |
| sort | क्रमवारी लावप | |
| filter | फिल्टर | |
| select | निवडप | select all: सगळें निवडात; selected: निवडलें |
| copy / move | कॉपी करप / हालयप | |
| add / create / new | जोडप / तयार करप / नवें | |
| restore | परत मेळोवप | imperative: परत मेळयात |
| install / uninstall | इन्स्टॉल करप / अनइन्स्टॉल करप | |
| related objects | संबंदीत ऑब्जेक्ट | relation: संबंद |
| dashboard | डॅशबोर्ड | |
| identifier / ID | अभिज्ञापक / ID | |
| priority | प्राधान्य | |
| owner | मालक | |
| modified / created | बदललें / तयार केलें | |
| date / time | तारीख / वेळ | |
| error / warning | त्रुटी / चेतावणी | |
| help | मदत | |
| notification | सूचणा | |
| account | खातें | user account: वापरपी खातें |
| default | डिफॉल्ट | |
| link | दुवो | broken link: मोडलो दुवो |
| none / all | कांयच ना / सगळें | |
| yes / no | होय / ना | |
| name / title / description | नांव / मथळो / वर्णन | |
| value / type / list | मोल / प्रकार / यादी | |
| page / path | पान / मार्ग | |
| required / optional | गरजेचें / ऐच्छीक | |
| options | पर्याय | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is दुकान, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is जतन करात.
- "Login" in the user, session and audit lists is the user's login name (लॉगिन नांव); the Login button of the error
  page is the act (लॉग इन करात).
- The sentence end is the full stop "."; the danda is not used anywhere in the catalogues.
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
