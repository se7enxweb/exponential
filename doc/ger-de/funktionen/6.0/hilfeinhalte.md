# Hilfe als Inhalt gepflegt

Status: Phase 1 in 6.0.15 (18. Oktober 2026); der DITA-Export, die gespeicherten E-Books und die überfälligen Prüfungen
am 20. Oktober 2026 ([Hinweis zur Aktualisierung](../../bc/6.0/hilfeinhalte-dita-ebooks-reviews.md)). Der Plan ist
[help-system.md](../../spezifikationen/6.0/hilfesystem.md), die redaktionellen Regeln stehen im
[Styleguide der Hilfe](../../spezifikationen/6.0/hilfe-stilrichtlinien.md). Für alle, die die Hilfe schreiben und
veröffentlichen: [die Hilfe aktuell halten](../../hilfe/hilfethemen-pflegen.md).

Hilfethemen, Handbücher, Abbildungen und das Glossar sind **Inhalte der Site**: in der Administrationsoberfläche
geschrieben, geprüft und veröffentlicht wie alle anderen Inhalte und in der Hilfe der Administrationsoberfläche
(`exphelp`, siehe [help-system.md](hilfesystem.md)) neben den Seiten aus `doc/` ausgeliefert, sowie als JSON für die
Kontexthilfe in anderen Clients.

## Was Phase 1 bringt

| Teil | Wo |
|---|---|
| Die acht Klassen `help_manual`, `help_section` (Kapitel), `help_concept`, `help_task`, `help_reference`, `help_troubleshooting`, `help_screenshot`, `help_glossary_entry` in der Klassengruppe "Help" | `./console exp:help:content classes` |
| Die Sektionen `help_public` und `help_internal`, die Objektstatusgruppe `help_status` (Entwurf, Prüfung, Veröffentlicht), der Ordnerbaum (`help-root`, `help-pool` ..., über die remote_id gefunden) | `./console exp:help:content tree` |
| Die Rollen "Help editor" und "Help release" sowie die eine zusätzliche Richtlinie von Anonymous | `./console exp:help:content roles` |
| Der Validator des Styleguides; `--fix` setzt die Sektionen anhand der Zielgruppe | `./console exp:help:content check` |
| Das anfängliche Glossar und die ersten Themen (die Vorlagen des Styleguides, ein Handbuch) | `./console exp:help:content seed-glossary`, `seed-topics` |
| Was installiert ist, Deinstallation | `./console exp:help:content report`, `uninstall` |
| Ein E-Book (EPUB, PDF) eines Handbuchs | `./console exp:help:content export`, **EPUB/PDF herunterladen** auf der Seite des Handbuchs |
| Der DITA-1.3-Export (Bookmaps, DITAVAL, Themen, Glossar), nach dem Schreiben geprüft | `./console exp:help:content dita`, **DITA herunterladen** auf der Seite des Handbuchs und in der Liste der Handbücher |
| Die E-Books, gespeichert als Dateiobjekte unter E-books, versioniert, bei der Veröffentlichung neu erstellt | `./console exp:help:content ebooks` und jede Veröffentlichung eines Themas |
| Die Themen mit überfälliger Prüfung (Prüfdatum überschritten, zu lange in Prüfung) | das Dashboard der Hilfe, `./console exp:help:content review` |
| Die acht Klassen als Inhaltsklassen-Paket `exp_help_classes` (.ezpkg) und seine Prüfung | `./console exp:help:content package`, `verify-package` ([das Paket](../../../eng-us/specifications/6.0/help-content-package.md)) |
| Ein Thema in der Hilfe: `exphelp/topic/<help_id>`; ein Handbuch: `exphelp/manual/<remote_id>`; die Liste der Handbücher: `exphelp/manual` | Admin, Richtlinie `exphelp/read` |
| Kontexthilfe als JSON: `exphelp/context/<help_id>`, `exphelp/context/manual/<remote_id>` | Richtlinie `exphelp/context` |

Der Plan nennt die Skripte `create_classes.php`, `create_tree.php`, `create_roles.php`, `check_help.php`,
`seed_glossary.php` und `seed_topics.php`. Sie sind die Aktionen `classes`, `tree`, `roles`, `check`, `seed-glossary`
und `seed-topics` eines einzigen Befehls, `./console exp:help:content` (`bin/php/exphelpcontent.php`), und verhalten
sich so, wie der Plan es beschreibt:

- `-s <siteaccess>` ist **Pflicht**; der Befehl gibt Siteaccess und Datenbank aus, bevor er beginnt;
- `--dry-run` zeigt, was sich ändern würde, und ändert nichts;
- jeder Schritt ist idempotent: ein zweiter Lauf gibt `No change.` aus (der Abnahmetest des Plans);
- `check` endet mit 1, solange ein ERROR in der Datenbank steht.

```
./console exp:help:content install -s admin --dry-run      # the plan
./console exp:help:content install -s admin                # classes, tree, roles
./console exp:help:content install -s admin                # No change.
./console exp:help:content seed-glossary -s admin
./console exp:help:content seed-topics -s admin --release  # the style guide's templates, Released
./console exp:help:content check -s admin --fix
./console exp:help:content report -s admin
```

`--login=<user>` gibt an, wer die Klassen und Objekte anlegt (Standard `admin`). Die `seed-*`-Aktionen nehmen
`--file=<json>` an (im Format von `share/help/seed-topics.json` und `share/help/seed-glossary.json`) und lassen
vorhandene Objekte so, wie die Redakteure sie hinterlassen haben, sofern nicht `--update` angegeben ist.

### Bei einer neuen Installation

