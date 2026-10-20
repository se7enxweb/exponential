---
title: Eine Website auf Ihren Paketserver richten
audience: admin, developer, hosting
category: install-upgrade
order: 94
summary: Den Setup-Assistenten, den Kickstarter und exp:install Ihren Paketserver statt dem von 7x lesen lassen (eine Überschreibung von package.ini), Ihr Seitenpaket in kickstart.ini oder auf der Kommandozeile wählen, den Server zu ezupdate hinzufügen und wissen, was die Einstellung Vendor ändert.
keywords: RemotePackagesIndexURL, RemotePackagesIndexURLBase, package.ini, settings/override, kickstart.ini, Site_package, site_types, exp:install --package, ezupdate server-add, Vendor, Offline-index.xml, offline index.xml
type: help
---
# Eine Website auf Ihren Paketserver richten

Diese Seite ist für Administratoren, Entwickler und Hoster, die Websites von ihrem eigenen Paketserver installieren. Sie zeigt die eine
Einstellung, die entscheidet, wo Installationen suchen, wie Sie Ihr Seitenpaket für jede Installationsmethode nennen und wie ezupdate
denselben Server auf einer installierten Website findet.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihr Seitenpaket per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen: Einmal angenommen, braucht keine Installation eine dieser Einstellungen, um es
zu finden. Siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Die Einstellung, die entscheidet](#die-einstellung-die-entscheidet)
2. [Sie überschreiben](#sie-überschreiben)
3. [Ihr Seitenpaket nennen](#ihr-seitenpaket-nennen)
4. [Den Server zu ezupdate hinzufügen](#den-server-zu-ezupdate-hinzufügen)
5. [Die Einstellung Vendor](#die-einstellung-vendor)
6. [Vor der Installation prüfen](#vor-der-installation-prüfen)

## Die Einstellung, die entscheidet

`settings/package.ini`:

```ini
[RepositorySettings]
RemotePackagesIndexURL=https://exponential.packages.exponential.earth/exponential/6.0/6.0.15
RemotePackagesIndexURLBase=https://exponential.packages.exponential.earth/exponential
Vendor=7x
```

Der Schritt der Seitentypen (`kernel/setup/steps/ezstep_site_types.php`) liest `RemotePackagesIndexURL` und hängt `/index.xml` an. Wenn es
leer ist, nimmt er `RemotePackagesIndexURLBase` und hängt `/<major.minor>/<version>/` an, zum Beispiel `/6.0/6.0.15/`. Der Setup-Assistent,
der Kickstarter und `exp:install` führen alle diesen Schritt aus, sodass eine Einstellung alle drei umlenkt. Jeder Schlüssel steht auf
[Einstellungen des Paketservers](../paketserver/einstellungen-des-paketservers.md).

## Sie überschreiben

Bearbeiten Sie nie `settings/package.ini`; setzen Sie Ihren Wert in eine Überschreibung:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[RepositorySettings]
RemotePackagesIndexURL=https://packages.example.com/exponential/6.0/6.0.15
*/ ?>
```

Oder lassen Sie das Release im Pfad automatisch:

```ini
[RepositorySettings]
RemotePackagesIndexURL=
RemotePackagesIndexURLBase=https://packages.example.com/exponential
```

> **Warnung**
> Ihr Index ersetzt den Index von 7x; er wird ihm nicht hinzugefügt. Nur die Pakete, die Ihre `index.xml` aufführt, lassen sich
> herunterladen; führen Sie also jedes Paket auf, das Ihr Seitenpaket verlangt, mit `url`s, die auf den Server von 7x zeigen dürfen
> ([Einen eigenen Paketserver betreiben](einen-eigenen-paketserver-betreiben.md#eine-vollständige-indexxml)).

Für eine neue Installation muss die Überschreibung an ihrem Platz sein, bevor die Installation läuft: Legen Sie sie in den Checkout (oder das
Image, von dem Ihre Hoster starten), bevor der Setup-Assistent, der Kickstarter oder `exp:install` startet.

## Ihr Seitenpaket nennen

| Methode | Wie Sie das Paket wählen |
|---|---|
| Setup-Assistent | die Seite **Site Paket** führt jeden Eintrag `type="site"` Ihres Index mit seinem Vorschaubild auf ([5.3.8](../../../installation/05-setup-assistent.md#538-site-paket)) |
| Kickstarter | `kickstart.ini`, Abschnitt `[site_types]`, Schlüssel `Site_package` ([6.4](../../../installation/06-kickstarter.md#64-referenz-zu-kickstartini)) |
| Installation über die Konsole | `php bin/php/console exp:install --package=<name>` ([7.3](../../../installation/07-installation-ueber-die-konsole.md#73-optionen)) |

```ini
# kickstart.ini
[site_types]
Continue=true
Site_package=acme_site
```

```bash
php bin/php/console exp:install --package=acme_site --url=https://www.example.com
```

Der Kickstarter zieht den entfernten Eintrag des Index vor, nutzt aber ein Paket, das bereits importiert ist; um eine frische Kopie zu
erzwingen, entfernen Sie zuerst das importierte Verzeichnis ([6.4](../../../installation/06-kickstarter.md#64-referenz-zu-kickstartini)).

## Den Server zu ezupdate hinzufügen

Auf einer installierten Website führt ezupdate zuerst den Server aus `package.ini` auf (als `exponential`, **built in**) und dann die Server,
die Sie hinzufügen. Fügen Sie Ihren unter **Setup > Updates and packages > Package servers** oder aus der Shell hinzu:

```bash
php extension/ezupdate/bin/php/ezupdate.php server-add ezpkg acme https://packages.example.com/exponential/6.0/6.0.15
php extension/ezupdate/bin/php/ezupdate.php packages acme
```

Er wird nach `settings/override/ezupdate.ini.append.php` als `[PackageServerSettings] Servers[acme]=...` geschrieben. Ein dort hinzugefügter
Server wird von ezupdate gelesen und ab Exponential 6.0.15 auch vom Setup-Assistenten, dem Kickstarter und `exp:install`, nach dem Server aus
`package.ini` (`[RepositorySettings] RemotePackagesIndexSources[]`). Siehe [Paketserver in ezupdate](../admin-oberflaeche/paketserver-in-ezupdate.md).

## Die Einstellung Vendor

`Vendor` nennt das Repository-Verzeichnis, das der Setup-Assistent als sein eigenes behandelt (`eZPackage::systemRepositoryInformation()`):

- wenn sich `index.xml` nicht herunterladen lässt, kopiert der Assistent stattdessen `var/storage/packages/<Vendor>/index.xml`, so
  funktioniert eine Offline-Installation ([Offline-Installationen und Spiegel](../paketserver/offline-installationen-und-spiegel.md));
- die Paketliste markiert jedes Paket in diesem Repository als Installer-Quelle.

Wo ein heruntergeladenes Paket gespeichert wird, hängt nicht von dieser Einstellung ab: Ein Import nutzt den eigenen `<vendor>` des Pakets.
Wenn Ihre Pakete `<vendor>acme</vendor>` nutzen und Sie aus ihnen offline installieren wollen, setzen Sie `Vendor=acme` in derselben
Überschreibung und halten Ihre Offline-`index.xml` in `var/storage/packages/acme/`.

## Vor der Installation prüfen

```bash
php bin/php/console exp:install --package=acme_site --print     # the configuration, no network
php bin/php/console exp:install --package=acme_site --dry-run   # fetches the packages into a temporary repository
```

Der Probelauf lädt das Seitenpaket und seine Anforderungen nach `var/storage/packages/dryrun/` herunter, entfernt es wieder und endet mit
`Dry-run completed: database and remote packages verified.` Mehr auf [In einer Sandbox testen](in-einer-sandbox-testen.md).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Einen eigenen Paketserver betreiben](einen-eigenen-paketserver-betreiben.md)
- [Wie der Kickstarter und die Konsole Pakete nutzen](../grundlagen/wie-der-kickstarter-und-die-konsole-pakete-nutzen.md)
- [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
