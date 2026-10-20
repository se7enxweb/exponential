# Eine Extension kann ein Kernel-Modul ersetzen

Diese Seite ist für Entwickler, die ändern müssen, wie ein ganzes Modul arbeitet. Ein Modul ist eine Menge von Ansichten
hinter einer URL (`content/view`, `user/login`, `shop/basket`). Bis Januar 2024 gewann das Modul des Kernels immer dann,
wenn eine Extension ein Modul mit demselben Namen anbot; eine Extension konnte also Module hinzufügen, aber keines
ersetzen. Jetzt werden die Modul-Repositorys so durchsucht, dass **Extensions zuerst kommen**: Legen Sie in Ihrer
Extension ein Modul mit demselben Namen an, und es wird statt des Kernel-Moduls verwendet.

## Wann Sie es verwenden

Ersetzen Sie ein ganzes Modul, wenn eine Ansicht mehr braucht, als eine Template-Überschreibung oder ein Ereignis leisten
kann, zum Beispiel einen Anmeldeablauf mit einem zusätzlichen Schritt oder einen geänderten Warenkorb. Für kleine
Änderungen sind die [RAD-Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md) und Ansichtsklassen
([Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)) vorzuziehen.

## So geht es

1. Legen Sie das Modul in Ihrer Extension an, mit demselben Aufbau wie ein Kernel-Modul:

   ```
   extension/myext/modules/user/module.php
   extension/myext/modules/user/login.php
   ```

2. Sorgen Sie dafür, dass die Extension aktiv ist (`settings/override/site.ini.append.php`,
   `[ExtensionSettings] ActiveExtensions[]=myext`) und sich selbst als Modul-Repository einträgt. Eine Extension tut das
   in ihrer eigenen `settings/module.ini.append.php`:

   ```ini
   [ModuleSettings]
   ExtensionRepositories[]=myext
   ```

3. Leeren Sie die Caches:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

4. Öffnen Sie die URL (zum Beispiel `/user/login`). Die Ansicht der Extension läuft.

**Grenze:** Eine Ansicht, die Sie in Ihrer Kopie des Moduls nicht definieren, wird nicht geerbt. Das ganze Modul wird
ersetzt, kopieren Sie also jede Ansicht, die Sie noch brauchen.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/module.ini` | `ModuleSettings` | `ModuleRepositories[]` | `kernel`, `kernel/private/modules` | Installation |
| `settings/module.ini` | `ModuleSettings` | `ExtensionRepositories[]` | leer; jede aktive Extension trägt sich selbst ein | Extension |

## Wie es funktioniert

`eZModule::activeModuleRepositories()` (`lib/ezutils/classes/ezmodule.php`) baut die Liste der Modulverzeichnisse aus
`ModuleRepositories[]` und danach aus `ExtensionRepositories[]` auf. Seit dem 24. Januar 2024 (`c6b90d723d`) kehrt es
die zusammengesetzte Liste um, sodass die Extension-Repositorys zuerst durchsucht werden. Das erste Verzeichnis, das ein
Modul mit dem angefragten Namen enthält, gewinnt.

## Eine verwandte Korrektur für mehr Robustheit

Ein Ansichtsskript einer unvollständigen Extension (die Extension ownerchange war eine) kann etwas zurückgeben, das nicht
das erwartete Array ist. Seit Oktober 2024 (`884f21e963`) setzt `eZModule` die Navigationsschlüssel
(`is_default_navigation_part`, `navigation_part`) im Ergebnis nur, wenn es ein Array ist (im aktuellen Stand lautet die
Prüfung `!is_string`). Eine Ansicht, die einen anderen Typ zurückgibt, endet nicht mehr in einem fatalen Fehler,
während die Seite zusammengesetzt wird.

## Verwandte Seiten

- [Extensions in mehr als einem Ordner](../../../eng-us/features/6.0/additional-extension-directories.md)
- [Ladereihenfolge der Extensions](../../../eng-us/features/6.0/extension-loading-order.md)
- [Verhaltensänderungen der Legacy-Extensions](../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Changelogs: [6.0.1](../../../changelogs/6.0/6.0.1.md) (Reihenfolge der Module), [6.0.5](../../../changelogs/6.0/6.0.5.md) (Korrektur für Robustheit)
- Chronik: [Januar 2024, zweite Hälfte](../../../eng-us/history/2024/2024-01b.md), [September 2024](../../../eng-us/history/2024/2024-09.md), [Oktober 2024](../../../eng-us/history/2024/2024-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
