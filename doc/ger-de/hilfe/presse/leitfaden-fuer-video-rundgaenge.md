---
title: Leitfaden für Video-Rundgänge
audience: press
category: press-media
order: 154
summary: Wie Sie einen Video-Rundgang durch Exponential planen, skripten, aufnehmen, schneiden und veröffentlichen - eine Aufgabe pro Video, die genauen Namen der Schaltflächen, eine Vorlage für das Skript, Aufnahmeeinstellungen für gute Lesbarkeit, der Schnittdurchgang, der Geheimnisse findet, und wie das Video nach dem nächsten Release korrekt bleibt.
keywords: Video, Rundgang, Screencast, Tutorial-Video, Aufnahme, Skript, Schnitt, YouTube, Bildschirmaufnahme, Sprecherstimme, Creator, video, walkthrough, screencast, tutorial video, recording, script, editing, screen recording, voice-over, creator
type: help
---
# Leitfaden für Video-Rundgänge

Diese Seite ist für Creator, die einen Rundgang durch Exponential aufnehmen: ein Tutorial, einen Überblick, einen
„ersten Eindruck“. Sie ergänzt [Einen Screencast aufnehmen](../fuersprecher/einen-screencast-aufnehmen.md), wo die Grundlagen
stehen, um den Arbeitsablauf von Creatorn. Ein guter Rundgang zeigt eine Aufgabe gut, verwendet die echten Namen dessen,
was auf dem Bildschirm steht, und sagt, welche Version er zeigt.

## Inhalt

