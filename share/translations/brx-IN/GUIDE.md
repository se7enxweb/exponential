# Bodo (brx-IN): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard written Bodo (बर’) in Devanagari, as used in the Bodoland Territorial Region and taught and published under
the Bodo Sahitya Sabha: the neutral, polite register of Bodo schoolbooks, newspapers and the Bodo localisations of
software. The locale's `LanguageName` is `बर’`, with the modifier apostrophe U+2019. No other locale falls back to
`brx-IN`.

- Word order is subject - object - verb; case markers and postpositions follow the noun (-आ subject/topic, -खौ object,
  -नि genitive, -आव locative, -निफ्राय from, -नैलाय with, -बो also). Bodo is its own Tibeto-Burman language: its grammar
  is never that of Assamese or Hindi.
- Technical words: where Bodo has a settled term (बिजिरनाय, नोनजा, फाइल) it is used. Where it has none, the word is
  borrowed in its Bodo-school spelling in Devanagari (फाइल, फोल्डार, सार्भार), with Bodo suffixes. Assamese (`asm-IN`)
  and Hindi (`hin-IN`) serve only to check the terminology of a loanword; never copy their grammar, particles or
  verb endings. Never leave an English word unless it is a product name or code.
- Short, plain sentences; no honorific forms beyond the polite request.

## 2. Form of address

- The user is addressed politely without a pronoun where Bodo can leave it out; where one is needed, नों (you).
- **Buttons, menu items and links**: the bare verb stem, as Bodo interfaces do: रैखा (save), बन्द (cancel), मेटा
  (delete), नायनाय (search), सायख्रा (select), जोराव (add), सोलोंथाय (edit); the glossary gives the word of each, and the
  stem is never followed by a particle.
- **Titles, column headings and tabs**: nouns or noun phrases (verbal nouns in -नाय): सेटिं, अनुवाद, मुख्य थान, मुं.
- **Instructions**: the verb with the polite particle नाजा before the verb where the source has please or gives an
  instruction: "नाजा आइटेमखौ सायख्रा आरो 'ठीक' जौँहोन".
- **Confirmations and questions**: the question particle -ला at the end: "बे सेक्सनखौ मेटानो गोनां नाला?" (decided by the
  verb form "-नो गोनां" = to want to / must).
- **What the user can or may do**: "... हायो" (can), "... हायाखै" (cannot); "You do not have permission" is
  "नोंथाङै अनुमति दैया".
- **Messages about a result**: the completed form with the past marker -खैसे / -नानै: "खस्रा रैखाखैसे", "फाइल आपलोड
  खालामनाय सोलोमना".

## 3. Script and typography

- **Devanagari** (U+0900-U+097F), the official script of Bodo. The modifier apostrophe **’** (U+2019) marks the
  vowel /ɯ/ of Bodo orthography (बर’, नों); never write the straight ' or a backtick instead. The same character stays
  where the source word in a glossary entry has it.
- **Sentence end**: the Latin full stop "." (as in the Bodo Sahitya Sabha publications) where the source has one; Bodo
  does not use the danda for the sentence end in new texts. Colons after labels, ellipses, question and exclamation
  marks are kept as in the source.
- **Latin** stays for product names, codes, acronyms, file names, INI settings and template code (Exponential, PHP,
  URL, ID, JSON).
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder, so a Bodo suffix never follows it
  directly ("Count: %count" and not "%countनि"). The same for Latin words, code and product names inside a Bodo
  sentence.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Numbers**: digits 0-9 (Devanagari digits are not used in the interface).

## 4. Glossary

Binding for the kernel and every extension. Where a row says "loan", the word is the Devanagari spelling of the
English or Hindi/Assamese term, because Bodo has no settled word of its own.

