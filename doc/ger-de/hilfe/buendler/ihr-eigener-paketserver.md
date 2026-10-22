---
title: Ihr eigener Paketserver für ein Bundle
audience: bundler, hosting, developer
category: bundling
order: 170
summary: Wann ein Bundle einen eigenen Paketserver oder einen Spiegel des öffentlichen braucht, die Einstellungen, die eine Installation darauf verweisen (RemotePackagesIndexURL, RemotePackagesIndexURLBase, Vendor, ezupdate-Server), die Warnung, dass Ihr Index den öffentlichen ersetzt, und wie Sie ihn aktuell halten.
keywords: Paketserver, Spiegel, Bundle, aktuell halten, package server, mirror, index.xml, RemotePackagesIndexURL, RemotePackagesIndexURLBase, AdditionalRemotePackagesIndexURLs, Vendor, ezupdate, server-add, package.ini, override, bundle
type: help
---
# Ihr eigener Paketserver für ein Bundle

Diese Seite richtet sich an Bündler, die Seitenpakete von einem Server installieren, den sie kontrollieren: einem
Spiegel des öffentlichen Paketservers oder einem Server mit eigenen Paketen. Die Referenzseiten sind
[Einen eigenen Paketserver betreiben](../seitenpakete/eigene/einen-eigenen-paketserver-betreiben.md) und
[Eine Website auf Ihren Paketserver zeigen lassen](../seitenpakete/eigene/eine-website-auf-ihren-paketserver-richten.md).

## Inhalt

1. [Wann Sie einen brauchen](#wann-sie-einen-brauchen)
2. [Was ein Paketserver ist](#was-ein-paketserver-ist)
3. [Ein Bundle darauf verweisen](#ein-bundle-darauf-verweisen)
4. [Ihn zu ezupdate hinzufügen](#ihn-zu-ezupdate-hinzufügen)
5. [Aktuell halten](#aktuell-halten)

## Wann Sie einen brauchen

| Situation | Paketserver |
|---|---|
| Das Bundle installiert nur die öffentlichen Pakete von 7x und darf deren Korrekturen folgen | keiner: Verwenden Sie den öffentlichen Server |
| Das Bundle muss genau die getesteten Pakete installieren | ein Spiegel, den Sie kontrollieren, oder Sie liefern die Dateien aus ([Offline-Bundles](offline-bundles.md)) |
| Das Bundle installiert Ihr eigenes Seitenpaket | Ihr eigener Server, oder Sie liefern die Datei aus |
| Viele Installationen in einem Netzwerk ohne Internetzugang | ein Spiegel im Netzwerk |

## Was ein Paketserver ist

"Der **Paketserver** ist eine statische HTTPS-Website ... Er hat einen Ordner je Exponential-Release mit einer
`index.xml` und den `.ezpkg`-Dateien" ([Seitenpakete: hier beginnen](../seitenpakete/README.md#die-kurzfassung)).
Jeder Webserver, der statische Dateien ausliefert, genügt. Das Format von `index.xml` steht unter
[Das Format von index.xml](../seitenpakete/paketserver/das-format-index-xml.md).

## Ein Bundle darauf verweisen

Die Einstellung steht in `settings/package.ini [RepositorySettings]`; ein Bundle überschreibt sie in
`settings/override/package.ini.append.php`, **bevor** der Installer läuft:

```ini
<?php /* #?ini charset="utf-8"?
[RepositorySettings]
RemotePackagesIndexURL=https://packages.example.com/exponential/6.0/6.0.15
*/ ?>
```

oder mit automatisch ergänztem Release-Pfad:

```ini
[RepositorySettings]
RemotePackagesIndexURL=
RemotePackagesIndexURLBase=https://packages.example.com/exponential
```

Ist `RemotePackagesIndexURL` leer, "nimmt der Schritt für die Seitentypen `RemotePackagesIndexURLBase` und hängt
`/<major.minor>/<version>/` an". `settings/package.ini` hat außerdem `AdditionalRemotePackagesIndexURLs[]`, das nach
`RemotePackagesIndexURL` gelesen wird; siehe
[Einstellungen des Paketservers](../seitenpakete/paketserver/einstellungen-des-paketservers.md) für jeden Schlüssel.

> **Warnung**
> "Ihr Index ersetzt den Index von 7x; er wird nicht hinzugefügt. Nur die Pakete, die Ihre `index.xml` aufführt,
> lassen sich herunterladen, führen Sie also jedes Paket auf, das Ihr Seitenpaket verlangt"
> ([Eine Website auf Ihren Paketserver zeigen lassen](../seitenpakete/eigene/eine-website-auf-ihren-paketserver-richten.md#sie-überschreiben)).

Wenn Ihre Pakete einen eigenen Anbieter verwenden (`<vendor>example</vendor>`) und Sie offline daraus installieren
möchten, setzen Sie `Vendor=example` in demselben Override
([Die Einstellung Vendor](../seitenpakete/eigene/eine-website-auf-ihren-paketserver-richten.md#die-einstellung-vendor)).

## Ihn zu ezupdate hinzufügen

Installierte Websites suchen über ezupdate nach aktualisierten Paketen. Fügen Sie Ihren Server unter
**Setup > Updates and packages > Package servers** hinzu, oder:

```bash
php extension/ezupdate/bin/php/ezupdate.php server-add ezpkg example https://packages.example.com/exponential/6.0/6.0.15
php extension/ezupdate/bin/php/ezupdate.php packages example
```

Er wird in `settings/override/ezupdate.ini.append.php` geschrieben. "ezupdate braucht eine `https://`-Adresse"
([Offline-Installationen und Spiegel](../seitenpakete/paketserver/offline-installationen-und-spiegel.md#installationen-auf-den-spiegel-richten)).

## Aktuell halten

- Ein Spiegel des öffentlichen Servers folgt dessen Korrekturen nur, wenn Sie ihn aktualisieren: `git pull` in einem
  Git-Spiegel und die Adressen in `index.xml` erneut umschreiben, oder erneut herunterladen und die Prüfsummen
  vergleichen ([Einen Spiegel aktuell halten](../seitenpakete/paketserver/offline-installationen-und-spiegel.md#einen-spiegel-aktuell-halten)).
- Entscheiden Sie je Bundle, ob der Spiegel jeder Korrektur folgt oder auf einem getesteten Stand bleibt; "ein Hoster,
  der viele Kunden-Websites installiert, testet meist zuerst".
- Prüfen Sie vor jedem Bundle-Release, dass jede Adresse des Index mit `200` antwortet.
- Prüfen Sie den Installer gegen den Server mit `exp:install --package=<name> --dry-run`, das "das Seitenpaket und
  seine Abhängigkeiten nach `var/storage/packages/dryrun/` herunterlädt".

## Verwandte Seiten

- [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md)
- [Offline-Bundles](offline-bundles.md)
- [Paketserver in ezupdate](../seitenpakete/admin-oberflaeche/paketserver-in-ezupdate.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
