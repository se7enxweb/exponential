---
title: Hosting für ein kleines Unternehmen wählen
audience: local-business, businesses, customer
category: local-business
order: 15
summary: Hosting-Möglichkeiten für die Website eines kleinen Unternehmens mit Exponential in einfachen Worten - Shared Hosting, ein verwalteter Hoster, ein kleiner virtueller Server oder die Server des Partners -, was jede für Sie bedeutet, was Exponential von einem Server braucht und welche Fragen Sie vor der Unterschrift stellen.
keywords: Hosting, Webhosting, kleines Unternehmen, verwaltetes Hosting, VPS, Shared Hosting, SQLite, PHP, HTTPS, Sicherungen, Anforderungen, Hoster, hosting, web hosting, small business, managed hosting, shared hosting, backups, requirements, host
type: help
---
# Hosting für ein kleines Unternehmen wählen

Diese Seite ist für Inhaber eines kleinen Unternehmens, die entscheiden müssen, wo die Website läuft. Die kurze
Antwort: Wählen Sie die Möglichkeit, bei der jemand, dem Sie vertrauen, für Updates, Sicherungen und HTTPS
verantwortlich ist und bei der Sie diese Person erreichen.

## Inhalt

1. [Was Exponential braucht](#was-exponential-braucht)
2. [Die Möglichkeiten](#die-möglichkeiten)
3. [Fragen an einen Hoster](#fragen-an-einen-hoster)
4. [Domain, E-Mail und die Website](#domain-e-mail-und-die-website)
5. [Checkliste](#checkliste)

## Was Exponential braucht

Aus [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md):

- PHP 7.4 oder neuer mit einigen üblichen Erweiterungen;
- eine Datenbank: SQLite braucht keinen Datenbankserver und reicht für eine Website auf einem Rechner; MySQL, MariaDB
  und PostgreSQL werden ebenfalls unterstützt ([Datenbankunterstützung](../../datenbankunterstuetzung.md));
- einen Webserver wie Apache oder Nginx oder Exponential Velocity, den Anwendungsserver, der mit Exponential
  ausgeliefert wird ([Velocity ist Teil von Exponential 6.0.15](../../velocity-ist-teil-von-exponential-6-0-15.md));
- Zugang zur Kommandozeile, um Cronjobs (Sitemaps, Newsletter, Aufräumen) und Updates auszuführen;
- beschreibbare Verzeichnisse für Caches und hochgeladene Dateien
  ([Beschreibbare Verzeichnisse und Berechtigungen](../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).

Beim Zugang zur Kommandozeile reicht billiges Shared Hosting oft nicht aus. Fragen Sie zuerst danach.

## Die Möglichkeiten

| Möglichkeit | Sie bekommen | Sie müssen weiterhin | Passt zu |
|---|---|---|---|
| Die Server Ihres Partners | Aufbau, Hosting und Updates von einem Anbieter | die Vereinbarung lesen; die Domain auf Ihren Namen halten | den meisten lokalen Unternehmen |
| Ein Hoster, der Exponential für Kunden betreibt | eine verwaltete Installation, Sicherungen, HTTPS | Inhalte bearbeiten; Reaktionszeiten vereinbaren | Unternehmen ohne Partner |
| Ein kleiner virtueller Server | volle Kontrolle, niedrige laufende Kosten | Betriebssystem, PHP und Exponential aktualisieren; Sicherungen prüfen | einem Unternehmen mit einer technischen Person |
| Shared Hosting | niedrige Kosten | vorher PHP-Version, Cron und Kommandozeile prüfen | kleinen Websites ohne Shop, mit einer technischen Person |

Mehr Einzelheiten: [Hosting-Modelle im Vergleich](../../unternehmen/auswahl/hosting-modelle-im-vergleich.md) und
[Hosting wählen](../../unternehmen/auswahl/hosting-auswaehlen.md).

## Fragen an einen Hoster

1. Welche PHP-Version läuft, und kann ich eine neuere wählen?
2. Können Cronjobs alle paar Minuten laufen, und bekomme ich Zugang zur Kommandozeile?
3. Wie oft werden Sicherungen erstellt, wie lange werden sie aufbewahrt, und wie lasse ich eine wiederherstellen?
4. Ist HTTPS enthalten und wird es automatisch erneuert?
5. Kann der Server E-Mails versenden, und ist die Absenderadresse so eingerichtet, dass E-Mails nicht als Spam markiert
   werden? (Das ist wichtig für Nachrichten über das Kontaktformular, Shop-Bestätigungen und Newsletter.)
6. Wen rufe ich an einem Samstag an, und wie schnell antwortet er?
7. Wo stehen die Server? (Fragen Sie Ihren Berater, ob das für die Daten Ihrer Kunden eine Rolle spielt.)

## Domain, E-Mail und die Website

Das sind drei verschiedene Dinge, oft bei drei Firmen gekauft:

| Was | Was es ist | Halten bei |
|---|---|---|
| Domain | Ihr Name im Internet (`example.com`) | auf den Namen des Unternehmens |
| E-Mail | Ihre Postfächer (`info@example.com`) | einem beliebigen E-Mail-Anbieter |
| Website-Hosting | der Server, auf dem Exponential läuft | dem Hoster oder Partner |

Ein Umzug der Website zu einem neuen Hoster muss Ihre Postfächer nicht berühren, solange die Einstellungen der Domain
sorgfältig geändert werden. Bitten Sie den Hoster, das zu erledigen.

## Checkliste

- [ ] Der Server erfüllt die [Anforderungen](../../hosting/grundlagen/anforderungen-auf-einen-blick.md), einschließlich Cron und Kommandozeile.
- [ ] Sicherungen: Häufigkeit, Aufbewahrung und Wiederherstellung getestet ([Sicherungen, die sich wiederherstellen lassen](../../unternehmen/zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)).
- [ ] HTTPS mit automatischer Erneuerung.
- [ ] E-Mails von der Website kommen an (testen Sie eine Nachricht über das Kontaktformular).
- [ ] Support-Zeiten und Kontakt sind aufgeschrieben.
- [ ] Die Domain ist auf das Unternehmen registriert.

## Verwandte Seiten

- [Einen Hoster wählen](../../kunden/partner-und-hoster/einen-hoster-auswaehlen.md)
- [Was das Hosting von Exponential umfasst](../../hosting/grundlagen/was-das-hosting-von-exponential-umfasst.md)
- [Kostentreiber beim Hosting](../../unternehmen/kosten/kostentreiber-beim-hosting.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
