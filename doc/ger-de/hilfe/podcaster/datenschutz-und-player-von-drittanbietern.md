---
title: Datenschutz und Player von Drittanbietern
audience: podcaster
category: podcasting-streaming
order: 43
summary: Wie sich eingebettete Player von Video- und Streamingdiensten auf einer Exponential-Website auf die Privatsphäre der Hörer auswirken - Laden per Klick im Block External video und in der Ansicht von NG Video, die datenschutzfreundlicheren Adressen von YouTube und Vimeo, die Content Security Policy, der Block HTML snippet, Ihre Datenschutzerklärung und was Ihr eigener Player und Ihr Feed senden.
keywords: Datenschutz, Player von Drittanbietern, Einbettung, YouTube, youtube-nocookie, Vimeo, dnt, Laden per Klick, Einwilligung, Cookies, Content Security Policy, frame-src, media-src, Datenschutzerklärung, DSGVO, privacy, third-party player, embed, click to load, consent, cookies, privacy notice, GDPR
type: help
---
# Datenschutz und Player von Drittanbietern

Diese Seite ist für Produzenten und Website-Betreiber, die Player anderer Dienste einbetten und Hörern sagen müssen, was
mit ihren Daten geschieht. Ein Player von einem anderen Host ist ein Fenster zu diesem Host: Sobald er lädt, sieht der
Dienst den Besucher. Die Layout-Blöcke von Exponential sind so gebaut, dass sie das aufschieben, bis der Besucher sich
entscheidet. Dies ist keine Rechtsberatung; fragen Sie die Person, die für den Datenschutz verantwortlich ist.

## Inhalt

1. [Ihr eigener Player](#ihr-eigener-player)
2. [Eingebettete Player: Laden per Klick](#eingebettete-player-laden-per-klick)
3. [Der Block HTML snippet](#der-block-html-snippet)
4. [Content Security Policy](#content-security-policy)
5. [Ihre Datenschutzerklärung](#ihre-datenschutzerklärung)

## Ihr eigener Player

Eine Episode, die mit dem HTML5-Player des Medienfelds abgespielt wird, lädt die Datei von Ihrer eigenen Website
(`/content/download/...`); kein Dritter ist beteiligt. Ihr Webserver protokolliert die Anfrage wie jede andere
([Downloads und Statistiken](downloads-und-statistiken.md)). Dasselbe gilt für Podcast-Apps, die Ihren Feed und Ihre
Dateien abrufen: Sie sprechen nur mit Ihrer Website.

## Eingebettete Player: Laden per Klick

Auf Installationen mit Exponential Layouts lädt der Block **External video** den Player nicht mit der Seite. Er zeigt
eine Schaltfläche **Load video** und den Hinweis "Loading it sends data to <host>." (das Laden sendet Daten an den
Host). Erst nach dem Klick wird das `<iframe>` eingefügt, in einer Sandbox und mit dem Ursprung Ihrer Website als
einzigem Referrer (`extension/explayouts/design/standard/templates/explayouts/block/parts/embed.tpl`).

Wenn der Player geladen ist, verwendet er die datenschutzfreundlicheren Varianten, wo der Dienst sie anbietet
(`extension/explayouts/classes/explayoutsvideoblockhandler.php`):

| Dienst | Verwendete Adresse | Hinweis im Code |
|---|---|---|
| YouTube | `https://www.youtube-nocookie.com/embed/<id>` | "without tracking cookies before the video is played" (ohne Tracking-Cookies, bevor das Video abgespielt wird) |
| Vimeo | `https://player.vimeo.com/video/<id>?dnt=1` | derselbe |
| Dailymotion | `https://www.dailymotion.com/embed/video/<id>` | Standardadresse |

Nachdem der Besucher das Video abgespielt hat, gelten die eigenen Bedingungen des Dienstes. Auf Installationen mit
Media Site lädt auch die Vollansicht von *NG Video* YouTube erst nach einem Klick (`full_ng_video.tpl`); ihre Rahmen
für Vimeo und Dailymotion laden mit der Seite.

## Der Block HTML snippet

Der Block **HTML snippet** fügt sein HTML so ein, wie es geschrieben ist. Ein dort eingefügter Einbettungscode lädt
sofort, mit allem, was der Code des Dienstes tut: kein Laden per Klick, keine Sandbox. Bevorzugen Sie den Block
External video; überlassen Sie HTML snippet vertrauenswürdigen Redakteuren und Code, den Sie gelesen haben.

## Content Security Policy

Die Richtlinie der Website wird in `settings/site.ini [ContentSecurityPolicy]` eingestellt. Im Auslieferungszustand
gilt `Mode=auto`: nur berichtend in der Administration und **keine Richtlinie** auf öffentlichen Siteaccesses. Wo eine
Website eine durchsetzt, erlauben die Standardwerte Rahmen und Medien nur von der Website selbst (`frame-src 'self'`,
`media-src 'self' blob:`). Ein eingebetteter Player braucht dann seinen Host ergänzt:

```ini
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
AdditionalSources[]=frame-src https://www.youtube-nocookie.com https://player.vimeo.com
```

Werden Audio- oder Videodateien von einem anderen Host ausgeliefert (einem CDN unter eigenem Namen), braucht
`media-src` diesen Host ebenfalls. Siehe [Content Security Policy](../hosting/sicherheit/content-security-policy.md) und
die Funktionsbeschreibung [Content Security Policy](../../funktionen/6.0/content-security-policy.md).

## Ihre Datenschutzerklärung

Sagen Sie in einfachen Worten:

- welche Player von welchen Diensten die Website einbettet und dass sie erst nach einem Klick laden (wo das zutrifft);
- was die eigenen Server-Logs der Website über Downloads und Feed-Abrufe festhalten und wie lange Logs aufbewahrt
  werden ([Aufbewahrung von Logs und Daten](../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md));
- welcher Analysedienst auf den Seiten läuft, falls einer;
- für Mitglieder: was über ihr Konto gespeichert wird.

Der Pressebereich hat den passenden Rat für Menschen vor der Kamera: [Privatsphäre vor der Kamera](../presse/privatsphaere-vor-der-kamera.md).

## Verwandte Seiten

- [Ein Video oder einen Stream einbetten](ein-video-oder-einen-stream-einbetten.md)
- [Compliance und DSGVO](../hosting/sicherheit/compliance-und-dsgvo.md)
- [Besucherstatistiken und Analyse](../ersteller/website-ersteller/besucherstatistik-und-analyse.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
