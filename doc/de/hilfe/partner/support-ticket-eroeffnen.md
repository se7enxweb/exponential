---
title: Ein Support-Ticket als Partner eröffnen
audience: partner
category: partnership
order: 330
summary: Wie Partner Support-Tickets eröffnen, die schnell gelöst werden - was Sie zuerst prüfen, welche Informationen hineingehören, wie Sie die Umgebung sicher ohne Geheimnisse oder Kundendaten beschreiben, eine Ticketvorlage und was nach der Antwort zu tun ist.
keywords: Support-Ticket, GitHub-Issue, Ticket, Vorlage, Umgebung, Logs, nachstellen, Debug, Partner-Support, support ticket, GitHub issue, template, environment, logs, reproduce, debug, partner support
type: help
---
# Ein Support-Ticket als Partner eröffnen

Diese Seite ist für Entwickler und Administratoren in Partnerunternehmen. Der Partner-Support läuft über „GitHub Issue
Tickets“, die je nach Stufe pro Woche oder Monat gezählt werden (<https://exponential.earth/partner/benefits>).
Welches Repository Sie für Partner-Tickets nutzen und ob es privat ist, wird Ihnen bei der Aktivierung mitgeteilt;
wenn nicht, fragen Sie das Partnerteam.

## Bevor Sie ein Ticket eröffnen

- [ ] Durchsuchen Sie die Hilfe (die Taste `/` setzt in der Hilfe den Fokus auf die Suche) und die Foren.
- [ ] Durchsuchen Sie offene und geschlossene Issues von <https://github.com/se7enxweb/exponential/issues>.
- [ ] Stellen Sie das Problem nach Möglichkeit auf einer sauberen, aktuellen Installation nach.
- [ ] Lesen Sie [Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md); dort werden Logs und Debug-Ausgabe erklärt.
- [ ] Entscheiden Sie: Ist es ein Fehler in Exponential (öffentliches Issue), eine Frage zu Ihrem Projekt
      (Partner-Ticket) oder ein Sicherheitsproblem (vertrauliche E-Mail an security@se7enx.com, nie ein Ticket)?

## Was hineingehört

| Angabe | Beispiel | Warum |
|---|---|---|
| Exponential-Version | 6.0.14, oder `main` bei Commit `abc1234` | Die meisten Antworten hängen davon ab |
| PHP-Version und Engine | PHP 8.3, Velocity / PHP-FPM / FrankenPHP | Das Verhalten unterscheidet sich |
| Datenbank | SQLite, PostgreSQL, MariaDB ... | Die Treiber unterscheiden sich |
| Beteiligte Erweiterungen | Namen und Versionen | Viele Probleme liegen in Erweiterungen |
| Was Sie getan haben | Genaue Schritte | Damit es nachgestellt werden kann |
| Was Sie erwartet haben | Ein Satz | Legt fest, was Erfolg ist |
| Was geschehen ist | Fehlertext, Logzeilen | Die Belege |
| Was Sie versucht haben | Cache geleert, Autoloads neu erzeugt ... | Vermeidet wiederholte Ratschläge |
| Dringlichkeit | Produktivbetrieb ausgefallen / beeinträchtigt / Frage | Priorisierung |

## Geheimnisse und Kundendaten heraushalten

Bevor Sie etwas einfügen, entfernen Sie Passwörter, Schlüssel, Hostnamen von Datenbanken, Kundennamen, E-Mail-Adressen
und private URLs. Ersetzen Sie sie durch Platzhalter wie `<customer-site>`. Genau aus diesem Grund erstellt der
Fehlerbericht-Helfer der Hilfe eine sichere Zusammenfassung der Umgebung ([Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md)).

## Vorlage

```text
Title: <short description> on Exponential <version>

Partner: <company> (<level>)          Urgency: <down / degraded / question>
Environment: Exponential <version>, PHP <x.y> (<engine>), <database>, extensions: <list>

Steps to reproduce:
1. ...
2. ...
Expected: ...
Actual: ...  (error text / log lines, secrets removed)
Tried so far: ...
```

## Nach der Antwort

- Bestätigen Sie, wann und wie es gelöst ist, damit das Ticket anderen hilft.
- War die Ursache ein Fehler in Exponential, sorgen Sie dafür, dass es ein öffentliches Issue oder einen Pull Request
  gibt ([Fehler gut melden](fehler-gut-melden.md)).
- Erfassen Sie gelöste Fehlerberichte als Aktivität ([Ihre Partneraktivität erfassen](partneraktivitaet-erfassen.md)).

## Verwandte Seiten

- [Support-Stufen und Reaktionszeiten](support-stufen-und-reaktionszeiten.md)
- [Eskalationen und Sicherheitsprobleme](eskalationen-und-sicherheitsprobleme.md)
- [Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
