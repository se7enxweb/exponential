---
title: "Wie der Setup-Assistent Pakete nutzt"
audience: admin, developer, hosting
category: install-upgrade
order: 18
summary: Was der Setup-Assistent Schritt für Schritt mit Paketen tut - Seitentypen (Herunterladen des Index, Liste, Herunterladen und Importieren mit Anforderungen, Hochladen), Sprachoptionen der Pakete und Anlegen der Websites (Installationsskripte und Installation der verlangten Pakete) - mit dem Code hinter jedem Schritt und den Meldungen, denen Sie begegnen können.
keywords: Setup-Assistent, Seitentypen, eZStepSiteTypes, index.xml, RemotePackagesIndexURL, downloadAndImportPackage, downloadDependantPackages, Paket hochladen, Sprachoptionen der Pakete, Websites anlegen, EZSW-041, EZSW-050, EZSW-051, Schritt Seitenpakete, ezstep_data, setup wizard, site types, upload package, package language options, create sites, site packages step
type: help
---
# Wie der Setup-Assistent Pakete nutzt

Diese Seite ist für Administratoren, die den Setup-Assistenten im Web ausführen, und für Entwickler, die wissen müssen, was er hinter den
Bildschirmen mit Paketen tut. Sie beantwortet: woher die Liste der Seitenpakete kommt, was wann heruntergeladen wird, wohin es geht und wie
aus dem gewählten Paket eine Website wird. Die Bildschirme selbst, mit jeder Meldung, sind in
[Der Setup-Assistent](../../../../en/us/install/05-setup-wizard.md) beschrieben; diese Seite folgt den Paketen.

## Inhalt

