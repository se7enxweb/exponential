---
title: Hosting auswählen
audience: businesses, project-manager, website-creator, hosting
category: business-solutions
order: 10
summary: Wie ein Unternehmen das Hosting für eine Exponential-Website auswählt - was die Website braucht (PHP, Datenbank, Webserver oder Velocity, Cron, Mail, Backups), die fünf Fragen, die entscheiden, was Sie einen Hoster fragen, und ein Entscheidungsprotokoll zum Aufbewahren.
keywords: Hosting auswählen, Hosting-Anbieter, Anforderungen, PHP, SQLite, PostgreSQL, Velocity, Apache, nginx, Shared Hosting, VPS, Managed Hosting, Cron, Mail, Backups, Entscheidungsprotokoll, choosing hosting, hosting provider, requirements, shared hosting, managed hosting, cron, mail, backups, decision record
type: help
---
# Hosting auswählen

Diese Seite ist für Inhaber und Führungskräfte, die einen Hoster für eine Exponential-Website wählen müssen, und für
den Projektleiter, der die Entscheidung vorbereitet. Sie fasst zusammen, was die Website braucht, die wenigen Fragen,
die den Großteil der Wahl entscheiden, und was Sie einen Hoster fragen sollten, bevor Sie unterschreiben. Die
ausführliche Fassung für Projektleiter ist
[Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md).

## Inhalt

