---
title: "Modul 11: Die REST-API"
audience: trainer, developer
category: training
order: 110
summary: Das elfte Modul des Kurses Entwickler. Die Teilnehmenden kartieren die REST-Schnittstelle und ihre Einstellungen, rufen sie mit einem persönlichen API-Schlüssel aus curl auf, lesen und schreiben Inhalte über v1 und v2 und schreiben einen eigenen REST-Provider. 2 Stunden 45 Minuten.
keywords: entwickler, modul 11, REST, API, ezprestapi, v1, v2, persönlicher API-schlüssel, apikey/create, curl, OAuth2, REST-provider, rest.ini, developers course, module 11, personal API key, REST provider
type: help
---
# Modul 11: Die REST-API

Apps und andere Systeme erreichen Exponential über seine REST-Schnittstelle. Dieses Modul kartiert die Schnittstelle,
lässt die Teilnehmenden sie mit einem persönlichen API-Schlüssel aufrufen und Inhalte darüber schreiben und endet mit
einem eigenen Provider, der die Daten der Erweiterung unter einem eigenen Präfix ausliefert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 2 Stunden 45 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 3 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 11.1: Die REST-API auf einen Blick](die-rest-api-im-ueberblick.md) | 40 Minuten | Provider, Routen, Versionen und `rest.ini` lesen |
| [Lektion 11.2: Die API mit einem persönlichen Schlüssel aufrufen](die-api-mit-einem-schluessel-aufrufen.md) | 55 Minuten | Schlüssel erlauben, einen erzeugen, mit `curl` lesen und schreiben, ihn widerrufen |
| [Lektion 11.3: Ein eigener REST-Provider](ein-eigener-rest-provider.md) | 60 Minuten | Routen, View-Controller, Controller; `/api/mysite/v1/greetings` |

## Dieses Modul vorbereiten

- `ezprestapi` muss auf den Schulungsinstallationen aktiv sein; prüfen Sie das mit `exp:ini get
  site.ini/ExtensionSettings/ActiveExtensions`.
- Alle Teilnehmenden brauchen ein Administratorkonto und einen Testbenutzer mit einer E-Mail-Adresse unter
  `example.invalid`.
- Kennen Sie Adresse und Port der Installation jeder Person für die `curl`-Zeilen (die Beispiele verwenden
  `http://127.0.0.1:8087`).
- Proben Sie Lektion 11.3 vollständig auf Ihrer eigenen Installation; halten Sie die fertigen Klassen zum Austeilen
  bereit.
- Erinnern Sie die Teilnehmenden daran, Schlüssel niemals zu committen oder irgendwo einzufügen; sie widerrufen ihre
  Schlüssel am Ende von Lektion 11.2.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 11.1 | 40 |
| Lektion 11.2 | 55 |
| Pause | 10 |
| Lektion 11.3 | 60 |
| **Gesamt** | **165** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
