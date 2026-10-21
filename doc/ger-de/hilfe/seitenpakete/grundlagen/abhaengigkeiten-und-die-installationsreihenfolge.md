---
title: "Abhängigkeiten und die Installationsreihenfolge"
audience: developer, admin
category: install-upgrade
order: 16
summary: Wie ein Seitenpaket die Pakete angibt, die es braucht (require type ezpackage mit min-version), wie der Setup-Assistent und der Kickstarter sie abrufen, wie min-version verglichen wird und die genaue Reihenfolge, in der Pakete importiert und installiert werden.
keywords: Abhängigkeiten, requires, require, ezpackage, min-version, eZPackageVersion, compareFull, downloadDependantPackages, Installationsreihenfolge, EZSW-050, EZSW-051, provides, obsoletes, conflicts, dependencies, install order
type: help
---
# Abhängigkeiten und die Installationsreihenfolge

Diese Seite ist für Entwickler, die die `<requires>` eines Seitenpakets schreiben, und für Administratoren, die sich fragen, warum eine
Installation ein Paket abgerufen, ersetzt oder abgewiesen hat. Sie beantwortet: wie eine Anforderung geschrieben wird, wie sie geprüft wird
und in welcher Reihenfolge Pakete abgerufen und installiert werden.

## Inhalt

