---
title: "Modul 6: Module und Views"
audience: trainer, developer
category: training
order: 60
summary: Das sechste Modul des Kurses Entwickler. Die Teilnehmenden lesen module.php-Dateien, schreiben eine View mit Parametern und einem Template, schützen sie mit einer Richtlinienfunktion, die über eine Rolle vergeben wird, und verarbeiten ein Formular mit Aktionen, Validierung, Weiterleitung und Formular-Token. 3 Stunden 35 Minuten.
keywords: entwickler, modul 6, modul, view, module.php, ViewList, Params, Result, richtlinie, FunctionList, rolle, single_post_actions, isCurrentAction, redirectTo, ezformtoken, formular, developers course, module 6, policy, role, form
type: help
---
# Modul 6: Module und Views

Über eine Modul-View bekommt eine Erweiterung eigene Seiten. Dieses Modul erklärt, wie eine Adresse zu einer View
gelangt; danach bauen die Teilnehmenden die View `hello/greet` Schritt für Schritt auf: Parameter und ein Template,
eine Richtlinienfunktion und eine Rolle und schließlich ein Formular mit einer Aktion, Validierung und einer
Weiterleitung, geschützt durch den Formular-Token.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 3 Stunden 35 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 6.1: Wie Module und Views funktionieren](wie-module-und-views-arbeiten.md) | 45 Minuten | Adressen zerlegen, `module.php` lesen, eine View bis zu ihrer Klasse verfolgen |
| [Lektion 6.2: Eine eigene View](eine-eigene-view.md) | 55 Minuten | `hello/greet` mit Parametern, einem Template und einer Brotkrumennavigation schreiben |
| [Lektion 6.3: Richtlinien für Ihre Views](richtlinien-fuer-ihre-views.md) | 50 Minuten | `greet` als Richtlinienfunktion deklarieren, sie der Rolle Anonymous zuweisen |
| [Lektion 6.4: Formulare und Aktionen in einer View](formulare-und-aktionen.md) | 55 Minuten | Ein Formular absenden, validieren, weiterleiten, den Formular-Token in Aktion sehen |

## Dieses Modul vorbereiten

- Alle Teilnehmenden haben das Modul `hello` aus Lektion 5.4 und ein Administratorkonto auf ihrer eigenen
  Schulungsinstallation; Lektion 6.3 ändert die Rolle Anonymous, die nicht von mehreren Teilnehmenden gemeinsam
  genutzt werden darf.
- Sie sollten wissen, wie Ihre Schulungs-Website erkannt wird (über einen Pfad wie `/site/` oder über den Host),
  damit die Teilnehmenden die richtigen Adressen eingeben.
- Halten Sie fertige Dateien `module.php`, `greet.php` und `greet.tpl` für Teilnehmende bereit, die zurückfallen.
- Proben Sie Schritt 8 von Lektion 6.4 in dem Browser, den Sie am Projektor verwenden: Das Entfernen eines Feldes
  mit den Entwicklerwerkzeugen unterscheidet sich leicht zwischen den Browsern.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 6.1 | 45 |
| Lektion 6.2 | 55 |
| Pause | 10 |
| Lektion 6.3 | 50 |
| Lektion 6.4 | 55 |
| **Gesamt** | **215** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
