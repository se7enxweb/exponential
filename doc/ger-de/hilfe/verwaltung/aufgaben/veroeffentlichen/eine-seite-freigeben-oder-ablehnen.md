---
title: Eine Seite freigeben oder ablehnen
audience: government, editor
category: public-sector
order: 250
summary: Für Freigebende - Versionen finden, die auf Ihre Freigabe warten, sie lesen und vergleichen, mit einem Kommentar im Eingang der Zusammenarbeit freigeben oder ablehnen und wissen, wann die freigegebene Version erscheint.
keywords: Freigebender, freigeben, ablehnen, Zusammenarbeit, Mein Konto Zusammenarbeit, ausstehende Elemente, Wartet auf Ihre Freigabe, vergleichen, Kommentar, Workflow-Cronjob, approver, approve, deny, reject, collaboration, pending items, waiting for your approval, compare, comment, workflow cronjob
type: help
---
# Eine Seite freigeben oder ablehnen

Diese Seite ist für Freigebende: Referatsleitungen, juristische Prüfer, fachliche Prüfer, Verantwortliche für die
Kommunikation. Ein Workflow-Ereignis *Approve* erzeugt ein Element der Zusammenarbeit für die Freigebenden; Sie
entscheiden im Eingang der Zusammenarbeit
([Workflows](../../../../anleitungen/workflows.md), [Entwürfe und ausstehende Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie sind ein Freigebender (im Ereignis genannt oder in einer Gruppe, die freigibt).
- Sie dürfen die Versionen lesen, die Sie freigeben (`content/versionread`); eine Version, die auf Sie wartet, wird nur
  aufgelistet, wenn Sie sie lesen dürfen.
- Eine Benachrichtigung über neue Anfragen per E-Mail lässt sich unter **Meine Benachrichtigungseinstellungen** im
  Bereich Zusammenarbeit einstellen.

## Schritte

1. Öffnen Sie **Meine ausstehenden Elemente** und filtern Sie nach **Wartet auf Ihre Freigabe**, oder öffnen Sie
   **Mein Konto > Zusammenarbeit** (`/collaboration/view/summary`).
2. Öffnen Sie die Anfrage. Lesen Sie die Vorschau der Version.
3. Bei einer Änderung an einer veröffentlichten Seite öffnen Sie die Versionsseite und verwenden **Vergleichen**, um
   genau zu sehen, was sich geändert hat ([Zwei Versionen vergleichen](zwei-versionen-vergleichen.md)).
4. Entscheiden Sie:
   - **Freigeben**: Die Version wird beim nächsten Lauf des Workflow-Cronjobs veröffentlicht;
   - **Ablehnen**, mit einem Kommentar, der sagt, was zu ändern ist: Der Autor kann einen neuen Entwurf anlegen.

## Das Ergebnis prüfen

- [ ] Die Anfrage verschwindet aus Ihrer Liste offener Elemente.
- [ ] Nach dem nächsten Lauf des häufigen Cronjobs ist eine freigegebene Version veröffentlicht (abgemeldet prüfen).
- [ ] Das Audit-Protokoll zeichnet die Veröffentlichung auf.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Freigeben, ohne zu vergleichen | vergleichen Sie bei Änderungen; kleine Änderungen verstecken sich in langen Texten |
| Eine Ablehnung ohne Begründung | kommentieren Sie immer; der Autor muss wissen, was zu ändern ist |
| Eigene Inhalte freigeben | Sie werden nicht gefragt: die eigenen Inhalte eines Freigebenden passieren das Ereignis |
| Anfragen warten, während Sie abwesend sind | vereinbaren Sie eine Vertretung; das Web-Team kann Freigebende hinzufügen |
| Freigegeben, aber nach einer Stunde nicht veröffentlicht | der Workflow-Cronjob läuft vielleicht nicht; informieren Sie das Web-Team |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Seite zur Freigabe schicken](eine-seite-zur-freigabe-schicken.md)
- [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