Der Installer selbst legt nichts an. Führen Sie nach einer Installation einmal `./console exp:help:content install -s
<admin siteaccess>` aus (und `seed-glossary`, `seed-topics`, wenn Sie die Startinhalte möchten): Derselbe Befehl, der
eine bestehende Site erweitert, legt auf einer neuen alles an und findet Vorhandenes über Bezeichner und remote_id,
sodass eine Site, die aus einem Paket mit den Klassen erstellt wurde, nur ergänzt wird. Die Daten, die der Installer
braucht, sind Dateien des Kernels: das Inhaltsmodell in `expHelpContentSchema`, die Startdaten in `share/help/`.

## Das Inhaltsmodell

Wie im Plan, mit diesen Entscheidungen dort, wo er eine Wahl lässt:

- **Textfelder sind `eztext` mit Markdown** (`body`, `context`, `prereq`, `steps`, `result`, `postreq`,
  `condition`, `cause`, `remedy`, im Glossar `definition`, `avoid`, `note`). Die Hilfe rendert sie mit ihrem sicheren
  Markdown-Renderer; der JSON-Endpunkt liefert sie unverändert aus ("Markdown body", Plan, Auslieferungsweg 2);
  der Validator erkennt die eine nummerierte Liste einer Aufgabe und die **fett** gesetzten UI-Beschriftungen des
  Styleguides. Rohes HTML wird maskiert, wie auf jeder Hilfeseite.
- Ein Link auf ein anderes Thema wird `[text](help:<help_id>)` geschrieben (bei Bedarf mit `#anchor`); die Hilfe macht
  daraus die Adresse des Themas.
- `audience` und `platform` sind `ezselection` (Mehrfachauswahl). Ihre Optionen stammen aus `exphelp.ini [ContentSettings]
  Audiences[]`, `Platforms[]` (Standard: Visitors, Members, Editors, Administrators; Web, Admin, Mobile). Der Befehl
  **hängt fehlende Optionen nur an** und ordnet nie um: `ezselection` speichert die Position einer Option.
- Das Feld "licence" des Handbuchs aus dem Plan ist das Attribut `license`. Der "Abschnitt des Themas, zu dem sie gehört"
  einer Abbildung ist das Attribut `section` (das Feld des Themas, etwa `steps`), nicht die Exponential-Sektion der
  Abbildung.
- Die Wurzel der Hilfe liegt standardmäßig unter der Wurzel **Medien** (`exphelp.ini [ContentSettings] ParentNode=media`;
  `content`, `users` oder eine remote_id sind möglich): nicht im Inhaltsbaum der Site, sodass sie in keinem Menü,
  keiner Liste und keinem Feed der Site auftaucht. Der Kernel hat keine Liste von Quellen für KI-Antworten (RAG); eine
  Extension, die eine hinzufügt, muss die Klassen `help_*` und `help-root` auslassen, sofern die Site sie nicht will.

### Sektionen und Status

- Ein Thema ist **öffentlich** (Sektion `help_public`), wenn seine Zielgruppe eine öffentliche Zielgruppe enthält
  (`[ContentSettings] PublicAudiences[]`, Standard Visitors); andernfalls und ohne Zielgruppe ist es **intern**
  (`help_internal`). Abbildungen sind öffentlich, sobald ein öffentliches Thema auf sie verweist; Kapitel, sobald ein
  öffentliches Handbuch sie aufführt (direkt oder über Kapitel); das Glossar ist immer intern. Die Ordner sind intern.
- Die Sektion setzt `check --fix` (und jeder `seed-*`-Lauf danach), **nur am Objekt**, nie an einem Teilbaum.
  Wird ein Objekt verschoben, erhält es die Sektion seines neuen Elternknotens: Führen Sie danach `check --fix` aus.
- `help_status` ist Entwurf (der Standard jedes neuen Objekts), Prüfung, Veröffentlicht. Nur Veröffentlichtes wird
  ausgeliefert. Der erste Status der neuen Gruppe wird einmalig jedem Objekt der Installation zugewiesen (ein INSERT;
  nur Hilfe-Richtlinien werten die Gruppe aus). Jeder Status hat eine Beschreibung (MySQL im Strict-Modus kennt dafür
  keinen Standardwert).

### Rollen

| Rolle | Richtlinien |
|---|---|
| Help editor | `content/read`, `versionread`, `create` im Teilbaum `help-root`; `content/edit` und `remove` dort in den Status Entwurf und Prüfung; `state/assign` von Entwurf nach Prüfung; `exphelp/read` |
| Help release | `state/assign` in `help-root`, von jedem Status in jeden Status; **zusätzlich** zur Redakteursrolle zugewiesen |
| Anonymous | **eine** Richtlinie `content/read`, eingeschränkt auf die Sektion `help_public` **und** den Status Veröffentlicht |

Die Rollen werden angelegt, nicht zugewiesen: Weisen Sie sie in der Administrationsoberfläche den Benutzergruppen der
Redakteure zu. Eine Richtlinie wird anhand von Modul, Funktion und Einschränkungswerten verglichen; eine fehlende wird
hinzugefügt, nichts wird geändert oder entfernt.

**Opt-in:** `exphelp.ini [ContentSettings] AnonymousContextHelp=enabled` (Standard `disabled`) lässt den Rollenschritt
Anonymous zusätzlich `exphelp/context` geben, sodass das JSON **öffentlicher, veröffentlichter** Themen ohne Anmeldung
gelesen werden kann (eine öffentliche Site, eine mobile App). Ohne diese Einstellung braucht der Endpunkt eine Anmeldung
(Session oder die Token-Anmeldung der Site) und die Richtlinie.

