---
title: "Thema: Multisite"
audience: press
category: press-media
order: 65
summary: Ein Themenbeitrag über den Betrieb mehrerer Websites aus einer Exponential-Installation - Siteaccesses, Einstellungen pro Website in Erweiterungen, Zuordnung nach Host und Sprache, Links zwischen Websites - mit druckbaren Fakten, Quellen, dem Vorbehalt zur Isolation und Ideen für Überschriften.
keywords: Thema, Multisite, mehrere Websites, Siteaccess, eine Installation, Zuordnung nach Host, Weiterleitung zur Sprache, Links zwischen Websites, Multisite-Hosting, Presse, story, multisite, several sites, one installation, host matching, language redirect, cross-site links, multi-site hosting, press
type: help
---
# Thema: Multisite

Diese Seite ist für Journalisten und Creator, die über Organisationen schreiben, die viele Websites betreiben.
Exponential liefert schon lange mehrere Websites aus einer Installation aus; die Linie 6.0.x hat es leichter gemacht,
sie zu hosten und zwischen ihnen zu verlinken. Diese Seite gibt das Thema, die Fakten mit Quellen und einen Vorbehalt,
den Sie drucken sollten.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Es Lesern erklären](#es-lesern-erklären)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„Multi-site support“ ist eine der Hauptfunktionen, die das README aufführt
([README, Main Exponential features](https://github.com/se7enxweb/exponential/blob/main/README.md#main-exponential-features)).
Eine Installation kann mehrere Websites ausliefern, „jede ein Siteaccess mit eigenem Host, Pfad, eigener Sprache und
eigenem Design“ ([Wer ist 7x?, Ein CMS, ein CMF und ein Shop in einem](../wer-ist-7x.md#cms-cmf-und-shop-in-einem)).
Seit Januar 2026 kann eine Website, die in ihrer eigenen Erweiterung lebt, Einstellungen mitbringen, die nur gelten,
während ihre Siteaccesses laufen; im Oktober 2026 ergänzte die Entwicklungslinie 6.0.15 die Zuordnung nach dem Anfang
eines Hostnamens, eine Weiterleitung zur Sprache des Besuchers und korrekte Links von den Seiten einer Website zu
Inhalten einer anderen.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| Einstellungen pro Website in Erweiterungen; ein einziges Leeren des INI-Caches für jedes Projekt | Januar 2026 | [Einstellungen pro Website in Erweiterungen](../../../eng-us/features/6.0/multi-site-ini-overrides.md) |
| Zuordnung nach dem Anfang eines Hosts und eine Weiterleitung zur Sprache | 6. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#6-october-2026-host-matching-by-the-start-of-a-host-and-a-redirect-to-the-language-for-addresses-without-one) |
| Links zu Inhalten einer anderen Website einer Multisite-Installation | 13. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#13-october-2026-links-to-content-of-another-site-of-a-multisite-installation) |
| Das Website-Menü der Administration: die anderen Websites, bei denen sich ein Benutzer anmelden darf | 15. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#15-october-2026-the-site-menu-of-the-administration) |

Die Änderungen vom Oktober gehören zur Linie 6.0.15, die nicht veröffentlicht ist.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Mehrere Websites laufen aus einer Installation, jede ein Siteaccess mit eigenem Host, Pfad, eigener Sprache und eigenem Design. | [Wer ist 7x?](../wer-ist-7x.md#cms-cmf-und-shop-in-einem) |
| Vor Januar 2026 bedeutete das Hosten von zehn Websites ein großes Einstellungsverzeichnis oder zehn Siteaccess-Verzeichnisse; jetzt bringt die Erweiterung jeder Website ihre eigenen mit. | [Einstellungen pro Website in Erweiterungen](../../../eng-us/features/6.0/multi-site-ini-overrides.md) |
| Eine Host-Zuordnung kann `www.example.com.test.local` dem Siteaccess von `www.example.com` zuordnen (`HostMatchMethod=start`). | [Siteaccess nach Host-Präfix](../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md#in-short) |
| Die Startseite kann einmalig zu der Sprache weiterleiten, die ein Browser anfordert, und behält dabei Adresse und Abfrage bei. | ebenda |
| Ohne die Erweiterung `ezurlmultisite` konnte ein Link zu Inhalten einer anderen Website in die falsche Website zeigen und mit 404 antworten; die Erweiterung behebt das. | [Links zu Inhalten einer anderen Website](../../../eng-us/features/6.0/multisite-cross-site-links.md#in-short) |
| Redakteure melden sich einmal an und sehen die anderen Websites, die sie nutzen dürfen, im Website-Menü der Kopfzeile. | [Das Design admin4, Was Sie bekommen](../../../eng-us/features/6.0/admin4-design.md#what-you-get) |

## Was Sie nicht behaupten sollten

- „Jede Website ist von den anderen isoliert.“ Für Velocity sagt das README, dass ein einzelner Serverprozess, der
  mehrere Websites ausliefert, „eine Bequemlichkeit beim Routing ist, keine Sicherheitsgrenze“; es empfiehlt eine
  Instanz pro Website, jede als eigener Benutzer des Betriebssystems, wenn Kunden die Daten der anderen nicht lesen
  dürfen ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)).
- „Unbegrenzt viele Websites.“ Die Quellen nennen kein Maximum und keine getestete Anzahl.
- Kundennamen von Multisite-Installationen: In den Quellen ist keiner mit Einverständnis veröffentlicht.

## Es Lesern erklären

```text
Ein „Siteaccess“ ist eine Tür in dieselbe Installation: eine Website, eine Sprachversion davon oder die
Administration. Jede Tür kann ihre eigene Adresse, ihr eigenes Design und ihre eigene Sprache haben, während die
Inhalte in einem gemeinsamen Baum liegen. So kann ein Team mehrere Websites ohne mehrere Installationen betreiben.
```

## Blickwinkel und Ideen für Überschriften

```text
„Zehn Websites, eine Installation: Multisite in Exponential“
„Die richtige Sprache an der Haustür: die Sprachweiterleitung von Exponential“
„Ein Inhaltsbaum, viele Websites - und Links, die auf der richtigen landen“
```

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Aufbau und Hosting von Multisite | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Erfahrungen von Website-Entwicklern | die Community | <https://share.exponential.earth/forums> |

## Verwandte Seiten

- [Multisite- und mehrsprachige Projekte planen](../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Thema: Sprachen und Schreibrichtung von rechts nach links](thema-sprachen-und-rechts-nach-links.md)
- [Thema: Freiheit beim Hosting](thema-freiheit-beim-hosting.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
