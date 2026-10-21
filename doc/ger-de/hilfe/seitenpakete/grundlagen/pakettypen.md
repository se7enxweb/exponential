---
title: "Pakettypen"
audience: admin, developer, designer
category: install-upgrade
order: 14
summary: Die Pakettypen, die Exponential kennt (site, sitestyle, contentclass, contentobject, extension), wo der Typ angegeben wird, wofür jeder gedacht ist, welche Typen der Index von 6.0.15 tatsächlich nutzt und was der Typ im Installer, in den Assistenten und in den Paketrichtlinien ändert.
keywords: Pakettyp, type value, TypeList, site, sitestyle, contentclass, contentobject, extension, design, Paketrichtlinie, Einschränkung Type, CreationSettings, index.xml, package type, package policy, Type limitation
type: help
---
# Pakettypen

Diese Seite ist für Administratoren und Entwickler, die wissen müssen, welche Art von Paket sie vor sich haben. Sie beantwortet: welche
Typen es gibt, wo der Typ geschrieben wird, welche Typen der Paketserver anbietet und was der Typ ändert.

## Inhalt

1. [Wo der Typ angegeben wird](#wo-der-typ-angegeben-wird)
2. [Die Typen auf einen Blick](#die-typen-auf-einen-blick)
3. [Die Typen im Index von 6.0.15](#die-typen-im-index-von-6015)
4. [TypeList und wofür es genutzt wird](#typelist-und-wofür-es-genutzt-wird)
5. [Was der Typ ändert](#was-der-typ-ändert)
6. [Typ, Installationstyp und Installationselemente sind drei Dinge](#typ-installationstyp-und-installationselemente-sind-drei-dinge)
7. [Verwandte Seiten](#verwandte-seiten)

## Wo der Typ angegeben wird

An zwei Stellen, die übereinstimmen müssen:

```xml
<!-- package.xml -->
<type value="site" />
```

```xml
<!-- index.xml on the package server -->
<package name="sevenx_multisite" version="6.0-15-stable" type="site" ... />
```

Der Kernel liest den Wert von `<type>` ohne umgebende Leerzeichen (`eZPackage::parseDOMTree()`) und weist die Definition ab, wenn der Wert
kein einfaches Token aus Buchstaben, Ziffern und `._+-` ist. Der Setup-Assistent liest das Attribut `type` des Index, um zu entscheiden,
welche entfernten Pakete Seitenpakete sind (`eZStepSiteTypes::retrieveRemotePackagesList()`).

## Die Typen auf einen Blick

| Typ | Enthält | Erstellt durch | Typische Installationselemente | Beispiele |
|---|---|---|---|---|
| `site` | Eine Startseite: Anforderungen und Installationsskripte | von Hand, in einem Quell-Repository | keine (`install_type="import"`) | `sevenx_multisite`, `cjw_multisite`, `plain_site` |
| `sitestyle` | Ein Farbschema oder Design: CSS, Bilder, Vorschaubild | den Assistenten für Seitenstile (Ersteller `ezstyle`) | `ezfile` | `sevenx_design_blue`, `ezflow_design` |
| `contentclass` | Definitionen von Inhaltsklassen | den Assistenten für Inhaltsklassen (Ersteller `ezcontentclass`) | `ezcontentclass` | `sevenx_classes`, `ezwebin_classes` |
| `contentobject` | Inhalte, oft mit den Klassen, die sie brauchen | den Assistenten für Inhaltsobjekte, `ezpm add ... ezcontentobject` | `ezcontentobject`, manchmal `ezcontentclass` | `sevenx_multisite_democontent`, `sevenx_banners` |
| `extension` | Eine oder mehrere Erweiterungen als Dateien | den Assistenten für den Export von Erweiterungen (Ersteller `ezextension`) | `ezextension` | `sevenx_extension`, `ezstarrating_extension` |

Die Ersteller und ihre Pakettypen stehen in `kernel/classes/packagecreators/*/` (`packageType()`), aufgeführt in
`settings/package.ini [CreationSettings] HandlerList[]`: `ezcontentobject`, `ezcontentclass`, `ezextension`, `ezstyle`.

> **Hinweis**
> Es gibt keinen Pakettyp `design`. Designdateien reisen in Paketen `sitestyle` oder innerhalb einer Erweiterung. Das Wort `design` kommt
> in `package.xml` zwar vor, aber als Typ einer `<file-list>` (Dateien, die unter `design/<design>/` gehören), nicht als Pakettyp.

## Die Typen im Index von 6.0.15

Die 44 Einträge von `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml`, gezählt nach ihrem Attribut `type`:

| Typ | Einträge |
|---|---|
| `site` | 12 |
| `contentobject` | 13 |
| `extension` | 8 |
| `sitestyle` | 7 |
| `contentclass` | 4 |

Der Index gruppiert die Pakete, die keine Seitenpakete sind, unter dem Kommentar `DEPENDENCY PACKAGE LIST`: Sie werden nur als
Anforderungen eines Seitenpakets angeboten, nie als eigene Wahl. Die vollständige Liste steht in
[Was auf dem Paketserver liegt](../paketserver/was-auf-dem-paketserver-liegt.md).

## TypeList und wofür es genutzt wird

```ini
[PackageSettings]
TypeList[]
TypeList[contentobject]=Content object
TypeList[contentclass]=Content class
TypeList[sitestyle]=Site style
TypeList[site]=Site
TypeList[extension]=Extension
```

`eZPackage::typeList()` macht daraus die Liste der Typen mit ihren Namen. Ihre Hauptverwendung ist die **Einschränkung Type** der
Paketrichtlinien: `kernel/package/module.php` gibt den Funktionen `read`, `list`, `create`, `edit`, `remove`, `install`, `import` und `export`
eine Einschränkung `Type`, deren Werte aus `typeList()` kommen. Ein Benutzer, dessen Richtlinie durch Type eingeschränkt ist, darf ein Paket
nur nutzen, wenn sein Typ unter den gewählten Werten ist (`eZPackage::canUsePackagePolicyFunction()`).

> **Hinweis**
> `extension` steht ab 6.0.15 in `TypeList`. Vorher fehlte es, sodass eine Einschränkung Type es nicht nennen konnte und eine durch Type
> eingeschränkte Rolle Pakete vom Typ `extension` nie erreichte. Fügen Sie es auf einem älteren Release in
> `settings/override/package.ini.append.php` hinzu:
>
> ```ini
> [PackageSettings]
> TypeList[extension]=Extension
> ```

## Was der Typ ändert

| Wo | Was der Typ bewirkt |
|---|---|
| Setup-Assistent, Schritt der Seitentypen | Nur Pakete `site` werden angeboten, lokal (`fetchPackages()` mit `'type' => 'site'`) und entfernt (`type="site"` im Index) |
| Schritt zum Anlegen der Websites | Nichts: Er installiert, was das Seitenpaket verlangt, gleich welchen Typs |
| Paketrichtlinien | Die Einschränkung Type (oben) |
| Paketliste in der Administration | Auf jeder Karte gezeigt; die Liste lässt sich in `eZPackage::fetchPackages()` nach Typ filtern |
| Installieren | Nichts: `install()` schaut auf `install_type` und die Installationselemente, nie auf den Typ |

## Typ, Installationstyp und Installationselemente sind drei Dinge

| | Sagt | Beispiel |
|---|---|---|
| `<type value>` | Was das Paket ist | `sitestyle` |
| `install_type` | Ob `install()` die Elemente überhaupt ausführt | `import` |
| `<install><item type>` | Welcher Handler die Arbeit für ein Element macht | `ezfile` |

Ein Paket `sitestyle` von 7x hat den Typ `sitestyle`, den Installationstyp `import` und ein Element `ezfile`: Es wird importiert, damit der
Installer des Seitenpakets seine Dateien nutzen kann, und es allein zu installieren ändert nichts. Siehe
[Importieren oder installieren](importieren-oder-installieren.md) und [Installationselemente und Handler](installationselemente-und-handler.md).

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Was ein Seiteninstallationspaket ist](was-ein-seiteninstallationspaket-ist.md)
- [Seitenstil-Paket](../../../../eng-us/help/designers/extensions/site-style-package.md): der Typ `sitestyle` aus der Sicht eines Designers
- [Berechtigungen für Pakete](../admin-oberflaeche/berechtigungen-fuer-pakete.md)
- [Anleitung zu Paketen, 10. Berechtigungen](../../../anleitungen/pakete.md#10-berechtigungen)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
