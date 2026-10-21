---
title: Mehrere Marken planen
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: Die geschäftlichen Entscheidungen, die Sie treffen sollten, bevor Sie mehrere Marken, Regionen oder Microsites in einer Exponential-Installation betreiben - gemeinsam oder getrennt, Teams, Designs, Upgrades und wann getrennte Installationen die bessere Wahl sind.
keywords: mehrere Marken, Multisite, Planung, Marken, Regionen, Microsites, eine Installation, getrennte Installationen, Abwägungen, Entscheidung, Unternehmen, multi-brand, multisite, planning, separate installations, trade-offs, business
type: help
---
# Mehrere Marken planen

Diese Seite richtet sich an Inhaber und Führungskräfte, die neben ihre Hauptwebsite eine zweite Marke, eine regionale
Website oder eine Reihe von Microsites für Kampagnen stellen wollen. Sie listet die Entscheidungen auf, die das Projekt
prägen, erklärt, was Exponential Ihnen bietet, wenn alle Websites eine Installation teilen, und nennt die Fälle, in
denen getrennte Installationen Ihnen besser dienen. Die technischen Einzelheiten stehen auf den Seiten, die am Ende
jedes Abschnitts verlinkt sind.

## Inhalt

1. [Was „eine Installation“ für ein Unternehmen bedeutet](#was-eine-installation-für-ein-unternehmen-bedeutet)
2. [Die fünf Fragen, die zuerst zu klären sind](#die-fünf-fragen-die-zuerst-zu-klären-sind)
3. [Übliche Formen](#übliche-formen)
4. [Eine Installation oder mehrere](#eine-installation-oder-mehrere)
5. [Teams und Berechtigungen](#teams-und-berechtigungen)
6. [Ein Planungsbogen zum Kopieren](#ein-planungsbogen-zum-kopieren)

## Was „eine Installation“ für ein Unternehmen bedeutet

Die Unterstützung mehrerer Websites wird im README des Projekts unter den wichtigsten Funktionen von Exponential
aufgeführt. Eine Installation ist ein Kernel, ein Satz Erweiterungen und (meist) eine Datenbank. Jede Ihrer Marken ist
ein oder mehrere **Siteaccesses** mit eigenem Host oder Pfad, eigener Sprache und eigenem Design
([Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#multisite-eine-installation-mehrere-sites)).

Was das in der Praxis bedeutet:

| Sie gewinnen | Sie nehmen in Kauf |
|---|---|
| Einen Ort, an dem Sie jede Marke aktualisieren und absichern | Jede Marke bewegt sich bei einem Upgrade mit den anderen |
| Gemeinsame Inhalte und Medien, einen Suchindex | Eine stark belastete Website bremst die anderen auf derselben Maschine |
| Eine Administration für alle Redakteure, wenn Sie das wollen | Eine Wiederherstellung aus dem Backup stellt alle Marken auf einmal wieder her |
| Eine Backup-Routine | Markenspezifischer Code muss ordentlich gehalten werden, sonst betrifft er alle |

Diese Abwägungen stammen aus [Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md#trade-offs).
Schreiben Sie sie in Ihren Projektauftrag, damit später niemand überrascht ist.

## Die fünf Fragen, die zuerst zu klären sind

- [ ] **Teilen die Marken Inhalte**, etwa einen Artikel, der auf zwei Websites erscheint, oder nur die Installation?
- [ ] **Hat jede Marke ihr eigenes Design**, oder teilen sich manche eines mit unterschiedlichen Farben und Logos?
- [ ] **Teilen die Marken Redakteure**, oder hat jede ein eigenes Team, das die Inhalte der anderen nicht sehen darf?
- [ ] **Teilen die Marken eine Datenbank?** Siteaccesses, die eine teilen, werden zusammen in
      `RelatedSiteAccessList[]` aufgeführt
      ([10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
- [ ] **Werden die Marken gemeinsam aktualisiert?** Eine Installation bedeutet eine Kernel-Version für jede Marke.

Wenn Sie die meisten dieser Fragen mit „nein“ beantworten, lesen Sie [Eine Installation oder mehrere](#eine-installation-oder-mehrere)
sorgfältig.

## Übliche Formen

| Form | Wie sie aufgebaut ist | Typisch für |
|---|---|---|
| Getrennte Inhalte pro Marke | Ein Ordner pro Marke unter der Inhaltswurzel; jeder Siteaccess nutzt seinen Ordner als Wurzel | Mehrere Marken eines Unternehmens |
| Gemeinsame Inhalte, unterschiedliche Designs | Dieselbe Wurzel, ein anderes Design pro Siteaccess | Eine Website für den Fachhandel und eine für Endkunden mit denselben Produkten |
| Eine Marke in mehreren Sprachen | Ein Siteaccess pro Sprache | Exporteure, internationale Verbände |
| Jede Marke in ihrer eigenen Erweiterung | Einstellungen, die nur gelten, während die Siteaccesses dieser Marke laufen | Agenturen und Organisationen mit vielen kleinen Websites |

Quellen: [Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md#shapes-of-a-multisite)
und [Einstellungen pro Website innerhalb von Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md).

> **Hinweis**
> Die Formen lassen sich kombinieren. Ein häufiges Muster sind zwei Marken, jede in zwei Sprachen: vier öffentliche
> Siteaccesses und eine Administration in derselben Installation.

## Eine Installation oder mehrere

Die Hosting-Dokumentation vergleicht die beiden Möglichkeiten
([Multisite in einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#eine-installation-oder-mehrere)):

| | Eine Installation, mehrere Siteaccesses | Eine Installation pro Website |
|---|---|---|
| Gemeinsame Inhalte und Benutzer | ja | nein |
| Ein Update für alle Websites | ja | eines pro Website |
| Isolation der Websites auf Betriebssystemebene | nein: ein Systembenutzer, eine Codebasis | ja |
| Die Last einer Website betrifft die anderen | ja | nein, mit getrennten Pools oder Instanzen |
| Backups und Wiederherstellungen | gemeinsam | pro Website |

Ihre Faustregel: mehrere Siteaccesses in einer Installation für **einen Kunden** mit mehreren Websites; getrennte
Installationen für **verschiedene Kunden**. Für ein Unternehmen bedeutet das: Ihre eigenen Marken gehören meist
zusammen; eine Marke, die Sie vielleicht verkaufen, ausgliedern oder einem anderen Eigentümer übergeben, ist in einer
eigenen Installation sicherer.

Wenn getrennte Installationen trotzdem Inhalte austauschen müssen, exportiert die Erweiterung `syndication` einen Teil
des Inhaltsbaums einer Installation als Feed, den eine andere importiert, und hält beide per Cron im Gleichschritt
([syndication](../../../../eng-us/features/6.0/extensions/syndication.md)).

## Teams und Berechtigungen

- Eine Administration kann jede Marke bedienen; Rollen, die auf einen Teilbaum beschränkt sind, halten jedes Team im
  Ordner seiner Marke
  ([Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md#editors-and-the-administration)).
- Mehrere Administrations-Siteaccesses sind möglich, wenn Teams die Websites der anderen überhaupt nicht sehen sollen.
- Ein Redakteur kann darauf beschränkt werden, sich nur in einer Administration anzumelden, indem `user/login` nach
  Siteaccess eingeschränkt wird
  ([Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#rollen-für-übersetzer-und-site-teams)).

Legen Sie fest, wem die Inhalte jeder Marke gehören, wer veröffentlichen darf und wer Änderungen freigibt, bevor die
Rollen angelegt werden.

## Ein Planungsbogen zum Kopieren

```text
Brand plan for <organization>                                  <date>

| Brand | Address (host or path) | Languages | Design | Content root | Editors | Owner |
|-------|------------------------|-----------|--------|--------------|---------|-------|
|       |                        |           |        |              |         |       |

Shared between brands: <content, media, users, products ...>
Kept apart: <...>
Upgraded together: yes / no (if no: separate installations)
Cross-brand links needed: yes / no
Open questions: <...>
```

Der Bereich für Führungskräfte hat einen längeren Bogen mit Sprachen und Einzelheiten zu den Siteaccesses
([Der Planungsbogen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md#der-planungsbogen)).

## Verwandte Seiten

- [Eine Installation, viele Websites](eine-installation-viele-websites.md)
- [Inhalte zwischen Marken teilen](inhalte-zwischen-marken-teilen.md)
- [Checkliste für mehrere Marken](checkliste-fuer-mehrere-marken.md)
- [Mehrsprachigkeit planen](../mehrsprachig/mehrsprachigkeit-planen.md)
- [Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
