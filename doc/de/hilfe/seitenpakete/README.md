---
title: "Seitenpakete: hier beginnen"
audience: admin, developer, hosting, designer
category: install-upgrade
order: 1
featured: true
summary: Der Einstieg in die Hilfe zu Seitenpaketen. Was ein Seiteninstallationspaket ist, der Paketserver, der sie veröffentlicht, was sevenx_multisite und cjw_multisite installieren, die Dokumentation, die ein Paket mitbringen muss, wie Sie eigene erstellen und veröffentlichen und wie Sie Pakete in der Administration und in ezupdate handhaben.
keywords: Seitenpaket, Seiteninstallationspaket, ezpkg, package.xml, Paketserver, exponential.packages.exponential.earth, index.xml, sevenx_multisite, cjw_multisite, Setup-Assistent, Kickstarter, exp:install, ezupdate, Paketserver, RemotePackagesIndexURL, beitragen, Pull Request, site package, site installer package, package server, setup wizard, kickstarter, package servers, contribute, pull request
type: help
---
# Seitenpakete: hier beginnen

Wenn Exponential installiert wird, entsteht die neue Website nicht allein aus Code. Sie wird aus **Seiteninstallationspaketen** gebaut:
Archiven (`.ezpkg`), die Inhaltsklassen, Inhalte, Einstellungen, Rollen und Installationsskripte enthalten und die der Setup-Assistent,
der Kickstarter und `exp:install` von einem **Paketserver** lesen. Dieser Abschnitt erklärt all das, vom Dateiformat bis zur
Veröffentlichung eines eigenen Pakets.

Er ist geschrieben für Administratoren, die Websites installieren, Entwickler, die Seitenpakete bauen oder ändern, Hosting-Unternehmen,
die entscheiden, was eine neue Kunden-Website enthält, und Designer, die einen Seitenstil oder eine ganze Startseite ausliefern. Jede
Einstellung, jede Datei und jede URL auf diesen Seiten wurde gegen den Code, die Pakete und den laufenden Paketserver geprüft.