## Der Validator

`./console exp:help:content check -s <siteaccess> [--fix] [--json]` prüft jedes Thema, jede Abbildung, jedes Kapitel,
jedes Handbuch und jeden Glossareintrag unterhalb von `help-root` (`expHelpContentValidator`, keine Datenbank in den
Regeln selbst):

| Stufe | Code | Regel (Abschnitt des Styleguides) |
|---|---|---|
| ERROR | `field.required` | die Pflichtfelder des Thementyps (1); Titel und Kurzbeschreibung eines Handbuchs |
| ERROR | `help-id.invalid`, `help-id.duplicate` | `area.topic[.variant]`, a-z 0-9, Bindestriche im Inneren, 2+ Teile, 80 Zeichen; eindeutig (7) |
| WARNING | `help-id.area`, `help-id.type` | der Bereich steht in `[ContentSettings] Areas[]`; der Thementeil benennt den Gegenstand, nicht den Typ (7) |
| ERROR | `shortdesc.sentences`, `shortdesc.too-long` | ein Satz, höchstens 250 Zeichen (8) |
| WARNING | `shortdesc.full-stop`, `shortdesc.markup`, `shortdesc.this-topic`, `shortdesc.repeats-title` | (8) |
| ERROR | `task.steps-list` | `steps` ist genau eine nummerierte Liste (4) |
| WARNING | `task.sub-steps`, `task.too-many-steps`, `task.steps-elsewhere` | keine Unterschritte, höchstens 9, keine Schritte in context/prereq/result (4) |
| WARNING | `notice.signal-word` | kein DANGER, WARNING, CAUTION (5) |
| WARNING | `notice.position` | ein Hinweis **Attention:** oder **Data protection:** einer Aufgabe steht in ihrem Schritt, nicht in `context`, `prereq`, `result` oder `postreq` (5) |
| WARNING | `notice.before-action`, `notice.per-step` | in einem Schritt steht ein solcher Hinweis zuerst, vor der Handlung; ein Hinweis pro Schritt (5) |
| WARNING | `notice.too-long` | ein Hinweis außerhalb der Schritte hat höchstens drei Sätze (5) |
| ERROR | `client.help-id-unknown` | eine help_id in `[ContentSettings] ContextHelpIds[]` oder `ClientHelpIds[]`, die kein Thema hat (Plan, "Checks") |
| WARNING | `client.help-id-not-released` | eine solche help_id nennt ein Thema, das noch nicht veröffentlicht ist: Clients erhalten bis dahin einen 404 |
| WARNING | `glossary.avoid` | ein Wort, das das Glossar vermeidet; fetter Text (eine zitierte UI-Beschriftung) und Code werden nicht geprüft (Terminologie) |
| ERROR/WARNING | `figure.alt-missing`, `figure.alt-too-long` | Alternativtext erforderlich; mehr als 150 Zeichen sind bei einer generierten Abbildung (mit `figure_id`) ein ERROR, sonst eine WARNING (6) |
| WARNING | `figure.alt-image-of`, `figure.alt-caption`, `figure.id-format` | (6) |
| ERROR | `figure.image-missing`, `related.target`, `figures.target`, `map.target`, `map.cycle` | Relationen verweisen auf das, worauf sie verweisen sollen |
| WARNING | `map.empty`, `map.duplicate`, `map.orphan-chapter` | |
| WARNING | `review-date.past`, `version.format` | (8) |
| NOTICE | `field.recommended` | `platform`, `version_from`, `review_date`, `owner` sind leer (1, 8) |
| ERROR | `section.mismatch` | die Sektion passt nicht zur Zielgruppe; `--fix` setzt sie |

Ein Hinweis ist eine fette Beschriftung mit Doppelpunkt: `**Attention:**`, `**Data protection:**` oder `**Note:**`
(auch `**Attention**:`), außerhalb von Code. Im Hilfe-Viewer wird ein Blockzitat, das mit einer solchen Beschriftung
beginnt (`> **Attention:** ...`), oder ein Container (`:::data-protection`) zu einem hervorgehobenen Kasten mit diesem
Titel; Datenschutz hat ein eigenes Symbol.

Nicht maschinell prüfbar und der Prüfung überlassen: Minimalismus, einfache Sprache, eine Handlung pro Schritt, die
Länge eines Hinweises innerhalb seines Schritts (Hinweis und Handlung teilen sich den Text des Schritts), ob eine
Beschriftung zur Oberfläche passt.

## In der Hilfe (exphelp)

- `exphelp/topic/<help_id>`: das Thema im Layout des Hilfe-Viewers, sein Typ, seine Zielgruppen, Plattform,
  Gültigkeit, die gerenderten Felder (Überschriften Bevor Sie beginnen, Schritte, Ergebnis ... bei Aufgaben;
  Bedingung, Ursache, Abhilfe bei der Fehlerbehebung), seine Abbildungen und verwandten Themen, die Liste der Themen
  und Handbücher. "Dieses Thema bearbeiten" für alle, die es bearbeiten dürfen; "JSON" öffnet die Antwort der
  Kontexthilfe.
- `exphelp/manual/<remote_id>`: das Inhaltsverzeichnis des Handbuchs, Kapitel verschachtelt; `exphelp/manual`: die
  Handbücher.
