---
title: Anforderungen an die Dokumentation von Seitenpaketen
audience: admin, developer, designer, partner
category: install-upgrade
order: 50
summary: Was ein Seitenpaket mitbringen muss, bevor es importiert, vom Setup-Assistenten angeboten und auf dem Paketserver veröffentlicht werden kann, aufgeteilt in das, ohne das der Code es abweist, und das, was die ausgelieferten Pakete von 7x nach Konvention mitbringen, mit einer kurzen Checkliste und einem Wegweiser zu den Seiten dieses Teils.
keywords: Anforderungen an Seitenpakete, package.xml, Pflichtfelder, Lizenz, LICENCE, Changelog, Version, Vorschaubild, Abhängigkeiten, requires, prüfen, Checkliste, Upload-Inspektor, site package requirements, required fields, license, thumbnail, dependencies, validate, checklist, upload inspector
type: help
---
# Anforderungen an die Dokumentation von Seitenpaketen

Dieser Teil der Hilfe zu Seitenpaketen ist für alle, die ein Seiteninstallationspaket bauen, ändern oder prüfen: einen Entwickler, der
`sevenx_multisite` forkt, einen Designer, der eine Startseite ausliefert, einen Partner, der ein Paket für einen Kunden vorbereitet, oder
jemanden, der einen Pull Request an den Paketserver prüft. Er beantwortet eine Frage: **Was muss ein Paket mitbringen, und was sollte es
mitbringen, bevor es veröffentlicht wird?**

