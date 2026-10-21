---
title: Kopien außer Haus und verschlüsselte Kopien
audience: government, hosting, admin
category: public-sector
order: 130
summary: Für IT- und Sicherheitsteams im öffentlichen Sektor. Warum Datensicherungen einer Exponential-Website den Server verlassen und verschlüsselt sein müssen, wohin Kopien nach den Regeln zum Speicherort der Daten gehen dürfen, wie Sie sie mit den in den Quellen genannten Werkzeugen verschlüsseln und wie Sie Schlüssel und Passphrasen wiederherstellbar halten.
keywords: Datensicherung außer Haus, verschlüsselte Datensicherung, zweiter Standort, GnuPG, Verschlüsselung im Ruhezustand, Speicherort der Daten, Datenresidenz, Passphrase, Schlüsselverwaltung, Ransomware, unveränderlicher Speicher, Zugriff auf Datensicherungen, offsite backup, encrypted backup, second location, encryption at rest, data location, data residency, key management, immutable storage, backup access
type: help
---
# Kopien außer Haus und verschlüsselte Kopien

Diese Seite richtet sich an IT- und Sicherheitsteams in öffentlichen Stellen, die entscheiden, wo Datensicherungen
einer Website aufbewahrt und wie sie geschützt werden. Sie beantwortet, warum Kopien den Server verlassen müssen, wohin
sie nach den Regeln zum Speicherort der Daten gehen dürfen, wie Sie sie verschlüsseln und wie Sie sicherstellen, dass
die Schlüssel genau die Katastrophe überstehen, für die die Datensicherung gedacht ist.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Warum die Kopie den Server verlassen muss](#warum-die-kopie-den-server-verlassen-muss)
2. [Wohin Kopien gehen dürfen](#wohin-kopien-gehen-dürfen)
3. [Verschlüsseln](#verschlüsseln)
4. [Die Schlüssel wiederherstellbar halten](#die-schlüssel-wiederherstellbar-halten)
5. [Kopien vor Angreifern schützen](#kopien-vor-angreifern-schützen)
6. [Checkliste](#checkliste)

## Warum die Kopie den Server verlassen muss

„Bewahren Sie Datensicherungen getrennt vom Server auf und halten Sie mehrere Generationen vor“
([10.9 Datensicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)).
Eine Kopie auf derselben Festplatte, derselben Maschine oder im selben Raum übersteht keinen Festplattenausfall, keine
Kompromittierung und keinen Brand. Datensicherungen, die innerhalb der Website erstellt werden (zum Beispiel mit
git_manager, das sie in `var/site/backups/captions` speichert), liegen auf demselben Server, bis jemand sie wegkopiert
([Datensicherung aus der Administration mit git_manager](datensicherungen-aus-der-administration-mit-git-manager.md)).

## Wohin Kopien gehen dürfen

Eine Datensicherung enthält dieselben Daten wie die Website: Inhalte, Konten, Formulareingaben, den Audit-Trail und
Zugangsdaten. Ihre Regeln zum Speicherort der Daten gelten für sie wie für die Live-Website
([Speicherort der Daten und was wo gespeichert wird](../hosting/datenstandort-und-was-wo-gespeichert-wird.md)).

| Speicherort | Zu bedenken |
|---|---|
| Ein zweites eigenes Gebäude | Entfernung zum ersten; Zutrittskontrolle |
| Das Unternehmens-Sicherungssystem Ihrer Organisation | meist der einfachste Weg; erfüllt zentrale Richtlinien |
| Eine Behördencloud oder ein anderer freigegebener Anbieter | die Standorte und Zugriffe des Anbieters; Verschlüsselung vor dem Hochladen |
| Wechseldatenträger in einem Tresor | Verfahren für die Handhabung; Tests, ob sich die Datenträger noch lesen lassen |

## Verschlüsseln

Verschlüsseln Sie Kopien, bevor sie Ihre Kontrolle verlassen, und bei der Übertragung. Werkzeuge, die die Quellen
nennen:

- **GnuPG**, von git_manager mit AES-256 für seine verschlüsselten Datensicherungen verwendet. Eine so verschlüsselte
  Datei wird entschlüsselt mit:

  ```bash
  gpg --batch --yes --passphrase "YOUR_PASSPHRASE" -d backup_file.tar.gz.gpg | tar -xzf -
  ```

  Bevorzugen Sie im echten Einsatz eine Passphrase-Datei oder einen Agenten gegenüber einer Passphrase auf der
  Kommandozeile ([git_manager](../../../../../eng-us/features/6.0/extensions/git_manager.md)).
- Die eigene Verschlüsselung Ihres Unternehmens-Sicherungssystems, wenn es eine von Ihrem Sicherheitsteam
  freigegebene hat.

Unabhängig vom Werkzeug dürfen die Dateien mit Zugangsdaten und Schlüsseln (`settings/override/`) nie in einer
unverschlüsselten Kopie liegen, die den Server verlässt
([Schlüssel und Geheimnisse in Datensicherungen](schluessel-und-geheimnisse-in-datensicherungen.md)).

## Die Schlüssel wiederherstellbar halten

„Für eine verlorene Passphrase gibt es keine Wiederherstellung.“ Eine verschlüsselte Datensicherung, deren Schlüssel
nur auf dem verlorenen Server lag, ist keine Datensicherung.

- Bewahren Sie Verschlüsselungsschlüssel und Passphrasen in Ihrem Geheimnisspeicher auf, mit mindestens zwei Personen,
  die sie erreichen können.
- Bewahren Sie dort auch eine Kopie der Schlüssel des Audit-Trails auf.
- Üben Sie eine Wiederherstellung aus der Kopie außer Haus, einschließlich der Entschlüsselung, als Teil von
  [Wiederherstellung testen](wiederherstellungstests.md).

## Kopien vor Angreifern schützen

Ein Angreifer, der den Server kontrolliert, kann versuchen, auch die Datensicherungen zu löschen oder zu
verschlüsseln. Hilfreiche Praktiken:

- Der Server kann neue Datensicherungen schreiben, aber alte weder ändern noch löschen (einmal beschreibbarer Speicher
  oder Speicher, an den nur angehängt werden kann, oder ein Abholen durch das Sicherungssystem statt eines Schiebens
  vom Server);
- separate Zugangsdaten für den Sicherungsspeicher, die nicht auf dem Webserver gespeichert sind;
- mehrere Generationen, damit eine Datensicherung, die nach Beginn des Angriffs erstellt wurde, nicht die einzige ist
  ([Kompromittierte Website](../../../hosting/runbooks/kompromittierte-website.md)).

Die Referenzkonfiguration des Audit-Trails für lange Aufbewahrung nutzt denselben Gedanken: Archive auf einem Mount,
dem der Webserver Dateien hinzufügen, den er aber nicht ändern kann
([Archive des Audit-Trails und Datensicherung](audit-trail-archive-und-datensicherungen.md)).

## Checkliste

- [ ] Jede Datensicherung wird vom Server weg an einen Ort kopiert, den Ihre Datenregeln zulassen.
- [ ] Kopien sind im Ruhezustand und bei der Übertragung verschlüsselt.
- [ ] Schlüssel und Passphrasen liegen im Geheimnisspeicher und sind für mindestens zwei Personen erreichbar.
- [ ] Der Server kann ältere Kopien weder löschen noch ändern.
- [ ] Eine Wiederherstellung aus der Kopie außer Haus, mit Entschlüsselung, wurde getestet.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Sicherungspläne und Aufbewahrung](sicherungsplaene-und-aufbewahrung.md)
- [Notfallwiederherstellungsplan](notfallwiederherstellungsplan.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
