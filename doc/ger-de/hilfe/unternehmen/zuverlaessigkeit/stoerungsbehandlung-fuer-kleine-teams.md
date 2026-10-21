---
title: Störungsbehandlung für kleine Teams
audience: businesses, admin, project-manager, editor
category: business-solutions
order: 200
summary: Ein Plan für den Tag, an dem die Website ausfällt, der zu einem Team aus zwei oder drei Personen passt, mit Rollen, den ersten fünfzehn Minuten, der Kommunikation mit Kunden, dem Platz der Werkzeuge von Exponential, den zu befolgenden Runbooks und der Nachbesprechung.
keywords: Störungsbehandlung, Ausfall, Website nicht erreichbar, Runbook, Bereitschaft, Kommunikation, Statusmeldung, Nachbesprechung, kleines Team, Sicherheitsvorfall, Eskalation, incident response, outage, site down, runbook, on call, communication, status message, post-incident review, small team, security incident, escalation
type: help
---
# Störungsbehandlung für kleine Teams

Diese Seite richtet sich an kleine Unternehmen, Geschäfte, Bibliotheken und gemeinnützige Organisationen, in denen „das
IT-Team“ aus ein oder zwei Personen und dem Inhaber besteht. Sie gibt einen Plan für den schlechten Tag, der zu dieser
Größe passt: wer was tut, die ersten fünfzehn Minuten, wie Sie mit Kunden sprechen, welche Werkzeuge und Runbooks von
Exponential Sie nutzen und wie Sie danach daraus lernen. Die technischen Runbooks stehen unter
[Runbooks für Hoster: Überblick](../../hosting/runbooks/README.md).

## Inhalt

