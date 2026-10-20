---
title: Open-Source-Werte in der täglichen Partnerarbeit
audience: partner
category: partnership
order: 250
summary: Was die Werte Open, Sharing und Innovative des Programms in der täglichen Arbeit eines Partners bedeuten - in Projekten, Verträgen, Code, Dokumentation, Support und Vertrieb - mit konkreten Gewohnheiten und Beispielen aus dem Projekt selbst.
keywords: Open Source, Werte, offen, teilen, innovativ, GPL, Beitrag, Upstream, Zusammenarbeit, Gewohnheiten, Community, open source, values, open, sharing, innovative, contribution, upstream, collaboration, habits, community
type: help
---
# Open-Source-Werte in der täglichen Partnerarbeit

Diese Seite ist für Partnerteams, die die Werte des Programms in alltägliche Gewohnheiten übersetzen möchten.
„Exponential-Partner müssen sich an Open-Source-Werten und einem Bekenntnis zur Qualität ausrichten“ (<https://exponential.earth/partner/requirements>, gelesen am
18. Oktober 2026).

## Die drei Werte

| Wert | Wie veröffentlicht (<https://exponential.earth/partner/program>) |
|---|---|
| **Open** | „Wir heißen Ideen und Wissen anderer willkommen und gehen offen und mit positiver Haltung in den Austausch mit der Welt. Unser Open-Source-Modell der Softwareverbreitung und unsere von Benutzern getragene Community sind Beispiele dieser offenen Haltung.“ |
| **Sharing** | „Wir teilen unsere Informationen, Ideen und unser Wissen und arbeiten als Team zusammen, intern ebenso wie mit Kunden, Partnern und der Exponential-Community.“ |
| **Innovative** | „Wir bauen kreative Lösungen, die echte Probleme lösen und einen einfacheren und wirksameren Austausch von Wissen und Informationen ermöglichen.“ |

## Gewohnheiten, die die Werte zeigen

| Wo | Open | Sharing | Innovative |
|---|---|---|---|
| Projekte | Auf öffentlichen Erweiterungen aufbauen, bevor Sie neue schreiben | Allgemeine Korrekturen an die Upstream-Projekte geben, statt privat zu patchen | Das Problem des Kunden lösen und es dann für alle verallgemeinern |
| Verträge | Kunden sagen, dass die Software unter der GPL steht und ihnen ihre Website gehört | Vorab vereinbaren, welche allgemeine Arbeit veröffentlicht werden darf | Wartung anbieten, die Websites aktuell hält |
| Code | In Git mit vollständiger Historie arbeiten ([Mit Git entwickeln](../mit-git-entwickeln.md)) | Pull Requests senden ([Code zurückgeben](code-zurueckgeben.md)) | Neue Funktionen mit einem funktionierenden Prototyp vorschlagen |
| Dokumentation | Die Hilfe lesen und verlinken | Tutorials unter einer freien Lizenz schreiben | Dokumentieren, was Sie entdeckt haben, auch wenn es klein ist |
| Support | In öffentlichen Kanälen antworten | Wiederholte Antworten in Dokumentation verwandeln | Automatisieren, was Sie zweimal beantworten |
| Vertrieb | Fakten mit Quellen nennen | Arbeit, die Sie nicht gut können, an einen anderen Partner empfehlen | Zeigen, was in jedem Release neu ist |

## Beispiele aus dem Projekt selbst

- **Jeder Pull Request wird übernommen.** „Jeder Pull Request wird übernommen, und seine Commits bleiben so erhalten, wie der Beitragende sie geschrieben hat, und werden nie umgeschrieben“ (`doc/eng-us/bc/6.0/ci.md`). Beitragende werden nicht abgewiesen; was korrigiert
  werden muss, wird obendrauf korrigiert.
- **Nennung fremder Arbeit.** Die LoveStack-Verbesserungen von Mugo Web wurden „unter voller Nennung von Mugo Web“ neu gebaut
  (README).
- **Beiträge an die Upstream-Projekte.** Die Profil-README von se7enxweb meldet „14 Sicherheits-PRs an das Upstream-Projekt zetacomponents eingereicht“ (zitiert in [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt)).
- **Freie Dokumentation.** Die Hilfe steht unter der GFDL 1.3 oder später, sodass jeder sie kopieren und verbessern kann
  ([Freie Dokumentation](../freie-dokumentation.md)).

## Was Sie mit Kunden vereinbaren sollten

Viele Kunden befürchten, dass „offen“ bedeutet, dass ihre Geschäftsgeheimnisse veröffentlicht werden. Das bedeutet es
nicht. Vereinbaren Sie schriftlich:

- Inhalte, Daten und Markenauftritt des Kunden bleiben privat;
- allgemeine, wiederverwendbare Verbesserungen (Fehlerkorrekturen, allgemeine Funktionen) dürfen an die
  Upstream-Projekte gegeben werden;
- der Kunde wird genannt oder nicht, wie er möchte.

## Verwandte Seiten

- [Verhaltenskodex für Partner](verhaltenskodex.md)
- [Lizenzierung für Partner: GPL-Code, GFDL-Dokumentation](lizenzierung-fuer-partner.md)
- [Erweiterungen an die Upstream-Projekte zurückgeben](erweiterungen-zurueckgeben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
