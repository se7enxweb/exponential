---
title: Warnungen in vielen Sprachen und in einfacher Sprache
audience: government, editor, project-manager, website-creator
category: public-sector
order: 70
summary: Wie eine Stelle des öffentlichen Gesundheitsdienstes in Exponential eine Warnung schnell veröffentlicht, sie in die Sprachen übersetzt, die die Menschen sprechen, sie einfach hält, während des Ereignisses aktualisiert und beendet - mit der Klasse Warnung, Übersetzungen, Caches und Benachrichtigungen.
keywords: Gesundheitswarnung, Ausbruch, Warnung, Übersetzung, mehrsprachig, einfache Sprache, Warnung aktualisieren, Warnung beenden, Datum zum Verbergen, Startseite, Cache, Benachrichtigungen, RSS, health alert, outbreak, warning, translation, multilingual, plain language, update alert, end alert, hide date, front page, cache, notifications
type: help
---
# Warnungen in vielen Sprachen und in einfacher Sprache

Diese Seite richtet sich an die Personen, die Warnungen veröffentlichen, an die Redaktion, Website-Ersteller und
Projektverantwortliche einer Stelle des öffentlichen Gesundheitsdienstes. Sie beschreibt eine Warnung von der ersten
Veröffentlichung bis zu ihrem Ende, mit der Klasse Warnung aus
[Inhaltsmodell für den öffentlichen Gesundheitsdienst](inhaltsmodell.md) und den Rollen aus
[Struktur, Sektionen und Rollen für den öffentlichen Gesundheitsdienst](struktur-sektionen-und-rollen.md). Exponential
hat kein eigenes Warn- oder Rundfunksystem; dies ist eine Veröffentlichungspraxis, die auf Inhalten, Übersetzungen und
Caches aufbaut.

## Inhalt