- Wer was sieht: allein die Berechtigungen von Exponential. Ein **veröffentlichtes** Thema wird allen angezeigt, die es
  lesen dürfen (`content/read`, mit den Einschränkungen nach Sektion und Status). Ein Thema im Entwurf oder in Prüfung
  sehen nur diejenigen, die es **bearbeiten** dürfen (Hilfe-Redakteure), mit einem Abzeichen **Vorschau**. Alles andere
  ist ein 404, genau wie eine unbekannte help_id. Relationen (verwandte Themen, Abbildungen, die Einträge eines
  Handbuchs) werden nur angezeigt, wenn der Leser ihr Ziel sehen darf.
- Suche, Dashboard und Kontextzuordnung der Hilfe: Die Provider-Klasse `expHelpContentProvider` hat die Hooks
  (`search()`, `topics()`, `manuals()` und als Kontext-Provider `helpContext()` mit
  `[ContentSettings] ContextHelpIds[<module>/<view>]=<help_id>`, aufgeführt in `[ContextHelp] Providers[]`). Sie werden
  mit der zweiten Welle des Hilfesystems in Suche, Dashboard und Einstellungen der Hilfe eingebunden.

## Kontexthilfe per help_id (JSON)

```
GET /exphelp/context/content.publish-article
GET /exphelp/context/manual/help-manual-editors
```

Ein Thema: `help_id`, `type`, `title`, `shortdesc`, `audience`, `platform`, `version_from`, `version_to`, `state`,
`preview`, `language`, `modified`, `markdown` (das ganze Thema, mit englischen Überschriften), `fields` (das Markdown
jedes Felds), `related` (help_id, title, shortdesc, url), `figures` (url, alt, caption, figure_id, field), `url`.
Ein Handbuch: seine Metadaten und `toc` (verschachtelte Kapitel und Themen mit ihrer JSON-`url`).

`ETag` bei jeder Antwort, `If-None-Match` wird mit 304 beantwortet; `Cache-Control: private, no-cache`, `Vary: Cookie,
Authorization`. Unbekannt, intern ohne Zugriff oder nicht veröffentlicht: dasselbe `404 {"error":"not_found"}`.

`expHelpContextRegistry` fragt zuerst seine Provider: Ein Provider, der zusätzlich `expHelpContextPageInterface`
implementiert, darf mit `content/<help_id>` antworten, und das Hilfe-Steuerelement "?" einer Admin-Seite öffnet dann
dieses Thema. Ein Eintrag der Zuordnung kann nie `content/...` nennen, und ein Provider, der die Schnittstelle nicht
implementiert, ebenfalls nicht.

## Einstellungen: exphelp.ini [ContentSettings]

| Einstellung | Standard | |
|---|---|---|
| `Enabled` | `enabled` | `disabled`: die Ansichten für Themen, Handbücher und Kontext liefern 404 |
| `ParentNode` | `media` | wo `help-root` angelegt wird: `media`, `content`, `users` oder eine remote_id |
| `Audiences[]` | Visitors, Members, Editors, Administrators | die Optionen für Zielgruppen (werden immer nur angehängt) |
| `PublicAudiences[]` | Visitors | eine Zielgruppe, die ein Thema öffentlich macht |
| `Platforms[]` | Web, Admin, Mobile | die Optionen für Plattformen |
| `Areas[]` | account, content, media, search, users, setup, privacy, help | die Bereiche der help_ids |
| `AudienceMap[<name>]` | visitors, members: user; editors: editor; administrators: admin | die Zielgruppe der Hilfe zu einer Zielgruppenoption |
| `ContextHelpIds[<key>]` | keine | `module/view` (oder `module`, `design:module/view`) = help_id |
| `AnonymousContextHelp` | `disabled` | `enabled`: der Rollenschritt gibt Anonymous `exphelp/context` |
| `EbooksOnRelease` | `enabled` | eine Veröffentlichung erstellt die gespeicherten E-Books der Handbücher neu; `disabled`: nur `exp:help:content ebooks` tut das |
| `ReviewMaxDays` | `14` | Tage, die ein Thema ohne Änderung in Prüfung bleiben darf, bevor es überfällig ist |
| `ReviewListLimit` | `10` | höchstens so viele überfällige Themen auf dem Dashboard der Hilfe |
| `ClientHelpIds[]` | keine (ein auskommentiertes Beispiel in der ausgelieferten Datei; setzen Sie es in einer Überschreibung) | die help_ids, die andere Clients öffnen (eine mobile App, das JavaScript einer Site); `check` meldet eine, die kein Thema hat oder deren Thema nicht veröffentlicht ist |

## E-Books

```
./console exp:help:content export -s admin --manual=help-manual-editors --format=epub [--output=file] [--dry-run]
./console exp:help:content export -s admin --manual=help-manual-editors --format=pdf
```

und auf der Seite des Handbuchs in der Hilfe **EPUB herunterladen** und **PDF herunterladen**
(`exphelp/manual/<remote_id>/(format)/epub`). Ein E-Book enthält nur die **veröffentlichten** Kapitel und Themen, die
der Benutzer lesen darf (auf der Kommandozeile: `--login`, Standard `admin`); eine Vorschau gelangt nie in ein Buch.
Die Standarddatei des Befehls ist `var/<site>/cache/exphelp/ebooks/<remote_id>.<format>`; der Download wird pro Anfrage
erstellt und nicht aufbewahrt.

