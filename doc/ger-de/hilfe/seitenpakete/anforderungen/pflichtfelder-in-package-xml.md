---
title: Pflichtfelder in package.xml
audience: developer, admin, designer
category: install-upgrade
order: 52
summary: Die Felder von package.xml, ohne die der Code ein Paket abweist, mit den genauen Regeln für den Namen, die Tokens für Version und Typ, den Hersteller, die Elemente ezpublish und packaging, und die Felder, die Konventionen der ausgelieferten Pakete sind und keine Prüfungen.
keywords: package.xml, Pflichtfelder, name, isValidName, identifier, vendor, packaging, ezpublish, version, release, type, install_type, ungültiger Name, existiert bereits, definition_incomplete, required fields, invalid name, already exists
type: help
---
# Pflichtfelder in package.xml

Diese Seite ist für Entwickler, die die `package.xml` eines Seitenpakets von Hand schreiben oder bearbeiten. Sie führt auf, ohne was der Code
ein Paket **abweist**, und warum, damit ein Paket nicht bei der ersten Installation scheitert, die es nutzt. Die Bedeutung jedes Elements
steht in [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md); diese Seite behandelt nur, was vorhanden sein muss und in welcher
Form.

## Inhalt

1. [Was jeder Import prüft](#was-jeder-import-prüft)
2. [Der Name](#der-name)
3. [Version, Release und Typ](#version-release-und-typ)
4. [Der Hersteller](#der-hersteller)
5. [Die Elemente ezpublish und packaging](#die-elemente-ezpublish-und-packaging)
6. [Was nicht geprüft wird](#was-nicht-geprüft-wird)
7. [Eine minimale Definition, die besteht](#eine-minimale-definition-die-besteht)
8. [Verwandte Seiten](#verwandte-seiten)

## Was jeder Import prüft

Jeder Weg, ein Paket zu importieren, geht durch `eZPackage::import()` in `kernel/classes/ezpackage.php`: der Download und das Hochladen im
Setup-Assistenten, `php ezpm.php import` und `package/upload` in der Administration. Es liest die Definition und hält an, wenn:

| Problem | Ergebnis |
|---|---|
| Keine `package.xml` oder `package.json` als einfache Datei oben im Archiv | Die Definition lässt sich nicht lesen, der Import scheitert |
| Kein `<name>` oder ein leerer | `A package definition without a name was not read` im Debug-Log |
| Eine Version, ein Release oder ein Typ, die kein einfaches Token sind | `A package definition with an invalid version / release / type was not read` |
| Ein Name, den die Transformation `identifier` ändern würde | `STATUS_INVALID_NAME`: „Der Paketname ... ist ungültig, konnte das Paket nicht importieren“ |
| Ein Paket dieses Namens ist bereits importiert | `STATUS_ALREADY_EXISTS`: „Paket ... existier bereits, kann Paket nicht importieren“ |
| Der Hersteller ergibt keinen brauchbaren Verzeichnisnamen | `The package ... names no usable repository, not imported` |

Die Seite zum Hochladen in der Administration prüft mehr, bevor irgendetwas entpackt wird (`eZPackageUploadInspector::definition()`); siehe
[Die Elemente ezpublish und packaging](#die-elemente-ezpublish-und-packaging).

## Der Name

**Erzwungen.** `eZPackage::isValidName()` schickt den Namen durch die Transformation `identifier` aus `settings/transform.ini` und akzeptiert
ihn nur, wenn sich nichts ändert. Die Transformation endet mit `lowercase` und `identifier_cleanup` (`lib/ezi18n/classes/ezcodemapper.php`),
sodass ein gültiger Name:

- nur die Zeichen `a` bis `z`, `0` bis `9` und `_` hat;
- keinen Großbuchstaben, kein Leerzeichen, keinen Bindestrich und keinen Punkt hat;
- nicht mit `_` beginnt oder endet und kein `__` enthält.

| Name | Gültig | Warum |
|---|---|---|
| `sevenx_multisite` | ja | |
| `cjw_multisite_democontent` | ja | |
| `MySite` | nein | Großbuchstaben |
| `my-site` | nein | Bindestrich |
| `my_site_` | nein | abschließendes `_` |

Das Hochladen in der Administration wendet außerdem `eZPackageRequestGuard::isSafeName()` an, das den Namen auf 200 Zeichen begrenzt. Der
Assistent zum Erstellen von Paketen weist einen ungültigen Namen mit „Der Paketname %packagename ist nicht gültig. Er kann nur Zeichen von
a-z, 0-9 und Unterstriche enthalten.“ ab (`kernel/classes/ezpackagecreationhandler.php`).

> **Tipp**
> Namen sind auf einer Installation global: Ein Import wird abgewiesen, wenn irgendein Repository bereits ein Paket dieses Namens enthält.
> Stellen Sie Ihren Paketen einen eigenen kurzen Namen voran (wie `sevenx_` und `cjw_` es tun), statt einen ausgelieferten Namen
> wiederzuverwenden.

## Version, Release und Typ

**Erzwungen.** Seit der Prüfung vom 15. Oktober 2026 weist `eZPackage::parseDOMTree()` eine Definition ab, deren `<version><number>`,
`<version><release>` oder `<type value="...">` kein einfaches Token ist (`eZPackage::isValidDefinitionToken()`): leer oder 1 bis 64 Zeichen
aus `0-9 A-Z a-z . _ + -`. Leerraum um den Wert wird vorher entfernt.

| Wert | Akzeptiert |
|---|---|
| `6.0` | ja |
| `15-stable` | ja |
| `1.0.0-beta.1+20261018` | ja |
| `6.0 final` | nein, Leerzeichen |
| `<b>1.0</b>` | nein, Markup |

Wie die Version aussehen sollte, steht auf [Versionsnummern](versionsnummern.md). Für ein Seitenpaket muss der Typ außerdem genau `site` sein,
sonst führt der Setup-Assistent es nicht auf ([Pakettypen](../grundlagen/pakettypen.md)).

## Der Hersteller

**Erzwungen.** `<vendor>` wird nach der Transformation `urlalias` zum Repository-Verzeichnis unter `var/storage/packages/`; ohne Hersteller
geht das Paket nach `local`. Ein Hersteller, der keinen brauchbaren Verzeichnisnamen übrig lässt (zum Beispiel `..`), wird abgewiesen. Die
ausgelieferten Pakete sagen alle `<vendor>7x</vendor>`, den Wert von `[RepositorySettings] Vendor` in `settings/package.ini`, und landen so
in `var/storage/packages/7x/`.

## Die Elemente ezpublish und packaging

**Erzwungen bei `package/upload`.** Der Upload-Inspektor weist eine Definition ab, die kein Element `<ezpublish>` oder kein Element
`<packaging>` hat, mit der Meldung, dass der `package.xml` des Archivs ihre Versions- oder Paketierungsangaben fehlen
(`definition_incomplete`). Er weist auch ein anderes Wurzelelement als `<package>` und jeden DOCTYPE ab. Der eigene Download des
Setup-Assistenten führt diese Prüfung nicht aus, sodass ein Paket ohne sie sich über den Assistenten installieren lässt und dennoch von der
Seite zum Hochladen abgewiesen wird.

```xml
<ezpublish>
  <version>6.0.15-stable</version>
  <named-version>6.0</named-version>
</ezpublish>
<packaging>
  <timestamp>1705211779</timestamp>
  <host>packages.example.com</host>
</packaging>
```

`<ezpublish>` ist der Name, den das Format schon immer für die Produktversion nutzt; Exponential behält ihn, wie er ist.

## Was nicht geprüft wird

Diese werden gelesen und gezeigt, aber nichts weist ein Paket ab, weil sie fehlen. Alle sind Konventionen der ausgelieferten Pakete:

| Element | Ausgelieferte Pakete von 7x | Wo es erscheint |
|---|---|---|
| `<summary>` | immer | Paketliste, Paketseite, Setup-Assistent |
| `<description>` | immer | Paketseite, Setup-Assistent |
| Attribut `install_type` | `import` für Seiten- und Seitenstil-Pakete, `install` für die anderen | Paketseite (**Importiert** oder **Installieren**) |
| `<licence>`, `<state>` | `GPL`, `stable` | Paketseite |
| `<maintainers>` | 15 von 16 | Paketseite |

Die Attribute `version` und `is_active` von `<package>` (die ausgelieferten Pakete schreiben `version="6.0.15-0"` oder `version="3.5.2"`)
liest `parseDOMTree()` nicht. Wenn der Kernel ein Paket schreibt, setzt er dort die Formatversion `3.5.2` (`eZPackage::VERSION`).

## Eine minimale Definition, die besteht

Diese besteht jede Prüfung oben, einschließlich der Seite zum Hochladen. Sie ist die Untergrenze, nicht die Empfehlung: Die ausgelieferten
Pakete bringen auch alles mit, was unter [Was nicht geprüft wird](#was-nicht-geprüft-wird) aufgeführt ist.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<package version="3.5.2" development="false" install_type="import">
  <name>example_site</name>
  <summary>Example site</summary>
  <vendor>example</vendor>
  <type value="site" />
  <ezpublish>
    <version>6.0.15-stable</version>
    <named-version>6.0</named-version>
  </ezpublish>
  <packaging>
    <timestamp>1791331200</timestamp>
    <host>packages.example.com</host>
  </packaging>
  <version>
    <number>1.0.0</number>
    <release>1</release>
  </version>
</package>
```

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Die Checkliste der Anforderungen](die-checkliste-der-anforderungen.md)
- [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md)
- [Ein Paket vor der Veröffentlichung prüfen](ein-paket-vor-der-veroeffentlichung-pruefen.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
