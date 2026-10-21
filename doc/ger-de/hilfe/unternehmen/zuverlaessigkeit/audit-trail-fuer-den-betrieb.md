---
title: Audit-Trail für den Betrieb
audience: businesses, admin, project-manager
category: business-solutions
order: 60
summary: Wie der Audit-Trail, der mit Exponential geliefert wird, einem Unternehmen hilft, seine Website zuverlässig zu betreiben, die Änderung hinter einer Störung zu finden und nachzuweisen, dass seine Aufzeichnung der Ereignisse nicht verändert wurde.
keywords: Audit-Trail, Audit, wer hat was getan, manipulationssicher, Hash-Kette, prüfen, Alarme, Betrieb, Änderungsprotokoll, Störung, Compliance-Nachweis, audit trail, who did what, tamper-evident, hash chain, verify, alerts, operations, change log, incident, compliance evidence
type: help
---
# Audit-Trail für den Betrieb

Diese Seite richtet sich an Führungskräfte und Administratoren, die wissen wollen, wer was auf der Website geändert hat,
vor allem wenn etwas schiefgegangen ist. Sie erklärt, was der Audit-Trail aufzeichnet, wie er dem Betrieb hilft, wie Sie
ihn lesen und was Sie einmalig einrichten. Die vollständige Referenz finden Sie unter
[Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) und
[Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md#3-das-audit-protokoll-lesen-5-minuten). Die Nutzung für
Compliance behandelt eine eigene Seite:
[Compliance-Nachweise aus dem Audit-Trail](../sicherheit/compliance-nachweise-aus-dem-audit-trail.md).

![Das Audit-Dashboard](../../../../eng-us/help/images/help/bu-audit-ops.webp)

## Inhalt

1. [Warum der Betrieb einen Audit-Trail braucht](#warum-der-betrieb-einen-audit-trail-braucht)
2. [Was aufgezeichnet wird](#was-aufgezeichnet-wird)
3. [Der Nachweis, dass die Aufzeichnung nicht verändert wurde](#der-nachweis-dass-die-aufzeichnung-nicht-verändert-wurde)
4. [Nutzung nach einer Störung](#nutzung-nach-einer-störung)
5. [Alarme](#alarme)
6. [Einmalig einrichten](#einmalig-einrichten)
7. [Wer ihn lesen darf](#wer-ihn-lesen-darf)
8. [Checkliste](#checkliste)

## Warum der Betrieb einen Audit-Trail braucht

Viele Störungen beginnen mit einer Änderung: eine geschriebene Einstellung, eine vergebene Rolle, ein entfernter
Teilbaum, ein im falschen Moment geleerter Cache. Wenn sich die Website seltsam verhält, lautet die schnellste Frage:
„Was hat sich kurz davor geändert?“ Der Audit-Trail beantwortet sie.

Ab Exponential 6.0.15 ist er standardmäßig eingeschaltet und zeichnet ohne Einrichtung auf
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Was aufgezeichnet wird

Aus [Der Audit-Trail, was aufgezeichnet wird](../../../../eng-us/features/6.0/audit-trail.md#what-is-recorded):

| Kanal | Beispiele |
|---|---|
| `content` | Veröffentlichen, Verschieben, Kopieren, Entfernen, Verbergen, Sektionen, Zustände, Papierkorb, Versionen, URL-Aliase, Klassen, Inhaltsjobs |
| `access` | Anmeldung, fehlgeschlagene Anmeldung, verweigerte Ansicht, Kontosperre, Rollenzuweisung, Zurücksetzen des Passworts |
| `system` | Schreiben von Einstellungen, Leeren von Caches, Cronjob-Läufe, Befehle, Paketinstallationen, Wartung, Deployments |
| `commerce` | Bestellungen, Zahlungen (nie Kartendaten), USt, Währungen, Rabatte, Exporte und Importe |
| `read` | Stichprobenartige Ansichten, Suchen und Downloads, nur wenn Lesezugriffe eingeschaltet sind |

Für den Betrieb ist der Kanal `system` der nützlichste: Er zeigt Deployments, Wartungsfenster, geleerte Caches und
geänderte Einstellungen an einer Stelle.

Mitgelieferte Aufbewahrung laut `settings/audit.ini`: Die Kanäle `content`, `access`, `system` und `commerce` behalten
laufende Dateien 90 Tage und Archive 730 Tage, der Kanal `read` 30 und 90 Tage. Das lässt sich in einem Override ändern
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#what-is-recorded)).

## Der Nachweis, dass die Aufzeichnung nicht verändert wurde

Ein Log, das jeder bearbeiten kann, beweist wenig. Der Audit-Trail ist manipulationssicher
([Audit-Trail, warum Sie ihn wollen](../../../../eng-us/features/6.0/audit-trail.md#why-you-want-it)):

- Jeder Eintrag trägt den Hash des Eintrags davor (eine Hash-Kette).
- Tagesdateien werden abgeschlossen, archiviert und in signierten Manifesten aufgeführt.
- `verify` sagt Ihnen, ob etwas bearbeitet oder herausgeschnitten wurde:

```bash
php bin/php/audit.php verify --allow-root-user     # exit 0: every chain intact; 1: a chain is broken
php bin/php/audit.php status --allow-root-user     # one line per channel, ending in "intact"
```

Führen Sie `verify` nach Zeitplan aus und lassen Sie bei einem Exit-Status ungleich null alarmieren
([Health-Checks und Monitoring](health-checks-und-monitoring.md#prüfungen-auf-dem-server)).

## Nutzung nach einer Störung

1. Notieren Sie die Uhrzeit, zu der das Problem begann (aus Ihrem Monitoring oder der ersten Kundenmeldung).
2. Öffnen Sie in der Administration den Reiter **Audit** (`/audit/dashboard`) und dessen Konsole, gefiltert auf ein
   Zeitfenster kurz vor diesem Zeitpunkt.
3. Suchen Sie in `system` nach Deployments, geschriebenen Einstellungen, Wartung und geleerten Caches und in `content`
   nach umfangreichen Entfernungen oder Verschiebungen.
4. Von einer Fehlerreferenz aus öffnet der Link **Das Audit dieses Moments** im Log-Viewer dasselbe Zeitfenster direkt
   ([Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md)).
5. Schreiben Sie Ihre Erkenntnisse in die Störungsnotiz.

In einer Shell die neuesten Ereignisse eines Kanals:

```bash
php bin/php/audit.php tail --channel=system --lines=20 --allow-root-user
```

> **Tipp**
> Das Entfernen eines Teilbaums wird als ein übergeordnetes Ereignis mit je einem untergeordneten Ereignis pro Knoten
> darunter aufgezeichnet, und ein Inhaltsjob ist das übergeordnete Ereignis von allem, was seine Stapel getan haben.
> „Wer hat das entfernt?“ hat genau eine Antwort ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#why-you-want-it)).

## Alarme

Alarmregeln achten auf Brute-Force-Anmeldungen, die Vergabe einer Administratorrolle, außerhalb der Arbeitszeit
geschriebene Einstellungen, Massenlöschungen, das Abschalten des Audits und eine gebrochene Kette
([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#why-you-want-it)). Standardmäßig geht die Alarm-Mail an die
Administrator-E-Mail-Adresse der Website; prüfen Sie die Empfänger mit:

```bash
php bin/php/console exp:audit alerts recipients --allow-root-user
```

Stellen Sie sicher, dass die Adresse ein Postfach ist, das jemand liest, und kein persönliches, das mit einer Person das
Unternehmen verlässt.

## Einmalig einrichten

Aus [Der Audit-Trail, in zwei Minuten ausprobieren](../../../../eng-us/features/6.0/audit-trail.md#try-it-in-two-minutes):

1. Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` alle paar Minuten läuft. Sie enthält den Teil `audit`, der
   indiziert, zustellt, Alarmregeln auswertet und einmal am Tag rotiert, prüft und archiviert.
2. Prüfen Sie, wer Alarm-Mails bekommt (siehe oben).
3. Sichern Sie `settings/override/audit.ini.append.php`: Die Datei enthält die Signatur- und Pseudonymschlüssel.
   Behandeln Sie sie so vertraulich wie ein Passwort.

Entscheiden Sie auch, was passiert, wenn das Audit nicht schreiben kann, zum Beispiel bei voller Festplatte. Die
mitgelieferte Einstellung `OnWriteFailure` ist `continue`: Die Anfrage läuft weiter, der Fehler wird geloggt, und das
Dashboard warnt, „damit eine volle Festplatte die Website nicht lahmlegt“. `refuse` blockiert stattdessen
sicherheitsrelevante Aktionen, solange das Audit nicht schreiben kann. Wählen Sie `refuse` nur dort, wo eine nicht
aufgezeichnete Änderung schlimmer ist als eine verweigerte
([Audit-Trail, wenn das Audit nicht schreiben kann](../../../../eng-us/features/6.0/audit-trail.md#when-the-audit-cannot-write-onwritefailure)).

## Wer ihn lesen darf

Zwei Richtlinien regeln den Zugang: `audit/read` für den Reiter, die Konsole und die Exporte und `audit/manage` für
Archive, Einstellungen und „Jetzt prüfen“. In einer neuen Installation hat nur die Rolle Administrator sie, und ein
verweigerter Versuch, ihn zu öffnen, wird selbst aufgezeichnet
([Audit-Trail, wer hineinsehen darf](../../../../eng-us/features/6.0/audit-trail.md#who-may-look)). Geben Sie `audit/read` den
Personen, die Störungen und Compliance bearbeiten, und beschränken Sie `audit/manage` auf sehr wenige.

## Checkliste

- [ ] Die Cronjob-Gruppe `frequent` läuft, sodass das Audit indiziert und Alarme ausgewertet werden.
- [ ] `audit.php verify` läuft nach Zeitplan und alarmiert bei einem Fehler.
- [ ] Alarm-Mails erreichen ein gemeinsames Postfach, das jemand liest.
- [ ] `settings/override/audit.ini.append.php` ist im Backup und wird vertraulich behandelt.
- [ ] Die Personen, die Störungen bearbeiten, können den Reiter **Audit** öffnen.
- [ ] Die Wahl für `OnWriteFailure` wurde bewusst getroffen.

## Verwandte Seiten

- [Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md)
- [Störungsbehandlung für kleine Teams](stoerungsbehandlung-fuer-kleine-teams.md)
- [Prüfprotokoll (Hosting)](../../hosting/sicherheit/pruefprotokoll.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