1. [Wie eine Anforderung geschrieben wird](#wie-eine-anforderung-geschrieben-wird)
2. [Wie min-version verglichen wird](#wie-min-version-verglichen-wird)
3. [Abrufen: was der Assistent herunterlädt](#abrufen-was-der-assistent-herunterlädt)
4. [Installieren: die Reihenfolge auf der Website](#installieren-die-reihenfolge-auf-der-website)
5. [Anforderungen von Anforderungen](#anforderungen-von-anforderungen)
6. [Die anderen Abschnitte zu Abhängigkeiten](#die-anderen-abschnitte-zu-abhängigkeiten)
7. [Checkliste für Ihre eigenen Anforderungen](#checkliste-für-ihre-eigenen-anforderungen)
8. [Verwandte Seiten](#verwandte-seiten)

## Wie eine Anforderung geschrieben wird

```xml
<dependencies>
  <requires>
    <require type="ezpackage" name="sevenx_classes" min-version="5.1" />
    <require type="ezpackage" name="sevenx_multisite_democontent" min-version="5.1" />
  </requires>
</dependencies>
```

| Attribut | Bedeutung |
|---|---|
| `type` | `ezpackage` für ein anderes Paket. Der Schritt zum Anlegen der Websites ignoriert jeden anderen Typ |
| `name` | Der `<name>` des verlangten Pakets, der auch sein Dateiname auf dem Paketserver ist |
| `min-version` | Die niedrigste `<number>-<release>`, die die Anforderung erfüllt; ohne sie genügt jede Version |

Die Reihenfolge der Elemente `<require>` ist die Reihenfolge der Installation. Setzen Sie Inhaltsklassen vor die Inhalte, die sie nutzen,
wie `sevenx_multisite` es tut.

## Wie min-version verglichen wird

Die Version eines Pakets ist `eZPackage::getVersion()`: `<version><number>` und `<version><release>`, mit `-` verbunden. Sie wird von
`eZPackageVersion::compareFull()` (`kernel/classes/ezpackageversion.php`) mit `min-version` verglichen:

- Der Teil nach dem letzten `-` ist nur dann die Release-Nummer, wenn er nur aus Ziffern besteht; der Rest ist die Versionsnummer.
- Versionsnummern werden nach dem Vorrang von Semantic Versioning 2.0.0 verglichen. Ältere Formen werden ebenfalls gelesen: `1` und `1.0`
  werden als `1.0.0` gelesen, ein vierter Teil wird nach PATCH verglichen, `3.4.0beta1` wird als Vorabversion `beta1` gelesen.
- Sind die Versionsnummern gleich, werden die Release-Nummern als Zahlen verglichen (eine fehlende zählt als 0).

| Installiert | `min-version` | Ergebnis |
|---|---|---|
| `6.0-10-stable` (`sevenx_classes` in 6.0.15) | `5.1` | erfüllt: 6 ist größer als 5 |
| `1.0-1` | `1.0-2` | nicht erfüllt |
| `1.0-1` | `1.0.0-1` | erfüllt: gleich |

> **Hinweis**
> Das Attribut `version` eines Eintrags in `index.xml` wird nicht mit `min-version` verglichen. Nur die Version in einem importierten Paket
> zählt.

## Abrufen: was der Assistent herunterlädt

`eZStepSiteTypes::downloadDependantPackages()` (`kernel/setup/steps/ezstep_site_types.php`) läuft, nachdem das Seitenpaket importiert ist.
Für jedes `<require>` des Seitenpakets, der Reihe nach:

1. Nach einem Paket dieses Namens im lokalen Repository suchen.
2. Wenn es existiert und `compareFull( installed, min-version ) >= 0` gilt: behalten, nichts herunterladen.
3. Wenn es existiert, aber älter ist: aus dem Repository entfernen und das neue herunterladen.
4. Wenn es nicht existiert: herunterladen.
5. Zum Herunterladen den Namen im Index nachschlagen; die URL ist die `url` des Eintrags. Ein Name, den der Index nicht aufführt, stoppt den
   Schritt mit „Der Download des Pakets '...' ist gescheitert. Sie können das Paket manuell uploaden.“

Jeder Download wird mit `eZPackage::import()` in das Repository importiert, das sein Hersteller nennt. Bei einem Probelauf des Kickstarters
wird stattdessen alles in das temporäre Repository `var/storage/packages/dryrun` heruntergeladen, sodass die ganze Kette geprüft wird, ohne
das echte Repository anzufassen.

## Installieren: die Reihenfolge auf der Website

Der Schritt zum Anlegen der Websites (`kernel/setup/steps/ezstep_create_sites.php`) dann:

1. Holt das Seitenpaket; Fehler `EZSW-041`, wenn das nicht gelingt.
2. Bindet seine Installationsskripte ein und ruft `eZSitePreInstall()` auf (siehe
   [Installationsskripte in einem Seitenpaket](installationsskripte-in-einem-seitenpaket.md)).
3. Geht die Liste `<require>` des Seitenpakets der Reihe nach durch. Für jedes Element `type="ezpackage"`:
   - holt das Paket; Fehler `EZSW-050` (`Could not fetch required package`), wenn es fehlt;
   - wenn sein `install_type` `install` ist, installiert es das Paket; Fehler `EZSW-051` (`Unable to install package`), wenn das scheitert;
   - wenn sein `install_type` `import` ist, lässt es das Paket, wie es ist (zum Beispiel einen Seitenstil, dessen Dateien die
     Installerskripte nutzen).
4. Schreibt die Einstellungen, Rollen und Voreinstellungen und ruft `eZSitePostInstall()` auf.

Für `sevenx_multisite` heißt das: Zuerst wird `sevenx_classes` installiert, dann `sevenx_multisite_democontent`, dann läuft der Installer.

## Anforderungen von Anforderungen

`eZPackage::install()` behandelt auch Anforderungen, wie ein Paketmanager: Bevor es die eigenen Elemente eines Pakets installiert,
installiert es jedes Paket, das dieses Paket verlangt, eine Ebene nach der anderen (`eZPackage::installUnaudited()`, im Stil von apt: alle
verlangten Pakete vorher installieren). Ein Paket, das weiter oben in der Kette bereits installiert wird, wird übersprungen, sodass eine
Schleife nicht ewig läuft. Ein verlangtes Paket, das nicht im Repository liegt, protokolliert dort nur eine Warnung.

> **Hinweis**
> Ab 6.0.15 **lädt** der Setup-Assistent die ganze Kette **herunter**: die Anforderungen des Seitenpakets, deren Anforderungen und so
> weiter, jedes Paket einmal (`eZStepSiteTypes::downloadDependantPackages()`), von jedem Paketserver, den er liest. Vorher lud er nur die
> direkten Anforderungen herunter, und die eigenen Anforderungen einer Anforderung mussten bereits im lokalen Repository liegen, sonst
> wurden sie bei ihrer Installation nur als fehlend protokolliert. Jedes Paket, das eine Website braucht, im Seitenpaket selbst aufzuführen,
> wie es jedes Seitenpaket des Index von 6.0.15 tut, funktioniert weiterhin auch mit älteren Releases.

`eZPackage::install()` prüft nicht, ob ein Paket bereits installiert ist: Wenn zwei Anforderungen dasselbe Paket verlangen, laufen seine
Elemente zweimal. Der Kernel lässt nur wiederholte Elemente innerhalb eines Pakets weg.

## Die anderen Abschnitte zu Abhängigkeiten

`<provides>`, `<obsoletes>` und `<conflicts>` werden gelesen und behalten und von den Paketseiten und `ezpm info` gezeigt, aber weder die
Setup-Schritte noch `eZPackage::install()` handeln danach. Die Designpakete von 7x geben zur Information
`<provide type="ezfile" name="collection" value="default"/>` an.

## Checkliste für Ihre eigenen Anforderungen

- [ ] Jedes Paket, das die Website braucht, ist im Seitenpaket selbst aufgeführt, mit `type="ezpackage"`.
- [ ] Klassen kommen vor Inhalten, Erweiterungen vor Inhalten, die ihre Datentypen nutzen.
- [ ] Jeder `name` steht in der `index.xml`, die die Installation liest.
- [ ] `min-version` ist höchstens die Version im Archiv auf dem Server.
- [ ] Die verlangten Pakete, die die Website ändern müssen, haben `install_type="install"`.

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Abhängigkeiten angeben](../anforderungen/abhaengigkeiten-angeben.md)
- [Wie der Setup-Assistent Pakete nutzt](wie-der-setup-assistent-pakete-nutzt.md)
- [Importieren oder installieren](importieren-oder-installieren.md)
- [Das Format index.xml](../paketserver/das-format-index-xml.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
