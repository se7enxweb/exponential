# Plan: Benutzerhandbücher und Hilfe, gepflegt in Exponential

Status: Vorschlag, 18. Oktober 2026; Phase 1 (Klassen, Baum, Sektionen, Zustände, Rollen, Validator, Web- und Kontexthilfe
in der Hilfe der Administration) umgesetzt in 6.0.15, siehe [Hilfe als Inhalt gepflegt](../../../en/us/features/6.0/help-content.md).
Begleitseite: [der Styleguide für die Hilfe](hilfe-stilrichtlinien.md).

## Idee

Die Hilfe einer Site ist **Inhalt dieser Site**. Sie wird in der Administrationsoberfläche
von Exponential geschrieben, geprüft und freigegeben wie jeder andere Inhalt und
von dort an drei Stellen ausgeliefert: an die öffentliche Site, an einen JSON-Endpunkt für
Kontexthilfe in anderen Clients und in E-Books (EPUB/PDF). Es gibt kein separates
Dokumentationswerkzeug und keine zweite Kopie des Textes.

Der Plan beschreibt das Inhaltsmodell, die Zugriffsregeln, den Workflow, die Auslieferungswege
und die Prüfungen. Er ist unabhängig von einer bestimmten Site: Zielgruppen, Produktnamen
und Bereiche in den Beispielen sind Platzhalter, die jede Installation selbst
füllt.

## Entscheidungen

| # | Frage | Entscheidung |
|---|---|---|
| 1 | Dokumentationsstandard | **DITA 1.3** (OASIS) als Informationsmodell; Inhaltsregeln nach IEC/IEEE 82079-1:2019 und ISO/IEC/IEEE 26514; E-Books nach EPUB Accessibility 1.1 / WCAG 2.2 AA |
| 2 | Wiederverwendung | **Topic-Pool + Maps**: Jedes Topic existiert genau einmal; Handbücher verweisen über eine geordnete Relationsliste auf Topics |
| 3 | Sprachen | Das Modell ist von Anfang an übersetzbar (Exponential-Übersetzungen pro Objekt, `xml:lang` im Export). Welche Sprachen geschrieben werden, entscheidet jede Installation |
| 4 | Zugriff | Was ein Leser **vor** dem Anlegen eines Kontos braucht, ist öffentlich. Alles andere erfordert eine Anmeldung und eine passende Rolle |

## Zielgruppen

Die Zielgruppen sind eine konfigurierbare Liste. Eine typische Installation:

| Zielgruppe | Wer | Zugriff | Kanal |
|---|---|---|---|
| Besucher | jeder auf der öffentlichen Site | öffentlich | Web |
| Mitglieder | registrierte Benutzer der Site | angemeldet | Web, E-Book, Kontexthilfe |
| Redakteure | Inhaltsredakteure in der Administrationsoberfläche | angemeldet (Admin-Siteaccess) | Administration, E-Book |
| Administratoren | Administratoren der Site | angemeldet (Admin-Siteaccess) | Administration, E-Book |

Dokumentation für Betrieb und IT (Installation, Bereitstellung, Servereinrichtung)
**bleibt im Repository** neben dem Code, nicht im CMS: Sie muss
lesbar sein, wenn das CMS nicht läuft.

## Der Standard: was DITA hier bedeutet

- Jedes Topic hat **genau einen** Typ: `concept`, `task`, `reference`,
  `troubleshooting`. Es gibt keine Mischformen; eine „FAQ“ ist eine Ansicht, kein Typ.
- Ein Handbuch ist eine **Bookmap**; Kapitel sind `topichead`/`chapter` mit
  `topicref`s.
- Zielgruppe, Plattform und Version sind DITA-Filterattribute (`audience`,
  `platform`, `rev`/`props`); eine DITAVAL-Datei pro Handbuch erzeugt die Ausgabe
  für eine Zielgruppe.
- Jedes Topic hat eine **`shortdesc`** (einen Satz). Sie ist zugleich Linktext, Suchtreffer
  und Vorschau.
- 82079-1 verlangt Sicherheits- und Datenschutzhinweise **dort, wo die Handlung
  stattfindet** (im Schritt, nicht in einem Anhang), Voraussetzungen vor den Schritten
  und ein Ergebnis danach.

## Inhaltsklassen (Präfix `help_`)

