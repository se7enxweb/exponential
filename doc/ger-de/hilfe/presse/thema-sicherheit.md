---
title: "Thema: Sicherheit"
audience: press
category: press-media
order: 59
summary: Ein Themenbeitrag darüber, wie Exponential mit Sicherheit umgeht - vertrauliche Meldungen mit einer ersten Antwort innerhalb eines Werktags, Korrekturen im nächsten Release, veröffentlichte Versionen, die sich nie ändern, und die Schutzmaßnahmen der Linie 6.0.15 - erzählt als Prozess und Beleg, mit Quellen und den Aussagen, die Sie nicht machen dürfen.
keywords: Thema, Sicherheit, SECURITY.md, Melden von Sicherheitslücken, security@se7enx.com, Härtung, Content Security Policy, Audit-Trail, zweistufige Anmeldung, Passwörter, Zeta Components, Presse, story, security, vulnerability reporting, hardening, audit trail, two-step sign-in, passwords, press
type: help
---
# Thema: Sicherheit

Diese Seite ist für Journalisten, die über die Sicherheit von Open-Source-Software berichten. Keine ehrliche Quelle sagt,
eine Software „ist sicher“; was die Quellen von Exponential beschreiben, ist ein **Prozess** und eine Reihe von
**Schutzmaßnahmen**. Diese Seite liefert beides, mit Daten und Quellen, und sagt Ihnen, wie Sie mit einer
Sicherheitslücke umgehen, die Ihnen jemand zeigt.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Wenn Ihnen jemand eine Sicherheitslücke zeigt](#wenn-ihnen-jemand-eine-sicherheitslücke-zeigt)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen können](#wen-sie-fragen-können)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Die Sicherheitsrichtlinie von Exponential passt auf eine Seite. „Please report security issues privately by e-mail to
security@se7enx.com, not in the public issue tracker or the discussions. You can expect a first answer within one
business day.“ Korrekturen entstehen auf `main` und werden „published with the next 6.0.x release“, und „A published
tag is never changed“ ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Rund um diesen Prozess ergänzt die Entwicklungslinie 6.0.15
Schutzmaßnahmen, die ein Administrator sehen kann: eine Content Security Policy, einen manipulationssicheren
Audit-Trail, die zweistufige Anmeldung, das Ablehnen allgemein bekannter Passwörter und eine Administration ohne
Inline-Scripts ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)).

## Was neu ist, und seit wann

Alles in der Linie 6.0.15, die in Entwicklung und nicht veröffentlicht ist.

| Was | Wann | Quelle |
|---|---|---|
| Sicherheitsvorgaben: Header auf jeder Seite, das Sitzungscookie, einheitliche Antworten bei der Anmeldung | 16. bis 30. September 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#16-to-30-september-2026) |
| Der Audit-Trail, standardmäßig eingeschaltet | 2. Oktober 2026 | [Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md) |
| Die zweistufige Anmeldung im Changelog | 6. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#6-october-2026-two-step-sign-in) |
| Härtung der Administration | 14. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#14-october-2026-administration-hardening) |
| SVG-Bilder werden beim Hochladen bereinigt | 15. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#15-october-2026-svg-images-sanitized-on-upload) |
| Eine Content Security Policy, in der Administration im Report-only-Modus | 16. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-a-content-security-policy-report-only-on-the-administration) |
| Allgemein bekannte Passwörter werden abgelehnt; Links für vergessene Passwörter laufen ab | 18. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#18-october-2026-well-known-passwords-refused-everywhere-and-forgot-password-links-expire) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Meldungen gehen vertraulich an security@se7enx.com; erste Antwort innerhalb eines Werktags. | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| 6.0.x ist die gepflegte Linie; 5.x, 4.x, 3.x und ältere werden nicht unterstützt. | ebenda |
| Eine Korrektur ändert nie eine veröffentlichte Version; sie kommt als nächstes Release. | ebenda |
| Eine neue Installation erzeugt ein zufälliges Administratorpasswort, wenn keines oder ein allgemein bekanntes angegeben wird. | [Das Standardpasswort des Administrators](../standard-administratorpasswort.md) |
| Ein Link für ein vergessenes Passwort gilt eine Stunde (`ForgotPasswordKeyLifetime`). | [Changelog 6.0.15, Upgrade](../../../changelogs/6.0/6.0.15.md#upgrading) |
| Sondierungen und Angriffe werden in ein Sicherheitslog eingeordnet, mit maskierten Adressen. | [Gesprächspunkte: Sicherheit](../fuersprecher/gespraechspunkte-sicherheit.md#die-punkte) |
| Das Profil von se7enxweb nennt 14 Sicherheits-Pull-Requests an Zeta Components im April 2026. | [Der PHP-Stack, auf dem es steht](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |
| Administratoren bekommen eine Anleitung zur Härtung, Bedrohung für Bedrohung, mit Checkliste. | [Sicherheitshärtung](../sicherheitshaertung.md) |

## Was Sie nicht behaupten sollten

- „Exponential ist sicher“, „unhackbar“, „Sicherheit auf Bankniveau“. Beschreiben Sie den Prozess und die
  Schutzmaßnahmen.
- „Es hatte nie eine Sicherheitslücke.“ Keine Quelle sagt das.
- „Konform mit“ irgendeinem Gesetz oder Standard. Konformität hängt davon ab, wie eine Website gebaut und betrieben wird.
- „Der Support antwortet innerhalb von N Stunden.“ Die einzige dokumentierte Zeit ist die erste Antwort auf eine
  Sicherheitsmeldung.
- Einzelheiten einer Sicherheitslücke, die noch nicht zusammen mit ihrer Korrektur veröffentlicht wurde.

## Wenn Ihnen jemand eine Sicherheitslücke zeigt

Veröffentlichen Sie keine Einzelheiten einer nicht behobenen Sicherheitslücke. Bitten Sie die Person, sie vertraulich an
security@se7enx.com zu melden, wie es [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) verlangt, und berichten Sie erst, wenn die Korrektur
veröffentlicht ist. Testen Sie sie nicht an einer Website, die nicht Ihnen gehört. Das Vorgehen für Fürsprecher gilt
auch für Reporter: [Sicherheitsmeldungen bleiben vertraulich](../fuersprecher/sicherheitsmeldungen-bleiben-vertraulich.md).

## Blickwinkel und Ideen für Überschriften

```text
"One business day: how an old open source CMS answers security reports"
"Fixed in the next release, never in the old one: Exponential's rule for security fixes"
"Process, not promises: inside Exponential's security work"
```

## Wen Sie fragen können

| Frage | Fragen Sie | Wo |
|---|---|---|
| Der Sicherheitsprozess, zum Zitieren | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Eine Sicherheitslücke | vertraulich, nie öffentlich | security@se7enx.com |

## Verwandte Seiten

- [Gesprächspunkte: Sicherheit](../fuersprecher/gespraechspunkte-sicherheit.md)
- [Thema: die Content Security Policy](thema-content-security-policy.md)
- [Thema: der Audit-Trail](thema-audit-trail.md)
- [Thema: zweistufige Anmeldung](thema-zweistufige-anmeldung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
