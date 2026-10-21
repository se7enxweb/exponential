---
title: "Lektion 6.1: Von eZ Platform oder Ibexa: zwei Ziele und wie man wählt"
audience: trainer, admin, developer
category: training
order: 61
summary: Die Teilnehmenden erkennen die Generation einer Quelle auf eZ Platform oder Ibexa, erklären die beiden Ziele (Weg A zu Exponential Platform Nexus und den Platform-Distributionen, Weg B hinunter zum Legacy-Kernel von Exponential 6 oder zu Platform Legacy) und wählen einen Weg mit der Entscheidungstabelle aus Kapitel 16.2. 45 Minuten.
keywords: migration zu exponential, generation, weg a, weg b, entscheidungstabelle, zuerst dieselbe generation, bestandsaufnahme, migrating to exponential, eZ Platform, Ibexa, Symfony, path A, path B, Exponential Platform Nexus, Exponential Platform Legacy, DXP, decision table, same generation first, inventory
type: help
---
# Lektion 6.1: Von eZ Platform oder Ibexa: zwei Ziele und wie man wählt

Websites auf eZ Platform oder Ibexa laufen auf den Symfony-basierten Plattformen, die der Hersteller nach dem
Legacy-Kernel gebaut hat. Sie haben in der Exponential-Familie zwei Ziele: die Exponential-Platform-Distributionen
derselben Generation (Weg A) oder den Legacy-Kernel von Exponential 6, allein oder als Platform Legacy (Weg B). Diese
Lektion vermittelt, wie man die Generation der Quelle erkennt und wie man wählt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 6: Von den Symfony-basierten Plattformen](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler, Administratoren und technische Projektleiter |
| Voraussetzungen | [Lektion 1.1](../modul-01-eine-migration-planen/einen-weg-waehlen.md); eine allgemeine Vorstellung von Symfony |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die fünf Quellgenerationen aus Kapitel 16.1 nennen und wie man jede erkennt (Kernel-Paket, Konfigurationsschlüssel,
  Namespace);
- die drei Stellen erklären, an denen sich das Datenbankschema entlang dieser Linie geändert hat;
- Weg A und Weg B beschreiben und sagen, was jeder behält und verliert;
- mit der Tabelle aus Kapitel 16.2 und der Regel „zuerst dieselbe Generation“ einen Weg für eine beschriebene Website
  wählen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 16.1](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele),
[16.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen) und
[16.4](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen).

1. **Die Quellen** (5 Minuten). Zeigen Sie die Tabelle „Wovon Sie migrieren“: eZ Platform 1.x und 2.x (Schlüssel
   `ezpublish:`, Namespace `eZ\Publish\API`), 3.0 bis 3.3 (Schlüssel `ezplatform:`), Ibexa 4.x und 5.0 (Schlüssel
   `ibexa:`, Namespace `Ibexa\Contracts`). Das Schema hat sich dreimal geändert: 3.0 entfernte etwa 80
   Legacy-Tabellen, 4.0 fügte `ibexa_*`-Tabellen hinzu, 5.0 benannte jede Kerntabelle in `ibexa_*` um.
2. **Weg A** (5 Minuten). Nexus und die Platform-Distributionen, eine Linie pro Generation. Drei Fakten: Die Forks
   ersetzen die Upstream-Paketnamen, die PHP-Namespaces bleiben die von Upstream, Nexus baut auf der
   Open-Source-Edition auf (kein Page Builder, kein Form Builder, kein kommerzieller Workflow).
3. **Weg B** (4 Minuten). Exponential 6, der Legacy-Kernel, oder Platform Legacy, der Hybrid, der beide Kernel auf
   einer Datenbank betreibt. „Hinunter“ zu gehen bedeutet, zu einer Datenbank zu wechseln, die der 6.0-Kernel lesen
   kann.
4. **Wählen** (6 Minuten). Gehen Sie die Tabelle aus 16.2 Zeile für Zeile durch. Die Faustregeln: zuerst dieselbe
   Generation; Weg B ist ein Neuschreiben der Darstellung und des eigenen Codes, nicht der Inhalte; mischen Sie die
   Wege nicht in einer Umstellung.

### Häufige Fragen

**Welcher Weg hat für eine Live-Website das geringste Risiko?** Weg A innerhalb derselben Generation: ein
Paketwechsel (16.2).

**Wir verwenden den kommerziellen Page Builder. Was passiert?** In Nexus gibt es keinen Page Builder; Landing Pages
werden auf Weg A mit Netgen Layouts neu aufgebaut, auf Weg B mit Exponential Layouts (16.2, 16.5.9).

**Müssen wir Fallen bei Composer-Versionen kennen?** Ja, für Weg A: Nennen Sie für Nexus immer eine Version, weil das
Repository die Upstream-Tags behält (16.1). Lektion 6.2 behandelt das.

### Stolperfallen

- **Generation und Produkt auf einmal wechseln.** Wechseln Sie zuerst zur passenden Exponential-Platform-Linie, lassen
  Sie sie sich einspielen, und entscheiden Sie dann über den nächsten Schritt.
- **Weg B wählen, weil das Team Symfony nicht mag.** Weg B ist der aufwendigste: Darstellung und eigener Code werden
  neu geschrieben. Treffen Sie die Entscheidung mit der Tabelle, nicht nach Geschmack.
