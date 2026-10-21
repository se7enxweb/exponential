---
title: "Installationselemente und Handler"
audience: developer, admin
category: install-upgrade
order: 15
summary: Was ein Installationselement in package.xml ist, wie der Kernel über package.ini HandlerAlias den Handler dafür findet, was jeder mitgelieferte Handler (ezcontentclass, ezcontentobject, ezextension, ezfile, ezsql, eziniaddon, ezinstallscript) bei Installation und Deinstallation tut und wie Konflikte und Transaktionen behandelt werden.
keywords: Installationselement, Pakethandler, HandlerAlias, PackageSettings, eZPackageHandler, ezcontentclass, ezcontentobject, ezextension, ezfile, ezsql, eziniaddon, ezinstallscript, installItem, nicht interaktiv, Transaktion, InstallerSettings, install item, package handler, non-interactive, transaction
type: help
---
# Installationselemente und Handler

Diese Seite ist für Entwickler, die den Teil `<install>` eines Pakets lesen oder schreiben, und für Administratoren, die wissen wollen, was
eine Installation mit einer Website tun wird. Sie beantwortet: was ein Installationselement ist, welcher Code es ausführt und was jeder
Handler ändert.

## Inhalt

1. [Installationselemente](#installationselemente)
2. [Wie ein Handler gefunden wird](#wie-ein-handler-gefunden-wird)
3. [Die mitgelieferten Handler](#die-mitgelieferten-handler)
4. [Was mit einem Element geschieht](#was-mit-einem-element-geschieht)
5. [Konflikte: ein Element, das auf Vorhandenes trifft](#konflikte-ein-element-das-auf-vorhandenes-trifft)
6. [Handler im Setup-Assistenten und in ezpm](#handler-im-setup-assistenten-und-in-ezpm)
7. [Die Schritt-Installer der Administration](#die-schritt-installer-der-administration)
8. [Verwandte Seiten](#verwandte-seiten)

## Installationselemente

Ein Installationselement ist ein `<item>` der Liste `<install>` in `package.xml`:

```xml
<install>
  <item type="ezcontentclass" filename="class-article" sub-directory="ezcontentclass"/>
  <item type="ezcontentobject" filename="contentobjects" sub-directory="ezcontentobject"/>
  <item type="ezextension" filename="extension-ezwebin" sub-directory="ezextension"/>
  <item type="ezfile" collection="default"/>
</install>
```

`type` nennt den Handler. `sub-directory` und `filename` nennen das Dokument des Elements, `<package>/<sub-directory>/<filename>.xml`. Die
Liste `<uninstall>` hat dieselbe Form. Elemente laufen in der Reihenfolge, in der sie aufgeführt sind; `eZPackage::installUnaudited()`
überspringt ein Element, dessen `type`, `filename` und `sub-directory` ein früheres wiederholen.

## Wie ein Handler gefunden wird

`eZPackage::packageHandler( $type )` schlägt den Typ in `settings/package.ini` nach:

```ini
[PackageSettings]
HandlerAlias[ezcontentclass]=eZContentClassPackageHandler
HandlerAlias[ezcontentobject]=eZContentObjectPackageHandler
HandlerAlias[eziniaddon]=eZINIAddonPackageHandler
HandlerAlias[ezfile]=eZFilePackageHandler
HandlerAlias[ezini]=eZFilePackageHandler
HandlerAlias[ezdesign]=eZFilePackageHandler
HandlerAlias[eztemplate]=eZFilePackageHandler
HandlerAlias[ezthumbnail]=eZFilePackageHandler
HandlerAlias[ezsql]=eZDBPackageHandler
HandlerAlias[ezextension]=eZExtensionPackageHandler
HandlerAlias[ezinstallscript]=eZInstallScriptPackageHandler
```

Die Klasse wird über `eZExtension::getHandlerClass()` erzeugt und für die Anfrage behalten. Ein Typ ohne Eintrag hat keinen Handler: Die
Installation dieses Elements scheitert mit der Meldung, dass für den Typ des Installationselements kein Pakethandler gefunden wurde. Weil
das ein INI-Array ist, lässt sich ein weiterer Handler mit einem zusätzlichen Eintrag `HandlerAlias[<type>]=<class>` in einer Überschreibung
oder in der `package.ini.append.php` einer Erweiterung registrieren; die Klasse muss `eZPackageHandler` erweitern
(`kernel/classes/ezpackagehandler.php`).

## Die mitgelieferten Handler

| Typ des Elements | Handler | Ordner | Installieren | Deinstallieren |
|---|---|---|---|---|
| `ezcontentclass` | `eZContentClassPackageHandler` | `kernel/classes/packagehandlers/ezcontentclass/` | Legt die Klasse aus ihrem Dokument an, mit ihren Attributen, in ihren Klassengruppen. Ein bereits vergebener Bezeichner bekommt `_1`, `_2` ... | Entfernt die über die Remote-ID gefundene Klasse, nach einer Bestätigung, wenn es Objekte davon gibt |
| `ezcontentobject` | `eZContentObjectPackageHandler` | `.../ezcontentobject/` | Legt die in `contentobjects.xml` aufgeführten Objekte an (`<object-files-list>`, je eine `object-<remote id>.xml`) unterhalb der Wurzelknoten, dann installiert es Templates, Overrides und Fetch-Aliase, die das Dokument mitbringt | Entfernt die Objekte |
| `ezextension` | `eZExtensionPackageHandler` | `.../ezextension/` | Kopiert `<sub-directory>/<name>/` nach `extension/<name>/`, erzeugt die Autoloads der Erweiterungen neu und fügt den Namen zu `ActiveExtensions[]` in `settings/override/site.ini.append.php` hinzu | Löscht `extension/<name>/`, entfernt ihn aus `ActiveExtensions[]`, erzeugt die Autoloads neu |
| `ezfile`, `ezini`, `ezdesign`, `eztemplate`, `ezthumbnail` | `eZFilePackageHandler` | `.../ezfile/` | Kopiert jede Datei der `collection` des Elements an ihren Platz: `design/<design>/templates`, `images`, `stylesheets`, `fonts`; `settings/[override/][siteaccess/<name>/]`; oder einen einfachen Pfad. Vorschaubilder werden nicht kopiert. INI-Dateien können gesetzte Variablen bekommen | ab 6.0.15: entfernt die Dateien, die seine Installation angelegt hat und die seitdem niemand geändert hat (siehe den Hinweis unten) |
| `ezsql` | `eZDBPackageHandler` | `.../ezdb/` | Führt `sql/[<database-type>/]<filename>` über `eZDB::insertFile()` aus; mit einem `database-type` einer anderen Engine als der aktuellen wird die Datei nicht ausgeführt, und das Element meldet einen Fehlschlag | nichts: SQL lässt sich nicht umkehren; das Element zählt als nicht deinstalliert |
| `eziniaddon` | `eZINIAddonPackageHandler` | `.../eziniaddon/` | Schreibt INI-Blöcke eines Siteaccess und macht dabei aus Remote-IDs die IDs dieser Website | nichts: Die Blöcke bleiben; das Element zählt als nicht deinstalliert |
| `ezinstallscript` | `eZInstallScriptPackageHandler` | `.../ezinstallscript/` | Nichts: meldet Erfolg. Das Skript führt ein Schritt-Installer aus (unten) | nichts |

Die Dateien unterscheiden sich darin, was sie lesen: `ezcontentclass`, `ezcontentobject`, `ezextension` und `eziniaddon` lesen das Dokument
des Elements, bevor `install()` aufgerufen wird (`extractInstallContent()`), die anderen nicht.

> **Hinweis**
> Vor 6.0.15 hatten `eZFilePackageHandler`, `eZDBPackageHandler` und `eZINIAddonPackageHandler` kein eigenes `uninstall()`: Die
> Basismethode tat nichts und gab keinen Wert zurück, was `eZPackage::uninstall()` als ein nicht deinstalliertes Element zählte, und Dateien,
> SQL und Änderungen an INI blieben auf der Website. Ab 6.0.15:
>
> - `eZFilePackageHandler::uninstall()` entfernt eine Datei nur, wenn ihre Installation sie angelegt hat (sie existierte vorher nicht; die
>   Installation merkt sie sich in den Installationsdaten als `installed_files`), ihr Pfad relativ ist und innerhalb der Installation
>   liegt, ihr Inhalt seitdem unverändert ist und dieser Inhalt eine der eigenen Dateien des Pakets ist. Alles andere bleibt: eine Datei,
>   die die Installation ersetzt hat, eine Datei, die die Website geändert hat, eine INI-Datei, die bei der Installation Variablen bekommen
>   hat, und die Verzeichnisse. Ein vor 6.0.15 installiertes Paket hat nichts gemerkt, also wird nichts entfernt.
> - `eZDBPackageHandler` und `eZINIAddonPackageHandler` haben ein `uninstall()`, das nichts ändert und in der Debug-Ausgabe sagt warum: SQL
>   lässt sich nicht automatisch umkehren, und INI-Blöcke enthalten seitdem vielleicht eigene Werte der Website. Das Element zählt weiterhin
>   als nicht deinstalliert; entfernen Sie diese Änderungen von Hand.

## Was mit einem Element geschieht

`eZPackage::installItem()` tut für jedes Element Folgendes:

1. Den Handler finden; scheitern, wenn es keinen gibt.
2. Wenn der Handler Inhalte ausliest, `<sub-directory>/<filename>.xml` lesen (oder ihr `.json`-Gegenstück); scheitern, wenn es sich nicht
   lesen lässt.
3. Eine Datenbanktransaktion öffnen.
4. `install()` des Handlers mit dem Paket, dem Element und den Installationsparametern aufrufen.
5. Bei Erfolg bestätigen; zurückrollen, wenn der Handler false zurückgibt, die Transaktion ungültig hinterlässt oder eine Ausnahme wirft.

Das ganze Paket scheitert am ersten scheiternden Element, es sei denn, die Installationsparameter sagen `continue-on-error`. Ein Paket,
dessen Elemente alle erfolgreich waren, wird in der Tabelle `ezpackage` als installiert vermerkt (Name, `<number>-<release>`, Datum). Was
sich die Handler für die Deinstallation merken, wird als Installationsdaten (`<install-data>`) aufbewahrt.

## Konflikte: ein Element, das auf Vorhandenes trifft

Handler melden einen Konflikt über `$installParameters['error']` mit einem Code, einer Beschreibung und den Aktionen, die eine Person wählen
kann. Die Installationsseite der Administration zeigt sie; die Wahl kann für den Rest des Laufs gespeichert werden.

| Handler | Konflikt | Angebotene Aktionen |
|---|---|---|
| `ezcontentclass` | Eine Klasse mit derselben Remote-ID existiert | Ersetzen (mit einer Warnung zu den Objekten, die mitgehen), vorhandene Klasse aktualisieren (Attribute hinzugefügt und aktualisiert, keines entfernt), Überspringen, vorhandene behalten und eine neue anlegen |
| `ezextension` | `extension/<name>/` existiert | Erweiterung ersetzen, Überspringen |
| `ezextension` | Der Name ist kein gültiger Name einer Erweiterung | keine: Das Element scheitert |

Mit gesetztem `non-interactive` kann niemand antworten, also nimmt ein Handler seinen nicht interaktiven Zweig: `ezcontentclass`
**überspringt** eine vorhandene Klasse (nicht interaktive Installationen, etwa über die Kommandozeile, dürfen vorhandene Klassen nicht
zerstörend ersetzen), während `ezextension` ein vorhandenes Erweiterungsverzeichnis **ersetzt**.

## Handler im Setup-Assistenten und in ezpm

Der Schritt zum Anlegen der Websites installiert jedes verlangte Paket mit diesen Parametern (`kernel/setup/steps/ezstep_create_sites.php`):

```php
array( 'use_dates_from_package' => true,
       'site_access_map' => array( '*' => $userSiteaccessName ),
       'top_nodes_map' => array( '*' => 2 ),
       'design_map' => array( '*' => $userDesignName ),
       'language_map' => $languageMap,
       'restore_dates' => true,
       'user_id' => $user->attribute( 'contentobject_id' ),
       'non-interactive' => true );
```

`ezpm install` nutzt dieselbe Art von Zuordnung mit dem Siteaccess des Laufs und dem Wurzelknoten 2 (oder dem angegebenen Zielknoten),
ebenfalls `non-interactive`. Beide überspringen also vorhandene Klassen und ersetzen vorhandene Erweiterungen.

## Die Schritt-Installer der Administration

Neben den Handlern nennt `settings/package.ini [InstallerSettings]` Schritt-Installer für die Installationsseite der Administration:

```ini
[InstallerSettings]
HandlerList[]=ezcontentobject
HandlerList[]=ezinstallscript
HandlerAlias[ezcontentobject]=eZContentObjectPackageInstaller
HandlerAlias[ezinstallscript]=eZInstallScriptPackageInstaller
InstallBatchMaxItems=12
InstallBatchTimeBudgetSeconds=6
```

Sie liegen in `kernel/classes/packageinstallers/` und fügen der Installation Seiten hinzu (zum Beispiel die Wahl, wohin Inhalte gehen).
`InstallBatchMaxItems` und `InstallBatchTimeBudgetSeconds` teilen eine lange Installation in Stapel; siehe
[Stapelverarbeitung im Paket-Installer](../../../funktionen/6.0/paketinstallation-in-teilschritten.md).

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Referenz zu package.xml](referenz-zu-package-xml.md)
- [Importieren oder installieren](importieren-oder-installieren.md)
- [Installieren und Deinstallieren](../admin-oberflaeche/installieren-und-deinstallieren.md)
- [Anleitung zu Paketen, 7. Installieren und Deinstallieren](../../../anleitungen/pakete.md#7-installieren-und-deinstallieren)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
