---
title: "Modul 4: Links zwischen Websites"
audience: trainer, admin, developer
category: training
order: 40
summary: Das vierte Modul von Multisite und Seitenpakete. Die Teilnehmenden erklären und konfigurieren mit ezurlmultisite Links von einer Website auf Inhalte einer anderen und betreiben mehrere Websites aus einer Administration mit Rollen, die auf jede Website beschränkt sind. 2 Stunden 25 Minuten.
keywords: multisite und seitenpakete, modul 4, websiteübergreifende links, eine administration, teilbaum, multisite and site packages, module 4, cross-site links, ezurlmultisite, RootNodeList, SiteAccessList, ezurl_roots, SiteList, FrontSiteAccess, Assign with limitation, subtree
type: help
---
# Modul 4: Links zwischen Websites

Websites, die einen Inhaltsbaum teilen, verlinken aufeinander, und die Teams, die sie bearbeiten, teilen eine
Administration. Dieses Modul schließt den Multisite-Teil des Kurses ab: warum Links in eine andere Website
kaputtgehen und wie `ezurlmultisite` sie repariert, wie man eine neue Website zu seinen Einstellungen hinzufügt und
wie man jedes Team in seiner eigenen Website hält.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Dauer | 2 Stunden 25 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 3 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 4.1: Warum Links auf eine andere Website kaputtgehen](warum-links-zu-einer-anderen-website-brechen.md) | 35 Minuten | Kaputte und aufgelöste websiteübergreifende Links für eine gegebene Installation vorhersagen |
| [Lektion 4.2: Websiteübergreifende Links konfigurieren](websiteuebergreifende-links-konfigurieren.md) | 50 Minuten | `site_b` zu `ezurlmultisite` hinzufügen, die Caches leeren, einem Link von der Hauptwebsite folgen |
| [Lektion 4.3: Eine Administration für mehrere Websites](eine-administration-fuer-mehrere-websites.md) | 50 Minuten | Die Editor-Rolle für ein Team auf *Site B* beschränken und mit einem Testbenutzer testen |

## Dieses Modul vorbereiten

- Die Übungsumgebungen der Teilnehmenden stammen aus den Modulen 1 bis 3; `site_b` hat einen `PathPrefix` (Lektion
  2.1, Schritt 6, nicht rückgängig gemacht). Bitten Sie Teilnehmende, die ihn rückgängig gemacht haben, ihn wieder zu
  setzen.
- Prüfen Sie in einer Übungsumgebung, ob der Installer `settings/override/ezurlmultisite.ini.append.php` geschrieben
  hat (bei den Multisite-Seitenpaketen tut er das), und zeigen Sie sie einmal am Beamer.
- Für Lektion 4.2 muss der Suchindex den Ordner *News* enthalten: Führen Sie vorher in jeder Übungsumgebung
  `php bin/php/updatesearchindex.php` aus, wenn die Suchergebnisse leer sind.
- Für Lektion 4.3 brauchen die Teilnehmenden ein Administratorkonto; Testbenutzer bekommen Adressen unter
  `example.invalid`, und die Schulungsinstallation darf keine echten E-Mails versenden.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 4.1 | 35 |
| Lektion 4.2 | 50 |
| Pause | 10 |
| Lektion 4.3 | 50 |
| **Gesamt** | **145** |

## Verwandte Seiten

- [Multisite und Seitenpakete: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
