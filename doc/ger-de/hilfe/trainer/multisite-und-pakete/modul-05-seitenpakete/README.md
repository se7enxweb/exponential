---
title: "Modul 5: Seitenpakete"
audience: trainer, admin, developer
category: training
order: 50
summary: Das fünfte Modul von Multisite und Seitenpakete. Die Teilnehmenden verstehen Seitenpakete und Repositorys, verwenden Setup > Pakete und ezpm, erstellen ein Paket mit dem Assistenten, vergleichen ein Paket mit der Website und importieren einzelne Elemente, und laden ein Paket auf eine zweite Installation hoch und installieren es dort. 5 Stunden 20 Minuten.
keywords: multisite und seitenpakete, modul 5, seitenpaket, paket, paket-assistent, lizenz, vergleichen, importieren, hochladen, als entwürfe installieren, multisite and site packages, module 5, site package, package, ezpkg, Setup Packages, installer source, ezpm, package wizard, license, Semantic Versioning, compare, import, upload, install as drafts, install batches
type: help
---
# Modul 5: Seitenpakete

Mit Paketen bewegt Exponential Inhalte, Klassen, Erweiterungen und ganze Websites zwischen Installationen, und aus
Seitenpaketen wird jede neue Installation aufgebaut. Dieses Modul führt von der Frage, was ein Seitenpaket ist, über
die Paketseiten und die Kommandozeile bis zum Erstellen, Vergleichen, Hochladen und Installieren von Paketen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Dauer | 5 Stunden 20 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 6 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 5.1: Was ein Seitenpaket ist](was-ein-seitenpaket-ist.md) | 45 Minuten | Die Repositorys finden und die `package.xml` von `sevenx_multisite` lesen |
| [Lektion 5.2: Die Paketseiten in der Administration](die-paketseiten.md) | 45 Minuten | Einen Rundgang durch **Setup** > **Pakete** machen, ein Paket validieren und herunterladen |
| [Lektion 5.3: Pakete auf der Kommandozeile mit ezpm](pakete-auf-der-kommandozeile.md) | 55 Minuten | Die Inhalte von *Site B* verpacken, in beiden Formaten exportieren, validieren |
| [Lektion 5.4: Ein Paket mit dem Assistenten erstellen](ein-paket-mit-dem-assistenten-erstellen.md) | 50 Minuten | Ein Paket mit einer Inhaltsklasse mit Lizenz und Version erstellen |
| [Lektion 5.5: Ein Paket mit der Website vergleichen und einzelne Elemente importieren](elemente-vergleichen-und-importieren.md) | 50 Minuten | `clubb_content` mit der Website vergleichen und einen Wert zurückholen |
| [Lektion 5.6: Pakete hochladen und installieren](pakete-hochladen-und-installieren.md) | 55 Minuten | Das Paket der Nachbarn hochladen, als Entwürfe installieren, die Stapel abstimmen |

## Dieses Modul vorbereiten

- Jede Person hat eine Schulungsinstallation aus dem Standard-Seitenpaket mit *Site B* und einigen Inhalten darunter
  (Module 1 und 2), ein Administratorkonto und Shell-Zugang.
- Für Lektion 5.6 braucht jede Person eine zweite Installation zum Installieren: die Übungsumgebung der Nachbarn oder
  ein zweites `exp:install` in einem anderen Verzeichnis. Planen Sie, wie Archive zwischen den Teilnehmenden wandern
  (ein gemeinsamer Ordner ist am einfachsten).
- Prüfen Sie, dass `var/tmp/` für den Website-Benutzer beschreibbar ist; Exporte gehen nach `var/tmp/course/` und
  werden am Ende entfernt.
- Entfernen oder ersetzen Sie auf einem gemeinsamen Server nie Pakete des Repositorys `7x`: Sie sind Quellen des
  Installers.
- Felder für Betreuer verwenden Rollennamen und Adressen unter `example.invalid`.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 5.1 | 45 |
| Lektion 5.2 | 45 |
| Lektion 5.3 | 55 |
| Pause | 10 |
| Lektion 5.4 | 50 |
| Lektion 5.5 | 50 |
| Pause | 10 |
| Lektion 5.6 | 55 |
| **Gesamt** | **320** |

Teilen Sie das Modul bei einem eintägigen Zeitplan nach Lektion 5.3.

## Verwandte Seiten

- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