- **EPUB 3** (`expHelpContentExport::writeEpub()`, die zip-Extension von PHP): eine Titelseite (Titel, Untertitel,
  Kurzbeschreibung, Ausgabe, Autoren, Lizenz), eine Seite pro Kapitel und pro Thema in der Reihenfolge des Handbuchs,
  ein Navigationsdokument mit dem verschachtelten Inhalt, die Abbildungen aus dem Speicher mit Alternativtext und
  Bildunterschrift, `xml:lang` aus der Sprache des Handbuchs sowie die Barrierefreiheits-Metadaten von EPUB
  Accessibility 1.1 (`schema:accessMode`, `accessModeSufficient`, `accessibilityFeature`, `accessibilityHazard`,
  `accessibilitySummary`, `dcterms:conformsTo`). Links zwischen Themen des Buchs bleiben im Buch; andere Links auf
  Themen führen zum Thema in der Hilfe. epubcheck und Ace by DAISY werden nicht mitgeliefert: Eine Site, die die Bücher
  veröffentlicht, lässt sie über die Datei laufen.
- **PDF** (`writePdf()`) verwendet den PDF-Writer der Hilfe (`expHelpPdfWriter`, Druck und PDF-Download von
  Hilfeseiten): ein Dokument mit dem Titelblock des Handbuchs, Kapiteln und Themen als Überschriften, internen Links und
  den Abbildungen. Das Format wird nur angeboten, wo dieser Writer installiert ist (`expHelpContentExport::formats()`).

### Gespeicherte E-Books

Der Plan speichert die erstellten Bücher als Inhalt (Auslieferungsweg 3): Jedes Format jedes veröffentlichten
Handbuchs ist ein Objekt der Klasse **file** unterhalb des Ordners E-books (remote_id `help-ebooks`), gefunden über
seine remote_id `help-ebook-<manual remote_id>-<format>` (`help-ebook-help-manual-editors-epub`).

```
./console exp:help:content ebooks -s admin [--manual=help-manual-editors] [--dry-run] [--json]
```

- **Bei der Veröffentlichung neu erstellt.** Wenn ein Objekt der Hilfe auf Veröffentlicht gesetzt wird (die
  Statuszuweisung der Administrationsoberfläche oder beliebiger Code, der Status über
  `eZContentOperationCollection::updateObjectState()` zuweist), werden die Bücher jedes veröffentlichten Handbuchs,
  das es enthält (das Handbuch selbst, seine Kapitel, deren Themen, die Abbildungen dieser Themen), neu erstellt
  (`expHelpContentEbooks::onStateAssign()`). `exphelp.ini [ContentSettings] EbooksOnRelease=disabled` überlässt das
  dem Befehl. Das Setzen von Entwurf oder Prüfung ändert kein Buch. Ein Buch, das sich nicht erstellen lässt, wird ins
  Debug-Log geschrieben; die Veröffentlichung bleibt bestehen.
- **Versionen.** Ein neuer Build wird als neue Version desselben Objekts veröffentlicht, sodass die Versionshistorie
  des Objekts die früheren Ausgaben behält (`content.ini [VersionManagement]` entscheidet, wie viele). Ein Build,
  dessen Datei der gespeicherten gleicht, ändert nichts: Die Dateien sind nach ihrem Inhalt datiert, nicht nach der
  Uhr, sodass ein zweiter Lauf des Befehls `No change.` ausgibt.
- **Status.** Veröffentlicht, wie alles, was die Hilfe ausliefert.
- **Sektion und Berechtigungen.** `help_public` für ein Handbuch mit öffentlicher Zielgruppe, sonst `help_internal`,
  nur am Objekt gesetzt. So entscheiden die Richtlinien des Plans, wer ein Buch herunterladen darf: Anonymous liest
  `help_public` und Veröffentlicht, Hilfe-Redakteure den Teilbaum `help-root`. Das Buch eines öffentlichen Handbuchs
  enthält nur öffentliche Objekte: veröffentlichte Themen in `help_public` mit öffentlicher Zielgruppe, Kapitel und
  Abbildungen in `help_public`; das Buch eines internen Handbuchs enthält jedes veröffentlichte Objekt, das es
  aufführt. Wer durch seine Veröffentlichung einen Build auslöst, ändert nichts daran, was hineinkommt.
- **Vom Handbuch aus verlinkt.** Die Seite des Handbuchs in der Hilfe listet seine gespeicherten Bücher auf, die der
  Leser lesen darf: die Datei (ein Link auf `content/download`), ihre Version (ein Link auf das Objekt), Größe und Datum.

Die Download-Schaltflächen oben erstellen ein Buch weiterhin auf Anfrage mit den eigenen Rechten des Lesers; die
gespeicherten Bücher sind die Ausgaben für alle.

## DITA-Export

Die Hilfe wird in Exponential geschrieben und verlässt es als **DITA 1.3**: das Informationsmodell des Plans
(Entscheidung 1), für DITA-OT und andere DITA-Werkzeuge sowie für die Änderungshistorie in Git (Plan,
Auslieferungswege 3 und 4).

```
./console exp:help:content dita -s admin [--manual=help-manual-editors] [--output=<dir>] [--dry-run] [--json]
./console exp:help:content export -s admin --format=dita ...     # the same, under the plan's name
```

und auf den Handbuchseiten der Hilfe: **DITA herunterladen** auf der Seite eines Handbuchs
(`exphelp/manual/<remote_id>/(format)/dita`) und **Alles als DITA herunterladen** in der Liste der Handbücher
(`exphelp/manual/(format)/dita`), jeweils eine Zip-Datei. Wie bei einem E-Book gelangen nur **veröffentlichte**
Objekte, die der Benutzer lesen darf, in einen Export (auf der Kommandozeile: `--login`, Standard `admin`).

