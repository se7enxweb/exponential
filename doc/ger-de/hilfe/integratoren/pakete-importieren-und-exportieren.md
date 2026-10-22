---
title: Pakete importieren und exportieren
audience: integrator, developer, admin
category: integration
order: 130
summary: Inhaltsklassen, Inhaltsteilbäume und Einstellungen mit Paketen zwischen Installationen übertragen - ezpm.php auf der Kommandozeile, das JSON-Paketformat für Werkzeuge, große Pakete installieren, Remote-IDs in Paketen und wann Pakete zu einer Integration passen.
keywords: Paket, ezpkg, ezpm.php, ezpm, Export, Import, installieren, hinzufügen, ezcontentobject, ezcontentsubtree, ezcontentclass, JSON-Paketformat, package.json, as-drafts, Remote-ID, Migration, Deployment, package, export, import, install, add, JSON package format, remote id, migration, deployment
type: help
---
# Pakete importieren und exportieren

Diese Seite ist für Integratoren, die **Struktur und Inhalte** zwischen Installationen übertragen: Inhaltsklassen von
einer Entwicklungs- auf eine Produktionswebsite, einen Beispielteilbaum für eine Demo, die Startinhalte einer neuen
Website oder einen anderswo vorbereiteten Migrationsstapel. Ein Paket (`.ezpkg`, ein gzip-komprimiertes Tar-Archiv)
kann Inhaltsklassen, Inhaltsobjekte mit ihren Dateien, Template-Overrides und Einstellungen enthalten. Die Seite der
Administration steht in [Pakete](../../anleitungen/pakete.md); auf der Kommandozeile ist es `ezpm.php`
([ezpm: der Paketmanager auf der Kommandozeile](../../funktionen/6.0/ezpm-paketmanager-cli.md)).

## Inhalt

