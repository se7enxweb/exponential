---
title: Ein Release ankündigen
audience: developer, designer, app-creator
category: extension-development
order: 120
summary: Sagen Sie den Menschen, die Ihre Erweiterung verwenden, dass es ein Release gibt - das GitHub-Release selbst, share.exponential.earth, die Projektseite auf projects.exponential.earth, ezpedia - was Sie schreiben und was Sie weglassen.
keywords: ankündigen, Ankündigung, Release, Community, Benutzer, Sicherheits-Release, announce, release, share.exponential.earth, projects.exponential.earth, ezpedia, GitHub release, community, users, security release
type: help
---
# Ein Release ankündigen

Teil 7, Kapitel 12. Ein Release, von dem niemand erfährt, hilft niemandem. Die meisten Benutzer einer Erweiterung
erfahren von Updates über `composer outdated` oder eine Benachrichtigung. Manchen muss man es sagen, und neue Benutzer
müssen die Erweiterung finden. Dieses Kapitel behandelt, wo Sie ankündigen und was Sie sagen.

## Wo

| Ort | Erreicht | Was Sie veröffentlichen |
|---|---|---|
| Das GitHub-Release | alle, die das Repository beobachten | die Release Notes ([GitHub-Releases und Release Notes](github-releases-und-release-notes.md)) |
| [share.exponential.earth](https://share.exponential.earth) | die Community: ihre Foren haben eines für Erweiterungen | einen kurzen Beitrag zu Releases, auf die es ankommt: eine neue Erweiterung, eine Major-Version, eine Sicherheitskorrektur |
| [projects.exponential.earth](https://projects.exponential.earth) | Menschen, die das Verzeichnis der Community-Projekte durchsehen | die Projektseite der Erweiterung aktuell halten (Version, Links); siehe [projects.exponential.earth](../teil-1-vom-benutzer-zum-entwickler/projects-exponential-earth.md) |
| [ezpedia.exponential.earth](https://ezpedia.exponential.earth) | Leser der Wissensdatenbank | einen Artikel, wenn das Release eine Technik mitbringt, die eine Erklärung wert ist |

Nicht jedes Patch-Release braucht einen Beitrag. Eine neue Erweiterung, eine neue Major-Version und jedes
Sicherheits-Release schon.

## Was Sie schreiben

Ein Beitrag sind die Release Notes für Menschen, die die Erweiterung noch nicht kennen:

1. **Ein Satz dazu, was die Erweiterung tut.**
2. **Was neu ist**, in zwei bis fünf Zeilen, so wie Benutzer es sehen.
3. **Wer aktualisieren sollte und wie dringend.** „Alle auf PHP 8.5“ oder „Nur wenn Sie den Newsletter-Import
   verwenden“.
4. **Wie man es bekommt**: die Zeile `composer require` und der Link zum Release.
5. **Dank** an die Beitragenden, mit ihren öffentlichen Namen oder Konten, wenn sie genannt werden möchten.

## Sicherheits-Releases

Für ein Release, das eine Schwachstelle behebt:

- Veröffentlichen Sie zuerst die Korrektur, dann kündigen Sie an. Beschreiben Sie das Problem nie, bevor Benutzer
  aktualisieren können.
- Sagen Sie klar, dass es ein Sicherheits-Release ist, welche Versionen betroffen sind und was Benutzer tun müssen.
- Beschreiben Sie das Risiko allgemein; veröffentlichen Sie keinen Exploit.
- Nennen Sie die Person, die es gemeldet hat, wenn sie einverstanden ist
  ([Der Weg zum Maintainer](../teil-5-erweitern-forken-zurueckgeben/der-weg-zum-maintainer.md)).

## Was Sie nicht veröffentlichen

- Vertrauliches: Kunden, interne Hostnamen, unveröffentlichte Pläne anderer.
- Versprechen zu Terminen, über die Sie nicht bestimmen.
- Denselben Beitrag in allen Kanälen gleichzeitig: ein Hauptbeitrag, anderswo Links darauf.

## Verwandte Seiten

- Zurück: [Vor einem Release testen](vor-einem-release-testen.md)
- Weiter: [Der Wartungslebenszyklus](der-wartungslebenszyklus.md)
- [Die Community durch Code wachsen lassen](../teil-5-erweitern-forken-zurueckgeben/die-community-durch-code-wachsen-lassen.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
