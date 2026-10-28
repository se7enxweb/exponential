# Luxembourgish (ltz-LU): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Luxembourgish (Lëtzebuergesch) in the official orthography of 2019 (Zenter fir d'Lëtzebuerger Sprooch, the spelling
of the Lëtzebuerger Online Dictionnaire), in the neutral, polite register of Luxembourgish public and software
texts. No other locale falls back to `ltz-LU`.

- Write real Luxembourgish, never German or French: "Fichier", "Passwuert", "Dossier", "Link", "Benotzer",
  "läschen", "späicheren", "Astellungen", not "Datei", "Benutzer", "speichern", "löschen". German (`ger-DE`) and
  French (`fre-FR`) may help with the meaning and the terminology; the text itself is translated from the English.
  Luxembourgish takes many words from French (Fichier, Dossier, Client, Produit, Remise, Objet); use them where the
  dictionary has them.
- Spelling of the norm: ë (Säit, Lëscht, Ännerung), é (Versioun, Funktioun, installéieren), the long vowels doubled
  (Sprooch, Passwuert, Feeler), the verbs on -éieren (publizéieren, aktivéieren, konfiguréieren).
- **The n-rule (Eifeler Regel)**: a final -n of den, een, an, un, kéinten ... falls before a consonant other than n,
  d, t, z, h and a vowel: "de Fichier", "den Objet", "an der Lëscht", "a Wierder". Before a placeholder or code,
  keep the -n (den %name).
- Nouns are written with a capital letter.
- Short, plain sentences.

## 2. Form of address

- The user is addressed with the polite **Dir** (capitalised: Dir, Iech, Äre), as Luxembourgish public texts do.
- **Buttons, menu items and links**: the infinitive: Späicheren, Ofbriechen, Läschen, Ewechhuelen, Änneren,
  Derbäisetzen, Erstellen, Sichen, Auswielen, Schécken, Ukucken, Publizéieren, Eroplueden, Eroflueden, Uwenden,
  Restauréieren.
- **Titles, column headings and tabs**: nouns or noun phrases: Astellungen, Iwwersetzungen, Haaptplaz, Numm.
- **Instructions**: the imperative of Dir: "Wielt en Objet aus a klickt op \"OK\".", "W.e.g." or "Wannechgelift"
  where the source has please.
- **Confirmations and questions**: "Sidd Dir sécher, datt Dir ... wëllt?".
- **What the user can or may do**: "Dir kënnt ...", "Dir kënnt net ..."; "You do not have permission" is "Dir hutt
  net d'Berechtegung, ...".
- **Messages about a result**: the perfect: "Den Entworf gouf gespäichert.", "De Fichier gouf erfollegräich
  eropgelueden.".

## 3. Script and typography

- **Latin** alphabet with ä, ë, é and the other accents of the norm, as composed characters.
- **Capitals**: nouns, the first word of a button, title or sentence, proper names and the polite Dir forms.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder. Product names, code, file names
  and INI settings stay as they are.