> **Tipp**
> Sie haben ein Seitenpaket gebaut? 7x und die Exponential Foundation ermutigen Sie ausdrücklich, es per Pull Request an das Repository
> hinter dem Paketserver einzureichen, damit jede Installation es anbieten kann:
> [Ihr Seitenpaket beitragen](ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Der Abschnitt auf einen Blick](#der-abschnitt-auf-einen-blick)
2. [Wo Sie beginnen](#wo-sie-beginnen)
3. [Die Kurzfassung](#die-kurzfassung)
4. [Die Seiten anderswo, auf denen dieser Abschnitt aufbaut](#die-seiten-anderswo-auf-denen-dieser-abschnitt-aufbaut)
5. [Verwandte Seiten](#verwandte-seiten)

## Der Abschnitt auf einen Blick

| Teil | Was er beantwortet |
|---|---|
| [Grundlagen der Seitenpakete](grundlagen/README.md) | Was ein Seiteninstallationspaket ist, die Datei `.ezpkg`, `package.xml`, Pakettypen, Installationselemente, Abhängigkeiten, Installationsskripte und wie der Setup-Assistent, der Kickstarter und die Konsole Pakete nutzen |
| [Der Paketserver](paketserver/README.md) | Was der Paketserver ist, seine Geschichte, sein Inhalt, das Format `index.xml`, ihn im Browser und auf GitHub durchsuchen, seine Einstellungen, Offline-Installationen und Spiegel |
| [Anforderungen an die Dokumentation](anforderungen/README.md) | Die Checkliste, die jedes Seitenpaket erfüllen muss: Pflichtfelder in `package.xml`, Lizenz, Changelog, Versionen, Vorschaubilder, Abhängigkeiten und Validierung |
| [sevenx_multisite](sevenx-multisite/README.md) | Das Standard-Seitenpaket von Exponential 6.0.15: woraus es besteht, was sein Installer tut und was Sie nach der Installation haben |
| [cjw_multisite](cjw-multisite/README.md) | Das Startseiten-Paket von CJW: seine Teile, sein Installer, seine Sprachen und wie es sich mit sevenx_multisite vergleicht |
| [Ein eigenes Seitenpaket erstellen](eigene/README.md) | Eigene Pakete forken, ändern, neu bauen, versionieren und veröffentlichen, auf Ihrem eigenen Paketserver oder auf dem gemeinsamen |
| [Pakete in der Administration](admin-oberflaeche/README.md) | **Setup > Pakete**, Importieren und Installieren, der Paketassistent und die Paketserver und Abrufe von ezupdate |
| [Ihr Seitenpaket beitragen](ihr-seitenpaket-beitragen.md) | Ihr Paket per Pull Request in die Standardauslieferung bringen |
| [Glossar](glossar-der-seitenpakete.md) | Die Begriffe dieses Abschnitts an einem Ort |

## Wo Sie beginnen

| Sie wollen ... | Lesen Sie |
|---|---|
| verstehen, was der Installer installiert | [Was ein Seiteninstallationspaket ist](grundlagen/was-ein-seiteninstallationspaket-ist.md), dann [sevenx_multisite](sevenx-multisite/README.md) |
| sehen, was auf dem Paketserver liegt | [Was auf dem Paketserver liegt](paketserver/was-auf-dem-paketserver-liegt.md) und [Den Paketserver im Browser durchsuchen](paketserver/den-paketserver-im-browser-durchsuchen.md) |
| ohne Internetzugang installieren | [Offline-Installationen und Spiegel](paketserver/offline-installationen-und-spiegel.md) |
| das Standard-Seitenpaket für Ihre Websites ändern | [Ein Seitenpaket forken](eigene/ein-seitenpaket-forken.md) und [Das Installerskript ändern](eigene/das-installerskript-aendern.md) |
| einen eigenen Paketserver betreiben | [Einen eigenen Paketserver betreiben](eigene/einen-eigenen-paketserver-betreiben.md) und [Eine Website auf Ihren Paketserver richten](eigene/eine-website-auf-ihren-paketserver-richten.md) |
| Paketserver in der Administration verwalten | [Paketserver in ezupdate](admin-oberflaeche/paketserver-in-ezupdate.md) |
| Ihr Paket mit allen teilen | [Ihr Seitenpaket beitragen](ihr-seitenpaket-beitragen.md) |

## Die Kurzfassung

- Ein **Paket** ist ein Ordner mit einer `package.xml` und den Dateien, die sie nennt, ausgeliefert als eine mit gzip komprimierte
  tar-Datei mit der Endung `.ezpkg`. Ein **Seitenpaket** ist ein Paket vom Typ `site`; es verlangt andere Pakete (Inhaltsklassen,
  Inhalte) und kann Installationsskripte mitbringen, die Siteaccesses, Erweiterungen, Rollen und Voreinstellungen einrichten.
- Der **Paketserver** ist eine statische HTTPS-Website,
  [exponential.packages.exponential.earth](https://exponential.packages.exponential.earth/), von GitHub Pages aus
  [se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) veröffentlicht.
  Er hat einen Ordner pro Release von Exponential mit einer `index.xml` und den `.ezpkg`-Dateien.
- Eine Installation findet den Server über `settings/package.ini [RepositorySettings] RemotePackagesIndexURL` (für 6.0.15:
  `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`). Heruntergeladene Pakete werden in
  `var/storage/packages/<vendor>/` aufbewahrt, Hersteller `7x`.
- Der Index von 6.0.15 bietet zwölf Seitenpakete an. Der Standard ist **sevenx_multisite** (7x MultiSite); **cjw_multisite** ist die
  Startseite von CJW mit einer englischen und einer deutschen Website.
- Die Quellen der Pakete liegen in eigenen GitHub-Repositorys
  ([se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage),
  [se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) und andere); ein Build-Skript macht aus jedem
  Ordner eine `.ezpkg`.
- Sie können eigene Versionen erstellen, sie auf einem eigenen Server veröffentlichen und Ihre Installationen in INI-Einstellungen oder in
  den Paketservern von ezupdate darauf richten; oder, besser für alle, sie zum gemeinsamen Server beitragen.

## Die Seiten anderswo, auf denen dieser Abschnitt aufbaut

Diese Seiten behandeln Teile des Themas bereits ausführlich; dieser Abschnitt verlinkt sie, statt sie zu wiederholen.

| Seite | Was sie behandelt |
|---|---|
| [Anleitung zu Paketen](../../../en/us/guides/packages.md) | **Setup > Pakete** Seite für Seite: die Liste, die Seite eines Pakets, Prüfungen beim Hochladen, Installation, Deinstallation, Entfernen, Berechtigungen |
| [Eine Installationsmethode wählen, 4.3 Seitenpakete](../../../en/us/install/04-choosing-an-install-method.md) | Welches Installations-Frontend Pakete wie nutzt |
| [Der Setup-Assistent](../../../en/us/install/05-setup-wizard.md), [Der Kickstarter](../../../en/us/install/06-kickstarter.md), [Die Installation über die Konsole](../../../en/us/install/07-console-install.md) | Die drei Installations-Frontends |
| [Der Paketassistent](../../../en/us/features/6.0/package-wizard.md) | Pakete in der Administration erstellen |
| [ezpm, der Paketmanager auf der Kommandozeile](../../../en/us/features/6.0/ezpm-package-manager-cli.md) | `ezpm.php` |
| [Das JSON-Paketformat](../../../en/us/bc/6.0/package-json-format.md) | Das zweite Speicherformat eines Pakets |
| [ezupdate](../../../en/us/features/6.0/extensions/ezupdate.md) | Updates, Composer-Pakete und Paketserver in der Administration |
| [Seitenpakete für Hoster](../hosting/bereitstellung/seitenpakete-fuer-hoster.md) | Ein Seitenpaket pro Hosting-Tarif wählen |
| [Seitenpakete und die mitgelieferten Themes](../../../en/us/help/creators/website-creators/site-packages-and-shipped-themes.md) | Die Seitenpakete aus der Sicht eines Website-Erstellers |
| [Was ein Exponential-Paket ist](../../../en/us/help/designers/extensions/what-an-exponential-package-is.md) und [Seitenstil-Paket](../../../en/us/help/designers/extensions/site-style-package.md) | Pakete aus der Sicht eines Designers |
| [Verteilung für Designer](../../../en/us/help/designers/distribution/distribution-start-here.md) | Themes und Erweiterungen veröffentlichen |
| [Mit Git aktualisieren](../../../en/us/help/update-with-git.md) | Den Code einer Installation aktuell halten |

## Verwandte Seiten

- [Ihr Seitenpaket beitragen](ihr-seitenpaket-beitragen.md)
- [Glossar](glossar-der-seitenpakete.md)
- [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md)
- [Wer ist 7x?](../../../en/us/help/who-is-7x.md) und [Wer ist die Exponential Foundation?](../../../en/us/help/who-is-the-exponential-foundation.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
