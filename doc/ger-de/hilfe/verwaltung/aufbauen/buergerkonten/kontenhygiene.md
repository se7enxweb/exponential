---
title: Kontenhygiene
audience: government, admin
category: public-sector
order: 60
summary: Wie Sie die Konten einer Exponential-Site mit Benutzer > Benutzerkonten prüfen in Ordnung halten - nie angemeldet, seit N Tagen nicht gesehen, deaktiviert, gesperrt, verwaist, doppelte Benutzernamen und Adressen - mit Deaktivieren, Aktivieren und Entsperren in einem Schritt, einem CSV-Export und einer monatlichen Routine.
keywords: Kontenhygiene, Benutzerkonten prüfen, nie angemeldet, nicht gesehen, deaktiviert, gesperrt, verwaist, doppelte E-Mail, deaktivieren, aktivieren, entsperren, CSV exportieren, inaktive Konten, account hygiene, Check user accounts, user/hygiene, never signed in, not seen, disabled, locked, orphaned, shared e-mail, disable, enable, unlock, export CSV, inactive accounts
type: help
---
# Kontenhygiene

Diese Seite richtet sich an Administratoren, die sich um die Konten einer Site für den öffentlichen Sektor kümmern.
Ungenutzte Konten sind ein Risiko (ein altes Passwort, das anderswo wiederverwendet wird) und personenbezogene Daten
ohne Zweck. **Benutzer > Benutzerkonten prüfen** (`user/hygiene`) findet sie und bearbeitet viele auf einmal. Quelle:
[Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md).

## Inhalt

1. [Die Seite](#die-seite)
2. [Die Filter](#die-filter)
3. [Die Aktionen](#die-aktionen)
4. [Eine monatliche Routine](#eine-monatliche-routine)
5. [Wer die Seite benutzen darf](#wer-die-seite-benutzen-darf)

## Die Seite

Öffnen Sie **Benutzer > Benutzerkonten prüfen**, auch eine Kachel der Setup-Übersicht unter **Benutzer und
Nachrichten**. Sie listet jedes Konto, oder die unterhalb einer Benutzergruppe, mit Name, Benutzername, E-Mail-Adresse
(nur mit dem Recht, Adressen zu sehen), Zuständen, letztem Besuch mit den Tagen seither, Zahl der Anmeldungen, Gruppen
und Rollen. Suche, Seitengrößen von 25, 50 und 100 und das Sortieren nach Name, Benutzername, Adresse, letztem Besuch
oder Anmeldungen grenzen die Liste ein. **Liste als CSV exportieren** gibt die ganze gefilterte Liste aus.

## Die Filter

| Filter | Zeigt |
|---|---|
| **Alle Konten** | jedes Konto, dessen Objekt nicht im Papierkorb liegt |
| **Nie angemeldet** | Konten, die sich nie angemeldet haben (nicht der anonyme Benutzer) |
| **Seit N Tagen nicht gesehen** | einmal angemeldet, letzter Besuch vor mehr als N Tagen (voreingestellt 180) |
| **Deaktiviert** | deaktivierte Konten; mit einem Aktivierungsschlüssel als *Nicht aktiviert* markiert |
| **Gesperrt** | über `MaxNumberOfFailedLogin` fehlgeschlagene Anmeldungen (keine, solange die Einstellung 0 ist) |
| **Verwaist** | eine Kontozeile, deren Benutzerobjekt fehlt, nie veröffentlicht wurde oder keinen Ort hat |
| **Doppelter Benutzername** | Benutzernamen, die ein anderes Konto ebenfalls hat (in beliebiger Schreibweise) |
| **Doppelte E-Mail-Adresse** | Adressen, die von mehr als einem Konto verwendet werden (nur mit `role/assign`) |

## Die Aktionen

| Aktion | Wirkung |
|---|---|
| **Deaktivieren** | die markierten Konten können sich nicht mehr anmelden; umkehrbar |
| **Aktivieren** | das Gegenteil |
| **Entsperren** | setzt die fehlgeschlagenen Anmeldungen gesperrter Konten zurück |
| **Deaktivieren und verschieben** (optional) | deaktiviert Konten und verschiebt sie in eine versteckte Gruppe ohne Rollen, wobei vermerkt wird, wo sie waren ([Deaktivierung und Entfernung](deaktivierung-und-entfernung.md)) |

Jede Schaltfläche zeigt eine Bestätigung mit der Zahl der Konten, die sich ändern, und der Zahl der ausgelassenen,
mit dem Grund. Nichts auf dieser Seite entfernt ein Konto: Das Entfernen eines Benutzerobjekts ist endgültig und wird
Konto für Konto mit dem eigenen **Entfernen** des Knotens erledigt.

## Eine monatliche Routine

- [ ] Filter **Nie angemeldet**, älter als Ihr Registrierungszeitraum: Entfernen oder deaktivieren Sie diese
  Registrierungen.
- [ ] Filter **Seit N Tagen nicht gesehen** mit Ihrer Aufbewahrungsfrist (zum Beispiel 365 für öffentliche Konten, als
  Beispielannahme): deaktivieren, per Mail warnen, wenn Ihre Richtlinie es vorsieht, dann nach einer weiteren Frist
  entfernen.
- [ ] Filter **Gesperrt**: Entsperren Sie berechtigte Benutzer nach einer Prüfung; untersuchen Sie Muster.
- [ ] Filter **Doppelte E-Mail-Adresse**: Führen Sie Dubletten zusammen oder bereinigen Sie sie.
- [ ] Für Gruppen von Mitarbeitern: Vergleichen Sie mit der Liste der ausgeschiedenen Personen.

Nicht aktivierte Registrierungen stehen auf einer eigenen Seite, **Benutzer > Nicht aktivierte Benutzer**
([Registrierung](registrierung.md#nicht-aktivierte-registrierungen)).

## Wer die Seite benutzen darf

Die Seite braucht die Rechte der Benutzerverwaltung, die in
[Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md) beschrieben sind; Adressen werden nur mit
`role/assign` angezeigt. Beschränken Sie das auf eine kleine Gruppe: Die Liste ist ein Verzeichnis aller, die ein
Konto haben.

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Deaktivierung und Entfernung](deaktivierung-und-entfernung.md)
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
