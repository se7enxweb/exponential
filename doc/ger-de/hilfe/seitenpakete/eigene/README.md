---
title: Ein eigenes Seitenpaket erstellen
audience: developer, designer, partner, hosting
category: install-upgrade
order: 85
summary: Der Weg von einem vorhandenen Seitenpaket zu einem eigenen - forken, den Installer und die Inhalte ändern, die Archive neu bauen, sie versionieren, auf einem Paketserver veröffentlichen, eine Installation darauf richten, testen - und wo jeder Schritt erklärt wird.
keywords: eigenes Seitenpaket, angepasstes Seitenpaket, Fork, sevenx_multisite, cjw_multisite, build.sh, Paketserver, index.xml, RemotePackagesIndexURL, kickstart.ini, ezpm, Paketassistent, Pull Request, own site package, custom site package, fork, package server, package wizard, pull request
type: help
---
# Ein eigenes Seitenpaket erstellen

Dieser Abschnitt ist für Entwickler, Designer, Agenturen und Hoster, die wollen, dass neue Exponential-Installationen mit ihrer eigenen
Website beginnen: ihren eigenen Inhaltsklassen, Demo-Inhalten, Einstellungen, Rollen und ihrem Design, im Setup-Assistenten gewählt oder in
`kickstart.ini` genannt, so wie die Pakete von 7x. Er beantwortet: wo Sie beginnen, was Sie ändern, wie die Archive gebaut werden, wo sie
veröffentlicht werden und wie eine Installation sie findet.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihr Seitenpaket per Pull Request an
[se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) einzureichen: Ein
angenommenes Paket wird über das Standard-Verteilungssystem, den Setup-Assistenten, den Kickstarter und ezupdate auf jeder Installation
verfügbar. Wie das geht, steht auf [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Der Weg in Kürze](#der-weg-in-kürze)
2. [Die Seiten dieses Abschnitts](#die-seiten-dieses-abschnitts)
3. [Was Sie brauchen, bevor Sie beginnen](#was-sie-brauchen-bevor-sie-beginnen)
4. [Zwei Wege, das Ergebnis zu teilen](#zwei-wege-das-ergebnis-zu-teilen)

## Der Weg in Kürze

Ein Seitenpaket ist ein gewöhnliches Exponential-Paket vom Typ `site`, dessen `package.xml` die Pakete nennt, die es verlangt, und die
PHP-Dateien, die der Installer an festen Punkten des Schritts `CreateSites` ausführt
([Was ein Seiteninstallationspaket ist](../grundlagen/was-ein-seiteninstallationspaket-ist.md)). Ein eigenes zu erstellen heißt meist, ein
funktionierendes zu kopieren und zu ändern:

```text
fork a source repository      sevenx-ezpackage or cjw_multisite-ezpackage, packages/<name>/ per package
  -> rename and edit           package.xml, the installer script, the content classes and the demo content
  -> rebuild the archives      packages/build.sh: one <name>.ezpkg per package folder
  -> publish                   a static HTTPS folder with index.xml, the .ezpkg files and thumbnails/
  -> point an installation     package.ini [RepositorySettings] RemotePackagesIndexURL, kickstart.ini Site_package
  -> test in a sandbox         exp:install --dry-run, then a throwaway install
```

Die Seitenpakete von 7x wurden genau so gemacht. `cjw_multisite`, mit dem Release 6.0.15 veröffentlicht, ist ein Fork von
`sevenx_multisite`: Es behält `sevenx_classes` als Anforderung, bringt ein eigenes Demo-Inhaltspaket mit (`cjw_multisite_democontent`) und
eine eigene Installer-Klasse (`cjwMultiSiteInstaller` in `settings/cjw-multi-site-installer.php`) und liegt in einem eigenen Repository,
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage), laut seinem ersten Commit (7. Oktober 2026) wie
das Paket-Repository von 7x aufgebaut.

## Die Seiten dieses Abschnitts

| Seite | Was sie beantwortet |
|---|---|
| [Ein Seitenpaket forken](ein-seitenpaket-forken.md) | Von welchem Paket Sie ausgehen, was Sie umbenennen, welche Namen eindeutig sein müssen |
| [Das Installerskript ändern](das-installerskript-aendern.md) | Die Hook-Funktionen, die Installer-Klasse, ihre Schritte, die Tabellen der Erweiterungen, die sie befüllt |
| [Klassen und Inhalte ändern](klassen-und-inhalte-aendern.md) | Die Klassen- und Objektdateien, sie bearbeiten, sie von einer Testwebsite exportieren, Remote-IDs |
| [Mit build.sh neu bauen](mit-build-sh-neu-bauen.md) | Wie die Archive von 7x gebaut werden, was weggelassen wird, wie Sie das Ergebnis prüfen |
| [Pakete mit dem Assistenten erstellen](pakete-mit-dem-assistenten-erstellen.md) | Pakete für Klassen, Inhalte, Erweiterungen und Seitenstile aus der Administration |
| [Pakete mit ezpm erstellen](pakete-mit-ezpm-erstellen.md) | Dasselbe aus einer Shell, skriptfähig |
| [Eigene Pakete versionieren](eigene-pakete-versionieren.md) | `<version>`, `min-version`, die `version` im Index, Release-Ordner |
| [Einen eigenen Paketserver betreiben](einen-eigenen-paketserver-betreiben.md) | Der Ordneraufbau, `index.xml`, GitHub Pages oder jeder HTTPS-Server |
| [Eine Website auf Ihren Paketserver richten](eine-website-auf-ihren-paketserver-richten.md) | Überschreibung von `package.ini`, `kickstart.ini`, `exp:install`, ezupdate |
| [In einer Sandbox testen](in-einer-sandbox-testen.md) | Probeläufe, eine Wegwerf-Installation, was zu prüfen ist, Aufräumen |

## Was Sie brauchen, bevor Sie beginnen

- [ ] Eine Installation von Exponential 6.0, die Sie wegwerfen können (ein eigener Checkout mit SQLite genügt), nie die Produktionswebsite:
      Der Installer schreibt die Datenbank und die Einstellungen.
- [ ] git und ein GitHub-Konto (oder einen beliebigen Git-Host) für das Quell-Repository Ihrer Pakete.
- [ ] Eine Shell mit `tar` und `gzip`; `xmllint` hilft beim Prüfen von XML-Dateien.
- [ ] Zum Veröffentlichen: einen Ort, der statische Dateien über HTTPS ausliefert (GitHub Pages funktioniert, wie der Server von 7x zeigt).
- [ ] Eine Entscheidung über die Lizenz für Code, Dokumentation und Demo-Bilder
      ([Lizenzwahl](../../designer/veroeffentlichung/lizenzwahl-ueberblick.md)).

> **Hinweis**
> Der Installer liest Seitenpakete; wie sie gemacht wurden, ist ihm gleich. Alles in diesem Abschnitt funktioniert gleich, ob Sie die Archive
> mit `build.sh`, mit `ezpm` oder von Hand bauen, solange jedes Archiv die Prüfungen aus
> [Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md) besteht.

## Zwei Wege, das Ergebnis zu teilen

| Weg | Wer es bekommt | Was Sie betreiben |
|---|---|---|
| Pull Request an den Paketserver von 7x | jede Installation, in ihrem Setup-Assistenten, Kickstarter und ezupdate, ohne geänderte Einstellungen | Ihre Pakete werden geprüft und mit dem nächsten Release-Ordner veröffentlicht ([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)) |
| Ihr eigener Paketserver | Installationen, deren `package.ini` auf Ihren Server zeigt oder die ihn in ezupdate hinzufügen | einen statischen HTTPS-Ordner, den Sie aktuell halten ([Einen eigenen Paketserver betreiben](einen-eigenen-paketserver-betreiben.md)) |

Beides lässt sich kombinieren: auf dem eigenen Server testen, dann den Pull Request senden.

> **Tipp**
> Beginnen Sie mit der Checkliste der Anforderungen ([Die Checkliste der Anforderungen](../anforderungen/die-checkliste-der-anforderungen.md)),
> damit das Paket ab seiner ersten Version bereit zur Prüfung ist, und führen Sie ab dem ersten Commit einen Changelog.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Glossar der Seitenpakete](../glossar-der-seitenpakete.md)
- [Wie der Setup-Assistent Pakete nutzt](../grundlagen/wie-der-setup-assistent-pakete-nutzt.md)
- [sevenx_multisite](../sevenx-multisite/README.md) und [cjw_multisite](../cjw-multisite/README.md)
- [Pakete in der Administration](../admin-oberflaeche/README.md)
- [4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
