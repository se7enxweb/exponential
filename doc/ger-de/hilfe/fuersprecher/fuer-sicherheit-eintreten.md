---
title: Für Sicherheit eintreten
audience: advocate
category: advocacy
order: 111
summary: Wie Fürsprecher gute Sicherheitspraxis rund um Exponential fördern - vertrauliche Meldung, zügige Upgrades, Härtung und zweistufige Anmeldung, sichere Beispiele in Beiträgen und Tutorials und ruhige, sachliche Antworten - ohne Panikmache oder Versprechen.
keywords: Fürsprache für Sicherheit, Upgrades, Härtung, zweistufige Anmeldung, 2FA, verantwortungsvolle Offenlegung, sichere Beispiele, Geheimnisse, Content Security Policy, Prüfprotokoll, Community, security advocacy, hardening, two-step sign-in, responsible disclosure, safe examples, secrets, audit trail
type: help
---
# Für Sicherheit eintreten

Diese Seite ist für Fürsprecher, die möchten, dass mit Exponential gebaute Websites sicherer werden. Sie müssen kein
Sicherheitsexperte sein. Das meiste Gute, das Sie tun können, ist, einige Gewohnheiten zu verbreiten und sie in allem
vorzuleben, was Sie veröffentlichen.

## Inhalt

1. [Die fünf Gewohnheiten, die Sie verbreiten sollten](#die-fünf-gewohnheiten-die-sie-verbreiten-sollten)
2. [Leben Sie es in Ihrem eigenen Material vor](#leben-sie-es-in-ihrem-eigenen-material-vor)
3. [Über Sicherheit sprechen, ohne Angst zu machen](#über-sicherheit-sprechen-ohne-angst-zu-machen)
4. [Administratoren helfen](#administratoren-helfen)

## Die fünf Gewohnheiten, die Sie verbreiten sollten

| Gewohnheit | Warum | Quelle |
|---|---|---|
| Sicherheitslücken vertraulich melden | Damit Korrekturen vor dem Missbrauch kommen | [Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md) |
| Auf das neueste 6.0.x aktualisieren | Veröffentlichte Tags werden „durch ein Upgrade auf das neueste 6.0.x behoben“ | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Der Checkliste zur Härtung folgen | Die meisten Vorfälle entstehen durch die Art, wie eine Website betrieben wird | [Sicherheitshärtung](../sicherheitshaertung.md#checkliste) |
| Die zweistufige Anmeldung einschalten | Gestohlene Passwörter allein reichen dann nicht mehr | [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md) |
| Nie Geheimnisse teilen | Schlüssel und Passwörter gelangen über Screenshots und Issues nach außen | [Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md) |

## Leben Sie es in Ihrem eigenen Material vor

- Beispiele verwenden Platzhalter (`<your-password>`), nie echt wirkende Zugangsdaten.
- Screenshots zeigen keine Schlüssel, Einstellungsdateien oder Serveradressen.
- Tutorials, die etwas installieren, sagen: „Ändern Sie das erzeugte Passwort und löschen Sie
  `var/log/initial-admin-password`“ ([Das Standardpasswort des Administrators](../standard-administratorpasswort.md)).
- Tutorials, die den Server berühren, erwähnen die Anleitung zur Härtung.
- Empfehlen Sie nie, eine Sicherheitsfunktion abzuschalten, „damit es funktioniert“; suchen Sie den dokumentierten Weg.

## Über Sicherheit sprechen, ohne Angst zu machen

- Beschreiben Sie den Ablauf ([Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)), nicht die Gefahren.
- Verbreiten Sie keine Gerüchte über Sicherheitslücken; verweisen Sie auf die Release Notes.
- Wenn ein Release ein Sicherheitsproblem behebt, ermutigen Sie mit den eigenen Worten des Release zum Upgrade, mehr nicht.

## Administratoren helfen

| Sie fragen | Verweisen Sie auf |
|---|---|
| „Wo fange ich an?“ | [Sicherheitshärtung](../sicherheitshaertung.md) und [Kapitel 13 des Installationsbuchs](../../installation/13-sicherheitshaertung.md) |
| „Wie sehe ich Angriffe?“ | [Request-Logs](../../../eng-us/features/6.0/request-logs.md), [Das Prüfprotokoll](../../../eng-us/features/6.0/audit-trail.md) |
| „Wie setze ich eine CSP durch?“ | [Content Security Policy, Auf Durchsetzung umstellen](../../../eng-us/features/6.0/content-security-policy.md#switching-to-enforce-step-by-step) |
| „Kann ein Schutzschild davor sitzen?“ | [Ein Schutzschild davor: request-shield](../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield) |
| „Wie aktualisieren wir sicher?“ | [Upgrade](../../installation/11-aktualisieren.md) |

## Verwandte Seiten

- [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)
- [Einwand: „Ist Open Source sicher?“](einwand-sicherheit-von-open-source.md)
- [Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
