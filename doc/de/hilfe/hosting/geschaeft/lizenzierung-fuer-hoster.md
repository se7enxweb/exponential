---
title: Lizenzierung für Hoster
audience: hosting
category: hosting
order: 90
summary: Was die GNU GPL v2 oder später für einen Hosting-Anbieter bedeutet - das Hosten von Kunden-Websites, vorinstallierte Images und Ein-Klick-Installer, geänderter Code, Erweiterungen mit anderen Lizenzen und das Kopieren dieser Dokumentation. In einfachen Worten, keine Rechtsberatung.
keywords: Lizenz, GPL, GPL-2.0-or-later, Hosting, Weitergabe, Image, Installer, Ein-Klick, geänderter Code, Patches, Quellcode, MIT, Velocity, GFDL, Dokumentation, Urheberrechtshinweis, license, distribution, one-click, changed code, source code, documentation, copyright notice
type: help
---
# Lizenzierung für Hoster

Diese Seite ist für Führungskräfte und Systemadministratoren bei Hosting-Anbietern. Sie beantwortet die Lizenzfragen,
die aufkommen, wenn Sie Exponential für andere hosten: ob Sie es hosten dürfen, ob Sie es in Images und Installern
ausliefern dürfen, was gilt, wenn Sie es ändern, und was mit Erweiterungen und dieser Dokumentation ist.

