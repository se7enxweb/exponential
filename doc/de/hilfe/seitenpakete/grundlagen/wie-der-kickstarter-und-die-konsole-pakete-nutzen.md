---
title: "Wie der Kickstarter und die Konsole Pakete nutzen"
audience: admin, developer, hosting
category: install-upgrade
order: 19
summary: Wie der Kickstarter (kickstart.ini [site_types] Site_package) und die Installation über die Konsole (exp:install --package) ein Seitenpaket wählen, abrufen und installieren, wie sie sich vom Web-Assistenten unterscheiden, wie Probeläufe Pakete prüfen, ohne etwas zu schreiben, und wie Sie ein Paket installieren, das nicht auf dem Paketserver liegt.
keywords: Kickstarter, kickstart.ini, site_types, Site_package, exp:install, --package, exp:kickstarter, Probelauf, Repository dryrun, Offline-Installation, sevenx_multisite, lokales Paket, forceDownload, Schlüssel Packages, kickstarter, dry run, dryrun repository, offline install, local package, Packages key
type: help
---
# Wie der Kickstarter und die Konsole Pakete nutzen

Diese Seite ist für Administratoren und Hosting-Teams, die von der Kommandozeile installieren, in Skripten, CI oder Containern. Sie
beantwortet: wie Sie das Seitenpaket benennen, woher es abgerufen wird, wie sich das vom Web-Assistenten unterscheidet und wie Sie die
Pakete vor einem echten Lauf prüfen.

## Inhalt

