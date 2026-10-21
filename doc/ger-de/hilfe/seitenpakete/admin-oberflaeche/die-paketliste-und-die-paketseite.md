---
title: Die Paketliste und die Paketseite
audience: admin, developer
category: install-upgrade
order: 101
summary: Setup > Pakete lesen - die Repositorys und ihre Zählungen, die Suche und die Filter, die Karten, Installer-Quellen und die Bestätigung beim Entfernen - und die eigene Seite eines Pakets mit ihren Details, ihrem Inhalt, Vergleichen, Prüfen und Herunterladen, für Seitenpakete und die Pakete, die sie verlangen.
keywords: Paketliste, package/list, package/view, Repositorys, Installer-Quelle, Installationszustand, nur importiert, Pakete finden, Ausgewählte entfernen, Vergleichen, Prüfen, Herunterladen, Als JSON herunterladen, Paketinhalt, package list, repositories, installer source, install state, imported only, Find packages, Remove selected, Compare, Validate, Download, package contents
type: help
---
# Die Paketliste und die Paketseite

Diese Seite ist für Administratoren, die sehen wollen, welche Pakete eine Installation enthält und was sie mitbringen. Nach einer
Installation liegen das Seitenpaket und alles, was es verlangt hat, hier, im Repository ihres Herstellers (`7x` für die Pakete von 7x).
Ansehen ändert nichts; nur die Schaltflächen tun das.

## Inhalt

