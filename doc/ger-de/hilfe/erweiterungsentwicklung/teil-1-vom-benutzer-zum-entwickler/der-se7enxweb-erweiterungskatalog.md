---
title: Der Erweiterungskatalog von se7enxweb
audience: user, developer, designer, app-creator
category: extension-development
order: 80
summary: Die 65 Erweiterungen, die die composer.json des Kernels verlangt, nach Zweck gruppiert, mit Repository, Lizenz und Dokumentation - der erste Ort, um nach Ideen und Beispielen zu suchen.
keywords: Katalog, Erweiterungsliste, Pakete, Layouts, Datentypen, Operatoren, Designs, Sicherheit, Newsletter, Ideen, Beispiele, catalogue, catalog, se7enxweb, extensions list, packages, composer require, github, layouts, datatypes, operators, designs, security, newsletter, ideas, examples
type: help
---
# Der Erweiterungskatalog von se7enxweb

Teil 1, Kapitel 8. Der Kernel von Exponential kommt nicht allein: Seine `composer.json` verlangt **66 Pakete der
Organisation se7enxweb**, 65 Erweiterungen und den Composer-Installer, der Erweiterungen in `extension/` ablegt. Ein
weiteres Dutzend schlägt sie vor. Dieses Kapitel listet sie nach dem auf, was sie tun. Für einen Entwickler sind sie
zweierlei zugleich: Bausteine, die Sie verwenden können, und funktionierende, gepflegte Beispiele für jede Art von
Erweiterung, die dieses Buch vermittelt.

Die Liste wurde an dem Tag, an dem diese Seite geschrieben wurde, aus der `composer.json` des Kernels entnommen. So sehen
Sie die aktuelle:

```bash
grep -o '"se7enxweb/[^"]*"' composer.json | sort -u
```