Ohne `--manual` enthält der Export jedes veröffentlichte Handbuch, jedes veröffentlichte Thema des Pools und die
veröffentlichten Glossareinträge; mit der Option ein Handbuch und die Themen, die es aufführt. Das Standardverzeichnis
ist `var/<site>/cache/exphelp/dita/all` (bzw. `/<remote_id>`). Nur geänderte Dateien werden geschrieben, und Dateien
eines früheren Exports, die nicht mehr darin vorkommen, werden entfernt (nur in `topics/`, `chapters/`, `glossary/`,
`images/` sowie die `.ditamap`- und `.ditaval`-Dateien des Verzeichnisses selbst). So kann ein Git-Repository das
Ausgabeverzeichnis sein: Sein `.git` und alles andere bleiben erhalten, und ein Commit nach dem Export hält fest, was
sich geändert hat. Ein zweiter Lauf gibt `No change.` aus.

| Hilfe als Inhalt | DITA 1.3 |
|---|---|
| `help_concept`, `help_task`, `help_reference`, `help_troubleshooting` | `concept`, `task`, `reference`, `troubleshooting`, eine Datei pro Thema: `topics/<help_id>.dita` |
| `help_id` | die `id` des Themas, sein Dateiname und `prolog/resourceid/@appid` (der DITA-Weg, ein Thema für die Kontexthilfe zu benennen) |
| `title`, `shortdesc` | `title`, `shortdesc` |
| `audience`, `platform`, `version_from` | die Filterattribute `audience`, `platform` (Kleinbuchstaben, Leerzeichen als Bindestriche) und `rev`; der Prolog wiederholt die Zielgruppen (`audience/@othertype`) und enthält `version_from`, `version_to`, `review_date` als `othermeta` sowie die Daten in `critdates` |
| `body` eines Konzepts | `conbody`; eine Überschrift eröffnet eine `section` mit ihrem `title` |
| `body` einer Referenz | `refbody`, jeder Block in einer `section` |
| `context`, `prereq`, `steps`, `result`, `postreq` | `prereq`, `context`, `steps` (jeder Punkt der einen nummerierten Liste ein `step` mit `cmd` und `info`), `result`, `postreq`. Schritte, die keine einzelne nummerierte Liste sind, werden zu `steps-informal`, und das Thema verwendet dann den DOCTYPE General Task von DITA 1.3 |
| `condition`, `cause`, `remedy` | `condition`, `troubleSolution` mit `cause` und `remedy` (`steps` oder `steps-informal`) |
| Abbildungen | `fig` mit `title` (Bildunterschrift) und `image` mit `alt`, im Element des Felds, das die Abbildung nennt (`section`), sonst am Ende; die Bilddatei in `images/` |
| `related` | `related-links` |
| Markdown | `p`, `ul`, `ol`, `codeblock`, `lq`, `simpletable`, `note` (aus einem hervorgehobenen Kasten, mit seinem Typ); **fett** (im Styleguide eine zitierte UI-Beschriftung) wird `uicontrol`, *kursiv* `i`, Code `codeph`; ein `help:`-Link wird ein `xref` auf das Thema im Export oder auf seine Seite in der Hilfe, wenn es nicht im Export ist |
| `help_manual` | eine `bookmap`, `<remote_id>.ditamap`: `booktitle` (Titel, Untertitel), `bookmeta` (Kurzbeschreibung, Autoren, Daten, Zielgruppen, Lizenz, Ausgabe), ein Inhaltsverzeichnis, ein `chapter` pro Eintrag |
| `help_section` | ein `chapter` (`chapters/<remote_id>.dita`, ein Konzept mit Titel und Kurzbeschreibung des Kapitels) mit seinen Themen als `topicref`s |
| die Zielgruppen des Handbuchs | `<remote_id>.ditaval`: die Zielgruppen des Handbuchs per `include`, die übrigen Zielgruppenoptionen per `exclude` (die Ausgabe für seine Zielgruppen) |
| `help_glossary_entry` | `glossentry` (`glossary/<term>.dita`): `glossterm`, `glossdef`, die Notiz und jedes zu vermeidende Wort als `glossAlt/glossSynonym` mit `glossStatus value="prohibited"` |
| der gesamte Export | `help-pool.ditamap`: jedes Thema nach Typ und das Glossar |

Jede Datei hat den DITA-1.3-DOCTYPE ihres Wurzelelements (`-//OASIS//DTD DITA 1.3 Concept//EN` ...) und `xml:lang` aus
der Sprache ihres Objekts. Nach dem Schreiben prüft der Befehl das Verzeichnis (`expHelpContentDita::validate()`): jede
Datei wohlgeformt, der richtige DOCTYPE, Themen-IDs eindeutig, und jedes nicht externe `href` einer Map, eines Themas
oder eines Bildes verweist auf eine Datei des Exports; trifft das nicht zu, endet er mit 1 und gibt `FAIL` aus. Der
Download wird vor dem Senden auf dieselbe Weise geprüft. Die DTDs werden nicht mitgeliefert und nichts wird
nachgeladen: Für eine vollständige DTD-Validierung lassen Sie DITA-OT oder `xmllint --valid` mit dem OASIS-Katalog über
das Verzeichnis laufen (der Export des Testbestands und der mitgelieferten Startthemen besteht sie).

## Überfällige Prüfungen

Die Wartungsphase (7) des Plans verlangt "eine Liste veralteter Themen (Prüfdatum überschritten) im Admin-Dashboard".
Die Startseite der Hilfe (`exphelp/dashboard`) zeigt Hilfe-Redakteuren und Release-Verantwortlichen
**Hilfethemen mit überfälliger Prüfung**, und der Befehl listet sie alle auf:

```
./console exp:help:content review -s admin [--json]
```

