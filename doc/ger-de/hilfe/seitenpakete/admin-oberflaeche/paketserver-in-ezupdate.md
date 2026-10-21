---
title: Paketserver in ezupdate
audience: admin, developer, hosting
category: install-upgrade
order: 105
summary: Der Bereich der Exponential-Paketserver auf der Seite Package servers von ezupdate (/update/servers) - der eingebaute Server aus package.ini, eigene hinzufügen und entfernen, die Einstellung Servers[], die er schreibt, die Befehle server-add und server-remove, die Regeln für Namen und Adressen und wofür diese Server genutzt werden.
keywords: ezupdate, Paketserver, update/servers, Exponential-Paketserver, Servers[], PackageServerSettings, ezupdate.ini.append.php, server-add ezpkg, server-remove ezpkg, Server, eingebaut, https, update/manage, package servers, Exponential package servers, servers, built in
type: help
---
# Paketserver in ezupdate

Diese Seite ist für Administratoren, die wollen, dass eine installierte Website die Pakete von mehr als einem `.ezpkg`-Paketserver sieht:
dem Server von 7x, einem eigenen, dem einer Agentur. ezupdate führt diese Liste auf seiner Seite **Package servers** und auf der
Kommandozeile. Es ist eine Liste zum Durchsuchen und Abrufen, und ab Exponential 6.0.15 lesen sie auch der Setup-Assistent, der Kickstarter
und `exp:install`, nach dem Server aus `package.ini`.

## Inhalt

