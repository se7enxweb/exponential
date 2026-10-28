# Western Frisian (fry-NL): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Western Frisian (Frysk) in the official spelling of 2015 (Fryske Akademy, Taalweb Frysk), with the computing terms of
the Frisian software localisations (Firefox, Thunderbird, LibreOffice), in a neutral, polite register. No other
locale falls back to `fry-NL`.

- Write real Frisian, not Dutch: "bestân", "wachtwurd", "brûker", "ynhâld", "fuortsmite", "bewarje", "ynstellingen".
  A Dutch word is wrong where Frisian has its own ("bestand", "gebruiker", "inhoud", "opslaan" are Dutch).
- Frisian spelling: û, ê, â, ô, ú (bestân, brûker, ynhâld, ûntwerp, útfiere), ij/y as in the standard (wizigje,
  ynstelle), -je verbs (bewarje, bewurkje, sykje) and -e verbs (oplade, fuortsmite) as the standard has them.
- Short, plain sentences.

## 2. Form of address

- The user is addressed with **jo** (polite), as the Frisian software localisations do: "Binne jo wis dat jo dit
  objekt fuortsmite wolle?", "Jo hawwe gjin tastimming om ...".
- **Buttons, menu items and links**: the infinitive: Bewarje, Annulearje, Fuortsmite, Fuorthelje, Bewurkje,
  Tafoegje, Oanmeitsje, Sykje, Selektearje, Ferstjoere, Besjen, Publisearje, Oplade, Downloade, Tapasse, Werstelle.
- **Titles, column headings and tabs**: nouns: Ynstellingen, Oersettingen, Haadlokaasje, Namme.
- **Instructions**: the imperative: "Selektearje in objekt en klik op \"OK\".".
- **Confirmations and questions**: "Binne jo wis dat jo ... wolle?".
- **What the user can or may do**: "Jo kinne ...", "Jo kinne net ...".
- **Messages about a result**: the passive or the participle: "It konsept is bewarre.", "Bestân oplaad.".

## 3. Script and typography

- **Latin** alphabet with â ê ô û ú é è ë ï ü, as composed characters.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder.
- **Compounds**: a Frisian compound is one word (brûkersnamme, wachtwurdbefêstiging); with a code or abbreviation a
  hyphen: URL-alias, XML-tag, INI-bestân, e-mailadres.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Western Frisian | Note |
|---|---|---|
| content | ynhâld | content object: ynhâldsobjekt |
| object | objekt | plural: objekten |
| node | knooppunt | main node: haadknooppunt; parent node: boppelizzend knooppunt |
| location | lokaasje | main location: haadlokaasje |
| item (list entry) | elemint | plural: eleminten |
| tree | beam | content structure: ynhâldsstruktuer |
| class | klasse | content class: ynhâldsklasse; class group: klassegroep |
| attribute | attribút | plural: attributen |
| datatype | gegevenstype | |
| version | ferzje | version history: ferzjeskiednis |
| draft | konsept | store draft: Konsept bewarje |
| publish | publisearje | published: publisearre |
| send for publishing | ferstjoere foar publikaasje | |
| discard | ferwerpe | |
| siteaccess | websidetagong | siteaccess as a setting name stays in code |
| site, website | website | |
| section | seksje | an INI section: INI-seksje |
| role | rol | |
| policy | belied | |
| limitation | beheining | |
| permission | tastimming | access denied: tagong wegere |
| module | module | |
| function | funksje | |
| user / user group | brûker / brûkersgroep | |
| anonymous | anonym | |
| administrator | behearder | administration interface: behearomjouwing |
| username | brûkersnamme | |
| password | wachtwurd | |
| log in / log out | oanmelde / ôfmelde | |
| e-mail | e-mail | e-mail address: e-mailadres |
| cache | buffer | clear cache: Buffer leegje |
| template | sjabloan | override template: oerskriuwsjabloan |
| override | oerskriuwe | |
| fallback | reserve | fallback language: reservetaal |
| design | ûntwerp | |
| workflow | wurkstream | |
| trigger | útlizzer | |
| event | barren | |
| trash | jiskefet | empty trash: Jiskefet leegje |
| bookmark | blêdwizer | |
| settings | ynstellingen | configuration: konfiguraasje |
| setup | ynstelle | the Setup tab: Ynstelle |
| layout | opmaak | Exponential Layouts stays |
| block / zone | blok / sône | |
| extension | útwreiding | |
| package | pakket | |
| search | sykje | search index: sykyndeks |
| log | lochboek | log file: lochboekbestân |
| queue | wachtrige | |
| job / task | taak | |
| handler | behanneler | never the English word |
| gateway (payment) | betelpoarte | |
| worker | wurkproses | |
| feed (RSS) | feed | Frisian loanword |
| repository | repository | |
| siteaccess (in prose) | websidetagong | |
| kernel, core | kearn | |
| server | server | |
| database | databank | |
| folder / directory | map | |
| file | bestân | |
| image | ôfbylding | thumbnail: miniatuer |
| preview | foarbyld | |
| upload / download | oplade / downloade | |
| export / import | eksportearje / ymportearje | |
| remove | fuorthelje | take out of a list or place |
| delete | fuortsmite | destroy |
| edit | bewurkje | |
| editor | bewurker | person or software |
| save / store | bewarje | the verb "Store" of the forms |
| Store (shop tab) | Winkel | the shop, as "Shop" |
| cancel | annulearje | |
| OK | OK | |
| apply | tapasse | |
| back | werom | |
| next / previous | folgjende / foarige | |
| enable / disable | ynskeakelje / útskeakelje | |
| active / inactive | aktyf / ynaktyf | |
| run | útfiere | |
| start / stop | begjinne / stopje | |
| session | sesje | |
| hidden / visible | ferburgen / sichtber | hide / reveal: ferbergje / toane |
| archive | argyf | |
| order (sort) | folchoarder | sort order: sortearfolchoarder |
| order (shop) | bestelling | |
| shop | winkel | |
| basket | winkelkarke | |
| product | produkt | |
| customer | klant | |
| discount | koarting | |
| VAT | btw | |
| price / currency | priis / faluta | |
| tag | label | |
| translation | oersetting | |
| language | taal | main language: haadtaal |
| locale | locale | as a setting; in prose: regionale ynstellingen |
| state | steat | the object state |
| status | status | |
| view (button) | besjen | a module view (noun): werjefte |
| sort | sortearje | |
| filter | filter | to filter: filterje |
| select | selektearje | selected: selektearre |
| copy / move | kopiearje / ferpleatse | |
| add / create / new | tafoegje / oanmeitsje / nij | |
| restore | werstelle | |
| install / uninstall | ynstallearje / de-ynstallearje | |
| related objects | relatearre objekten | relation: relaasje |
| dashboard | dashboerd | |
| identifier / ID | identifikaasje / ID | |
| priority | prioriteit | |
| owner | eigner | |
| modified / created | wizige / oanmakke | |
| date / time | datum / tiid | |
| error / warning | flater / warskôging | |
| help | help | |
| notification | notifikaasje | |
| account | account | |
| default | standert | |
| link | keppeling | |
| none / all | gjin / alle | |
| yes / no | ja / nee | |
| name / title / description | namme / titel / beskriuwing | |
| value / type / list | wearde / type / list | |
| page / path | side / paad | |
| required / optional | ferplicht / opsjoneel | |
| options | opsjes | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Winkel, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Bewarje.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Oanmelde).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, OK ...) and words spelled the
  same in Frisian are listed with their reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when
  a whole message consists of them.
