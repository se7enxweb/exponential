---
title: "Lektion 1.1: Die Exponential-Familie und die Wahl eines Wegs"
audience: trainer, admin, developer, project-manager
category: training
order: 11
summary: Die Teilnehmenden benennen die Produkte der Exponential-Familie, unterscheiden den Legacy-Kernel von den Symfony-basierten Plattformen und wählen mit der Entscheidungstabelle des Migrations-Hubs die richtige Anleitung für eine gegebene Website. 45 Minuten.
keywords: migration zu exponential, migrationsweg, exponential-familie, entscheidungstabelle, migrations-hub, migrating to exponential, migration path, exponential family, platform legacy, nexus, dxp, exponential basic, decision table, migration hub
type: help
---
# Lektion 1.1: Die Exponential-Familie und die Wahl eines Wegs

Jede Migration beginnt mit zwei Fragen: Woher kommt die Website, und wohin soll sie? Diese Lektion gibt den
Teilnehmenden die Landkarte der Exponential-Familie und die Entscheidungstabelle des Migrations-Hubs, damit der Rest
des Kurses immer von der richtigen Anleitung ausgeht.

> **Hinweis**
> Produktversionen entwickeln sich weiter. Der Migrations-Hub nennt das Datum, an dem seine Produkttabelle erhoben
> wurde. Öffnen Sie ihn vor dem Kurs und prüfen Sie, ob die Versionen, die Sie zeigen werden, noch die dort
> aufgeführten sind.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 1: Eine Migration planen](README.md) |
| Dauer | 45 Minuten (25 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und Projektleiter, die eine Migration planen |
| Voraussetzungen | Keine in diesem Kurs; Grundwissen darüber, was ein CMS ist |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Produkte der Exponential-Familie benennen und sagen, welche davon den Legacy-Kernel von Exponential 6
  enthalten;
- den Unterschied zwischen dem Legacy-Kernel (INI, TPL) und den Symfony-basierten Plattformen (YAML, Twig) erklären;
- mit der Entscheidungstabelle des Migrations-Hubs die Anleitung für eine beschriebene Website finden;
- die Regel „eine Änderung nach der anderen“ auf einen Migrationsplan anwenden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Migration zu und von Exponential](../../../../installation/migration/README.md), Abschnitte 1 und 2.

1. **Die Familie** (8 Minuten). Zeigen Sie die Produkttabelle des Hubs (Abschnitt 1.1). Lesen Sie nicht jede Zeile
   vor. Weisen Sie auf die drei Gruppen hin: den Kernel von Exponential 6 selbst (und seinen Starter), die reinen
   Symfony-Plattformen (Exponential Platform, DXP) sowie die Hybride und Distributionen, die beides enthalten
   (Platform Legacy, Nexus in seinen älteren Linien). Exponential Basic ist eine andere Codebasis (die Fortführung
   des klassischen 2.x-CMS), und Exponential Velocity ist ein Anwendungsserver, kein Inhaltsprodukt.
2. **Was jedes Produkt bietet** (5 Minuten). Zeigen Sie die Tabelle in Abschnitt 1.3: Inhaltsmodell, Templates,
   Seitenaufbau, REST, Datenbank-Engines. Die Botschaft: Der Legacy-Kernel und die Symfony-Plattformen teilen das
   Inhaltsmodell (Klassen oder Inhaltstypen, Objekte, Orte), unterscheiden sich aber bei Templates, Konfiguration und
   eigenem Code.
3. **Die Geschichte in einem Satz** (2 Minuten). Exponential 6 ist die Fortführung des eZ Publish Legacy-Kernels,
   eines Produkts, das sein Hersteller nach fast 18 Jahren eingestellt hat. Der alte Name erscheint in den
   Migrationskapiteln nur, um die Versionen zu bezeichnen, von denen die Teilnehmenden migrieren.
4. **Die Entscheidungstabelle** (6 Minuten). Gehen Sie Abschnitt 2 des Hubs durch: Beantworten Sie die Fragen der
   Reihe nach; das erste „Ja“ ergibt die Anleitung.
5. **Die Faustregeln** (4 Minuten). Erst die Inhalte, dann die Darstellung; eine Änderung nach der anderen; jede alte
   Adresse funktionsfähig halten; proben.

### Häufige Fragen

**Ist Exponential Platform dasselbe wie Exponential?** Nein. „Exponential Platform“ ohne Zusatz ist die reine
Symfony-Distribution; „Exponential 6“ ist der Legacy-Kernel, zu dem diese Hilfe gehört. Der Hub enthält genau für
diese Verwechslung eine Tabelle „Die Namen, die Sie vielleicht gehört haben, und was sie bedeuten“.

**Welches Produkt sollte ein neues Projekt wählen?** Das ist keine Migrationsfrage. Verweisen Sie für Migrationen auf
die Entscheidungstabelle des Hubs und für neue Websites auf die Einführung des Installationshandbuchs.

**Können wir Produkt, PHP-Version und Datenbank-Engine in einem Schritt wechseln?** Der Hub rät davon ab: Wechseln Sie
zuerst zum nächstgelegenen Ziel, lassen Sie es sich einspielen, und ändern Sie dann das Nächste.

**Ist „Nexus 5“ eine Versionsnummer?** Nein. Die Zahlen 2.5, 4.x oder 5.x hinter Nexus sind Plattformgenerationen
innerhalb von Nexus, keine Nexus-Versionen. Der Hub erklärt die Versionslinien in Abschnitt 1.2.

