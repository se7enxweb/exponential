---
title: "Referenz zu package.xml"
audience: developer, admin
category: install-upgrade
order: 13
summary: Jedes Element und Attribut von package.xml, wie eZPackage es liest (parseDOMTree) und schreibt (domStructure), mit dem, was erforderlich ist, was geprüft wird, was ignoriert wird und was verloren geht, wenn der Kernel ein Paket zurückschreibt.
keywords: package.xml, eZPackage, parseDOMTree, domStructure, install_type, type, vendor, version, release, licence, state, dependencies, requires, settings-file, install, uninstall, files, simple-files, changelog, maintainers, metadata, install-data, Referenz, Elemente, Attribute, reference, elements, attributes
type: help
---
# Referenz zu package.xml

Diese Seite ist für Entwickler, die eine `package.xml` von Hand schreiben oder ändern. Sie führt jedes Element und Attribut auf, das
`eZPackage` versteht, wie es jedes liest (`eZPackage::parseDOMTree()`), wie es jedes zurückschreibt (`eZPackage::domStructure()`) und die
Fallen zwischen beidem. Alle Verweise beziehen sich auf `kernel/classes/ezpackage.php`, sofern keine andere Datei genannt ist.

## Inhalt

1. [Wie die Datei gelesen wird](#wie-die-datei-gelesen-wird)
2. [Das Wurzelelement](#das-wurzelelement)
3. [Beschreibende Elemente](#beschreibende-elemente)
4. [Personen, Paketierung und Geschichte](#personen-paketierung-und-geschichte)
5. [Release-Elemente](#release-elemente)
6. [Dateien](#dateien)
7. [Abhängigkeiten](#abhängigkeiten)
8. [Installationsskripte: settings](#installationsskripte-settings)
9. [Installations- und Deinstallationselemente](#installations--und-deinstallationselemente)
10. [Metadaten und Installationsdaten](#metadaten-und-installationsdaten)
11. [Was verloren geht, wenn der Kernel ein Paket schreibt](#was-verloren-geht-wenn-der-kernel-ein-paket-schreibt)
12. [Verwandte Seiten](#verwandte-seiten)

## Wie die Datei gelesen wird

- Der Leser sucht die meisten Elemente mit `getElementsByTagName()`, das ein Element **irgendwo** unterhalb der Wurzel findet und das erste
  nimmt. Lassen Sie jedes Element dort, wo der Schreiber es hinsetzt; verwenden Sie einen Elementnamen nicht innerhalb eines anderen
  Elements wieder (die eine Ausnahme, die der Leser behandelt, ist `<version>`, das mit XPath als direktes Kind von `<package>` gelesen
  wird, sodass `<ezpublish><version>` nicht im Weg ist).
- Eine Definition **ohne Wurzelelement oder ohne nicht leeren `<name>`** ist kein Paket: Sie wird nicht gelesen, und die Paketlisten
  überspringen sie.
- `<version><number>`, `<version><release>` und `<type value>` müssen jeweils fehlen, leer sein oder ein einfaches Token aus 1 bis 64 Zeichen
  aus `0-9 A-Z a-z . _ + -` sein (`isValidDefinitionToken()`). Alles andere, und die ganze Definition wird abgewiesen.
- Ein JSON-Paket (`package.json`) wird zuerst in dasselbe DOM umgewandelt, sodass alles Folgende für beide Formate gilt.

## Das Wurzelelement

```xml
<package version="3.5.2" development="false" install_type="install">
```

| Attribut | Gelesen | Geschrieben |
|---|---|---|
| `install_type` | `install` (Standard) oder `import`; siehe [Importieren oder installieren](importieren-oder-installieren.md) | immer |
| `development` | `"true"` bedeutet Entwicklung, alles andere nicht | immer, `false` |
| `version` | nicht gelesen | immer `eZPackage::VERSION`, `3.5.2` (die Formatversion, nicht die Paketversion) |
| andere Attribute (die Seitenpakete von 7x tragen `is_active="true"`) | nicht gelesen | nicht geschrieben |

## Beschreibende Elemente

| Element | Erforderlich | Hinweise |
|---|---|---|
| `<name>` | **ja** | Der Paketname. Beim Import muss er `eZPackage::isValidName()` bestehen (die Transformation `identifier` muss ihn unverändert lassen), sonst `STATUS_INVALID_NAME`. Er ist auch der Verzeichnisname im Repository |
| `<summary>` | nein | Eine Zeile; der Setup-Assistent zeigt sie als Titel der Website, und `[site_details] Title` ist standardmäßig dieser Wert |
| `<description>` | nein | Längerer Text |
| `<vendor>` | nein | Wählt das Repository-Verzeichnis: der Hersteller, durch die Transformation `urlalias` geleitet (`7x` bleibt `7x`, `eZ systems` wird zu `ez-systems`); ohne Hersteller geht das Paket nach `local` |
| `<type value="..."/>` | nein | Der Pakettyp, ohne umgebende Leerzeichen; siehe [Pakettypen](pakettypen.md) |
| `<priority value="..."/>` | nein | Behalten und in `eZPackage::fetchPackages()` filterbar; nichts im Installer nutzt ihn |
| `<extension name="..."/>` | nein | Die Erweiterung, zu der ein Paket gehört; in `fetchPackages()` filterbar |
| `<source>` | nein | Freier Text |
| `<ezpublish><version>`, `<named-version>` | nein | Die Version von Exponential, mit der das Paket gemacht wurde (`6.0.15-stable`, `6.0` in sevenx_multisite). Zur Information behalten: Der Paketcode vergleicht sie nicht mit der laufenden Version. Der Elementname ist historisch und bleibt, wie er ist |

## Personen, Paketierung und Geschichte

```xml
<maintainers>
  <maintainer><name>...</name><email>...</email><role>lead</role></maintainer>
</maintainers>
<packaging><timestamp>1705211779</timestamp><host>...</host><packager>...</packager></packaging>
<documents><document mime-type="text/plain" name="LICENCE" os="" audience=""/></documents>
<groups><group name="..."/></groups>
<changelog>
  <entry timestamp="..." person="..." email="..." release="1"><change>Creation of package.</change></entry>
</changelog>
```

| Element | Hinweise |
|---|---|
| `<maintainer>` | `name`, `email`, wahlweise `role`; die angebotenen Rollen sind `[MaintainerSettings] RoleList[]`: `lead`, `developer`, `designer`, `contributor`, `tester` |
| `<packaging>` | Wann und wo das Archiv gemacht wurde. Ohne es liest sich das Paket als nicht paketiert (keine Zeit, kein Host) |
| `<document>` | Eine Datei in `documents/`; `name` und `mime-type`, wahlweise `os` und `audience` |
| `<group>` | Freie Gruppennamen |
| `<entry>` | Einer pro Release, Attribute `timestamp`, `person`, `email`, `release`, ein `<change>` pro Änderung |

Was ein veröffentlichtes Paket ausfüllen muss, steht in [Anforderungen an die Dokumentation](../anforderungen/README.md).

## Release-Elemente

```xml
<version><number>6.0</number><release>15-stable</release></version>
<timestamp>1705211779</timestamp>
<licence>GPL</licence>
<state>stable</state>
```

| Element | Hinweise |
|---|---|
| `<version><number>` | Die Versionsnummer, ohne umgebende Leerzeichen. Neue Pakete aus den Assistenten bekommen eine strikte Nummer nach SemVer 2.0.0; das Lesen bleibt tolerant (`kernel/classes/ezpackageversion.php`) |
| `<version><release>` | Die Release-Nummer, ohne umgebende Leerzeichen. `eZPackage::getVersion()` verbindet sie als `<number>-<release>`; mit dieser Zeichenkette wird `min-version` verglichen, und sie vermerkt die Tabelle `ezpackage` |
| `<timestamp>` | Der Zeitpunkt des Release; nur ein `<timestamp>`, das direktes Kind von `<package>` ist, zählt |
| `<licence>` | Der Lizenzbezeichner; abgeglichen mit `package.ini [LicenseSettings]` (`AliasList[GPL]=GPL-2.0-or-later` macht `GPL` bekannt). Ein unbekannter Wert wird so gezeigt, wie er ist. Das Element behält seine historische Schreibweise |
| `<state>` | Einer aus `[PackageSettings] StateList[]`: `mature`, `stable`, `beta`, `alpha`, `prealpha` |

## Dateien

```xml
<simple-files>
  <simple-file key="..." original-path="..." package-path="simplefiles/24ec46f7.jpg"/>
</simple-files>
<files>
  <collection name="default">
    <file-list type="thumbnail" variable-name="thumbnail">
      <file name="thumbnail.png" md5sum="77a7bc0cc6e1e5138ca7a6e39a130307"/>
    </file-list>
  </collection>
</files>
```

| Element | Attribute |
|---|---|
| `<simple-file>` | `key`, `original-path`, `package-path` (unter `simplefiles/`) |
| `<collection>` | `name`; ein `<files>` ohne `<collection>` wird als eine Sammlung gelesen, die nach seinem eigenen `name` benannt ist |
| `<file-list>` | `type` (`file`, `design`, `ini`, `thumbnail`, `image` ...), `design`, `role`, `role-value`, `variable-name` |
| `<file>` | `name`, `sub-directory`, `path` (woher sie kam), `md5sum`, `type` (`dir` für ein Verzeichnis), `variable-name` |

Die erste Datei `thumbnail` der Sammlung `default` ist das Bild, das der Setup-Assistent für ein Seitenpaket zeigt (`thumbnail-list`).

## Abhängigkeiten

```xml
<dependencies>
  <provides/>
  <requires>
    <require type="ezpackage" name="sevenx_classes" min-version="5.1"/>
  </requires>
  <obsoletes/>
  <conflicts/>
</dependencies>
```

Vier Abschnitte mit den Elementen `<provide>`, `<require>`, `<obsolete>` und `<conflict>`. Der Leser behält jedes Attribut eines Elements.
Der Installer handelt nach `<require type="ezpackage">` mit `name` und `min-version`; siehe
[Abhängigkeiten und die Installationsreihenfolge](abhaengigkeiten-und-die-installationsreihenfolge.md). Ein Designpaket von 7x gibt
`<provide type="ezfile" name="collection" value="default"/>` an.

## Installationsskripte: settings

```xml
<settings>
  <settings-file filename="install-scripts.php"/>
</settings>
```

Jeder `filename` ist eine PHP-Datei im Verzeichnis `settings/` des Pakets, die der Schritt zum Anlegen der Websites einbindet. Siehe
[Installationsskripte in einem Seitenpaket](installationsskripte-in-einem-seitenpaket.md).

## Installations- und Deinstallationselemente

```xml
<install>
  <item type="ezcontentclass" filename="class-article" sub-directory="ezcontentclass"/>
  <item type="ezfile" collection="default"/>
</install>
<uninstall>
  <item type="ezcontentclass" filename="class-article" sub-directory="ezcontentclass"/>
</uninstall>
```

| Attribut | Bedeutung |
|---|---|
| `type` | Der Handler, nachgeschlagen in `[PackageSettings] HandlerAlias[]` |
| `filename`, `sub-directory` | Das Dokument des Elements: `<sub-directory>/<filename>.xml` (oder `.json`) |
| `name`, `os` | Wahlweise; `os` beschränkt ein Element in `installItemsList()` auf ein Betriebssystem |
| Attribute des Handlers | Vom Handler gelesen: `collection` (`ezfile`), `original-path` und `database-type` (`ezsql`) |

Was jeder Typ tut, steht in [Installationselemente und Handler](installationselemente-und-handler.md).

## Metadaten und Installationsdaten

- `<metadata><entry key="expai.provenance">value</entry></metadata>`: freie Zeichenketten, die Werkzeuge mit einem Paket aufbewahren, nur
  geschrieben, wenn es welche gibt (`eZPackage::setMetadata()`).
- `<install-data><data type="..."><element name="" value=""/><array name="">...</array></data></install-data>`: was sich Handler zwischen
  Installation und Deinstallation merken; vom Kernel geschrieben, nicht von Hand.

## Was verloren geht, wenn der Kernel ein Paket schreibt

`domStructure()` schreibt die Elemente in einer festen Reihenfolge (name, summary, description, vendor, priority, type, extension, source,
ezpublish, maintainers, packaging, documents, groups, changelog, simple-files, files, version, timestamp, licence, state, dependencies,
install, uninstall, install-data, metadata). Behalten Sie diese Unterschiede im Kopf, bevor Sie den Kernel eine von Hand erstellte Datei
neu schreiben lassen:

> **Hinweis**
> In 6.0.15 behoben: `domStructure()` schreibt `<settings>` (nach `<dependencies>`) und jedes Attribut eines Abhängigkeitselements. Vorher
> verlor ein vom Kernel erneut gespeichertes Seitenpaket die Liste seiner Installationsskripte und die `min-version` seiner Elemente
> `<require>`. Die `package.xml` eines Seitenpakets in seinem Quell-Repository zu bearbeiten, bleibt der klarste Weg, es zu ändern.

- Das Wurzelattribut `version` wird zu `3.5.2`; Attribute, die der Kernel nicht kennt, werden weggelassen.
- `<files>` wird weggelassen, wenn es keine Datei gibt.
- `<settings>` wird weggelassen, wenn das Paket keine Einstellungsdatei hat.
- Ein Abhängigkeitselement wird mit `type`, `name`, `value` (wenn nicht leer) und, ab 6.0.15, jedem anderen Attribut geschrieben, mit dem es
  gelesen wurde (`min-version`, `max-version` ...). Vor 6.0.15 wurden diese weggelassen, sodass ein gespeichertes Seitenpaket seine
  Versionsanforderungen verlor.

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Pflichtfelder in package.xml](../anforderungen/pflichtfelder-in-package-xml.md)
- [Versionsnummern](../anforderungen/versionsnummern.md)
- [Paketlizenzen und Semantic Versioning](../../../funktionen/6.0/paketlizenzen-und-versionen.md)
- [Das JSON-Paketformat](../../../bc/6.0/package-json-format.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
