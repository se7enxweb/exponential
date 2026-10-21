---
title: E-Mail-Daten zu einer Adresse löschen
audience: government, admin
category: public-sector
order: 280
summary: Schritt für Schritt - die E-Mail-Einstellungen und ausstehenden Bestätigungen einer E-Mail-Adresse löschen und ihr Einwilligungsprotokoll mit exp:mail:preferences erase anonymisieren, für Abonnenten ohne Konto, und entscheiden, ob ein rechtlicher Sperreintrag hinzukommt.
keywords: löschen, Recht auf Löschung, E-Mail-Einstellungen, Einwilligungsprotokoll anonymisiert, exp:mail:preferences erase, Newsletter-Abonnent, Sperrliste, rechtlich, DSGVO Artikel 17, öffentlicher Sektor, erase, right to erasure, mail preferences, consent log anonymized, newsletter subscriber, suppression list, legal, GDPR article 17, public sector
type: help
---
# E-Mail-Daten zu einer Adresse löschen

Für Administratoren, die einen Löschantrag von jemandem bearbeiten, der E-Mails von der Site erhält, oft ein
Newsletter-Abonnent ohne Benutzerkonto. (Bei einem Konto löscht das Entfernen des Kontos seine E-Mail-Daten
automatisch; siehe „Ein Benutzerkonto löschen“.)

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Der Antrag ist geprüft und die Identität der Person (die Kontrolle über die Adresse) bestätigt.
- Wenn die Person zuerst eine Kopie verlangt hat, exportieren Sie sie („Ein Auskunftsersuchen zu E-Mail-Einstellungen
  beantworten“).
- Shell-Zugang.

## Schritte

1. Sehen Sie nach, was vorhanden ist: `./console exp:mail:preferences show --email=<address> --allow-root-user`.
2. Löschen Sie:

   ```bash
   ./console exp:mail:preferences erase --email=<address> --yes --allow-root-user
   ```

   Ohne `--yes` verweigert der Befehl. Er antwortet mit der Zahl der entfernten Einstellungen und ausstehenden
   Bestätigungen und der anonymisierten Zeilen des Einwilligungsprotokolls. Mit der Newsletter-Erweiterung
   (cjw_newsletter 4.2) laufen Newsletter-Benutzer, Interessen, SMS-Daten und personenbezogene Statistiken der Person
   über deren Lösch-Hook.
3. Wenn der Person nie wieder geschrieben werden darf (und sie das wünscht), fügen Sie einen Sperreintrag hinzu;
   gespeichert wird nur ein Hash:

   ```bash
   ./console exp:mail:suppression add --email=<address> --reason=legal --note="<case reference>" --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

- `show` meldet für die Adresse nichts mehr.
- `./console exp:mail:consent list --email=<address> --allow-root-user` zeigt keine Zeilen mit der Adresse;
  anonymisierte Zeilen behalten nur Zeit, Kategorie und Änderung, sodass Sie weiterhin nachweisen können, dass die
  E-Mails eingestellt wurden.
- Halten Sie den Fall fest: Datum, Ausgabe des Befehls, ob ein Sperreintrag hinzugefügt wurde.

## Verwandte Seiten

- [Das Recht auf Löschung](../../datenschutz/recht-auf-loeschung.md)
- [Die Sperrliste](../../datenschutz/sperrliste.md)
- [E-Mail-Einstellungen für Administratoren: Anfragen](../../../../anleitungen/mail-einstellungen-administrator.md#6-anfragen-nach-dem-datenschutzrecht)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