1. [Wofür Pakete in einer Integration gut sind](#wofür-pakete-in-einer-integration-gut-sind)
2. [Einen Teilbaum exportieren](#einen-teilbaum-exportieren)
3. [Ein Paket installieren](#ein-paket-installieren)
4. [Das JSON-Paketformat für Werkzeuge](#das-json-paketformat-für-werkzeuge)
5. [Remote-IDs in Paketen](#remote-ids-in-paketen)
6. [Vorsicht](#vorsicht)

## Wofür Pakete in einer Integration gut sind

| Verwendung | Eignung |
|---|---|
| Die Inhaltsklassen, die Ihre Integration braucht, zusammen mit dem Release Ihrer Erweiterung ausliefern | gut: Klassen werden mit ihren Bezeichnern und Attributen installiert |
| Einen Teilbaum einmalig von einer Staging-Installation in die Produktion übertragen | gut |
| Eine neue Website mit Startinhalten befüllen | gut |
| Ein Werkzeug Inhalte offline erzeugen und als Datei übergeben lassen | gut mit dem JSON-Format |
| Eine tägliche Synchronisation sich ändernder Datensätze | schlecht: verwenden Sie stattdessen einen Import mit `remote_id` als Schlüssel ([wie](idempotente-importe-ueber-die-remote-id.md)) |
| Austausch mit einem System, das nicht Exponential ist | schlecht, es sei denn, dieses System schreibt das Paketformat |

## Einen Teilbaum exportieren

Führen Sie dies im Wurzelverzeichnis der Installation aus (als root mit `--allow-root-user`):

```bash
php ezpm.php create mypackage 'Starting content for the product area' 1.0.0 install
php ezpm.php add mypackage ezcontentsubtree /products --exclude-templates --language=eng-US
php ezpm.php export mypackage -d var/tmp
```

`add ezcontentobject` (Alias `ezcontentsubtree`) nimmt eine Knoten-ID oder einen URL-Pfad und die Optionen
`--include-classes`/`--exclude-classes`, `--include-templates`/`--exclude-templates`, `--node-main`/`--node-selected`,
`--siteaccess=`, `--language=`, `--current-version`/`--all-versions` und `--minimal-template-set`. Die Konsole kennt
dasselbe Werkzeug als `php bin/php/console ezpm`.

## Ein Paket installieren

```bash
php ezpm.php import var/tmp/mypackage-1.0.0-1.ezpkg        # into the package repository, with the archive checks
php ezpm.php install mypackage --destination-node-id 61    # objects below node 61 (default: node 2)
php bin/php/updatesearchindex.php --allow-root-user        # the installer delays indexing
php bin/php/ezcache.php --clear-all --allow-root-user
```

Was Sie über das Installationsprogramm wissen sollten:

- **Ein Lauf zur Zeit**: `install` und `add` setzen eine Sperre (`ezpm.lock` im var-Verzeichnis).
- **Commits in Stapeln** alle 50 Objekte, damit große Pakete nicht für den ganzen Lauf Sperren halten; ein
  fehlgeschlagenes Objekt wird mit seiner Remote-ID genannt.
- **Keine Duplikate**: Wiederholte Elemente werden einmal installiert.
- **Die Suchindizierung wird verzögert** während des Laufs: Bauen Sie den Index danach neu auf.
- **Abhängigkeiten zuerst**: Unter `requires` aufgeführte Pakete werden vor dem Paket installiert.
- **Nicht interaktive Läufe überspringen das zerstörerische Ersetzen von Klassen**, sodass eine unbeaufsichtigte
  Installation nie eine Klasse ersetzt.

## Das JSON-Paketformat für Werkzeuge

`ezpm.php export --format=json` schreibt dasselbe Paket als JSON (`package.json`, Dokumente als JsonML, SHA-256 jeder
Datei), und `install --as-drafts` installiert Objekte als Entwürfe zur Prüfung. `convert`, `validate` und `check`
arbeiten mit JSON-Paketen. Ein eigenes Werkzeug oder ein Skript auf Seite des Kunden kann ein Paket in diesem Format
erzeugen und es vor der Übergabe gegen `share/package/schema/package-v1.json` prüfen
([Das JSON-Paketformat](../../bc/6.0/package-json-format.md)).

## Remote-IDs in Paketen

Objekte in einem Paket tragen ihre Remote-IDs (ein Dokument pro Objekt heißt `object-<remote id>.xml`). Dadurch kann
ein Paket, das in mehreren Installationen installiert wird, Objekte erzeugen, die derselbe Import später mit
`eZContentObject::fetchByRemoteID()` findet, obwohl ihre Objekt-IDs sich unterscheiden.

## Vorsicht

- **Installieren Sie zuerst in eine Kopie.** Das Installationsprogramm schreibt Inhalte und Klassen.
- **Legen Sie nie Zugangsdaten in ein Paket.** Ein Paket ist eine Datei, die zwischen Installationen und Menschen
  wandert.
- **Upload-Grenzen.** Die Upload-Seite weist Archive mit Links oder Pfaden außerhalb des Pakets ab; bauen Sie Archive
  mit `ezpm export`, nicht von Hand.
- **Klassen sind der Vertrag Ihrer Integration.** Liefern Sie Klassenänderungen als neue Paketversion zusammen mit der
  Version der Erweiterung aus, die sie braucht, und schreiben Sie beides in die Release Notes.

## Verwandte Seiten

- [Pakete](../../anleitungen/pakete.md)
- [ezpm: der Paketmanager auf der Kommandozeile](../../funktionen/6.0/ezpm-paketmanager-cli.md)
- [In ein Paket hineinsehen, es vergleichen, einzelne Elemente importieren](../../funktionen/6.0/pakete-vergleichen-und-importieren.md)
- [Pakete und ezpkg für App-Ersteller](../ersteller/anwendungsentwickler/pakete-und-ezpkg.md)
- [Massendatenaustausch mit xrowextract](massendatenaustausch-mit-xrowextract.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
