---
title: Muster in extension/ezupdate
audience: developer
category: standards
order: 457
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezupdate mit den Entwurfsmustern.
keywords: extension/ezupdate, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezupdate

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezupdate` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (17)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Command\Extension\Ezupdate\Ezupdate` | class extends Command | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/ezupdate/classes/runnable/commands/php_ezupdate.php:25` | Der Code von extension/ezupdate/bin/php/ezupdate.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Browse` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/browse.php:22` | Der Code von extension/ezupdate/modules/update/browse.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Dashboard` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/dashboard.php:23` | Der Code von extension/ezupdate/modules/update/dashboard.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Fund` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/fund.php:27` | Der Code von extension/ezupdate/modules/update/fund.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Installed` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/installed.php:24` | Der Code von extension/ezupdate/modules/update/installed.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Job` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/job.php:23` | Der Code von extension/ezupdate/modules/update/job.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Package` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/package.php:22` | Der Code von extension/ezupdate/modules/update/package.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Packages` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/packages.php:22` | Der Code von extension/ezupdate/modules/update/packages.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezupdate\Update\Servers` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezupdate/classes/runnable/views/update/servers.php:22` | Der Code von extension/ezupdate/modules/update/servers.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZUpdateComposerServers` | class | – | `extension/ezupdate/classes/ezupdatecomposerservers.php:15` | Die Composer-Paketserver der Installation: die "repositories" ihrer composer.json. |
| `eZUpdateFunding` | class | – | `extension/ezupdate/classes/ezupdatefunding.php:20` | – |
| `ezupdateInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezupdate/ezinfo.php:7` | – |
| `eZUpdateInventory` | class | – | `extension/ezupdate/classes/ezupdateinventory.php:18` | – |
| `eZUpdateJob` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezupdate/classes/ezupdatejob.php:21` | Ein Composer-Lauf im Hintergrund, damit ein langes Update oder eine lange Installation weder eine Web-Anfrage offen hält noch an deren Zeitlimit stößt und die Seite die Aus… |
| `eZUpdateManager` | class | [Fassade / Dienst](fassade.md), [Singleton / instance()](singleton.md) | `extension/ezupdate/classes/ezupdatemanager.php:19` | Führt Composer für die Installation aus, zu der diese Extension gehört. |
| `eZUpdatePackageServers` | class | – | `extension/ezupdate/classes/ezupdatepackageservers.php:20` | Die Exponential-Paketserver (.ezpkg): der Server, den der Setup-Assistent verwendet (package.ini [RepositorySettings] RemotePackagesIndexURL, nur lesbar angezeigt)… |
| `eZUpdatePackagist` | class | – | `extension/ezupdate/classes/ezupdatepackagist.php:18` | Liest die API von packagist.org, um Pakete vor der Installation zu finden und zu untersuchen: |
