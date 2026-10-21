---
title: Wer was macht
audience: customer, partner, hosting
category: customer-success
order: 2
summary: Eine Tabelle der Verantwortlichkeiten für eine Exponential-Website, die sich das Team des Betreibers, der Hoster, der Partner und das Projekt teilen - Updates, Backups, Sicherheit, Inhalte, eigener Code, Überwachung und Vorfälle - einmal zu vereinbaren und in jeden Vertrag aufzunehmen.
keywords: Verantwortlichkeiten, RACI, Rollen, Hoster, Partner, Betreiber, Projekt, Hosting-Support, Anwendungs-Support, Verantwortungsgrenze, responsibilities, roles, host, partner, owner, project, hosting support, application support, line of responsibility
type: help
---
# Wer was macht

„Die meiste Reibung im Hosting-Support entsteht durch eine unklare Grenze“ ([Support-Stufen](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)).
Diese Seite gibt Ihnen eine Tabelle, die Sie einmal mit Ihrem Hoster und Partner vereinbaren und in jeden Vertrag
aufnehmen. Passen Sie sie an Ihre Vereinbarung an; wichtig ist, dass jede Zeile einen Namen hat.

## Die vier Parteien

| Partei | Typischerweise |
|---|---|
| Ihr Team | Verantwortet die Website, die Inhalte und die Entscheidungen; verwaltet Redakteure und Konten |
| Der Hoster | Betreibt den Server, PHP, die Engine, den Datenbankserver, Backups und Zertifikate |
| Der Partner oder Entwickler | Baut und ändert Templates, Designs, Erweiterungen und das Inhaltsmodell |
| Das Projekt und 7x | Machen Exponential selbst, beheben Fehler in der Software, veröffentlichen Releases ([Mit 7x und dem Projekt zusammenarbeiten](mit-7x-und-dem-projekt-zusammenarbeiten.md)) |

## Die Grenze zwischen Hosting und Anwendung

Aus [Support-Stufen](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md):

| Hosting-Support | Anwendungs-Support |
|---|---|
| Der Server, PHP, die Engine, der Datenbankserver | Inhaltsklassen, Templates, Design |
| Backups und Wiederherstellungen | Die eigenen Erweiterungen und der eigene Code der Website |
| TLS-Zertifikate | Redaktionelle Fragen („Wie veröffentliche ich?“) |
| E-Mail-Zustellung vom Server | Datenkorrekturen innerhalb der Inhalte |
| Updates von Exponential einspielen, wo vereinbart | Upgrades, die Codeänderungen brauchen |

Manche Hoster bieten beides in einem Vertrag an; viele bieten nur die linke Spalte.

## Eine Tabelle der Verantwortlichkeiten zum Ausfüllen

| Aufgabe | Ihr Team | Hoster | Partner | Hinweise |
|---|---|---|---|---|
| Entscheiden, wann aktualisiert wird | ✓ | | | |
| Updates von Exponential einspielen | | | | Hoster oder Partner, wie vereinbart |
| Updates für PHP und Betriebssystem | | ✓ | | |
| Backups von Datenbank und Dateien | | ✓ | | |
| Wiederherstellungstests und ihr Protokoll | | ✓ | | Ergebnis an Sie berichtet ([Wiederherstellungstest](../gesund-halten/wiederherstellungstest.md)) |
| Überwachung und Alarme | | ✓ | | |
| TLS-Zertifikate und DNS | | | | Wer die Domain hält |
| Sicherheitseinstellungen der Website | | | ✓ | |
| Administratorkonten und Rollen | ✓ | | | |
| Templates, Designs, Erweiterungen | | | ✓ | |
| Inhalte, Übersetzungen, Medien | ✓ | | | |
| Fehler in Exponential beim Projekt melden | | | ✓ | ([GitHub-Issues](../gesund-halten/github-issues.md)) |
| Vertrauliche Sicherheitsmeldungen | | | | Wer es findet ([Vertrauliche Sicherheitsmeldungen](../gesund-halten/vertrauliche-sicherheitsmeldungen.md)) |
| Leitung im Vorfall | ✓ | | | Entscheidet; Anbieter beheben |
| Redakteure schulen | | | ✓ | |

Die Häkchen sind ein Beispiel für eine gängige Aufteilung: Ersetzen Sie sie durch Ihre eigene. Wenn ein Unternehmen
zugleich Hoster und Partner ist, fassen Sie die beiden Spalten zusammen, behalten aber die Zeilen.

## Die Fassung für Projektleitungen

Der Bereich für Projektleitungen hat eine RACI-Matrix (verantwortlich, rechenschaftspflichtig, konsultiert, informiert)
für ein ganzes Projekt ([Projektvorlagen](../../fuehrung/projektvorlagen.md)). Verwenden Sie sie, während ein Aufbau
oder eine Migration läuft; die obige Tabelle ist für die Jahre des Betriebs danach.

## Aktuell halten

- [ ] Die Tabelle ist jedem Vertrag beigefügt.
- [ ] Jede Zeile hat einen Namen, nicht „beide“.
- [ ] Sie wird überprüft, wenn ein Anbieter wechselt, und einmal im Jahr
      ([Ihren Anbieter jedes Jahr überprüfen](anbieter-jedes-jahr-ueberpruefen.md)).
- [ ] Ihre [Eskalationskontakte](vorlage-eskalationskontakte.md) stimmen damit überein.

## Verwandte Seiten

- [Wann Sie wen anrufen](wann-sie-wen-anrufen.md)
- [Verträge und SLAs](vertraege-und-slas.md)
- [Sicherheitsgrundlagen](../gesund-halten/sicherheitsgrundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
