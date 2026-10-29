# Faroese (fao-FO): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Faroese (føroyskt) in its standard written form, with the computing terms of the Faroese term work (Málráðið,
Orðabanki) and of the Faroese software localisations, in a neutral, friendly register. No other locale falls back to
`fao-FO`.

- Write real Faroese, never leave the Danish or English word where Faroese has a term: "fíla", "loyniorð", "mappa",
  "leinki", "brúkari", "strika", "goyma", "ambætari", "dátugrunnur". Danish forms are wrong here ("fil", "bruger",
  "adgangskode", "gem", "slet").
- Inflection follows the grammar (fílan, fíluna, fílunnar; brúkarin, brúkarar). A placeholder, a product name or
  code is never inflected; place it where no case ending is needed (after a colon, after a noun: "brúkarin %name",
  "Navn: %name").
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **tú** (singular), as Faroese software and public texts do: "Ert tú vís/ur í, at tú
  vilt strika hendan lutin?" is avoided in favour of the neutral "Vilt tú veruliga strika hendan lutin?"; "Tú hevur
  ikki loyvi til at ...".
- **Buttons, menu items and links**: the infinitive without "at": Goyma, Ógilda, Strika, Taka burtur, Rætta,
  Leggja afturat, Stovna, Leita, Velja, Senda, Vísa, Útgeva, Leggja upp, Taka niður, Nýta, Endurskapa.
- **Titles, column headings and tabs**: nouns: Stillingar, Umsetingar, Høvuðsstaðseting, Navn.
- **Instructions**: the imperative: "Vel ein lut og trýst á \"Í lagi\".".
- **Confirmations and questions**: "Vilt tú veruliga ...?".
- **What the user can or may do**: "Tú kanst ...", "Tú kanst ikki ...".
- **Messages about a result**: the passive with "varð/er": "Kladdan er goymd.", "Fílan varð løgd upp.".

## 3. Script and typography

- **Latin** alphabet with the Faroese letters á í ó ú ý æ ø ð, as composed characters. Never the Icelandic letters
  ö, þ, é and never Icelandic word forms (ákvörðun is Icelandic; Faroese writes ø and t/h where Icelandic has ö and þ).
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder and no case ending is added.
- **Compounds**: a Faroese compound is one word (brúkaranavn, høvuðsstaðseting); with a code or abbreviation a
  hyphen: URL-dulnevni, XML-frámerki, INI-fíla.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Faroese | Note |
|---|---|---|
| content | innihald | content object: innihaldslutur |
| object | lutur | plural: lutir |
| node | knútur | plural: knútar; main node: høvuðsknútur; parent node: yvirknútur |
| location | staðseting | main location: høvuðsstaðseting |
| item (list entry) | liður | plural: liðir |
| tree | træ | content structure: innihaldsbygnaður |
| class | flokkur | content class: innihaldsflokkur; class group: flokkabólkur |
| attribute | eginleiki | plural: eginleikar |
| datatype | dátuslag | |
| version | útgáva | version history: útgávusøga |
| draft | kladda | store draft: Goyma kladdu |
| publish | útgeva | published: útgivið |
| send for publishing | senda til útgávu | |
| discard | vraka | |
| siteaccess | atgongd til vevstað | siteaccess as a setting name stays in code |
| site, website | vevstaður | |
| section | deild | an INI section: INI-deild |
| role | leiklutur | |
| policy | politikkur | |
| limitation | avmarking | |
| permission | loyvi | access denied: atgongd noktað |
| module | modul | |
| function | funktión | |
| user / user group | brúkari / brúkarabólkur | plural: brúkarar |
| group | bólkur | |
| anonymous | dulnevndur | |
| administrator | umsitari | administration interface: umsitingarmarkamót |
| username | brúkaranavn | |
| password | loyniorð | |
| log in / log out | rita inn / rita út | |
| e-mail | teldupostur | e-mail address: teldupostadressa |
| cache | skjótgoymsla | clear cache: Tøma skjótgoymsluna |
| template | formur | override template: yvirskrivingarformur |
| override | yvirskriva | |
| fallback | vara- | fallback language: varamál |
| design | snið | |
| workflow | arbeiðsgongd | |
| trigger | útloysari | |
| event | hending | |
| trash | ruskspann | empty trash: Tøma ruskspannina |
| bookmark | bókamerki | |
| settings | stillingar | configuration: uppseting |
| setup | uppseting | the Setup tab: Uppseting |
| layout | skipan | Exponential Layouts stays |
| block / zone | blokkur / øki | |
| extension | viðbót | |
| package | pakki | |
| search | leita | search index: leitiskrá |
| log | loggur | log file: loggfíla |
| queue | bíðirøð | |
| job / task | uppgáva | |
| kernel, core | kjarni | |
| server | ambætari | |
| database | dátugrunnur | |
| folder / directory | mappa | |
| file | fíla | |
| image | mynd | thumbnail: smámynd |
| preview | forsýning | |
| upload / download | leggja upp / taka niður | |
| export / import | útflyta / innflyta | |
| remove | taka burtur | take out of a list or place |
| delete | strika | destroy |
| edit | rætta | |
| editor | ritstjóri | person; the software: ritil |
| save / store | goyma | the verb "Store" of the forms |
| Store (shop tab) | Handil | the shop, as "Shop" |
| cancel | ógilda | |
| OK | Í lagi | |
| apply | nýta | |
| back | aftur | |
| next / previous | næsta / undanfarna | |
| enable / disable | virkja / óvirkja | |
| active / inactive | virkin / óvirkin | |
| run | koyra | |
| start / stop | byrja / steðga | |
| session | seta | |
| hidden / visible | fjaldur / sjónligur | hide / reveal: fjala / vísa |
| archive | skjalasavn | |
| order (sort) | raðfylgja | sort order: raðfylgja |
| order (shop) | bílegging | |
| shop | handil | |
| basket | keypikorgur | |
| product | vøra | |
| customer | kundi | |
| discount | avsláttur | |
| VAT | MVG | meirvirðisgjald |
| price / currency | prísur / gjaldoyra | |
| tag | frámerki | |
| translation | umseting | |
| language | mál | main language: høvuðsmál |
| locale | locale | as a setting; in prose: staðbundnar stillingar |
| state | støða | the object state |
| status | støða | |
| view (button) | vísa | a module view (noun): sýni |
| sort | raða | |
| filter | filtur | to filter: filtrera |
| select | velja | selected: valdur |
| copy / move | avrita / flyta | |
| add / create / new | leggja afturat / stovna / nýggjur | |
| restore | endurskapa | |
| install / uninstall | leggja inn / avinnleggja | |
| related objects | tengdir lutir | relation: tilknýti |
| dashboard | yvirlitsborð | |
| identifier / ID | eyðmerki / ID | |
| priority | raðfesting | |
| owner | eigari | |
| modified / created | broytt / stovnað | |
| date / time | dagfesting / tíð | |
| error / warning | villa / ávaring | |
| help | hjálp | |
| notification | fráboðan | |
| account | konta | |
| default | forsett | |
| link | leinki | |
| none / all | eingin / øll | |
| yes / no | ja / nei | |
| name / title / description | navn / heiti / lýsing | |
| value / type / list | virði / slag / listi | |
| page / path | síða / leið | |
| required / optional | kravt / valfrítt | |
| options | møguleikar | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Handil, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Goyma.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Rita inn).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