1. [An einem ruhigen Tag vorbereiten](#an-einem-ruhigen-tag-vorbereiten)
2. [Drei Rollen, auch für zwei Personen](#drei-rollen-auch-für-zwei-personen)
3. [Die ersten fünfzehn Minuten](#die-ersten-fünfzehn-minuten)
4. [Welches Runbook](#welches-runbook)
5. [Mit Kunden sprechen](#mit-kunden-sprechen)
6. [Sicherheitsvorfälle sind anders](#sicherheitsvorfälle-sind-anders)
7. [Die Störung abschließen](#die-störung-abschließen)
8. [Die Nachbesprechung](#die-nachbesprechung)

## An einem ruhigen Tag vorbereiten

Ein Störungsplan wird vor der Störung geschrieben. Halten Sie eine Seite bereit, ausgedruckt und dort aufbewahrt, wo das
Team sie ohne die Website findet:

- [ ] Wer zuerst angerufen wird und wer die Vertretung ist, mit Telefonnummern.
- [ ] Wie Sie den Support des Hosting-Partners erreichen und was Ihr Vertrag über Reaktionszeiten sagt
      ([Grundlagen zu Verträgen und SLAs](../auswahl/grundlagen-zu-vertraegen-und-slas.md)).
- [ ] Wo die Backups liegen und wer sie wiederherstellen kann
      ([Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md)).
- [ ] Wie Sie sich am Server und an der Administration anmelden, in einem Passwortmanager aufbewahrt, nie auf der
      ausgedruckten Seite.
- [ ] Wo Kunden informiert werden: soziale Medien, eine Telefonansage, ein Mailversand.
- [ ] Die Links zu den Runbooks unten.

Proben Sie einmal im Quartal: Tun Sie so, als sei die Website ausgefallen, und gehen Sie diese Seite gemeinsam durch
([Verfügbarkeit in der Praxis](verfuegbarkeit-in-der-praxis.md#jedes-quartal)).

## Drei Rollen, auch für zwei Personen

| Rolle | Tut | Tut nicht |
|---|---|---|
| **Leitung** | Entscheidet: reparieren, zurückrollen, Wartungsseite, den Hoster anrufen. Behält die Zeit im Blick | Befehle tippen, während sie entscheidet |
| **Behebung** | Untersucht und repariert nach dem Runbook | Mit Kunden sprechen |
| **Kommunikation** | Sagt Kunden, Mitarbeitern und dem Inhaber, was passiert und wann die nächste Meldung kommt | Zeiten versprechen, die die Behebung nicht genannt hat |

Bei zwei Personen übernimmt die Leitung auch die Kommunikation. Bei einer Person führen Sie die Schritte der Reihe nach
aus und notieren die Uhrzeit jedes Schritts.

## Die ersten fünfzehn Minuten

1. **Notieren Sie die Uhrzeit**, zu der das Problem bemerkt wurde, und wie.
2. **Sehen Sie selbst nach**: Öffnen Sie die Website und die Administration. Notieren Sie, was die Seite zeigt, und den
   Status ([Runbook: Website nicht erreichbar, die ersten fünf Minuten](../../hosting/runbooks/website-nicht-erreichbar.md#die-ersten-fünf-minuten)).
3. **Wurde etwas geändert?** „Fehler, die direkt nach einem Deployment oder einem Update begonnen haben: Die Ursache
   liegt fast immer in dieser Änderung“
   ([Runbook: Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md#welche-seite-sieht-der-besucher)). Der Kanal
   `system` des Audit-Trails zeigt jüngste Deployments, geschriebene Einstellungen und Wartung
   ([Audit-Trail für den Betrieb](audit-trail-fuer-den-betrieb.md#nutzung-nach-einer-störung)).
4. **Lesen Sie die neuesten Fehler**: `php bin/php/console exp:logs --tail=var/log/error.log --lines=50` oder
   **Setup > Logdateien**, wenn die Administration funktioniert. Hat ein Kunde eine Referenz `ERR-` geschickt, suchen Sie
   danach ([Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md)).
5. **Prüfen Sie den Rechner**: Plattenplatz, Arbeitsspeicher, Last. „Eine volle Festplatte verursacht die meisten
   Symptome“ einer nicht erreichbaren Website (dasselbe Runbook).
6. **Entscheiden Sie**: eine schnelle Reparatur, das Zurückrollen der letzten Änderung oder eine Wartungsseite, während
   Sie arbeiten.

Wenn Kunden auf Fehler stoßen und die Reparatur länger als ein paar Minuten dauert, stellen Sie eine ehrliche
Wartungsseite ein ([Wartungsmodus und geplante Arbeiten, Notfälle](wartungsmodus-und-geplante-arbeiten.md#notfälle)):

```bash
php bin/php/maintenance.php on --message="We are fixing a problem. Back shortly." --allow-admin --allow-root-user
```

## Welches Runbook

Der Hosting-Bereich von Exponential hat Schritt-für-Schritt-Runbooks
([Runbooks für Hoster: Überblick](../../hosting/runbooks/README.md)):

| Symptom | Runbook |
|---|---|
| Die Website antwortet nicht oder zeigt eine Fehlerseite | [Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md) |
| Die Website antwortet, aber langsam | [Langsame Website](../../hosting/runbooks/langsame-website.md) |
| Die Festplatte ist voll | [Festplatte voll](../../hosting/runbooks/festplatte-voll.md) |
| Velocity startet nicht | [Velocity startet nicht](../../hosting/runbooks/velocity-startet-nicht.md) |
| HTTPS-Warnungen im Browser | [Ablauf von Zertifikaten](../../hosting/runbooks/ablauf-von-zertifikaten.md) |
| Daten müssen aus einem Backup zurückkommen | [Aus einem Backup wiederherstellen](../../hosting/runbooks/aus-einem-backup-wiederherstellen.md) |
| Ein PHP-Upgrade ist schiefgegangen | [PHP-Upgrade](../../hosting/runbooks/php-upgrade.md) |
| Anzeichen eines Einbruchs | [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md) |

Das Runbook zur nicht erreichbaren Website ordnet dem, was Kunden sehen, die Ursache zu. Zum Beispiel ist
„Something went wrong on our side“ mit einer Referenz `ERR-` ein nicht abgefangener Fehler, den Sie über seine Referenz
nachschlagen; „Down for maintenance“ bedeutet, dass der Wartungsmodus eingeschaltet ist; „The site is missing the
software libraries it needs“ bedeutet, dass `vendor/` fehlt
([Runbook: Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md#welche-seite-sieht-der-besucher)).

## Mit Kunden sprechen

- **Früh und ehrlich**: „Unser Shop ist derzeit nicht erreichbar. Wir arbeiten daran und melden uns bis 14:30 Uhr
  wieder.“
- **Ein Zeitpunkt für die nächste Meldung**, auch wenn es noch nichts Neues gibt, und halten Sie ihn ein.
- **Nicht nur auf der defekten Website**: Nutzen Sie die Kanäle aus Ihrem Plan.
- **Keine technischen Einzelheiten und keine Schuldzuweisungen**: Kunden wollen wissen, wann sie wieder bestellen
  können.
- **Wenn es vorbei ist**: Sagen Sie es, und wenn Bestellungen oder Nachrichten verloren gegangen sein könnten, sagen Sie,
  was Kunden tun sollten.

Mitarbeiter, die Telefon oder E-Mail beantworten, brauchen dieselbe Botschaft, damit sie dasselbe sagen.

## Sicherheitsvorfälle sind anders

Wenn Sie einen Einbruch vermuten (unbekannte Administratorkonten, veränderte Dateien, Seiten, die Sie nicht
veröffentlicht haben), ändert sich die Priorität von „schnell wieder online“ zu „eindämmen und Beweise sichern“. Folgen
Sie dem Runbook [Kompromittierte Website](../../hosting/runbooks/kompromittierte-website.md): eindämmen, Beweise sichern,
untersuchen, beseitigen und neu aufbauen, wiederherstellen, berichten und benachrichtigen.

Die Werkzeuge von Exponential helfen dabei:

- Der Audit-Trail zeigt Anmeldungen, Rollenzuweisungen und Änderungen an Einstellungen, und `audit.php verify` zeigt, ob
  seine Aufzeichnung manipuliert wurde
  ([Audit-Trail für den Betrieb](audit-trail-fuer-den-betrieb.md#der-nachweis-dass-die-aufzeichnung-nicht-verändert-wurde)).
- Die Upgrade-Prüfung zeigt Release-Dateien, die verändert wurden
  ([Upgrade-Prüfung und Dateikonsistenz](upgrade-pruefung-und-dateikonsistenz.md)).
- `security.log` listet Anfragen auf, die nach Sondierungen oder Angriffen aussahen
  ([Logs und Fehlerreferenzen](logs-und-fehlerreferenzen.md#die-logs-und-wofür-jedes-da-ist)).

Liegt die Ursache in einem Sicherheitsproblem von Exponential selbst, melden Sie es vertraulich wie in
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) beschrieben, nie in einem öffentlichen
Tracker. Verletzungen des Schutzes personenbezogener Daten können gesetzliche Meldepflichten auslösen; siehe
[Sicherheitsgrundlagen für Unternehmen](../sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md) und fragen Sie Ihren
Datenschutzbeauftragten oder Ihren Anwalt. Diese Seite ist keine Rechtsberatung.

## Die Störung abschließen

Aus [Runbook: Website nicht erreichbar, nach der Störung](../../hosting/runbooks/website-nicht-erreichbar.md#nach-dem-vorfall):

- [ ] Leeren Sie die Caches, die die Reparatur braucht, und mit Velocity nach jedem Neustart dessen Antwortcache.
- [ ] Prüfen Sie: Startseite, Administration, eine Anmeldung, eine Veröffentlichung; bei einem Shop eine Testbestellung
      bis zur Zahlung.
- [ ] Schalten Sie die Wartung aus, falls sie eingeschaltet war.
- [ ] Sagen Sie Kunden und Mitarbeitern, dass es vorbei ist.
- [ ] Schreiben Sie die Störungsnotiz: Beginn, Erkennung, Ursache, Reparatur, Ende, was eine Wiederholung verhindert.
- [ ] Ist die Ursache ein Fehler in Exponential, melden Sie ihn
      ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden)).

## Die Nachbesprechung

Nehmen Sie sich innerhalb einer Woche gemeinsam eine halbe Stunde für vier Fragen, ohne Schuldzuweisungen:

1. Wie haben wir es erfahren, und hätten wir es früher erfahren können? (Monitoring, Alarme.)
2. Was hat uns aufgehalten? (Zugänge, Wissen, fehlende Schritte im Runbook.)
3. Was hätte es verhindert? (Eine Prüfung, eine Probe, eine Regel für Änderungen.)
4. Was ändern wir, wer tut es, bis wann?

Tragen Sie die Maßnahmen in Ihren [Bewertungsbogen Zuverlässigkeit](bewertungsbogen-zuverlaessigkeit.md) und in das Risikoregister
ein ([Risikomanagement für Exponential-Projekte](../../fuehrung/risikomanagement.md)). Die Kosten der Störung, in Ihren
eigenen Zahlen, gehören in das [Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md).

## Verwandte Seiten

- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Health-Checks und Monitoring](health-checks-und-monitoring.md)
- [Wiederherstellungsübungen](wiederherstellungsuebungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