Ein Thema ist überfällig, wenn

- sein **Prüfdatum überschritten** ist (`review-date.past` des Validators, Abschnitt 8 des Styleguides), in jedem
  Status. Der Release-Verantwortliche setzt ein veröffentlichtes Thema zurück auf Prüfung, der Redakteur prüft es und
  setzt ein neues Prüfdatum, und es wird erneut veröffentlicht (Plan, "Permissions");
- oder es **länger als** `exphelp.ini [ContentSettings] ReviewMaxDays` Tage (Standard 14) ohne Änderung **in Prüfung**
  ist. Der Kernel speichert kein Datum einer Statusänderung, deshalb zählt die letzte Veröffentlichung des Themas: Ein
  Thema, an dem ein Redakteur in Prüfung noch arbeitet, ist nicht überfällig.

Die Liste zeigt jedes Thema mit seinem Status, warum und wie lange es überfällig ist, das am längsten überfällige
zuerst, sowie einen Link auf das Thema in der Hilfe und auf sein Objekt in der Administrationsoberfläche
(**Bearbeiten** oder **Status setzen** für alle, die nur seinen Status zuweisen dürfen). Sie wird nur denen angezeigt,
die bei einem Thema handeln können: wer es bearbeiten oder ihm einen anderen Status von `help_status` zuweisen darf. Ein
Hilfe-Redakteur (Entwurf und Prüfung) sieht die Themen in Prüfung; ein Release-Verantwortlicher zusätzlich die
veröffentlichten, deren Prüfdatum überschritten ist. Wer bei keinem handeln kann, sieht nichts, und solange kein Thema
überfällig ist, wird nichts angezeigt. Höchstens `ReviewListLimit` Themen (Standard 10) werden aufgelistet, der Rest
wird gezählt. Die Karte verwendet die gemeinsamen Admin-Komponenten (`a4-card`, `a4-table`, `a4-tag`, `a4-btn`) und
folgt so dem hellen und dunklen Modus der Administrationsoberfläche; sie hat kein eigenes CSS.

## Die Hilfeklassen als Paket

`./console exp:help:content package -s <admin siteaccess>` schreibt die acht Klassen der Installation als
Inhaltsklassen-Paket `exp_help_classes` (ein Paketverzeichnis und `exp_help_classes-<version>.ezpkg`), und
`verify-package --file=<.ezpkg>` vergleicht ein Paket ohne Datenbank mit den Klassen des Release. Das Paket enthält nur
die Klassen; Sektionen, Status, Baum und Rollen legt weiterhin `exp:help:content install` an. Erstellen, Prüfen und
Übergabe: [die Hilfeklassen als Paket](../../../eng-us/specifications/6.0/help-content-package.md).

## Abgleich mit den Spezifikationen

