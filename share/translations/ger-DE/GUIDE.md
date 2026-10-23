# German (ger-DE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md).

## 1. Language and register

Standard German of Germany, neutral and technical, as in current German software. The German catalogue is the
reference translation of Exponential: it is maintained together with the English source, and other translations
are compared with it when a meaning is unclear.

`ger-DE` is the one German catalogue. The other German locales fall back to it (`settings/i18n.ini`,
`FallbackLanguages[]`): `ger-AT`, `ger-CH`, `ger-LI`, `ger-LU` and `ger-BE`. Avoid words that only Germany uses
where a general German word exists. Swiss readers see ß as written here; the catalogue does not switch to ss.

## 2. Form of address

The user is addressed formally with **Sie**, everywhere.

- **Buttons, menu items and links**: the infinitive, with the object first. "Speichern", "Abbrechen", "Bearbeiten",
  "Ausgewählte entfernen", "Entwurf speichern", "Entwurf verwerfen", "Papierkorb leeren", "Zur Veröffentlichung
  schicken". Not the imperative ("Speichern Sie").
- **Titles, column headings and tabs**: nouns. "Einstellungen", "Übersetzungen", "Hauptsprache", "Objektzustände".
- **Requests**: "Bitte" with the Sie imperative: "Bitte wählen Sie einen Ort aus.", "Bitte geben Sie eine gültige
  E-Mail-Adresse ein."
- **Instructions in hints and messages**: the Sie imperative: "Prüfen Sie die Verbindung und versuchen Sie es
  erneut."
- **Confirmations**: "Sind Sie sicher, dass Sie ... möchten?": "Sind Sie sicher, dass Sie diese Sprache entfernen
  möchten?". "Möchten Sie ... wirklich ...?" is also correct.
- **Statements about the user**: Sie, Ihr, Ihre: "Sie können die letzte Klasse, die Benutzerkonten enthält, nicht
  entfernen.", "Die E-Mail-Adresse Ihres Kontos wird geändert".
- Not allowed: du, dein, dich, dir; "man" where the user is meant; the imperative on buttons.

## 3. Script and typography

- **Sie, Ihr, Ihnen** in the form of address are always capitalized.
- **ß** after long vowels and diphthongs, as in the reformed spelling: "dass", "Größe", "schließen".
- **Quotation marks**: German quotes „...“ (U+201E, U+201C) in running text: „%name“. Straight quotes only where
  the source quotes code or a value that is typed exactly.
- **No space** before ":", ";", "?" and "!".
- **Compounds with an English word or a code** take a hyphen: "Cache-Verwaltung", "Template-Datei",
  "Siteaccess-Einstellungen", "Layout-Editor", "Upload-Verzeichnis", "INI-Cache", "XML-Tag", "E-Mail-Adresse".
  German compounds are written as one word: "Benutzergruppe", "Objektzustand", "Klassengruppe".
- **Dashes**: the en dash with spaces – for a break in a sentence; the em dash as in the source.
- **Numbers**: digits 0-9, decimals with a comma in prose ("1,5 MB").
- **Capitals**: German capitalizes nouns; otherwise only the first word of a button or title starts with a capital.
  No English title case.

## 4. Glossary

Binding for the kernel and every extension. The words marked **(en)** stay English in German, with a German
gender. Those that also stand alone as a message (Cache, Siteaccess, Extension, Layout, Template, Workflow, Login,
Log, Job, Upload, Dashboard, Setup, OK and more) are in the German list of
`tests/tests/lib/ezi18n/translation-identical-allowlist.json`, each with its reason. That list also holds words
spelled the same in both languages (Administration, Alias, Block, Design, Details, Filter, Format, Index,
Information, Installation, Name, Status, System, Text, Version and others).