Jede Anforderung hier stammt aus dem Code, der ein Paket liest (`kernel/classes/ezpackage.php`,
`kernel/classes/ezpackageuploadinspector.php`, `kernel/classes/exppackagejson.php`, die Schritte des Setup-Assistenten in
`kernel/setup/steps/`), und aus den 16 Paketen in den zwei Quell-Repositorys von 7x,
[se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage) (14 Pakete) und
[se7enxweb/cjw_multisite-ezpackage](https://github.com/se7enxweb/cjw_multisite-ezpackage) (2 Pakete).

## Inhalt

1. [Zwei Arten von Anforderungen](#zwei-arten-von-anforderungen)
2. [Die Checkliste in Kürze](#die-checkliste-in-kürze)
3. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
4. [Wo die Anforderungen geprüft werden](#wo-die-anforderungen-geprüft-werden)
5. [Verwandte Seiten](#verwandte-seiten)

## Zwei Arten von Anforderungen

Jede Seite markiert jeden Punkt mit einer von zwei Kennzeichnungen:

| Kennzeichnung | Bedeutung | Was geschieht, wenn er fehlt |
|---|---|---|
| **Erzwungen** | Der Code weist das Paket ab, überspringt es oder stoppt die Installation | Der Import scheitert, der Assistent führt das Paket nicht auf, oder die Installation endet mit einem Fehler |
| **Konvention** | Jedes ausgelieferte Paket von 7x tut es, und die Seiten und Werkzeuge, die ein Paket zeigen, verlassen sich darauf | Das Paket funktioniert, aber ein Leser, ein Prüfer oder ein späteres Release hat weniger Anhaltspunkte |

Ein Pull Request an den Paketserver wird gegen beides geprüft. „Es lässt sich importieren“ ist die niedrigste Hürde, nicht das Ziel: siehe
[Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Die Checkliste in Kürze

Die vollständige Liste mit dem Grund für jeden Punkt ist [Die Checkliste der Anforderungen](die-checkliste-der-anforderungen.md).

- [ ] **Erzwungen**: Das Archiv ist ein mit gzip komprimiertes tar mit `package.xml` oben, nur einfachen Dateien und Verzeichnissen, ohne `..`
      und ohne absolute Pfade.
- [ ] **Erzwungen**: `<name>` besteht nur aus Kleinbuchstaben, Ziffern und `_`. **Konvention**: Die Datei `.ezpkg` trägt denselben Namen.
- [ ] **Erzwungen** (Hochladen von Paketen in der Administration): `<ezpublish>` und `<packaging>` sind vorhanden.
- [ ] **Erzwungen**: `<version><number>`, `<release>` und `<type value>` sind einfache Tokens von höchstens 64 Zeichen.
- [ ] **Erzwungen** (Setup-Assistent): Ein Seitenpaket hat `<type value="site" />`, und jedes Paket, das es verlangt, steht in derselben
      `index.xml` mit einer `url`.
- [ ] **Konvention**: `<vendor>`, `<summary>`, `<description>`, `<licence>`, `<state>`, ein Dokument `LICENCE`, ein Eintrag im Changelog pro
      Release, ein Vorschaubild in der Dateisammlung `default`.
- [ ] **Konvention**: Das Paket besteht die Prüfung (**Prüfen** auf seiner Seite oder `php ezpm.php validate`), bevor es veröffentlicht
      wird.
- [ ] **Konvention**: Das Quell-Repository erklärt das Paket in einem README und nennt seine Lizenz.

## Die Seiten dieses Teils

| Seite | Was sie behandelt |
|---|---|
| [Die Checkliste der Anforderungen](die-checkliste-der-anforderungen.md) | Jeder Punkt in einer Tabelle, mit Kennzeichnung und Quelle |
| [Pflichtfelder in package.xml](pflichtfelder-in-package-xml.md) | Die Regeln für Namen, Versions-Tokens, `<ezpublish>`, `<packaging>`, Hersteller und Typ |
| [Lizenz und Dokumente](lizenz-und-dokumente.md) | `<licence>`, `documents/LICENCE`, `[LicenseSettings]` |
| [Einträge im Changelog](eintraege-im-changelog.md) | `<changelog><entry>`, was die Paketseite zeigt |
| [Versionsnummern](versionsnummern.md) | `<number>` und `<release>`, wie sie verglichen werden, die Version in `<ezpublish>` |
| [Vorschaubilder und Screenshots](vorschaubilder-und-screenshots.md) | `files/default/thumbnail`, `thumbnail_url` in `index.xml` |
| [Abhängigkeiten angeben](abhaengigkeiten-angeben.md) | `<requires>`, `min-version`, Reihenfolge, die flache Liste |
| [Ein Paket vor der Veröffentlichung prüfen](ein-paket-vor-der-veroeffentlichung-pruefen.md) | Prüfen, der Upload-Inspektor, `xmllint`, `ezpm.php validate` |
| [README und Dokumentation im Repository](readme-und-dokumentation-im-repository.md) | Was das Quell-Repository mitbringen sollte |

## Wo die Anforderungen geprüft werden

Ein Paket trifft an vier Stellen auf den Code, und jede prüft etwas anderes:

| Wo | Code | Prüft |
|---|---|---|
| Jeder Import (Download im Setup-Assistenten, `ezpm.php import`, Hochladen in der Administration) | `eZPackage::importUnaudited()` | Einträge des Archivs, eine lesbare Definition mit Namen, gültiger Name, sicheres Herstellerverzeichnis, noch nicht vorhanden |
| Hochladen von Paketen in der Administration (`package/upload`) | `eZPackageUploadInspector::inspect()`, dann `expPackageJson::validateDirectory()` | Zusätzlich: Dateiendung, gzip-Signatur, Größengrenzen, Wurzel `<package>`, kein DOCTYPE, `<ezpublish>` und `<packaging>`, jedes Dokument wohlgeformt |
| Der Schritt der Seitentypen im Setup-Assistenten | `kernel/setup/steps/ezstep_site_types.php` | Typ `site`, der Eintrag im Index, jedes verlangte Paket herunterladbar und neu genug |
| Der Schritt zum Anlegen der Websites | `kernel/setup/steps/ezstep_create_sites.php` | Jedes verlangte Paket lässt sich holen und installieren (Fehler `EZSW-050`, `EZSW-051`) |

> **Hinweis**
> Die eigenen Wege des Setup-Assistenten zum Herunterladen und Hochladen rufen `eZPackage::import()` direkt auf, ohne die Prüfungen der
> Definition durch den Upload-Inspektor. Ein Paket, das sich über den Assistenten importieren lässt, kann von der Seite zum Hochladen in der
> Administration dennoch abgewiesen werden. Testen Sie beides, wie
> [Ein Paket vor der Veröffentlichung prüfen](ein-paket-vor-der-veroeffentlichung-pruefen.md) beschreibt.

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Glossar](../glossar-der-seitenpakete.md)
- [Referenz zu package.xml](../grundlagen/referenz-zu-package-xml.md)
- [Das Format index.xml](../paketserver/das-format-index-xml.md)
- [Anleitung zu Paketen](../../../anleitungen/pakete.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
