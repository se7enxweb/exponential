---
title: Ihr Seitenpaket beitragen
audience: developer, designer, partner, admin
category: install-upgrade
order: 2
featured: true
summary: 7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihr eigenes Seitenpaket per Pull Request an das Repository hinter dem Paketserver einzureichen, damit der Setup-Assistent, der Kickstarter und ezupdate jeder Installation es anbieten können. Diese Seite zeigt das zu forkende Repository, den Ordneraufbau, die Checkliste, Versionen, Lizenz, Tests und den Pull Request.
keywords: beitragen, Seitenpaket, Pull Request, PR, Fork, Paketserver, exponential.packages.exponential.earth, index.xml, ezpkg, einreichen, teilen, Community, Standardauslieferung, Setup-Assistent, Kickstarter, ezupdate, GPL, GFDL, contribute, site package, fork, package server, submit, share, community, default distribution, setup wizard
type: help
---
# Ihr Seitenpaket beitragen

**7x und die Exponential Foundation ermutigen jeden Entwickler, Designer und Partner, der ein Seitenpaket gebaut hat, ausdrücklich, es per
Pull Request an das Repository einzureichen, das den Inhalt des Paketservers enthält.** Ein angenommenes Paket wird Teil des
Standard-Verteilungssystems von Exponential: Es steht im Paketindex, den jede Installation liest, sodass der Setup-Assistent, der
Kickstarter, `exp:install` und ezupdate auf jeder Exponential-Website es anbieten können, ohne zusätzliche Einstellungen auf irgendeiner
Seite.

Diese Seite ist für alle, die ein funktionierendes Seitenpaket haben (oder gerade eines erstellen) und es jede Installation erreichen
lassen wollen. Sie beantwortet: welches Repository Sie forken, wohin Ihre Dateien gehören, was im Paket enthalten sein muss, welche
Versionsnummer Sie ihm geben, welche Lizenz, wie Sie es vorher testen, was Sie in den Pull Request schreiben und wie die Prüfung abläuft.

## Inhalt

