---
title: Seitenpakete in einem Bundle
audience: bundler, developer, hosting
category: bundling
order: 160
summary: Wie ein Bundle ein Seitenpaket wählt, mitbringt und installiert - was ein Seitenpaket ist, welche Pakete der Index von 6.0.15 anbietet, wie jeder Installer das Paket benennt, warum ein Bundle genau die getesteten .ezpkg-Dateien ausliefern sollte, ein eigenes Seitenpaket erstellen und was Sie vor dem Release prüfen.
keywords: Seitenpaket, Paketserver, Demo-Inhalte, Import, site package, ezpkg, package.xml, sevenx_multisite, sevenx_multisite_clean, cjw_multisite, package server, index.xml, Site_package, --package, import, ezpm, bundle, demo content
type: help
---
# Seitenpakete in einem Bundle

Diese Seite richtet sich an Bündler, deren Bundle eine Website installiert, und das tut fast jedes Bundle. Die
Website, die ein Installer anlegt, wird aus einem **Seitenpaket** aufgebaut. Die vollständige Referenz ist der Bereich
[Seitenpakete](../seitenpakete/README.md); diese Seite zeigt ihn aus der Sicht des Bündlers.

## Inhalt

1. [Was ein Seitenpaket mitbringt](#was-ein-seitenpaket-mitbringt)
2. [Ein Paket wählen](#ein-paket-wählen)
3. [Dem Installer das Paket nennen](#dem-installer-das-paket-nennen)
4. [Liefern Sie die getesteten Dateien aus](#liefern-sie-die-getesteten-dateien-aus)
5. [Ihr eigenes Seitenpaket](#ihr-eigenes-seitenpaket)
6. [Checkliste](#checkliste)

## Was ein Seitenpaket mitbringt

"Ein **Paket** ist ein Ordner mit einer `package.xml` und den darin genannten Dateien, ausgeliefert als eine
gzip-komprimierte tar-Datei mit der Endung `.ezpkg`. Ein **Seitenpaket** ist ein Paket vom Typ `site`; es verlangt
andere Pakete (Inhaltsklassen, Inhalte) und kann Installationsskripte mitbringen, die Siteaccesses, Erweiterungen,
Rollen und Voreinstellungen einrichten"
([Seitenpakete: hier beginnen](../seitenpakete/README.md#die-kurzfassung)).

Die Installationsskripte laufen innerhalb von `CreateSites`, gleich welcher Installer den Lauf gestartet hat; der
"Multi-Site-Installer" von `sevenx_multisite` "ist daher keine vierte Installationsmethode: Er ist der Teil des
Seitenpakets an jeder Installation"
([4.3](../../installation/04-installationsweg-waehlen.md#installationsskripte-der-pakete-der-multi-site-installer)).

## Ein Paket wählen

Der Paketindex von 6.0.15 bietet zwölf Seitenpakete an
([4.3](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)):

| Seitenpaket | Für ein Bundle, das ... |
|---|---|
| `sevenx_multisite` | Exponential mit Demo-Inhalten zeigt: Evaluierungen, Demos, Schulungen. Es ist der Standard von `exp:install --package` |
| `sevenx_multisite_clean` | dieselbe Website leer liefert, für eine echte Website, die der Empfänger füllt |
| `cjw_multisite` | die CJW-Starter-Website mit einer englischen und einer deutschen Website möchte |
| `sevenx_site`, `sevenx_site_clean` | eine einfachere Website möchte |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site`, `plain_site` (und `_clean`) | zu älteren, darauf aufgebauten Websites passen muss |
| Ihr eigenes | Ihr Design und Ihr Inhaltsmodell mitbringt ([Ihr eigenes Seitenpaket](#ihr-eigenes-seitenpaket)) |

"Ein `_clean`-Paket installiert dieselbe Struktur ohne die Demo-Artikel und -Bilder: Wählen Sie es für eine echte
Website, die Sie selbst füllen, und das vollständige, um Exponential zu evaluieren."

## Dem Installer das Paket nennen

| Installer | Wie |
|---|---|
| `exp:install` | `--package=<name>` |
| Kickstarter | `kickstart.ini`, `[site_types] Site_package=<name>` |
| Einrichtungsassistent | die Seite **Site Paket**; sie lässt sich per `kickstart.ini` vorbelegen |

Quelle: [Eine Website auf Ihren Paketserver zeigen lassen](../seitenpakete/eigene/eine-website-auf-ihren-paketserver-richten.md#ihr-seitenpaket-nennen).

## Liefern Sie die getesteten Dateien aus

Standardmäßig laden die Installer das Seitenpaket und seine Abhängigkeiten von dem Paketserver herunter, der in
`settings/package.ini [RepositorySettings] RemotePackagesIndexURL` genannt ist. Für ein Bundle ist das ein beweglicher
Teil:

- "Pakete in einem Release-Ordner sind auf dem öffentlichen Server fest verankert: Der Dateiname bleibt, der Inhalt
  ändert sich"
  ([Offline-Installationen und Spiegel](../seitenpakete/paketserver/offline-installationen-und-spiegel.md#einen-spiegel-aktuell-halten)).
  Ein Bundle, das zur Installationszeit herunterlädt, kann also ein neueres Paket installieren als das getestete.
- "Wenn ein Paket in der Installation vorhanden ist, verwendet der Schritt für den Seitentyp dieses, statt es
  herunterzuladen" ([Kickstarter](../../funktionen/6.0/kickstarter-cli.md#gut-zu-wissen)).

Ein Bundle, das jedes Mal dieselbe Website installieren muss, liefert daher die getesteten `.ezpkg`-Dateien aus und
importiert sie vor der Installation ([Offline-Bundles](offline-bundles.md)) oder zeigt auf einen Spiegel, den es
kontrolliert ([Ihr eigener Paketserver](ihr-eigener-paketserver.md)). Halten Sie die Prüfsumme jeder ausgelieferten
`.ezpkg` fest.

## Ihr eigenes Seitenpaket

Die Schritte stehen unter [Ein eigenes Seitenpaket erstellen](../seitenpakete/eigene/README.md): ein Paket forken,
Klassen, Inhalte und das Installer-Skript ändern, die `.ezpkg` neu bauen, versionieren und in einer Sandbox testen.
Bevor Sie es ausliefern, erfüllen Sie die
[Anforderungen an die Dokumentation](../seitenpakete/anforderungen/README.md): Pflichtfelder in `package.xml`,
Lizenz, Änderungsprotokoll, Versionen, Vorschaubilder, Abhängigkeiten, Validierung.

7x und die Exponential Foundation "ermutigen Sie ausdrücklich, es per Pull Request beim Repository hinter dem
Paketserver einzureichen, damit jede Installation es anbieten kann"
([Ihr Seitenpaket beitragen](../seitenpakete/ihr-seitenpaket-beitragen.md)).

## Checkliste

- [ ] Das Seitenpaket und jedes Paket, das es verlangt, sind gewählt und ihre Versionen festgehalten.
- [ ] Die `.ezpkg`-Dateien, die das Bundle installiert, sind die getesteten (ausgeliefert, gespiegelt oder per
      Prüfsumme kontrolliert).
- [ ] Demo-Inhalte gibt es nur in Bundles, die zur Vorführung gedacht sind.
- [ ] Die Lizenz des Pakets und die Lizenzen seiner Bilder sind bekannt
      ([Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md)).
- [ ] Das Paket enthält keine Benutzerkonten mit bekannten Passwörtern
      ([Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md)).
- [ ] `exp:install --package=<name> --dry-run` läuft im Build fehlerfrei durch.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../seitenpakete/README.md)
- [Seitenpakete für Hoster](../hosting/bereitstellung/seitenpakete-fuer-hoster.md)
- [Ihr eigener Paketserver](ihr-eigener-paketserver.md), [Offline-Bundles](offline-bundles.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
