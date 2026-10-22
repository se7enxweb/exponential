---
title: Offline-Bundles
audience: bundler, hosting
category: bundling
order: 180
summary: Wie Sie ein Bundle bauen, das Exponential ohne Internetzugang installiert - Code und Composer-Pakete im Bundle, mit ezpm vor der Installation importierte Seitenpakete, was der Installer tut, wenn der Paketserver nicht erreichbar ist, der Vorbehalt beim Probelauf und ein Test, der beweist, dass das Bundle offline funktioniert.
keywords: offline, ohne Internet, abgeschottetes Netz, Import, lokale index.xml, Probelauf, Appliance, Schulung, air-gapped, no internet, import, ezpm import, var/storage/packages, local index.xml, dry run, appliance, classroom, bundle
type: help
---
# Offline-Bundles

Diese Seite richtet sich an Bündler, deren Bundle dort installieren muss, wo es kein Internet gibt: Appliances,
Schulungsräume, abgesicherte Netzwerke, Messe-Laptops. Sie hilft auch allen anderen Bündlern, denn ein offlinefähiges
Bundle installiert jedes Mal dasselbe.

## Inhalt

1. [Was schon enthalten sein muss](#was-schon-enthalten-sein-muss)
2. [Die Seitenpakete importieren](#die-seitenpakete-importieren)
3. [Was der Installer ohne den Server tut](#was-der-installer-ohne-den-server-tut)
4. [Der Vorbehalt beim Probelauf](#der-vorbehalt-beim-probelauf)
5. [Beweisen, dass es offline funktioniert](#beweisen-dass-es-offline-funktioniert)

## Was schon enthalten sein muss

| Teil | Wie er ins Bundle kommt |
|---|---|
| Der Code mit `vendor/` und jeder Erweiterung | zur Build-Zeit mit Composer gebaut ([Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md)) |
| Die PHP-Erweiterungen | im Image oder in den Betriebssystempaketen |
| Das Seitenpaket und jedes Paket, das es verlangt | `.ezpkg`-Dateien, importiert nach `var/storage/packages/` ([siehe unten](#die-seitenpakete-importieren)) |
| Velocity oder ein anderer Webserver | Composer (Velocity) oder Betriebssystempakete |
| Suche, falls verwendet | eine Solr-Installation im Bundle oder die eingebaute Suche |

## Die Seitenpakete importieren

Der dokumentierte Weg: "Der zuverlässige Weg, offline zu installieren, ist, **zuerst jedes Paket zu importieren**"
([Offline-Installationen und Spiegel](../seitenpakete/paketserver/offline-installationen-und-spiegel.md#was-der-installer-ohne-den-server-tut)).

```bash
for f in var/tmp/packages/*.ezpkg; do php ezpm.php import "$f"; done
ls var/storage/packages/7x/
php bin/php/console exp:install --package=sevenx_multisite_clean
```

Für `sevenx_multisite` sind die verlangten Pakete `sevenx_classes` und `sevenx_multisite_democontent`; die
Abhängigkeiten anderer Pakete finden Sie wie unter
[Ein Paket von Hand herunterladen](../seitenpakete/paketserver/ein-paket-von-hand-herunterladen.md#herausfinden-was-es-verlangt)
beschrieben. Entfernen Sie die Kopien in `var/tmp/packages/` nach dem Import.

Führen Sie den Import in einem Bundle zur Build-Zeit aus, sodass das Image `var/storage/packages/` bereits enthält,
oder als ersten Schritt des Skripts für den ersten Start, bevor der Installer läuft.

## Was der Installer ohne den Server tut

Aus `kernel/setup/steps/ezstep_site_types.php`, wie dokumentiert:

| Situation | Kickstarter und `exp:install` |
|---|---|
| Index nicht erreichbar, `var/storage/packages/7x/index.xml` existiert | diese Datei wird stattdessen verwendet |
| Index nicht erreichbar, keine lokale `index.xml` | das importierte Seitenpaket wird verwendet, wenn alle verlangten Pakete importiert sind; sonst bricht der Schritt ab |
| ein verlangtes Paket ist nicht importiert und lässt sich nicht herunterladen | der Schritt bricht mit einem Fehler ab, der das Paket nennt |
| alles importiert | es wird nichts heruntergeladen |

> **Warnung**
> Eine lokale `index.xml`, deren `url`s nicht erreichbar sind, kann Ihr importiertes Seitenpaket im
> Einrichtungsassistenten verdecken, wenn ihre Versionen höher sind. "Halten Sie die Versionen in einem lokalen Index
> gleich oder niedriger als die der importierten Pakete, oder verwenden Sie keinen lokalen Index."

## Der Vorbehalt beim Probelauf

"Ein Probelauf (`exp:install --dry-run`, `exp:kickstarter run --dry-run`) lädt das Seitenpaket und seine
Abhängigkeiten zur Prüfung immer in das temporäre Repository `var/storage/packages/dryrun/` herunter. Er braucht den
Paketserver (oder einen Spiegel), auch wenn die Pakete importiert sind." Führen Sie Probeläufe im Build aus, wo das
Netzwerk verfügbar ist, nicht auf dem Offline-Zielsystem.

## Beweisen, dass es offline funktioniert

1. Bauen Sie das Bundle.
2. Starten Sie es in einer Umgebung ohne ausgehendes Netzwerk (ein Container mit abgeschaltetem Netzwerk, eine
   virtuelle Maschine ohne Netzwerkadapter).
3. Führen Sie die Installation beim ersten Start aus.
4. Prüfen Sie das Ergebnis: Exit-Status `0`, `var/log/setup.log` endet mit einer RESULT-Zeile ohne Fehler, die Website
   antwortet.
5. Bewahren Sie das Protokoll mit dem Release auf ([Ein Bundle testen](ein-bundle-testen.md)).

Dinge, die nach der Installation womöglich noch ins Netzwerk wollen und die ein Offline-Bundle konfigurieren sollte:
der Mailversand ([Mailzustellung](../hosting/betrieb/mailzustellung.md)), die Paketserver von ezupdate und jede
Erweiterung, die einen externen Dienst aufruft.

## Verwandte Seiten

- [Offline-Installationen und Spiegel](../seitenpakete/paketserver/offline-installationen-und-spiegel.md)
- [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md)
- [Appliances](appliances.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