| English | German | Note |
|---|---|---|
| content | Inhalt | content object: Inhaltsobjekt |
| object | Objekt | |
| node | Knoten | |
| location | Ort | the place of an object in the tree; Platzierung in a few older texts |
| main location | Hauptort | |
| site | Website | Site **(en)** is also used, e.g. in "Site-Design" |
| class | Klasse | the content class; a PHP class is eine PHP-Klasse |
| content class | Inhaltsklasse | the list of classes: Klassenliste |
| class group | Klassengruppe | |
| attribute | Attribut | |
| datatype | Datentyp | |
| identifier | Bezeichner | |
| remote ID | Remote-ID | |
| version | Version | |
| draft | Entwurf | |
| publish | veröffentlichen | |
| published | veröffentlicht | |
| send for publishing | zur Veröffentlichung schicken | |
| store draft | Entwurf speichern | |
| discard | verwerfen | |
| archived | archiviert | an archived version |
| pending | ausstehend | |
| rejected | abgelehnt | |
| siteaccess | Siteaccess **(en)** | masculine: der Siteaccess; plural Siteaccesses |
| section | Sektion | always the Exponential section; a part of a page is Abschnitt |
| role | Rolle | |
| policy | Richtlinie | |
| limitation | Einschränkung | |
| module | Modul | |
| function | Funktion | |
| permission | Berechtigung | |
| user | Benutzer | |
| user group | Benutzergruppe | |
| user account | Benutzerkonto | |
| log in | anmelden | the action: Anmeldung; the Login page or button: Login **(en)** |
| username | Benutzername | |
| log out | abmelden | |
| password | Passwort | |
| e-mail | E-Mail | |
| cache | Cache **(en)** | der Cache; clear cache: Cache leeren |
| template | Template **(en)** | das Template |
| override | Override **(en)** | override template: Überschreib-Template |
| fallback | Fallback **(en)** | fallback chain: Fallback-Kette |
| workflow | Workflow **(en)** | der Workflow |
| trigger | Trigger **(en)** | |
| event | Ereignis | |
| trash | Papierkorb | empty trash: Papierkorb leeren |
| bookmarks | Lesezeichen | |
| settings | Einstellungen | |
| configuration | Konfiguration | |
| setup | Setup **(en)** | the Setup tab; the setup wizard: Setup-Assistent |
| dashboard | Dashboard **(en)** | |
| layout | Layout **(en)** | das Layout; Exponential Layouts; plural Layouts |
| block | Block | a block of a layout |
| zone | Zone | |
| design | Design | |
| extension | Extension **(en)** | feminine: die Extension; plural Extensions |
| package | Paket | |
| kernel | Kernel **(en)** | |
| search | suchen | the noun: Suche |
| search index | Suchindex | indexing: Indizierung |
| search engine | Suchmaschine | |
| log | Log **(en)** | das Log; log file: Logdatei |
| queue | Warteschlange | queued: in der Warteschlange |
| job | Job **(en)** | der Job |
| cronjob | Cronjob **(en)** | |
| file | Datei | |
| folder | Ordner | |
| image | Bild | |
| thumbnail | Miniaturansicht | |
| media | Medien | media library: Medienbibliothek |
| preview | Vorschau | |
| upload | hochladen | the noun: Upload **(en)** |
| download | herunterladen | the noun: Download **(en)** |
| export | exportieren | the noun: Export |
| import | importieren | the noun: Import |
| remove | entfernen | take out of a list or place |
| delete | löschen | destroy |
| edit | bearbeiten | |
| editor | Editor | the software; a person who edits: Redakteur |
| save / store | speichern | the verb "Store" of the forms too |
| Store / shop | Shop **(en)** | webshop: Web-Shop |
| cancel | abbrechen | |
| OK | OK **(en)** | |
| back | zurück | |
| next / previous | weiter / zurück | in a list of items: nächste / vorherige |
| enable / disable | aktivieren / deaktivieren | |
| enabled / disabled | aktiviert / deaktiviert | |
| active / inactive | aktiv / inaktiv | |
| hidden / visible | verborgen / sichtbar | hide: verbergen |
| apply | anwenden | |
| reset | zurücksetzen | |
| clear | leeren | a cache, a list; a form field: zurücksetzen |
| archive | Archiv | |
| order (sort) | Reihenfolge | sort by: sortieren nach |
| order (shop) | Bestellung | |
| ascending / descending | aufsteigend / absteigend | |
| basket | Warenkorb | |
| wish list | Wunschliste | |
| product | Produkt | |
| customer | Kunde | |
| discount | Ermäßigung | |
| VAT | USt | |
| tag (keyword) | Schlagwort | eztags; plural Schlagwörter |
| tag (HTML/XML) | Tag **(en)** | HTML-Tag, Custom-Tag |
| translation | Übersetzung | |
| language | Sprache | main language: Hauptsprache |
| locale | Locale **(en)** | |
| state | Zustand | the object state; object states: Objektzustände |
| status | Status | |
| view (button) | ansehen | |
| view (noun) | Ansicht | a module view |
| format | Format | |
| select | auswählen | invert selection: Auswahl umkehren |
| browse | durchsuchen | |
| copy | kopieren | |
| move | verschieben | |
| add | hinzufügen | |
| create | erstellen | created: erstellt |
| new | neu | |
| restore | wiederherstellen | |
| install / uninstall | installieren / deinstallieren | |
| sub items | Unterelemente | |
| children / parent | untergeordnet / übergeordnet | |
| subtree | Teilbaum | |
| related objects | verknüpfte Objekte | relations: Verknüpfungen |
| URL alias | URL-Alias | |
| content structure | Inhaltsstruktur | |
| owner | Eigentümer | |
| creator / author | Ersteller / Autor | |
| modified | geändert | |
| priority | Priorität | |
| session | Sitzung | |
| debug | Debug **(en)** | |
| default | Standard | |
| none | keine / keiner | agrees with the noun |
| yes / no | ja / nein | |
| help | Hilfe | |
| link | Link **(en)** | |
| sitemap | Sitemap **(en)** | |

## 5. Decisions after the translation

- **English terms that stay English.** Siteaccess, Extension, Cache, Template, Override, Layout, Workflow,
  Dashboard, Setup, Log, Job, Cronjob, Login, Upload, Download, Tag (HTML), Locale and the others marked (en) are
  the words German administrators and the German documentation use. A German coinage (Seitenzugang,
  Zwischenspeicher, Vorlage, Örtlichkeit, Arbeitsablauf) was not recognizable. New entries go into the allowlist
  with their reason before the translation uses them.
- **Siteaccess** replaces the older glossary entry "Seitenzugang", which the catalogue no longer uses.
- **Status and Zustand** are kept apart: Status is a status (of a version, a job, an order), Zustand is the object
  state of the Exponential states feature.
- **Remove and delete** are kept apart: entfernen takes something out of a list or place, löschen destroys.
- `share/translations/ger-DE/readme.txt` and `phrasebook.qph` describe the translation process of 2010 and its
  word list. They are history; where they differ from this guide, this guide applies.
