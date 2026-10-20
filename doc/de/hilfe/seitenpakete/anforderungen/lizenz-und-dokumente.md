---
title: Lizenz und Dokumente
audience: developer, designer, partner, admin
category: install-upgrade
order: 53
summary: Wie ein Seitenpaket seine Lizenz angibt, im Element licence von package.xml und in der Datei documents/LICENCE, wie package.ini LicenseSettings aus einem gespeicherten Wert eine benannte Lizenz mit Link macht und was die ausgelieferten Pakete von 7x mitbringen.
keywords: licence, Lizenz, LICENCE, documents, LicenseSettings, LicenseList, AliasList, GPL, GPL-2.0-or-later, SPDX, Paketlizenz, Element document, mime-type, license, package license, document element
type: help
---
# Lizenz und Dokumente

Diese Seite ist für Entwickler und Designer, die ein Seitenpaket veröffentlichen, und für Prüfende, die eines prüfen. Ein Paket, das nicht
sagt, unter welcher Lizenz es veröffentlicht wird, gibt niemandem das Recht, es zu nutzen, zu ändern oder weiterzugeben. Diese Seite erklärt
die zwei Stellen, an denen ein Paket seine Lizenz angibt, wie Exponential sie zeigt und was die ausgelieferten Pakete tun. Sie ist keine
Rechtsberatung; zur Wahl einer Lizenz siehe [Lizenzwahl: ein Überblick](../../../../en/us/help/designers/distribution/licensing-choices-overview.md).

## Inhalt