> **Warnung**
> Dies ist eine Zusammenfassung in einfachen Worten, keine Rechtsberatung. Es gelten die Lizenztexte im Repository: die
> GNU General Public License Version 2 in `LICENSE.md` und `LICENSE` und die GNU Free Documentation License 1.3 in
> [doc/en/us/LICENSE-GFDL-1.3.txt](../../../../en/us/LICENSE-GFDL-1.3.txt). Fragen Sie für einen bestimmten Tarif Ihren
> rechtlichen Berater. Die Sicht des Käufers auf dieselben Lizenzen steht auf
> [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md).

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Kunden-Websites hosten](#kunden-websites-hosten)
3. [Images, Vorlagen und Ein-Klick-Installer](#images-vorlagen-und-ein-klick-installer)
4. [Wenn Sie den Code ändern](#wenn-sie-den-code-ändern)
5. [Erweiterungen und andere Komponenten](#erweiterungen-und-andere-komponenten)
6. [Diese Dokumentation kopieren](#diese-dokumentation-kopieren)
7. [Fragen an Ihren rechtlichen Berater](#fragen-an-ihren-rechtlichen-berater)

## Kurz gesagt

- Exponential steht unter der **GNU GPL v2 oder einer späteren Version** (`COPYRIGHT.md`, `composer.json`:
  `"license": "GPL-2.0-or-later"`).
- Es gibt **keine Lizenzgebühr**: Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos, und die
  Professional License ist nicht mehr erhältlich (`README.md`).
- Das **Ausführen** der Software ist nicht eingeschränkt (GPL Abschnitt 0). Eine Website zu hosten heißt, sie auszuführen.
- Das **Weitergeben** von Kopien (an andere verschenken oder verkaufen) bringt die Bedingungen der GPL mit sich: die
  Hinweise erhalten, den Quellcode bereitstellen, dieselben Rechte weitergeben.

## Kunden-Websites hosten

Abschnitt 0 der GPL: Das Ausführen des Programms wird nicht eingeschränkt. Sie dürfen Exponential auf so vielen Servern
für so viele Kunden betreiben, wie Sie möchten, ohne jemanden zu fragen und ohne eine Gebühr zu zahlen. Nichts in der
Lizenz bindet die Software an einen bestimmten Hoster; Ihre Kunden können jederzeit zu einem anderen Hoster wechseln, mit
denselben Rechten.

## Images, Vorlagen und Ein-Klick-Installer

Wenn Sie einem Kunden eine Kopie der Software übergeben (zum Beispiel ein Image einer virtuellen Maschine, das er
herunterlädt, oder Dateien, die Sie in sein Konto legen und die er dann besitzt und mitnehmen darf), geben Sie sie
möglicherweise weiter. In diesem Fall gelten die Bedingungen:

| Bedingung | Was sie für ein Image oder einen Installer bedeutet |
|---|---|
| Die Hinweise erhalten | lassen Sie `COPYRIGHT.md`, `LICENSE.md` und die Hinweise in den Dateien, wie sie sind |
| Den Quellcode bereitstellen | Exponential ist PHP, die Dateien sind also der Quellcode; wenn Sie sie ändern, stellen Sie die geänderten Dateien bereit |
| Dieselbe Lizenz | Ihre geänderte Version von Exponential steht als Ganzes unter der GPL |
| Keine zusätzlichen Einschränkungen | fügen Sie keine Bedingungen hinzu, die dem Kunden seine Rechte aus der GPL nehmen |

Praktische Regel: Bauen Sie Images und Installer aus den öffentlichen Repositories und Paketen, lassen Sie sie, wo Sie
können, unverändert, und legen Sie Ihre eigene Konfiguration in Dateien, die zur Installation gehören, etwa
`settings/override` ([Bereitstellung automatisieren](../bereitstellung/bereitstellung-automatisieren.md)).

## Wenn Sie den Code ändern

- Eine Änderung, die Sie auf Ihren eigenen Servern behalten und nur ausführen, wird nicht weitergegeben.
- Eine Änderung, die Sie Kunden mit ihrer Kopie geben, wird weitergegeben: Kennzeichnen Sie die geänderten Dateien mit
  einem Hinweis und Datum (GPL Abschnitt 2a), und stellen Sie den geänderten Quellcode bereit.
- In beiden Fällen erspart Ihnen das Beitragen der Änderung an das Projekt, sie bei jedem Update erneut anzuwenden
  ([Mit dem Projekt und 7x zusammenarbeiten](mit-dem-projekt-und-7x-zusammenarbeiten.md)). Beiträge erfolgen unter derselben
  Lizenz (`CONTRIBUTING.md`).

## Erweiterungen und andere Komponenten

| Komponente | Lizenz | Wo Sie es prüfen |
|---|---|---|
| Der Kernel | GPL v2 oder später | `COPYRIGHT.md`, `composer.json` |
| Jede Erweiterung | ihre eigene, angegeben in `ezinfo.php` und `extension.xml` | **Info > Über** in der Administration |
| Exponential Velocity | MIT | seine `composer.json` |
| Komponenten von Drittanbietern | ihre eigene | die Tabelle **Software von Drittanbietern** auf der Seite Über |

Prüfen Sie die Seite Über der Installation, die Sie ausliefern, bevor Sie sagen, alles stehe unter der GPL
([Seiten Über und Copyright](../../../../en/us/features/6.0/about-and-package-pages.md)).

## Diese Dokumentation kopieren

Die Hilfe, einschließlich dieser Hosting-Seiten, steht unter der GNU FDL 1.3 oder später. Sie dürfen sie in Ihre eigene
Kundendokumentation kopieren, übersetzen und anpassen, unter den Bedingungen der Lizenz: die Hinweise erhalten, sie unter
derselben Lizenz halten, sagen, was Sie geändert haben ([Freie Dokumentation](../../freie-dokumentation.md)). Ein Link auf
die Seiten ist an gar keine Bedingungen geknüpft.

## Fragen an Ihren rechtlichen Berater

1. Geben wir Kunden Kopien der Software, die sie dann selbst kontrollieren? Wenn ja, erfüllen unsere Images und Installer
   die Bedingungen für die Weitergabe?
2. Ändern wir Exponential oder Erweiterungen, und verlassen diese Änderungen unsere Server?
3. Kombinieren wir Exponential mit proprietärem Code, den wir weitergeben?
4. Welche Erweiterungen installieren wir vor, und unter welchen Lizenzen stehen sie?

## Verwandte Seiten

- [Lizenzierung für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Ein-Klick-Installer](../bereitstellung/ein-klick-installer-fuer-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
