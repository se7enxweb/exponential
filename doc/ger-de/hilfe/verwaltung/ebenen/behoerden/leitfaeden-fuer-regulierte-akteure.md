---
title: Leitfäden für regulierte Akteure
audience: government, project-manager, website-creator, editor
category: public-sector
order: 70
summary: Wie eine Behörde in Exponential Leitfäden veröffentlicht, auf die sich regulierte Akteure verlassen können - eine stabile Adresse, Geltungsdaten, Änderungshinweise, der Status als Objektzustand, rechtliche Prüfung, Benachrichtigungen und RSS bei Änderungen und der Umgang mit ersetzten Leitfäden.
keywords: Leitfäden, regulierte Akteure, Einhaltung, Compliance, Geltungsdatum, Änderungshinweis, ersetzt, zurückgezogen, stabile URL, Benachrichtigungen, RSS, rechtliche Prüfung, Versionen, guidance, regulated parties, effective date, change note, superseded, withdrawn, stable URL, notifications, legal review, versions
type: help
---
# Leitfäden für regulierte Akteure

Diese Seite richtet sich an Projektverantwortliche, Website-Ersteller und die Redaktion einer Behörde, die Leitfäden
veröffentlicht, denen andere folgen müssen. Sie beschreibt eine Veröffentlichungspraxis aus Funktionen von
Exponential: die Klasse Leitfaden und die Zustandsgruppe für den Status aus
[Inhaltsmodell für Behörden](inhaltsmodell.md), den Prüf-Workflow aus
[Struktur, Sektionen und Rollen für Behörden](struktur-sektionen-und-rollen.md) und die Versionsseite.

![Die Anmeldeseite der Administration](../../../../../eng-us/help/images/government/gov-sign-in.webp)

## Inhalt