Weder das alte `documentation_page` noch `article`: Die Hilfe braucht Felder,
die diese Klassen nicht haben, und sie darf nicht mit den redaktionellen Inhalten der Site
vermischt werden. Zwei harte Bedingungen:

- **Die Hilfe ist nie eine Quelle für suchbasierte KI-Antworten (RAG)**, es sei denn, das ist
  ausdrücklich gewollt. Sonst wird die Hilfe zu einer zitierfähigen Quelle für
  Fragen, für deren Beantwortung sie nie geschrieben wurde.
- **Die Hilfe liegt nicht unter dem regulären Inhaltsbaum der Site**, damit sie
  nicht in Menüs, Listen und Feeds der Site auftaucht.

### Struktur (Maps)

| Klasse | Container | Felder |
|---|---|---|
| `help_manual` (Handbuch) | ja | Titel, Untertitel, Zielgruppe (mehrfach), Ausgabe/Version, Datum, Kurzbeschreibung, Titelbild (`help_screenshot`), Lizenz, Autoren, `topicrefs` (geordnete `ezobjectrelationlist` aus `help_section` und Topics) |
| `help_section` (Kapitel) | ja | Titel, Kurzbeschreibung, `topicrefs` |

### Topics (im Pool)

Felder, die alle vier Typen gemeinsam haben:

| Feld | Datentyp | Zweck |
|---|---|---|
| `title` | ezstring | Titel |
| `shortdesc` | ezstring (max. 250) | ein Satz, Pflichtfeld |
| `help_id` | ezstring, eindeutig, `[a-z0-9.-]` | stabiler, sprachneutraler Schlüssel: Kontexthilfe, DITA-`id`, Dateiname im Export |
| `audience` | ezselection (mehrfach) | die Zielgruppen der Installation |
| `platform` | ezselection (mehrfach) | z. B. Web, Administration, Mobil |
| `version_from` / `version_to` | ezstring | Gültigkeit (`to` leer = aktuell) |
| `review_date` | ezdate | nächste Überprüfung |
| `owner` | ezobjectrelation (Benutzer) | verantwortliche Person |
| `related` | ezobjectrelationlist | DITA-`related-links` |
| `figures` | ezobjectrelationlist (`help_screenshot`) | Abbildungen, geordnet |

Pro Typ:

| Klasse | Felder |
|---|---|
| `help_concept` | `body` |
| `help_task` | `context`, `prereq`, `steps` (genau **eine** nummerierte Liste, vom Validator geprüft), `result`, `postreq` |
| `help_reference` | `body` (Tabellen erlaubt und erwartet) |
| `help_troubleshooting` | `condition`, `cause`, `remedy` (DITA 1.3) |

### Bausteine

| Klasse | Felder |
|---|---|
| `help_screenshot` | Bild, **Alternativtext (Pflichtfeld)**, Bildunterschrift, Plattform, Version, Sprache, Abschnitt des Topics, zu dem es gehört, Abbildungs-ID |
| `help_glossary_entry` | Begriff, Definition, **zu vermeidende Synonyme**, Zielgruppe, Anmerkung |

Die Terminologie driftet, sobald mehr als eine Person schreibt. Das Glossar
entscheidet jedes Paar konkurrierender Wörter; der Validator meldet die abgelehnte
Form.

**Auswahloptionen werden immer nur angehängt.** `ezselection` speichert die
Position einer Option. Wird die Liste umsortiert, erhält jedes Topic stillschweigend eine
andere Zielgruppe und über den Validator eine andere Sektion.

### Status

Als **Zustandsgruppe** `help_status` („Entwurf“ → „Prüfung“ → „Freigegeben“), nicht als
Feld: Objektzustände können als Einschränkungen von Rollen verwendet werden. Nur „Freigegeben“ wird
ausgeliefert (Web, Endpunkt, E-Book).

Der Zustand gehört zum **Objekt**, nicht zur Version. Wer ein
freigegebenes Topic bearbeitet und veröffentlicht, liefert die Änderung sofort aus. Deshalb
darf die Redaktionsrolle nur Topics im Zustand „Entwurf“ oder „Prüfung“ bearbeiten (siehe „Berechtigungen“).

### Zugriff über Sektionen

