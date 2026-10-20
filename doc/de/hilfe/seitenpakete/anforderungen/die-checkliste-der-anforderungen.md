---
title: Die Checkliste der Anforderungen
audience: admin, developer, designer, partner
category: install-upgrade
order: 51
summary: Jede Anforderung, die ein Seitenpaket erfüllen muss, in einer Checkliste, jede als vom Code erzwungen oder als Konvention der ausgelieferten Pakete von 7x markiert, mit der Datei, die sie prüft, und dem, was ohne sie geschieht.
keywords: Checkliste, Anforderungen an Seitenpakete, erzwungen, Konvention, package.xml, Archiv, index.xml, LICENCE, Changelog, Vorschaubild, requires, min-version, Prüfung, checklist, site package requirements, enforced, convention, archive, thumbnail, review
type: help
---
# Die Checkliste der Anforderungen

Diese Seite ist für Entwickler, die ein Seitenpaket zur Veröffentlichung vorbereiten, und für Prüfende, die eines prüfen. Sie führt jede
Anforderung an einem Ort auf. **Erzwungen** bedeutet, dass der Code ohne sie abweist, überspringt oder anhält; **Konvention** bedeutet, dass
die 16 Pakete in den Quell-Repositorys von 7x es alle tun und die Werkzeuge, die ein Paket zeigen, sich darauf verlassen. Die anderen Seiten
dieses Teils erklären jeden Punkt im Detail.

## Inhalt

