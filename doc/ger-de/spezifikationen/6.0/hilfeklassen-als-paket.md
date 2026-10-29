# Die Hilfeklassen als Paket: Inhalt, Build und Übergabe

Stand: 19. Oktober 2026, Exponential 6.0.15. Anfrage eines Partners: die Inhaltsklassen der Hilfe als Paket, mit einer
Übergabe des Builds. Plan: [help-system.md](hilfesystem.md); was die Klassen sind: [Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md).

Wer es lesen sollte: wer das Paket für ein Release baut und an den Paketserver übergibt, und ein Administrator, der die
Hilfeklassen über **Setup > Pakete** statt über die Kommandozeile auf eine Website bringen möchte.

## Kurz gesagt

- Das Paket **exp_help_classes** (Anbieter `7x`, Typ `contentclass`) enthält die acht Hilfeklassen der Klassengruppe
  **Help**: `help_manual`, `help_section`, `help_concept`, `help_task`, `help_reference`,
  `help_troubleshooting`, `help_screenshot`, `help_glossary_entry`.
- Es wird mit einem Befehl aus einer Installation gebaut, die die Klassen hat:
  `./console exp:help:content package -s <admin siteaccess>`. Die Klassendateien sind das eigene Klassen-XML des Kernels,
  das Paket installiert sich also wie jedes andere Klassenpaket.
- `./console exp:help:content verify-package -s <admin siteaccess> --file=<.ezpkg>` vergleicht ein gebautes Paket mit den
  Klassen des Releases, **ohne Datenbank**.
- Das Paket ist **nicht** das ganze Hilfesystem. Sektionen, die Objektzustandsgruppe, der Ordnerbaum und die Rollen
  enthalten IDs der Installation; `./console exp:help:content install --apply` legt sie an und ergänzt die Klassen, die ein Paket
  installiert hat. Der Befehl ist idempotent: Nach dem Paket gibt er nur die Teile aus, die das Paket nicht mitbringt.
- Gebaut wird hier; **das Veröffentlichen auf dem Paketserver ist der Schritt des Eigentümers** (unten).

## Inhalt

