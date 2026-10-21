---
title: eZSys und eZExtension
audience: developer
category: extension-development
order: 110
summary: Pfade und die Umgebung mit eZSys (varDirectory, cacheDirectory, serverURL, isShellExecution) und aktive Erweiterungen mit eZExtension (activeExtensions, expandedPathList, getHandlerClass).
keywords: Pfade, Umgebung, aktive Erweiterungen, Request, Proxy, Handler-Klasse, eZSys, varDirectory, cacheDirectory, storageDirectory, rootDir, wwwDir, indexFile, serverURL, hostname, isShellExecution, isSSLNow, eZExtension, activeExtensions, baseDirectory, expandedPathList, extensionInfo, getHandlerClass
type: help
---
# eZSys und eZExtension

Teil 8, Referenz 11. Zwei Hilfsklassen, die jede Erweiterung früher oder später braucht: `eZSys`
(`lib/ezutils/classes/ezsys.php`) weiß, wo die Dinge liegen und wie der Request hereinkam; `eZExtension`
(`lib/ezutils/classes/ezextension.php`) weiß, welche Erweiterungen aktiv sind und wo sie liegen.

## eZSys: Pfade

Alle statisch:

| Methode | Gibt zurück (auf einer typischen Installation) |
|---|---|
| `varDirectory()` | das var-Verzeichnis der Website, z. B. `var/site` |
| `cacheDirectory()` | dessen Cache-Verzeichnis, z. B. `var/site/cache` |
| `storageDirectory()` | dessen Speicherverzeichnis (hochgeladene Dateien) |
| `rootDir()` | das Wurzelverzeichnis der Installation |
| `wwwDir()`, `indexDir()`, `indexFile()` | die Webpfade der Installation |
| `siteDir()` | das Website-Verzeichnis |

Schreiben Sie nie Pfade wie `var/site/cache` in den Code: Das var-Verzeichnis ist eine Einstellung und unterscheidet
sich je Installation.

```php
<?php
$dir = eZSys::cacheDirectory() . '/myext';
eZDir::mkdir( $dir, false, true );
```

## eZSys: der Request

| Methode | Gibt zurück |
|---|---|
| `isShellExecution()` | `true` auf der Kommandozeile |
| `serverURL()` | Schema und Host des aktuellen Requests |
| `hostname()` | den Hostnamen |
| `isSSLNow()` | ob der Request über HTTPS kam (unter Berücksichtigung der konfigurierten Proxys) |
| `clientIP()` | die Adresse des Clients (unter Berücksichtigung der konfigurierten Proxys) |
| `serverVariable( $variableName, $quiet = false )` | einen Wert aus `$_SERVER` |
| `osType()`, `fileSeparator()` | das Betriebssystem |

Hinter einem Reverse Proxy hängen `isSSLNow()` und `clientIP()` von den Einstellungen für vertrauenswürdige Proxys ab
([Vertrauenswürdige Proxys](../../../bc/6.0/vertrauenswuerdige-proxys.md)).

## eZExtension

| Methode (statisch) | Gibt zurück |
|---|---|
| `activeExtensions( $extensionType = false, $siteINI = null )` | die Namen der aktiven Erweiterungen in Ladereihenfolge |
| `baseDirectory( $siteINI = null )` | das Erweiterungsverzeichnis, meist `extension` |
| `expandedPathList( $extensionList, $subdirectory = false )` | den Pfad jeder Erweiterung, optional mit angehängtem Unterverzeichnis |
| `extensionInfo( $extension )` | die Metadaten der Erweiterung (Name, Version, Copyright, Lizenz, Info-URL) |
| `nameFromPath( $path )` | die Erweiterung, zu der ein Pfad gehört |
| `getHandlerClass( ezpExtensionOptions $options )` | ein Objekt der Klasse, die eine INI-Einstellung nennt |

```php
<?php
if ( in_array( 'ezjscore', eZExtension::activeExtensions() ) )
{
    // ezjscore is there: use its packer keys
}
$settingsDirs = eZExtension::expandedPathList( array( 'myext' ), 'settings' );   // array( 'extension/myext/settings' )
```

Auf einer Demo-Installation ausgeführt, gab `activeExtensions()` 62 Namen zurück und
`expandedPathList( array( 'ezjscore' ), 'settings' )` lieferte `extension/ezjscore/settings`. Für umfangreichere
Metadaten (die Daten der Info-Seite) liest die Kernel-Klasse `expInfo` `extension.xml` und `ezinfo.php` zusammen
([Spezifikation der Erweiterungsmetadaten](../../../spezifikationen/6.0/extension-metadaten.md)).

`getHandlerClass()` und die Optionen, die es entgegennimmt, erklärt am Beispiel des Mail-Transports
[Durch Einstellungen benannte Handler](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md).

## Verwandte Seiten

- Zurück: [eZHTTPTool: Requests, Sitzungen und Weiterleitungen](ezhttptool.md)
- Weiter: [eZTemplate aus PHP](eztemplate.md)
- [Verzeichnisse unter var/](verzeichnisse-unter-var.md)
- [Ladereihenfolge der Erweiterungen](../teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
