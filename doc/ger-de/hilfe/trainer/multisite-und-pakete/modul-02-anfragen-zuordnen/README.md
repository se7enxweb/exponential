---
title: "Modul 2: Anfragen Siteaccesses zuordnen"
audience: trainer, admin, developer
category: training
order: 20
summary: Das zweite Modul von Multisite und Seitenpakete. Die Teilnehmenden ordnen Anfragen Siteaccesses nach URL-Pfad, Hostname, Host und Pfad zusammen sowie Port zu und leiten Besucher der bloßen Adresse zu ihrer Sprache weiter. 3 Stunden 45 Minuten.
keywords: multisite und seitenpakete, modul 2, zuordnung, host-zuordnung, port, sprachweiterleitung, multisite and site packages, module 2, MatchOrder, uri, host, host_uri, HostMatchMapItems, HostMatchMethod, HostUriMatchMapItems, DefaultHostUriMatchMapItems, PathPrefix, language redirect
type: help
---
# Modul 2: Anfragen Siteaccesses zuordnen

Ein Siteaccess nützt nur, wenn Anfragen ihn erreichen. Dieses Modul vermittelt jeden Weg, auf dem der Kernel eine
Anfrage einem Siteaccess zuordnet: vom ersten Teil des Pfads über Hostnamen, Host und Pfad zusammen und Ports bis zur
Weiterleitung, die Besucher von `example.com/` zu ihrer Sprache schickt. Alle Übungen verwenden `curl` mit einem
`Host`-Header, deshalb werden keine DNS-Einträge gebraucht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Dauer | 3 Stunden 45 Minuten (mit zwei Pausen von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 2.1: Zuordnung nach URL-Pfad](zuordnung-nach-url-pfad.md) | 50 Minuten | Vorhersagen, welcher Siteaccess eine Adresse beantwortet; `PathPrefix` setzen |
| [Lektion 2.2: Zuordnung nach Hostname](zuordnung-nach-hostname.md) | 55 Minuten | Einen Host `site_b` zuordnen, mit `curl` testen, `HostMatchMethod=start` verwenden |
| [Lektion 2.3: Host und Pfad zusammen und Zuordnung nach Port](zuordnung-nach-host-und-pfad-und-port.md) | 50 Minuten | Host und Pfad mit `HostUriMatchMapItems[]` zuordnen, sehen, warum die Reihenfolge zählt, die Port-Voreinstellungen ausgeben |
| [Lektion 2.4: Die Weiterleitung zur Sprache des Besuchers](die-weiterleitung-zur-sprache.md) | 50 Minuten | `DefaultHostUriMatchMapItems[]` einrichten und die 302-Antwort und ihre Cache-Header lesen |

## Dieses Modul vorbereiten

- Alle Teilnehmenden haben Modul 1 abgeschlossen: Die Siteaccesses `site_b` und `site_b_ger` antworten unter
  `/site_b` und `/site_b_ger`.
- `curl` ist dort installiert, wo die Teilnehmenden arbeiten (ihr Rechner oder der Server der Übungsumgebung), und sie
  kennen die Adresse ihrer Übungsumgebung.
- Die Übungen ändern `MatchOrder` und die Host-Zuordnungen im globalen Override. Mit einer Installation pro Person ist
  das in Ordnung; auf einem gemeinsamen Schulungsserver führen Sie die Lektionen 2.2 bis 2.4 stattdessen als
  Vorführung am Beamer durch.
- Notieren Sie die Befehle, die jede Lektion rückgängig machen (`exp:ini rem`, `exp:ini set ... MatchOrder uri`),
  falls eine Person einen sauberen Zustand möchte, oder setzen Sie die Übungsumgebung zurück wie in
  [Lektion 2.6 von Train the Trainer](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/resetting-the-lab.md).
- Wenn die Übungsumgebungen auf Exponential Velocity laufen, zeigen Sie einmal, dass jede Einstellungsänderung
  `php bin/php/console exp:velocity restart` braucht.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 2.1 | 50 |
| Lektion 2.2 | 55 |
| Pause | 10 |
| Lektion 2.3 | 50 |
| Pause | 10 |
| Lektion 2.4 | 50 |
| **Gesamt** | **225** |

## Verwandte Seiten

- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
