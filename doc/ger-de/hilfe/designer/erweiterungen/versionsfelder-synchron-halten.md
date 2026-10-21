---
title: "Alle Versionsfelder synchron halten"
audience: designer, developer
category: theme-extensions
order: 70
summary: Die Versionsnummer eines Themes steht an bis zu fünf Stellen; die Liste, die Reihenfolge, in der Sie sie vor einem Release ändern, und eine einminütige Prüfung, dass sie übereinstimmen.
keywords: Version, Versionsnummer, ezinfo.php, extension.xml, composer.json, filelist.md5, Tag, Release, synchron, Konsistenz, version, version number, release, sync, consistency
type: help
---
# Alle Versionsfelder synchron halten

Diese Seite ist für alle, die ein Theme veröffentlichen. Installationen, die Seite Über, Packagist und Ihre Nutzer lesen
die Version aus verschiedenen Dateien. Stimmen die Dateien nicht überein, kann niemand sagen, welches Release installiert
ist.

## Wo die Version steht

| Ort | Form | Pflicht |
|---|---|---|
| `ezinfo.php` `'Version'` | `1.0.4` | ja |
| `extension.xml` `<version>` | `1.0.4` | ja |
| `composer.json` `version` | `1.0.4` | nur wenn Sie das Feld führen (Packagist liest den Tag, wenn es fehlt) |
| Kopf von `share/filelist.md5` | so, wie die Datei es angibt | nur wenn das Theme eine mitliefert ([Datei-Prüfsummen](../veroeffentlichung/filelist-pruefsummen.md)) |
| Der Git-Tag | `v1.0.4` (oder `1.0.4`, wenn Ihr Repository ohne `v` taggt) | ja, bei jedem Release |

## Die Reihenfolge

1. **Wählen Sie die Nummer** aus dem, was schon veröffentlicht ist, nie aus dem Gedächtnis
   ([Prüfen, was schon veröffentlicht ist](../veroeffentlichung/pruefen-was-schon-veroeffentlicht-ist.md)).
2. **Schreiben Sie sie in jede Datei** der Tabelle.
3. **Committen Sie** („Updated: Version 1.0.4“ oder als Teil des letzten Commits des Releases).
4. **Taggen Sie diesen Commit** und pushen Sie beides.

Taggen Sie nie zuerst und erhöhen danach: Der Tag trüge die alte Nummer, und ein gepushter Tag ist endgültig
([Ein gepushter Tag ist endgültig](../veroeffentlichung/ein-gepushter-tag-ist-endgueltig.md)).

## Die einminütige Prüfung

Führen Sie im Ordner des Themes aus:

```bash
grep -n "'Version'" ezinfo.php
grep -n '<version>' extension.xml
grep -n '"version"' composer.json
php -l ezinfo.php
xmllint --noout extension.xml
```

Erwartet: dieselbe Nummer bei den ersten beiden (und beim dritten, falls vorhanden), `No syntax errors detected in
ezinfo.php` und keine Ausgabe von `xmllint`.

## Wenn sie schon nicht übereinstimmen

- Wurde mit den falschen Nummern noch nichts veröffentlicht: Korrigieren Sie die Dateien und veröffentlichen Sie dann.
- Ist ein Release mit nicht übereinstimmenden Dateien hinausgegangen: Verschieben Sie seinen Tag nicht. Veröffentlichen
  Sie die nächste Version mit korrigierten Dateien und sagen Sie es in deren Notes
  ([Wenn ein Release schiefgeht](../veroeffentlichung/wenn-ein-release-schiefgeht.md)).

## Verwandte Seiten

- [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md)
- [Die Version vor dem Tag erhöhen](../veroeffentlichung/version-vor-dem-tag-erhoehen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
