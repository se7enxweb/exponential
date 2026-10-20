---
title: Klassen und Inhalte ändern
audience: developer, designer, website-creator
category: install-upgrade
order: 88
summary: Wo die Inhaltsklassen und Demo-Inhalte eines Seitenpakets liegen, wie ihre XML-Dateien als Installationselemente aufgeführt werden, die zwei Wege, sie zu ändern (die Dateien bearbeiten oder auf einer Testwebsite bauen und exportieren), warum Remote-IDs stabil bleiben müssen und was gar nicht in den Paketen ist.
keywords: Inhaltsklassen, Demo-Inhalte, ezcontentclass, ezcontentobject, Installationselement, Klassen-XML, Objekt-XML, contentobjects.xml, Remote-ID, Export, ezpm add, Paketassistent, sevenx_classes, democontent, Layouts, content classes, demo content, install item, class XML, object XML, remote ID, export, package wizard, layouts
type: help
---
# Klassen und Inhalte ändern

Diese Seite ist für Entwickler und Website-Bauer, die wollen, dass ihr eigenes Seitenpaket andere Inhaltsklassen oder andere Demo-Inhalte
installiert als das Paket, das sie geforkt haben. Sie zeigt, wo diese liegen, wie Sie sie ändern und was Sie stabil halten, damit das
Installerskript weiterhin findet, was es erwartet.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, das fertige Paket per Pull Request an
se7enxweb/exponential.packages.exponential.earth zu teilen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Wo Klassen und Inhalte liegen](#wo-klassen-und-inhalte-liegen)
2. [Wie die Dateien aufgeführt werden](#wie-die-dateien-aufgeführt-werden)
3. [Weg 1: die Dateien bearbeiten](#weg-1-die-dateien-bearbeiten)
4. [Weg 2: auf einer Testwebsite bauen und exportieren](#weg-2-auf-einer-testwebsite-bauen-und-exportieren)
5. [Remote-IDs stabil halten](#remote-ids-stabil-halten)
6. [Was nicht in den Paketen ist](#was-nicht-in-den-paketen-ist)
7. [Vor dem Neubau](#vor-dem-neubau)

## Wo Klassen und Inhalte liegen

Ein Seitenpaket selbst bringt keine Klassen und keine Inhalte mit. Es verlangt Pakete, die das tun:

| Paket (7x) | Typ | Enthält |
|---|---|---|
| `sevenx_classes` | `contentclass` | `ezcontentclass/class-<identifier>.xml`, eine Datei pro Klasse (33 Dateien im Quell-Repository) |
| `sevenx_multisite_democontent` | `contentobject` | eigene Klassen in `ezcontentclass/`, die Objekte in `ezcontentobject/` (`contentobjects.xml` plus eine `object-*.xml` pro Objekt), Binärdateien in `simplefiles/` |
| `sevenx_multisite_democontent_clean` | `contentobject` | die Inhalte der Varianten ohne Demo-Artikel |

Ein Fork behält meist das Klassenpaket und ersetzt das Inhaltspaket, wie `cjw_multisite` es mit `cjw_multisite_democontent` tut. Siehe
[Die Demo-Inhaltspakete](../sevenx-multisite/die-demo-inhaltspakete.md) und [sevenx_classes](../sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md).

## Wie die Dateien aufgeführt werden

Nichts in einem Paket wird installiert, weil es im Ordner liegt; nur die Installationselemente der `package.xml` werden es:

```xml
<install>
  <item type="ezcontentclass" filename="class-article" sub-directory="ezcontentclass"/>
  <item type="ezcontentclass" filename="class-folder" sub-directory="ezcontentclass"/>
  <item type="ezcontentobject" filename="contentobjects" sub-directory="ezcontentobject"/>
</install>
```

`type` wählt den Handler (`package.ini [PackageSettings] HandlerAlias[]`, zum Beispiel `ezcontentclass` = `eZContentClassPackageHandler`),
`filename` ist die Datei ohne `.xml`, `sub-directory` der Ordner. Eine Klassendatei, die Sie hinzufügen, braucht ein eigenes `<item>`; bei
einer Klassendatei, die Sie entfernen, muss ihr `<item>` entfernt werden. Siehe [Installationselemente und Handler](../grundlagen/installationselemente-und-handler.md).

## Weg 1: die Dateien bearbeiten

Gut für kleine Änderungen: einen Klassennamen, die Beschreibung eines Attributs, einen Text in einem Objekt.

```bash
cd packages/acme_classes/ezcontentclass
xmllint --noout class-article.xml     # well formed after your edit
```

Eine Klassendatei enthält den Bezeichner, die serialisierten Namen pro Sprache, die Remote-ID, die Attribute mit Datentyp und Einstellungen
und einen Block `<remote>`, der beschreibt, woher sie exportiert wurde. Behalten Sie die Struktur bei, die der Kernel geschrieben hat; ändern
Sie Werte, keine Elementnamen.

## Weg 2: auf einer Testwebsite bauen und exportieren

Gut für neue Klassen, neue Attribute und neue Inhalte: Modellieren Sie es in der Administration einer Testinstallation (einer, die aus Ihrem
aktuellen Paket installiert wurde), exportieren Sie es dann und kopieren Sie die Dateien in Ihr Quell-Repository.

```bash
# classes by identifier, comma separated
php ezpm.php create acme_classes_export 'Classes export' 1.0.0 install
php ezpm.php add acme_classes_export ezcontentclass article,folder
php ezpm.php export acme_classes_export -d var/tmp

# a content subtree, without its classes
php ezpm.php create acme_content_export 'Content export' 1.0.0 install
php ezpm.php add acme_content_export ezcontentobject /content/demo --exclude-classes
php ezpm.php export acme_content_export -d var/tmp
```

Dieselben Exporte stehen unter **Setup > Pakete > Neues Paket erstellen** (Export von Inhaltsklassen, Export von Inhaltsobjekten):
[Pakete mit dem Assistenten erstellen](pakete-mit-dem-assistenten-erstellen.md). Entpacken Sie dann das Archiv und kopieren Sie die Klassen- oder
Objektdateien und die Zeilen `<item>` seiner `package.xml` in Ihren Paketordner:

```bash
mkdir -p var/tmp/unpacked && tar -xzf var/tmp/acme_classes_export-*.ezpkg -C var/tmp/unpacked
```

`ezpm export` nennt das Archiv `<name>-<version>-<release>.ezpkg` (`eZPackage::exportName()`); die Optionen stehen in
[Pakete mit ezpm erstellen](pakete-mit-ezpm-erstellen.md).

> **Tipp**
> Bevor Sie ein geändertes Inhaltspaket irgendwo installieren, führt `php ezpm.php check <package>` auf, was die Installation auf dieser
> Installation vorfinden würde, nach Remote-ID, und schreibt nichts.

## Remote-IDs stabil halten

Pakete finden Dinge über die Remote-ID. Der Klassenhandler sucht eine vorhandene Klasse mit `eZContentClass::fetchByRemoteID()` und fragt,
was zu tun ist, wenn sie existiert; die Installation von Inhalten ordnet Objekte nach Remote-ID in neu, vorhanden und im Konflikt ein
([In ein Paket hineinsehen, es mit Ihrer Website vergleichen](../../../../en/us/features/6.0/package-compare-and-import.md)). Auch der Installer von 7x
verlässt sich auf feste Remote-IDs: `mainSiteaccessHomeRemoteID()` gibt die Remote-ID des Startknotens der Hauptwebsite zurück, wie sie das
Demo-Inhaltspaket mitbringt.

Also:

- Ändern Sie nicht die Remote-ID eines Objekts oder einer Klasse, auf die sich das Installerskript bezieht; suchen Sie im Installer nach der
  ID, bevor Sie sie anfassen.
- Geben Sie neuen Objekten und Klassen neue Remote-IDs (der Export von einer Testwebsite erledigt das für Sie).
- Ein Fork, der neben den Inhalten von 7x installiert wird, sollte Remote-IDs von 7x nicht für andere Dinge wiederverwenden.

## Was nicht in den Paketen ist

| Daten | Woher sie kommen |
|---|---|
| Layouts, Zonen, Blöcke und Layoutregeln (Tabellen `explayouts_*`) | `extension/sevenx_themes_media/share/db_data.dba` und `extension/explayouts/share/`, vom Installer geladen ([Layoutdaten und share-DBA-Dateien](../sevenx-multisite/layoutdaten-und-share-dba-dateien.md)) |
| Tags und Sternebewertungen der Demo-Inhalte | `share/db_data.dba` von `sevenx_themes_media`, im selben Schritt |
| Einstellungen, Siteaccesses, Rollen | das Installerskript ([Das Installerskript ändern](das-installerskript-aendern.md)) |

Eine Demo-Seite, die auf der Website von 7x ihre Blöcke zeigt, braucht ihr Layout auch in diesen Daten der Erweiterung; nur das
Inhaltspaket zu ändern lässt die Seite ohne sie.

## Vor dem Neubau

- [ ] Jede neue Klassen- oder Objektdatei hat ihr `<item>`, jede entfernte hat es verloren.
- [ ] `xmllint --noout` besteht für jede geänderte XML-Datei.
- [ ] Keine Backups von Editoren oder andere Reste in den Ordnern: `build.sh` packt jede Datei außer den Namen, die es ausschließt
      ([Mit build.sh neu bauen](mit-build-sh-neu-bauen.md)).
- [ ] Remote-IDs, die der Installer nutzt, sind unverändert.
- [ ] Version und Changelog des Pakets sind erhöht ([Eigene Pakete versionieren](eigene-pakete-versionieren.md)).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Installationselemente und Handler](../grundlagen/installationselemente-und-handler.md)
- [Pakete mit Inhaltsklassen bündeln](../../../../en/us/help/designers/extensions/bundling-content-class-packages.md)
- [Inhaltsklassen für Websites](../../../../en/us/help/creators/website-creators/content-classes-for-websites.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
