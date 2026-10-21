---
title: Wenn Sie eine Sicherheitslücke finden
audience: press
category: press-media
order: 111
summary: Was ein Journalist, Rezensent oder Creator tut, wenn er ein Sicherheitsproblem in Exponential entdeckt oder davon erfährt - es vertraulich an security@se7enx.com melden, es aus dem Beitrag heraushalten, was in die Meldung gehört und wie Sie darüber berichten, sobald es behoben ist.
keywords: Sicherheitslücke, Sicherheit, Meldung, verantwortungsvolle Offenlegung, vertraulich, security@se7enx.com, SECURITY.md, Hinweis, Quelle, Rezension, Demo, Sperrfrist, vulnerability, security, report, responsible disclosure, private, tip, source, review, embargo
type: help
---
# Wenn Sie eine Sicherheitslücke finden

Diese Seite ist für Reporter, Rezensenten und Creator, die beim Testen von Exponential auf ein Sicherheitsproblem stoßen
oder von einer Quelle einen Hinweis darauf bekommen. Die Regel ist einfach und kennt keine Ausnahmen: Eine nicht
behobene Sicherheitslücke wird vertraulich gemeldet, und sie kommt nicht in einen Beitrag, ein Video oder einen Post,
bevor sie behoben ist.

## Inhalt

1. [Die Regel](#die-regel)
2. [Schritt für Schritt](#schritt-für-schritt)
3. [Was in die Meldung gehört](#was-in-die-meldung-gehört)
4. [Eine Nachricht, die Sie anpassen können](#eine-nachricht-die-sie-anpassen-können)
5. [Wenn eine Quelle Ihnen einen Hinweis gibt](#wenn-eine-quelle-ihnen-einen-hinweis-gibt)
6. [Nach der Korrektur darüber berichten](#nach-der-korrektur-darüber-berichten)

## Die Regel

> „Bitte melden Sie Sicherheitsprobleme vertraulich per E-Mail an security@se7enx.com, nicht im öffentlichen
> Issue-Tracker oder in den Diskussionen. Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen.“
> ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md))

Dieselbe Regel gilt für Fürsprecher und Partner
([Sicherheitsmeldungen bleiben vertraulich](../fuersprecher/sicherheitsmeldungen-bleiben-vertraulich.md)). Sie gilt auch für Sie: Ein
Beitrag, ein Video, ein Livestream oder ein Social-Media-Post ist öffentlich, und wer ein nicht behobenes Problem
veröffentlicht, gefährdet jede Website, auf der die Software läuft.

## Schritt für Schritt

1. **Hören Sie mit dem Testen auf**, sobald Sie zeigen können, dass das Problem besteht. Probieren Sie es nicht auf
   Websites aus, die Ihnen nicht gehören.
2. **Notieren Sie** die Version, die Engine und die Datenbank sowie die Schritte zum Nachstellen (siehe nächsten
   Abschnitt).
3. **Schicken Sie es vertraulich** an security@se7enx.com. Eröffnen Sie dazu kein Issue, keinen Forumsthread und keine
   Chatnachricht.
4. **Halten Sie es aus Entwürfen heraus, die andere sehen können** (geteilte Dokumente, Redaktionssysteme mit weitem
   Zugriff). Behandeln Sie es wie jedes andere vertrauliche Quellenmaterial.
5. **Warten Sie auf die Antwort.** SECURITY.md verspricht eine erste Antwort innerhalb eines Werktags und dass Ihnen
   „mitgeteilt wird, ob die Meldung angenommen oder abgelehnt wird, und Sie auf dem Laufenden gehalten werden, während
   eine Korrektur vorbereitet wird“.
6. **Schneiden Sie alle Aufnahmen**, die das Problem zeigen, aus einem Video oder Stream heraus, bevor er erscheint.

## Was in die Meldung gehört

Aus [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

| Angeben | Wie Sie es herausfinden |
|---|---|
| Die Version | `composer show se7enxweb/exponential` oder der Tag bzw. Commit, den Sie verwenden |
| Die PHP-Version | `php -v` |
| Die Engine, die die Website ausliefert | Exponential Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM |
| Die Datenbank | Wie in Ihrer Testinstallation eingerichtet |
| Was ein Angreifer tun kann, und die Schritte, um es nachzustellen | Ihre Notizen |

„Lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg“ (SECURITY.md). Wenn Ihre Testinstallation echte
Daten enthält, hängen Sie sie nicht an.

## Eine Nachricht, die Sie anpassen können

```text
To: security@se7enx.com
Subject: Security report: <one-line description>

I am a <journalist / reviewer / creator> at <outlet>. While testing Exponential for <a story / a review>
I found what looks like a security problem. I have not published it and will not until it is fixed.

Version: <composer show output, tag or commit>
PHP: <version>    Engine: <Velocity / FrankenPHP / Apache or nginx with PHP-FPM>    Database: <name>

What an attacker can do: <one or two sentences>
Steps to reproduce:
1. ...
2. ...

I tested only on my own installation. Please tell me whether you accept the report and when a fix is
published, so I can decide whether and when to write about it.
```

## Wenn eine Quelle Ihnen einen Hinweis gibt

- Fragen Sie die Quelle, ob sie es an security@se7enx.com gemeldet hat. Wenn nicht, ermutigen Sie sie dazu oder bitten
  Sie um Erlaubnis, es weiterzugeben.
- Überprüfen Sie einen Hinweis nicht, indem Sie die Live-Website eines anderen angreifen.
- Ein Hinweis auf ein nicht behobenes Problem ist für sich allein nicht veröffentlichungsfähig. Sie können später
  berichten, dass eine Korrektur erfolgt ist, mit dem Changelog als Quelle.

## Nach der Korrektur darüber berichten

Sobald ein Release die Korrektur enthält, können Sie darüber wie über jede andere Sicherheitskorrektur schreiben
([Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md)): Sagen Sie, was das Changelog sagt, welche
Version es behebt und was die Leser tun sollten. Fragen Sie, bevor Sie sich selbst in den Kanälen des Projekts als
Melder nennen, und fragen Sie den Melder, bevor Sie ihn in Ihrem Beitrag nennen. Die Dokumente des Projekts beschreiben
kein Programm für Danksagungen oder Prämien (Bug Bounty); deuten Sie nicht an, dass es eines gibt
([Behauptungen, die Sie nicht aufstellen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#veranstaltungen-und-programme)).

## Verwandte Seiten

- [Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md)
- [Sperrfristen und Zeitpunkt](sperrfristen-und-zeitpunkt.md)
- [Welcher Tracker für welche Meldung](../fuersprecher/welcher-tracker-fuer-welche-meldung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