1. [Einfach schreiben](#einfach-schreiben)
2. [Veröffentlichen](#veröffentlichen)
3. [Übersetzen](#übersetzen)
4. [Aktualisieren](#aktualisieren)
5. [Beenden](#beenden)
6. [Die Menschen informieren](#die-menschen-informieren)

## Einfach schreiben

| Teil | Regel | Beispiel für die Form |
|---|---|---|
| Schlagzeile | was geschieht und wo, in weniger als zehn Wörtern | „<Krankheit>-Fälle in <Gebiet>“ |
| Was zu tun ist | die Handlung zuerst, als Anweisung | „Waschen Sie oft die Hände. Bleiben Sie zu Hause, wenn Sie krank sind.“ |
| Einzelheiten | wer gefährdet ist, Symptome, wann man Hilfe holt | kurze Absätze unter Fragen |
| Zuletzt aktualisiert | immer angezeigt | |

Leitlinien für einfache Sprache: [plainlanguage.gov](https://www.plainlanguage.gov/). Bereiten Sie Vorlagen typischer
Warnungen (Hitze, Wasser, Ausbruch) im Voraus als unveröffentlichte Entwürfe oder als Text im Verfahren vor, von
Medizinerinnen und Medizinern freigegeben, damit das Bereitschaftsteam die Einzelheiten ergänzt, statt unter Druck zu
schreiben.

## Veröffentlichen

1. Öffnen Sie den Ordner Alerts, **Neu erstellen** > **Warnung**.
2. Füllen Sie Schlagzeile, Schweregrad, was zu tun ist, Einzelheiten, Gebiet, Zeitpunkt der Herausgabe und, falls
   bekannt, die Endzeit aus.
3. **Zur Veröffentlichung schicken**. Warnungen liegen außerhalb des Workflows der medizinischen Freigabe, daher wird die
   Warnung sofort veröffentlicht.
4. Öffnen Sie die Startseite abgemeldet und prüfen Sie, dass die Warnung erscheint. Das Veröffentlichen leert den
   Ansichts-Cache der Warnung und ihrer verwandten Seiten; wird die Startseite aus einem statischen Cache, dem
   Antwort-Cache von Velocity oder einem CDN ausgeliefert, frischen Sie diese wie geprobt auf
   ([Caches](../../../../anleitungen/caches.md), [Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md)).

## Übersetzen

1. Wählen Sie an der Warnung die Sprache in der Liste neben **Bearbeiten** (oder **Neue Übersetzung** in der
   Sprachliste), dann **Bearbeiten**; das Bearbeitungsformular zeigt das Original als Referenz
   ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)).
2. Übersetzen Sie Schlagzeile, was zu tun ist, und Einzelheiten; Daten und Schweregrad sind nicht übersetzbar und
   bleiben gleich.
3. **Zur Veröffentlichung schicken**. Die Übersetzung erscheint im Siteaccess dieser Sprache.

| Situation | Tun |
|---|---|
| Eine Sprache ist noch nicht fertig | zuerst das Original veröffentlichen; ob es in Siteaccesses anderer Sprachen erscheint, hängt von der Einstellung „Immer verfügbar“ der Klasse und der Sprachliste Ihres Siteaccess ab |
| Viele Sprachen, wenige Übersetzende | zuerst „was zu tun ist“ übersetzen, dann die Einzelheiten |
| Eine Übersetzung weicht nach einer Aktualisierung ab | jede Sprache aktualisieren; der Reiter **Übersetzungen** zeigt, welche Sprachen es gibt |

Sprachen von rechts nach links werden von der Administration und von Templates unterstützt, die `dir` setzen
([Sprachen von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md)).

## Aktualisieren

1. **Bearbeiten** Sie die Warnung; ändern Sie den Text; setzen Sie **Zuletzt aktualisiert** auf jetzt.
2. Schreiben Sie die Änderung oben in die Einzelheiten („Aktualisiert <Uhrzeit>: <was sich geändert hat>“).
3. **Zur Veröffentlichung schicken**, dann jede Übersetzung aktualisieren.
4. Der frühere Wortlaut bleibt im Versionsverlauf ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)).

## Beenden

| Weg | Wann |
|---|---|
| Die Endzeit und `hide.php` | das Ende ist im Voraus bekannt; die Warnung wird beim nächsten Cronjob-Lauf nach dieser Zeit verborgen |
| **Verstecken / Zeigen** im Menü des Elements | das Ende wird jetzt entschieden |
| Eine letzte Aktualisierung, später verbergen | die Menschen müssen eine Weile „Die Warnung ist beendet“ lesen können |

Beendete Warnungen bleiben als Aufzeichnung in der Administration. Entfernen Sie sie nur, wenn Ihre Regeln für das
Schriftgut es erlauben.

## Die Menschen informieren

| Kanal | Einrichtung |
|---|---|
| RSS | ein RSS-Export des Ordners Alerts (**Setup > RSS**) für Medien und Partnerwebsites ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)) |
| E-Mail an Folgende | Personen mit einem Konto folgen dem Ordner Alerts mit **Benachrichtigen**; der Cronjob für Benachrichtigungen versendet die E-Mail ([Benachrichtigungen: der Leitfaden für Benutzer](../../../../../eng-us/features/6.0/notifications.md)) |
| Newsletter | für langsamere Aktualisierungen ([Newsletter mit CJW Newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)) |
| Soziale Medien | außerhalb von Exponential; auf die Adresse der Warnung verlinken |

Benachrichtigungen gehen nur an Personen, die sich zum Folgen entschieden haben, nach dem nächsten Lauf der
Benachrichtigungen; sie sind kein Warnsystem für Notfälle. Für dringende Warnungen an alle nutzen Sie die Warnsysteme,
die Ihr Land bereitstellt.

## Verwandte Seiten

- [Öffentliche Gesundheit](README.md)
- [Rettungsdienste](../rettungsdienste/README.md)
- [Mehrsprachige Websites](../../../../../eng-us/help/creators/website-creators/multilingual-sites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
