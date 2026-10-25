---
title: Patterns in extension/ezupdate
audience: developer
category: standards
order: 461
summary: Every class, view, fetch function, operator and plugin point of extension/ezupdate with its design patterns.
keywords: extension/ezupdate, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/ezupdate

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/ezupdate` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (17)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `Exponential\Command\Extension\Ezupdate\Ezupdate` | class extends Command | [Command (console, scripts, cronjobs)](command.md) | `extension/ezupdate/classes/runnable/commands/php_ezupdate.php:25` | The code of extension/ezupdate/bin/php/ezupdate.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Browse` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/browse.php:22` | The code of extension/ezupdate/modules/update/browse.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Dashboard` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/dashboard.php:23` | The code of extension/ezupdate/modules/update/dashboard.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Fund` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/fund.php:27` | The code of extension/ezupdate/modules/update/fund.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Installed` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/installed.php:24` | The code of extension/ezupdate/modules/update/installed.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Job` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/job.php:23` | The code of extension/ezupdate/modules/update/job.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Package` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/package.php:22` | The code of extension/ezupdate/modules/update/package.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Packages` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/packages.php:22` | The code of extension/ezupdate/modules/update/packages.php, moved into a class (#207 stage 1). |
| `Exponential\View\Extension\Ezupdate\Update\Servers` | class extends ModuleView | [Module view (Page Controller)](module-view.md) | `extension/ezupdate/classes/runnable/views/update/servers.php:22` | The code of extension/ezupdate/modules/update/servers.php, moved into a class (#207 stage 1). |
| `eZUpdateComposerServers` | class | – | `extension/ezupdate/classes/ezupdatecomposerservers.php:15` | The Composer package servers of the installation: the "repositories" of its composer.json. |
| `eZUpdateFunding` | class | – | `extension/ezupdate/classes/ezupdatefunding.php:20` | – |
| `ezupdateInfo` | class | [Value object / struct](value-object.md) | `extension/ezupdate/ezinfo.php:7` | – |
| `eZUpdateInventory` | class | – | `extension/ezupdate/classes/ezupdateinventory.php:18` | – |
| `eZUpdateJob` | class | [Factory and static factory methods](factory.md) | `extension/ezupdate/classes/ezupdatejob.php:21` | A Composer run in the background, so a long update or install neither holds a web request open nor hits its time limit, and the page can show the out… |
| `eZUpdateManager` | class | [Facade / service](facade.md), [Singleton / instance()](singleton.md) | `extension/ezupdate/classes/ezupdatemanager.php:19` | Runs Composer for the installation this extension belongs to. |
| `eZUpdatePackageServers` | class | – | `extension/ezupdate/classes/ezupdatepackageservers.php:20` | The Exponential package servers (.ezpkg): the server the setup wizard uses (package.ini [RepositorySettings] RemotePackagesIndexURL, shown read-only)… |
| `eZUpdatePackagist` | class | – | `extension/ezupdate/classes/ezupdatepackagist.php:18` | Reads the packagist.org API to find and study packages before installing them: |
