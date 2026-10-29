# Hilfe als Inhalt gepflegt

Status: Phase 1 in 6.0.15 (18. Oktober 2026); der DITA-Export, die gespeicherten E-Books und die überfälligen Prüfungen
am 20. Oktober 2026 ([Hinweis zur Aktualisierung](../../bc/6.0/hilfeinhalte-dita-ebooks-reviews.md)); der Prüfablauf
am Thema, "War das hilfreich?", Kontexthilfe für Clients mit Token, die Prüfung eines EPUB, die Historie in git, die
Hilfe auf den eigenen Seiten einer Site und die übrigen Regeln des Styleguides am 25. Oktober 2026
([Hinweis zur Aktualisierung](../../bc/6.0/hilfeinhalte-pruefablauf-rueckmeldung.md)). Der Plan ist
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
| Die acht Klassen `help_manual`, `help_section` (Kapitel), `help_concept`, `help_task`, `help_reference`, `help_troubleshooting`, `help_screenshot`, `help_glossary_entry` in der Klassengruppe "Help" | `./console exp:help:content classes --apply` |
| Die Sektionen `help_public` und `help_internal`, die Objektstatusgruppe `help_status` (Entwurf, Prüfung, Veröffentlicht), der Ordnerbaum (`help-root`, `help-pool` ..., über die remote_id gefunden) | `./console exp:help:content tree --apply` |
| Die Rollen "Help editor" und "Help release" sowie die eine zusätzliche Richtlinie von Anonymous | `./console exp:help:content roles --apply` |
| Der Validator des Styleguides; `--fix` setzt die Sektionen anhand der Zielgruppe | `./console exp:help:content check` |
| Das anfängliche Glossar und die ersten Themen (die Vorlagen des Styleguides, ein Handbuch) | `./console exp:help:content seed-glossary --apply`, `seed-topics` |
| Die Handbücher aus der Markdown-Hilfe: eines je Ordner von `doc/eng-us/help/` und je Referenzbereich, bei jedem Release neu erstellt, mit der deutschen Übersetzung | `./console exp:help:content build-manuals --apply` ([unten](#handbücher-aus-der-markdown-hilfe)) |
| Was installiert ist, Deinstallation | `./console exp:help:content report`, `uninstall` |
| Ein E-Book (EPUB, PDF) eines Handbuchs | `./console exp:help:content export`, **EPUB/PDF herunterladen** auf der Seite des Handbuchs |
| Der DITA-1.3-Export (Bookmaps, DITAVAL, Themen, Glossar), nach dem Schreiben geprüft | `./console exp:help:content dita`, **DITA herunterladen** auf der Seite des Handbuchs und in der Liste der Handbücher |
| Die E-Books, gespeichert als Dateiobjekte unter E-books, versioniert, bei der Veröffentlichung neu erstellt | `./console exp:help:content ebooks --apply` und jede Veröffentlichung eines Themas |
| Die Themen mit überfälliger Prüfung (Prüfdatum überschritten, zu lange in Prüfung) | das Dashboard der Hilfe, `./console exp:help:content review` |
| Die acht Klassen als Inhaltsklassen-Paket `exp_help_classes` (.ezpkg) und seine Prüfung | `./console exp:help:content package`, `verify-package` ([das Paket](../../spezifikationen/6.0/hilfeklassen-als-paket.md)) |
| Ein Thema in der Hilfe: `exphelp/topic/<help_id>`; ein Handbuch: `exphelp/manual/<remote_id>`; die Liste der Handbücher: `exphelp/manual` | Admin, Richtlinie `exphelp/read` |
| Der Prüfablauf am Thema: **Zur Prüfung geben**, **Freigeben**, **Zurück in Prüfung**, **Zurück zum Entwurf**, und die Prüfung des Themas nach dem Styleguide ([unten](#der-prüfablauf)) | Admin, `state/assign` der Hilferollen |
| "War das hilfreich?" unter jedem veröffentlichten Thema, die Antworten je Thema ([unten](#war-das-hilfreich)) | `exphelp/feedback/<help_id>`, `./console exp:help:content feedback` |
| Kontexthilfe als JSON: `exphelp/context/<help_id>`, `exphelp/context/manual/<remote_id>` | Richtlinie `exphelp/context` |
| Kontexthilfe für Clients mit Token (OAuth oder ein persönlicher API-Schlüssel): `/api/help/v1/topic/<help_id>`, `/api/help/v1/manual/<remote_id>` ([unten](#kontexthilfe-für-clients-mit-token)) | REST, Scope `help` |
| Die Hilfe auf den eigenen Seiten der Site: die Voll- und Zeilenansichten der Hilfeklassen ([unten](#hilfe-auf-den-seiten-der-site)) | Extension `exphelpsite` |
| Die Tabellen `exphelp_help_id` (veröffentlichte help_ids) und `exphelp_feedback` in einer Installation, die älter ist als sie | `./console exp:help:content tables --apply` (ein Schritt von `install`) |

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
./console exp:help:content install --apply -s admin                # classes, tree, roles
./console exp:help:content install --apply -s admin                # No change.
./console exp:help:content seed-glossary --apply -s admin
./console exp:help:content seed-topics --apply -s admin --release  # the style guide's templates, Released
./console exp:help:content check --apply -s admin --fix
./console exp:help:content report -s admin
```

`--login=<user>` gibt an, wer die Klassen und Objekte anlegt (Standard `admin`). Die `seed-*`-Aktionen nehmen
`--file=<json>` an (im Format von `share/help/seed-topics.json` und `share/help/seed-glossary.json`) und lassen
vorhandene Objekte so, wie die Redakteure sie hinterlassen haben, sofern nicht `--update` angegeben ist.

### Bei einer neuen Installation

Der Kickstarter und `./console exp:install --apply` stellen am Ende einer Installation den Aufbau der Handbücher in die
Warteschlange (`./console exp:help:content build-manuals --apply -s admin --queue`, aus `expKickstarter::installHelpManuals()`)
und warten nicht darauf: Die Installation dauert nur wenige Sekunden länger. Der Inhaltsjob `helpmanuals` installiert
die Hilfe als Inhalt (Klassen, Baum, Rollen) und baut dann die Handbücher in den folgenden 20 bis 30 Minuten im
Hintergrund auf, während die Site schon genutzt wird ([der Hintergrundjob](#der-hintergrundjob)). `exphelp.ini
[ManualSettings] BuildOnInstall=disabled` lässt das weg. Scheitert das Einreihen, wird es gemeldet, und die
Installation bleibt bestehen: Die Site funktioniert ohne die Handbücher, und der Befehl kann jederzeit ausgeführt
werden. Der Setup-Assistent im Browser reiht ihn nicht ein: Führen Sie nach einer Installation mit dem Assistenten
einmal `./console exp:help:content build-manuals --apply -s admin --queue` aus. `seed-glossary` und `seed-topics` führen Sie
von Hand aus, wenn Sie die Startinhalte möchten.

Dieselben Befehle, die eine bestehende Site erweitern, legen auf einer neuen alles an und finden Vorhandenes über
Bezeichner und remote_id, sodass eine Site, die aus einem Paket mit den Klassen erstellt wurde, nur ergänzt wird. Die
Daten, die der Installer braucht, sind Dateien des Kernels: das Inhaltsmodell in `expHelpContentSchema`, die
Startdaten in `share/help/`, die Liste der Handbücher in `exphelp.ini [ManualSettings]`.

## Handbücher aus der Markdown-Hilfe

Die Seiten der Hilfe in `doc/eng-us/` (und ihre Übersetzungen in `doc/ger-de/`) sind die eigentliche Hilfe: Tausende
von Seiten. `build-manuals` macht daraus Handbücher, sodass sie auch Inhalt der Site sind: als Handbücher in der Hilfe,
als E-Books, als DITA und als Kontexthilfe lesbar, mit Sektionen, Status und Berechtigungen wie jedes andere Thema.

**Das Markdown bleibt die Quelle.** Die Handbücher werden bei jedem Release daraus neu erstellt: Ein Lauf bringt
Handbücher, Kapitel und Themen wieder in Übereinstimmung mit den Seiten. Eine Änderung, die nur in der
Administrationsoberfläche gemacht wurde, wird beim nächsten Lauf überschrieben; eine Korrektur gehört in die
Markdown-Seite. Objekte, die der Befehl nicht erstellt hat, werden nie angefasst: Alles, was er erstellt, hat eine
remote_id, die mit `help-gen-` beginnt, und nur diese Objekte werden geändert oder entfernt. Die Startthemen des
Styleguides, das Glossar und alles, was Redakteure geschrieben haben, bleiben, wie sie sind.

```
./console exp:help:content build-manuals -s admin --dry-run    # was sich ändern würde
./console exp:help:content build-manuals --apply -s admin --release    # anlegen, aktualisieren, entfernen; Veröffentlicht setzen
./console exp:help:content build-manuals --apply -s admin --release    # No change.
./console exp:help:content build-manuals --apply -s admin --manual=press --release   # ein Handbuch
./console exp:help:content build-manuals --apply -s admin --language=none            # ohne die Übersetzungen
./console exp:help:content build-manuals --apply -s admin --queue                    # dasselbe im Hintergrund (Inhaltsjob)
./console exp:help:content report -s admin                     # "Handbücher werden erstellt (n von 52)", solange er läuft
./console exp:help:content ebooks --apply -s admin                     # die E-Books jedes veröffentlichten Handbuchs
./console exp:help:content ebooks --apply -s admin --language=ger-DE   # und ihre deutsche Ausgabe
```

### Was woraus wird

| Markdown-Hilfe | Inhalt |
|---|---|
| eine Zeile von `exphelp.ini [ManualSettings] Manuals[]`: ein Ordner unterhalb von `doc/eng-us/` | ein Handbuch (`help_manual`, remote_id `help-gen-manual-<Schlüssel>`) unterhalb des Ordners Manuals |
| ein Unterordner (und dessen Unterordner) | ein Kapitel (`help_section`, `help-gen-chapter-<Hash des Ordners>`) unterhalb seines Handbuchs oder Kapitels |
| eine Markdown-Seite | ein Thema (`help-gen-topic-<Hash der Seite>`) unterhalb seines Kapitels oder Handbuchs |
| die `README.md` des Ordners | das erste Thema seines Handbuchs oder Kapitels; ihr Titel ist der Titel des Kapitels |
| `<Ordner>/*.md` in `Manuals[]` | ein Handbuch aus den Seiten direkt im Ordner, ohne Kapitel ("Getting started": `help/*.md`) |
| der Platz der Seite in der Hilfe | die Reihenfolge: Front Matter `order`, dann der Titel (wie die Navigation der Hilfe sie zeigt) |
| `doc/ger-de/` (zugeordnet über `doc/ger-de/.paths.json`) | die Übersetzung ger-DE derselben Objekte |

- **Thementyp.** Das Front Matter `topic_type: concept|task|reference|troubleshooting` entscheidet. Ohne es: eine
  Referenz für die Referenzseiten der Hilfe (Typ `reference`, `catalogue`, `specification`, `generated`), eine
  Aufgabe für eine Seite, deren Titel mit "How to" beginnt und die genau eine nummerierte Liste hat (der Text davor
  ist der Kontext, die Liste die Schritte, der Text danach das Ergebnis), sonst ein Konzept. Eine
  Fehlerbehebungsseite braucht die Überschriften Condition (Symptom, Problem) und Remedy (Solution, Fix); eine Seite
  ohne die Teile ihres Typs wird ein Konzept und gemeldet (`notice`).
- **Text.** Die Seite ohne Front Matter und Titel, als Markdown in den Textfeldern des Themas. Ein Link auf eine Seite,
  die ein Thema ist, wird `help:<help_id>` und öffnet so das Thema; ein Link auf eine andere Seite oder ein Bild der
  Hilfe wird `/doc/eng-us/<Pfad>`, den die Hilfe beim Anzeigen des Themas auflöst (Hilfe-Viewer, Bilder der Hilfe).
  Links mit einem Schema, Anker und Code bleiben unverändert.
- **Kennungen.** Die help_id ist `doc.<Pfad der Seite>`: `doc.help.editors.publishing` für
  `help/editors/publishing.md` (Bereich `doc`; ein Pfad über 80 Zeichen oder zwei Seiten, die dieselbe bekämen, enden
  mit einem Hash). Die remote_ids hängen nur vom Pfad ab, sodass ein Lauf jedes Objekt wiederfindet; eine verschobene
  Seite ist ein neues Thema, und das alte wird entfernt.
- **Titel und Kurzbeschreibung.** Der Titel der Seite; die Kurzbeschreibung ist der erste Satz ihrer Zusammenfassung
  (Front Matter `summary`, sonst ihr erster Absatz), höchstens 250 Zeichen. Der Titel eines Handbuchs ist der Titel
  seiner Zeile, sonst der Titel der `README.md` des Ordners, sonst der seiner Startseite ohne ": start here"
  (`Adopters: start here` ergibt `Adopters`). Übersetzte Titel von Handbüchern, deren Titel aus ihrer Zeile kommt:
  `TranslatedTitles[]`.
- **Zielgruppe, Sektion, Status.** Die Zielgruppen der Seite in der Hilfe (Front Matter `audience`, sonst
  `[AudienceSettings] PathRules[]` ...), jeweils als die erste Zielgruppenoption, die `[ContentSettings]
  AudienceMap[]` ihr zuordnet. Die Sektion ist nur dann `help_public`, wenn eine davon öffentlich ist
  (`PublicAudiences[]`, Visitors: die Seiten für `user`), sonst `help_internal`; ein Kapitel eines öffentlichen
  Handbuchs ist öffentlich. Neue Objekte sind Entwurf; `--release` setzt jedes erstellte Objekt auf Veröffentlicht
  (der erste Aufbau eines Releases tut das, damit die Handbücher ausgeliefert werden).
- **Übersetzungen.** Jede Sprache von `[ManualSettings] Languages[]` (ger-DE), die die Installation als Inhaltssprache
  hat: Eine Seite mit Übersetzung (nicht die stattdessen angezeigte englische Seite) ist die Übersetzung ihres Themas;
  ein Kapitel oder Handbuch wird übersetzt, wenn es ein übersetztes Thema enthält. Eine Sprache, die die Installation
  nicht hat, wird gemeldet und nie angelegt; legen Sie sie unter Einrichtung, Sprachen an und starten Sie den Lauf
  erneut. Ein Thema, dessen Übersetzung nicht mehr existiert, verliert die Übersetzung.
- **Zielgruppenoptionen.** Die Zielgruppen der Hilfe werden an `[ContentSettings] Audiences[]` angehängt
  (Developers, Partners, Press ...) mit ihrem `AudienceMap[]`, und `build-manuals` hängt sie zuerst an die Klassen an,
  wie es der Schritt `classes` tut (immer nur angehängt, damit die gespeicherten Zielgruppen ihre Bedeutung behalten).

### Aktualisieren, Entfernen, "No change."

Was gespeichert ist, wird mit drei Abfragen gelesen und mit dem verglichen, was die Seiten ergeben, bevor etwas
geschrieben wird: Ein Objekt mit denselben Werten kostet keinen Schreibvorgang. Dann

- legt der Befehl an, was fehlt (ein neues Thema mit seiner Übersetzung in **einer** veröffentlichten Version),
- aktualisiert, was abweicht (eine neue Version, in der abweichenden Sprache),
- verschiebt ein Objekt, dessen Handbuch oder Kapitel sich geändert hat, legt ein Objekt eines anderen Typs neu an,
- setzt die Kapitel und Themen jedes Handbuchs und Kapitels (die Relationsliste), die Sektion und mit `--release` den
  Status,
- entfernt die erstellten Objekte, deren Seite oder Ordner nicht mehr existiert, mit `--manual=<Schlüssel>` nur die
  unterhalb dieses Handbuchs.

Ein Lauf, der nichts zu tun findet, gibt `No change.` aus. Eine aus `Manuals[]` entfernte Zeile entfernt beim nächsten
Lauf ohne `--manual` ihr Handbuch samt Kapiteln und Themen.

### Wie lange es dauert

Der erste Aufbau legt jedes Objekt an: Auf der Kopie der SQLite-Datenbank einer Entwicklungsinstallation dauerten 52 Handbücher, 342
Kapitel und 5.315 Themen (4.614 davon mit deutscher Übersetzung) auf einem stark ausgelasteten gemeinsamen Server
(Last um 23, der Befehl mit der niedrigsten Priorität) 28 Minuten; ein zweiter Lauf, der nur vergleicht, 34 Sekunden,
das meiste davon für das Lesen der Seiten. Den größten Teil eines ersten Aufbaus kostet das Veröffentlichen jedes
Objekts (seine Version, sein Knoten, seine Namen und URL-Aliase). Ein Release, das wenige Seiten ändert,
aktualisiert wenige Objekte. Dafür ist der Befehl gebaut:

- eine veröffentlichte Version je neuem Objekt, mit seinen Übersetzungen (eine eigens veröffentlichte Übersetzung würde
  den ersten Aufbau verdoppeln);
- die Schreibvorgänge in Transaktionen von `[ManualSettings] BatchSize` Objekten (50);
- ohne Benachrichtigung veröffentlicht (keine E-Mail an die Abonnenten des Hilfebaums);
- der Suchindex wird danach vom Cronjob `indexcontent` aktualisiert (`DelayedIndexing` nur für diesen Lauf: Die Hilfe
  durchsucht ihre Themen selbst), und der View-Cache wird nicht je Objekt geleert (Hilfethemen zeigt `exphelp`); der
  eigene Cache der Hilfe wird am Ende einmal geleert;
- alle 250 Objekte eines Laufs, der etwas ändert, sagt eine Zeile, wie weit er ist.

### Der Hintergrundjob

`build-manuals --queue` (und eine neue Installation) erledigt dieselbe Arbeit als Inhaltsjob `helpmanuals`
(`expContentJobHelpManuals`, `content.ini [ContentJobSettings] JobTypes[helpmanuals]`), mit der Maschinerie der
Inhaltsjobs: ein abgekoppelter Worker (`bin/php/expcontentjob.php run <id>`), sein Protokoll und Fortschritt auf der
Jobseite (`content/job/<id>`) und der Cronjob-Teil `contentjobs` (in `frequent`), der einen Job fortsetzt, dessen
Worker abgebrochen ist.

- **Nie der Site im Weg.** Der Worker läuft mit der niedrigsten CPU-Priorität; er legt je Durchgang `[ManualSettings]
  JobBatchSize` Objekte an (10), jeden Durchgang in einer eigenen Transaktion, und wartet danach `JobPause`
  Millisekunden (200), damit die eigenen Schreibvorgänge der Site nie lange warten.
- **Zweimal ausführen ist sicher.** Es ist immer nur ein `helpmanuals`-Job aktiv: Ein zweites `--queue` gibt den
  laufenden zurück. Der Job hält die Teilbaumsperre der Hilfe-Wurzel, sodass kein Inhaltsjob sie währenddessen
  entfernt oder verschiebt. Jeder Durchgang ist idempotent: Ein fortgesetzter Job findet, was schon angelegt ist, und
  macht weiter.
- **Zuerst die Hilfe als Inhalt.** Der Job führt zuerst den Schritt `install` aus (Klassen, Baum, Rollen; `No change.`,
  wenn es sie gibt), sodass eine neue Installation nichts weiter braucht.
- **Wie weit er ist.** Solange er läuft, zeigen die Handbuchseiten der Hilfe und `./console exp:help:content report -s
  admin` "Handbücher werden erstellt (n von 52)": n zählt die Handbücher, deren Objekte angelegt sind.
- Von root gestartet, wechselt der Worker vor dem Start zum Eigentümer von `var/` (wie jeder Inhaltsjob); gehört die
  Installation root, läuft er als root (Inhaltsjobs, die root startet, geben ihrem Worker jetzt `--allow-root-user`
  mit, statt dass er nicht startet).

### Bei jedem Release

Das Markdown ändert sich mit jedem Release, also werden die Handbücher mit ihm neu erstellt. Nach dem Deployment des
Releases:

```
./console exp:help:content install --apply -s admin                    # neue Zielgruppenoptionen, Rollen (meist No change.)
./console exp:help:content build-manuals --apply -s admin --release
./console exp:help:content ebooks --apply -s admin
./console exp:help:content ebooks --apply -s admin --language=ger-DE
```

`--release` veröffentlicht, was der Aufbau erstellt oder geändert hat: Die Seiten wurden vor dem Release im Repository
geprüft. Ohne die Option warten neue und geänderte Themen als Entwurf auf einen Release-Verantwortlichen.

### Einstellungen: exphelp.ini [ManualSettings]

| Einstellung | Standard | |
|---|---|---|
| `Manuals[]` | 52 Handbücher: die 45 Ordner von `doc/eng-us/help/`, Getting started (`help/*.md`), install, guides, features, bc (Hinweise für Mitwirkende), standards, specifications | `<Schlüssel>;<Ordner>[;<Titel>]`, in der Reihenfolge der Handbücher; der Umfang ist eine Änderung dieser Liste |
| `Languages[]` | ger-DE | die geschriebenen Übersetzungen, wenn die Installation die Sprache hat |
| `TranslatedTitles[]` | Getting started, Contributor notes auf Deutsch | `<Schlüssel>;<Locale>;<Titel>` |
| `License` | GNU Free Documentation License 1.3 | die Lizenz jedes Handbuchs |
| `BuildOnInstall` | `enabled` | der Kickstarter und `exp:install` reihen nach einer Installation den Aufbau im Hintergrund ein (`build-manuals --queue`) |
| `JobBatchSize` | `10` | Objekte je Durchgang des Hintergrundjobs, jeder Durchgang eine eigene Transaktion |
| `JobPause` | `200` | Millisekunden, die der Hintergrundjob nach jedem Durchgang wartet |
| `BatchSize` | `50` | Objekte je Datenbanktransaktion |

`doc/eng-us/history/` und `doc/eng-us/examples/` (generiert) stehen nicht in der Liste.

Klassen: `expHelpManualBuilder` (der Plan: Seiten zu Handbüchern, Kapiteln und Themen, ohne Datenbank),
`expHelpManualSource` (der Index der Hilfe in einer Sprache als Quelle), `expHelpManualSync` (die Datenbank, die dem
Plan angeglichen wird). Tests: `tests/tests/kernel/classes/help/expHelpManualBuilderTest.php`.

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
- Die Sektion setzt `check --fix` (und jeder `seed-*`-Lauf danach), **nur am Objekt**, nie an einem Teilbaum. Ein
  Thema erhält die Sektion seiner Zielgruppe außerdem, sobald ihm ein Status von `help_status` zugewiesen wird (Zur
  Prüfung geben, Freigeben ...), sodass ein neues Thema nie in der Sektion seines Ordners freigegeben wird.
  Wird ein Objekt verschoben, erhält es die Sektion seines neuen Elternknotens: Führen Sie danach `check --fix` aus.
- `help_status` ist Entwurf (der Standard jedes neuen Objekts), Prüfung, Veröffentlicht. Nur Veröffentlichtes wird
  ausgeliefert. Der erste Status der neuen Gruppe wird einmalig jedem Objekt der Installation zugewiesen (ein INSERT;
  nur Hilfe-Richtlinien werten die Gruppe aus). Jeder Status hat eine Beschreibung (MySQL im Strict-Modus kennt dafür
  keinen Standardwert).

### Rollen

| Rolle | Richtlinien |
|---|---|
| Help editor | `content/read`, `versionread`, `create` im Teilbaum `help-root`; `content/edit` und `remove` dort in den Status Entwurf und Prüfung; `state/assign` von Entwurf nach Prüfung; `exphelp/read`, `exphelp/context` |
| Help release | `state/assign` in `help-root`, von jedem Status in jeden Status; **zusätzlich** zur Redakteursrolle zugewiesen |
| Anonymous | **eine** Richtlinie `content/read`, eingeschränkt auf die Sektion `help_public` **und** den Status Veröffentlicht |

Die Rollen werden angelegt, nicht zugewiesen: Weisen Sie sie in der Administrationsoberfläche den Benutzergruppen der
Redakteure zu. Eine Richtlinie wird anhand von Modul, Funktion und Einschränkungswerten verglichen; eine fehlende wird
hinzugefügt, nichts wird geändert oder entfernt.

**Wer schreibt, gibt nicht frei.** "Wer ein Thema geschrieben hat, gibt es nicht frei" (Plan, "Permissions") ist
durchgesetzt, nicht nur aufgeschrieben: Beim Zuweisen von Status (`eZContentOperationCollection::updateObjectState()`,
die der Reiter Objektstatus, Inhaltsjobs und die Schaltflächen der Hilfe alle nutzen) wird der Status Veröffentlicht für
ein Hilfeobjekt herausgenommen, dessen aktuelle Version der aktuelle Benutzer angelegt hat
(`expHelpContentWorkflow::refusedStates()`). Jemand anderes mit der Freigaberolle gibt es frei; die übrigen Status der
Zuweisung bleiben. `exphelp.ini [ContentSettings] ReleaseByAuthor=enabled` schaltet die Regel ab, für eine Site mit nur
einem Redakteur. Die Kommandozeile (`seed-topics --release`, `build-manuals --release`) weist Status direkt zu: Wer sie
ausführt, trifft damit die Entscheidung zur Freigabe.

**Opt-in:** `exphelp.ini [ContentSettings] AnonymousContextHelp=enabled` (Standard `disabled`) lässt den Rollenschritt
Anonymous zusätzlich `exphelp/context` geben, sodass das JSON **öffentlicher, veröffentlichter** Themen ohne Anmeldung
gelesen werden kann (eine öffentliche Site, eine mobile App). Ohne diese Einstellung braucht der Endpunkt eine Anmeldung
(Sitzung oder die Token-Anmeldung der Site) und die Richtlinie. `AnonymousFeedback=enabled` (Standard `disabled`) gibt
Anonymous ebenso `exphelp/feedback`, damit die Leser einer öffentlichen Hilfe "War das hilfreich?" beantworten können.

## Der Validator

`./console exp:help:content check --apply -s <siteaccess> [--fix] [--json]` prüft jedes Thema, jede Abbildung, jedes Kapitel,
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
| WARNING | `review-date.too-far` | das Prüfdatum liegt mehr als `ReviewMonths` (12) Monate in der Zukunft, für die Bereiche aus `ShortReviewAreas[]` (privacy, account) mehr als `ShortReviewMonths` (6), mit einem Monat Spielraum (8) |
| WARNING | `version.successor` | `version_to` ist gesetzt, aber kein verwandtes Thema nennt den Nachfolger (7) |
| ERROR | `help-id.renamed`, `help-id.reused` | das Thema wurde unter einer anderen help_id veröffentlicht; die help_id wurde für ein anderes Objekt veröffentlicht. Eine help_id wird nie umbenannt und nie wiederverwendet (7, [veröffentlichte help_ids](#veröffentlichte-help_ids)) |
| WARNING | `help-id.gone` | eine veröffentlichte help_id hat kein Thema mehr; Clients erhalten einen 404 (7) |
| WARNING | `verb.mix` | das Thema verwendet mehr als eines von "click", "tap" und "press" für ein Bedienelement ("press Enter" nennt eine Taste und ist in Ordnung; fette Beschriftungen zählen nicht) (4) |
| NOTICE | `language.sentence-too-long` | ein Satz hat mehr als `SentenceWordsPublic` (20) Wörter in einem Thema für `PlainLanguageAudiences[]` (Besucher, Mitglieder), sonst mehr als `SentenceWordsTechnical` (25) (3) |
| NOTICE | `language.paragraph-too-long` | ein Absatz hat mehr als `ParagraphSentencesPublic` (4) Sätze in einem Thema für Besucher oder Mitglieder (3) |
| NOTICE | `field.recommended` | `platform`, `version_from`, `review_date`, `owner` sind leer (1, 8) |
| ERROR | `section.mismatch` | die Sektion passt nicht zur Zielgruppe; `--fix` setzt sie |

Die Regeln zu einfacher Sprache und zu den Verben lassen die Themen aus der Markdown-Hilfe aus (remote_id
`help-gen-...`): Ihr Text wird in `doc/` geschrieben und dort geprüft. Code, Tabellen, Überschriften und Linkziele zählen
nicht als Sätze; ein Listeneintrag ist ein eigener Absatz. Die Zählungen sind NOTICEs: ein Hinweis für die Prüfung, da
auch eine Liste von Beschriftungen oder ein langer Produktname als Wörter zählt.

Ein Hinweis ist eine fette Beschriftung mit Doppelpunkt: `**Attention:**`, `**Data protection:**` oder `**Note:**`
(auch `**Attention**:`), außerhalb von Code. Im Hilfe-Viewer wird ein Blockzitat, das mit einer solchen Beschriftung
beginnt (`> **Attention:** ...`), oder ein Container (`:::data-protection`) zu einem hervorgehobenen Kasten mit diesem
Titel; Datenschutz hat ein eigenes Symbol.

Nicht maschinell prüfbar und der Prüfung überlassen: Minimalismus, die Anrede, eine Handlung pro Schritt, die Länge
eines Hinweises innerhalb seines Schritts (Hinweis und Handlung teilen sich den Text des Schritts), ob eine Beschriftung
zur Oberfläche passt, die Titelform eines Thementyps.

`check --fix` erfasst auch die help_ids veröffentlichter Themen, die noch nicht erfasst sind (`help_ids record ...`), so
dass eine Installation, die Themen vor der Tabelle veröffentlicht hat, sie einmal erfasst bekommt.

### Veröffentlichte help_ids

Clients rufen ein Thema über seine help_id auf, daher gilt "eine help_id wird nie umbenannt und nie wiederverwendet"
(Styleguide Abschnitt 7). Die Tabelle `exphelp_help_id` hält jede help_id, die veröffentlicht wurde, mit ihrem Objekt
und der Zeit (`expHelpContentHelpIds`); es kommen nur Zeilen hinzu. Eine help_id wird erfasst, wenn ihr Thema auf
Veröffentlicht gesetzt wird (die Statuszuweisung, `expHelpContentWorkflow::afterStateAssign()`), von
`seed-topics --release` und von `check --fix`. Themen aus der Markdown-Hilfe werden nicht erfasst: Ihre help_id folgt dem
Pfad der Seite. Der Validator meldet dann ein Thema, dessen help_id von der abweicht, mit der es veröffentlicht wurde
(`help-id.renamed`), eine help_id, die jetzt ein anderes Objekt nutzt (`help-id.reused`), und eine veröffentlichte
help_id, die kein Thema mehr hat (`help-id.gone`). Der Ausweg, den der Styleguide nennt: die alte help_id zurückgeben,
oder ein neues Thema anlegen und beim alten `version_to` und einen Verweis setzen.

## In der Hilfe (exphelp)

- `exphelp/topic/<help_id>`: das Thema im Layout des Hilfe-Viewers, sein Typ, seine Zielgruppen, Plattform,
  Gültigkeit, die gerenderten Felder (Überschriften Bevor Sie beginnen, Schritte, Ergebnis ... bei Aufgaben;
  Bedingung, Ursache, Abhilfe bei der Fehlerbehebung), seine Abbildungen und verwandten Themen, die Liste der Themen
  und Handbücher. "Dieses Thema bearbeiten" für alle, die es bearbeiten dürfen; "JSON" öffnet die Antwort der
  Kontexthilfe. Unter dem Thema: "War das hilfreich?" (veröffentlichte Themen) und für alle, die das Thema bearbeiten
  oder seinen Status zuweisen dürfen, die Karte **Prüfung und Freigabe** ([der Prüfablauf](#der-prüfablauf)).
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

### Kontexthilfe für Clients mit Token

Der Plan liefert interne Themen an andere Clients "mit einem Token und einer passenden Rolle". `exphelp/context`
antwortet einem angemeldeten Browser (der Sitzung) und, wenn eingeschaltet, anonymen Lesern öffentlicher Themen. Eine
mobile App oder ein Skript ohne Sitzung nutzt den REST-Provider `help` (`rest.ini [ApiProvider]
ProviderClass[help]=expHelpRestProvider`):

```
GET /api/help/v1/topic/content.publish-article
GET /api/help/v1/manual/help-manual-editors
Authorization: Bearer <OAuth-Token oder ein persönlicher API-Schlüssel expk_...>
```

Die Antwort ist das JSON von `exphelp/context` (dieselben Felder; Verweise auf andere Themen sind Adressen von
`exphelp/context`). Die REST-Schicht authentifiziert die Anfrage wie bei jeder REST-Route: ein OAuth-Token des Benutzers
oder ein persönlicher API-Schlüssel mit dem Scope **help** (`rest.ini [ApiKeySettings] Scopes[help]=exphelp/context`,
"Read context help"; ein Schlüssel wird auf der Seite der API-Schlüssel des Benutzers angelegt, und nur von einem
Benutzer, der `exphelp/context` hat). Die Berechtigungen des Benutzers entscheiden, was die Antwort enthält, genau wie
in der Hilfe: ein veröffentlichtes Thema, das er lesen darf, eine Vorschau für alle, die es bearbeiten dürfen, und
dasselbe `404 {"error":"not_found"}` für alles andere. Ein Benutzer ohne `exphelp/context` erhält `403 access_denied`.
Die Antworten werden nie im Anwendungscache von REST gehalten (`[expHelpRestController_CacheSettings]
ApplicationCache=disabled`): Was ein Benutzer sehen darf, hängt vom Benutzer ab.

## Der Prüfablauf

Die Statusgruppe `help_status` ist der Ablauf: Entwurf, Prüfung, Veröffentlicht. Der Reiter Objektstatus der
Administrationsoberfläche kann ihn wie jeden Status setzen; die Seite des Themas in der Hilfe (admin und admin4) zeigt
ihn dort, wo das Thema gelesen wird, in der Karte **Prüfung und Freigabe**, allen, die das Thema bearbeiten oder seinen
Status zuweisen dürfen:

- die drei Status als Schritte, der aktuelle markiert;
- die Schritte, die die `state/assign`-Richtlinien des Benutzers erlauben, als Schaltflächen: **Zur Prüfung geben**
  (Entwurf), **Freigeben** und **Zurück zum Entwurf** (Prüfung), **Zurück in Prüfung** (Veröffentlicht). Ein
  Hilfe-Redakteur erhält nur Zur Prüfung geben; wer zusätzlich die Rolle Help release hat, auch den Rest. Eine
  Schaltfläche ist ein POST an `exphelp/state/<help_id>` mit dem Formular-Token des Kernels und läuft über die
  Statusoperation des Kernels, sodass daran gehängte Workflows wie beim Reiter Objektstatus laufen;
- wenn der Benutzer die aktuelle Version des Themas geschrieben hat, keine Schaltfläche Freigeben, sondern der Grund:
  "Sie haben die aktuelle Version geschrieben, deshalb gibt sie jemand anderes mit der Freigaberolle frei"
  ([wer schreibt, gibt nicht frei](#rollen));
- nach einem Schritt sein Ergebnis (das Thema ist jetzt in Prüfung, veröffentlicht, wieder ein Entwurf, oder warum sich
  nichts geändert hat);
- **Prüfung nach dem Styleguide**: die Befunde des Validators zu diesem Thema (Stufe, Regel, Feld, Befund), geprüft mit
  dem, was seine Regeln brauchen (die verwandten Themen, die Themen mit seiner help_id, seine Abbildungen, das Glossar,
  die veröffentlichten help_ids), nicht mit der ganzen Hilfe, sodass die Redakteurin sieht, was `check` melden würde,
  bevor sie das Thema zur Prüfung gibt;
- die Antworten auf "War das hilfreich?" zu dem Thema: Ja, Nein und die Gründe für Nein.

Die Karte nutzt die gemeinsamen Komponenten der Admin-Designs (`a4-card`, `a4-steps`, `a4-notice`, `a4-table`, `a4-tag`,
`a4-btn`), hat kein eigenes CSS außer ihrem Platz in der Spalte (`stylesheets/modules/help.css`) und folgt dem hellen und
dunklen Modus. Dieselbe Vorlage liegt in design/admin und design/admin4.

## War das hilfreich?

Die Wartungsphase des Plans verlangt "War das hilfreich?" zu jedem Thema und lässt zwei Entscheidungen offen: wo die
Antworten gehalten werden und was das für den Datenschutz der Leser bedeutet. Exponential entscheidet beides für jede
Site:

- **Was gespeichert wird**: Ja, oder Nein mit einem Grund aus einer festen Liste (Es ist veraltet, Es ist schwer zu
  verstehen, Es fehlt etwas, Es ist falsch, Ein anderer Grund), das Thema (Objekt und help_id), seine Sprache und der
  **Tag**, in der Tabelle `exphelp_feedback`. **Nichts darüber, wer geantwortet hat**: kein Benutzer, keine Adresse,
  keine Sitzungs-ID, kein freier Text (freier Text lädt zu personenbezogenen Daten ein und bräuchte Moderation). Die
  Antworten lassen sich zählen, nicht zurückverfolgen.
- **Eine Antwort je Thema und Sitzung** wird gezählt; die Sitzung merkt sich, welche Themen sie beantwortet hat
  (höchstens 50), die Tabelle nicht. Für eine zweite Antwort wird gedankt, gespeichert wird sie nicht.
- **Wie lange**: `FeedbackKeepDays` (730 Tage). Der Cronjob-Teil `exphelp` (Teil von `infrequent`, oder allein:
  `php runcronjobs.php exphelp`) entfernt ältere Antworten; `./console exp:help:content feedback --apply --prune` tut es von Hand.
- **Wer antworten darf**: wer ein veröffentlichtes Thema sehen darf und `exphelp/read` hat (die Leser der Hilfe in der
  Administrationsoberfläche) oder die neue Richtlinienfunktion `exphelp/feedback` (die Leser der öffentlichen Hilfe
  einer Site). Anonymous erhält `exphelp/feedback` nur mit `AnonymousFeedback=enabled` und dem Rollenschritt. Eine
  Vorschau kann nicht beantwortet werden.

Die Frage steht unter jedem veröffentlichten Thema in der Hilfe (`exphelp/feedback/<help_id>`, ein POST mit dem
Formular-Token; zurück zum Thema mit `(helpful)/thanks`) und auf den eigenen Seiten der Site
([unten](#hilfe-auf-den-seiten-der-site)), wo die Antwort zur Seite des Themas zurückführt (`RedirectURI`, nur ein Pfad
der Site). Die Zählungen stehen auf der Karte **Prüfung und Freigabe** des Themas und je Thema auf der Kommandozeile,
das am wenigsten hilfreiche zuerst:

```
./console exp:help:content feedback -s admin [--prune] [--dry-run] [--json]
```

`Feedback=disabled` schaltet die Frage ab; ohne die Tabelle ist sie ebenfalls aus.

## Hilfe auf den Seiten der Site

Der erste Auslieferungsweg des Plans ist die öffentliche Site: "templates `full/help_*.tpl` and `line/help_*.tpl` in the
site design". Der Kernel liefert sie als die Extension **exphelpsite** (`extension/exphelpsite`) aus, nur für den
öffentlichen Siteaccess aktiviert:

```ini
# settings/siteaccess/<öffentlicher Siteaccess>/site.ini.append.php
[ExtensionSettings]
ActiveAccessExtensions[]=exphelpsite
```

Aktivieren Sie sie nie für die Administrationsoberfläche (oder in `ActiveExtensions[]`): Dort behalten die
Hilfeobjekte die eigenen Ansichten der Administrationsoberfläche. Die Überschreibungsregeln der Extension
(`settings/override.ini.append.php`) geben der Vollansicht der vier Themenklassen die Vorlage `help/full_topic.tpl`, von
`help_manual` und `help_section` `help/full_map.tpl` und der Zeilenansicht aller sechs `help/line.tpl`. Ein Design einer
Site mit eigenen Vorlagen für diese Klassen überschreibt die Regeln in seinem Siteaccess.

- Die Seite eines Themas: Titel, Kurzbeschreibung, die gerenderten Felder (das sichere Markdown-Rendering der Hilfe),
  seine Abbildungen mit Alternativtext und Bildunterschrift, seine verwandten Themen und "War das hilfreich?". Die Seite
  eines Handbuchs oder Kapitels: Titel, Untertitel, Kurzbeschreibung, Ausgabe und das Inhaltsverzeichnis, zwei Ebenen
  tief.
- Die Vorlagen lesen `fetch( 'exphelp', 'site_topic', hash( 'object_id', $node.contentobject_id ) )` und
  `fetch( 'exphelp', 'site_map', ... )` (`expHelpContentSite`). Zugriff regeln allein die Berechtigungen von Exponential:
  ein veröffentlichtes Thema, das der Leser lesen darf (Anonymous: `help_public` und Veröffentlicht, die eine Richtlinie
  des Plans), eine Vorschau für alle, die es bearbeiten dürfen. Verwandte Themen, Abbildungen und die Einträge eines
  Handbuchs werden nur angezeigt, wenn der Leser sie sehen darf, und ein Verweis `help:<help_id>` im Text wird zu einem
  Link auf die Seite dieses Themas auf der Site, oder zu einfachem Text, wenn der Leser es nicht sehen darf. Die Vorlagen
  filtern nicht nach Zielgruppe.
- Die Wurzel der Hilfe liegt standardmäßig unter Medien und damit in keinem Menü und keiner Liste der Site; eine Site,
  die ihre Hilfe in der Navigation haben will, verlinkt die Seiten der Handbücher.

## Einstellungen: exphelp.ini [ContentSettings]

| Einstellung | Standard | |
|---|---|---|
| `Enabled` | `enabled` | `disabled`: die Ansichten für Themen, Handbücher und Kontext liefern 404 |
| `ParentNode` | `media` | wo `help-root` angelegt wird: `media`, `content`, `users` oder eine remote_id |
| `Audiences[]` | Visitors, Members, Editors, Administrators, dann die übrigen Zielgruppen der Hilfe (Developers, Partners, Press, media and influencers ...) | die Optionen für Zielgruppen (werden immer nur angehängt) |
| `PublicAudiences[]` | Visitors | eine Zielgruppe, die ein Thema öffentlich macht |
| `Platforms[]` | Web, Admin, Mobile | die Optionen für Plattformen |
| `Areas[]` | account, content, media, search, users, setup, privacy, help, doc | die Bereiche der help_ids (`doc`: die Themen von `build-manuals`) |
| `AudienceMap[<name>]` | visitors, members: user; editors: editor; administrators: admin; developers: developer ... eine je Zielgruppe der Hilfe | die Zielgruppe der Hilfe zu einer Zielgruppenoption |
| `ContextHelpIds[<key>]` | keine | `module/view` (oder `module`, `design:module/view`) = help_id |
| `AnonymousContextHelp` | `disabled` | `enabled`: der Rollenschritt gibt Anonymous `exphelp/context` |
| `EbooksOnRelease` | `enabled` | eine Veröffentlichung erstellt die gespeicherten E-Books der Handbücher neu; `disabled`: nur `exp:help:content ebooks` tut das |
| `ReviewMaxDays` | `14` | Tage, die ein Thema ohne Änderung in Prüfung bleiben darf, bevor es überfällig ist |
| `ReviewListLimit` | `10` | höchstens so viele überfällige Themen auf dem Dashboard der Hilfe |
| `ClientHelpIds[]` | keine (ein auskommentiertes Beispiel in der ausgelieferten Datei; setzen Sie es in einer Überschreibung) | die help_ids, die andere Clients öffnen (eine mobile App, das JavaScript einer Site); `check` meldet eine, die kein Thema hat oder deren Thema nicht veröffentlicht ist |
| `ReleaseByAuthor` | `disabled` | `enabled`: wer die aktuelle Version eines Themas geschrieben hat, darf es freigeben |
| `SentenceWordsPublic`, `SentenceWordsTechnical`, `ParagraphSentencesPublic` | `20`, `25`, `4` | die Grenzen des Validators für einfache Sprache; `0` schaltet eine ab |
| `PlainLanguageAudiences[]` | Visitors, Members | die Zielgruppen mit einfacher Sprache |
| `ReviewMonths`, `ShortReviewMonths`, `ShortReviewAreas[]` | `12`, `6`, privacy und account | wie weit ein Prüfdatum entfernt sein darf (`review-date.too-far`); `0` schaltet es ab |
| `Feedback` | `enabled` | "War das hilfreich?" unter jedem veröffentlichten Thema |
| `FeedbackKeepDays` | `730` | Tage, die die Antworten gehalten werden |
| `AnonymousFeedback` | `disabled` | `enabled`: der Rollenschritt gibt Anonymous `exphelp/feedback` |
| `EpubCheckCommand`, `AceCommand` | leer | die Befehle von epubcheck und Ace by DAISY ([Prüfung eines EPUB](#prüfung-eines-epub)) |
| `BookChecks` | `report` | `required`: ein E-Book, das eine Prüfung nicht besteht, wird nicht gespeichert; `disabled`: keine Prüfung läuft |
| `DitaHistoryDirectory`, `DitaHistoryLogin`, `DitaHistoryAuthor` | leer, `admin`, leer | die Historie der Hilfe in git ([unten](#die-historie-in-git)) |

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
  Themen führen zum Thema in der Hilfe. epubcheck und Ace by DAISY werden nicht mitgeliefert; eine Site, die sie hat,
  nennt ihre Befehle, und jedes Buch wird geprüft ([Prüfung eines EPUB](#prüfung-eines-epub)).
- **PDF** (`writePdf()`) verwendet den PDF-Writer der Hilfe (`expHelpPdfWriter`, Druck und PDF-Download von
  Hilfeseiten): ein Dokument mit dem Titelblock des Handbuchs, Kapiteln und Themen als Überschriften, internen Links und
  den Abbildungen. Das Format wird nur angeboten, wo dieser Writer installiert ist (`expHelpContentExport::formats()`).

### Gespeicherte E-Books

Der Plan speichert die erstellten Bücher als Inhalt (Auslieferungsweg 3): Jedes Format jedes veröffentlichten
Handbuchs ist ein Objekt der Klasse **file** unterhalb des Ordners E-books (remote_id `help-ebooks`), gefunden über
seine remote_id `help-ebook-<manual remote_id>-<format>` (`help-ebook-help-manual-editors-epub`).

```
./console exp:help:content ebooks -s admin [--manual=help-manual-editors] [--language=ger-DE] [--dry-run] [--json]
```

- **Sprachen.** Ohne `--language` sind die Bücher die Ausgabe der Standardsprache. `--language=ger-DE` erstellt die
  deutsche Ausgabe: Themen, Kapitel und Handbuch in ihrer deutschen Übersetzung, auf Englisch, wo ein Thema keine hat,
  gespeichert als `help-ebook-<manual remote_id>-ger-de-<format>` und benannt "<Titel> (EPUB, ger-DE)". Die Seite des
  Handbuchs listet die Ausgaben der Standardsprache und von `[ManualSettings] Languages[]`. Auch `export` nimmt
  `--language`. Der Neuaufbau beim Veröffentlichen erstellt die Ausgabe der Standardsprache; die anderen Ausgaben
  erstellt der Befehl.
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

### Prüfung eines EPUB

Der Plan prüft jedes E-Book mit **epubcheck** (dem EPUB-Validator des W3C, Java) und **Ace by DAISY** (der Prüfung der
Barrierefreiheit nach EPUB Accessibility, Node.js), bevor es veröffentlicht wird. Sie werden nicht mit einer
PHP-Anwendung ausgeliefert; eine Site, die sie hat, nennt ihre Befehle, und Exponential führt sie aus
(`expHelpContentBookCheck`):

```ini
[ContentSettings]
EpubCheckCommand=java -jar /opt/epubcheck/epubcheck.jar
AceCommand=ace
BookChecks=report
```

- Die EPUB-Datei wird als letztes Argument angehängt; Ace erhält davor `--outdir <dir> --force`, und sein `report.json`
  wird gelesen. epubcheck besteht mit dem Exit-Code 0; Ace besteht, wenn sein Bericht keine fehlgeschlagene Aussage hat
  (einen Verstoß gegen EPUB Accessibility oder WCAG). Eine Prüfung läuft höchstens 300 Sekunden.
- `./console exp:help:content export --format=epub --check` gibt das Ergebnis jeder Prüfung aus und endet mit 1, wenn
  eine nicht besteht. Jedes gespeicherte E-Book (`ebooks` und das Neuerstellen bei der Veröffentlichung) wird geprüft,
  und das Ergebnis steht bei den Änderungen (`check epubcheck PASS ...`).
- `BookChecks=report` (Standard) meldet; `required` speichert ein Buch nicht, das eine Prüfung nicht besteht oder dessen
  Prüfung nicht laufen kann (`error ... not stored`); `disabled` führt nichts aus.
- Ein Befehl, der nicht gesetzt ist, wird **übersprungen**, nie als bestanden gewertet: Die Angabe
  `dcterms:conformsTo` des Buchs (EPUB Accessibility 1.1, WCAG 2.2 AA) ist nur durch eine Prüfung belegt, die gelaufen
  ist.

Den Konverter des Plans, DITA-OT, führt Exponential nicht aus: Es schreibt EPUB und PDF selbst aus demselben
Inhaltsmodell (die Writer oben), und der DITA-Export ist die Eingabe für eine Site, die stattdessen die Ausgabe von
DITA-OT möchte.

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

### Die Historie in git

"The DITA export runs regularly into its own git repository. The content lives in the CMS, the change history still
ends up in git" (Plan, Auslieferungsweg 4). `exphelp.ini [ContentSettings] DitaHistoryDirectory` nennt dieses
Verzeichnis, außerhalb des Web-Roots; der Cronjob-Teil `exphelp` (in `infrequent`, oder `php runcronjobs.php exphelp`)
schreibt dann den DITA-Export jedes Handbuchs hinein und committet, was sich geändert hat (`expHelpContentDitaHistory`):

- der erste Lauf macht das Verzeichnis zu einem Git-Repository (`git init`); ein Lauf ohne Änderung erzeugt keinen
  Commit;
- der Export enthält die veröffentlichten Objekte, die `DitaHistoryLogin` (Standard `admin`) lesen darf, auch interne;
  das Repository ist also so vertraulich wie die interne Hilfe: Behandeln Sie es so;
- die Commits tragen `DitaHistoryAuthor` ("Name <mail>"), wenn gesetzt, sonst die Git-Konfiguration des Repositorys; der
  Job pusht nie, ein Remote ist Sache der Site;
- git läuft ohne Shell, mit dem Verzeichnis als Arbeitsbaum.

Von Hand: `./console exp:help:content dita -s admin --output=<dir> --commit` (mit `--dry-run`: was committet würde).

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
Übergabe: [die Hilfeklassen als Paket](../../spezifikationen/6.0/hilfeklassen-als-paket.md).

## Abgleich mit den Spezifikationen

Geprüft am 19. Oktober 2026 gegen den [Plan](../../spezifikationen/6.0/hilfesystem.md), den
[Styleguide](../../spezifikationen/6.0/hilfe-stilrichtlinien.md) und die [Standards](../../spezifikationen/6.0/hilfestandards.md)
(Pull-Request #288), Punkt für Punkt im Code, nicht anhand dieser Seite. Der DITA-Export, die E-Books als Dateiobjekte,
die Prüfliste und ihre Übersetzungen, am 19. Oktober noch in Arbeit, folgten am 20. Oktober. Erneut geprüft am
25. Oktober 2026; was damals ausgelassen oder nur eine Absprache war, ist jetzt erledigt oder hat unten seine
Begründung.

| Spezifikation | Stand |
|---|---|
| DITA-Thementypen: ein Typ pro Thema, Konzept, Aufgabe, Referenz, Fehlerbehebung | erledigt: `expHelpContentSchema::classes()`, die Felder jedes Typs so, wie der Plan sie aufführt |
| Maps: Handbuch (Bookmap) und Kapitel mit geordneten `topicrefs`, Themenpool | erledigt: `help_manual`, `help_section`, der Baum unterhalb von `help-pool`; Validator `map.*` |
| Abbildungen und Glossar als Klassen | erledigt: `help_screenshot` (Alternativtext erforderlich), `help_glossary_entry` (zu vermeidende Synonyme) |
| Die Vorlagen des Styleguides (Konzept, Aufgabe, Referenz, Fehlerbehebung, die zweite Aufgabe) | erledigt: alle fünf stehen in `share/help/seed-topics.json` (`content.versions`, `content.publish-article`, `content.edit-buttons`, `content.not-visible`, `account.log-in`) und bestehen den Validator ohne Befund (Test `testStyleGuideTemplatesPass`) |
| Sektionen `help_public`/`help_internal` anhand der Zielgruppe gesetzt, nur am Objekt | erledigt: `check --fix`, nach jedem `seed-*` |
| Objektstatus Entwurf, Prüfung, Veröffentlicht; nur Veröffentlichtes ausgeliefert | erledigt: `help_status`, der Provider zeigt Entwurf und Prüfung nur Redakteuren, als Vorschau |
| Rollen Help editor, Help release; eine Anonymous-Richtlinie mit beiden Einschränkungen | erledigt: `exp:help:content roles` |
| "Wer ein Thema geschrieben hat, gibt es nicht frei" | erledigt (25. Oktober): in der Statusoperation für jeden Weg der Statuszuweisung durchgesetzt ([Rollen](#rollen)); `ReleaseByAuthor=enabled` für einen einzigen Redakteur |
| Der Ablauf Entwurf, Prüfung, Veröffentlicht dort, wo das Thema gelesen wird | erledigt (25. Oktober): die Karte **Prüfung und Freigabe** von `exphelp/topic` in admin und admin4, mit der Prüfung des Themas nach dem Styleguide ([der Prüfablauf](#der-prüfablauf)) |
| `help_id` nie umbenannt, nie wiederverwendet | erledigt (25. Oktober): `exphelp_help_id`, `help-id.renamed`, `help-id.reused`, `help-id.gone` ([veröffentlichte help_ids](#veröffentlichte-help_ids)) |
| Grenzen einfacher Sprache, ein Verb je Plattform, Prüfdatum + 12/6 Monate, `version_to` mit Nachfolger (Styleguide Abschnitte 3, 4, 7, 8) | erledigt (25. Oktober): `language.*` (NOTICE), `verb.mix`, `review-date.too-far`, `version.successor` |
| CLI-Skripte mit `-s`, `--dry-run`, idempotent ("No change.") | erledigt: die Aktionen von `exp:help:content` |
| Validatorregeln des Plans (Schritte, Alternativtext, `shortdesc`, `help_id`, Glossar, Prüfdatum, Sektion) | erledigt |
| Hinweisstufen (Abschnitt 5 des Styleguides: Attention, Data protection, Note; im Schritt, zuerst, einer pro Schritt, höchstens drei Sätze) | **jetzt hinzugefügt**: `notice.position`, `notice.before-action`, `notice.per-step`, `notice.too-long`; hervorgehobene Kästen für Attention und Data protection mit eigenem Titel im Viewer. Die Länge eines Hinweises innerhalb eines Schritts bleibt eine Regel für die Prüfung |
| Schema und Eindeutigkeit der `help_id` | erledigt: `help-id.*` |
| Kontexthilfe-JSON per `help_id`, ETag, öffentliche Themen ohne Anmeldung | erledigt: `exphelp/context/<help_id>`, `exphelp/context/manual/<remote_id>`; anonymer Zugriff ist Opt-in (`AnonymousContextHelp`) |
| Interne Themen für Clients "mit einem Token und einer passenden Rolle" | erledigt (25. Oktober): `/api/help/v1/topic/<help_id>` und `/manual/<remote_id>` mit einem OAuth-Token oder einem persönlichen API-Schlüssel mit dem Scope `help` ([Kontexthilfe für Clients mit Token](#kontexthilfe-für-clients-mit-token)) |
| "Clients führen eine Liste der help_ids, die sie verwenden; ein Test prüft, dass jede existiert" | **jetzt hinzugefügt**: `client.help-id-unknown`, `client.help-id-not-released` über `ContextHelpIds[]` und das neue `ClientHelpIds[]` |
| E-Book EPUB und PDF eines Handbuchs, Barrierefreiheits-Metadaten | erledigt: `exp:help:content export`, **EPUB/PDF herunterladen** |
| Export als DITA-Bookmap + DITAVAL, regelmäßiger DITA-Export in ein Git-Repository (Auslieferungswege 3 und 4) | erledigt: `exp:help:content dita`, **DITA herunterladen** ([DITA-Export](#dita-export)); gültig gegen die DTDs von OASIS DITA 1.3. Der regelmäßige Lauf nach Git (25. Oktober): der Cronjob-Teil `exphelp` mit `DitaHistoryDirectory` und `dita --commit` ([die Historie in git](#die-historie-in-git)) |
| DITA-OT 4.x als Konverter nach EPUB und PDF | bewusst ausgelassen: Exponential schreibt EPUB und PDF selbst aus demselben Modell, sodass eine PHP-Installation Bücher ohne Java erstellt; der DITA-Export ist die Eingabe für eine Site, die die Ausgabe von DITA-OT möchte |
| E-Book als Dateiobjekt unter "E-books" (`help-ebooks`) gespeichert | erledigt: `exp:help:content ebooks`, bei der Veröffentlichung neu erstellt, versioniert, Sektion anhand der Zielgruppe ([gespeicherte E-Books](#gespeicherte-e-books)) |
| Liste der Themen mit überschrittenem Prüfdatum im Dashboard (Phase 7) | erledigt: das Dashboard der Hilfe und `exp:help:content review`, auch Themen, die zu lange in Prüfung sind ([überfällige Prüfungen](#überfällige-prüfungen)) |
| epubcheck und Ace by DAISY für jedes E-Book | erledigt (25. Oktober), wo die Site sie hat: Die Werkzeuge sind Java und Node.js und werden nicht mitgeliefert, daher nennt die Site ihre Befehle (`EpubCheckCommand`, `AceCommand`), und jedes gespeicherte E-Book und `export --check` führt sie aus; `BookChecks=required` speichert kein Buch, das nicht besteht. Ohne die Befehle wird eine Prüfung als übersprungen gemeldet, nie als bestanden ([Prüfung eines EPUB](#prüfung-eines-epub)) |
| Web-Templates `full/help_*.tpl`, `line/help_*.tpl` für die öffentliche Site (Auslieferungsweg 1) | erledigt (25. Oktober): die Extension `exphelpsite`, für den öffentlichen Siteaccess aktiviert, mit `fetch( 'exphelp', 'site_topic' / 'site_map' )` ([Hilfe auf den Seiten der Site](#hilfe-auf-den-seiten-der-site)) |
| "War das hilfreich?" bei jedem Thema (Phase 7) | erledigt (25. Oktober): Ja oder Nein mit Grund, je Thema und Tag, nichts darüber, wer geantwortet hat, 730 Tage aufbewahrt ([War das hilfreich?](#war-das-hilfreich)) |
| Hilfe nie Quelle für KI-Antworten (RAG), sofern nicht gewollt | standardmäßig erledigt: Der Kernel hat keine Liste von RAG-Quellen, und die Wurzel der Hilfe liegt unter Medien, nicht im Inhaltsbaum. Eine Extension, die eine solche Liste hinzufügt, muss `help-root` auslassen |
| Die Hilfeklassen als Paket | **jetzt hinzugefügt**: `exp:help:content package`, `verify-package` (CJW #16564) |

## Später

Komfortfunktionen für Redakteure (ein Bearbeitungsformular je Thementyp, eine Vorschau einer Abbildung im Feld ihres
Themas).

## Klassen

`expHelpContentSchema` (das Modell, nur Daten), `expHelpContentInstaller` (Klassen, Baum, Rollen, Bericht,
Deinstallation, Korrektur der Sektionen), `expHelpContentRepository` (liest die Hilfeobjekte in Arrays),
`expHelpContentValidator` (die Regeln, keine Datenbank), `expHelpContentSeeder`, `expHelpContentProvider` (was der
aktuelle Benutzer sehen darf, Rendering, JSON, Suche, Kontext), `expHelpContextPageInterface`, `expHelpContentPackage`
(das Klassenpaket), `expHelpContentExport` (EPUB, PDF), `expHelpContentDita` (der DITA-Export, seine Prüfung, das
Schreiben und das Zip), `expHelpDitaMarkup` (das HTML der Hilfe als DITA-Elemente), `expHelpContentEbooks` (die
gespeicherten E-Books, bei der Veröffentlichung neu erstellt), `expHelpContentEditionProvider` (was in eine gespeicherte
Ausgabe kommt), `expHelpContentReview` (überfällige Prüfungen), `expHelpContentWorkflow` (der Prüfablauf, die Regel
für Autoren), `expHelpContentHelpIds` (veröffentlichte help_ids), `expHelpContentFeedback` ("War das hilfreich?"),
`expHelpContentTables` (die beiden Tabellen auf jeder Engine), `expHelpContentBookCheck` (epubcheck, Ace),
`expHelpContentDitaHistory` (die Historie in git), `expHelpContentSite` (die eigenen Seiten der Site) und der
REST-Provider `expHelpRestProvider`, `expHelpRestController`, `expHelpRestViewController`. Ansichten:
`Exponential\View\Kernel\Exphelp\Topic`, `Manual`, `Context`, `State`, `Feedback`; Cronjob-Teil
`Exponential\Cronjob\Kernel\Exphelp`. Tests (ohne Datenbank):
`tests/tests/kernel/classes/help/expHelpContentTest.php`, `expHelpContentPackageTest.php`, `expHelpContentExportTest.php`,
`expHelpContentDitaTest.php`, `expHelpContentEbooksTest.php`, `expHelpContentReviewTest.php`,
`expHelpContentTranslationsTest.php`, `expHelpContentWorkflowTest.php`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
