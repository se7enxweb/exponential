---
title: README und Dokumentation im Repository
audience: developer, designer, partner
category: install-upgrade
order: 59
summary: Was das Quell-Repository eines Seitenpakets neben den Paketen mitbringen sollte - ein README, das sagt, was das Paket installiert und wie man es baut, eine Datei LICENSE, einen Changelog oder Release-Hinweise und eine Beschreibung des Repositorys -, verglichen mit dem, was die Quell-Repositorys von 7x und das Repository des Paketservers heute mitbringen.
keywords: README, LICENSE, Changelog, Repository, Quell-Repository, GitHub, sevenx-ezpackage, cjw_multisite-ezpackage, FUNDING.yml, build.sh, pack.sh, Beschreibung des Repositorys, Dokumentation, changelog, repository, source repository, repository description, documentation
type: help
---
# README und Dokumentation im Repository

Diese Seite ist für Entwickler, die die Quelle eines Seitenpakets in einem Repository führen, und für Prüfende, die sich eines ansehen, bevor
sie ein Paket annehmen. Die Datei `.ezpkg` bringt mit, was eine Installation braucht; das Repository bringt mit, was ein Mensch braucht, um
es zu verstehen, neu zu bauen und zu ändern. **Nichts hiervon wird vom Code erzwungen**. Alles ist Konvention, und zwar die, nach der
Prüfende zuerst fragen, wenn ein Paket eingereicht wird ([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)).

## Inhalt