1. [Das Archiv](#das-archiv)
2. [Die Paketdefinition](#die-paketdefinition)
3. [Ein Seitenpaket im Setup-Assistenten](#ein-seitenpaket-im-setup-assistenten)
4. [Der Eintrag im Index](#der-eintrag-im-index)
5. [Dokumente und Beschreibungen](#dokumente-und-beschreibungen)
6. [Vor der Veröffentlichung](#vor-der-veröffentlichung)
7. [Das Quell-Repository](#das-quell-repository)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Archiv

| | Anforderung | Kennzeichnung | Geprüft in |
|---|---|---|---|
| [ ] | Ein mit gzip komprimiertes tar-Archiv | Erzwungen | `eZPackageUploadInspector::inspect()` (gzip-Signatur); jeder Import öffnet es als `compress.zlib://` |
| [ ] | Dateiname endet auf `.ezpkg`, `.tar.gz` oder `.tgz` (`[UploadSettings] AllowedSuffixes[]`) | Erzwungen bei `package/upload` | `eZPackageUploadInspector::hasAllowedSuffix()` |
| [ ] | Dateiname ist `<name>.ezpkg`, der Name aus `<name>` | Konvention | Jede `url` der `index.xml` von 6.0.15 endet auf `<name>.ezpkg` |
| [ ] | `package.xml` (oder `package.json`) ist eine einfache Datei oben, nicht in einem Ordner | Erzwungen | `eZPackage::importUnaudited()` |
| [ ] | Jeder Eintrag ist eine einfache Datei oder ein Verzeichnis: kein symbolischer Link, kein harter Link, kein Gerät | Erzwungen | `eZPackageUploadInspector::entriesProblem()` (`unsafe_type`) |
| [ ] | Kein Pfad eines Eintrags mit `..`, führendem `/`, Laufwerksbuchstaben, umgekehrtem Schrägstrich oder Steuerzeichen | Erzwungen | `eZPackageUploadInspector::safeEntryPath()` (`unsafe_path`) |
| [ ] | Höchstens 256 MiB gepackt, 2 GiB entpackt, 50000 Einträge (Standards von `[UploadSettings]`) | Erzwungen (gepackte Größe: nur `package/upload`) | `eZPackageUploadInspector::limits()` |
| [ ] | Kein `.git`, keine Backups von Editoren, kein `node_modules` | Konvention | Die Liste `EXCLUDES` von `packages/build.sh` |

## Die Paketdefinition

| | Anforderung | Kennzeichnung | Geprüft in |
|---|---|---|---|
| [ ] | Wohlgeformtes XML, Wurzelelement `<package>`, kein DOCTYPE | Erzwungen bei `package/upload` | `eZPackageUploadInspector::definition()` |
| [ ] | Ein nicht leerer `<name>` | Erzwungen | `eZPackage::parseDOMTree()` |
| [ ] | `<name>` von der Transformation `identifier` unverändert: `a-z`, `0-9`, `_`, kein führendes, abschließendes oder doppeltes `_` | Erzwungen (`STATUS_INVALID_NAME`) | `eZPackage::isValidName()` |
| [ ] | Kein Paket desselben Namens bereits importiert | Erzwungen (`STATUS_ALREADY_EXISTS`) | `eZPackage::importUnaudited()` |
| [ ] | Elemente `<ezpublish>` und `<packaging>` vorhanden | Erzwungen bei `package/upload` (`definition_incomplete`) | `eZPackageUploadInspector::definition()` |
| [ ] | `<version><number>`, `<version><release>` und `<type value>` sind leer oder bis zu 64 Zeichen aus `0-9 A-Z a-z . _ + -` | Erzwungen (die Definition wird nicht gelesen) | `eZPackage::isValidDefinitionToken()` |
| [ ] | `<vendor>` ergibt einen brauchbaren Verzeichnisnamen (`7x` für die ausgelieferten Pakete) | Erzwungen (`invalid_vendor`) | `eZPackageRequestGuard::isSafeName()` |
| [ ] | `<summary>`, `<description>`, `<state>` vorhanden | Konvention | Von der Paketseite und dem Setup-Assistenten gezeigt |
| [ ] | `<maintainers>` mit einer Rolle aus `[MaintainerSettings] RoleList[]` | Konvention | 15 der 16 ausgelieferten Pakete nennen einen `lead` (`sevenx_extension` hat keine `<maintainers>`) |

Details: [Pflichtfelder in package.xml](pflichtfelder-in-package-xml.md), [Versionsnummern](versionsnummern.md).

## Ein Seitenpaket im Setup-Assistenten

| | Anforderung | Kennzeichnung | Geprüft in |
|---|---|---|---|
| [ ] | `<type value="site" />` | Erzwungen (sonst nicht aufgeführt) | `eZStepSiteTypes::fetchAvailableSitePackages()` filtert nach dem Typ `site` |
| [ ] | `install_type="import"` an `<package>` | Konvention | Alle 5 ausgelieferten Seitenpakete; ein Seitenpaket wird importiert, nicht installiert |
| [ ] | Jedes verlangte Paket steht in derselben `index.xml` mit einer `url` | Erzwungen (die URL ist unbekannt) | `eZStepSiteTypes::downloadDependantPackages()` |
| [ ] | Jedes verlangte Paket lässt sich holen und installieren | Erzwungen (`EZSW-050`, `EZSW-051`) | `eZStepCreateSites` |
| [ ] | Jedes `<require>` hat `type="ezpackage"`, `name` und `min-version` | Erzwungen: Der Schritt zum Anlegen der Websites installiert nur Anforderungen `type="ezpackage"`; der Kickstart-Weg liest `min-version` ohne Rückfall | `ezstep_create_sites.php`, `ezstep_site_types.php` |
| [ ] | Jede von `<settings-file filename>` genannte Datei existiert in `settings/` | Konvention (eine fehlende wird stillschweigend übersprungen) | `ezstep_create_sites.php` (`file_exists()` vor `include_once`) |

Details: [Abhängigkeiten angeben](abhaengigkeiten-angeben.md).

## Der Eintrag im Index

| | Anforderung | Kennzeichnung |
|---|---|---|
| [ ] | Attribute `name`, `type` und `url` am Element `<package>` der `index.xml` | Erzwungen: `name` ist der Schlüssel, `type="site"` führt es auf, `url` wird heruntergeladen |
| [ ] | Attribute `summary`, `description`, `version` | Konvention: im Schritt der Seitentypen als „summary (ver. version)“ gezeigt |
| [ ] | `thumbnail_url`, die auf `thumbnails/<name>.png` zeigt | Konvention: gezeigt, bevor das Paket importiert ist |

Details: [Das Format index.xml](../paketserver/das-format-index-xml.md), [Vorschaubilder und Screenshots](vorschaubilder-und-screenshots.md).

## Dokumente und Beschreibungen

| | Anforderung | Kennzeichnung |
|---|---|---|
| [ ] | `<licence>` mit einem Lizenzbezeichner | Konvention (nur die Assistenten zum Erstellen bestehen darauf) |
| [ ] | `documents/LICENCE`, in `<documents>` genannt | Konvention: alle 16 ausgelieferten Pakete |
| [ ] | Ein `<changelog><entry>` pro Release, mit `timestamp`, `person`, `release` und einem `<change>` pro Änderung | Konvention |
| [ ] | Ein Vorschaubild in der Dateisammlung `default` | Konvention: vom Assistenten und der Paketseite gezeigt |

Details: [Lizenz und Dokumente](lizenz-und-dokumente.md), [Einträge im Changelog](eintraege-im-changelog.md).

## Vor der Veröffentlichung

- [ ] `xmllint --noout package.xml` gibt nichts aus.
- [ ] `php ezpm.php validate <archive>` endet mit `PASS`.
- [ ] Das Archiv lässt sich auf `package/upload` einer Testinstallation hochladen.
- [ ] Eine frische Installation mit dem Paket gelingt in einer Sandbox.

Details: [Ein Paket vor der Veröffentlichung prüfen](ein-paket-vor-der-veroeffentlichung-pruefen.md),
[In einer Sandbox testen](../eigene/in-einer-sandbox-testen.md).

## Das Quell-Repository

- [ ] Ein README, das sagt, was das Paket installiert und wie man es baut (Konvention).
- [ ] Eine Datei LICENSE im Wurzelverzeichnis (Konvention).
- [ ] Betreffzeilen von Commits mit den Präfixen `Added:`, `Updated:`, `Removed:`, `Renamed:` (Konvention).

Details: [README und Dokumentation im Repository](readme-und-dokumentation-im-repository.md).

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Die Datei ezpkg](../grundlagen/die-datei-ezpkg.md)
- [Anleitung zu Paketen](../../../../en/us/guides/packages.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