1. [Was das Paket enthält](#was-das-paket-enthält)
2. [Was es nicht enthält und warum](#was-es-nicht-enthält-und-warum)
3. [Bauen](#bauen)
4. [Prüfen](#prüfen)
5. [Version](#version)
6. [Übergabe an den Paketserver](#übergabe-an-den-paketserver)
7. [Auf einer Website installieren](#auf-einer-website-installieren)
8. [Dateien](#dateien)
9. [Verwandte Seiten](#verwandte-seiten)

## Was das Paket enthält

| Teil | Inhalt |
|---|---|
| `package.xml` | Name `exp_help_classes`, Anbieter `7x`, Typ `contentclass`, Installationstyp `install`, Lizenz GPL, Status `stable`, das Release (unten), ein `provide` je Klasse und ein Installationseintrag je Klasse, in der Reihenfolge von `expHelpContentSchema::classes()` (die Relationslisten nennen ihre erlaubten Klassen per Bezeichner, die Reihenfolge spielt also keine Rolle) |
| `ezcontentclass/class-<identifier>.xml` | jede Klasse so, wie `eZContentClassPackageHandler::classDOMTree()` sie schreibt: Namen und Beschreibungen, Bezeichner, remote_id (`help-class-<identifier>`), Namensschema, Container-Kennzeichen, die Gruppe **Help** und jedes Attribut mit seinem Datentyp, seinen Kennzeichen und Datentypparametern (Zeichenkettenlängen, die Auswahloptionen, die erlaubten Klassen der Relationsliste) |

Die Auswahloptionen (`audience`, `platform`) sind die der Installation, aus der das Paket gebaut wird. Bauen Sie auf einer
Installation mit den Standardoptionen (`exphelp.ini [ContentSettings] Audiences[]`, `Platforms[]` nicht überschrieben),
damit das Paket Visitors, Members, Editors, Administrators und Web, Admin, Mobile mitbringt. Eine Website mit anderen
Optionen bekommt ihre eigenen von `exp:help:content install` angehängt; Optionen werden immer nur angehängt, nie umsortiert.

## Was es nicht enthält und warum

| Nicht im Paket | Angelegt von | Warum |
|---|---|---|
| die Sektionen `help_public`, `help_internal` | `exp:help:content tree` | eine Sektion ist eine Zeile mit einer ID der Installation; die Einschränkungen der Rollen nennen sie |
| die Objektzustandsgruppe `help_status` (Draft, Review, Released) | `exp:help:content tree` | dasselbe, und ihr erster Zustand wird einmal jedem Objekt der Installation zugewiesen |
| der Ordnerbaum (`help-root`, `help-pool` ...) | `exp:help:content tree` | er liegt unter dem Knoten, den `[ContentSettings] ParentNode` nennt, und der ist je Website verschieden |
| die Rollen Help editor, Help release, die Richtlinie für Anonymous | `exp:help:content roles` | sie schränken nach Sektions-, Zustands- und Teilbaum-IDs der Installation ein |
| Themen, Handbücher, Abbildungen, das Glossar | `exp:help:content seed-glossary`, `seed-topics` oder die Redakteure | Inhalt, keine Struktur; die Startdaten liegen in `share/help/` |

Ein Paket mit Inhaltsklassen kann nichts davon ausdrücken ohne Knoten-, Sektions- oder Zustands-IDs, die bei der nächsten
Installation falsch sind. Der Befehl findet und erzeugt sie auf jeder Website per Bezeichner und remote_id, idempotent.

## Bauen

Auf einer Installation des Releases (am besten eine Wegwerfinstallation; nie eine Produktivwebsite mit geänderten
Hilfeeinstellungen):

```bash
./console exp:help:content classes --apply -s <admin siteaccess>             # the classes, if not yet there
./console exp:help:content classes --apply -s <admin siteaccess>             # "No change." (the acceptance test)
./console exp:help:content package -s <admin siteaccess> --dry-run   # what would be written
./console exp:help:content package -s <admin siteaccess> [--output=<dir>] [--version=6.0.15]
```

`package`:

1. liest die acht Klassen per Bezeichner; fehlt eine, bricht es mit „run exp:help:content classes first“ ab;
2. schreibt das Paketverzeichnis `<dir>/exp_help_classes/` (`package.xml`, `ezcontentclass/`), den Betreuer und den
   Changelog-Eintrag vom Benutzer aus `--login` (Standard `admin`);
3. exportiert es als `<dir>/exp_help_classes-<version>.ezpkg` (eine gzip-komprimierte Tar-Datei, wie `ezpm.php export` sie
   schreibt);
4. prüft das Archiv mit `verify-package` und gibt `PASS wrote ...` aus oder die Unterschiede und `FAIL` (Exit-Code 1).

`--output` ist standardmäßig `var/<site>/cache/exphelp/packages`. Ein vorhandenes Paketverzeichnis und ein Archiv derselben
Version in diesem Verzeichnis werden ersetzt. Nichts wird nach `var/storage/packages/` geschrieben: Das Paket wird nicht in
die Installation importiert, auf der es gebaut wurde.

## Prüfen

```bash
./console exp:help:content verify-package -s <admin siteaccess> --file=<dir>/exp_help_classes-6.0.15.ezpkg
./console exp:help:content verify-package -s <admin siteaccess> --file=<dir>/exp_help_classes      # a directory
```

`verify-package` liest das Archiv in PHP (kein Entpacken auf die Platte, kein Programm wird gestartet) und vergleicht es mit
`expHelpContentSchema::classes()` des Releases, auf dem es läuft:

- `package.xml`: den Namen, den Typ `contentclass`, ein `provide` und einen Installationseintrag für jede der acht Klassen,
  keine andere Klasse;
- jede Klassendatei: ihren Bezeichner, das Container-Kennzeichen, die Klassengruppe **Help**, jedes Attribut des Schemas
  mit seinem Datentyp und seinem Kennzeichen „erforderlich“ und kein Attribut, das das Schema nicht hat.

Es gibt `PASS ... matches the help classes of this release` aus (Exit-Code 0) oder je Unterschied eine Zeile und `FAIL`
(Exit-Code 1). Es braucht keine Datenbank und kann daher auch im Checkout des Paketservers laufen.

Der Abnahmetest eines Releases, auf einer **neuen Wegwerfinstallation** (nie auf der Live-Website):

1. Installieren Sie das Paket (**Setup > Pakete > Hochladen**, dann **Installieren**, oder `php ezpm.php import <file>` und
   `php ezpm.php install exp_help_classes`);
2. `./console exp:help:content classes --apply -s <admin siteaccess>` gibt **No change.** aus: Das Paket hat jede Klasse, jedes
   Attribut und jede Option installiert, die der Befehl angelegt hätte;
3. `./console exp:help:content install --apply -s <admin siteaccess>` legt dann die Sektionen, Zustände, den Baum und die Rollen
   an, und ein zweiter Lauf gibt **No change.** aus;
4. entfernen Sie die Wegwerfinstallation.

Letzter Lauf am 19. Oktober 2026 auf einer SQLite-Wegwerfinstallation von 6.0.15 (sevenx_multisite): `classes` zweimal
(„No change.“ beim zweiten Mal), `package` (`PASS wrote ... (8 classes)`), `verify-package` PASS; danach auf der frischen
Datenbank `ezpm.php import` und `install` von `exp_help_classes-6.0.15.ezpkg`, worauf `classes` „No change.“ ausgab,
`install` die Sektionen, Zustände, den Baum und die Rollen anlegte und sein zweiter Lauf „No change.“ ausgab.

## Version

- Die Version des Pakets ist das Exponential-Release, aus dem es gebaut wurde, aufgeteilt wie bei den 7x-Paketen: Version
  `6.0`, Release `15` für 6.0.15 (`expHelpContentPackage::release()`); die Datei heißt `exp_help_classes-6.0.15.ezpkg`.
- `--version=` baut eine andere Nummer (eine Korrektur des Pakets eines Releases: Bauen Sie es aus dem korrigierten Release
  und geben Sie ihm die nächste Nummer; eine veröffentlichte Datei wird nie ersetzt, siehe die Release-Regeln des Projekts).
- Ein neues Paket ist nötig, wenn sich `expHelpContentSchema::classes()` ändert (eine Klasse oder ein Attribut kommt
  hinzu). Das Schema wächst nur: Attribute werden nie geändert oder entfernt, ein älteres Paket lässt sich also weiter
  installieren, und `exp:help:content classes` ergänzt, was ihm fehlt.

## Übergabe an den Paketserver

Wer baut, übergibt; der Eigentümer veröffentlicht. Was der Bauende liefert:

| Gegenstand | Wo |
|---|---|
| das Archiv `exp_help_classes-<version>.ezpkg` | das Ausgabeverzeichnis von `package` |
| die Ausgabe von `verify-package` (PASS) | in der Übergabenotiz |
| die Ausgabe des Abnahmetests (Schritte 2 und 3: „No change.“) | in der Übergabenotiz |
| der Commit des Releases, aus dem das Paket gebaut wurde | in der Übergabenotiz |

Was der Eigentümer tut (das eigene Verfahren des Paketservers, außerhalb dieses Repositorys): das Archiv in den Checkout des
Paketservers für das Release kopieren (`exponential/6.0/6.0.15/`), es in die `index.xml` dieses Releases aufnehmen, wenn der
Setup-Assistent es anbieten soll, den Paketserver neu bauen und committen und dann prüfen, dass die Download-URL antwortet.
Der Setup-Assistent liest den Index seines Releases aus `package.ini [RepositorySettings] RemotePackagesIndexURL`.

Der Build selbst lädt nichts hoch, pusht nichts und veröffentlicht nichts.

## Auf einer Website installieren

Ein Administrator, der Pakete der Kommandozeile vorzieht:

1. **Setup > Pakete > Hochladen**, wählen Sie `exp_help_classes-<version>.ezpkg`, dann auf seiner Seite **Installieren**.
   Die Klassen erscheinen in der Klassengruppe **Help**.
2. Führen Sie einmal `./console exp:help:content install --apply -s <admin siteaccess>` aus (Sektionen, Zustände, Baum, Rollen). Der
   Befehl findet die Klassen, die das Paket installiert hat, und ändert nichts an ihnen.
3. Optional `seed-glossary` und `seed-topics` für die Startinhalte, wie auf jeder Installation.

Das Deinstallieren des Pakets entfernt die Klassen (es fragt vorher, wenn Objekte einer Klasse existieren, und entfernt sie
mit); `exp:help:content uninstall --with-content` ist der vollständige Weg zurück, mit dem Baum, den Zuständen und den
Sektionen.

## Dateien

- `kernel/classes/help/content/exphelpcontentpackage.php`: `expHelpContentPackage` (Definition, `build()`,
  `verifyDirectory()`, `verifyArchive()`, `readArchive()`).
- `kernel/private/classes/commands/exphelpcontent.php`: die Aktionen `package` und `verify-package`.
- `tests/tests/kernel/classes/help/expHelpContentPackageTest.php` (ohne Datenbank).

## Verwandte Seiten

- [Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md)
- [Plan des Hilfesystems](hilfesystem.md)
- [Hilfestandards](hilfestandards.md)
- [Pakete](../../anleitungen/pakete.md)
