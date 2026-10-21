---
title: "Sicherheitsmeldungen bearbeiten"
audience: designer, developer
category: theme-lifecycle
order: 190
summary: Was ab dem Moment zu tun ist, in dem eine Sicherheitsmeldung eintrifft - bestätigen, vertraulich nachstellen, bewerten, beheben, veröffentlichen, nennen - und was zu tun ist, wenn die Meldung in einem öffentlichen Issue ankommt.
keywords: Sicherheitsmeldung, Sicherheitslücke, Offenlegung, bestätigen, nachstellen, bewerten, Nennung, öffentliches Issue, vertraulich, security report, vulnerability, disclosure, acknowledge, reproduce, assess, credit, public issue, private
type: help
---
# Sicherheitsmeldungen bearbeiten

Diese Seite ist für Designer, die eine Sicherheitsmeldung zu ihrem Theme erhalten, über die Adresse in `SECURITY.md`
([Eine Sicherheitsrichtlinie für ein Theme](../veroeffentlichung/sicherheitsrichtlinie-fuer-ein-theme.md)) oder auf anderem Weg.

## Schritt für Schritt

1. **Bestätigen** Sie innerhalb der Zeit, die Ihre Richtlinie verspricht: Danken Sie dem Meldenden, sagen Sie, dass Sie
   der Sache nachgehen.
2. **Stellen Sie es nach** auf Ihrer Testinstallation, nie auf einer Live-Website. Schreiben Sie die Schritte auf.
3. **Bewerten** Sie: Was kann ein Angreifer tun, wer kann es tun (anonymer Besucher oder Redakteur), welche Versionen sind
   betroffen?
4. **Beheben** Sie es vertraulich: auf einem lokalen Branch, der vor dem Release nicht in ein öffentliches Repository
   gepusht wird.
5. **Sagen Sie dem Meldenden**, was Sie gefunden haben und wann die Korrektur veröffentlicht wird; fragen Sie, wie er
   genannt werden möchte.
6. **Veröffentlichen** Sie ([Ein Sicherheitsrelease veröffentlichen](ein-sicherheitsrelease-veroeffentlichen.md)).
7. **Nennen** Sie den Meldenden in den Release Notes, wie er es wünscht.

## Wenn die Meldung ein öffentliches Issue ist

1. Danken Sie dem Meldenden und bitten Sie ihn, über die vertrauliche Adresse weiterzumachen.
2. Verbergen oder bearbeiten Sie die Einzelheiten, wenn Ihre Plattform es erlaubt, ohne die Existenz der Meldung zu löschen.
3. Beheben und veröffentlichen Sie schnell; das Problem ist schon öffentlich.

## Niemals

- Bitten Sie den Meldenden nie um Zugangsdaten oder Kundendaten; veröffentlichen Sie sie nie selbst.
- Verschieben Sie nie einen veröffentlichten Tag, um eine Version zu „korrigieren“ ([Ein gepushter Tag ist endgültig](../veroeffentlichung/ein-gepushter-tag-ist-endgueltig.md)).

## Verwandte Seiten

- [Sicherheitskorrekturen in Themes](sicherheitskorrekturen-in-themes.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