1. [Die Seite öffnen](#die-seite-öffnen)
2. [Der eingebaute Server](#der-eingebaute-server)
3. [Einen Server hinzufügen](#einen-server-hinzufügen)
4. [Einen Server entfernen](#einen-server-entfernen)
5. [Von der Kommandozeile](#von-der-kommandozeile)
6. [Wofür die Liste genutzt wird](#wofür-die-liste-genutzt-wird)

## Die Seite öffnen

**Setup > Updates and packages**, Reiter **Package servers** (`/update/servers`).

![Der Reiter Package servers von Updates and packages: die Liste der Composer-Server mit eingeschaltetem packagist.org und einem Formular zum Hinzufügen eines Servers, und die Liste der Exponential-Paketserver mit dem eingebauten Server exponential.packages.exponential.earth/exponential/6.0/6.0.15](../../../../eng-us/help/images/site-packages-ezupdate-servers.webp)

Die Seite hat zwei Listen:

| Bereich | Enthält | Gespeichert in |
|---|---|---|
| Composer-Server | wo Composer nach Code-Paketen sucht: die `repositories` der `composer.json` | `composer.json` |
| Exponential-Paketserver | Server für `.ezpkg`-Pakete (Website-Designs, Inhaltsklassen, Demo-Inhalte) im Format `index.xml`, das der Setup-Assistent liest | `settings/override/ezupdate.ini.append.php` |

Diese Seite behandelt die zweite Liste. Die Seite braucht die Richtlinie `update/manage` (`extension/ezupdate/modules/update/module.php`);
ezupdate wird mit `ActiveExtensions[]=ezupdate` eingeschaltet
([ezupdate: Updates und Pakete in der Administration](../../../funktionen/6.0/erweiterungen/ezupdate.md)).

## Der eingebaute Server

Die erste Zeile ist immer der Server, den der Setup-Assistent nutzt, mit dem Namen `exponential` und als **built in** markiert (Titel
`package.ini`). ezupdate ermittelt ihn so wie der Assistent (`eZUpdatePackageServers::builtinIndexURL()`):
`package.ini [RepositorySettings] RemotePackagesIndexURL` oder, wenn das leer ist, `RemotePackagesIndexURLBase` mit angehängtem
`x.y/x.y.z` (`6.0/6.0.15`; vor ezupdate 1.1.24 hängte es `6.0/6.0.15stable` an, einen Ordner, den der Paketserver nicht hat). Auf einer
Standardinstallation von 6.0.15 ist das `https://exponential.packages.exponential.earth/exponential/6.0/6.0.15`.

Er kann hier nicht entfernt werden, und sein Name ist reserviert: Ändern Sie ihn in `package.ini` (einer Überschreibung), wenn er sich
ändern muss ([Eine Website auf Ihren Paketserver richten](../eigene/eine-website-auf-ihren-paketserver-richten.md)).

## Einen Server hinzufügen

Geben Sie unter **Add package server** einen **Name** und eine **Address** an und senden Sie ab.

| Feld | Regel (`eZUpdatePackageServers::validate()`) | Meldung bei Fehlschlag |
|---|---|---|
| Name | Buchstaben, Ziffern, `.`, `-`, `_`, beginnend mit einem Buchstaben oder einer Ziffer, höchstens 64 Zeichen | `The name may use letters, digits, dot, dash and underscore.` |
| Name | nicht `exponential` | `This name is reserved for the server in package.ini.` |
| Address | eine URL `https://` | `The address must be an https:// URL.` |

Die Adresse ist der Ordner, der `index.xml` enthält, ohne `/index.xml` und ohne abschließenden Schrägstrich (einer wird entfernt), zum
Beispiel `https://packages.example.com/exponential/6.0/6.0.15`. Wird ein vorhandener Name hinzugefügt, wird seine Adresse ersetzt. Bei Erfolg
meldet die Seite `The package server was added.`

Der Server wird in die Überschreibungsdatei geschrieben:

```ini
<?php /* #?ini charset="utf-8"?
[PackageServerSettings]
Servers[acme]=https://packages.example.com/exponential/6.0/6.0.15
*/ ?>
```

Kann die Datei nicht geschrieben werden, meldet die Seite `Could not write settings/override/ezupdate.ini.append.php.` Die Datei wird
direkt gelesen, ohne den INI-Cache, sodass die Seite und die Kommandozeile immer dieselbe Liste zeigen.

## Einen Server entfernen

**Remove** in einer Zeile fragt `Remove this package server?` und löscht seine Zeile aus der Überschreibungsdatei
(`The package server was removed.`). Bereits von ihm abgerufene Pakete bleiben im lokalen Repository.

## Von der Kommandozeile

```bash
php extension/ezupdate/bin/php/ezupdate.php servers
php extension/ezupdate/bin/php/ezupdate.php server-add ezpkg acme https://packages.example.com/exponential/6.0/6.0.15
php extension/ezupdate/bin/php/ezupdate.php server-remove ezpkg acme
```

`servers` listet beide Arten auf; der eingebaute wird mit `(package.ini)` gezeigt. Es gelten dieselben Regeln wie auf der Seite
(`extension/ezupdate/doc/COMMAND_LINE.md`).

## Wofür die Liste genutzt wird

| Genutzt von | Für |
|---|---|
| der Seite **Packages** von ezupdate und dem Befehl `packages` | die Pakete eines Servers auflisten |
| **Fetch** von ezupdate und dem Befehl `fetch` | eines ins lokale Repository herunterladen |
| dem Setup-Assistenten, dem Kickstarter, `exp:install` | ab 6.0.15: weitere Server, nach dem aus `package.ini` gelesen (`[RepositorySettings] RemotePackagesIndexSources[]=ezupdate.ini;PackageServerSettings;Servers`, der Standard). Vor 6.0.15 lasen sie nur `package.ini [RepositorySettings]` |

Einen Server hier hinzuzufügen lässt Administratoren also seine Pakete in eine installierte Website abrufen, und die Setup-Werkzeuge
derselben Installation bieten seine Seitenpakete neben denen des Servers aus `package.ini` an (ein Paket, das beide auflisten, wird vom
Server aus `package.ini` genommen). Für eine neue Installation, die noch keine Liste von ezupdate hat, richten Sie `package.ini` auf den
Server (`RemotePackagesIndexURL` oder `AdditionalRemotePackagesIndexURLs[]`, um auch den Server von 7x zu behalten), oder reichen Sie die
Pakete beim Server von 7x ein ([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)).

> **Hinweis**
> Mit `site.ini [ConfigurationSettings] ManagedByDeployment=enabled` werden Einstellungsdateien nicht aus einer Web-Anfrage geschrieben,
> sodass das Hinzufügen oder Entfernen eines Servers im Browser scheitert; führen Sie die Liste stattdessen im Ausrollen.

## Verwandte Seiten

- [Pakete in der Administration](README.md)
- [Pakete mit ezupdate abrufen](pakete-mit-ezupdate-abrufen.md)
- [ezupdate: Updates und Pakete in der Administration](../../../funktionen/6.0/erweiterungen/ezupdate.md)
- [Einstellungen des Paketservers](../paketserver/einstellungen-des-paketservers.md)
- [Einen eigenen Paketserver betreiben](../eigene/einen-eigenen-paketserver-betreiben.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
