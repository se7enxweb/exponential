---
title: Eigene Pakete versionieren
audience: developer, partner
category: install-upgrade
order: 92
summary: Wo die Version eines Pakets steht (Versionsnummer und Release-Nummer in package.xml, das Attribut version in index.xml, der Release-Ordner auf dem Server), wie Exponential Versionen vergleicht, wie min-version entscheidet, ob ein verlangtes Paket erneut heruntergeladen wird, und die Regeln, die veröffentlichte Versionen vertrauenswürdig halten.
keywords: Version, Release, package.xml, Versionsnummer, Release-Nummer, min-version, eZPackageVersion, Semantic Versioning, SemVer, Version in index.xml, Release-Ordner, Changelog, compareFull, version, release, version number, release number, index.xml version, release folder, changelog
type: help
---
# Eigene Pakete versionieren

Diese Seite ist für Entwickler, die Seitenpakete und die Pakete, die sie verlangen, veröffentlichen und brauchen, dass Installationen die
richtige Kopie wählen. Sie zeigt jede Stelle, an der eine Version steht, wie der Kernel sie liest und vergleicht und wie Sie sie für ein neues
Release erhöhen.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihre Pakete per Pull Request an se7enxweb/exponential.packages.exponential.earth
einzureichen, wo die Release-Ordner den Versionen von Exponential folgen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Wo Versionen stehen](#wo-versionen-stehen)
2. [Wie Versionen verglichen werden](#wie-versionen-verglichen-werden)
3. [Was min-version tut](#was-min-version-tut)
4. [Was die Version im Index tut](#was-die-version-im-index-tut)
5. [Die Version für ein Release erhöhen](#die-version-für-ein-release-erhöhen)
6. [Veröffentlicht heißt dauerhaft](#veröffentlicht-heißt-dauerhaft)

## Wo Versionen stehen

| Stelle | Beispiel | Bedeutung |
|---|---|---|
| `package.xml` `<version><number>` | `1.2.0` | die Versionsnummer des Pakets |
| `package.xml` `<version><release>` | `1` | die Release-Nummer dieser Version (1 für ein neues Paket) |
| `package.xml` `<changelog><entry release="...">` | `release="1"` | zu welchem Release ein Eintrag im Changelog gehört |
| `package.xml` `<require min-version="...">` | `1.2.0-1` | die niedrigste Version eines verlangten Pakets, die dieses akzeptiert |
| `package.xml` `<ezpublish><version>` | `6.0.15-stable` | die Version von Exponential, für die das Paket gemacht wurde (das Element behält seinen historischen Namen) |
| `index.xml` `<package version="...">` | `1.2.0-1` | die Version, die der Server ankündigt |
| der Ordner auf dem Server | `exponential/6.0/6.0.15/` | das Release von Exponential, für das die Dateien veröffentlicht wurden |

`eZPackage::getVersion()` verbindet Nummer und Release als `<number>-<release>`; dieses Paar nutzen `min-version` und die Tabelle
`ezpackage`. Das Attribut `version` des Wurzelelements `<package>` ist etwas anderes: Der Kernel schreibt dort die Version des Paketformats
hinein (`eZPackage::VERSION`, `3.5.2`), wenn er ein Paket speichert.

## Wie Versionen verglichen werden

`kernel/classes/ezpackageversion.php` (`eZPackageVersion`) liest eine Versionsnummer zuerst als Semantic Versioning 2.0.0:

- `MAJOR.MINOR.PATCH`, mit wahlweise `-prerelease` und `+build` (Build-Metadaten werden beim Vergleichen ignoriert);
- ältere Formen werden weiterhin verstanden: weniger Teile (`1.0` wird als `1.0.0` gelesen), mehr Teile (`1.2.3.4`), eine Buchstabenendung
  (`3.4.0beta1` ist die Vorabversion `beta1`);
- alles andere wird als Text behalten und sortiert vor jeder Version, die sich lesen lässt.

Die Release-Nummer wird zuletzt verglichen, numerisch: `1.0-1` < `1.0-2` < `1.0.1-1`, und `1.0-1` ist gleich `1.0.0-1`. In einem Paar
`<number>-<release>` ist der Teil nach dem letzten `-` nur dann das Release, wenn er nur aus Ziffern besteht.

Neue Pakete aus den Assistenten zum Erstellen müssen eine strikte SemVer-Version von höchstens 24 Zeichen haben
(`eZPackageVersion::isValidNew()`); `ezpm create` nimmt die Version, wie sie angegeben wird. Nutzen Sie für Ihre eigenen Pakete striktes
SemVer: Es ist die eine Form, die jedes Werkzeug gleich liest.

> **Hinweis**
> Die Seitenpakete von 7x tragen noch `<number>6.0</number>` und `<release>15-stable</release>`, eine Form, die älter ist als diese Regeln.
> Ihr Fork muss sie nicht übernehmen: Setzen Sie die Version auf Ihr eigenes erstes Release zurück, zum Beispiel `<number>1.0.0</number>` und
> `<release>1</release>`.

## Was min-version tut

Wenn der Setup-Assistent oder der Kickstarter ein Seitenpaket installiert, sieht sich `downloadDependantPackages()` in
`kernel/setup/steps/ezstep_site_types.php` jedes `<require type="ezpackage">` an:

| Situation | Was geschieht |
|---|---|
| das verlangte Paket ist nicht importiert | es wird von der `url` seines Eintrags in `index.xml` heruntergeladen und importiert |
| es ist mit einer Version unter `min-version` importiert (`eZPackageVersion::compareFull()`) | die alte Kopie wird entfernt und das Paket erneut heruntergeladen |
| es ist mit dieser Version oder höher importiert | es wird so genutzt, wie es ist |
| es steht nicht im Index | die Installation hält mit „Der Download des Pakets '...' ist gescheitert. Sie können das Paket manuell uploaden.“ an |

Erhöhen Sie also `min-version`, wann immer Ihr Seitenpaket die neueren Inhalte oder Klassen braucht; sonst behält eine Installation, die eine
ältere Kopie importiert hat, diese.

## Was die Version im Index tut

Im Setup-Assistenten im Browser wird ein Seitenpaket, das sowohl im Index steht als auch lokal importiert ist, aus dem Index angeboten, wenn
die `version` im Index höher ist als die lokale (`compareFull()` in `createSitePackagesList()`), und sonst wird die lokale Kopie genutzt. Der
Kickstarter zieht immer den Eintrag des Index vor, weil, wie der Code vermerkt, das Attribut version des Paketindex nicht mit der lokalen
Paketversion vergleichbar ist. Halten Sie die `version` im Index gleich dem `<number>-<release>` des Archivs, auf das sie zeigt, dann stimmen
beide Lesarten überein.

## Die Version für ein Release erhöhen

1. Erhöhen Sie `<number>` für ein geändertes Paket (oder `<release>` für ein erneutes Paketieren derselben Inhalte).
2. Fügen Sie einen `<changelog><entry release="...">` hinzu, der die Änderung beschreibt.
3. Erhöhen Sie `min-version` in jedem Paket, das die Änderung braucht, und wiederum deren Versionen.
4. Bauen Sie die Archive neu ([Mit build.sh neu bauen](mit-build-sh-neu-bauen.md)).
5. Schreiben Sie die neuen Versionen in `index.xml`.
6. Veröffentlichen Sie in den Ordner des Release von Exponential, für das die Pakete gedacht sind
   ([Einen eigenen Paketserver betreiben](einen-eigenen-paketserver-betreiben.md)).

## Veröffentlicht heißt dauerhaft

Sobald ein Archiv auf einem Server liegt, haben Installationen es vielleicht unter seiner Version importiert. Ersetzen Sie nie ein
veröffentlichtes Archiv mit anderem Inhalt unter derselben Version: Veröffentlichen Sie eine neue Version und sagen Sie in ihrem Changelog,
was an der vorigen falsch war. Eine Version, deren letzte Stelle 9 erreicht, geht weiter auf 10 (auf `1.0.9` folgt `1.0.10`); nur die letzte
Stelle zählt weiter. Die Begründung steht auf [Ein gepushtes Tag ist dauerhaft](../../designer/veroeffentlichung/ein-gepushter-tag-ist-endgueltig.md)
und [Nur die letzte Stelle zählt](../../designer/veroeffentlichung/nur-die-letzte-stelle-zaehlt.md).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Versionsnummern](../anforderungen/versionsnummern.md)
- [Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md)
- [Paketlizenzen und Versionen](../../../funktionen/6.0/paketlizenzen-und-versionen.md)
- [Eine Versionsnummer wählen](../../designer/veroeffentlichung/eine-versionsnummer-waehlen.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