Zwei Sektionen, `help_public` und `help_internal`. Die Rolle `Anonymous` erhält
`content/read` **nur** mit einer Einschränkung auf `help_public` **und** den Zustand
„Freigegeben“ – eine Richtlinie mit beiden Einschränkungen, nicht zwei Richtlinien (zwei würden
Lesezugriff bei jeder der beiden Bedingungen gewähren). Ein Topic ist öffentlich, wenn seine `audience`
eine öffentliche Zielgruppe enthält (im Beispiel oben die Besucher); andernfalls ist es
intern, und ein Topic ohne Zielgruppe ist als sichere Rückfallebene intern.

Die Sektion setzt das Validator-Skript anhand von `audience`, **niemals von
Hand**. Von Hand gesetzt, driftet sie, sobald jemand die Zielgruppe ändert, und
ein internes Topic landet im offenen Web.

Die Sektion wird **nur am Objekt** gesetzt, nie am Teilbaum: Ein Handbuch ist ein
Container, und eine Zuweisung an den Teilbaum würde die Entscheidungen des
Validators bei jedem Lauf wieder rückgängig machen. Wird ein Objekt in der Administrationsoberfläche verschoben, erhält es die
Sektion seines neuen übergeordneten Knotens; führen Sie den Validator danach erneut aus.

Das Glossar ist immer intern, unabhängig von seiner Zielgruppe: Es ist ein redaktionelles
Werkzeug, und seine Anmerkungen sind nicht für anonyme Leser bestimmt.

### Baum

```
Help                          (folder, remote_id help-root)
├── Topic pool                (folder, remote_id help-pool)
│   ├── Concepts / Tasks / Reference / Troubleshooting
│   ├── Figures               (help_screenshot)
│   └── Glossary              (help_glossary_entry)
├── Manuals                   (folder, remote_id help-manuals)
│   ├── Manual for members
│   ├── Manual for editors
│   └── Manual for administrators
└── E-books                   (folder, remote_id help-ebooks)
```

Alles wird über die **`remote_id`** gefunden, nie über eine Knoten-ID in einer INI-Datei.
Knoten-, Sektions- und Zustands-IDs unterscheiden sich zwischen Installationen und werden zur
Laufzeit aufgelöst.

## Berechtigungen

| Rolle | Darf |
|---|---|
| Hilfe-Redaktion | `content/create`, `content/edit`, `content/read` im Teilbaum `help-root`; `edit` und `remove` eingeschränkt auf die Zustände „Entwurf“ und „Prüfung“; `state/assign` von „Entwurf“ nach „Prüfung“ |
| Hilfe-Freigabe | `state/assign` für alle drei Zustände, **zusätzlich** zur Redaktionsrolle zugewiesen |
| Anonymous | `content/read` eingeschränkt auf die Sektion `help_public` **und** den Zustand „Freigegeben“ |

Wer ein Topic geschrieben hat, ist nicht die Person, die es freigibt. Eine Änderung an einem
freigegebenen Topic, selbst ein Tippfehler: Jemand mit „Hilfe-Freigabe“ setzt es zurück auf
„Prüfung“, die Redaktion ändert es, dann wird es erneut freigegeben.

## Auslieferungswege

1. **Web (öffentlich)**: Templates `full/help_*.tpl` und `line/help_*.tpl` im
   Design der Site. Relationen (`topicrefs`, `related`, `figures`) werden nur angezeigt, wenn
   der Leser das Ziel lesen darf; über den Zugriff entscheiden allein die Berechtigungen von
   Exponential, die Templates filtern nicht nach Zielgruppe.
2. **Kontexthilfe**: ein JSON-Endpunkt `/<module>/help/<help_id>` und
   `/<module>/help/manual/<remote_id>`, Inhalt in Markdown, ETag. Öffentliche Topics
   ohne Anmeldung; interne Topics mit einem Token **und** einer passenden Rolle. Eine
   mobile App oder ein JavaScript-Frontend öffnet die Hilfe über die
   `help_id` an der richtigen Stelle.
3. **E-Book**: CLI `export_manual.php -s <admin siteaccess> --manual=<remote_id>
   --format=dita|epub|pdf [--dry-run]`. Exponential → DITA-Bookmap + DITAVAL →
   DITA-OT 4.x → EPUB3/PDF → **epubcheck** + **Ace by DAISY** → gespeichert als
   Dateiobjekt unter „E-books“ (Sektion entsprechend der Zielgruppe).