### Stolperfallen

- **Aus dem Gedächtnis unterrichten.** Versionsnummern und Versionslinien ändern sich. Zeigen Sie immer den Hub, nie
  eine Folie mit Zahlen, die Sie vor Monaten eingetippt haben.
- **Sich in der Produkttabelle verlieren.** Die Teilnehmenden brauchen die drei Gruppen, nicht jeden Paketnamen.
  Heben Sie die Details für Modul 6 auf.
- **Ein Werkzeug versprechen, das es nicht gibt.** Wenn jemand nach einem Ein-Klick-Konverter von einem anderen
  System fragt, steht die ehrliche Antwort im Hub: Jede Anleitung nennt die echten Werkzeuge und sagt, wo ein eigenes
  Skript nötig ist.

## Praktische Übung

Dies ist eine Papierübung, bei der der Migrations-Hub in einem Browser oder im Hilfecenter der
Schulungsinstallation geöffnet ist. Arbeiten Sie zu zweit.

1. Öffnen Sie [Migration zu und von Exponential](../../../../installation/migration/README.md).
2. Lesen Sie Abschnitt 2, **Which path fits?**
3. Notieren Sie für jede der folgenden Websites die Anleitung, die die Entscheidungstabelle ergibt, und die erste
   Frage, die mit „Ja“ beantwortet wurde:
   - eine Website auf einer 4.6-Version des alten Legacy-Kernels, unter PHP 5;
   - ein WordPress-Blog mit 800 Beiträgen;
   - eine Ibexa-4.6-Website mit eigenen Symfony-Bundles, deren Team seine Twig-Templates behalten möchte;
   - eine handgemachte HTML-Website mit 40 Seiten;
   - eine Exponential-6-Website, deren Betreiber den Symfony-Stack möchte, aber die Legacy-Administration noch ein
     Jahr behalten muss.
4. Öffnen Sie für die Ibexa-4.6-Website [Kapitel 16.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#162-einen-weg-wählen)
   und notieren Sie, welche Nexus-Linie dieselbe Generation hat.
5. Wählen Sie eine der fünf Websites und schreiben Sie einen Plan in drei Schritten, der „eine Änderung nach der
   anderen“ befolgt.
6. Vergleichen Sie Ihre Antworten mit einem anderen Paar.

## Erwartetes Ergebnis

- Jedes Paar hat pro Website eine Anleitung: Kapitel 14 (oder „Ältere Versionen dieses Kernels“), die
  WordPress-Anleitung, Kapitel 16 und dann die Familienanleitung des Ziels, die Anleitung für statisches HTML und die
  Anleitung zu Platform Legacy.
- Für die Ibexa-4.6-Website hat das Paar die Nexus-Linie `1.2.0.x` genannt, die 4.6-Generation.
- Der Plan in drei Schritten ändert pro Schritt eine Sache (zum Beispiel: zuerst dieselbe Generation, dann die
  PHP-Version, dann das Design).
- Der Trainer prüft das, indem er zwei Paare bittet, ihre Antworten vorzulesen, und sie mit dem Hub vergleicht.

## Quiz

1. Welches dieser Produkte hat eine andere Codebasis und ein anderes Datenmodell als Exponential 6?
   A. Exponential Platform Legacy B. Exponential Basic C. Exponential Starter D. CJW Exponential Platform Nexus
2. Was haben der Legacy-Kernel und die Symfony-basierten Plattformen gemeinsam?
3. Richtig oder falsch: Exponential Velocity ist ein Inhaltsprodukt, zu dem Sie eine Website migrieren können.
4. Die Entscheidungstabelle: Was tun Sie mit den Fragen, und wann hören Sie auf?
5. Nennen Sie zwei der vier Faustregeln, die für jeden Migrationsweg gelten.

<details>
<summary>Antworten</summary>

1. **B.** Exponential Basic führt das klassische 2.x-CMS mit eigenen Modulen fort
   ([Migrations-Hub, Abschnitt 1.1](../../../../installation/migration/README.md#11-die-produkte)).
2. Das Inhaltsmodell: Klassen oder Inhaltstypen, Objekte, Orte, in denselben Tabellen
   ([Migrations-Hub, Abschnitt 1.3](../../../../installation/migration/README.md#13-was-jedes-produkt-mitbringt-aus-sicht-einer-migration)).
3. **Falsch.** Es ist der für Exponential 6 empfohlene PHP-Anwendungsserver, kein Inhaltsprodukt (Hub, Abschnitt 1.1).
4. Beantworten Sie sie der Reihe nach; das erste „Ja“ ergibt die Anleitung
   ([Migrations-Hub, Abschnitt 2](../../../../installation/migration/README.md#2-welcher-weg-passt)).
5. Zwei beliebige aus: erst die Inhalte, dann die Darstellung; eine Änderung nach der anderen; jede alte Adresse
   funktionsfähig halten; proben (Hub, Abschnitt 2).

</details>

## Weiterführende Informationen

- [Migration zu und von Exponential](../../../../installation/migration/README.md): der Hub mit der Produkttabelle und der
  Entscheidungshilfe.
- [Kapitel 16.1: Die beiden Ziele](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele):
  die Versionslinien von Nexus und Platform Legacy im Detail.
- [Ältere Versionen dieses Kernels](../../../../installation/migration/aeltere-versionen.md): welches Kapitel zu welcher alten
  Version passt.

## Verwandte Seiten

- [Modul 1: Eine Migration planen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