- **Versionsnummern aus dem Gedächtnis.** Lesen Sie die Tabellen aus Kapitel 16.1 am Kurstag.

## Praktische Übung

Eine Entscheidungsübung zu zweit, mit geöffnetem Kapitel 16.

1. Öffnen Sie [Kapitel 16.1](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele).
2. Schreiben Sie für jede der folgenden Quellen ihre Generation, ihr Kernel-Paket und ihren wichtigsten
   Konfigurationsschlüssel auf:
   - ein Projekt, dessen `composer.json` `ezsystems/ezpublish-kernel` 7.5 verlangt;
   - ein Projekt, dessen Konfiguration mit `ibexa:` beginnt und dessen Kernel `ibexa/core` 4.6 ist;
   - ein Projekt mit `ezsystems/ezplatform-kernel` 1.x.
3. Suchen Sie für jedes in den Tabellen von 16.1 die passende Nexus-Linie und die passende Platform-Legacy-Linie.
4. Öffnen Sie [Kapitel 16.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen).
   Schreiben Sie für eine Ibexa-4.6-Website mit acht eigenen Bundles, RichText-Inhalten, einem Team, das Twig kennt,
   und Redakteuren, die an die Symfony-Administration gewöhnt sind, pro Zeile der Tabelle Weg A oder Weg B auf, und
   ein Fazit.
5. Tun Sie dasselbe für eine 2.5-Website mit zwei kleinen Bundles, ohne RichText, mit einem Team, das INI und TPL
   kennt, und der Anforderung, unter PHP 8.5 zu laufen.
6. Schreiben Sie die Abfragen der Bestandsaufnahme aus [Kapitel 16.4](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#164-bevor-sie-beginnen-bestandsaufnahme-einfrieren-und-sicherungen)
   auf, die Sie auf jeder Quelle zuerst ausführen würden, und warum.

## Erwartetes Ergebnis

- Schritt 2: eZ Platform 2.5 (`ezpublish:`); Ibexa 4.6 (`ibexa:`); eZ Platform 3.x oder Ibexa 3.3 (`ezplatform:`).
- Schritt 3: Nexus `1.0.0.x` oder `v2.5.0.x` mit Platform Legacy `v2.5.0.x`; Nexus `1.2.0.x` mit Platform Legacy
  `v4.6.23.x`; Nexus `1.1.0.x` mit Platform Legacy `v3.3.44.x`.
- Schritt 4: Weg A (Nexus `1.2.0.x`, dieselbe Generation) für die 4.6-Website.
- Schritt 5: Weg B ist für die 2.5-Website eine vernünftige Wahl; das Paar nennt die Arbeit, die das bedeutet
  (Templates und Bundles neu geschrieben).
- Schritt 6: Versionszeilen von `ezsite_data`, verwendete Feldtypen, Sortierfelder 6 und 7, Hash-Typen, Sprachen,
  Größen.
- Der Trainer prüft die Schritte 3 und 4 mit zwei Paaren.

## Quiz

1. Welche Generation hat jede Kerntabelle in `ibexa_*` umbenannt?
   A. eZ Platform 2.5 B. eZ Platform 3.0 C. Ibexa 4.0 D. Ibexa 5.0
2. Was bedeutet „die Forks ersetzen die Upstream-Namen“ für Pakete von Drittanbietern?
3. Wovon ist Weg B ein Neuschreiben, und wovon nicht?
4. Nennen Sie die Faustregel für eine Ibexa-4.6-Website, die irgendwann Nexus 5 erreichen möchte.
5. Richtig oder falsch: Nexus enthält den kommerziellen Page Builder.

<details>
<summary>Antworten</summary>

1. **D** ([Kapitel 16.1](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele)).
2. Jeder Fork deklariert das Upstream-Paket unter `replace`, sodass Pakete, die Namen wie `ibexa/*` oder
   `ezsystems/*` verlangen, weiter aufgelöst werden (Kapitel 16.1).
3. Von der Darstellung und dem eigenen Code, nicht von den Inhalten; die Inhaltstabellen werden gemeinsam genutzt
   ([Kapitel 16.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen)).
4. Zuerst dieselbe Generation: Nexus `1.2.0.x` (oder das 4.6-Skeleton), danach die Generation wechseln (Kapitel 16.2).
5. **Falsch.** Nexus baut Seiten mit Netgen Layouts; der Page Builder hat keine Entsprechung (Kapitel 16.1, 16.5.9).

</details>

## Weiterführende Informationen

- [Kapitel 16: Migration von eZ Platform und Ibexa](../../../../installation/16-migration-von-ez-platform-und-ibexa.md):
  beide Wege vollständig.
- [Exponential Platform Nexus](../../../../installation/migration/exponential-platform-nexus.md) und
  [Exponential Platform Legacy](../../../../installation/migration/exponential-platform-legacy.md): die Anleitungen zur
  Familie.
- [Paketübersicht der Plattform](../../../../spezifikationen/6.0/platform-paketzuordnung.md): die vollständige Paketliste.

## Verwandte Seiten

- [Modul 6: Von den Symfony-basierten Plattformen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
