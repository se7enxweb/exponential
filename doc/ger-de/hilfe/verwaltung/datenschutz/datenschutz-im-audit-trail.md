---
title: Datenschutz im Prüfprotokoll
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Für öffentliche Stellen, die Rechenschaft und Datenschutz gegeneinander abwägen - welche personenbezogenen Daten das Prüfprotokoll von Exponential standardmäßig aufbewahrt, wie sie minimiert und pseudonymisiert werden, welche Rechtsgrundlage der Audit-Leitfaden vorschlägt, und die Konfiguration für strengen Datenschutz für Websites, die weniger aufbewahren müssen.
keywords: Datenschutz im Prüfprotokoll, Pseudonymisierung, AuditPrivacySettings, Field, truncate, hash, PseudonymiseAfterDays, BeforeAfter, strenger Datenschutz, Rechtsgrundlage, berechtigtes Interesse, rechtliche Verpflichtung, DSGVO, Beschäftigte, Betriebsrat, Personalrat, öffentlicher Sektor, audit trail privacy, pseudonymization, lawful basis, GDPR
type: help
---
# Datenschutz im Prüfprotokoll

Diese Seite richtet sich an Datenschutzbeauftragte und Administratoren. Das Prüfprotokoll zeichnet auf, was Beschäftigte
und Besucher tun; das sind personenbezogene Daten über sie, häufig über Beschäftigte. Exponential gestaltet es
datenschutzfreundlich voreingestellt und lässt Sie es verschärfen
([4.12 Datenschutz- und DSGVO-Aufgaben](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was standardmäßig aufbewahrt wird](#was-standardmäßig-aufbewahrt-wird)
2. [Rechtsgrundlage](#rechtsgrundlage)
3. [Pseudonymisierung](#pseudonymisierung)
4. [Ein strengeres Profil](#ein-strengeres-profil)
5. [Beschäftigte und Personalvertretungen](#beschäftigte-und-personalvertretungen)
6. [Verwandte Seiten](#verwandte-seiten)

## Was standardmäßig aufbewahrt wird

`settings/audit.ini [AuditPrivacySettings] Field[]` im Auslieferungszustand:

| Feld | Voreinstellung | Bedeutung |
|---|---|---|
| `actor.login` | `full` | der Anmeldename der handelnden Person |
| `actor.ip` | `truncate` | die Adresse, gekürzt auf ihr /24-Netz (IPv6 /48) |
| `actor.ua` | `truncate` | der User-Agent, reduziert auf Browser- und Betriebssystemfamilie |
| `actor.session` | `hash` | die Sitzungskennung wird nie aufgezeichnet |
| `request.url` | `truncate` | |
| `object.name` | `full` | der Name des Objekts, auf das sich die Handlung bezieht |
| `email`, `attempted_login` | `hash` | Adressen und Anmeldenamen unbekannter Benutzer |

Nie aufgezeichnet werden: Inhaltswerte, Passwörter, Passwort-Hashes, Token, Rücksetzschlüssel, Geheimnisse,
POST-Inhalte. Lesezugriffe (der Kanal `read`) sind ausgeschaltet.

## Rechtsgrundlage

Der Audit-Leitfaden nennt für Ihr eigenes Verzeichnis von Verarbeitungstätigkeiten: das berechtigte Interesse an der
Sicherheit und Nachvollziehbarkeit des Systems (Art. 6 Abs. 1 Buchst. f) und eine rechtliche Verpflichtung, wo ein
Gesetz die Protokollierung von Zugriffen verlangt (Art. 6 Abs. 1 Buchst. c); Art. 32 nennt die Protokollierung als
Sicherheitsmaßnahme. Öffentliche Stellen stützen sich häufig auf eine Aufgabe im öffentlichen Interesse oder eine
rechtliche Verpflichtung statt auf das berechtigte Interesse; entscheiden Sie das mit Ihrer bzw. Ihrem
Datenschutzbeauftragten und dokumentieren Sie es.

## Pseudonymisierung

- Nach `PseudonymiseAfterDays` (standardmäßig 90) ersetzt die tägliche Aufgabe Anmeldename, Adresse, User-Agent und
  personenbezogene Felder **im Index** (den die Konsole durchsucht) durch ihren verschlüsselten Hash; das wird als
  `system.audit.pseudonymise` aufgezeichnet.
- Vorschau: `./console exp:audit pseudonymise --dry-run --allow-root-user`.
- Die Dateien werden nicht umgeschrieben (das würde die Hash-Kette unterbrechen): Die vollständigen Werte bleiben in den
  Archiven und sind mit `audit/manage` lesbar. Eine Website, die sie nicht einmal dort aufbewahren darf, setzt von
  Anfang an `hash`.
- Ein Hash ist ein HMAC mit Schlüssel: Dieselbe Person lässt sich weiterhin zusammenfassen (Warnungen nach Adresse
  funktionieren weiter), aber ohne den Schlüssel nicht zurücklesen.

## Ein strengeres Profil

Die Referenzkonfiguration des Leitfadens für strengen Datenschutz
([6.4 Strenger Datenschutz](../../../../eng-us/bc/6.0/audit.md#64-strict-privacy)) in
`settings/override/audit.ini.append.php`:

```ini
[AuditPrivacySettings]
Field[actor.login]=hash
Field[actor.ip]=hash
Field[actor.ua]=off
Field[actor.cli.os_user]=hash
Field[request.url]=truncate
Field[request.host]=hash
Field[object.name]=truncate
PseudonymiseAfterDays=30

[AuditRecordSettings]
BeforeAfter=keys
```

mit kürzerer Aufbewahrung pro Kanal (`LiveDays=30`, `ArchiveDays=180`) und `[AuditIndexSettings] KeepDays=180`. Mit
`BeforeAfter=keys` sagt der Eintrag, welche Felder sich geändert haben, nicht aber ihre Werte. Wägen Sie das gegen
Untersuchungen ab: Gehashte Anmeldenamen lassen sich weiterhin zuordnen, wenn Sie den Anmeldenamen kennen, aber nicht
lesen.

## Beschäftigte und Personalvertretungen

Die Überwachung von Beschäftigten ist in vielen Rechtsordnungen geregelt und kann eine Beteiligung der
Personalvertretung erfordern. Dokumentieren Sie, was das Audit aufzeichnet, wer es lesen darf (`audit/read`, begrenzt
nach Kanal), wie lange es aufbewahrt wird und dass es nicht zur Leistungskontrolle genutzt wird, sofern das nicht
vereinbart ist. Geben Sie `audit/read` nur wenigen Personen; jeder Lesezugriff auf das Audit wird selbst
aufgezeichnet (`system.audit.read`).

## Verwandte Seiten

- [Audit-Leitfaden, 4.12](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)
- [Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)
- [Aufbewahrung von Audit-Einträgen](aufbewahrung-von-audit-eintraegen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