1. [Was die Website braucht](#was-die-website-braucht)
2. [Die fünf Fragen, die entscheiden](#die-fünf-fragen-die-entscheiden)
3. [Worauf Sie über die Technik hinaus achten sollten](#worauf-sie-über-die-technik-hinaus-achten-sollten)
4. [Fragen, die Sie einem Hoster schicken](#fragen-die-sie-einem-hoster-schicken)
5. [Ein Entscheidungsprotokoll](#ein-entscheidungsprotokoll)

## Was die Website braucht

Aus [Hosting-Anforderungen für eine Website](../../../../eng-us/help/creators/website-creators/hosting-for-a-website.md#the-checklist)
und `README.md`, „Requirements“:

| Bedarf | Minimum | Hinweise |
|---|---|---|
| PHP | 8.0 | empfohlen ist das neueste 8.x, das das System bietet; Velocity läuft auch auf 8.0 (ab Release 0.0.4.54) |
| Datenbank | SQLite 3, kein Server | in der offiziellen Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Webserver | keiner, wenn Velocity die Website ausliefert | oder Apache, nginx, LiteSpeed, Caddy, IIS und andere |
| Composer | 2.x | installiert die Software und ihre Erweiterungen |
| Cron | ein Job alle paar Minuten | führt Benachrichtigungen, Aufräumarbeiten und die Arbeit des Prüfprotokolls aus |
| Mail | ausgehende Mail | Formulare, Belege, Zurücksetzen von Passwörtern, Newsletter |
| HTTPS | jede öffentliche Website | Velocity kann Zertifikate selbst beziehen |
| Backups | Datenbank, hochgeladene Dateien, Einstellungen, Schlüssel, Prüfprotokoll | [Backups: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) |

## Die fünf Fragen, die entscheiden

Aus [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md#die-entscheidung-in-fünf-fragen):

| Frage | Wenn die Antwort ... lautet | Dann erwägen Sie |
|---|---|---|
| Muss mehr als eine Maschine in die Datenbank schreiben? | ja | einen Datenbankserver, an erster Stelle PostgreSQL; nicht SQLite |
| Erlaubt das Hosting langlaufende Prozesse? | nein (typisches Shared Hosting) | Apache oder nginx mit PHP-FPM; Velocity meist nicht |
| Hat die Maschine PHP 8.0? | ja | jeden Webserver; Velocity läuft ab seinem Release 0.0.4.54 auf 8.0 |
| Hat sich die Organisation auf eine Datenbank festgelegt? | ja | diese Datenbank, wenn sie zu den sechs gehört |
| Gibt es Shell-Zugang auf dem Server? | nein | den Einrichtungsassistenten im Browser |

Für die meisten Websites kleiner und mittlerer Unternehmen ist die einfachste Antwort eine Maschine mit SQLite und
Velocity ([Velocity für Unternehmen](../zuverlaessigkeit/velocity-fuer-unternehmen.md)). Fügen Sie einen Datenbankserver und
weitere Maschinen hinzu, wenn Messungen den Bedarf zeigen ([Kostentreiber beim Hosting](../kosten/kostentreiber-beim-hosting.md)).

> **Tipp**
> Die richtige Frage ist nicht nur „Welche Technik ist die beste?“, sondern „Wer kann sie betreiben, sichern und
> wiederherstellen?“. Schreiben Sie den Namen dieser Person in das Entscheidungsprotokoll.

## Worauf Sie über die Technik hinaus achten sollten

| Thema | Was Sie prüfen |
|---|---|
| Wer was tut | Aktualisiert der Hoster PHP, Exponential, Erweiterungen? Wer stellt wieder her? |
| Backups | Wie oft, wie lange aufbewahrt, wo, und wurde eine Wiederherstellung getestet? |
| Monitoring | Überwacht der Hoster die Website von außen, und wer wird benachrichtigt? |
| Sicherheit | Trennung von anderen Kunden, TLS, Zugang zur Administration ([Sicherheitsgrundlagen für Unternehmen](../sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)) |
| Datenschutz | Wo die Server stehen, Verträge zur Verarbeitung personenbezogener Daten ([DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md)) |
| Support | Reaktionszeiten schriftlich ([Grundlagen zu Verträgen und SLAs](grundlagen-zu-vertraegen-und-slas.md)) |
| Ausstieg | Wie Sie Ihre Daten und Dateien bekommen, wenn Sie gehen, und in welchem Format |
| Erfahrung | Hat der Hoster schon Exponential-Websites betrieben? Hosting-Partner werden vom Projekt aufgeführt ([Hosting-Partner](../../partner/hosting-partner.md)) |

## Fragen, die Sie einem Hoster schicken

Kopieren und abschicken; mehr in [Fragen an einen Partner](fragen-an-einen-partner.md):

- [ ] Welche PHP-Versionen bieten Sie an, und kann ich pro Website das neueste 8.x wählen?
- [ ] Darf ich einen langlaufenden Prozess für Exponential Velocity betreiben, oder nur PHP-FPM hinter Ihrem Webserver?
- [ ] Unterstützen Sie SQLite für den Produktivbetrieb auf einer Maschine, und welche Datenbankserver bieten Sie an?
- [ ] Kann ich einen Cronjob alle paar Minuten einplanen?
- [ ] Wie wird ausgehende Mail versendet, und ist DKIM-Signierung verfügbar?
- [ ] Was umfassen die Backups, wie lange werden sie aufbewahrt, und wann haben Sie zuletzt eine Wiederherstellung
      getestet?
- [ ] Wer spielt Sicherheitsupdates für PHP, das Betriebssystem und Exponential ein, und wie schnell?
- [ ] Zu welchen Reaktionszeiten verpflichten Sie sich schriftlich?
- [ ] Wie bekomme ich alle meine Daten und Dateien, wenn ich gehe?

Die vollständige technische Liste für Hoster steht in
[Hosting-Anforderungen für eine Website: Fragen](../../../../eng-us/help/creators/website-creators/hosting-for-a-website.md#questions-for-a-hosting-provider).

## Ein Entscheidungsprotokoll

```text
HOSTING DECISION RECORD
Site: ____________________   Date: __________   Decided by: ______________
Host chosen: ______________   Hosting model: shared / VPS / dedicated / managed / containers
PHP version: _____   Engine: Velocity / FrankenPHP / Apache + PHP-FPM / nginx + PHP-FPM
Database: SQLite 3 / PostgreSQL / MongoDB / MariaDB / Oracle / MySQL
Reason for the choice: ______________________________________________
Backups: frequency ______  retention ______  last restore test ______
Person who restores: ______________
Updates applied by: ______________   within: ______________
Exit: data format and time agreed: ______________
Review date: __________
```

Die Seite für Führungskräfte hat ein ausführlicheres Protokoll:
[Ein Entscheidungsprotokoll zum Kopieren](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md#ein-entscheidungsprotokoll-zum-kopieren).

## Verwandte Seiten

- [Hosting-Modelle im Vergleich](hosting-modelle-im-vergleich.md)
- [Kostentreiber beim Hosting](../kosten/kostentreiber-beim-hosting.md)
- [Ein Hosting-Modell wählen (für Hoster)](../../hosting/modelle/ein-hosting-modell-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