1. [Die Liste öffnen](#die-liste-öffnen)
2. [Die Übersicht und die Repositorys](#die-übersicht-und-die-repositorys)
3. [Pakete finden](#pakete-finden)
4. [Die Karten](#die-karten)
5. [Installer-Quellen](#installer-quellen)
6. [Die Seite eines Pakets](#die-seite-eines-pakets)
7. [Pakete entfernen](#pakete-entfernen)

## Die Liste öffnen

**Setup > Pakete** öffnet `/package/list`: alle Repositorys auf einmal. `/package/list/<repository>` zeigt eines, zum Beispiel
`/package/list/7x`. Sie brauchen `package/list`; die Schaltflächen brauchen eigene Richtlinien
([Berechtigungen für Pakete](berechtigungen-fuer-pakete.md)).

![Die Paketliste des Repositorys 7x im Design admin4: die Zahlen (Pakete, installiert, nicht installiert, nur importiert, Installer-Quellen, Größe) und die Kacheln der Repositorys, wobei 7x als Repository des Setup-Assistenten markiert ist](../../../../eng-us/help/images/site-packages-package-list.webp)

## Die Übersicht und die Repositorys

Die Zahlen oben zählen Pakete, installierte, nicht installierte, nur importierte, Installer-Quellen, Größe, Dateien und nicht lesbare
Verzeichnisse. Darunter eine Kachel pro Repository (ein Verzeichnis von `var/storage/packages/`): `local` für Pakete, die ohne Hersteller
erstellt oder hochgeladen wurden, `7x` für das eigene Repository des Setup-Assistenten (`package.ini [RepositorySettings] Vendor`) und eine
pro anderem Hersteller. Das Repository des Setup-Assistenten ist markiert.

Ein Verzeichnis ohne `package.xml` wird gezählt (als Verzeichnis ohne Paketdefinition, nicht aufgeführt), aber nicht aufgeführt: ein Rest
eines Exports oder eines Assistenten, der vorzeitig endete. Ein Verzeichnis, dessen `package.xml` nicht gelesen werden kann, wird in einer
Warnung mit dem Grund genannt, zum Beispiel, dass `package.xml` ein anderes Paket nennt als sein Verzeichnis.

## Pakete finden

| Bedienelement | Sucht oder filtert |
|---|---|
| **Suche** | „Name, Zusammenfassung, Typ, Anbieter, Version, Betreuer oder ein benötigtes Paket.“ |
| **Paketquelle** | ein Repository oder alle |
| **Typ** | `site`, `contentclass`, `contentobject`, `sitestyle`, `extension`, ... wie vorgefunden |
| **Status** | Installiert, Nicht installiert, Nur importiert, Keine Installationselemente, Installer-Quellen |
| **Sortierung** | Name, letzte Änderung, Größe, Version, Typ, Repository, Installationszustand; **Umkehren** |

**Anwenden** macht aus der Auswahl die Adresse, zum Beispiel `/package/list/7x/(search)/demo/(type)/contentobject`, sodass sie sich als
Lesezeichen speichern lässt. **Zurücksetzen** setzt sie zurück. Die Seitengröße wird mit **Pro Seite** gewählt.

## Die Karten

Jedes Paket ist eine Karte: Name und Version, Typ, Installationszustand, Zusammenfassung, Repository und Hersteller, Betreuer, die Pakete,
die es **Benötigt** (ein fehlendes ist markiert), und die, von denen es **Benötigt von** wird, Größe und Dateien, letzte Änderung und wann
es paketiert wurde. **Ansehen** öffnet seine Seite, **Herunterladen** seine `.ezpkg`, **JSON** dasselbe Paket im JSON-Format.

Was der Installationszustand für Seitenpakete bedeutet:

| Zustand | Bedeutet | Typisches Paket |
|---|---|---|
| Nur importiert | `install_type="import"`: hier nichts zu installieren | ein Seitenpaket wie `sevenx_multisite` (seine Arbeit erledigt der Installer beim Setup) |
| Installiert | seine Installationselemente liefen auf dieser Website | `sevenx_classes`, `sevenx_multisite_democontent` nach einem Setup |
| Nicht installiert | es hat Installationselemente, die hier nicht liefen | ein Paket, das Sie hochgeladen oder abgerufen haben |
| Keine Installationselemente | `install_type="install"`, aber ein leeres `<install>` | |

Die Regeln stehen in `eZPackageCatalog::card()` (`kernel/classes/ezpackagecatalog.php`).

## Installer-Quellen

Ein Paket wird als **Installer-Quelle** markiert, wenn es im Repository des Setup-Assistenten liegt, wenn es ein Seitenpaket ist („Ein
Site-Paket, das der Setup-Assistent anbietet“) oder wenn eine andere Installer-Quelle es verlangt. Die Karte hat einen markierten Rand, und
die Seite des Pakets sagt warum. Aus diesen Paketen werden neue Installationen und veröffentlichte Pakete gebaut, weshalb das Entfernen
eines davon eine zusätzliche Bestätigung braucht.

## Die Seite eines Pakets

`/package/view/full/<name>` (außerhalb von `local` mit angehängtem `/<repository>`).

![Die Seite des Pakets sevenx_multisite: seine Version, Typ site, die Markierungen Importiert und Installer-Quelle, die Schaltflächen Herunterladen, Als JSON herunterladen, Prüfen und Entfernen, ein Hinweis, dass es nichts zu installieren gibt, weil der Installationstyp import ist, und warum es eine Installer-Quelle ist](../../../../eng-us/help/images/site-packages-package-page.webp)

Die Seite zeigt:

- **Aktionen**: Vergleichen, Installieren oder Neu installieren, Herunterladen, Als JSON herunterladen, Deinstallieren, Entfernen, Prüfen;
  jede nur, wenn Ihre Rolle es erlaubt und das Paket es kann.
- **Details**: Repository und Hersteller, Zustand, Lizenz, Betreuer, Dokumente, veröffentlicht und paketiert, die Version von Exponential,
  für die es gemacht wurde, Benötigt und Benötigt von, Größe, letzte Änderung, Beschreibung.
- **Was es enthält**: Inhaltsklassen, Inhalte (Installationselemente und Objektdateien), Erweiterungen, Einstellungsdateien (bei einem
  Seitenpaket: seine Installerdateien), andere Installationselemente, Dateien nach Art.
- **Logbuch** und **Paketinhalt**: jede Datei, filterbar, ansehbar („Hier wird nur das erste Megabyte gezeigt; Herunterladen liefert die
  ganze Datei.“) und herunterladbar.

**Vergleichen** zeigt den Inhalt des Pakets neben dem der Website, ohne an der Website etwas zu ändern; siehe
[In ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Elemente importieren](../../../../eng-us/features/6.0/package-compare-and-import.md).
**Prüfen** prüft jedes Dokument des Pakets und im JSON-Format das Schema und die Prüfsummen.

## Pakete entfernen

1. Haken Sie in der Liste Pakete an und drücken Sie **Ausgewählte entfernen** (oder **Entfernen** auf der Seite eines Pakets).
2. Die Bestätigung führt **Was entfernt wird** auf: pro Paket sein Verzeichnis, Größe und Dateien, ob es installiert ist, welche Pakete es
   verlangen und ob es eine Installer-Quelle ist.
3. Sind Installer-Quellen darunter, haken Sie „Ich verstehe, dass %count Installer-Quelle(n) gelöscht werden und dass neue Installationen
   und veröffentlichte Pakete von ihnen abhängen.“ an.
4. Drücken Sie **%count Paket(e) entfernen**.

Das Verzeichnis wird gelöscht. Die Inhalte eines installierten Pakets bleiben: Die Website weiß danach nicht mehr, aus welchem Paket sie
stammen, und das Paket lässt sich hier nicht mehr deinstallieren. Laden Sie zuerst eine Kopie herunter, wenn Sie sie vielleicht brauchen.

## Verwandte Seiten

- [Pakete in der Administration](README.md)
- [Pakete, Abschnitte 2 bis 4 und 9](../../../anleitungen/pakete.md#2-die-paketliste)
- [Das lokale Paket-Repository](../grundlagen/das-lokale-paket-repository.md)
- [Importieren oder installieren](../grundlagen/importieren-oder-installieren.md)
- [Installieren und Deinstallieren](installieren-und-deinstallieren.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