1. [Wofür das Repository da ist](#wofür-das-repository-da-ist)
2. [Was die Repositorys von 7x heute mitbringen](#was-die-repositorys-von-7x-heute-mitbringen)
3. [Das README](#das-readme)
4. [Die Datei LICENSE](#die-datei-license)
5. [Changelog und Release-Hinweise](#changelog-und-release-hinweise)
6. [Einstellungen des Repositorys](#einstellungen-des-repositorys)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Wofür das Repository da ist

Ein Seitenpaket hat zwei Zuhause:

| Zuhause | Enthält | Gelesen von |
|---|---|---|
| Das Quell-Repository | Einen Ordner pro Paket (`packages/<name>/`), das Build-Skript, die Geschichte | Menschen, die das Paket ändern oder prüfen |
| Das Repository des Paketservers | Die gebauten Dateien `.ezpkg`, `index.xml`, Vorschaubilder | dem Setup-Assistenten, dem Kickstarter und `ezupdate` |

Das Quell-Repository ist der einzige Ort, an dem sich eine Änderung erklären lässt. Das Archiv behält bestenfalls einen Eintrag im Changelog
pro Release; das Repository behält jeden Commit und kann erklären, warum.

## Was die Repositorys von 7x heute mitbringen

Am 19.10.2026 mit `gh api` und den Klonen geprüft:

| Repository | Oberste Ebene | README | LICENSE | Changelog-Datei |
|---|---|---|---|---|
| [se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) | `.github/FUNDING.yml`, `README.md`, `LICENSE`, `ant/`, `bin/` (`pack.sh`, `repack_extension.sh`, `ezpxmlextfilelist.php`), `packages/` (14 Pakete und `build.sh`) | ja | ja (GPL-2.0) | nein |
| [se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) | Dieselben `.github/`, `ant/` und `bin/`, `README.md`, `LICENSE`; `packages/` mit 2 Paketen und `build.sh` | ja | ja (GPL-2.0) | nein |
| [se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) | `.github/` (`FUNDING.yml`, `workflows/workflow.yml`), `CNAME`, `README.md`, `CONTRIBUTING.md`, `crossdomain.xml`, `exponential/` | ja | nein | nein |

Beide Quell-Repositorys haben eine Beschreibung auf GitHub und nutzen `master` als Standard-Branch. Bis zum 19.10.2026 hatten sie kein README
und keine `LICENSE`: Ihre Lizenz stand nur in jedem Paket (`<licence>GPL</licence>` und `documents/LICENCE`), sodass GitHub für sie keine
Lizenz zeigte. Seitdem hat jedes ein README (was die Pakete sind, was sie brauchen, wie man sie baut und veröffentlicht) und eine `LICENSE`
mit dem vollständigen Text der GPL Version 2, die GitHub erkennt; `cjw_multisite-ezpackage` bekam außerdem das `packages/build.sh` des
Repositorys von 7x. Ein heute eingereichtes Paket sollte dasselbe mitbringen; die Abschnitte unten sagen wie.

## Das README

Eine `README.md` im Wurzelverzeichnis des Repositorys. Sie sollte in dieser Reihenfolge beantworten:

1. **Was es installiert.** Die Seitenpakete, die es enthält, was eine neue Website von jedem bekommt (Siteaccesses, Sprachen, Klassen,
   Demo-Inhalte, aktivierte Erweiterungen) und die Varianten (`_clean` ohne Demo-Inhalte).
2. **Für welche Releases es gedacht ist.** Das Release von Exponential, auf dem es gebaut und getestet wurde, dasselbe wie
   `<ezpublish><version>`, und die getesteten Datenbank-Engines.
3. **Wie man es installiert.** Über den Setup-Assistenten vom Paketserver, mit dem Kickstarter (`Site_package` in `kickstart.ini`) oder
   offline aus einer lokalen `index.xml`; verlinken Sie die Seiten der Hilfe, statt sie zu wiederholen.
4. **Wie man es baut.** Der Befehl, der aus den Ordnern Dateien `.ezpkg` macht (für das Repository von 7x: `cd packages && ./build.sh`), und
   was der Build weglässt.
5. **Wovon es abhängt.** Die Pakete, die jedes Seitenpaket verlangt, und woher sie kommen, wenn sie in einem anderen Repository liegen
   (`cjw_multisite` verlangt `sevenx_classes` aus dem Repository von 7x).
6. **Material von Dritten.** Bilder, Schriften, Symbole oder Skripte unter anderen Bedingungen, jeweils mit ihrer Lizenz.
7. **Screenshots** der installierten Website ([Vorschaubilder und Screenshots](vorschaubilder-und-screenshots.md#screenshots)).
8. **Wo Probleme gemeldet werden und wie man beiträgt.**

Wie Sie den werbenden Teil gut schreiben, steht auf
[Ein README, das erklärt und überzeugt](../../../../en/us/help/designers/distribution/readme-that-explains-and-sells.md).

## Die Datei LICENSE

Eine Datei `LICENSE` im Wurzelverzeichnis mit dem vollständigen Text der Lizenz, derselben Lizenz wie `<licence>` und `documents/LICENCE` in
jedem Paket. Für die GPL Version 2 oder neuer, die Lizenz von Exponential und der ausgelieferten Pakete, ist das der Text der GNU GPL v2. Der
Dateiname und der vollständige Text lassen GitHub und Packagist die Lizenz erkennen, was der kurze Hinweis in `documents/LICENCE` nicht tut.
Details: [Die Datei LICENSE eines Themes](../../../../en/us/help/designers/extensions/license-file.md) und [Lizenz und Dokumente](lizenz-und-dokumente.md).

## Changelog und Release-Hinweise

Führen Sie im Repository eine Changelog-Datei, einen Abschnitt pro veröffentlichtem Release eines Pakets, geschrieben aus den Betreffzeilen
der Commits seit dem letzten Release. Die Repositorys von 7x schreiben Betreffzeilen von Commits mit den Präfixen `Added:`, `Updated:`,
`Removed:` und `Renamed:`, was eine solche Datei leicht zusammenstellen lässt. Kopieren Sie die Zeilen, die für einen Administrator zählen,
in den neuen `<changelog><entry>` jedes Pakets ([Einträge im Changelog](eintraege-im-changelog.md)).

Siehe [Eine Changelog-Datei führen](../../../../en/us/help/designers/distribution/keeping-a-changelog-file.md) und
[Release-Hinweise schreiben](../../../../en/us/help/designers/distribution/writing-release-notes.md).

## Einstellungen des Repositorys

- [ ] Eine Beschreibung, die das Produkt, das Paket und das, was es installiert, nennt.
- [ ] Das Feld für die Website zeigt auf eine Demo oder die Dokumentation, wo es eine gibt.
- [ ] Topics, die helfen, es zu finden.
- [ ] Issues eingeschaltet, damit Probleme dort gemeldet werden können, wo die Quelle liegt.

[Name, Beschreibung und Topics des Repositorys](../../../../en/us/help/designers/distribution/repository-name-description-topics.md) behandelt die
Entscheidungen.

## Checkliste

- [ ] `README.md` im Wurzelverzeichnis, die die acht Fragen oben beantwortet.
- [ ] `LICENSE` im Wurzelverzeichnis mit dem vollständigen Lizenztext.
- [ ] Eine Changelog-Datei mit einem Abschnitt für jedes veröffentlichte Release.
- [ ] Das Build-Skript und ein Satz dazu, wie man es ausführt.
- [ ] Ein Ordner pro Paket unter `packages/`, genau wie der `<name>` des Pakets benannt.
- [ ] Keine gebauten `.ezpkg` der eigenen Pakete des Repositorys neben ihren Quellen committet; sie gehören auf den Paketserver. (Archive
      innerhalb einer mitgelieferten Erweiterung, etwa die Ordner `packages/` der Erweiterungen in `sevenx_extension`, sind Teil dieser
      Erweiterung.)
- [ ] Keine Geheimnisse in Dateien unter `settings/` eines Pakets: Sie werden mit ihm veröffentlicht.

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Die Quell-Repositorys](../paketserver/die-quell-repositorys.md)
- [Wie ein Release den Paketserver erreicht](../paketserver/wie-ein-release-den-paketserver-erreicht.md)
- [Ein Seitenpaket forken](../eigene/ein-seitenpaket-forken.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
