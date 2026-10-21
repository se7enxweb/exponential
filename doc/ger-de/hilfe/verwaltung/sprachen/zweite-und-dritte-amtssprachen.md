---
title: Zweite und dritte Amtssprachen
audience: government, project-manager, admin, editor
category: public-sector
order: 120
summary: Eine Einrichtungsanleitung für öffentliche Stellen mit zwei oder drei Amtssprachen - ein Siteaccess und eine Adresse pro Sprache mit gleichem Rang, Grundsatz für den Ersatz, Rollen und Freigabe für Übersetzer, ein Sprachumschalter, Oberflächensprachen für Mitarbeitende und die Prüfungen vor dem Start.
keywords: Amtssprachen, zweisprachig, dreisprachig, zweite Sprache, Gleichrangigkeit, Sprachpolitik, Übersetzer, Siteaccess pro Sprache, Sprachumschalter, öffentlicher Dienst, official languages, bilingual, trilingual, second language, equal standing, language policy, translators, siteaccess per language, switcher, public service
type: help
---
# Zweite und dritte Amtssprachen

Diese Seite richtet sich an Führungskräfte und Administratoren öffentlicher Stellen, die die Öffentlichkeit in zwei
oder drei Amtssprachen bedienen müssen. Sie fasst die anderen Seiten dieses Bereichs zu einer Einrichtung zusammen.
Die zugrunde liegende Anleitung ist [Eine mehrsprachige Website bauen](../../../../eng-us/help/creators/website-creators/multilingual-sites.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Zuerst die Entscheidungen](#zuerst-die-entscheidungen)
2. [Die Einrichtung](#die-einrichtung)
3. [Menschen und Abläufe](#menschen-und-abläufe)
4. [Vor dem Start](#vor-dem-start)

## Zuerst die Entscheidungen

| Entscheidung | Möglichkeiten |
|---|---|
| Gleicher Rang in den Adressen | ein Präfix für jede Sprache (`/en/`, `/fr/`) oder ein Host pro Sprache; vermeiden Sie eine Sprache unter `/` und die anderen darunter |
| Was die Startseite tut | Weiterleitung nach Browsersprache oder eine neutrale Seite, die alle Sprachen anbietet |
| Ersatz | volle Gleichwertigkeit (Unübersetztes ausblenden) oder Ersatz durch eine andere Amtssprache mit Hinweis |
| Welche Inhalte in allen Sprachen vorliegen müssen | pro Bereich, schriftlich festgehalten |
| Hauptsprache neuer Inhalte | pro Team oder die Entwurfssprache |
| Oberflächensprache der Mitarbeitenden | die Wahl jeder einzelnen Person |

## Die Einrichtung

1. **Fügen Sie die Inhaltssprachen hinzu** unter **Setup > Sprachen** (siehe „Eine Inhaltssprache hinzufügen“).
2. **Ein Siteaccess pro Sprache** mit `Locale`, `SiteLanguageList[]` und `SiteName` (siehe „Ein Siteaccess pro
   Sprache“).
3. **Adressen** nach Pfad oder Host (siehe „Sprachadressen: Pfad oder Host“).
4. **Startseite** mit Weiterleitung oder neutraler Seite (siehe „Weiterleitung in die Sprache des Besuchers“).
5. **Sprachumschalter** auf jeder Seite (siehe „Der Sprachumschalter“).
6. **Feste Texte** des Designs in `.ts`-Dateien übersetzt; Menüs und Fußzeilen möglichst als Inhalte gepflegt.
7. **Immer verfügbar** für Medien und Benutzer (siehe „Immer verfügbare Inhalte“).
8. **Suche** pro Sprache (siehe „Mehrsprachige Suche“).

## Menschen und Abläufe

- Rollen für Übersetzer mit der Einschränkung **Sprache** (siehe „Rollen und Rechte für Übersetzer“).
- Ein Freigabeschritt pro Sprache, wenn Übersetzungen durchgesehen werden (siehe „Übersetzungsablauf und Freigabe“);
  das Ereignis *Freigeben* lässt sich auf **Betroffene Sprachen** beschränken.
- Ein Übersetzungsrückstand: Die Seite einer Sprache unter **Setup > Sprachen** listet die Objekte mit einer
  Übersetzung in dieser Sprache auf; vergleichen Sie die Anzahlen zwischen den Sprachen, um zu sehen, was fehlt.
- Stil und Terminologie: ein Glossar pro Sprache (siehe „Begriffsglossare“).

## Vor dem Start

- [ ] Jede Sprachadresse antwortet mit dem richtigen `lang` an `<html>`.
- [ ] Der Sprachumschalter führt zur selben Seite in der anderen Sprache.
- [ ] Seiten, die es in allen Sprachen geben muss, gibt es.
- [ ] Formulare, Fehlerseiten, Suchergebnisse und E-Mails gibt es in jeder Sprache.
- [ ] Die Datenschutzerklärung und die Erklärung zur Barrierefreiheit gibt es in jeder Sprache.

## Verwandte Seiten

- [Sprachen auf einen Blick](sprachen-auf-einen-blick.md)
- [Mehrsprachige Projekte und Multisite planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
