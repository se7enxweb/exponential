---
title: "Ein Sicherheitsrelease veröffentlichen"
audience: designer, developer
category: theme-lifecycle
order: 200
summary: Eine Sicherheitskorrektur für ein Theme veröffentlichen - zuerst beheben, Version, Tag und Release, dann das Problem klar mit den betroffenen Versionen und der nötigen Handlung der Nutzer beschreiben und die Kanäle informieren, auf denen Ihre Nutzer sind.
keywords: Sicherheitsrelease, Korrektur-Release, Sicherheitshinweis, betroffene Versionen, Upgrade-Hinweis, Changelog Fixed, Offenlegung, security release, fix release, advisory, affected versions, upgrade notice, disclosure
type: help
---
# Ein Sicherheitsrelease veröffentlichen

Diese Seite ist für Designer, die eine Sicherheitskorrektur fertig haben ([Sicherheitsmeldungen bearbeiten](sicherheitsmeldungen-bearbeiten.md)).

## Schritt für Schritt

1. **Version**: die nächste Nummer in Ihrer gepflegten Linie ([Eine Versionsnummer wählen](../veroeffentlichung/eine-versionsnummer-waehlen.md)).
   Nehmen Sie wenn möglich nur die Korrektur auf, damit Nutzer ohne Risiko aktualisieren können.
2. **Erhöhen, committen, taggen, pushen** ([Die Version vor dem Tag erhöhen](../veroeffentlichung/version-vor-dem-tag-erhoehen.md)).
3. **Release Notes**, die klar sagen:
   - was falsch war, in Worten, die ein Website-Betreiber versteht („Markup in einer Adresse oder einem Titel wurde im
     Karten-Popup ausgeführt“);
   - welche Versionen betroffen sind;
   - was Nutzer tun müssen (aktualisieren; ein Upgrade-Skript ausführen; Caches leeren);
   - wer es gemeldet hat, so wie die Person genannt werden möchte.
4. **Changelog-Eintrag** mit dem Präfix `Fixed:`.
5. **Informieren Sie Ihre Nutzer**: das Release, die Projektneuigkeiten auf projects.exponential.earth, Ihren
   Ankündigungs-Thread und zahlende Kunden direkt.
6. **Optional** veröffentlichen Sie für das Repository einen GitHub Security Advisory, damit Werkzeuge, die Advisories
   lesen, Nutzer warnen.

## Was Sie nicht veröffentlichen

- Einen einsatzbereiten Exploit.
- Einzelheiten, bevor die Korrektur verfügbar ist, außer das Problem ist schon öffentlich.

## Beispiel

`cjw_themes_jumper` 0.1.4 (17. Oktober 2026) wurde mit dem Titel „The map popup escapes the address and title“
veröffentlicht und mit einem Changelog-Eintrag, der die Ursache, die Korrektur, die Nennung (ein Pull-Request eines
Mitwirkenden) und einen verwandten Schritt erklärt, den Nutzer einer anderen Erweiterung gehen sollten.

## Verwandte Seiten

- [Wenn ein Release schiefgeht](../veroeffentlichung/wenn-ein-release-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
