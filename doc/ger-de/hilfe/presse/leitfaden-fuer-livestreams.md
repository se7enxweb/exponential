---
title: Leitfaden für Livestreams
audience: press
category: press-media
order: 162
summary: Wie Sie Exponential live streamen, ohne Geheimnisse oder personenbezogene Daten preiszugeben - ein Ablaufplan für einen Stream von 30 oder 60 Minuten, die Verzögerung und die Einrichtung der Szenen, der Umgang mit Fragen im Chat und Bitten wie „schau dir meine Website an“, was Sie in der Sekunde tun, in der etwas durchsickert, und wie Sie die Aufzeichnung aufbewahren.
keywords: Livestream, Streaming, Twitch, YouTube Live, Ablaufplan, Szenen, Stream-Verzögerung, Chat, Moderation, Lecks, Aufzeichnung, VOD, Creator, live stream, streaming, run sheet, scenes, stream delay, chat, moderation, leaks, recording, creator
type: help
---
# Leitfaden für Livestreams

Diese Seite ist für Creator, die Exponential live zeigen: ein Mitmach-Aufbau, ein erster Blick, eine Fragerunde. Ein
Livestream lässt sich nicht bearbeiten, bevor ihn jemand sieht; alles, was ein aufgezeichnetes Video sicher macht, muss
also vorher erledigt sein. Lesen Sie zuerst [Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md); diese Seite
ergänzt, was speziell für das Streaming gilt.

## Inhalt