Geprüft am 19. Oktober 2026 gegen den [Plan](../../spezifikationen/6.0/hilfesystem.md), den
[Styleguide](../../spezifikationen/6.0/hilfe-stilrichtlinien.md) und die [Standards](../../../eng-us/specifications/6.0/help-standards.md)
(Pull-Request #288), Punkt für Punkt im Code, nicht anhand dieser Seite. Der DITA-Export, die E-Books als Dateiobjekte,
die Prüfliste und ihre Übersetzungen, am 19. Oktober noch in Arbeit, folgten am 20. Oktober.

| Spezifikation | Stand |
|---|---|
| DITA-Thementypen: ein Typ pro Thema, Konzept, Aufgabe, Referenz, Fehlerbehebung | erledigt: `expHelpContentSchema::classes()`, die Felder jedes Typs so, wie der Plan sie aufführt |
| Maps: Handbuch (Bookmap) und Kapitel mit geordneten `topicrefs`, Themenpool | erledigt: `help_manual`, `help_section`, der Baum unterhalb von `help-pool`; Validator `map.*` |
| Abbildungen und Glossar als Klassen | erledigt: `help_screenshot` (Alternativtext erforderlich), `help_glossary_entry` (zu vermeidende Synonyme) |
| Die Vorlagen des Styleguides (Konzept, Aufgabe, Referenz, Fehlerbehebung, die zweite Aufgabe) | erledigt: alle fünf stehen in `share/help/seed-topics.json` (`content.versions`, `content.publish-article`, `content.edit-buttons`, `content.not-visible`, `account.log-in`) und bestehen den Validator ohne Befund (Test `testStyleGuideTemplatesPass`) |
| Sektionen `help_public`/`help_internal` anhand der Zielgruppe gesetzt, nur am Objekt | erledigt: `check --fix`, nach jedem `seed-*` |
| Objektstatus Entwurf, Prüfung, Veröffentlicht; nur Veröffentlichtes ausgeliefert | erledigt: `help_status`, der Provider zeigt Entwurf und Prüfung nur Redakteuren, als Vorschau |
| Rollen Help editor, Help release; eine Anonymous-Richtlinie mit beiden Einschränkungen | erledigt: `exp:help:content roles` |
| CLI-Skripte mit `-s`, `--dry-run`, idempotent ("No change.") | erledigt: die Aktionen von `exp:help:content` |
| Validatorregeln des Plans (Schritte, Alternativtext, `shortdesc`, `help_id`, Glossar, Prüfdatum, Sektion) | erledigt |
| Hinweisstufen (Abschnitt 5 des Styleguides: Attention, Data protection, Note; im Schritt, zuerst, einer pro Schritt, höchstens drei Sätze) | **jetzt hinzugefügt**: `notice.position`, `notice.before-action`, `notice.per-step`, `notice.too-long`; hervorgehobene Kästen für Attention und Data protection mit eigenem Titel im Viewer. Die Länge eines Hinweises innerhalb eines Schritts bleibt eine Regel für die Prüfung |
| Schema und Eindeutigkeit der `help_id` | erledigt: `help-id.*` |
| Kontexthilfe-JSON per `help_id`, ETag, öffentliche Themen ohne Anmeldung | erledigt: `exphelp/context/<help_id>`, `exphelp/context/manual/<remote_id>`; anonymer Zugriff ist Opt-in (`AnonymousContextHelp`) |
| "Clients führen eine Liste der help_ids, die sie verwenden; ein Test prüft, dass jede existiert" | **jetzt hinzugefügt**: `client.help-id-unknown`, `client.help-id-not-released` über `ContextHelpIds[]` und das neue `ClientHelpIds[]` |
| E-Book EPUB und PDF eines Handbuchs, Barrierefreiheits-Metadaten | erledigt: `exp:help:content export`, **EPUB/PDF herunterladen** |
| Export als DITA-Bookmap + DITAVAL, regelmäßiger DITA-Export in ein Git-Repository (Auslieferungswege 3 und 4) | erledigt: `exp:help:content dita`, **DITA herunterladen** ([DITA-Export](#dita-export)); gültig gegen die DTDs von OASIS DITA 1.3. Der regelmäßige Lauf nach Git ist ein Cronjob der Site auf Basis des Exports (nur geänderte Dateien werden geschrieben, daher kann sein Verzeichnis das Git-Repository sein) |
| E-Book als Dateiobjekt unter "E-books" (`help-ebooks`) gespeichert | erledigt: `exp:help:content ebooks`, bei der Veröffentlichung neu erstellt, versioniert, Sektion anhand der Zielgruppe ([gespeicherte E-Books](#gespeicherte-e-books)) |
| Liste der Themen mit überschrittenem Prüfdatum im Dashboard (Phase 7) | erledigt: das Dashboard der Hilfe und `exp:help:content review`, auch Themen, die zu lange in Prüfung sind ([überfällige Prüfungen](#überfällige-prüfungen)) |
| epubcheck und Ace by DAISY für jedes E-Book | ausgelassen: Java- und Node-Werkzeuge, die nicht mit einer PHP-Anwendung ausgeliefert werden. Eine Site, die Bücher veröffentlicht, lässt sie laufen. Das EPUB trägt `dcterms:conformsTo` EPUB Accessibility 1.1 / WCAG 2.2 AA, obwohl Exponential diese Prüfungen nicht ausführt; siehe [die Standards](../../../eng-us/specifications/6.0/help-standards.md#epub-accessibility-11) |
| Web-Templates `full/help_*.tpl`, `line/help_*.tpl` für die öffentliche Site (Auslieferungsweg 1) | vorerst ausgelassen: Der Kernel hat kein eigenes Design für eine öffentliche Site, in das sie gehören könnten; die Hilfe wird in der Administrationsoberfläche (`exphelp/topic`, `exphelp/manual`) und als JSON ausgeliefert, das das Design einer Site rendern kann. Ein Site-Paket, das öffentliche Hilfeseiten möchte, fügt die Templates in seinem Design hinzu |
| "War das hilfreich?" bei jedem Thema (Phase 7) | vorerst ausgelassen: Es braucht einen Ort zum Speichern der Antworten und eine Datenschutzentscheidung (wer geantwortet hat, wie lange aufbewahrt) pro Site |
| Hilfe nie Quelle für KI-Antworten (RAG), sofern nicht gewollt | standardmäßig erledigt: Der Kernel hat keine Liste von RAG-Quellen, und die Wurzel der Hilfe liegt unter Medien, nicht im Inhaltsbaum. Eine Extension, die eine solche Liste hinzufügt, muss `help-root` auslassen |
| Die Hilfeklassen als Paket | **jetzt hinzugefügt**: `exp:help:content package`, `verify-package` (CJW #16564) |

## Später

"War das hilfreich?", Templates für die öffentliche Site, Komfortfunktionen für Redakteure.

## Klassen

`expHelpContentSchema` (das Modell, nur Daten), `expHelpContentInstaller` (Klassen, Baum, Rollen, Bericht,
Deinstallation, Korrektur der Sektionen), `expHelpContentRepository` (liest die Hilfeobjekte in Arrays),
`expHelpContentValidator` (die Regeln, keine Datenbank), `expHelpContentSeeder`, `expHelpContentProvider` (was der
aktuelle Benutzer sehen darf, Rendering, JSON, Suche, Kontext), `expHelpContextPageInterface`, `expHelpContentPackage`
(das Klassenpaket), `expHelpContentExport` (EPUB, PDF), `expHelpContentDita` (der DITA-Export, seine Prüfung, das
Schreiben und das Zip), `expHelpDitaMarkup` (das HTML der Hilfe als DITA-Elemente), `expHelpContentEbooks` (die
gespeicherten E-Books, bei der Veröffentlichung neu erstellt), `expHelpContentEditionProvider` (was in eine gespeicherte
Ausgabe kommt) und `expHelpContentReview` (überfällige Prüfungen). Ansichten: `Exponential\View\Kernel\Exphelp\Topic`,
`Manual`, `Context`. Tests (ohne Datenbank):
`tests/tests/kernel/classes/help/expHelpContentTest.php`, `expHelpContentPackageTest.php`, `expHelpContentExportTest.php`,
`expHelpContentDitaTest.php`, `expHelpContentEbooksTest.php`, `expHelpContentReviewTest.php`,
`expHelpContentTranslationsTest.php`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
