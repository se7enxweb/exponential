---
title: Schlüssel und Geheimnisse in Datensicherungen
audience: government, hosting, admin
category: public-sector
order: 60
summary: Für Sicherheits- und Betriebsteams in öffentlichen Stellen. Welche kleinen Dateien einer Exponential-Website Schlüssel enthalten, die sich nicht neu erzeugen lassen (Signaturschlüssel des Prüfprotokolls, das Geheimnis der E-Mail-Einstellungen, Zertifikatsschlüssel), was bei ihrem Verlust nicht mehr funktioniert und wie Sie sie sichern, ohne Zugangsdaten zu verbreiten.
keywords: Schlüssel, Geheimnisse, Audit-Schlüssel, Signaturschlüssel, Pseudonymschlüssel, Geheimnis der E-Mail-Einstellungen, TokenSecret, Zertifikatsschlüssel, settings/override, Geheimnisspeicher, Schlüsselrotation, GenerateKeys, Verschlüsselung von Datensicherungen, keys, secrets, audit keys, signing key, pseudonym key, mail preference secret, certificate keys, secrets store, key rotation, backup encryption
type: help
---
# Schlüssel und Geheimnisse in Datensicherungen

Diese Seite richtet sich an Sicherheitsbeauftragte und Betriebsteams in öffentlichen Stellen. Sie beantwortet, welche
Dateien einer Exponential-Website Schlüssel enthalten, die die Software erzeugt hat und die sich nicht neu erzeugen
lassen, was bei ihrem Verlust nicht mehr funktioniert und wie Sie sie sichern, ohne Zugangsdaten weiter als nötig zu
verbreiten. Die Referenz ist [Datensicherung: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)
und der Abschnitt des Audit-Leitfadens zu [Schlüsseln](../../../../bc/6.0/audit.md#46-schlüssel).

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Was bei ihrem Verlust nicht mehr funktioniert](#was-bei-ihrem-verlust-nicht-mehr-funktioniert)
3. [Sie sicher sichern](#sie-sicher-sichern)
4. [Schlüssel aus Ihrem Geheimnisspeicher](#schlüssel-aus-ihrem-geheimnisspeicher)
5. [Rotation](#rotation)
6. [Checkliste](#checkliste)

## Die Dateien

| Datei | Enthält |
|---|---|
| `settings/override/audit.ini.append.php` | die Installations-ID, die Signaturschlüssel und den Pseudonymschlüssel des Prüfprotokolls, beim ersten Ereignis erzeugt |
| `settings/override/mailpreferences.ini.append.php` | `[SecretSettings] TokenSecret`, das Website-Geheimnis der E-Mail-Einstellungen |
| `settings/override/site.ini.append.php` | Datenbank-Zugangsdaten und andere Verbindungseinstellungen |
| Konfigurationsbaum von Velocity (`/etc/vc` oder `var/vc/qbix/etc`) | Website-Konfiguration und in `ssl/` die privaten Schlüssel der Zertifikate (Modus `0600`) |
| `config.cluster.php`, Cluster-Einstellungen | die Zugangsdaten der Cluster-Datenbank |

Die Datei mit den Audit-Schlüsseln hat den Modus `0640` und wird nie committet; exp:ini, die Debug-Leiste und die
Einstellungsansicht maskieren die Schlüssel und zeigen nur IDs und Fingerabdrücke
([Audit-Leitfaden](../../../../bc/6.0/audit.md#46-schlüssel)).

## Was bei ihrem Verlust nicht mehr funktioniert

| Verloren | Auswirkung |
|---|---|
| Signaturschlüssel des Prüfprotokolls | damit signierte Archive und Prüfpunkte lassen sich nicht mehr prüfen (`unknown_key`) |
| Pseudonymschlüssel des Prüfprotokolls | neue Pseudonyme stimmen nicht mehr mit alten überein |
| Geheimnis der E-Mail-Einstellungen | jeder Link in jeder bereits versandten E-Mail funktioniert nicht mehr, und Sperreinträge greifen nicht mehr |
| Zertifikatsschlüssel | HTTPS unter Velocity muss neu eingerichtet werden |

Für eine öffentliche Stelle ist der erste Punkt der gravierendste: Ein Prüfprotokoll, das sich nicht prüfen lässt, ist
ein schwächerer Nachweis.

## Sie sicher sichern

- **Schließen Sie sie in jede Datensicherung ein.** Sie sind klein; sie wegzulassen, um „das Risiko zu verringern“,
  schafft ein größeres Risiko.
- **Verschlüsseln Sie die Datensicherung**, oder zumindest den Teil, der `settings/override/` enthält
  ([Kopien außer Haus und verschlüsselte Kopien](externe-und-verschluesselte-kopien.md)).
- **Beschränken Sie, wer sie lesen darf.** Eine Datensicherung mit `settings/override/` ist „so schutzbedürftig wie der
  Server selbst“; geben Sie sie nie an einen externen Dienstleister oder ein Testteam weiter, ohne zu prüfen, was sie
  enthält.
- **Bewahren Sie eine separate, geschützte Kopie der Schlüssel** in Ihrem Geheimnisspeicher auf, damit das Prüfprotokoll
  auch dann prüfbar bleibt, wenn die Datensicherungen der Website verloren gehen.
- **Testkopien**: Eine aus der Produktion wiederhergestellte Testumgebung trägt dieselben Schlüssel; entscheiden Sie,
  ob das akzeptabel ist, oder geben Sie Testumgebungen eigene Schlüssel.

## Schlüssel aus Ihrem Geheimnisspeicher

Statt das Prüfprotokoll seine Schlüssel erzeugen zu lassen, können Sie sie bereitstellen: Setzen Sie
`[AuditKeySettings] GenerateKeys=disabled` und tragen Sie `InstallationID`, `ActiveSigningKey`, `SigningKey[<id>]`
(base64, 32 Byte) und `PseudonymKey` selbst in `settings/override` ein
([Audit-Leitfaden](../../../../bc/6.0/audit.md#46-schlüssel)). Mehrere Webserver müssen sich eine Schlüsseldatei teilen.

## Rotation

Rotieren Sie den Signaturschlüssel des Prüfprotokolls einmal im Jahr (das Dashboard erinnert Sie nach 365 Tagen daran)
und sofort, wenn er gelesen worden sein könnte:

```bash
./console exp:audit key list --allow-root-user
./console exp:audit key rotate --apply --allow-root-user
```

Die alten Schlüssel bleiben für ihre Archive erhalten; sichern Sie die Datei nach einer Rotation erneut. Die
Aufbewahrungsregel entfernt nie einen Signaturschlüssel aus den Einstellungen; entfernen Sie einen selbst nur, wenn
kein damit signiertes Archiv mehr vorhanden ist.

## Checkliste

- [ ] Die Schlüsseldateien sind in jeder Datensicherung enthalten, und die Datensicherung ist verschlüsselt.
- [ ] Eine separate Kopie der Audit-Schlüssel liegt im Geheimnisspeicher.
- [ ] Der Zugriff auf Datensicherungen, die `settings/override/` enthalten, ist beschränkt und wird protokolliert.
- [ ] Das Datum der Rotation des Audit-Signaturschlüssels steht im Betriebskalender.
- [ ] Über die Verwendung von Produktionsschlüsseln in Testumgebungen ist entschieden.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Archive des Prüfprotokolls und Datensicherung](audit-trail-archive-und-datensicherungen.md)
- [Konfiguration unter Versionsverwaltung](../hosting/konfiguration-unter-versionskontrolle.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