1. [Warum beitragen](#warum-beitragen)
2. [Das zu forkende Repository](#das-zu-forkende-repository)
3. [Ordneraufbau und Dateinamen](#ordneraufbau-und-dateinamen)
4. [Der Eintrag im Index](#der-eintrag-im-index)
5. [Die Checkliste für das Paket](#die-checkliste-für-das-paket)
6. [Versionsnummern](#versionsnummern)
7. [Lizenz](#lizenz)
8. [Die Installation zuerst in einer Sandbox testen](#die-installation-zuerst-in-einer-sandbox-testen)
9. [Den Pull Request öffnen](#den-pull-request-öffnen)
10. [Wie die Prüfung abläuft](#wie-die-prüfung-abläuft)
11. [Verwandte Seiten](#verwandte-seiten)

## Warum beitragen

- **Reichweite.** Ein Paket im Standardindex ist in jeder neuen Installation einen Klick entfernt: Der Schritt für Seitenpakete im
  Setup-Assistenten listet jedes Paket `type="site"` des Index auf (`eZStepSiteTypes::retrieveRemotePackagesList()` in
  `kernel/setup/steps/ezstep_site_types.php`), und der Kickstarter und `exp:install --package=<name>` installieren es über den Namen.
- **Gemeinsame Pflege.** Die Pakete auf dem Server werden für jedes Release von Exponential neu gebaut (ein Ordner pro Release unter
  `exponential/6.0/`: heute dreizehn Ordner, von `6.0.0` bis `6.0.15`). Ein Paket, das Teil dieser Sammlung ist, reist mit den Releases,
  statt auf der Festplatte einer Person zu veralten.
- **Wachstum der Community.** Jedes Seitenpaket zeigt, was Exponential kann. Die Exponential Foundation und 7x wollen viele davon:
  Unternehmens-Websites, Intranets, Portale, Shops, Community-Websites, Websites in vielen Sprachen.

Sie behalten Ihr eigenes Quell-Repository und Ihr eigenes Urheberrecht; der Pull Request fügt das gebaute Paket und seinen Eintrag im Index
dem gemeinsamen Server hinzu.

## Das zu forkende Repository

| Was | Wo |
|---|---|
| Der Inhalt des Paketservers (dieses forken) | [github.com/se7enxweb/exponential.packages.exponential.earth](https://github.com/se7enxweb/exponential.packages.exponential.earth) |
| Seine Anleitung für Beiträge (die Kurzform dieser Seite) | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential.packages.exponential.earth/blob/master/CONTRIBUTING.md) |
| Was es veröffentlicht | [exponential.packages.exponential.earth](https://exponential.packages.exponential.earth/) (GitHub Pages, Standard-Branch `master`) |
| Der Index, den der Setup-Assistent von 6.0.15 liest | [exponential/6.0/6.0.15/index.xml](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml) |

Jeder Push nach `master` veröffentlicht den ganzen Baum über den Workflow `.github/workflows/workflow.yml`, der auch die
Verzeichnisauflistung erzeugt, die Sie im Browser sehen ([Der Paketserver auf GitHub](paketserver/der-paketserver-auf-github.md)).

```bash
gh repo fork se7enxweb/exponential.packages.exponential.earth --clone
cd exponential.packages.exponential.earth
git checkout -b add-<your_package>
```

Das Repository ist groß (es enthält jede gebaute `.ezpkg` jedes Release). Ein partieller Klon genügt zum Arbeiten:
`git clone --filter=blob:none <your fork's URL>`.

Halten Sie die **Quelle** Ihres Pakets (die entpackten Ordner) in einem eigenen Repository, so wie die Pakete von 7x in
[se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) und die Pakete von CJW in
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) liegen
([Die Quell-Repositorys](paketserver/die-quell-repositorys.md)). Die Prüfenden werden sie sehen wollen.

## Ordneraufbau und Dateinamen

Der Server hat einen Ordner pro Release von Exponential. Ihre Dateien gehören in den Ordner jedes Release, das Sie unterstützen, neben die
vorhandenen Pakete:

```text
exponential/6.0/6.0.15/
├── index.xml                          the index: add one <package> line per package
├── <your_package>.ezpkg               the site package (type site)
├── <your_package>_democontent.ezpkg   the packages it requires, if any
└── thumbnails/
    └── <your_package>.png             the picture the setup wizard shows
```

| Regel | Warum |
|---|---|
| Dateiname = `<package name>.ezpkg`, der Name genau wie in `<name>` von `package.xml` | Der Index zeigt auf die Datei, und der Importer weist einen Namen ab, der nicht aus Kleinbuchstaben, Ziffern und `_` besteht ([Pflichtfelder in package.xml](anforderungen/pflichtfelder-in-package-xml.md)) |
| Ein eigener Name, nie einer, der existiert | Ein Import weist ein Paket ab, dessen Name bereits in irgendeinem Repository der Website steht ([Anleitung zu Paketen](../../anleitungen/pakete.md#6-ein-paket-hochladen)) |
| Vorschaubild in `thumbnails/<package name>.png` | Dasselbe Muster, das jedes vorhandene Seitenpaket nutzt (`thumbnail_url` in `index.xml`) |
| Pakete, die Ihr Seitenpaket verlangt, liegen ebenfalls auf dem Server | Der Assistent lädt jedes verlangte Paket aus demselben Index herunter ([Abhängigkeiten und die Installationsreihenfolge](grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md)) |
| Nur gebaute Archive, keine Quellordner | Der Server liefert Dateien aus; die Quelle gehört in Ihr eigenes Repository |

Bauen Sie jede `.ezpkg` so, wie die ausgelieferten gebaut werden: ein mit gzip komprimiertes tar des Inhalts des Paketordners
(`package.xml` oben), ohne `.git`, Editor-Dateien und `node_modules` ([Mit build.sh neu bauen](eigene/mit-build-sh-neu-bauen.md)).

## Der Eintrag im Index

Fügen Sie Ihre Pakete der `index.xml` des Release hinzu, in derselben Form wie die vorhandenen Einträge. Verlangte Pakete bekommen einen
kurzen Eintrag in der Liste der Abhängigkeiten oben; das Seitenpaket bekommt einen vollständigen:

```xml
<package name="<your_package>_democontent" type="contentobject"
         url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/<your_package>_democontent.ezpkg"/>

<package name="<your_package>" version="1.0-1" type="site"
         summary="<one line, shown as the package's title>"
         description="<two or three sentences: what the site is and what it contains>"
         url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/<your_package>.ezpkg"
         thumbnail_url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/thumbnails/<your_package>.png"/>
```

Der Assistent liest jedes Attribut eines Elements `<package>` so, wie es ist; die URLs müssen also absolut und korrekt sein; er listet nur
Einträge `type="site"` zur Auswahl auf. Prüfen Sie die Datei mit `xmllint --noout index.xml`, bevor Sie committen
([Das Format index.xml](paketserver/das-format-index-xml.md)).

## Die Checkliste für das Paket

Die vollständige Liste, mit dem Grund für jeden Punkt, ist [Die Checkliste der Anforderungen](anforderungen/die-checkliste-der-anforderungen.md).
Kurz gefasst, bevor Sie den Pull Request öffnen:

- [ ] `package.xml` ist wohlgeformt, hat `<package>` als Wurzel und keinen Dokumenttyp.
- [ ] `<name>` besteht aus Kleinbuchstaben, Ziffern und `_` und passt zum Dateinamen.
- [ ] `<summary>` und `<description>` sagen in einfachen Worten, was die Website ist.
- [ ] `<vendor>` nennt Sie oder Ihre Organisation.
- [ ] `<type value="site" />` für das Seitenpaket; der richtige Typ für jedes Paket, das es verlangt.
- [ ] `<version>` (`<number>`, `<release>`) stimmt mit dem Attribut `version` des Eintrags im Index überein (das Attribut `version` des Wurzelelements `<package>` ist die Formatversion; lassen Sie es so, wie die Werkzeuge es schreiben).
- [ ] `<ezpublish><version>` nennt das Release von Exponential, mit dem Sie getestet haben (das Element behält seinen historischen Namen).
- [ ] `<licence>` enthält einen Lizenzbezeichner aus `package.ini [LicenseSettings]`, und `documents/LICENCE` enthält den Lizenztext.
- [ ] `<changelog>` hat einen Eintrag für dieses Release.
- [ ] `<maintainers>` führt einen Betreuer und eine Rolle aus `[MaintainerSettings] RoleList` auf.
- [ ] `<dependencies><requires>` führt jedes Paket auf, das es braucht, und jedes davon ist im Pull Request oder bereits auf dem Server.
- [ ] `files/default/thumbnail/thumbnail.png` und das Vorschaubild im Index existieren.
- [ ] Keine Geheimnisse: keine Passwörter, Schlüssel, Tokens oder echten E-Mail-Adressen in Installationsskripten, Einstellungen oder
      Inhalten.
- [ ] Das Paket besteht **Prüfen** auf seiner Seite in **Setup > Pakete**
      ([Ein Paket vor der Veröffentlichung prüfen](anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)).

## Versionsnummern

- Die Version des Pakets ist `<version><number>` und `<release>` in `package.xml`; der Index wiederholt sie im Attribut `version`,
  geschrieben so, wie `eZPackage::getVersion()` sie schreibt, `<number>-<release>`: `<number>1.0</number>` und `<release>1</release>`
  werden als `version="1.0-1"` aufgeführt und die Seitenpakete von 7x (`6.0`, `15-stable`) als `version="6.0-15-stable"`. Halten Sie beide
  gleich: Der Setup-Assistent vergleicht die Version im Index mit einer Kopie, die auf einer Website bereits importiert ist, sodass eine
  höhere Version im Index ihn das Paket bei jedem Lauf erneut herunterladen lässt und eine niedrigere Ihr neues Release verbirgt
  ([Versionsnummern](anforderungen/versionsnummern.md)).
- Ein geändertes Paket bekommt eine neue Version. Ein Ordner auf dem Server ist ein Release von Exponential; darin erhöhen Sie die Version
  Ihres Pakets, wenn Sie seine Datei ersetzen, und sagen in `<changelog>` und im Pull Request, was sich geändert hat.
- Wenn Ihr Paket auch in Ihrem eigenen Repository getaggt wird, gilt dort ebenfalls die Regel des Projekts für Tags: Ein gepushtes Tag
  wird nie verschoben ([Ein gepushtes Tag ist dauerhaft](../../../eng-us/help/designers/distribution/a-pushed-tag-is-permanent.md)).

## Lizenz

- **Code** (Installationsskripte, Templates, Einstellungen, Erweiterungen im Paket): eine Lizenz, die mit der GNU GPL vereinbar ist, weil
  Exponential selbst unter GPL 2.0 oder neuer steht. Der Standard des Paketassistenten ist `GPL-2.0-or-later`
  (`package.ini [LicenseSettings] DefaultLicense`). Siehe [GPL für Theme-Code](../../../eng-us/help/designers/distribution/gpl-for-theme-code.md).
- **Dokumentation**, die mit dem Paket ausgeliefert wird (README, Anleitungen): die GNU Free Documentation License 1.3, wie für die eigene
  Dokumentation von Exponential ([Freie Dokumentation](../freie-dokumentation.md),
  [GFDL für Theme-Dokumentation](../../../eng-us/help/designers/distribution/gfdl-for-theme-documentation.md)).
- **Bilder und Demo-Inhalte**: nur Material, das Sie teilen dürfen, mit angegebener Lizenz. Creative-Commons-Lizenzen stehen in der Liste
  des Assistenten ([Creative Commons für Bilder](../../../eng-us/help/designers/distribution/creative-commons-for-images.md)).

## Die Installation zuerst in einer Sandbox testen

Testen Sie ein Seitenpaket nie auf einer Website mit echten Inhalten: Das Installieren eines Seitenpakets baut eine neue Website.

1. Erstellen Sie eine Wegwerf-Installation (ein frischer Klon mit eigener SQLite-Datenbank ist am schnellsten,
   [In einer Sandbox testen](eigene/in-einer-sandbox-testen.md)).
2. Richten Sie sie auf Ihre Pakete: Importieren Sie sie entweder in ihr lokales Repository, oder setzen Sie
   `[RepositorySettings] RemotePackagesIndexURL` in ihrer `settings/override/package.ini.append.php` auf einen Ordner, der Ihre Kopie des
   Release-Ordners ausliefert ([Eine Website auf Ihren Paketserver richten](eigene/eine-website-auf-ihren-paketserver-richten.md)).
3. Installieren Sie mit dem Setup-Assistenten und noch einmal mit `./console exp:install --package=<your_package>` (oder dem Kickstarter),
   sodass beide Frontends belegt sind.
4. Prüfen Sie das Ergebnis: die Website, die Administration, den Siteaccess der Redaktion, die Rollen, eine Suche, eine Bearbeitung und
   eine Veröffentlichung. Lesen Sie `var/log/error.log` der Sandbox.
5. Entfernen Sie die Sandbox, wenn Sie fertig sind.

## Den Pull Request öffnen

Pushen Sie Ihren Branch in Ihren Fork und öffnen Sie den Pull Request gegen `se7enxweb/exponential.packages.exponential.earth`, Branch
`master`:

```bash
git add exponential/6.0/6.0.15/<your_package>*.ezpkg exponential/6.0/6.0.15/thumbnails/<your_package>.png exponential/6.0/6.0.15/index.xml
git commit -m "Added: <your_package> site package for 6.0.15"
git push origin add-<your_package>
gh pr create -R se7enxweb/exponential.packages.exponential.earth --base master
```

Schreiben Sie in den Pull Request:

| Abschnitt | Inhalt |
|---|---|
| Was es ist | Ein Absatz: die Art der Website, ihre Siteaccesses und Sprachen, was ein Redakteur bekommt |
| Was es enthält | Die Pakete mit ihren Typen und die Erweiterungen und Designs, die es braucht |
| Quelle | Der Link zum Repository mit der entpackten Quelle |
| Getestet | Die Version von Exponential, die Datenbank(en) und die Frontends, mit denen Sie installiert haben (Setup-Assistent, `exp:install`, Kickstarter) |
| Lizenz | Die Lizenz des Codes, der Dokumentation und der Bilder |
| Betreuer | Wer Fragen zum Paket beantwortet, und wo |

Commit-Nachrichten folgen den Präfixen des Projekts (`Added:`, `Updated:`, `Removed:`, `Renamed:`), wie es die vorhandene Geschichte des
Repositorys tut.

## Wie die Prüfung abläuft

Seit dem 19.10.2026 hat das Repository eine eigene Anleitung für Beiträge,
[CONTRIBUTING.md](https://github.com/se7enxweb/exponential.packages.exponential.earth/blob/master/CONTRIBUTING.md), mit denselben Schritten
wie diese Seite in Kurzform. Die Betreuer von `se7enxweb/exponential.packages.exponential.earth` prüfen den Pull Request: Sie lesen Ihr
Quell-Repository, prüfen die Archive und den Eintrag im Index anhand der Checkliste, installieren das Paket in einer eigenen Sandbox,
bitten vielleicht um Änderungen und führen es zusammen, wenn es bereit ist. Sein erster Pull Request (#1, der ezwebin und die einfache
Website zur Installationsversion hinzufügte, 31.12.2023) lief genau so. Nach dem Zusammenführen veröffentlicht der Push nach `master` den
Server neu, und jede Installation, die den Index dieses Release liest, sieht Ihr Paket.

Fragen zu einem Paket, bevor Sie einen Pull Request öffnen, sind in den Issue-Trackern des Projekts willkommen; siehe
[Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md), wo welcher Tracker liegt.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](README.md)
- [Ein eigenes Seitenpaket erstellen](eigene/README.md)
- [Der Paketserver](paketserver/README.md)
- [Anforderungen an die Dokumentation von Seitenpaketen](anforderungen/README.md)
- [Wählen, wo Sie veröffentlichen](../../../eng-us/help/designers/distribution/choosing-where-to-publish.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
