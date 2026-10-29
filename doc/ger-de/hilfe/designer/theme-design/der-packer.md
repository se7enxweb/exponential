---
title: Der Packer
audience: designer, developer
category: theme-design
order: 230
summary: Was der Packer von ezjscore mit Ihren Stylesheets und Skripten tut: zusammenführen, minimieren, url()-Pfade umschreiben, die gepackte Datei nach ihren Quellen und deren Zeit benennen, wo die Datei gespeichert wird und die Einstellungen, die ihn steuern.
keywords: Packer, Packer von ezjscore, minimieren, CSS zusammenführen, Bündel, gepackte Datei, öffentlicher Cache, AppendLastModifiedTime, DevelopmentMode, Packstufe, CustomHosts, JavaScriptOptimizer, CssOptimizer, packer, ezjscore packer, minify, merge css, bundle, packed file, cache public, pack level
type: help
---
# Der Packer

Diese Seite ist für Designer, die verstehen möchten, was mit ihrem CSS und JavaScript zwischen dem Design-Ordner und dem Browser geschieht.
Der Packer ist Teil der Erweiterung ezjscore (`extension/ezjscore/classes/ezjscpacker.php`).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).

## Inhalt

1. [Was er tut](#was-er-tut)
2. [Wo gepackte Dateien liegen](#wo-gepackte-dateien-liegen)
3. [Wie eine gepackte Datei benannt wird](#wie-eine-gepackte-datei-benannt-wird)
4. [Einstellungen](#einstellungen)
5. [Mit ausgeschaltetem Packer entwickeln](#mit-ausgeschaltetem-packer-entwickeln)
6. [Fehlerbehebung](#fehlerbehebung)

## Was er tut

Für jeden Aufruf von `ezcss_load`, `ezscript_load` (und der anderen Packer-Operatoren):

1. findet er jede Datei über die Design-Kaskade;
2. fügt er sie ab Packstufe 1 in der angegebenen Reihenfolge zu einer Datei zusammen;
3. führt er auf Stufe 2 die Optimierer aus `ezjscore.ini [eZJSCore]` aus (`JavaScriptOptimizer[]=ezjscJavascriptOptimizer`,
   `CssOptimizer[]=ezjscCssOptimizer`), die Kommentare und Leerraum entfernen;
4. schreibt er bei CSS relative `url()`-Pfade in absolute um, sodass Bilder und Schriften, auf die Ihr Stylesheet verweist, vom gepackten
   Ort weiter laden;
5. schreibt er das Ergebnis einmal und gibt ein einziges `<link>`- oder `<script>`-Tag aus, das darauf zeigt.

Weniger Dateien bedeuten weniger Anfragen: Die `pagelayout/head/style.tpl` des Designs media nennt fünfzehn Stylesheets in einem Aufruf von
`ezcss_load`, und der Browser holt eine Datei.

## Wo gepackte Dateien liegen

Im öffentlichen Cache der Website: `var/<site>/cache/public/stylesheets/` und `.../javascript/`. Die mitgelieferten Rewrite-Regeln liefern
`var/<site>/cache/public/` direkt aus (`.htaccess_root`), sodass gepackte Dateien, einmal geschrieben, nie über PHP laufen.

## Wie eine gepackte Datei benannt wird

Mit `ezjscore.ini [Packer] AppendLastModifiedTime=enabled` (der Standard in dieser Version) heißt eine gepackte Datei

```text
<md5 of the source file list and pack level>_<newest modification time of the sources>_<media>.css
```

zum Beispiel `82b3f6472765825fde39b69dc69ebd0a_1792109816_all.css`. Folgen:

- **Ändern Sie eine Quelldatei, ändert sich ihre Zeit**, sodass die gepackte Datei einen neuen Namen bekommt und Browser sie holen; keine
  manuelle Versionsabfrage ist nötig ([Keine Versionsabfragen an Asset-Links](keine-versionsparameter-an-asset-links.md)).
- Gepackte Dateien können von Browsern lange zwischengespeichert werden.
- Alte gepackte Dateien bleiben auf der Festplatte, bis der Cache `ezjscore-packer` geleert wird.

## Einstellungen

| Einstellung | Standard | Wirkung |
|---|---|---|
| `site.ini [TemplateSettings] DevelopmentMode` | `disabled` | `enabled` erzwingt Packstufe 0 (jede Datei einzeln), außer `ezjscore.ini [eZJSCore] Packer` ist gesetzt |
| `ezjscore.ini [eZJSCore] Packer` | nicht gesetzt | `disabled` = immer Stufe 0; eine Zahl von 0 bis 3 = immer diese Stufe; `enabled` = die Stufe jedes Aufrufs, auch im Entwicklungsmodus |
| `ezjscore.ini [Packer] AppendLastModifiedTime` | `enabled` | die Zeit im Dateinamen, wie oben |
| `ezjscore.ini [Packer] CustomHosts[]` | leer | gepackte `.js` oder `.css` von einem anderen Host ausliefern, z. B. `CustomHosts[.js]=https://static.example.com` |
| `ezjscore.ini [eZJSCore] LoadFromCDN` | `disabled` | `ezjsc::jquery` aus der lokalen Kopie (enabled: von den CDN-URLs aus `ExternalScripts[]`) |

Ändern Sie sie in der `ezjscore.ini.append.php` Ihrer Design-Erweiterung oder Ihres Siteaccess, nie in `extension/ezjscore/settings/`.

## Mit ausgeschaltetem Packer entwickeln

Auf einem Entwicklungs- oder Vorschau-Siteaccess:

```ini
# settings/siteaccess/<preview>/site.ini.append.php
[TemplateSettings]
DevelopmentMode=enabled
```

Jedes Stylesheet wird dann einzeln unter seinem eigenen Namen verlinkt, sodass der Stil-Inspektor des Browsers zeigt, aus welcher Datei und
Zeile eine Regel kommt. Schalten Sie `DevelopmentMode` nie auf einer Live-Website ein: Es lässt die Template-Engine außerdem bei jeder
Anfrage die Zeiten der Dateien prüfen.

## Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| Eine Datei fehlt im gepackten CSS | in der Kaskade nicht gefunden: Die Debug-Ausgabe hat „Could not find: ...“; prüfen Sie den Pfad relativ zu `stylesheets/` |
| Bilder in CSS nach dem Packen kaputt | ein absolutes `url(/...)`, das an die falsche Stelle zeigt; verwenden Sie Pfade relativ zum Stylesheet |
| Administration ohne Stile nach dem Leeren eines Caches | der Kopf der Administration nennt gepackte Dateien in einem Template-Block; leeren Sie `ezjscore-packer` und `template-block` zusammen (`exp:velocity deploy --packer`) |
| Ein Skriptfehler legt jedes Skript lahm | in einem gepackten Bündel teilen sich alle Dateien eine Datei; beheben Sie den Fehler oder teilen Sie das Bündel |

## Verwandte Seiten

- [Stylesheets laden](stylesheets-laden.md)
- [Der Packer-Cache und Deploys](packer-cache-und-deployment.md)
- [Die Operatoren ezcss und ezscript](die-operatoren-ezcss-und-ezscript.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
