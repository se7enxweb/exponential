---
title: "Gesprächspunkte: Sicherheit"
audience: advocate
category: advocacy
order: 16
summary: Wie Sie ehrlich über die Sicherheit von Exponential sprechen, als Prozess mit Belegen statt als Versprechen, mit belegten Punkten zu Meldung von Sicherheitslücken, unterstützten Versionen, Content Security Policy, Prüfprotokoll, zweistufiger Anmeldung, Härtung und Abhängigkeiten.
keywords: Gesprächspunkte, Sicherheit, Sicherheitslücke, SECURITY.md, verantwortungsvolle Offenlegung, Content Security Policy, CSP, Prüfprotokoll, zweistufige Anmeldung, 2FA, Härtung, Request-Logs, Standardpasswort, Zeta Components, Lieferkette, talking points, security, vulnerability, responsible disclosure, audit trail, two-step sign-in, hardening, request logs, default password, supply chain
type: help
---
# Gesprächspunkte: Sicherheit

Diese Seite ist für Fürsprecher, die gefragt werden: „Ist es sicher?“ Kein ehrlicher Mensch kann diese Frage für
irgendeine Software mit „ja“ beantworten. Was Sie tun können: den **Prozess** und die **Belege** beschreiben und der
Person zeigen, wie sie sie prüft.

## Inhalt

1. [Die Regel: Prozess, keine Versprechen](#die-regel-prozess-keine-versprechen)
2. [Die Punkte](#die-punkte)
3. [Was Sie zeigen](#was-sie-zeigen)
4. [Sagen Sie nicht](#sagen-sie-nicht)
5. [Wenn Ihnen jemand eine Sicherheitslücke meldet](#wenn-ihnen-jemand-eine-sicherheitslücke-meldet)

## Die Regel: Prozess, keine Versprechen

| Statt | Sagen Sie |
|---|---|
| „Es ist sicher.“ | „Sicherheitslücken werden vertraulich gemeldet, innerhalb eines Werktags beantwortet und im nächsten Release behoben.“ |
| „Es wurde nie gehackt.“ | „Hier sind die Anleitung zur Härtung und die Checkliste, der Ihr Administrator folgen kann.“ |
| „Wir nehmen Sicherheit ernst.“ | „Das tut das Projekt, mit der Quelle zu jedem Punkt.“ |

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Sicherheitsprobleme werden vertraulich per E-Mail gemeldet, nicht öffentlich, und Sie bekommen innerhalb eines Werktags eine erste Antwort.“ | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| „6.0.x ist die gepflegte Linie. Korrekturen werden auf `main` gemacht und mit dem nächsten Release 6.0.x veröffentlicht.“ | [Sicherheitskorrekturen](../wer-ist-die-exponential-foundation.md#sicherheitskorrekturen) |
| „Eine veröffentlichte Version ändert sich nie. Eine Korrektur erreicht Sie als nächstes Release, sodass das, was Sie abgenommen haben, bleibt, was es war.“ | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| „Die Administration kann eine Content Security Policy senden; standardmäßig ist sie dort nur meldend, mit dokumentierten Schritten zur Durchsetzung.“ | [Content Security Policy](../../funktionen/6.0/content-security-policy.md) |
| „Neue Templates der Administration enthalten keine Inline-Styles oder -Skripte, sodass eine strenge Richtlinie durchgesetzt werden kann.“ | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md), [Die Administration gestalten](../../bc/6.0/admin4-gestaltung.md) |
| „Ein manipulationssicheres Prüfprotokoll hält fest, wer was getan hat; jeder Eintrag trägt den Hash des vorherigen.“ | [Das Prüfprotokoll](../../funktionen/6.0/pruefprotokoll.md) |
| „Zweistufige Anmeldung mit einer Authenticator-App oder einem Code per E-Mail.“ | [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md) |
| „Neue Installationen erzeugen ein zufälliges Administratorpasswort, wenn keines oder ein allgemein bekanntes angegeben wird.“ | [Das Standardpasswort des Administrators](../standard-administratorpasswort.md) |
| „Sondierungen und Angriffe werden in ein Sicherheitslog eingeordnet, mit maskierten Adressen.“ | [Request-Logs](../../funktionen/6.0/anfrageprotokolle.md) |
| „Administratoren bekommen eine Anleitung zur Härtung, Bedrohung für Bedrohung, und eine Checkliste.“ | [Sicherheitshärtung](../sicherheitshaertung.md) |
| „Das Projekt gibt Sicherheitskorrekturen auch an die Upstream-Projekte: 14 Pull Requests an die Zeta Components im April 2026, wie das Profil von se7enxweb berichtet.“ | [Der PHP-Stack, auf dem es steht](../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt) |

## Was Sie zeigen

Auf einer Installation, die Sie kontrollieren, als Administrator:

1. Der Reiter **Audit**: „Ihre Anmeldung ist bereits erfasst.“
2. `/user2fa/setup`: die Einrichtung der zweistufigen Anmeldung.
3. Die [Checkliste zur Härtung](../sicherheitshaertung.md#checkliste) auf dem Bildschirm.
4. [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) im Browser.

Zeigen Sie nie Einstellungsdateien, Schlüssel, Passwörter oder Serveradressen auf einem geteilten Bildschirm
([Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md)).

## Sagen Sie nicht

- „Es ist sicher“, „nicht zu hacken“, „Sicherheit wie bei Banken“, „Sicherheit auf Militärniveau“.
- „Es hatte nie eine Sicherheitslücke.“ Keine Quelle sagt das, und jedes ernsthafte Projekt hatte welche.
- „Die CSP schützt Sie.“ Sie ist in der Administration standardmäßig nur meldend und auf den Websites aus
  ([Content Security Policy](../../funktionen/6.0/content-security-policy.md)); der Administrator entscheidet.
- Irgendetwas über eine bestimmte Sicherheitslücke, die nicht öffentlich ist.

## Wenn Ihnen jemand eine Sicherheitslücke meldet

Menschen erzählen es manchmal zuerst einem Fürsprecher. Dann:

1. **Danken Sie** und bitten Sie, Details **nicht** öffentlich zu veröffentlichen.
2. **Verweisen Sie** auf die vertrauliche Adresse in `SECURITY.md` oder die vertrauliche Meldung von Sicherheitslücken
   des Repositorys bei GitHub ([Wenn etwas schiefgeht, Sicherheitsprobleme](../wenn-etwas-schiefgeht.md#sicherheitsprobleme)).
3. Leiten Sie die Details **nicht** in einem öffentlichen Kanal weiter, und versuchen Sie **nicht**, sie auf der Website
   eines anderen nachzustellen.
4. Versprechen Sie **keinen** Termin für eine Korrektur.

Die vollständige Routine steht unter [Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md).

## Verwandte Seiten

- [Für Sicherheit eintreten](fuer-sicherheit-eintreten.md)
- [Einwand: „Ist Open Source sicher?“](einwand-sicherheit-von-open-source.md)
- [Mit Einwänden umgehen, Sicherheit](../geschaeft/einwandbehandlung.md#sicherheit)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
