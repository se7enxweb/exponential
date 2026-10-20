---
title: Das Audit-Protokoll für einen Benutzer prüfen
audience: government, admin
category: public-sector
order: 150
summary: Schritt für Schritt - alles sehen, was ein Benutzer getan hat (Anmeldungen, fehlgeschlagene Anmeldungen, verschobene, versteckte oder entfernte Inhalte, Änderungen an Rollen und Einstellungen), in der Audit-Konsole von Exponential oder mit exp:audit search, nach Zeit und Ereignis eingrenzen und festhalten, was Sie gefunden haben.
keywords: Audit-Protokoll, Benutzeraktivität, Audit-Konsole, exp:audit search, Filter, Benutzer-ID, Login, access.session, content.node, Rollenänderungen, Untersuchung, Prüfung, audit/read, öffentlicher Sektor, audit trail, user activity, audit console, filters, user id, role changes, investigation, review, public sector
type: help
---
# Das Audit-Protokoll für einen Benutzer prüfen

Für Administratoren und Prüfer mit `audit/read`: eine Prüfung, wenn jemand ausscheidet, eine Beschwerde über eine
Änderung oder der Verdacht auf Missbrauch
([Das Audit-Protokoll für Behörden](../../../../../eng-us/help/government/security/audit-trail-for-public-bodies.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte in der Konsole](#schritte-in-der-konsole)
3. [Schritte auf der Kommandozeile](#schritte-auf-der-kommandozeile)
4. [Was Sie sehen werden und was nicht](#was-sie-sehen-werden-und-was-nicht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Richtlinie `audit/read` (nach Kanal eingeschränkt, wenn so zugewiesen). Jede Ansicht des Audits wird selbst
  aufgezeichnet.
- Die ID des Benutzers (Benutzer > Benutzerkonten prüfen oder der Knoten des Benutzers) oder sein Login.
- Folgen Sie bei Personalangelegenheiten Ihrem Verfahren (wer nachsehen darf, wer informiert werden muss).

## Schritte in der Konsole

1. Öffnen Sie den Reiter **Audit** > **Konsole** (`/audit/console`).
2. Filtern Sie nach Benutzer: `/audit/console/(user)/<id>` (oder `(login)/<login>`).
3. Grenzen Sie ein: `(from)/<YYYY-MM-DD>/(to)/<YYYY-MM-DD>`, `(name)/access.session.*` für Anmeldungen,
   `(name)/content.node.*` für Verschieben, Verstecken und Entfernen, `(name)/access.role.*` für Rollenänderungen.
4. Öffnen Sie ein Ereignis (`audit/event/<id>`) für den vollständigen Eintrag: Werte vorher und nachher, die Anfrage,
   die anderen Ereignisse derselben Anfrage.
5. Die gefilterte Adresse lässt sich als Lesezeichen speichern und mit jemandem teilen, der ebenfalls `audit/read` hat.

## Schritte auf der Kommandozeile

```bash
./console exp:audit search --user=<id> --from=<YYYY-MM-DD> --limit=200 --allow-root-user
./console exp:audit search --user=<id> --name='content.node.*' --allow-root-user
```

Zeiten ohne Offset sind auf der Kommandozeile UTC und in der Konsole die Zeitzone der Site.

## Was Sie sehen werden und was nicht

- Einträge innerhalb der Aufbewahrung (90 Tage live; ältere Tage nach `exp:audit restore`).
- Standardmäßig nicht aufgezeichnet: das Veröffentlichen und Anlegen von Inhalten (`content.object.publish`,
  `content.object.create`), Lesezugriffe. Wie ein Objekt aussah, zeigt seine Versionsseite.
- Adressen auf /24 gekürzt; nach 90 Tagen zeigt der Index pseudonymisierte Werte (der Export findet sie trotzdem).

## Verwandte Seiten

- [Anleitung zum Audit: die Oberfläche der Administration](../../../../../eng-us/bc/6.0/audit.md#32-the-admin-interface)
- [Audit-Einträge für eine Untersuchung exportieren](audit-eintraege-fuer-eine-untersuchung-exportieren.md)
- [Aufzeichnen, wer Inhalte veröffentlicht hat](aufzeichnen-wer-inhalte-veroeffentlicht-hat.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