1. [Zwei Stellen, beide Konventionen](#zwei-stellen-beide-konventionen)
2. [Das Element licence](#das-element-licence)
3. [Wie eine gespeicherte Lizenz gezeigt wird](#wie-eine-gespeicherte-lizenz-gezeigt-wird)
4. [Das Element documents und documents/LICENCE](#das-element-documents-und-documentslicence)
5. [Was die ausgelieferten Pakete mitbringen](#was-die-ausgelieferten-pakete-mitbringen)
6. [Checkliste](#checkliste)
7. [Verwandte Seiten](#verwandte-seiten)

## Zwei Stellen, beide Konventionen

| Wo | Form | Erzwungen |
|---|---|---|
| `<licence>` in `package.xml` | Ein Bezeichner, etwa `GPL-2.0-or-later` | Nur von den Assistenten zum Erstellen von Paketen, die eine strikte Auswahl anbieten. Ein Import akzeptiert jeden Text oder keinen |
| `documents/LICENCE` im Archiv, in `<documents>` genannt | Eine einfache Textdatei | Nein. Die Paketseite verlinkt sie, wenn sie da ist |

Beide Schreibweisen, `<licence>` und `LICENCE`, sind die wörtlichen Namen, die das Paketformat nutzt, und bleiben, wie sie sind. Von jedem
auf dem Paketserver veröffentlichten Paket wird erwartet, dass es beides mitbringt; Prüfende achten darauf
([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)).

## Das Element licence

`eZPackage::parseDOMTree()` liest den Text des ersten Elements `<licence>` und behält ihn, wie er ist. Wenn der Kernel ein Paket schreibt
(`eZPackage::domStructure()`), folgt `<licence>` auf `<version>` und den `<timestamp>` des Release:

```xml
<version>
  <number>6.0</number>
  <release>15-stable</release>
</version>
<licence>GPL-2.0-or-later</licence>
<state>stable</state>
```

Die Assistenten zum Erstellen (`kernel/classes/ezpackagecreationhandler.php`) weisen eine Lizenz ab, die nicht in `[LicenseSettings]
LicenseList[]` von `settings/package.ini` steht (und verlangen, eine aus der Liste zu wählen), und speichern den Bezeichner. Ein von Hand
geschriebenes Paket sollte dieselben Bezeichner nutzen: einen SPDX-Bezeichner, wo es einen gibt
([spdx.org/licenses](https://spdx.org/licenses/)), sonst `LicenseRef-<name>`.

## Wie eine gespeicherte Lizenz gezeigt wird

Die Paketseite (`package/view/full/<name>`) zeigt die Lizenz über `eZPackageLicense::describe()` in `kernel/classes/ezpackagelicense.php`:

| Gespeicherter Wert | Gezeigt als |
|---|---|
| Ein Bezeichner aus `LicenseList[]`, etwa `GPL-2.0-or-later` | Der `Name=` der Lizenz als Link auf ihre `URL=`, gefolgt vom Bezeichner |
| Ein Alias aus `[LicenseSettings] AliasList[]`, etwa `GPL` | Die Lizenz, für die er steht, plus „gespeichert als GPL“ |
| Jeder andere Text | Der Text, wie er ist, ohne Link |
| Nichts | Ein Gedankenstrich |

Die ausgelieferten Einstellungen definieren 46 Lizenzen in neun Gruppen, `DefaultLicense=GPL-2.0-or-later` und einen Alias:

```ini
[LicenseSettings]
AliasList[]
AliasList[GPL]=GPL-2.0-or-later
```

Der Kommentar darüber erklärt warum: Pakete, die erstellt wurden, bevor die Lizenz eine Wahl wurde, speichern `GPL`, was immer die GNU GPL
Version 2 oder (nach Ihrer Wahl) jede spätere Version bedeutete, die Lizenz von Exponential selbst. Wie Sie der Liste eine eigene Lizenz
hinzufügen, ist in [Paketlizenzen und Semantic Versioning](../../../../en/us/features/6.0/package-licenses-and-versions.md) beschrieben.

## Das Element documents und documents/LICENCE

Dokumente sind Dateien für den Leser eines Pakets, aufbewahrt im Verzeichnis `documents/` des Pakets (`eZPackage::documentDirectory()`).
`package.xml` nennt jedes:

```xml
<documents>
  <document mime-type="text/plain" name="LICENCE" />
</documents>
```

| Attribut | Gelesen | Hinweise |
|---|---|---|
| `name` | ja | Der Dateiname unter `documents/` |
| `mime-type` | ja | `text/plain`, wenn der Schreiber keinen bekommt |
| `os`, `audience` | ja | Wahlweise; die ausgelieferten Pakete lassen sie weg |

Was mit einem Dokument geschieht:

- **Der Export** behält es: `eZPackage::exportToArchive()` kopiert `documents/`, `files/`, `simplefiles/`, `settings/` und die
  Unterverzeichnisse der Installationselemente, und sonst nichts.
- **Die Paketseite** führt die Dokumente unter **Details** auf. Jedes, das im Archiv liegt, öffnet sich im Inhaltsbrowser der Seite (Issue
  #389); ein direkter Link auf die Datei unter `var/storage` wird vom Webserver abgewiesen.
- **Nichts weist** ein Paket ohne Dokumente ab oder eines, das ein Dokument nennt, das es nicht mitbringt.

> **Hinweis**
> Vor 6.0.15 ging eine Datei oben im Paket, die nicht in einem der exportierten Verzeichnisse lag, verloren, wenn das Paket erneut aus der
> Administration exportiert wurde. Ab 6.0.15 nimmt der Export jede Datei und jeden Ordner oben mit (`eZPackage::extraTopLevelEntries()`;
> keine verborgenen, keine symbolischen Links). Behalten Sie `LICENCE` trotzdem in `documents/`: Dort führt die Paketseite Dokumente auf.

## Was die ausgelieferten Pakete mitbringen

In den zwei Quell-Repositorys am 18.10.2026 geprüft:

- Alle 16 Pakete haben `<licence>GPL</licence>`, gezeigt als „GNU General Public License v2.0 or later (gespeichert als GPL)“.
- Alle 16 haben `documents/LICENCE`, einen GPL-Hinweis mit 15 Zeilen (Version 2 der Lizenz oder, nach Ihrer Wahl, jede spätere Version),
  in `<documents>` genannt.
- Die erste Zeile jeder Datei nennt das Paket (dass die Datei Teil des Pakets ... ist). Bis zum 19.10.2026 nannten drei davon ein anderes
  Paket, ein Rest des Kopierens: `sevenx_multisite_clean` nannte `sevenx_multisite`, und beide Pakete `sevenx_multisite_democontent` nannten
  `sevenx_democontent`. Am 19.10.2026 im Quell-Repository behoben, und die neu gebauten Pakete liegen auf dem Paketserver.
- Beide Quell-Repositorys haben seit dem 19.10.2026 eine Datei `LICENSE` mit dem vollständigen Text der GNU GPL Version 2.

> **Tipp**
> Wenn Sie ein Paket forken, ändern Sie die erste Zeile von `documents/LICENCE` auf den neuen Paketnamen und fügen Sie eine eigene
> Copyright-Zeile hinzu, wenn Ihre Änderungen erheblich sind
> ([Copyright- und Autorenzeilen](../../../../en/us/help/designers/extensions/copyright-and-author-lines.md)).

## Checkliste

- [ ] `<licence>` enthält einen Bezeichner aus `[LicenseSettings] LicenseList[]` (oder `GPL`, den Alias, den die ausgelieferten Pakete
      nutzen).
- [ ] `documents/LICENCE` existiert und nennt dieselbe Lizenz.
- [ ] `<documents>` nennt sie mit `mime-type="text/plain"`.
- [ ] Die erste Zeile von `LICENCE` nennt dieses Paket.
- [ ] Bilder, Schriften oder Skripte unter anderen Bedingungen sind mit ihrer Lizenz im README des Quell-Repositorys genannt
      ([README und Dokumentation im Repository](readme-und-dokumentation-im-repository.md)).

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Die Checkliste der Anforderungen](die-checkliste-der-anforderungen.md)
- [Paketlizenzen und Semantic Versioning](../../../../en/us/features/6.0/package-licenses-and-versions.md)
- [Die Datei LICENSE eines Themes](../../../../en/us/help/designers/extensions/license-file.md)
- [Eine Lizenz im Paketassistenten wählen](../../../../en/us/help/designers/extensions/package-licenses-in-the-wizard.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