1. [Grundsätze](#grundsätze)
2. [Neue Leitfäden veröffentlichen](#neue-leitfäden-veröffentlichen)
3. [Leitfäden ändern](#leitfäden-ändern)
4. [Ersetzen und zurückziehen](#ersetzen-und-zurückziehen)
5. [Regulierte Akteure informieren](#regulierte-akteure-informieren)
6. [Die Frage „Was stand damals darin?“ beantworten](#die-frage-was-stand-damals-darin-beantworten)

## Grundsätze

| Grundsatz | Wie |
|---|---|
| Eine stabile Adresse je Leitfaden | ein Objekt; zusätzliche Orte für andere Menüs; nie eine Kopie |
| Geltungsdatum sichtbar | das Attribut `effective_from`, oben angezeigt |
| Status sichtbar | der Zustand `guidance_status`, oben und in Listen angezeigt |
| Änderungen erklärt | das Attribut `change_note`, eine Zeile je Version |
| Vor dem Veröffentlichen geprüft | das Ereignis *Freigeben* für die Sektion der Leitfäden |
| Änderungen angekündigt | Benachrichtigungen und ein RSS-Feed der Leitfadenbibliothek |

## Neue Leitfäden veröffentlichen

1. Die Autorin oder der Autor legt in der Leitfadenbibliothek ein Leitfaden-Objekt an, füllt Fundstelle,
   Zusammenfassung, Text, Geltungsdatum und Tags aus und schreibt „Erste Fassung“ als Änderungshinweis.
2. **Zur Veröffentlichung schicken**. Die Version wird ausstehend; die prüfende Person erhält ein Element der
   Zusammenarbeit.
3. Die prüfende Person öffnet es unter **Mein Konto > Zusammenarbeit**, liest die Vorschau und drückt **Freigeben**
   oder **Ablehnen** mit einem Kommentar ([Workflows](../../../../anleitungen/workflows.md)).
4. Nach dem Lauf des Workflow-Cronjobs wird der Leitfaden mit dem Standardzustand *In Kraft* veröffentlicht.
5. Das Webteam fügt unter den Zielgruppenordnern, die ihn auflisten sollen, zusätzliche Orte hinzu (bearbeiten,
   **Orte**, **Orte hinzufügen**).

## Leitfäden ändern

1. Die Autorin oder der Autor drückt **Bearbeiten**; aus der veröffentlichten Version wird ein neuer Entwurf.
2. Sie ändert den Text, aktualisiert das Geltungsdatum, falls es sich ändert, und ersetzt den Änderungshinweis durch den
   dieser Version, zum Beispiel „Abschnitt 3 aktualisiert: neue Frist für Jahresmeldungen.“
3. Prüfung und Veröffentlichung wie oben. Die vorherige Version wird *archiviert* und bleibt auf der Versionsseite.
4. Eine prüfende Person kann auf der Versionsseite **Vergleichen** verwenden, um vor der Freigabe jede Änderung zu sehen
   ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)).

Kleine Korrekturen (ein Tippfehler, ein defekter Link) gehen denselben Weg; vereinbaren Sie, ob sie geprüft werden
müssen. **Ohne Benachrichtigung veröffentlichen** kann, wo eingeschaltet, verhindern, dass kleine Korrekturen
Abonnenten per E-Mail erreichen
([Ohne Benachrichtigung veröffentlichen](../../../../../eng-us/features/6.0/publish-without-notification.md)).

## Ersetzen und zurückziehen

| Situation | Tun |
|---|---|
| Ein neuer Leitfaden ersetzt einen alten | das neue Objekt mit **Ersetzt** auf das alte veröffentlichen; den Zustand des alten Objekts auf *Ersetzt* setzen; die alte Seite nennt ihren Nachfolger |
| Ein Leitfaden wird ohne Ersatz zurückgezogen | den Zustand auf *Zurückgezogen* setzen; die Seite mit einer kurzen Erklärung behalten oder verbergen, wenn Ihre Richtlinie es so vorsieht |
| Ein Abschnitt eines Leitfadens wird zurückgezogen | bearbeiten, den Abschnitt entfernen, den Änderungshinweis schreiben |

Zustände werden im Reiter **Details**, Kasten **Zustand des Objekts**, **Zustände einstellen** gesetzt; es entsteht
keine neue Version, sodass der Text so bleibt, wie er freigegeben wurde
([Objektzustände](../../../../anleitungen/objektzustaende.md)). Templates sollten ersetzte und zurückgezogene Einträge deutlich
anzeigen und aus Listen „aktueller Leitfäden“ heraushalten.

## Regulierte Akteure informieren

| Kanal | Einrichtung |
|---|---|
| E-Mail bei Änderung | regulierte Akteure mit einem Konto folgen der Leitfadenbibliothek oder einem Themenordner mit **Benachrichtigen**; unter **Meine Benachrichtigungseinstellungen** wählen sie sofortige Benachrichtigung oder eine Zusammenfassung ([Benachrichtigungen: der Leitfaden für Benutzer](../../../../../eng-us/features/6.0/notifications.md)) |
| RSS | ein RSS-Export der Leitfadenbibliothek (**Setup > RSS**) ([RSS-Feeds](../../../../anleitungen/rss-feeds.md)) |
| Newsletter | eine regelmäßige Zusammenfassung mit cjw_newsletter ([Newsletter mit CJW Newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)) |
| Eine Seite „Letzte Änderungen“ | ein Template, das Leitfäden nach Änderungsdatum mit ihrem Änderungshinweis auflistet |

Benachrichtigungen brauchen den Cronjob für Benachrichtigungen und Konten mit `notification/use`.

## Die Frage „Was stand damals darin?“ beantworten

1. Öffnen Sie das Leitfaden-Objekt, **Versionen verwalten**.
2. Filtern Sie nach dem Status *Archiviert*; suchen Sie die Version, die an dem fraglichen Datum veröffentlicht war
   (jede Karte zeigt Erstellungs- und Änderungsdatum).
3. **Ansehen** zeigt den Text dieser Version; **Vergleichen** zeigt die Unterschiede zur aktuellen.
4. Das Audit-Protokoll bestätigt, wann jede Version von wem veröffentlicht wurde
   ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

Das funktioniert nur für Versionen, die noch aufbewahrt sind: Heben Sie die Grenze des Versionsverlaufs für die Objekte
der Klasse Leitfaden an oder bewahren Sie in Ihrem Schriftgutverwaltungssystem von jeder Version ein datiertes PDF auf
([PDF-Exporte](../../../../anleitungen/pdf-exporte.md)).

## Verwandte Seiten

- [Behörden](README.md)
- [Schwerpunkte der Anforderungen für Behörden](schwerpunkte-der-anforderungen.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
