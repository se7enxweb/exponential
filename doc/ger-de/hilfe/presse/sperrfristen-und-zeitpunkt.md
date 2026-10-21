---
title: Sperrfristen und Zeitpunkt
audience: press
category: press-media
order: 139
summary: Den Zeitpunkt von Berichten über Exponential planen, wenn keine Regel zu Sperrfristen veröffentlicht ist - Releases, die als öffentliche Git-Tags erscheinen, Entwicklungsarbeit, die im offenen Repository sichtbar ist, Sicherheitskorrekturen, die auf das Release warten müssen, wie Sie eine Sperrfrist schriftlich vereinbaren und wie Sie einen Beitrag über eine Entwicklungslinie zeitlich planen.
keywords: Sperrfrist, Embargo, Zeitpunkt, Zeitpunkt eines Release, Veröffentlichungszeitpunkt, Vorab-Briefing, Sicherheits-Sperrfrist, koordinierte Offenlegung, offene Entwicklung, Tags, Planung, embargo, timing, release timing, publication time, pre-briefing, security embargo, coordinated disclosure, open development, tags, scheduling
type: help
---
# Sperrfristen und Zeitpunkt

Diese Seite ist für Reporter und Redakteure, die planen, wann sie veröffentlichen. Exponential wird offen entwickelt:
Die meisten Neuigkeiten sind in dem Moment öffentlich, in dem sie geschehen, im Repository. Das Projekt veröffentlicht
keine Regel zu Sperrfristen. Diese Seite erklärt, was das für den Zeitpunkt bedeutet, und den einen Fall, in dem Warten
Pflicht ist.

## Inhalt

1. [Offene Entwicklung heißt keine geheimen Termine](#offene-entwicklung-heißt-keine-geheimen-termine)
2. [Releases](#releases)
3. [Arbeit in Entwicklung](#arbeit-in-entwicklung)
4. [Sicherheit: der eine Fall, in dem Sie warten](#sicherheit-der-eine-fall-in-dem-sie-warten)
5. [Eine Sperrfrist vereinbaren](#eine-sperrfrist-vereinbaren)

## Offene Entwicklung heißt keine geheimen Termine

Jeder Commit auf den main-Branch ist auf GitHub öffentlich, und die Geschichtsseiten beschreiben die Arbeit Monat für
Monat ([Die Geschichtsseiten als Quelle](die-geschichtsseiten-als-quelle.md)). Ein Journalist, der dem Repository folgt,
sieht Funktionen, während sie gebaut werden. Meist gibt es nichts, wofür eine Sperrfrist gelten könnte.

## Releases

- Ein Release findet statt, wenn sein Git-Tag veröffentlicht wird ([Über ein Release berichten](ueber-ein-release-berichten.md)).
  Ab diesem Moment kann darüber berichtet werden.
- In den Quellen, die diese Hilfe verwendet, werden keine Veröffentlichungsdaten im Voraus angekündigt. Veröffentlichen
  Sie kein Datum, das Sie erschlossen haben.
- Packagist zeigt die neue Version kurz nach dem Tag; Composer-Nutzer können sie ab dann installieren.

## Arbeit in Entwicklung

Sie dürfen über Arbeit in der Entwicklungslinie berichten; sie ist öffentlich. Machen Sie klar, dass sie unveröffentlicht
ist:

```text
„Das Changelog des kommenden Release 6.0.15, das noch in Entwicklung ist, nennt ...“
```

Funktionen in Entwicklung können sich vor dem Release ändern. Prüfen Sie sie erneut, wenn das Release getaggt ist
([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).

## Sicherheit: der eine Fall, in dem Sie warten

Sicherheitslücken werden vertraulich gemeldet und im nächsten Release behoben ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
Wenn Sie von einer nicht behobenen Sicherheitslücke erfahren, von einer Quelle, einem Leser oder aus eigenen Tests:

1. Veröffentlichen Sie keine technischen Einzelheiten und keinen Weg, sie auszunutzen.
2. Stellen Sie sicher, dass sie vertraulich an security@se7enx.com gemeldet wurde ([Wenn Sie eine Sicherheitslücke finden](wenn-sie-eine-sicherheitsluecke-finden.md)).
3. Veröffentlichen Sie, nachdem das korrigierte Release erschienen ist, und verweisen Sie Leser auf das Update
   ([Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md)).

Das schützt die Menschen, die Websites betreiben, nicht das Image des Projekts.

## Eine Sperrfrist vereinbaren

Wenn 7x oder die Foundation Ihnen Informationen anbietet, bevor sie öffentlich sind (eine Ankündigung, ein Interview
über etwas Kommendes), vereinbaren Sie die Bedingungen zuerst schriftlich:

```text
Ich verpflichte mich, Informationen aus <Briefing / Dokument> nicht vor <Datum, Uhrzeit, Zeitzone> zu
veröffentlichen, es sei denn, sie werden früher über <das Repository / die Website> öffentlich; dann darf ich
ab diesem Zeitpunkt veröffentlichen.
```

Die zweite Klausel ist in einem offenen Projekt wichtig: Sobald der Code öffentlich ist, ist er öffentlich.

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md)
- [Ein Interview anfragen](ein-interview-anfragen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