1. [Dieselben Schritte, kein Browser](#dieselben-schritte-kein-browser)
2. [Das Seitenpaket benennen](#das-seitenpaket-benennen)
3. [Wie das Paket gefunden wird](#wie-das-paket-gefunden-wird)
4. [Unterschiede zum Web-Assistenten](#unterschiede-zum-web-assistenten)
5. [Probeläufe prüfen die Pakete](#probeläufe-prüfen-die-pakete)
6. [Ein Paket installieren, das nicht auf dem Server liegt](#ein-paket-installieren-das-nicht-auf-dem-server-liegt)
7. [Eine frische Kopie holen](#eine-frische-kopie-holen)
8. [Verwandte Seiten](#verwandte-seiten)

## Dieselben Schritte, kein Browser

Der Kickstarter (`php bin/php/console exp:kickstarter run`, auch `php bin/php/kickstarter.php run`) führt die Schritte aus
`kernel/setup/steps/ezstep_data.php` von Welcome bis Final aus, und jeder Schritt liest seinen Abschnitt der `kickstart.ini` im
Wurzelverzeichnis der Installation. `exp:install` (`bin/php/install.php`) macht aus seinen Optionen dieselben Abschnitte und führt dieselben
Schritte aus. Der Paketcode ist also der Code des Web-Assistenten, beschrieben in
[Wie der Setup-Assistent Pakete nutzt](wie-der-setup-assistent-pakete-nutzt.md); nur die Wahl des Pakets und die Regeln für das Abrufen
unterscheiden sich.

## Das Seitenpaket benennen

**kickstart.ini:**

```ini
[site_types]
Continue=true
Site_package=sevenx_multisite
```

`Site_package` ist erforderlich; sein Wert ist der `<name>` eines Seitenpakets (`kickstart.ini-dist` dokumentiert ihn als das zu
installierende Seitenpaket, angegeben mit seinem Bezeichner). `php bin/php/console exp:kickstarter ini --yes` schreibt `sevenx_multisite`
(`kernel/classes/expkickstarterini.php`).

**exp:install:**

```bash
php bin/php/console exp:install --package=sevenx_multisite
php bin/php/console exp:install --package=cjw_multisite --languages=ger-DE
```

`--package=<name>` ist standardmäßig `sevenx_multisite` und wird zu `[site_types] Site_package` der erzeugten Konfiguration. Ohne jede
Option installiert `exp:install` `sevenx_multisite` auf SQLite.

> **Hinweis**
> Der Kernel hat noch einen Schritt `SitePackages` (`ezstep_site_packages.php`), der einen Schlüssel `[site_packages] Packages` lesen würde,
> aber der Schritt ist in `StepTable` auskommentiert, und `kickstart.ini-dist` erwähnt ihn nicht. Zusätzliche Pakete lassen sich so nicht
> wählen; führen Sie sie stattdessen als Anforderungen des Seitenpakets auf. Ab 6.0.15 werden auch deren eigene Anforderungen
> heruntergeladen, die ganze Kette hinunter. Der Schritt bleibt aus: siehe
> [Wie der Setup-Assistent Pakete nutzt](wie-der-setup-assistent-pakete-nutzt.md).

## Wie das Paket gefunden wird

Im Kickstart-Modus tut `eZStepSiteTypes::init()` Folgendes:

1. Den entfernten Index lesen (`[RepositorySettings] RemotePackagesIndexURL`, `index.xml`) und auf `var/storage/packages/7x/index.xml`
   zurückfallen, wenn der Server nicht erreichbar ist.
2. Die lokalen Seitenpakete und den Zustand ihrer Anforderungen lesen.
3. Die beiden Listen zusammenführen und dabei **den entfernten Eintrag vorziehen** (`createSitePackagesList( ..., true )`): Die `version`
   des Index lässt sich nicht mit der Version eines lokalen Pakets vergleichen, also wird der entfernte Eintrag behalten, wann immer beide
   existieren.
4. Den Eintrag finden, den `Site_package` nennt.
5. **Der Eintrag hat eine URL** (er steht im Index): `downloadAndImportPackage()` **ohne force** aufrufen. Ein bereits importiertes Paket
   dieses Namens wird so genutzt, wie es ist; nur ein fehlendes wird heruntergeladen und importiert. Dann ruft
   `downloadDependantPackages()` Anforderungen ab, die fehlen oder älter sind als ihre `min-version`.
6. **Der Eintrag hat keine URL** (nur lokal, zum Beispiel bei einer Offline-Installation mit einem lokalen Index, der ihn nicht aufführt):
   nur weitermachen, wenn jede Anforderung importiert ist; sonst hält der Schritt an und fragt.
7. Das Paket auswählen und mit dem nächsten Schritt weitermachen.

Die Installation selbst geschieht im Schritt zum Anlegen der Websites, genau wie im Assistenten: Die verlangten Pakete werden in der
Reihenfolge der Liste `<require>` des Seitenpakets installiert.

## Unterschiede zum Web-Assistenten

| | Web-Assistent | Kickstarter und `exp:install` |
|---|---|---|
| Wahl | Optionsfeld, erstes importiertes Paket vorausgewählt | `Site_package` / `--package` |
| Entfernter und lokaler Eintrag existieren beide | der neuere nach Version | immer der entfernte Eintrag |
| Entfernter Eintrag gewählt, Paket bereits importiert | entfernt und erneut heruntergeladen (force) | die importierte Kopie wird genutzt |
| Anforderungen | fehlend oder zu alt: heruntergeladen | dasselbe |
| Hochladen einer `.ezpkg` | auf der Seite | nicht angeboten: zuerst mit `ezpm import` importieren |
| Probelauf | keiner | `--dry-run` |

Mit den Frontends für die Kommandozeile wird ein importiertes Paket also nie durch einen Lauf ersetzt. Das macht eine Offline-Installation
oder eine Installation mit festgelegten Versionen wiederholbar, und es ist auch der Grund, warum ein altes importiertes Paket weiter genutzt
wird, nachdem der Server ein neueres hat; siehe [Eine frische Kopie holen](#eine-frische-kopie-holen).

## Probeläufe prüfen die Pakete

```bash
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:install --package=sevenx_multisite --dry-run
```

Bei einem Probelauf importiert `SiteTypes` in ein temporäres Repository, `var/storage/packages/dryrun`, und lädt im Modus, der Vorhandenes
überspringt, das Seitenpaket und **alle** seine Anforderungen vom Server herunter, sodass die ganze Kette geprüft wird, selbst wenn Kopien
bereits importiert sind. Das temporäre Repository wird entfernt, wenn der Schritt fertig ist, und zu Beginn jedes Laufs
(`expKickstarter::cleanupDryRun()`). Ein Probelauf hält nach den Seitendetails an und schreibt weder Datenbank noch Einstellungen. Seine
letzte Zeile bei Erfolg:

```text
Dry-run completed: database and remote packages verified. Stopped after SiteDetails, nothing written.
```

Führen Sie ihn vor jeder echten Installation auf einem Server aus und nachdem Sie `RemotePackagesIndexURL` geändert haben.

## Ein Paket installieren, das nicht auf dem Server liegt

1. Importieren Sie das Archiv ins lokale Repository (aus dem Wurzelverzeichnis der Installation):

   ```bash
   php ezpm.php import my_site.ezpkg
   php ezpm.php import my_site_classes.ezpkg
   ```

   Importieren Sie auch jedes Paket, das es verlangt.
2. Nennen Sie es in `kickstart.ini` oder mit `--package=my_site`.
3. Wenn der Index des Servers es nicht aufführt, nimmt der Schritt die lokale Kopie und macht weiter, solange jede Anforderung importiert
   ist (Schritt 6 oben).

Damit jede Installation ein solches Paket anbieten kann, veröffentlichen Sie es auf einem Paketserver und richten die Installationen darauf:
siehe [Einen eigenen Paketserver betreiben](../eigene/einen-eigenen-paketserver-betreiben.md) und
[Eine Website auf Ihren Paketserver richten](../eigene/eine-website-auf-ihren-paketserver-richten.md), oder tragen Sie es zum gemeinsamen
Server bei.

## Eine frische Kopie holen

Weil ein importiertes Paket wiederverwendet wird, entfernen Sie das importierte Verzeichnis vor einem Lauf, wenn Sie das aktuelle Archiv des
Servers wollen (die Dokumentation des Kickstarters sagt dasselbe: Um eine frische Kopie eines Pakets zu erzwingen, entfernen Sie zuerst sein
importiertes Verzeichnis). Nutzen Sie **Entfernen** in der Paketliste unter **Setup > Pakete** oder `php ezpm.php delete <name>`; beide
löschen nur die Kopie im Repository, nicht das, was eine Installation auf eine Website gebracht hat. Siehe
[Importieren oder installieren](importieren-oder-installieren.md).

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Der Kickstarter, 6.4.2 Abschnitte und Schlüssel](../../../../en/us/install/06-kickstarter.md#642-sections-and-keys)
- [Der Kickstarter, 6.7 Probeläufe](../../../../en/us/install/06-kickstarter.md#67-dry-runs)
- [Die Installation über die Konsole, 7.3 Optionen](../../../../en/us/install/07-console-install.md#73-options)
- [Installation mit einem Befehl](../../../../en/us/features/6.0/install-in-one-command.md)
- [Offline-Installationen und Spiegel](../paketserver/offline-installationen-und-spiegel.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
