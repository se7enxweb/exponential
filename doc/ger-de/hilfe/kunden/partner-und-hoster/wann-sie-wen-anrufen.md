---
title: Wann Sie wen anrufen
audience: customer, editor, admin
category: customer-success
order: 11
summary: Eine Nachschlagetabelle für das Team des Betreibers einer Exponential-Website - welche Art von Problem an den Administrator der Website, den Hoster, den Partner, die öffentlichen Issue-Tracker, die vertrauliche Sicherheitsadresse, die Community oder den bezahlten Support von 7x geht.
keywords: wen anrufen, Support, Hoster, Partner, Administrator, Issue-Tracker, Sicherheit, Community, 7x, bezahlter Support, Eskalation, who to call, support, host, partner, administrator, issue tracker, security, community, paid support, escalation
type: help
---
# Wann Sie wen anrufen

Probleme werden schneller gelöst, wenn sie zuerst die richtige Person erreichen. Bewahren Sie diese Tabelle bei Ihren
[Eskalationskontakten](vorlage-eskalationskontakte.md) auf und passen Sie sie an die Aufteilung an, die Sie in
[Wer was macht](wer-was-macht.md) vereinbart haben.

## Die Tabelle

| Problem | Zuerst anrufen | Dann |
|---|---|---|
| Die Website antwortet überhaupt nicht oder zeigt „We'll be right back“ | Hoster | Partner, wenn der Hoster die Ursache in der Website findet ([Wenn die Website nicht erreichbar ist](../gesund-halten/wenn-die-website-nicht-erreichbar-ist.md)) |
| „Something went wrong on our side“ mit einer Referenz `ERR-` | Administrator der Website oder Partner, mit der Referenz | Hoster, wenn sie auf den Server deutet |
| Eine Zertifikatswarnung im Browser | Hoster | |
| E-Mails der Website kommen nicht an (Formulare, Passwort-Zurücksetzungen, Belege) | Hoster | Partner, wenn die E-Mail-Einstellungen der Website die Ursache sind |
| Langsame Website | Hoster | Partner ([Geschwindigkeit und Caching](../gesund-halten/geschwindigkeit-und-caching.md)) |
| Eine Seite sieht falsch aus, ein Problem mit Template oder Design | Partner | |
| Ein Redakteur kann etwas nicht, was er können sollte | Administrator der Website (Rollen und Zugriff) | Partner |
| „Wie mache ich ...?“ in der Administration | Die Hilfe in der Administration, dann der Administrator der Website | Community ([Support erhalten](../../projekt/unterstuetzung-erhalten.md)) |
| Ein vermuteter Einbruch: unbekannte Konten, geänderte Dateien | Hoster, sofort, als Schweregrad 1 | Partner; Ihr Datenschutzbeauftragter, wenn personenbezogene Daten offengelegt sein könnten |
| Eine Sicherheitslücke in Exponential selbst | Vertraulich an security@se7enx.com, meist über Ihren Partner | ([Vertrauliche Sicherheitsmeldungen](../gesund-halten/vertrauliche-sicherheitsmeldungen.md)) |
| Ein reproduzierbarer Fehler in Exponential selbst | Partner oder Administrator, der ihn meldet | Der öffentliche Tracker ([GitHub-Issues](../gesund-halten/github-issues.md)) |
| Sie brauchen garantierte Hilfe, und Ihre Anbieter können sie nicht geben | Bezahlter Support von 7x ([Mit 7x und dem Projekt zusammenarbeiten](mit-7x-und-dem-projekt-zusammenarbeiten.md)) | |

## Bevor Sie anrufen

Halten Sie die Fakten bereit: Seitenadresse, Uhrzeit mit Zeitzone, Fehlerreferenz, was getan wurde, ob alle betroffen
sind, jüngste Änderungen ([Probleme melden](../gesund-halten/probleme-melden.md)). Geben Sie nie ein Passwort am Telefon
oder in einem Ticket weiter.

## Der Schweregrad sagt, wie schnell

Nennen Sie beim Anruf den Schweregrad. Eine gängige Skala ([Support-Stufen](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)):

| Schweregrad | Bedeutung |
|---|---|
| 1 Kritisch | Die Website ist nicht erreichbar oder Daten gehen verloren oder werden offengelegt |
| 2 Hoch | Eine wichtige Funktion fällt aus, keine Umgehung |
| 3 Normal | Eine Funktion fällt aus, eine Umgehung existiert |
| 4 Niedrig | Fragen, Wünsche |

## Wenn niemand antwortet

1. Prüfen Sie die vereinbarte Zeit für die erste Reaktion in Ihrer Support-Vereinbarung
   ([Vorlage für eine Support-Vereinbarung](vorlage-support-vereinbarung.md)).
2. Verwenden Sie den nächsten Schritt Ihrer Eskalationsliste.
3. Notieren Sie die Uhrzeiten in Ihren [Vorfallnotizen](../gesund-halten/vorlage-vorfallnotizen.md); sie zählen für die
   jährliche Überprüfung.
4. Erwägen Sie bei einem Problem des Schweregrads 1 eine Wartungsseite, während Sie warten, wenn jemand sie
   einschalten kann ([Wartungsmodus und geplante Arbeiten](../gesund-halten/wartungsmodus-und-geplante-arbeiten.md)).

## Kostenlose Hilfe hat keine zugesagte Zeit

Hilfe der Community in den Foren und Chats ist kostenlos, und „niemand verspricht eine Antwortzeit“
([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)). Nutzen Sie sie für Fragen, nicht für einen Ausfall.

## Verwandte Seiten

- [Vorlage für Eskalationskontakte](vorlage-eskalationskontakte.md)
- [Probleme melden](../gesund-halten/probleme-melden.md)
- [Support-Stufen (für Hoster)](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
