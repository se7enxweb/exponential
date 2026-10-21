---
title: Funktionen, die Kunden oft übersehen
audience: customer, editor, admin
category: customer-success
order: 60
summary: Werkzeuge, die mit Exponential geliefert werden und einem Team Zeit sparen, von denen Betreiber und Redakteure aber oft nicht wissen, dass sie sie haben - bei der Inhaltsarbeit, beim Aufräumen, im Shop, in der Administration und in der Hilfe - jeweils mit dem Ort, an dem sie zu finden sind, und ob eine Einstellung oder Berechtigung nötig ist.
keywords: versteckte Funktionen, Zeitersparnis, Inhaltsfinder, Inhaltsprüfungen, CSV-Export der Unterelemente, ohne Benachrichtigung veröffentlichen, Tastenkürzel, Hilfe-Center, dauerhafte Belege, Sortierung der Bestellungen, Kontenpflege, hidden features, time savers, content finder, content checks, sub-items CSV export, publish without notification, keyboard shortcuts, help center, permanent receipts, order sorting, user hygiene
type: help
---
# Funktionen, die Kunden oft übersehen

Diese Seite ist für Betreiber und erfahrene Redakteure, die mehr aus der Website herausholen möchten, die sie bereits
haben. Jedes der folgenden Werkzeuge wird mit Exponential geliefert, und jedes ersetzt Arbeit, die Teams oft von Hand
erledigen. Manche brauchen eine Einstellung oder eine Berechtigung; die Tabelle sagt das. Zeigen Sie diese Seite Ihrem
Team und Ihrem Partner, wenn Sie fragen, was eingeschaltet werden soll.

## Inhaltsarbeit

| Werkzeug | Was es erspart | Wo | Braucht |
|---|---|---|---|
| **Auswahl kopieren** in der Liste der Unterelemente | Viele Elemente einzeln zu kopieren | Die Liste der Unterelemente eines Knotens ([Ausgewählte Unterelemente kopieren](../../../funktionen/6.0/unterelemente-auswahl-kopieren.md)) | Inhaltsberechtigungen |
| **Einstellungen** der Unterelemente-Tabelle mit Spalten, Voreinstellungen und CSV-Export | Listen für einen Bericht von Hand zu erstellen | Die Liste der Unterelemente ([Die Liste der Unterelemente](../../../funktionen/6.0/optionen-der-unterelementtabelle.md)) | Nichts weiter |
| Inhaltsaufträge für große Entfernungen, Kopien und Verschiebungen | Vorgänge, die an Zeitlimits scheiterten | Von der Administration für große Teilbäume angeboten ([Inhaltsaufträge](../../../funktionen/6.0/inhaltsauftraege.md)) | Nichts weiter |
| **Ohne Benachrichtigung veröffentlichen** | E-Mails an jeden Abonnenten wegen einer Tippfehlerkorrektur | Neben der Schaltfläche zum Veröffentlichen ([Ohne Benachrichtigung veröffentlichen](../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)) | Einstellung in `notification.ini` und die Richtlinie `content/publish_without_notification` |
| Versionsvorschau in anderen View-Modi | Nur zu veröffentlichen, um ein Druck- oder PDF-Layout zu sehen | `content/versionview` mit einem View-Modus ([Versionsvorschau in anderen View-Modi](../../../funktionen/6.0/ansichtsmodi-der-versionsvorschau.md)) | Nichts weiter |
| Lesezeichenordner | Jeden Tag nach denselben Orten zu suchen | Die Seitenleiste und der Auswahldialog ([Lesezeichenordner](../../../funktionen/6.0/lesezeichenordner.md)) | Nichts weiter |
| Tastenkürzel, etwa `/` für die Suche | Wege mit der Maus | Überall in der Administration ([Tastenkürzel](../../tastenkuerzel.md)) | Funktioniert im Design Admin 4; die anderen Designs haben die meisten davon |

## Aufräumen

| Werkzeug | Was es erspart | Wo | Braucht |
|---|---|---|---|
| Der Inhaltsfinder | Die Suche nach den Inhalten hinter einer ID, einer Adresse oder einem Dateipfad | **Setup > Inhaltsfinder** ([Der Inhaltsfinder](../../../funktionen/6.0/inhalts-finder.md)) | `contentfinder/search` |
| Inhaltsprüfungen: nicht referenzierte Dateien, kaputte Beziehungen, fehlende Dateien | Zu raten, welche Bilder und Dateien niemand nutzt | `/contentcheck/...` ([Die Inhaltsprüfungen](../../../funktionen/6.0/inhaltspruefungen.md)) | `contentcheck/unreferenced`, `contentcheck/brokenrelations` |
| Die Prüfung umgekehrter Beziehungen vor dem Entfernen oder Verbergen | Kaputte Links nach einer Entfernung | Auf der Seite zum Entfernen angezeigt, ausgeliefert als `warn` ([Entfernen und Verbergen](../../../funktionen/6.0/pruefung-umgekehrter-beziehungen.md)) | Nichts weiter |
| Der Papierkorb zeigt, wer ein Element entfernt hat und wo es lag | Herumzufragen, wer etwas entfernt hat | Der Papierkorb ([Die Ansicht des Papierkorbs](../../../bc/6.0/papierkorb.md)) | Nichts weiter |
| **Benutzerkonten prüfen** | Konten einzeln zu öffnen, um ungenutzte oder doppelte zu finden | **Benutzer > Benutzerkonten prüfen** ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)) | Rechte zur Benutzerverwaltung |