1. [Den Stream planen](#den-stream-planen)
2. [Szenen und ein Sicherheitsnetz einrichten](#szenen-und-ein-sicherheitsnetz-einrichten)
3. [Ein Ablaufplan zum Kopieren](#ein-ablaufplan-zum-kopieren)
4. [Chat, Fragen und Bitten](#chat-fragen-und-bitten)
5. [Wenn live etwas durchsickert](#wenn-live-etwas-durchsickert)
6. [Nach dem Stream](#nach-dem-stream)

## Den Stream planen

| Entscheidung | Empfehlung |
|---|---|
| Thema | Ein Thema pro Stream: Bearbeiten, Layouts, Exponential Intelligence, Velocity, eine Installation |
| Installation | Ihre eigene Demo-Installation mit erfundenen Inhalten ([Eine Demo-Installation einrichten](eine-demo-installation-einrichten.md)) |
| Version | Nennen Sie sie im Titel und zu Beginn: ein Release oder die Entwicklungslinie 6.0.15 mit Datum |
| Skript | Eines der Demo-Skripte in diesem Bereich, mit Zeit für Fragen |
| Offenlegung | Im Titel oder im Infobereich und zu Beginn gesprochen ([Offenlegung und Ethik beim Sponsoring](offenlegung-und-ethik-beim-sponsoring.md)) |
| Co-Moderation | Eine zweite Person, die die Ausgabe und den Chat beobachtet, hilft, Lecks zu erkennen |

## Szenen und ein Sicherheitsnetz einrichten

```text
SCENES
[ ] "Starting soon" and "Be right back" scenes with no screen capture at all
[ ] A "Demo" scene that captures one browser window (a separate profile), never the whole desktop
[ ] A "Terminal" scene that captures one terminal window with a large font and a plain prompt
[ ] A hotkey that switches to "Be right back" instantly - practice it

SAFETY NET
[ ] A stream delay, if your platform offers one, so you can cut before viewers see a mistake
[ ] Notifications off on the machine and the phone near the microphone
[ ] Password manager locked; no sign-in needed during the stream (sign in before going live)
[ ] Provider keys for Exponential Intelligence: a separate key with a low limit, revoked after the stream
[ ] The exp:install summary and settings files are never shown (they contain passwords)
```

Die vollständige Liste dessen, was nie auf dem Bildschirm sein darf, steht unter
[Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md).

## Ein Ablaufplan zum Kopieren

```text
RUN SHEET - <title> - <date>                        Version: <Exponential version>   Design: admin4
00:00  Starting soon scene; check audio
00:03  Hello, disclosure, what we will build today, the version
00:06  Part 1: <demo script part>                    Help page in chat: <link>
00:18  Questions from chat (5 minutes)
00:23  Part 2: <demo script part>                    Help page in chat: <link>
00:40  Questions from chat
00:50  Recap: what we did, where to read more, where to ask (forums, Telegram)
00:55  Clean up on stream or after: discard drafts, revoke keys
```

Fügen Sie die Links zur Hilfe bei jedem Teil in den Chat ein; die Zuschauer können mitlesen, während Sie sprechen.

## Chat, Fragen und Bitten

- **Antworten Sie mit einer Quelle.** Wenn die Hilfe die Frage beantwortet, fügen Sie den Link ein. Wenn nicht: „Die
  Dokumentation sagt dazu nichts. Ich prüfe es und poste die Quelle.“ Schreiben Sie es auf
  ([Mit Kommentaren und Fragen umgehen](mit-kommentaren-und-fragen-umgehen.md)).
- **„Schau dir meine Website an, sie ist kaputt.“** Lehnen Sie im Stream freundlich ab. Eine echte Website auf dem
  Bildschirm kann die Daten und Einstellungen ihres Eigentümers zeigen. Verweisen Sie auf die Foren oder die Seite
  **Fehler melden** der Hilfe.
- **Ein Zuschauer postet ein Passwort, einen Schlüssel oder personenbezogene Daten im Chat.** Löschen Sie die
  Nachricht, sagen Sie ihm, er solle es ändern, und lesen Sie es nie laut vor.
- **Ein Zuschauer meldet ein Sicherheitsproblem.** Beenden Sie das Thema: „Bitte schicken Sie das vertraulich an die
  Adresse in `SECURITY.md`.“ Besprechen Sie keine Einzelheiten live.
- **Themenfremde Angriffe auf andere Projekte oder Menschen.** Moderieren Sie; halten Sie den Stream respektvoll
  ([Andere Projekte und Unternehmen respektieren](../fuersprecher/andere-projekte-respektieren.md)).

## Wenn live etwas durchsickert

| Was durchgesickert ist | Jetzt tun |
|---|---|
| Ein Passwort | Auf „Be right back“ umschalten; sofort ändern; andere Sitzungen abmelden |
| Ein API- oder Anbieterschlüssel | Szene wechseln; den Schlüssel beim Anbieter widerrufen; außerhalb des Streams einen neuen erstellen |
| Eine Serveradresse oder ein Pfad | Notieren; nach dem Stream prüfen, was dadurch offenliegt |
| Personenbezogene Daten | Szene wechseln; sie aus der Aufzeichnung entfernen, bevor diese online bleibt |

Sagen Sie den Zuschauern danach offen, was passiert ist und dass es behoben ist. Gehen Sie nie davon aus, dass es
niemand bemerkt hat: Streams werden aufgezeichnet und zu Clips geschnitten.

## Nach dem Stream

1. Sehen Sie sich die Aufzeichnung an, bevor Sie sie öffentlich lassen; schneiden Sie heraus, was durchgesickert ist,
   oder stellen Sie sie auf privat.
2. Ergänzen Sie Kapitel, Untertitel und die Links, die Sie im Chat gepostet haben
   ([Untertitel und barrierefreie Videos](untertitel-und-barrierefreie-videos.md)).
3. Posten Sie die versprochenen Antworten, mit Quellen.
4. Setzen Sie die Demo-Installation zurück: Entwürfe verwerfen, Schlüssel widerrufen, die Oberfläche wieder auf
   Englisch umstellen.

## Verwandte Seiten

- [Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md)
- [Ein Online-Meetup durchführen](../fuersprecher/ein-online-treffen-durchfuehren.md)
- [Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
