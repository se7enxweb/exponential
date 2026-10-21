---
title: Eine dringende Warnmeldung auf der Startseite veröffentlichen
audience: government, editor
category: public-sector
order: 50
summary: Für Redakteure in Bereitschaft - eine dringende Meldung schnell vor die Besucher bringen, über den Ordner der Bekanntmachungen, den Ihre Startseite zeigt, sie durch jeden Cache hindurch prüfen, aktualisieren und herunternehmen; Exponential hat kein eingebautes Warnbanner.
keywords: dringende Warnmeldung, Startseite, Bekanntmachung, Banner, Notfall, Schließung, Störung, Cache, schnell veröffentlichen, verstecken, Bereitschaft, urgent alert, front page, notice, banner, emergency, closure, disruption, cache, publish fast, hide, on-call
type: help
---
# Eine dringende Warnmeldung auf der Startseite veröffentlichen

Diese Seite ist für Redakteure in Bereitschaft, die Besucher jetzt über eine Störung, eine Schließung oder eine Gefahr
informieren müssen. Exponential hat kein eingebautes Warnbanner; Sites zeigen Warnmeldungen mit einem Ordner für
Bekanntmachungen, den die Startseite (oder jede Seite) auflistet, oder mit einem Block im Layout-Editor. Wie Ihre Site
das macht, wurde beim Aufbau entschieden
([Das Muster für dringende Bekanntmachungen](../../aufbauen/nachrichten-und-warnmeldungen/das-muster-fuer-dringende-bekanntmachungen.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Die Warnmeldung aktualisieren und beenden](#die-warnmeldung-aktualisieren-und-beenden)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie wissen, wohin Warnmeldungen auf Ihrer Site gehören: in einen Ordner Notices (oder Alerts) oder in einen
  Layout-Block.
- Sie können sich anmelden, einschließlich des zweiten Schritts (halten Sie Ihre Authenticator-App oder Ihre E-Mail
  bereit).
- Ihre Rolle darf dort ohne Freigabe veröffentlichen (Bereitschaftsrollen dürfen das meist).
- Sie haben den genehmigten Wortlaut oder einen vorbereiteten Mustertext.

## Schritte

**Mit einem Ordner für Bekanntmachungen:**

1. Öffnen Sie den Ordner der Bekanntmachungen, **Neu erstellen**, wählen Sie die Klasse für Bekanntmachungen.
2. Überschrift: was und wo, in wenigen Worten. Text: zuerst, was die Menschen tun sollen.
3. Schweregrad: die richtige Stufe. **Hide after**: die Endzeit, falls bekannt.
4. **Zur Veröffentlichung schicken**.

**Mit einem Layout-Block** ([Der Layout-Editor](../../../layout-editor.md)):

1. **Layouts**, öffnen Sie das Layout, das die Zone für Warnmeldungen enthält (oft ein gemeinsames Layout für den
   Kopfbereich).
2. Wählen Sie den Block für Warnmeldungen, tippen Sie den Text in seine Eigenschaften, **Publish layout**.

## Das Ergebnis prüfen

- [ ] Öffnen Sie die Startseite abgemeldet in einem privaten Fenster: Die Warnmeldung ist da.
- [ ] Öffnen Sie eine andere Seite, wenn Warnmeldungen auf der ganzen Site erscheinen.
- [ ] Wenn die Warnmeldung nicht in der Zeit erscheint, die Ihr Ablauf vorsieht, bitten Sie das Web-Team, die Caches
  zu aktualisieren (statischer Cache, der Antwort-Cache von Velocity, ein CDN), wie geübt
  ([Eine Seite aktualisieren, die alte Inhalte zeigt](eine-seite-aktualisieren-die-alte-inhalte-zeigt.md)).
- [ ] Bitten Sie die zweite Person in Bereitschaft, sie zu lesen.

## Die Warnmeldung aktualisieren und beenden

| Bedarf | Tun |
|---|---|
| Neue Informationen | **Bearbeiten**, den Text ändern, oben „Aktualisiert <Uhrzeit>: <Änderung>“ hinzufügen, veröffentlichen |
| Jetzt beenden | **Verstecken / Zeigen** an der Bekanntmachung (oder den Layout-Block leeren und das Layout veröffentlichen) |
| Zu einer bekannten Zeit beenden | setzen Sie **Hide after**; sie wird beim nächsten Lauf des Cronjobs danach versteckt |

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Nachts auf eine Freigabe warten | Bereitschaftsrollen sollten außerhalb des Freigabe-Workflows stehen; prüfen Sie das in einer Übung |
| Die Warnmeldung nur in einem Bild | zuerst Text; Bilder laden vielleicht nicht und können nicht von allen gelesen werden |
| Das Beenden vergessen | setzen Sie eine Endzeit oder eine Erinnerung; eine veraltete Warnmeldung kostet Vertrauen |
| Annehmen, dass alle sie gesehen haben | die Website ist ein Kanal; nutzen Sie die anderen aus Ihrem Ablauf |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine öffentliche Bekanntmachung mit Ablaufdatum veröffentlichen](eine-oeffentliche-bekanntmachung-mit-ablaufdatum-veroeffentlichen.md)
- [Warnmeldungen, Verfügbarkeit, Wartungsmodus und statischer Cache](../../../../../eng-us/help/government/levels/emergency-services/alerts-availability-maintenance-and-static-cache.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