1. [Die Schritte, die Pakete berühren](#die-schritte-die-pakete-berühren)
2. [Seitentypen: die Liste bauen](#seitentypen-die-liste-bauen)
3. [Seitentypen: ein Paket wählen](#seitentypen-ein-paket-wählen)
4. [Seitentypen: ein Paket hochladen](#seitentypen-ein-paket-hochladen)
5. [Sprachoptionen der Pakete](#sprachoptionen-der-pakete)
6. [Websites anlegen](#websites-anlegen)
7. [Wohin die Dateien gehen](#wohin-die-dateien-gehen)
8. [Wenn etwas scheitert](#wenn-etwas-scheitert)
9. [Verwandte Seiten](#verwandte-seiten)

## Die Schritte, die Pakete berühren

Die Schritte des Assistenten stehen in `kernel/setup/steps/ezstep_data.php` (`StepTable`). Drei davon arbeiten mit Paketen:

| Schritt | Klasse und Datei | Was er mit Paketen tut |
|---|---|---|
| Seitentypen (auf dem Bildschirm **Site Paket**) | `eZStepSiteTypes`, `ezstep_site_types.php` | Liest den Index, listet die Seitenpakete auf, lädt das gewählte und seine Anforderungen herunter und importiert sie |
| Sprachoptionen der Pakete | `eZStepPackageLanguageOptions`, `ezstep_package_language_options.php` | Vergleicht die Sprachen der verlangten Pakete mit den Sprachen der Website |
| Websites anlegen | `eZStepCreateSites`, `ezstep_create_sites.php` | Führt die Installationsskripte aus und installiert die verlangten Pakete |

> **Hinweis**
> Es gibt eine vierte Schrittklasse für Pakete, `eZStepSitePackages` (`ezstep_site_packages.php`, **Funktionalität der Seite**, Auswahl
> zusätzlicher Pakete), aber ihr Eintrag in `StepTable` ist auskommentiert: Der Assistent zeigt sie nie, und ihre Schlüssel in
> `kickstart.ini` haben keine Wirkung. Sie bleibt absichtlich aus (für 6.0.15 geprüft): Sie bietet die Pakete des Repositorys „addons“ aus
> 3.x anhand der Listen von `eZSetupFunctionality()` an, einer Funktion, die es nicht mehr gibt, sodass der Schritt mit einem fatalen Fehler
> anhielte, und kein Paket des Index ist ein Addon. Wofür sie gedacht war, ist anders abgedeckt: Die Anforderungen eines Seitenpakets, die
> ganze Kette hinunter, lädt der Schritt der Seitentypen herunter, und der Schritt zum Anlegen der Websites installiert sie.

## Seitentypen: die Liste bauen

Wenn der Schritt aufgebaut wird, ermittelt er die Adresse des Index aus `settings/package.ini`:

```ini
[RepositorySettings]
RemotePackagesIndexURL=https://exponential.packages.exponential.earth/exponential/6.0/6.0.15
RemotePackagesIndexURLBase=https://exponential.packages.exponential.earth/exponential
```

`RemotePackagesIndexURL` wird genutzt, wenn es nicht leer ist; sonst die Basis plus `/<major.minor>/<full version>/`. Der Schritt hängt
`index.xml` an. Dann bei jeder Anzeige:

1. **Entfernte Liste.** `index.xml` in den Ordner `packages/` des Cache-Verzeichnisses herunterladen (mit den Wiederholungen aus
   `DownloadAttempts` und `DownloadRetryDelays[]`, siehe [Einstellungen des Paketservers](../paketserver/einstellungen-des-paketservers.md)),
   lesen, die Kopie löschen und die Einträge behalten, deren `type` `site` ist. Scheitert der Download, wird `index.xml` aus dem
   System-Repository (`var/storage/packages/<Vendor>/index.xml`, `Vendor=7x`) genutzt, wenn es eines gibt. Ab 6.0.15 folgt der Index jedes
   weiteren Paketservers und fügt die Pakete hinzu, die die früheren nicht aufführen
   ([Weitere Paketserver](../paketserver/einstellungen-des-paketservers.md#weitere-paketserver)).
2. **Lokale Liste.** Jedes Paket vom Typ `site`, das bereits im lokalen Repository liegt, in jedem Herstellerverzeichnis.
3. **Zustand der Anforderungen.** Für jedes lokale Seitenpaket wird jedes `<require>` als importiert markiert, wenn ein Paket dieses Namens
   mit einer Version existiert, die seine `min-version` erfüllt.
4. **Zusammenführen.** Ein Eintrag pro Name. Ein lokales Paket gewinnt, es sei denn, die `version` des entfernten Eintrags ist höher
   (`eZPackageVersion::compareFull()`). Ein Eintrag ohne Download-URL zählt als importiert.
5. **Vorauswahl** des ersten importierten Seitenpakets, sodass **Weiter** es ohne Download installiert.

Die Seite zeigt auch die Adresse des Index (als URL des entfernten Repositorys), sodass Sie sehen, welcher Server genutzt wird.

## Seitentypen: ein Paket wählen

Das Optionsfeld sendet `eZSetup_site_type`: den reinen Namen für ein importiertes Paket, `name|url` für ein entferntes. Bei **Weiter**
(`eZStepSiteTypes::processPostData()`):

| Wahl | Was geschieht |
|---|---|
| Ein entfernter Eintrag | `downloadAndImportPackage()` im Modus **force**: Eine importierte Kopie gleichen Namens wird zuerst aus dem Repository entfernt, dann wird das Archiv heruntergeladen, mit `eZPackage::import()` importiert, und die heruntergeladene Datei wird gelöscht |
| Ein importierter Eintrag | Das Paket wird aus dem Repository geholt; nichts wird heruntergeladen |

Danach prüft `downloadDependantPackages()` in beiden Fällen jedes `<require>` des Seitenpakets: behalten, wenn importiert und neu genug,
sonst entfernt und erneut von der URL heruntergeladen, die der Index angibt. Die Regeln stehen in
[Abhängigkeiten und die Installationsreihenfolge](abhaengigkeiten-und-die-installationsreihenfolge.md). Erst wenn alle Anforderungen da sind, geht der
Assistent weiter.

## Seitentypen: ein Paket hochladen

Das Feld **Paket hochladen** (`PackageBinaryFile`, Schaltfläche `UploadPackageButton`) importiert eine `.ezpkg` von Ihrem Computer mit
`eZPackage::import()` und zeigt dieselbe Seite erneut, nun mit dem Paket in der lokalen Liste. So installieren Sie ein Seitenpaket, das
nicht auf dem Paketserver liegt, oder installieren ohne Netzwerkzugang. Ein bereits importiertes Paket wird nicht erneut importiert (eine
Warnung wird protokolliert); alles, was kein Paket ist, ergibt „Die hochgeladene Datei ist kein Exponential Paket“. Das Archiv wird wie in
[Die Datei .ezpkg](die-datei-ezpkg.md) beschrieben geprüft.

## Sprachoptionen der Pakete

Der Schritt liest die Sprachen der Inhaltsklassen und -objekte in den Paketen, die das Seitenpaket verlangt
(`eZPackage::languageInfoFromPackageList()`), und vergleicht sie mit den Sprachen, die Sie gewählt haben. Nur wenn Pakete andere Sprachen
mitbringen, fragt er, was damit geschehen soll (anlegen, einer Sprache der Website zuordnen oder auslassen). Die Antwort wird als
`language_map` behalten, die der Schritt zum Anlegen der Websites an die Installation jedes Pakets übergibt.

## Websites anlegen

Der Schritt, der die Website schreibt. Für Pakete:

1. Holt das gewählte Seitenpaket aus dem Repository (`EZSW-041`, `Could not fetch site package`, wenn das nicht gelingt).
2. Bindet die Installationsskripte des Seitenpakets ein und ruft `eZSitePreInstall()` auf
   ([Installationsskripte in einem Seitenpaket](installationsskripte-in-einem-seitenpaket.md)).
3. Meldet den Administrator an (Benutzer 14), sodass die Inhalte von diesem Benutzer angelegt werden.
4. Für jedes `<require type="ezpackage">` des Seitenpakets, der Reihe nach: holt es (`EZSW-050`, wenn es fehlt) und installiert es, wenn
   sein `install_type` `install` ist, nicht interaktiv mit dem Siteaccess, dem Design, dem Wurzelknoten 2 und der Sprachzuordnung der
   Website (`EZSW-051`, wenn das scheitert).
5. Baut die Einstellungen und Rollen mit den Hooks für INI, Rollen und Voreinstellungen und ruft `eZSitePostInstall()` auf.

Das Seitenpaket selbst wird nie installiert: Es ist `install_type="import"` und hat keine Elemente.

## Wohin die Dateien gehen

| Was | Wo |
|---|---|
| Die heruntergeladene `index.xml` und die `.ezpkg`-Dateien, während sie genutzt werden | das Cache-Verzeichnis, `packages/` (nach der Nutzung gelöscht) |
| Die entpackte Definition während des Imports | das Cache-Verzeichnis, `packages/import/` (nach der Nutzung gelöscht) |
| Jedes importierte Paket | `var/storage/packages/<vendor directory>/<name>/` |
| Was die Pakete installieren | die Datenbank, `extension/`, `design/`, `settings/`, wie es jeder Handler tut |
| Der Vermerk, dass ein Paket installiert ist | die Tabelle `ezpackage` |

## Wenn etwas scheitert

| Symptom | Wahrscheinliche Ursache | Lesen Sie |
|---|---|---|
| „Das Empfangen der Remote Site Paketliste ist fehlgeschlangen. Sie können die Pakete manuell hochladen.“ | Keine Verbindung zum Paketserver und keine lokale `index.xml` | [Wenn der Paketserver nicht erreichbar ist](../paketserver/wenn-der-paketserver-nicht-erreichbar-ist.md) |
| „Der Download des Pakets ... ist nach N Versuchen fehlgeschlagen“ | Der Server oder ein Proxy hat mit Fehlern geantwortet | dieselbe Seite und [Offline-Installationen und Spiegel](../paketserver/offline-installationen-und-spiegel.md) |
| „Der Download des Pakets ... ist gescheitert. Sie können das Paket manuell uploaden.“ für eine Anforderung | Der Index führt den verlangten Namen nicht auf | [Das Format index.xml](../paketserver/das-format-index-xml.md) |
| `EZSW-050` | Ein verlangtes Paket liegt nicht im Repository | [Das lokale Paket-Repository](das-lokale-paket-repository.md) |
| `EZSW-051` | Ein Installationselement eines verlangten Pakets ist gescheitert | das Setup-Log, `var/log/setup.log`, und [Installationselemente und Handler](installationselemente-und-handler.md) |
| `EZSW-080` | Die Nachinstallation des Seitenpakets hat angehalten | [Installationsskripte in einem Seitenpaket](installationsskripte-in-einem-seitenpaket.md) |

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Der Setup-Assistent, 5.3.8 Seitenpaket](../../../../en/us/install/05-setup-wizard.md#538-site-package)
- [Wie der Kickstarter und die Konsole Pakete nutzen](wie-der-kickstarter-und-die-konsole-pakete-nutzen.md)
- [Ein Paket von Hand herunterladen](../paketserver/ein-paket-von-hand-herunterladen.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md): Ein auf dem gemeinsamen Server angenommenes Paket erscheint in der Liste
  jedes Assistenten.
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