- **Quotation marks**: as in the source (straight "..." or `&quot;`); entities are kept exactly.
- **Apostrophe**: the straight ' of the article d' (d'Lëscht, d'Astellungen) and of 't.
- **Punctuation**: as in the source; a comma before datt, wann, deen, déi as the norm wants it.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Luxembourgish | Note |
|---|---|---|
| content | Inhalt | content object: Inhaltsobjet |
| object | Objet | plural: Objeten |
| node | Knuet | plural: Kniet; main node: Haaptknuet; parent node: Elterknuet |
| location | Plaz | main location: Haaptplaz |
| item (list entry) | Element | |
| tree | Bam | tree menu: Bammenü; content structure: Inhaltsstruktur |
| class | Klass | content class: Inhaltsklass; class group: Klassegrupp |
| attribute | Attribut | |
| datatype | Datentyp | |
| version | Versioun | version history: Versiounshistorique |
| draft | Entworf | store draft: Entworf späicheren |
| publish | publizéieren | published: publizéiert; publishing: Publikatioun |
| send for publishing | fir d'Publikatioun schécken | |
| discard | verwerfen | discard draft: Entworf verwerfen |
| archived / pending / rejected | archivéiert / amgaang / refuséiert | |
| siteaccess | Site-Zougang | siteaccess as a setting name stays in code |
| site, website | Site, Websäit | |
| section | Sektioun | the Exponential section; an INI section: INI-Sektioun |
| role | Roll | plural: Rollen |
| policy | Richtlinn | |
| limitation | Aschränkung | |
| permission | Berechtegung | access denied: Zougang refuséiert |
| module | Modul | |
| function | Funktioun | |
| user / user group | Benotzer / Benotzergrupp | |
| group | Grupp | plural: Gruppen |
| anonymous | anonym | anonymous user: anonyme Benotzer |
| administrator | Administrateur | administration interface: Administratiounsinterface |
| username | Benotzernumm | |
| password | Passwuert | |
| log in / log out | umellen / ofmellen | the act: Umeldung / Ofmeldung |
| e-mail | E-Mail | e-mail address: E-Mail-Adress |
| cache | Cache | clear cache: Cache eidelmaachen |
| template | Schabloun | override template: Iwwerschreiwungsschabloun |
| override | Iwwerschreiwung | |
| fallback | Ersatz | fallback language: Ersatzsprooch |
| design | Design | |
| workflow | Aarbechtsoflaf | |
| trigger | Ausléiser | |
| event | Evenement | |
| trash | Dreckskëscht | empty trash: Dreckskëscht eidelmaachen; move to trash: An d'Dreckskëscht réckelen |
| bookmark | Lieszeechen | |
| settings | Astellungen | configuration: Konfiguratioun |
| setup | Konfiguratioun | the Setup tab |
| layout | Mise en page | Exponential Layouts stays |
| block / zone | Block / Zon | |
| extension | Extensioun | |
| package | Package | |
| search | Sich | to search: sichen; search index: Sichindex |
| log | Protokoll | a log file: Protokollfichier; never "log in" |
| queue | Waardeschlaang | job queue: Aufgabeschlaang |
| job / task | Aufgab | |
| kernel, core | Kär | |
| server | Server | |
| database | Datebank | |
| folder / directory | Dossier / Verzeechnes | |
| file | Fichier | |
| image | Bild | thumbnail: Miniatur |
| preview | Virschau | |
| upload / download | eroplueden / eroflueden | |
| export / import | exportéieren / importéieren | |
| remove | ewechhuelen | take out of a list or place |
| delete | läschen | destroy; deleted: geläscht |
| edit | änneren | |
| editor | Editeur | person or software |
| save / store | späicheren | the verb "Store" of the forms |
| Store (shop tab) | Buttek | the shop, as "Shop" |
| cancel | ofbriechen | |
| OK | OK | allowlisted where it stands alone |
| apply | uwenden | |
| back | zréck | |
| next / previous | nächst / vireg | |
| enable / disable | aktivéieren / desaktivéieren | enabled / disabled: aktivéiert / desaktivéiert |
| active / inactive | aktiv / inaktiv | |
| run | ausféieren | |
| start / stop | starten / stoppen | |
| session | Sessioun | |
| hidden / visible | verstoppt / siichtbar | hide / reveal: verstoppen / weisen |
| archive | Archiv | |
| order (sort) | Reiefolleg | sort order: Sortéierreiefolleg |
| order (shop) | Bestellung | |
| shop | Buttek | |
| basket | Kuerf | |
| product | Produit | |
| customer | Client | |
| discount | Remise | |
| VAT | TVA | |
| price / currency | Präis / Wärung | |
| tag | Tag | allowlisted where it stands alone; keyword: Schlagwuert |
| translation | Iwwersetzung | |
| language | Sprooch | main language: Haaptsprooch |
| locale | Regionalastellung | |
| state | Zoustand | the object state |
| status | Status | |
| view (button) | ukucken | a module view (noun): Usiicht |
| format | Format | |
| sort | sortéieren | |
| filter | Filter | to filter: filteren |
| select | auswielen | select all: All auswielen; selected: ausgewielt |
| copy / move | kopéieren / réckelen | |
| add / create / new | derbäisetzen / erstellen / nei | |
| restore | restauréieren | |
| install / uninstall | installéieren / desinstalléieren | |
| related objects | verbonnen Objeten | relation: Relatioun |
| dashboard | Iwwersiicht | |
| identifier / ID | Identifiant / ID | |
| priority | Prioritéit | |
| owner | Besëtzer | |
| modified / created | geännert / erstallt | |
| date / time | Datum / Zäit | |
| error / warning | Feeler / Warnung | |
| help | Hëllef | |
| notification | Notifikatioun | |
| account | Kont | |
| default | Standard | |
| link | Link | |
| none / all | keen / all | |
| yes / no | jo / neen | |
| name / title / description | Numm / Titel / Beschreiwung | |
| value / type / list | Wäert / Typ / Lëscht | |
| page / path | Säit / Pad | |
| required / optional | obligatoresch / fakultativ | |
| options | Optiounen | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is Buttek, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is Späicheren.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the
  act (Umellen).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, OK ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