4. **Verlauf**: Der DITA-Export läuft regelmäßig in ein eigenes Git-Repository.
   Der Inhalt lebt im CMS, der Änderungsverlauf landet trotzdem in Git.

## CLI-Skripte

Alle Skripte:

- nehmen `-s <siteaccess>` als **Pflichtoption** und geben Siteaccess und
  Datenbank aus, bevor sie beginnen (ein Verzeichnisbaum kann mehrere Installationen mit
  verschiedenen Datenbanken bedienen);
- folgen `startup()` → `getOptions()` → `initialize()` und rufen
  `initialize()` genau einmal auf;
- unterstützen `--dry-run`;
- sind idempotent: Ein zweiter Lauf meldet „No change.“ – das ist der
  Abnahmetest.

| Skript | Tut |
|---|---|
| `create_classes.php` | legt die acht `help_*`-Klassen in einer Klassengruppe „Help“ an; vorhandene Klassen werden nur erweitert, vorhandene Attribute werden nie geändert |
| `create_tree.php` | Sektionen, Zustandsgruppe, Ordnerbaum |
| `create_roles.php` | die zwei Hilfe-Rollen und die zusätzliche Richtlinie für `Anonymous` |
| `check_help.php` | der Validator (siehe unten); `--fix` setzt die Sektion anhand der Zielgruppe; Exit-Code 1, solange ein ERROR in der Datenbank steht |
| `seed_glossary.php` | legt das anfängliche Glossar aus einer Datendatei an |
| `seed_topics.php` | legt anfängliche Topics und Handbücher aus einer Datendatei an; vorhandene Objekte bleiben so, wie die Redaktion sie hinterlassen hat, es sei denn, `--update` wird angegeben |

Zwei Tatsachen des Kernels, mit denen die Skripte umgehen müssen:

- Der erste Zustand einer neuen Zustandsgruppe wird **jedem** vorhandenen
  Objekt der Installation zugewiesen. Das ist harmlos, solange nur Hilfe-Richtlinien
  die Gruppe auswerten.
- `ezcobj_state_language.description` hat in MySQL keinen Standardwert. Ohne
  Beschreibung schlägt das INSERT unter `STRICT_TRANS_TABLES` fehl; das Skript muss
  eine setzen.

## Prüfungen

- Klassenskript idempotent (zweiter Lauf: keine Änderung).
- Hilfe nie in einer Quellenliste für RAG/KI, nie unter dem Inhaltsbaum der Site.
- `Anonymous` liest nur `help_public` + „Freigegeben“ (prüfbar mit einer Untersuchung der
  Rolle und mit einer anonymen Anfrage nach einem internen Topic, die
  keinen Inhalt liefern darf).
- Validator (`check_help.php`):
  - `task` ohne genau eine nummerierte Liste;
  - Screenshot ohne Alternativtext, Alternativtext länger als 150 Zeichen;
  - Topic ohne `shortdesc`, `shortdesc` länger als ein Satz;
  - ungültige oder doppelte `help_id`;
  - ein Wort, das das Glossar ablehnt (fett gesetzter Text ist eine zitierte Beschriftung der Oberfläche und wird nicht
    geprüft);
  - Überprüfungsdatum in der Vergangenheit;
  - Sektion, die nicht zur Zielgruppe passt.
- Clients, die die Hilfe über die `help_id` aufrufen, führen eine Liste der IDs, die sie verwenden; ein Test
  prüft, dass jede davon im Export vorhanden ist.

## Phasen

1. Grundlagen: Klassen, Baum, Sektionen, Zustände, Rollen.
2. Redaktioneller Styleguide ([help-style-guide.md](hilfe-stilrichtlinien.md)) und Glossar.
3. Validator.
4. Web-Templates und Endpunkt für die Kontexthilfe.
5. E-Book-Export mit epubcheck und Ace.
6. Inhalte: zuerst, was Besucher brauchen, dann Mitglieder, Redakteure, Administratoren.
7. Pflege: eine Liste veralteter Topics (Überprüfungsdatum überschritten) im Dashboard
   der Administration, „War das hilfreich?“ bei jedem Topic.

## Siehe auch

- [Styleguide für die Hilfe](hilfe-stilrichtlinien.md)
- [Sektionen](../../anleitungen/sektionen.md)
- [Objektzustände](../../anleitungen/objektzustaende.md)
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
