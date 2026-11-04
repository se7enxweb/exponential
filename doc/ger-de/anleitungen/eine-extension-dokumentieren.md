---
title: Eine Extension dokumentieren
audience: developer, admin
summary: Wohin die Dokumentation einer Extension gehört (README.md und INSTALL.md oben, jede weitere Seite als Markdown in doc/, auf Englisch), wie das Hilfecenter sie zeigt und wie alte Klartextdokumente zu Markdown werden.
keywords: Extension-Dokumentation, doc-Verzeichnis, README.md, INSTALL.md, Markdown, Klartext, Hilfecenter, ext/, ExtensionTopFiles, ActiveExtensionDocs, ExtensionDocDirs, Front Matter, Kontexthilfe
---

# Eine Extension dokumentieren

Jede Extension dokumentiert sich am selben Ort und nach derselben Konvention. So findet jeder, der sie installiert,
die Dokumentation ohne Suchen, und das Hilfecenter jeder Installation zeigt sie ohne jede Einrichtung. Diese Anleitung
ist die Referenz für diese Konvention. Die anderen Seiten, die die Dokumentation einer Extension erwähnen, verweisen
hierher.

Am Ende dieser Anleitung haben Sie eine Extension, deren README, Installationshinweise und Handbuch im Hilfecenter
unter **Extensions** erscheinen, in der richtigen Reihenfolge, mit funktionierenden Links, Bildern und Kontexthilfe.

## Inhalt

