---
title: Probleme melden
audience: customer, editor, admin
category: customer-success
order: 17
summary: Wie das Team eines Website-Betreibers ein Problem mit einer Exponential-Website so meldet, dass es behoben wird - ein einziger Kontakt, die zu sammelnden Fakten, wohin welche Art von Problem geht (Ihr Administrator, Hoster, Partner, die öffentlichen Tracker oder die vertrauliche Sicherheitsadresse) und was nie verschickt wird.
keywords: Problem melden, Fehler, Support, Fehlerreferenz, Schweregrad, Hoster, Partner, Issue-Tracker, Sicherheit, was hineingehört, report a problem, bug, support, error reference, severity, host, partner, issue tracker, security, what to include
type: help
---
# Probleme melden

Ein gut gemeldetes Problem ist halb gelöst. Diese Seite legt fest, wie Ihr Team Probleme meldet: wer sie sammelt,
welche Fakten verschickt werden und wohin welche Art von Problem geht. Die Schritt-für-Schritt-Fassung für Redakteure
ist [Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md).

## Zuerst: Ist es ein Fehler?

Manche Seiten sagen nur, dass etwas nicht erlaubt oder nicht vorhanden ist, zum Beispiel „Your account does not have
the proper privileges“, oder eine ausgegraute Schaltfläche. Das sind Fragen des Zugriffs, keine Fehler: Fragen Sie den
Administrator Ihrer Website ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)).

## Ein Kontakt für das Team

Vereinbaren Sie eine Person (und eine Vertretung), die Probleme von Redaktion und Kunden sammelt und weitergibt. Das
verhindert, dass dasselbe Problem dreimal gemeldet wird, und gibt Ihrem Anbieter eine Person, der er antworten kann.

## Die zu sammelnden Fakten

| Fakt | Beispiel |
|---|---|
| Die Seitenadresse | Aus der Adressleiste des Browsers kopiert |
| Die Uhrzeit, mit Zeitzone | „14:05 MEZ“ |
| Die Fehlerreferenz, falls angezeigt | `ERR-0123456789` |
| Was getan wurde, Schritt für Schritt | „Artikel geöffnet, Bearbeiten gedrückt, eine ZIP-Datei hochgeladen“ |
| Was erwartet wurde, und was geschah | Die genaue Meldung |
| Alle, oder ein Konto? | „Nur mein Konto“, „jeder Redakteur“ |
| Jüngste Änderungen | Ein Update, eine neue Erweiterung, eine Einstellungsänderung |

Das ist die Liste, die das Support-Personal eines Hosters sammeln soll ([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md));
wenn Sie sie gleich mitschicken, spart das eine Rückfrage. Ein Screenshot hilft, „solange er keine Passwörter und keine
personenbezogenen Daten anderer zeigt“ ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)).

## Wie dringend ist es?

Eine gängige Skala für den Schweregrad aus dem Bereich für Hosting ([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md)):

| Schweregrad | Definition | Beispiel |
|---|---|---|
| 1 Kritisch | Die Website ist nicht erreichbar oder Daten gehen verloren oder werden offengelegt | Jede Seite liefert einen Fehler; ein vermuteter Einbruch |
| 2 Hoch | Eine wichtige Funktion fällt aus, keine Umgehung | Die Redaktion kann nicht veröffentlichen; die Anmeldung schlägt fehl |
| 3 Normal | Eine Funktion fällt aus, eine Umgehung existiert | Eine Ansicht wird falsch dargestellt |
| 4 Niedrig | Fragen, Wünsche | „Können wir eine Sprache hinzufügen?“ |

Verwenden Sie die Skala, die Ihr Vertrag festlegt, falls er eine hat; andernfalls vereinbaren Sie diese.

## Wohin es geht

| Art des Problems | Geht an |
|---|---|
| Zugriff, Rollen, „Wie mache ich ...“ | Den Administrator Ihrer Website und die Hilfe in der Administration |
| Der Server, die Website ist nicht erreichbar, E-Mail, Zertifikate | Ihren Hoster ([Wann Sie wen anrufen](../partner-und-hoster/wann-sie-wen-anrufen.md)) |
| Ihre Templates, Designs, eigene Erweiterungen | Ihren Partner oder Entwickler |
| Ein reproduzierbarer Fehler in Exponential selbst | Den öffentlichen Issue-Tracker, meist von Ihrem Administrator oder Partner gemeldet ([GitHub-Issues](github-issues.md), [Die Meldeseite der Hilfe](die-meldeseite-der-hilfe.md)) |
| Ein Sicherheitsproblem in der Software | Vertraulich an security@se7enx.com, nie öffentlich ([Vertrauliche Sicherheitsmeldungen](vertrauliche-sicherheitsmeldungen.md)) |
| Fragen zum Projekt | Die Orte der Community ([Support erhalten](../../../../en/us/help/project/getting-support.md)) |

## Nie verschicken

„Schreiben Sie nie Passwörter, Schlüssel, Sitzungs-Cookies, Datenbankadressen oder die personenbezogenen Daten von
Kunden oder Benutzern in ein öffentliches Issue“ ([Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md)).
Dieselbe Regel ist für Tickets und E-Mails an Ihre Anbieter klug: Ein Anbieter sollte nie nach einem Passwort fragen;
„Verwenden Sie ein Wegwerf-Testkonto, wenn Sie etwas im angemeldeten Zustand nachstellen müssen“ ([Support-Stufen](../../../../en/us/help/hosting/business/support-tiers.md)).

## Eine Meldung zum Kopieren

```text
Subject: [Severity 1-4] <short description>
Page:            <address>
Time:            <date, time, time zone>
Error reference: <ERR-... or none>
Who is affected: <everyone / one account / editors only>
Steps:           1. ...  2. ...  3. ...
Expected:        <what should happen>
Happened:        <what happened, exact message>
Recent changes:  <update, new extension, settings, none known>
Reported by:     <name, role, how to reach you>
```

## Verwandte Seiten

- [Wenn die Website nicht erreichbar ist](wenn-die-website-nicht-erreichbar-ist.md)
- [Logs für Betreiber](logs-fuer-betreiber.md)
- [Logs und Fehlerreferenzen (für Unternehmen)](../../../../en/us/help/businesses/reliability/logs-and-error-references.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