| English | Bodo | Note |
|---|---|---|
| content | बिषयबस्तु | content object: बिषयबस्तु बस्तु |
| object | बस्तु | |
| node | नोड | loan; main node: मुख्य नोड parent node: बिफा नोड |
| location | थान | main location: मुख्य थान |
| item (list entry) | आइटेम | loan |
| tree | बिफां | tree menu: बिफां मेनु content structure tree: बिषयबस्तु बिफां |
| class | क्लास | loan; the content class: बिषयबस्तु क्लास class group: क्लास हुदा |
| attribute | गुन | |
| datatype | डेटा रोखोम | |
| version | भार्सन | loan; version history: भार्सननि इतिहास |
| draft | खस्रा | |
| publish | फोरमाय | published: फोरमायनाय जादों |
| send for publishing | फोरमायनो फै | |
| discard | नायजा | discard draft: खस्रा नायजा |
| archived / pending / rejected | आर्काइभ / थाङै / नाजादों | |
| siteaccess | साइट-प्रबेश | siteaccess as a setting name stays in code |
| site, website | साइट, ओयेबसाइट | |
| section | सेक्सन | loan; the Exponential section; an INI section: INI सेक्सन |
| role | बाहागो | |
| policy | निति | |
| limitation | सिमा | |
| permission | अनुमति | access denied: प्रबेश जायाखै |
| module | मडुल | loan |
| function | फङ्सन | loan; a PHP function: PHP function |
| user / user group | बेबहार खालामग्रा / बेबहारग्रा हुदा | |
| group | हुदा | |
| anonymous | मुं मावग्रा | anonymous user: मुं मावग्रा बेबहारग्रा |
| administrator | अडमिनिस्ट्रेटर | administration (interface): प्रशासन |
| username | बेबहारग्रानि मुं | |
| password | पासओयार्ड | loan |
| log in / log out | लगइन / लगआउट | |
| e-mail | इ-मेइल | e-mail address: इ-मेइल ठिकाना |
| cache | क्यास | loan; clear cache: क्यास सिन्द्राय |
| template | टेम्पलेट | loan; override template: ओभारराइड टेम्पलेट |
| override | ओभारराइड | |
| fallback | फलबेक | fallback language: फलबेक राव |
| design | डिजाइन | |
| workflow | कामनि रोखोम | |
| trigger | ट्रिगार | |
| event | घटना | |
| trash | जाथाइ गाङ | empty trash: जाथाइ गाङ सिन्द्राय move to trash: जाथाइ गाङाव थां |
| bookmark | बुकमार्क | |
| settings | सेटिं | configuration: कनफिगारेसन |
| setup | सेटआप | the Setup tab |
| layout | लेआउट | Exponential Layouts stays |
| block / zone | ब्लक / जोन | |
| extension | एक्सटेनसन | |
| package | प्याकेज | |
| search | नायनाय | search index: नायनाय इन्डेक्स |
| log | लग | a log file: लग फाइल never "log in" |
| queue | लाइन | job queue: खामानि लाइन |
| job / task | खामानि | |
| cron | cron | |
| script | script | |
| kernel, core | कर्नेल | |
| server | सार्भार | |
| database | डेटाबेस | |
| folder / directory | फोल्डार | |
| file | फाइल | |
| image | मुहुर | thumbnail: खुदै मुहुर |
| preview | सोलोंथाइ | |
| upload / download | आपलोड / डाउनलोड | |
| export / import | एक्सपोर्ट / इम्पोर्ट | |
| remove | आगाय | take out of a list or place |
| delete | मेटा | destroy; deleted: मेटानाय जादों |
| edit | सोलोंथाय | |
| editor | सोलोंथायग्रा / इडिटर | person / software |
| save / store | रैखा | the verb "Store" of the forms |
| Store (shop tab) | दोकान | the shop, as "Shop" |
| cancel | बन्द | |
| OK | ठीक | |
| apply | लागाव | |
| back | खेबसे | |
| next / previous | उननि / सिगांनि | |
| enable / disable | चालु / बन्द | enabled / disabled: चालु जानाय / बन्द जानाय |
| active / inactive | सक्रिय / निष्क्रिय | |
| run | चलाव | |
| start / stop | सुरु / थाय | |
| session | सेसन | loan |
| hidden / visible | गुबुन / नायनो हायो | hide / reveal: गुबुन खालाम / गोख्रों |
| archive | आर्काइभ | |
| order (sort) | क्रम | sort order: क्रमनि रोखोम |
| order (shop) | अर्डार | |
| shop | दोकान | |
| basket | डाला | |
| product | बस्तु-गुदि | |
| customer | गाहाइ | |
| discount | कम राइज | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | दाम / मुद्रा | |
| tag | टेग | loan |
| translation | अनुबाद | |
| language | राव | main language: मुख्य राव |
| locale | लोकेल | |
| state | अबस्था | the object state |
| status | स्थिति | |
| view (button) | नायनाय | a module view (noun): भिउ |
| format | फर्माट | |
| sort | क्रम खालाम | |
| filter | फिल्टार | |
| select | सायख्रा | select all: जोबोद सायख्रा selected: सायख्रानाय जादों |
| copy / move | कपि / थां | |
| add / create / new | जोराव / सर्जि / गोदान | |
| restore | फिन्हुं | |
| install / uninstall | इन्स्टल / आनइन्स्टल | |
| related objects | सोमोन्धोनि बस्तु | relation: सोमोन्धो |
| dashboard | डेसबोर्ड | |
| identifier / ID | चिन्हा / ID | |
| priority | आगानि | |
| owner | मालिक | |
| modified / created | सोलोंथायनाय / सर्जिनाय | |
| date / time | तारिख / सोमोय | |
| error / warning | गोलाव / सावधान | |
| help | मदद | |
| notification | खौसिनाय | |
| account | एकाउन्ट | |
| default | डिफल्ट | |
| link | लिंक | |
| none / all | मुं / जोबोद | |
| yes / no | होय / नङा | |
| name / title / description | मुं / सिरिनाम / बिबरण | |
| value / type / list | मान / रोखोम / तालिका | |
| page / path | फोथा / फथ | |
| required / optional | गोनांनो / इच्छा | |
| options | अप्सन | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is दोकान, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is रैखा.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (लगइन).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Where this guide marks a word as a loan, a reviewer who knows a settled Bodo word may replace it in the glossary
  first and then in the catalogues; the glossary stays the one place the term is decided.
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
