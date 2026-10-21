---
title: ezinfo.php und extension.xml anheben
audience: developer, designer, app-creator
category: extension-development
order: 40
summary: Jedes Release hebt die Version in der Erweiterung - ezinfo.php, extension.xml und, wo vorhanden, composer.json oder filelist.md5 - auf genau die Nummer des Releases an, in dem Commit, auf den der Tag zeigt, nie danach.
keywords: Version anheben, Versionssprung, Release-Commit, Tag, Seite Über, Metadaten, version bump, ezinfo.php, extension.xml, composer.json, release commit, about page, metadata, php -l, xmllint
type: help
---
# ezinfo.php und extension.xml anheben

Teil 7, Kapitel 4. Die Seite „Über“ der Administration, die Upgrade-Prüfungen und alle, die sich eine installierte
Kopie ansehen, lesen die Version aus Dateien in der Erweiterung, nicht aus Git. Deshalb schreibt jedes Release seine
Nummer in diese Dateien, in dem Commit, auf den der Tag zeigt. Dieses Kapitel zeigt die Schritte.

## Die Regel

| Datei | Feld | Wert für den Tag `v1.2.4` |
|---|---|---|
| `ezinfo.php` | `'Version'` | `"1.2.4"` |
| `extension.xml` | `<version>` (in `<metadata>`, falls die Datei eines hat) | `1.2.4` |
| `composer.json` | `"version"`, nur falls die Datei eines hat | `"1.2.4"` |
| `share/filelist.md5` | ihr Versionskopf, nur falls die Erweiterung eine mitliefert | `1.2.4` |

Kein `v`, kein `dev-master`, kein `//autogentag//`. Die Seite „Über“ des Kernels liest zuerst `extension.xml`, daher
müssen beide Dateien übereinstimmen
([Spezifikation: Metadaten von Extensions](../../../spezifikationen/6.0/extension-metadaten.md)).

## Die Reihenfolge

1. Wählen Sie die Nummer ([Die nächste Versionsnummer wählen](die-naechste-version-waehlen.md)).
2. Ändern Sie die Felder.
3. Committen Sie: `Updated: Version 1.2.4`, oder nehmen Sie das Anheben in den letzten Commit des Releases auf.
4. Prüfen Sie (siehe unten).
5. Taggen Sie diesen Commit und pushen Sie ihn ([Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md)).

Taggen Sie nie zuerst und heben Sie danach an: Der Tag trüge in seinen Dateien für immer die alte Nummer.

Ein echtes Beispiel, se7enxweb/expchangeclass Commit 32196f0, hat genau drei Zeilen geändert:

```text
composer.json   "php": "^8.1 || ..."     ->  "php": "^8.0 || ^8.1 || ..."
extension.xml   <version>1.0.5</version>  ->  <version>1.0.6</version>
ezinfo.php      'Version' => "1.0.5",     ->  'Version' => "1.0.6",
```

(Die Zeile in `composer.json` war die Änderung, um die es in dem Release ging; diese Datei hat kein eigenes
Versionsfeld.)

## Prüfen

```bash
php -l ezinfo.php
xmllint --noout extension.xml
grep -n "Version\|<version>" ezinfo.php extension.xml
```

Erwartet: „No syntax errors detected“, keine Ausgabe von `xmllint`, zweimal dieselbe neue Nummer.

Nachdem Sie das Release auf einer Test-Website installiert haben, öffnen Sie die Seite „Über“ der Administration (die
View `ezinfo/about`) und suchen Sie die Zeile Ihrer Erweiterung: Die angezeigte Version muss die neue sein.

## Die anderen Felder

Wenn Sie schon in den Dateien sind, prüfen Sie, ob der Rest noch stimmt: der Name, die Copyright-Jahre, die Lizenz
(`GNU General Public License v2.0 (or any later version)` oder genau das, was Ihre Lizenzdatei sagt) und `Info_url`,
die auf das Repository zeigt. Sie werden in
[ezinfo.php und extension.xml modernisieren](../teil-4-aktualisieren/ezinfo-und-extension-xml-modernisieren.md)
behandelt.

## Erweiterungen, die mit dem Kernel ausgeliefert werden

Erweiterungen, die im Repository des Kernels liegen (ezjscore, ezoe, ezformtoken und andere), tragen die Version des
Exponential-Releases, mit dem sie ausgeliefert werden, keine eigene Nummerierung.

## Verwandte Seiten

- Zurück: [Tags und Releases sind dauerhaft](tags-sind-dauerhaft.md)
- Weiter: [Composer und Packagist](composer-und-packagist.md)
- [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md), [extension.xml](../teil-3-erweiterungsentwickler/extension-xml.md)
- [filelist.md5 und die Upgrade-Prüfung](filelist-md5-und-die-upgrade-pruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