1. [Planen: ein Video, ein Versprechen](#planen-ein-video-ein-versprechen)
2. [Mit den echten Namen skripten](#mit-den-echten-namen-skripten)
3. [Für einen Telefonbildschirm aufnehmen](#für-einen-telefonbildschirm-aufnehmen)
4. [Schneiden: der Sicherheitsdurchgang und der Genauigkeitsdurchgang](#schneiden-der-sicherheitsdurchgang-und-der-genauigkeitsdurchgang)
5. [Veröffentlichen](#veröffentlichen)
6. [Korrekt halten](#korrekt-halten)

## Planen: ein Video, ein Versprechen

Wählen Sie eine Aufgabe, die die Hilfe schon erklärt, damit Zuschauer weiterlesen können, und ein passendes Skript aus
diesem Bereich:

| Video | Länge | Skript | Zu verlinkende Hilfeseite |
|---|---|---|---|
| Was Exponential ist, in fünf Minuten gezeigt | 5 Min. | [Überblick in fünf Minuten](demo-skript-ueberblick-in-fuenf-minuten.md) | [Erste Schritte](../erste-schritte.md) |
| Bearbeiten, Entwürfe, Vorschau und Versionen | 8 bis 10 Min. | [Rundgang für Redakteure](demo-skript-rundgang-fuer-redakteure.md) | [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| Eine Seite mit dem Seitenbaukasten bauen | 6 bis 8 Min. | [Exponential Layouts](demo-skript-exponential-layouts.md) | [Der Layout-Editor](../layout-editor.md) |
| Der Schreibassistent, der nur Entwürfe schreibt | 6 Min. | [Exponential Intelligence](demo-skript-exponential-intelligence.md) | [Exponential Intelligence](../../../eng-us/features/6.0/extensions/expai.md) |
| Eine Website unter Velocity betreiben | 8 Min. | [Velocity](demo-skript-velocity.md) | [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md) |
| Von einem leeren Ordner zu einer laufenden Website | 10 Min. | [Installation von null](demo-skript-installation-von-null.md) | [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md) |
| Was ein Administrator sieht | 10 Min. | [Rundgang für Administratoren](demo-skript-rundgang-fuer-administratoren.md) | [Sicherheitshärtung](../sicherheitshaertung.md) |

Schreiben Sie vor allem anderen das Versprechen in einem Satz auf: „Nach diesem Video können Sie <Aufgabe>.“

## Mit den echten Namen skripten

Verwenden Sie die Namen genau so, wie der Bildschirm sie zeigt, in Ihrem Skript und Transkript **fett**: **Zur
Veröffentlichung schicken**, **Entwurf speichern und beenden**, **Entwurf verwerfen**, **Versionen verwalten**,
**„Publish layout“**. Die Namen auf dieser Seite stammen aus [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
und [Der Layout-Editor](../layout-editor.md).

```text
WALKTHROUGH SCRIPT
Title:        <the task, as a viewer would search it>
Promise:      After this video you can <task>.
Version:      Exponential <6.0.14 | 6.0.15 development line, checked on <date>>, design admin4
Disclosure:   <line from the disclosure page>
Help page:    <link>

0:00  Disclosure and promise (10 seconds)
0:10  Where we start: <screen>
0:20  Step 1  Click <name>            Say: <one sentence, from the help>
...
      Result: show what changed, on the site if it shows there
      Outro: "The help page is linked below. Questions: the forums or Telegram."
```

## Für einen Telefonbildschirm aufnehmen

- Nehmen Sie ein Browserfenster auf, nicht den ganzen Bildschirm, mit einem Zoom, der auf einem Telefon lesbar ist.
- Verwenden Sie für Aufnahmen den hellen Farbmodus, sofern Sie den dunklen Modus nicht absichtlich zeigen (die
  Sonnen-Schaltfläche schaltet um).
- Bewegen Sie den Mauszeiger langsam; halten Sie bei jedem Klick eine Sekunde inne. Sagen Sie, was Sie drücken: „Ich
  drücke **Bearbeiten**“.
- Nehmen Sie die Stimme getrennt auf oder in einem ruhigen Raum; korrigieren Sie sie in der Nachbearbeitung.
- Legen Sie einen Ausdruck des Skripts neben sich; lesen Sie nie Schlüssel oder Passwörter vom Bildschirm ab.

## Schneiden: der Sicherheitsdurchgang und der Genauigkeitsdurchgang

Sehen Sie sich die ganze Aufnahme vor der Veröffentlichung zweimal an.

```text
SAFETY PASS (frame by frame where a sign-in, a summary or an error page appears)
[ ] No password, key, token, cookie, server address, settings file
[ ] No real person's name, e-mail, order or message
[ ] No other tabs, notifications, bookmarks or file names
[ ] The exp:install summary is cut or blurred (it prints the password)

ACCURACY PASS
[ ] Every button name matches the screen and the help
[ ] Every number and date has a source I can link
[ ] The version is said and shown
[ ] Nothing claims an endorsement, a program, a release date or a speed multiple
```

Die vollständige Liste dessen, was nicht zu sehen sein darf, steht unter [Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md).

## Veröffentlichen

- Titel, Vorschaubild und Beschreibung: [Titel, Vorschaubilder und Beschreibungen](titel-vorschaubilder-und-beschreibungen.md).
- Untertitel und ein Transkript: [Untertitel und barrierefreies Video](untertitel-und-barrierefreie-videos.md).
- Nennen Sie die Lizenz Ihres Videos. Wenn Sie Hilfeseiten vorlesen oder zeigen, stehen diese unter der GNU FDL 1.3;
  ein Zitat mit Link ist der einfache Weg ([Quellen angeben und Ihre Texte lizenzieren](../fuersprecher/quellen-zitieren-und-texte-lizenzieren.md)).
- Teilen Sie es dort, wo die Community hinschaut: in den Foren und auf Share ([Auf Share und Projects veröffentlichen](../fuersprecher/auf-share-und-projects-veroeffentlichen.md)).

## Korrekt halten

- Lesen Sie nach jedem Release den Changelog auf Änderungen an den Bildschirmen, die Sie gezeigt haben
  ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)).
- Wenn ein Schritt nicht mehr funktioniert, heften Sie eine Korrektur an und korrigieren Sie die Beschreibung; nehmen
  Sie neu auf, wenn es darauf ankommt ([Ein Video oder einen Beitrag korrigieren](ein-video-oder-einen-beitrag-korrigieren.md)).
- Wenn das Video veraltet ist, sagen Sie es im Titel oder in der Beschreibung.

## Verwandte Seiten

- [Einen Screencast aufnehmen](../fuersprecher/einen-screencast-aufnehmen.md)
- [Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md)
- [Untertitel und barrierefreies Video](untertitel-und-barrierefreie-videos.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