1. [Die Konvention in Kürze](#1-die-konvention-in-kürze)
2. [Der Verzeichnisaufbau](#2-der-verzeichnisaufbau)
3. [Warum Markdown: von Klartext zu .md](#3-warum-markdown-von-klartext-zu-md)
4. [Ein Klartextdokument umwandeln](#4-ein-klartextdokument-umwandeln)
5. [Wie das Hilfecenter die Seiten zeigt](#5-wie-das-hilfecenter-die-seiten-zeigt)
6. [Reihenfolge, Titel und Kontexthilfe](#6-reihenfolge-titel-und-kontexthilfe)
7. [Was nie gezeigt wird](#7-was-nie-gezeigt-wird)
8. [Prüfen](#8-prüfen)
9. [Fehlerbehebung](#9-fehlerbehebung)

## 1. Die Konvention in Kürze

- **Oben in der Extension:** `README.md` und `INSTALL.md`, dazu `CHANGELOG.md`, `LICENSE`, `USAGE.md` und `COPYING`,
  soweit die Extension sie hat, direkt in `extension/<extensionName>/`.
- **Alles Weitere:** als Markdown-Dateien (`.md`) in `extension/<extensionName>/doc/`, mit `doc/README.md` (oder
  `doc/index.md`) als Startseite des Handbuchs.
- **Auf Englisch.** Englisch ist die Sprache, die man bei jedem Leser einer Extension voraussetzen kann. Ein englisches
  Original hält die Dokumentation für alle nutzbar. Das Hilfecenter ordnet Übersetzungen von Extension-Seiten nicht
  ihren englischen Seiten zu, wie es das für `doc/eng-us/` und `doc/ger-de/` des Kernels tut.
- **Eingecheckt.** Gezeigt werden nur Dateien, die das Repository der Extension verfolgt (oder die ihr Release in
  `share/filelist.md5` aufführt). Eine Datei, die nur auf der Platte liegt, nicht.

Einzurichten ist nichts: Das Hilfecenter zeigt die Dokumentation jeder aktiven Extension, sobald sie aktiv ist.

## 2. Der Verzeichnisaufbau

```text
extension/myextension/
├── README.md            was sie tut, Voraussetzungen, Installation in Kürze, ein erstes Beispiel, Lizenz
├── INSTALL.md           die Installation Schritt für Schritt
├── CHANGELOG.md         was sich in jedem Release geändert hat
├── LICENSE              der Lizenztext
├── extension.xml, ezinfo.php, settings/, design/, modules/, classes/ ...   (nie gezeigt)
└── doc/
    ├── README.md        Startseite des Handbuchs: was darin steht, Links zu jeder Seite
    ├── settings.md      die Einstellungen mit ihren Standardwerten
    ├── templates.md     Templates, Variablen, Operatoren
    ├── upgrading.md     was zwischen Versionen zu tun ist
    ├── images/
    │   └── edit-form.webp
    └── howto/
        └── first-steps.md
```

- **Links zwischen Seiten** sind relativ, wie auf GitHub: `[Settings](settings.md)` aus `doc/README.md`,
  `[the README](../README.md)` aus einer Seite in `doc/` zur README oben.
- **Bilder** liegen neben den Seiten (`doc/images/`) und werden relativ verlinkt:
  `![The edit form](images/edit-form.webp)`. Das Hilfecenter liefert PNG-, JPEG-, GIF- und WebP-Bilder aus. SVG und
  HTML werden nie ausgeliefert.
- **Eine Seite in einem Unterordner** von `doc/` erscheint in diesem Ordner: `doc/howto/first-steps.md` wird zur Seite
  `ext/myextension/howto/first-steps.md`.
- **Eine Datei in `doc/` hat Vorrang vor einer gleichnamigen Datei oben:** Gibt es `doc/README.md` und `README.md`,
  zeigt das Hilfecenter `doc/README.md` als `ext/myextension/README.md`.

`expai` ist ein vollständiges Beispiel: `README.md`, `INSTALL.md` und `USAGE.md` oben und mehr als vierzig
Markdown-Seiten in seinem `doc/`.

## 3. Warum Markdown: von Klartext zu .md

Die Dokumentation von Extensions begann als Klartextdateien auf Englisch. Die Extensions der eZ-Publish-Jahre, die
Exponential fortgeführt hat, lieferten eine `README` und eine `INSTALL` ohne Endung, ein `CHANGELOG` oder Dateien wie
`doc/INSTALL`, `doc/FAQ` und `doc/*.txt`; einige nutzten reStructuredText (`README.rst`) oder Office-Dokumente (`.odt`,
`.pdf`). Das sieht man noch an den Extensions aus dieser Zeit: `ezjscore` hat `README`, `CHANGELOG`, `doc/INSTALL` und
`doc/FAQ`, `ezoe` hat `README.rst` und `changelog.txt`.

Seitdem hat sich die Dokumentation zu vollständigen Markdown-Dokumenten entwickelt, und **Markdown (`.md`) ist die
Konvention für neue und überarbeitete Extensions.** Markdown gibt dem Leser, was Klartext nicht kann:

- **Überschriften**, die zur Gliederung der Seite und zu Ankern werden (`settings.md#clearing-the-cache`);
- **Links zwischen Seiten**, denen das Hilfecenter folgt und die es prüft;
- **Tabellen** für Einstellungen und ihre Standardwerte;
- **Codeblöcke** mit Hervorhebung für INI, PHP, Templates und Shell-Befehle;
- **Bilder** neben dem Text;
- **Front Matter** für Titel, Reihenfolge, Zielgruppe und Zusammenfassung der Seite
  ([Abschnitt 6](#6-reihenfolge-titel-und-kontexthilfe)).

Das Hilfecenter stellt Markdown als Seite mit eigenem Inhaltsverzeichnis, hervorgehobenem Code, einem Sucheintrag und
einem Platz in der Seitenleiste dar. Eine Klartextdatei wird so gezeigt, wie sie ist, in einem Block mit fester
Zeichenbreite, ohne Links und Überschriften.

**Vorhandene Klartextdokumentation funktioniert weiter.** Das Hilfecenter zeigt `.txt`, `.rst` und Textdateien ohne
Endung (`README`, `INSTALL`, `CHANGELOG`) weiterhin als Klartextseiten, damit beim Installieren einer alten Extension
nichts bricht. Office-Dokumente (`.odt`, `.pdf`) werden nicht gezeigt; übertragen Sie ihren Inhalt in Markdown-Seiten.

## 4. Ein Klartextdokument umwandeln

1. **Umbenennen** mit `git mv`, damit die Historie erhalten bleibt: `git mv INSTALL INSTALL.md`,
   `git mv doc/FAQ doc/faq.md`.
2. **Überschriften ergänzen:** ein `#`-Titel in der ersten Zeile, `##` für jeden Abschnitt. Unterstrichene
   Überschriften (`=====`, `-----`) werden zu `#`- und `##`-Zeilen.
3. **Verweise zu Links machen:** „see doc/settings.txt" wird zu `see [the settings](doc/settings.md)`, eine nackte URL
   zu `[ihr Name](https://...)`. Tote Adressen (ez.no, share.ez.no) werden durch die aktuelle Seite ersetzt oder in
   Worten beschrieben.
4. **Code einzäunen:** eingerückte Befehle und Einstellungen kommen in umzäunte Blöcke mit ihrer Sprache (`ini`,
   `bash`, `php`).
5. **Englisch beibehalten** und den Inhalt bewahren: Die Umwandlung ändert die Form, nicht das, was das Dokument sagt.
6. **Einchecken** und prüfen ([Abschnitt 8](#8-prüfen)).

Vorher, `extension/myextension/INSTALL`:

```text
MyExtension installation
========================

1. Copy the extension to extension/myextension
2. Enable it in settings/override/site.ini.append.php:

   [ExtensionSettings]
   ActiveExtensions[]=myextension

3. Clear the caches. See doc/settings.txt for the settings.
```

Nachher, `extension/myextension/INSTALL.md`:

````markdown
# Installing MyExtension

## 1. Get the code

```bash
composer require vendor/myextension
```

## 2. Switch it on

In `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=myextension
```

## 3. Clear the caches

```bash
php bin/php/ezcache.php --clear-all
```

The settings are described in [Settings](doc/settings.md).
````

## 5. Wie das Hilfecenter die Seiten zeigt

Das Hilfecenter (`/exphelp/dashboard` in der Administration) führt die Dokumentation jeder **aktiven** Extension auf
(`[IndexSettings] ActiveExtensionDocs=enabled` in `settings/exphelp.ini`, die Voreinstellung). Eine eingeschaltete
Extension erscheint sofort, eine ausgeschaltete verschwindet sofort.

| | |
|---|---|
| Welche Dateien | die eingecheckten Dateien unter `extension/<name>/doc/` und die eingecheckten Dateien oben in `extension/<name>/`, deren Namen zu `[IndexSettings] ExtensionTopFiles[]` passen (`README*`, `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*`, ohne Beachtung der Groß- und Kleinschreibung) |
| Seitenendungen | `.md`, `.markdown`, `.txt`, `.rst` (`DocumentSuffixes[]`) und Textdateien ohne Endung |
| Seiten-ID | `ext/<name>/<Pfad unter doc/>`, zum Beispiel `ext/expai/vector.md`; eine Datei oben ist `ext/<name>/README.md` |
| Adresse | `/exphelp/viewer/ext/<name>/<Pfad>` |
| Wo | der Ordner und die Kategorie **Extensions** und der Extensions-Index `/exphelp/extensions`, eine Extension unter `/exphelp/extensions/<name>` |
| Startseite | `doc/README.md` oder `doc/index.md`, sonst die `README` oben |

Um die Dokumentation einer **nicht** aktiven Extension zu zeigen, nennen Sie sie in `ExtensionDocDirs[]`:

```ini
[IndexSettings]
ExtensionDocDirs[]=myextension
```

Alle Regeln stehen in [Das Hilfesystem](../funktionen/6.0/hilfesystem.md#dokumentation-der-extensions).

## 6. Reihenfolge, Titel und Kontexthilfe

Eine Markdown-Seite kann mit einem Front-Matter-Block beginnen. Das Hilfecenter liest diese Schlüssel:

```markdown
---
title: Settings of MyExtension
summary: Every setting of myextension.ini with its default.
audience: developer, admin
order: 20
keywords: settings, cache
---
```

- `title` ersetzt die erste Überschrift der Seite in Listen und in der Seitenleiste; `summary` erscheint auf Karten
  und in Suchergebnissen.
- `order` ordnet die Seite unter ihren Geschwistern ein; ohne diesen Schlüssel wird nach dem Titel sortiert, hinter
  der `README.md` des Ordners.
- `audience` (editor, admin, developer ...) entscheidet, auf welchen Einstiegsseiten sie erscheint.

**Kontexthilfe:** Der „?"-Knopf der Seiten Ihres Moduls kann Ihre Seite öffnen. Ergänzen Sie in
`extension/<name>/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[mymodule]=ext/myextension/README.md
Map[mymodule/edit]=ext/myextension/settings.md#clearing-the-cache
```

## 7. Was nie gezeigt wird

- PHP, Templates, Skripte, Stylesheets, Einstellungen und jede andere Quelldatei, wo auch immer sie liegt
  (`ExcludePatterns[]` enthält `*.php`, `*.html` und `*.svg`, und andere Endungen sind keine Seitenendungen);
- eine Textdatei ohne Endung, die wie ein Programm beginnt (`#!` oder `<?`);
- alles der Extension außerhalb von `doc/` außer den Dateien oben;
- nicht eingecheckte Dateien, symbolische Links, Pfade mit `..` und Dateien über `MaxFileSize` (2 MB);
- die ganze Dokumentation einer Extension, die als bloße Kopie installiert ist, ohne ihr Repository und ohne
  `share/filelist.md5`: Das Hilfecenter kann ihre eigenen Dateien nicht von Dateien unterscheiden, die jemand dort
  abgelegt hat.

## 8. Prüfen

1. Checken Sie die Seiten im Repository der Extension ein.
2. Öffnen Sie **Hilfe > Extensions** (`/exphelp/extensions`): Ihre Extension steht dort mit dem Titel ihrer
   Startseite und der Zahl ihrer Seiten. `/exphelp/extensions/myextension` zeigt ihre Seiten nach Ordnern.
3. Prüfen Sie Links und Bilder Ihrer Seiten:

```bash
./console exp:help check --below=ext/myextension/
```

4. Öffnen Sie eine Seite über den „?"-Knopf Ihres Moduls, um die Kontexthilfe zu testen.

## 9. Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| Die Extension fehlt unter Extensions | Sie ist nicht aktiv, oder nichts von ihr ist eingecheckt. Aktivieren Sie sie oder tragen Sie sie in `ExtensionDocDirs[]` ein; checken Sie die Seiten ein. |
| Eine neue Seite fehlt | Sie ist noch nicht eingecheckt, oder der Index ist älter als der Commit: `./console exp:help index` ausführen. |
| Eine Seite erscheint als Klartext ohne Links | Sie hat keine Endung `.md`. Benennen Sie sie um ([Abschnitt 4](#4-ein-klartextdokument-umwandeln)). |
| Ein PDF- oder ODT-Handbuch fehlt | Office-Dokumente werden nicht gezeigt. Übertragen Sie ihren Inhalt in Markdown-Seiten. |
| Ein Bild erscheint nicht | SVG wird nie ausgeliefert; nutzen Sie WebP oder PNG neben der Seite und verlinken Sie es relativ. |
| Ein Link öffnet „nicht gefunden" | Das Ziel liegt außerhalb von `doc/` und ist keine Datei oben, oder der Pfad ist falsch. `exp:help check` nennt ihn. |
| Eine deutsche Seite erscheint als eigene Seite | Übersetzungen von Extension-Seiten werden nicht der englischen Seite zugeordnet. Behalten Sie eine englische Seite. |

## Verwandte Seiten

- [Exponential 6.0 erweitern](../EXTENDING.md): die Wege, Exponential zu erweitern, einschließlich der Hilfeseiten einer Extension.
- [Das Hilfesystem](../funktionen/6.0/hilfesystem.md): alle Regeln des Hilfecenters.
- [Extensions](extensions.md): eine Extension finden, installieren, bauen und veröffentlichen.
- [Ihre Erweiterung dokumentieren](../hilfe/erweiterungsentwicklung/teil-3-erweiterungsentwickler/ihre-erweiterung-dokumentieren.md),
  das Kapitel des Kurses zur Extension-Entwicklung.
