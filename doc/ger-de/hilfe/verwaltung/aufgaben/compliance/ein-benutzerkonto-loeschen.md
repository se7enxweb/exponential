---
title: Ein Benutzerkonto löschen
audience: government, admin
category: public-sector
order: 270
summary: Schritt für Schritt - ein Benutzerkonto nach einer Löschentscheidung endgültig entfernen, im Wissen, dass Benutzer nie in den Papierkorb kommen, dass das Entfernen die E-Mail-Einstellungen des Kontos löscht und sein Einwilligungsprotokoll anonymisiert, und was vorher und nachher von Hand zu entfernen ist.
keywords: Benutzer löschen, Konto löschen, Recht auf Löschung, Benutzer entfernen, kein Papierkorb, eraseForRemovedAccount, E-Mail-Einstellungen, Einwilligungsprotokoll, Formulareinsendungen, UserCreatorID, Eigentümerschaft von Inhalten, Audit, öffentlicher Sektor, erase user, delete account, right to erasure, remove user, no trash, mail preferences, consent log, form submissions, content ownership, public sector
type: help
---
# Ein Benutzerkonto löschen

Für Administratoren, nachdem ein Löschantrag geprüft und genehmigt wurde. Das Entfernen eines Benutzerobjekts ist in
Exponential **endgültig**: Benutzer kommen nie in den Papierkorb. Wenn das Konto nur nicht mehr funktionieren soll,
deaktivieren Sie es oder verwenden Sie stattdessen Deaktivieren und verschieben
([Das Recht auf Löschung](../../datenschutz/recht-auf-loeschung.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Was bleibt](#was-bleibt)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Entscheidung und ihr Datum sind festgehalten.
- Das Konto ist nicht der anonyme Benutzer, nicht `UserCreatorID` und nicht Ihr eigenes; geschützte Konten werden von den
  Werkzeugen zur Kontenpflege absichtlich abgelehnt.
- Inhalte, die der Benutzer erstellt hat, bleiben und behalten den Benutzer als Verweis auf den Eigentümer; entscheiden
  Sie, ob Sie zuerst die Eigentümer ändern ([Eigentümer ändern](../../../../anleitungen/eigentuemer-aendern.md)).
- Exportiert ist, was die Person verlangt hat (Auskunftsersuchen gehen vor).

## Schritte

1. Entfernen Sie die **Formulareinsendungen** der Person: Setup > Gesammelte Informationen, öffnen Sie jedes Formular,
   das sie verwendet hat, markieren Sie ihre Sammlungen, **Ausgewählte entfernen**.
2. Finden Sie mit den Redakteuren Erwähnungen in Inhalten (Namen, Fotos) und behandeln Sie sie gemäß der Entscheidung.
3. Öffnen Sie den Knoten des Benutzers in der Administration (Benutzer) und **Entfernen** Sie ihn; bestätigen Sie. Ein
   Konto nach dem anderen.
4. Wenn die Person außerdem E-Mail-Einstellungen unter einer anderen Adresse ohne Konto hatte:
   `./console exp:mail:preferences erase --email=<address> --yes --allow-root-user`.
5. Setzen Sie die Suchstatistiken zurück, wenn ihr Name darin erscheint (Setup > Suchstatistiken).

## Prüfen, ob es funktioniert hat

- Das Konto erscheint nicht mehr unter Benutzer > Benutzerkonten prüfen.
- `./console exp:mail:preferences show --email=<address> --allow-root-user` zeigt für die Adresse keine Einstellungen.
- Die Zeilen des Einwilligungsprotokolls der Person sind anonymisiert (keine Adresse, keine IP, kein Benutzer).

## Was bleibt

- Audit-Einträge über das Konto, bis ihre Aufbewahrung endet (das Löschen eines Eintrags würde die Kette brechen); im
  Index nach 90 Tagen pseudonymisiert.
- Logs und Sicherungen, bis ihre Aufbewahrung und Rotation enden.

## Verwandte Seiten

- [Das Recht auf Löschung](../../datenschutz/recht-auf-loeschung.md)
- [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md)
- [E-Mail-Daten zu einer Adresse löschen](e-mail-daten-zu-einer-adresse-loeschen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
