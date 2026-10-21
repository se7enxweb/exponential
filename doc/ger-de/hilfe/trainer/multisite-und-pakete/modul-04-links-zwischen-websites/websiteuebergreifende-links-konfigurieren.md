---
title: "Lektion 4.2: Websiteübergreifende Links konfigurieren"
audience: trainer, admin, developer
category: training
order: 42
summary: Die Teilnehmenden prüfen, dass ezurlmultisite aktiv ist, fügen die zweite Website mit den Knoten-IDs ihrer eigenen Installation zu SiteAccessList und RootNodeList hinzu, leeren die richtigen Caches und prüfen einen websiteübergreifenden Link; Entwickler sehen den PHP-Aufruf für im Code erzeugte Links. 50 Minuten.
keywords: multisite und seitenpakete, websiteübergreifende links, template-cache, multisite and site packages, ezurlmultisite, ezurlmultisite.ini, MultisiteSettings, SiteAccessList, RootNodeList, ezurl_roots, eZURLMultisite::crossSiteURL, postInstallWriteMultisiteURLSettings, template cache, autoloads
type: help
---
# Lektion 4.2: Websiteübergreifende Links konfigurieren

Auf einer Installation, die aus einem der Multisite-Seitenpakete entstanden ist, ist `ezurlmultisite` bereits aktiv
und für die Websites konfiguriert, die der Installer angelegt hat. Eine später hinzugefügte Website, wie `site_b` in
diesem Kurs, muss von Hand hinzugefügt werden, mit den Knoten-IDs dieser Installation. Diese Lektion erledigt das in
der Übungsumgebung und prüft einen Link von der Hauptwebsite in die zweite Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 4: Links zwischen Websites](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 4.1](warum-links-zu-einer-anderen-website-brechen.md); `site_b` hat `PathPrefix` auf den Alias seines Ordners gesetzt ([Lektion 2.1](../modul-02-anfragen-zuordnen/zuordnung-nach-url-pfad.md), Schritt 6) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- prüfen, dass `ezurlmultisite` aktiv ist, und seine Einstellungen lesen;
- eine Website mit der richtigen Knoten-ID zu `[MultisiteSettings] SiteAccessList[]` und `RootNodeList[]` hinzufügen;
- die Caches leeren, die den alten `ezurl`-Code enthalten;
- die teilnehmenden Websites mit dem Template-Operator `ezurl_roots()` auflisten;
- `eZURLMultisite::crossSiteURL()` in PHP-Code verwenden, der Links selbst erzeugt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Links auf Inhalte einer anderen Website, Installation und neue Installationen](../../../../../eng-us/features/6.0/multisite-cross-site-links.md#installing-it-on-an-existing-site).

1. **Die Einstellungen** (5 Minuten):

   ```ini
   # settings/override/ezurlmultisite.ini.append.php (the node ids of YOUR installation)
   [MultisiteSettings]
   SiteAccessList[]
   SiteAccessList[]=site
   SiteAccessList[]=bold
   SiteAccessList[]=bold_ger
   RootNodeList[]
   RootNodeList[site]=97
   RootNodeList[bold]=81
   RootNodeList[bold_ger]=81
   ```

   `SiteAccessList[]` nennt die Websites in der Reihenfolge der Bevorzugung; `RootNodeList[<siteaccess>]` den
   Wurzelknoten des Teilbaums jeder Website. Ohne die Datei nehmen die öffentlichen Siteaccesses mit einem
   `PathPrefix` teil, jeder mit dem Knoten seines `RootNode` aus `content.ini` oder seiner `IndexPage`.
2. **Knoten-IDs nie kopieren** (2 Minuten). Sie unterscheiden sich zwischen Installationen. Der Wurzelknoten einer
   Website ist der Knoten der `IndexPage` ihres Siteaccess oder von `content.ini [NodeSettings] RootNode`.
3. **Neue Installationen** (3 Minuten). Die Multisite-Installer (`sevenx_multisite`, `sevenx_multisite_clean`,
   `cjw_multisite`) aktivieren die Erweiterung und führen den Schritt nach der Installation
   `postInstallWriteMultisiteURLSettings` aus, der die Datei aus den Siteaccess-Dateien schreibt, die die Installation
   gerade geschrieben hat. Administrations-Siteaccesses werden ausgelassen.
4. **Nach einer Änderung** (2 Minuten). Erzeugen Sie die Autoloads der Erweiterungen neu, wenn die Erweiterung gerade
   aktiviert wurde, und leeren Sie den INI-, Template- und Inhalts-Cache: Kompilierte Templates enthalten den
   `ezurl`-Code des Kernels, bis sie erneut kompiliert werden. Unter Velocity erledigt
   `php bin/php/console exp:velocity deploy` all das.
5. **Für Entwickler** (3 Minuten). Code, der Inhaltslinks selbst erzeugt, stellt der Erweiterung dieselbe Frage:

   ```php
   if ( class_exists( 'eZURLMultisite' ) && eZURLMultisite::isActive() )
   {
       $url = eZURLMultisite::crossSiteURL( $nodeOrURLAlias, 'relative' ); // false: keep the local link
   }
   ```

### Häufige Fragen

**Was gibt `ezurl_roots()` zurück?** Pro teilnehmendem Siteaccess: `siteaccess`, `host`, `prefix` und
`root_node_id` (README der Erweiterung). Verwenden Sie es in einem Debug-Template, nie in der Produktivausgabe.

**Wo steht die vollständige Liste der Einstellungen?** Das README der Erweiterung listet die anderen auf:
`[HostMap]`, `[PathMap]`, `[RoleMap]`, klassenbasierte Links und die Operatoren `ezurl_path`, `ezurl_siteaccess` und
`ezurl_roots`.

**Warum muss `bold_ger` mit demselben Knoten wie `bold` aufgeführt werden?** Eine Website und ihre Übersetzungen
teilen einen Teilbaum; werden beide aufgeführt, gewinnt für einen Besucher der Übersetzung der aktuelle Siteaccess.

**Kann `exp:ini` diese Datei schreiben?** Ja, wie jede Einstellungsdatei: `exp:ini add 'ezurlmultisite.ini/MultisiteSettings/SiteAccessList[]' site_b global`.

### Stolperfallen

- **Eine leere Zeile `SiteAccessList[]` zum Zurücksetzen in der Datei ohne die alten Einträge.** Beim Hinzufügen von
  `site_b` müssen die Einträge erhalten bleiben, die der Installer geschrieben hat. Lesen Sie zuerst die Datei.
- **Den Template-Cache vergessen.** Die Einstellung stimmt, aber die alten Links bleiben, bis die Templates erneut
  kompiliert werden.
- **Eine Website ohne `PathPrefix`.** Ein URL-Alias als Zeichenkette wird über das Präfix zugeordnet, mit dem er
  beginnt; eine Website, deren Aliase kein Präfix tragen, wird nur über ihren Wurzelknoten gefunden.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Modul 3. Ersetzen Sie `http://127.0.0.1:8087` durch die Adresse Ihrer
Übungsumgebung.

1. Prüfen Sie, dass die Erweiterung aktiv ist:
   `php bin/php/console exp:ini get 'site.ini/ExtensionSettings/ActiveExtensions[]' | grep ezurlmultisite`.
2. Lesen Sie ihre wirksamen Einstellungen: `php bin/php/console exp:ini list ezurlmultisite.ini/MultisiteSettings`.
   Hat der Installer `settings/override/ezurlmultisite.ini.append.php` geschrieben, öffnen Sie die Datei und notieren
   Sie die Einträge.
3. Ermitteln Sie die Knoten-ID des Ordners *Site B* (Lektion 1.3) und der Wurzel der Hauptwebsite (ihre `IndexPage`:
   `php bin/php/console exp:ini get site.ini/SiteSettings/IndexPage -s site`).
4. Wenn die Datei Websites auflistet, fügen Sie `site_b` und seinen Wurzelknoten hinzu und behalten die vorhandenen
   Einträge bei:

   ```bash
   php bin/php/console exp:ini add 'ezurlmultisite.ini/MultisiteSettings/SiteAccessList[]' site_b global
   php bin/php/console exp:ini set 'ezurlmultisite.ini/MultisiteSettings/RootNodeList[site_b]' NNN global
   ```

   Existiert keine Datei, nehmen die öffentlichen Siteaccesses mit einem `PathPrefix` automatisch teil; überspringen
   Sie diesen Schritt und notieren Sie das.
5. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=content
   ```

6. Suchen Sie auf der Hauptwebsite nach dem Ordner *News* aus Lektion 2.1: Öffnen Sie
   `/site/content/search?SearchText=News` und finden Sie seinen Link in der Ergebnisliste (oder im Seitenquelltext:
   `curl -s ... | grep -i news`).
7. Notieren Sie das `href` des Links und öffnen Sie ihn.
8. Prüfen Sie in der Administration, dass die Links dort unverändert sind (sie nehmen nicht teil).

## Erwartetes Ergebnis

- Schritt 1 listet `ezurlmultisite`.
- Nach Schritt 5 zeigt der Link auf *News* auf der Hauptwebsite auf `/site_b/News` (die Adresse auf `site_b`, ohne
  sein Präfix), und beim Öffnen wird der Ordner auf *Site B* angezeigt.
- Zeigt die Suche nichts (ein leerer Suchindex in einer frischen Übungsumgebung), führt der Trainer
  `php bin/php/updatesearchindex.php` in einer Übungsumgebung aus oder zeigt die Linkprüfung stattdessen am Beamer.
- Der Trainer prüft das `href` aus Schritt 7 in einer Übungsumgebung.

## Quiz

1. Welche zwei Schlüssel von `[MultisiteSettings]` konfigurieren die teilnehmenden Websites?
2. Woher nehmen Sie die Wurzelknoten-ID einer Website?
   A. Aus einer anderen Installation B. Aus der `IndexPage` des Siteaccess oder dem `RootNode` aus `content.ini` C. Aus
   dem URL-Alias D. Aus der Objekt-ID
3. Welche Caches müssen nach einer Änderung geleert werden, und warum der Template-Cache?
4. Welcher Installer-Schritt schreibt die Datei auf einer neuen Multisite-Installation?
5. Welcher PHP-Aufruf gibt die websiteübergreifende Adresse eines Knotens zurück, und was bedeutet `false`?

<details>
<summary>Antworten</summary>

1. `SiteAccessList[]` und `RootNodeList[<siteaccess>]`.
2. **B.** Knoten-IDs unterscheiden sich zwischen Installationen; kopieren Sie sie nie.
3. INI, Template und Inhalt: Kompilierte Templates enthalten den `ezurl`-Code des Kernels, bis sie erneut kompiliert
   werden.
4. `postInstallWriteMultisiteURLSettings` der Multisite-Installer.
5. `eZURLMultisite::crossSiteURL( $nodeOrURLAlias, 'relative' )`; `false` bedeutet, den lokalen Link beizubehalten
   ([Für Entwickler von Erweiterungen](../../../../../eng-us/features/6.0/multisite-cross-site-links.md#for-extension-developers)).

</details>

## Weiterführende Informationen

- [Links auf Inhalte einer anderen Website einer Multisite-Installation](../../../../../eng-us/features/6.0/multisite-cross-site-links.md).
- [Suche mit eZ Find und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md) und
  [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md): wenn Suchergebnisse websiteübergreifend
  verlinken.
- [Caches](../../../../anleitungen/caches.md): der Template- und der Inhalts-Cache.

## Verwandte Seiten

- [Modul 4: Links zwischen Websites](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
