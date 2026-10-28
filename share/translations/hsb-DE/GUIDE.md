# Upper Sorbian (hsb-DE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Standard Upper Sorbian (hornjoserbšćina) as written in Upper Lusatia (Saxony, Germany), following the current
orthography and the vocabulary of the Upper Sorbian dictionaries (soblex.de) and of the Upper Sorbian versions of
Firefox, Thunderbird and LibreOffice. No other locale falls back to this catalogue. It is not Lower Sorbian: dsb-DE
forms are wrong here (hsb "wužiwar", "prošu", "hesło", "wotstronić", "składować", "přełožk", not dsb "wužywaŕ",
"pšosym", "gronidło", "wótpóraś", "składowaś", "pśełožk").

The tone is neutral, concise and professional. Avoid German words where an Upper Sorbian one is established, and
avoid needless English; do not invent new words where an established international one exists (atribut, modul,
paket, wersija, filter).

## 2. Form of address

The user is addressed formally with **Wy** (2nd person plural), written with a capital letter in every form, as is
the Sorbian courtesy convention: Wy, Was, Wam, Wami, Waš, Waša, Waše, Wašeho, Wašeje, Wašim. The verb ending already
marks the person, so the pronoun is usually left out ("Nimaće prawo ...", not "Wy nimaće prawo ...").

- **Buttons, menu items, links and action labels** use the infinitive (ending in -ć): "Składować", "Zhašeć",
  "Wotstronić", "Wobdźěłać", "Přidać", "Přetorhnyć", "Nowy wutworić", "Změny nałožić", "K wozjewjenju pósłać",
  "Wšitko wubrać", "Naćisk składować", "Naćisk zaćisnyć", "Papjernik wuprózdnić".
- **Titles, column headings and tabs** are nouns: "Struktura wobsaha", "Wužiwarske konta", "Hłowna rěč".
- **Requests** use "prošu" with the plural imperative: "Prošu wubjerće městno.", "Prošu zapodajće płaćiwu
  e-mejlowu adresu."
- **Instructions** use the plural imperative: "Přepruwujće zwisk a spytajće hišće raz.", "Klikńće na ...".
- **Questions and confirmations**: "Chceće ... woprawdźe ...?": "Chceće tutu klasu woprawdźe zhašeć?".
- **Error messages** state the fact: "Nimaće prawo klasy zhašeć.", "Dataja njeje so namakała."
- **Status messages** are impersonal (reflexive or passive): "Změny su so wuspěšnje składowali.", "Objekt je so
  zhašał.".
- Not allowed: the familiar form (ty, tebje, twój, chceš, móžeš, singular imperative "Składuj", "Wubjer"); "wy",
  "waš" in lower case where the user is addressed; the imperative on buttons.

## 3. Script and typography

- Latin script with the full Upper Sorbian alphabet: a b c č ć d dź e ě f g h ch i j k ł l m n ń o ó p r ř s š t u w
  y z ž. Use the precomposed characters (ć, ń, ó, ř, ě, ł, č, š, ž), never a letter plus a combining accent, and
  never the Lower Sorbian ŕ, ś, ź spellings.
- **Capitals**: sentence case; only the first word of a button, title or heading, proper names and the courtesy
  pronoun Wy/Waš are capitalized. No English title case and no German noun capitals.
- **Quotation marks** in running text: „…“ (U+201E, U+201C). Inside HTML attributes and JavaScript the straight
  quotes of the source stay; where the source has `&quot;`, keep the entity.
- **No space** before ":", ";", "?" and "!".
- **Compounds with a code or abbreviation** take a hyphen and the Sorbian word declines: "URL-alias", "XML-tag",
  "INI-dataja", "PDF-dokument", "e-mejlowa adresa".
- **Plural forms**: Upper Sorbian has four number forms: 1 (jedyn objekt), 2 (dual: dwaj objektaj), 3-4 (tři
  objekty) and 5+ (pjeć objektow). Never write "objekt(y)". Use a label form in which the count stands alone:
  "Objekty: %count".
- **Placeholders and case**: a placeholder is never declined and nothing is glued to it. Put a noun in front that
  carries the case, so that the placeholder stays in the nominative: "Klasu <%class_name> wobdźěłać", "objekt %id
  w rěči %language".
- **Numbers**: digits 0-9; decimal comma; dates as "23.10.2026", time as "14:05".
- **Exponential** and other product names are not declined; where grammar needs a case, use a frame word:
  "system Exponential", "w systemje Exponential".

## 4. Glossary

Binding for the kernel and every extension. Verbs are given in the infinitive (the button form).