## Der Shop

| Werkzeug | Was es erspart | Wo | Braucht |
|---|---|---|---|
| Sortieren der Bestellliste nach jeder Spalte, in der Adresse gespeichert | Scrollen nach den größten oder ältesten Bestellungen | **Shop > Bestellungen** ([Sortierung der Bestellliste](../../../funktionen/6.0/sortierung-der-bestellliste.md)) | `shop/administrate` |
| „So funktioniert Ihr Shop“ im Dashboard | Einem neuen Kollegen oder Partner die Kasse zu erklären | **Shop** ([Shop-Dashboard und Bestellstatus](../../../funktionen/6.0/shop-dashboard.md)) | `shop/administrate` |
| Dauerhafte Bestellbelege | Anrufe von Gästen wegen „Ich habe meine Bestellung verloren“ | Eine Einstellung ([Rechnungen und Belege](../handel/rechnungen-und-belege.md)) | `shop.ini [OrderViewSettings] OrderLinkView=orderreceipt` |
| Grenzen für Statusänderungen | Fehler von Mitarbeitenden, die nur verpacken und versenden sollen | Rollen mit `shop/setstatus`, nach Status eingeschränkt ([Bestellungen im Alltag](../handel/bestellungen-im-alltag.md)) | Ein Administrator legt die Rolle an |

## Administration und Hilfe

| Werkzeug | Was es erspart | Wo | Braucht |
|---|---|---|---|
| Das Hilfe-Center in der Administration | Im Internet nach Anleitungen zu suchen | `/exphelp/dashboard` und die Schaltfläche **Hilfe** auf jeder Seite ([Das Hilfesystem](../../../funktionen/6.0/hilfesystem.md)) | Die Richtlinie `exphelp/read` |
| Die Meldeseite der Hilfe | Einen Fehlerbericht von Grund auf zu schreiben | `/exphelp/report`, mit einem vorausgefüllten Issue und Diagnosedaten zum Kopieren (dieselbe Seite) | `exphelp/read` |
| Signierte Links in der E-Mail „New user registered“ | Nach einem neuen Konto zu suchen, um es zu genehmigen | Die E-Mail selbst ([Signierte Links in der Registrierungs-E-Mail](../../../funktionen/6.0/admin-links-der-registrierung.md)) | Rückmeldung zu Registrierungen per E-Mail |
| Wartungsmodus mit Vorschau | Eine leere oder kaputte Website während geplanter Arbeiten | **Setup > Wartung**, oder ein Befehl ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)) | Administrator |
| Cronjob-Teile pausieren | Bei einem Release auf jedem Server die Crontab zu bearbeiten | **Setup > Cronjobs** ([Cronjob-Teile pausieren](../../../funktionen/6.0/cronjobs-pausieren.md)) | Administrator |
| PDF-Export mit eigener Fußzeile | Inhalte für ein PDF in eine Textverarbeitung zu kopieren | **Setup > PDF export** ([PDF-Export](../../../funktionen/6.0/pdf-export.md)) | Die Richtlinie `pdf/edit` |

## Wie Sie sie einführen

1. Wählen Sie drei Werkzeuge von dieser Seite, die zu Arbeit passen, die Ihr Team heute von Hand erledigt.
2. Fragen Sie Ihren Administrator oder Partner, welche eine Einstellung oder eine Richtlinie brauchen, und bitten Sie
   darum, sie für die richtigen Rollen einzuschalten.
3. Zeigen Sie sie dem Team in zehn Minuten bei einer regelmäßigen Besprechung.
4. Fragen Sie nach einem Monat, ob sie genutzt werden. Behalten Sie, was hilft.

> **Tipp**
> Die „Nächsten Schritte“ im Shop-Dashboard und die Suche des Hilfe-Centers sind die beiden Orte, an denen die Software
> selbst vorschlägt, was als Nächstes zu tun ist. Machen Sie es sich zur Gewohnheit, sie zu lesen.

## Verwandte Seiten

- [Rundgang durch die Funktionen](rundgang-durch-die-funktionen.md)
- [Betriebskosten für Betreiber](betriebskosten-fuer-betreiber.md)
- [Rundgang durch die Administration](../../rundgang-durch-die-administration.md)
- [Tastenkürzel](../../tastenkuerzel.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