Die Repositories liegen auf GitHub unter [github.com/se7enxweb](https://github.com/se7enxweb), die Pakete auf Packagist
unter [packagist.org/packages/se7enxweb](https://packagist.org/packages/se7enxweb/). Die Spalte Lizenz zeigt, was GitHub
in jedem Repository erkennt; wo GPL-2.0 steht, lesen Sie in der eigenen `LICENSE` und `ezinfo.php` der Erweiterung nach,
ob „or any later version“ gilt. Für viele davon gibt es Seiten in der Dokumentation; die Spalte „Doku“ verlinkt sie.

## Seiten bauen: Exponential Layouts

Ein visueller Seitenbaukasten, aus den Paketen von Netgen Layouts nach Exponential portiert. Dreizehn Pakete, die zeigen,
wie eine große Funktion in einen fachlichen Kern, eine API, eine Benutzeroberfläche und austauschbare Abfrage-Handler
aufgeteilt wird.

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [explayouts](https://github.com/se7enxweb/explayouts) | Layouts, Zonen, Blöcke und Regeln: Speicherung, Resolver, Renderer | GPL-2.0 | |
| [explayouts_core](https://github.com/se7enxweb/explayouts_core) | Fachliche Kernschicht (Dienste für Layouts, Regeln, Collections, Blöcke und Zonen) | GPL-2.0 | |
| [explayouts_api](https://github.com/se7enxweb/explayouts_api) | PHP-API-Adapter zwischen Inhalten und Layouts | GPL-2.0 | |
| [explayouts_standard](https://github.com/se7enxweb/explayouts_standard) | Standard-Block-Handler (Titel, Text, Button, Bild ...) | GPL-2.0 | |
| [explayouts_ui](https://github.com/se7enxweb/explayouts_ui) | Administrationsoberfläche für Layouts | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/explayouts_ui.md) |
| [explayouts_ui_api](https://github.com/se7enxweb/explayouts_ui_api) | JSON-API und Hülle des Single-Page-Editors | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/explayouts_ui_api.md) |
| [explayouts_site_api](https://github.com/se7enxweb/explayouts_site_api) | Brücke zwischen Layouts und der Site-API | GPL-2.0 | |
| [explayouts_relation_list_query](https://github.com/se7enxweb/explayouts_relation_list_query) | Abfrage-Handler für Collections aus Relationslisten | GPL-2.0 | |
| [explayouts_tags_query](https://github.com/se7enxweb/explayouts_tags_query) | Abfrage-Handler für Collections aus Tags | GPL-2.0 | |
| [explayouts_content_browser](https://github.com/se7enxweb/explayouts_content_browser) | Back-End des Content-Browsers | GPL-2.0 | |
| [explayouts_content_browser_core](https://github.com/se7enxweb/explayouts_content_browser_core) | Kerndienste des Content-Browsers | GPL-2.0 | |
| [explayouts_content_browser_ui](https://github.com/se7enxweb/explayouts_content_browser_ui) | Serverseitig gerenderte Oberfläche des Content-Browsers | GPL-2.0 | |
| [expquery_translator](https://github.com/se7enxweb/expquery_translator) | Parser für Suchanfragen und Knotenfilter, portiert aus einer Query-Translator-Bibliothek | GPL-2.0 | |

## Site-Framework

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [expsite_api](https://github.com/se7enxweb/expsite_api) | Einfache PHP-Dienste über dem Inhaltsmodell für Website-Code | GPL-2.0 | |
| [expsite_core](https://github.com/se7enxweb/expsite_core) | Website-Helfer: Breadcrumb-Pfad, Weiterleitung, Mail | GPL-2.0 | |
| [expsite_app](https://github.com/se7enxweb/expsite_app) | Ein Behälter für die Design-Overrides und Anpassungen eines Projekts | GPL-2.0 | |
| [expsite_data_media](https://github.com/se7enxweb/expsite_data_media) | Website-Daten für die Media-Website | GPL-2.0 | |
| [expsite_installer](https://github.com/se7enxweb/expsite_installer) | Einrichtung einer Website mit einem Befehl | GPL-2.0 | |

## Administration und Bearbeitung

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [exp_adminui](https://github.com/se7enxweb/exp_adminui) | Eine alternative Administrationsoberfläche (AdminUI) | GPL-2.0 | [Funktion](../../../../eng-us/features/6.0/exp-adminui.md) |
| [expui](https://github.com/se7enxweb/expui) | Die JavaScript-API der Administration: jQuery 4 und der Namensraum `Exp`, als Ersatz für YUI | GPL-2.0 | |
| [ezautosave](https://github.com/se7enxweb/ezautosave) | Speichert Entwürfe, während Redakteure tippen | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezautosave.md) |
| [ezie](https://github.com/se7enxweb/ezie) | Bildeditor in der Bearbeitungsoberfläche | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezie.md) |
| [ezmultiupload](https://github.com/se7enxweb/ezmultiupload) | Viele Dateien auf einmal per Drag-and-drop hochladen | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezmultiupload.md) |
| [ezwt](https://github.com/se7enxweb/ezwt) | Die Website-Werkzeugleiste: von der öffentlichen Website aus bearbeiten | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezwt.md) |
| [expchangeclass](https://github.com/se7enxweb/expchangeclass) | Die Inhaltsklasse eines Objekts oder einer ganzen Klasse ändern | GPL-2.0 | |
| [ezownerchange](https://github.com/se7enxweb/ezownerchange) | Den Eigentümer von Inhalten neu zuweisen | GPL-2.0 | |
| [powercontent](https://github.com/se7enxweb/powercontent) | Werkzeuge zum Anlegen von Inhalten für Templates und Code | GPL-2.0 | |
| [sevenx_dse](https://github.com/se7enxweb/sevenx_dse) | Editor für Datenbankquellen in der Administration | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/sevenx_dse.md) |
| [git_manager](https://github.com/se7enxweb/git_manager) | Git-Checkouts, Branches und Sicherungen aus dem Browser | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/git_manager.md) |
| [ezupdate](https://github.com/se7enxweb/ezupdate) | Paketverwaltung: Composer, Packagist und Paketserver in der Administration | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/ezupdate.md) |

## Neue Arten von Feldern (Datentypen)

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [eztags](https://github.com/se7enxweb/eztags) | Taxonomie: Tags als Datentyp, mit Modulen und Fetch-Funktionen | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/eztags.md) |
| [birthday](https://github.com/se7enxweb/birthday) | Ein Datum, gespeichert als YYYY-MM-DD | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/birthday.md) |
| [enhancedezbinaryfile](https://github.com/se7enxweb/enhancedezbinaryfile) | Ein erweitertes Dateifeld | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/enhancedezbinaryfile.md) |
| [enhancedselection2](https://github.com/se7enxweb/enhancedselection2) | Ein erweitertes Auswahlfeld | GPL-2.0-only | [Seite](../../../../eng-us/features/6.0/extensions/enhancedselection2.md) |
| [exp_enhanced_link](https://github.com/se7enxweb/exp_enhanced_link) | Ein Linkfeld zu Inhalten oder URLs | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/exp_enhanced_link.md) |
| [ngclasslist](https://github.com/se7enxweb/ngclasslist) | Ein Feld, das eine Liste von Inhaltsklassen speichert | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/ngclasslist.md) |
| [ezgmaplocation](https://github.com/se7enxweb/ezgmaplocation) | Ein Feld für einen Ort auf der Karte | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezgmaplocation.md) |
| [ezstarrating](https://github.com/se7enxweb/ezstarrating) | Sternebewertungen | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezstarrating.md) |

## Benutzer und Sicherheit

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [sevenx_authentication_2fa](https://github.com/se7enxweb/sevenx_authentication_2fa) | Zwei-Faktor-Anmeldung und Handler für Social Login | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md) |
| [ezmbpaex](https://github.com/se7enxweb/ezmbpaex) | Ablauf und Prüfung von Passwörtern | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/ezmbpaex.md) |
| [hcaptcha](https://github.com/se7enxweb/hcaptcha) | hCaptcha als Datentyp | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/hcaptcha.md) |
| [recaptcha](https://github.com/se7enxweb/recaptcha) | reCAPTCHA als Datentyp | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/recaptcha.md) |

## Suche, SEO und Statistik

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [bcgooglesitemaps](https://github.com/se7enxweb/bcgooglesitemaps) | XML-Sitemaps | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/bcgooglesitemaps.md) |
| [bcwebsitestatistics](https://github.com/se7enxweb/bcwebsitestatistics) | Statistiken zu Seitenaufrufen und Bestellungen | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/bcwebsitestatistics.md) |
| [xrowmetadata](https://github.com/se7enxweb/xrowmetadata) | Metadaten und Sitemaps für Suchmaschinen | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/xrowmetadata.md) |

## Import, Export und Integration

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [bccie](https://github.com/se7enxweb/bccie) | Export gesammelter Informationen | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/bccie.md) |
| [ezodf](https://github.com/se7enxweb/ezodf) | Import und Export von Office-Dokumenten | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezodf.md) |
| [xrowextract](https://github.com/se7enxweb/xrowextract) | Import und Export von Inhalten (CSV, XML, JSON, Pakete) | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/xrowextract.md) |
| [syndication](https://github.com/se7enxweb/syndication) | Synchronisation von Inhalten zwischen Installationen | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/syndication.md) |
| [ezprestapi](https://github.com/se7enxweb/ezprestapi) | Inhaltsanbieter für die REST-API v2 | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/ezprestapi.md) |
| [cjw_tmv](https://github.com/se7enxweb/cjw_tmv) | Werkzeuge für die Integration eines externen Webdienstes | GPL-2.0 | |
| [expqueue](https://github.com/se7enxweb/expqueue) | Eine Job-Warteschlange in der Datenbank | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/expqueue.md) |

## Kommunikation, Handel, Intelligenz, Datenbanken

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [cjw_newsletter](https://github.com/se7enxweb/cjw_newsletter) | Newsletter | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) |
| [ezpaypal](https://github.com/se7enxweb/ezpaypal) | PayPal als Zahlungsanbieter für den Shop | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/ezpaypal.md) |
| [expai](https://github.com/se7enxweb/expai) | Exponential Intelligence: KI-gestütztes Erstellen von Inhalten | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/expai.md) |
| [ezoracle](https://github.com/se7enxweb/ezoracle) | Datenbanktreiber für Oracle | GPL-2.0-or-later | |

## Template-Werkzeuge

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [owsimpleoperator](https://github.com/se7enxweb/owsimpleoperator) | Template-Operatoren als einfache Methoden schreiben; erlaubte PHP-Funktionen bereitstellen | GPL-3.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/owsimpleoperator.md) |
| [swark](https://github.com/se7enxweb/swark) | Viele Template-Operatoren und einige Workflow-Ereignisse | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/swark.md) |
| [ezurlmultisite](https://github.com/se7enxweb/ezurlmultisite) | URL-Operatoren für Links über Websites hinweg | GPL-2.0 | |

## Designs und Seitenpakete

| Paket | Was es tut | Lizenz | Doku |
|---|---|---|---|
| [sevenx_themes_simple](https://github.com/se7enxweb/sevenx_themes_simple) | Das Standard-Theme „Simple“ | GPL-2.0 | [Seite](../../../../eng-us/features/6.0/extensions/sevenx_themes_simple.md) |
| [sevenx_themes_media](https://github.com/se7enxweb/sevenx_themes_media) | Das Theme der Media-Website | GPL-2.0 | |
| [cjw_themes_jumper](https://github.com/se7enxweb/cjw_themes_jumper) | Ein umgewandeltes Theme der Media-Website | GPL-2.0 | |
| [ezwebin](https://github.com/se7enxweb/ezwebin) | Das klassische Design der Website-Oberfläche | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezwebin.md) |
| [ezflow](https://github.com/se7enxweb/ezflow) | Seitenblöcke und Zonen, der ältere Seitenbaukasten | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezflow.md) |
| [ezdemo](https://github.com/se7enxweb/ezdemo) | Das Design der Demo-Website | GPL-2.0-or-later | [Seite](../../../../eng-us/features/6.0/extensions/ezdemo.md) |

## Der Installer

| Paket | Was es tut |
|---|---|
| [exponential-legacy-installer](https://github.com/se7enxweb/exponential-legacy-installer) | Der Composer-Installer für Pakete vom Typ `ezpublish-legacy-extension`: Er legt jedes in `extension/<name>` ab |

## Vorgeschlagen, nicht verlangt

Die `composer.json` des Kernels schlägt außerdem (installiert nur, wenn Sie es verlangen) unter anderem diese
Erweiterungen vor: `ezfind` (Solr-Suche), `ezauthorize` (Zahlungsanbieter), `ezsi` (Edge Side Includes),
`ezscriptmonitor` (lang laufende Skripte), `bcurlaliaswithdash`, `sevenx_valkey` und `sevenx_valkey_cache` (Valkey und
Redis), `adminaid`, `ezpm` (private Nachrichten). Lesen Sie den Block `suggest` in `composer.json` für die aktuelle Liste
mit je einer Zeile zu jeder.

Erweiterungen, die *im* eigenen Repository des Kernels ausgeliefert werden, stehen in keiner der beiden Listen:
`ezjscore`, `ezoe`, `ezformtoken` und `expservices`. Sie sind immer da.

## Wie Sie diesen Katalog als Entwickler nutzen

| Sie möchten lernen | Lesen Sie |
|---|---|
| Einen kleinen Template-Operator | `str_replace` (im Kernel-Baum), dann `owsimpleoperator` |
| Einen Datentyp | `birthday` (klein), dann `eztags` (groß) |
| Ein Modul mit Views und Richtlinien | `ezownerchange`, `expchangeclass` |
| Workflow-Ereignisse | `swark` (`eventtypes/event/`) |
| Ajax-Funktionen von ezjscore | `ezstarrating`, `ezautosave` |
| Eine Design-Erweiterung | `sevenx_themes_simple` |
| Eine große Funktion, aufgeteilt in Pakete | die Layouts-Pakete |
| Modernes JavaScript ohne Inline-Skripte | `expui` |

[Schritt für Schritt von einer Erweiterung lernen](schritt-fuer-schritt-von-einer-erweiterung-lernen.md) zerlegt zwei davon.

## Verwandte Seiten

- Zurück: [Die auf Ihrer Website installierten Erweiterungen finden](die-erweiterungen-ihrer-website-finden.md)
- Weiter: [Ideen auf GitHub finden](ideen-auf-github-finden.md)
- [Der Erweiterungskatalog in der Dokumentation](../../../../eng-us/features/6.0/extensions/README.md)
- [Was eine neue Installation mitbringt](../../../../eng-us/features/6.0/default-extension-distribution.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