| English | Upper Sorbian | Note |
|---|---|---|
| content | wobsah | gen. wobsaha |
| content object / object | objekt wobsaha / objekt | |
| item (list entry) | zapisk | pl. zapiski |
| node | suk | main node: hłowny suk |
| location / main location | městno / hłowne městno | |
| content class / class | klasa wobsaha / klasa | class group: skupina klasow |
| attribute | atribut | |
| datatype | datowy typ | |
| version | wersija | version history: historija wersijow |
| draft | naćisk | Store draft: Naćisk składować; Discard draft: Naćisk zaćisnyć |
| publish / published | wozjewić / wozjewjeny | Send for publishing: K wozjewjenju pósłać |
| translation | přełožk | pl. přełožki |
| language / main language | rěč / hłowna rěč | |
| fallback | narunanje | fallback language: narunanska rěč |
| siteaccess | přistup k sydłu | |
| site / website | sydło / websydło | |
| design | design | |
| template | předłoha | |
| override | přepisanje | override template: přepisowaca předłoha |
| cache | pufrowak | Clear cache: Pufrowak wuprózdnić |
| workflow | dźěłowy wotběh | pl. dźěłowe wotběhi |
| event | podawk | |
| trigger | spušćak | |
| role | róla | |
| policy | zasada | pl. zasady |
| permission | prawo | pl. prawa |
| access / access denied | přistup / přistup zakazany | |
| limitation | wobmjezowanje | |
| user / user group | wužiwar / wužiwarska skupina | pl. wužiwarjo |
| user account | wužiwarske konto | |
| anonymous user | anonymny wužiwar | |
| administrator / admin interface | administrator / administraciski powjerch | |
| log in / log out | přizjewić so / wotzjewić so | |
| username | wužiwarske mjeno | |
| password | hesło | |
| e-mail | e-mejl | e-mail address: e-mejlowa adresa |
| section | wotrězk | the Exponential section; an INI section: blok |
| object state / state | staw objekta / staw | |
| status | status | |
| relation / related objects | poćah / zwjazane objekty | |
| trash | papjernik | Empty trash: Papjernik wuprózdnić |
| bookmark | zapołožka | |
| parent | nadrjadowany zapisk | parent node: nadrjadowany suk |
| owner / creator | wobsedźer / awtor | |
| created / modified | wutworjeny / změnjeny | |
| layout | wuhotowanje | layout type: typ wuhotowanja; Exponential Layouts stays |
| block / zone | blok / cona | |
| settings | nastajenja | configuration: konfiguracija |
| Setup (admin tab) | Zarjadowanje | |
| Dashboard | Přehladowa tafla | |
| extension | rozšěrjenje | |
| package | paket | |
| module | modul | |
| view (noun) / view (button) | napohlad / pokazać | |
| function | funkcija | |
| search | pytać / pytanje | search index: pytanski indeks |
| query | naprašowanje | |
| job / task | nadawk | job queue: čakanski rěd nadawkow |
| handler | wobdźěłowak | |
| gateway (payment) | płaćenska słužba | |
| feed (RSS) | kanal | |
| worker | dźěłowy proces | |
| queue | čakanski rěd | |
| log / log file | protokol / protokolowa dataja | |
| notification | zdźělenka | |
| preview | přehlad | |
| file | dataja | |
| folder / directory | rjadowak | |
| image / media | wobraz / medije | |
| thumbnail | miniatura | |
| upload / download | nahrać / sćahnyć | |
| export / import | eksportować / importować | |
| remove | wotstronić | take out of a list or place |
| delete | zhašeć | destroy |
| edit | wobdźěłać | |
| editor | editor | |
| save / store | składować | the verb "Store" of the forms |
| shop / Store (shop tab) | wobchod / Wobchod | every other "Store" is the verb składować |
| basket | nakupowanski košik | |
| order (shop) | skazanka | |
| order (sorting) | porjad | sort order: sortěrowanski porjad |
| customer / product | kupc / produkt | |
| VAT | přidatna dań | |
| discount | rabat | |
| price / currency | płaćizna / pjenjezy | currency: měna |
| tag (keyword) | wobznamjenje | an HTML/XML tag: tag |
| cancel | přetorhnyć | |
| OK | W porjadku | |
| apply | nałožić | |
| back | wróćo | |
| next / previous | dale / wróćo | |
| enable / disable | zmóžnić / znjemóžnić | |
| active / inactive | aktiwny / njeaktiwny | |
| run | wuwjesć | |
| start / stop | startować / zastajić | |
| session | posedźenje | |
| hidden / visible | schowany / widźomny | hide / reveal: schować / pokazać |
| archive | archiw | |
| sort / filter | sortěrować / filtrować | a filter: filter |
| select | wubrać | selected: wubrany |
| copy / move | kopěrować / přesunyć | |
| add / create / new | přidać / wutworić / nowy | |
| restore | wobnowić | |
| install / uninstall | instalować / wotinstalować | |
| identifier / ID | identifikator / ID | |
| priority | priorita | |
| date / time | datum / čas | |
| warning / error | warnowanje / zmylk | |
| help | pomoc | |
| account | konto | |
| default | standard | default value: standardna hódnota |
| link | wotkaz | |
| none / all | žadyn / wšě | |
| yes / no | haj / ně | |
| name / title / description | mjeno / titul / wopisanje | |
| value / type / list | hódnota / typ / lisćina | |
| page / path | strona / šćežka | |
| required / optional | trěbny / opcionalny | |
| options | opcije | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Wobchod, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Składować.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Přizjewić so).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID ...) are listed with their reason
  in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
