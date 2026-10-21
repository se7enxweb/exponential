---
title: Berechtigungen im Extranet prüfen
audience: businesses, admin, project-manager
category: business-solutions
order: 70
summary: Eine wiederkehrende Routine, um zu prüfen, wer in einem Exponential-Extranet was sehen kann - Rollen und Zuordnungen lesen, ungenutzte und verwaiste Konten finden, Konten beendeter Partnerschaften stilllegen, Sektionen und Zustände prüfen und Nachweise mit dem Audit-Trail aufbewahren.
keywords: Berechtigungsprüfung, Zugriffsprüfung, Rollen, Zuordnungen, Voller Zugriff, Kann Rollen ändern, Benutzerkonten prüfen, Kontenhygiene, nie angemeldet, nicht gesehen, deaktivieren und verschieben, verwaiste Konten, Audit-Trail, exp:access:check, Governance des Extranets, Rezertifizierung, permissions review, access review, roles, assignments, user hygiene, never signed in, orphaned accounts, audit trail, extranet governance, recertification
type: help
---
# Berechtigungen im Extranet prüfen

Diese Seite ist für Portalverantwortliche und Administratoren. Zugriffe, die zu Beginn einer Partnerschaft richtig
waren, sind ein Jahr später oft falsch: Menschen wechseln die Stelle, Verträge enden, Stufen ändern sich. Eine kurze
Prüfung in einem festen Rhythmus hält das Portal sauber. Die Seite nennt die Routine und die Seiten von Exponential,
die jeden Schritt schnell machen.

![Sektionen mit den Rollen, die sie verwenden](../../../../eng-us/help/images/help/bu-sections.webp)

## Inhalt

1. [Warum prüfen](#warum-prüfen)
2. [Der Rhythmus](#der-rhythmus)
3. [Schritt 1: Rollen](#schritt-1-rollen)
4. [Schritt 2: Zuordnungen](#schritt-2-zuordnungen)
5. [Schritt 3: Konten](#schritt-3-konten)
6. [Schritt 4: Bereiche](#schritt-4-bereiche)
7. [Schritt 5: Stichproben](#schritt-5-stichproben)
8. [Schritt 6: die Prüfung festhalten](#schritt-6-die-prüfung-festhalten)
9. [Wenn eine Partnerschaft endet](#wenn-eine-partnerschaft-endet)
10. [Verwandte Seiten](#verwandte-seiten)

## Warum prüfen

- Die Mitarbeiter von Partnern gehen, ohne es Ihnen zu sagen; ihre Konten bleiben.
- Eine Rolle, die für einen Zweck bearbeitet wurde, kann mehr öffnen als beabsichtigt; Richtlinien mehrerer Rollen
  addieren sich.
- Inhalte werden verschoben; eine Teilbaum-Einschränkung folgt dem Baum, verschobene Inhalte wechseln also die
  Zielgruppe.
- Datenschutzregeln erwarten, dass personenbezogene Daten nur so lange aufbewahrt werden wie nötig
  ([DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md); diese Seite ist keine Rechtsberatung).

## Der Rhythmus

| Prüfung | Empfohlener Rhythmus | Aufwand |
|---|---|---|
| Rollen und Kennzeichen | jedes Quartal und nach jeder Rollenänderung | ein kurzer Blick auf eine Seite |
| Konten | jedes Quartal | eine gefilterte Liste pro Frage |
| Bereiche und Sektionen | zweimal im Jahr | die Sektionsliste und einige Stichproben |
| Vollständige Prüfung mit den Verantwortlichen für die Beziehungen | einmal im Jahr | ein Termin pro Zielgruppe |

Wählen Sie den Rhythmus mit Ihrem Sicherheitsbeauftragten; die Seite [Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)
im Bereich Sicherheit beschreibt die allgemeine Methode.

## Schritt 1: Rollen

Öffnen Sie **Benutzer > Rollen und Richtlinien**. Jede Rolle ist eine Karte mit ihren Richtlinien, ihren Zuordnungen und
Kennzeichen ([Rollen und Richtlinien, 1](../../../anleitungen/rollen-und-richtlinien.md#1-die-rollenliste-lesen)):

- [ ] **Voller Zugriff** (rot) erscheint nur bei Administrator.
- [ ] **Kann Rollen ändern** (gelb) erscheint bei keiner Partnerrolle.
- [ ] Rollen mit **Nicht zugewiesen** werden entweder später gebraucht oder entfernt.

Öffnen Sie jede Partnerrolle. Ihre Richtlinien lesen sich als Sätze, etwa *May read content, in section Partner
material*. Eine Einschränkung, die kein Handler auswertet, ist mit *(no handler, denies)* gekennzeichnet
([Rollen und Richtlinien, 2](../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)).

- [ ] Jede Partnerrichtlinie hat eine Einschränkung auf Sektion, Teilbaum oder Zustand.
- [ ] `user/login` ist auf den Siteaccess des Portals beschränkt.
- [ ] Keine Partnerrolle hat `content/remove`, sofern nicht vereinbart.

## Schritt 2: Zuordnungen

Auf jeder Rollenseite zeigt die Liste der Benutzer und Gruppen jede Zuordnung mit ihrer Einschränkung (*Only in the
subtree Partner A*). Eine Zuordnung, deren Benutzer oder Gruppe entfernt wurde, steht ganz oben und kann entfernt werden
([Rollen und Richtlinien, 2](../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen)).

- [ ] Jede Teilbaum-Zuordnung passt zu einem aktuellen Partner.
- [ ] Keine Zuordnung zeigt auf eine entfernte Gruppe.
- [ ] Bevorzugen Sie Gruppenzuordnungen; direkt zugeordnete Benutzer sind Ausnahmen mit einem Grund.

Die Zahl **Betroffene Benutzer insgesamt** auf der Rollenseite zählt alle, die die Rolle erreicht; ein plötzlicher
Sprung verdient eine Nachfrage.

## Schritt 3: Konten

**Benutzer > Benutzerkonten prüfen** (`user/hygiene`) beantwortet die Fragen zu Konten mit Filtern
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md#the-filters)):

| Filter | Frage |
|---|---|
| Nie angemeldet | angelegte, aber nie benutzte Konten: noch nötig? |
| „Not seen for“ N Tagen (standardmäßig 180) | Partner, die das Portal nicht mehr nutzen |
| Deaktiviert, Gesperrt | Konten zum Aufräumen oder Entsperren |
| Verwaist | Konten, deren Benutzerobjekt fehlt oder keinen Ort hat |
| Doppelte E-Mail-Adresse | eine Person mit mehreren Konten |

Wählen Sie eine Benutzergruppe, um nur eine Partnerorganisation anzusehen. **Export the list as CSV** gibt der
verantwortlichen Person für die Beziehung eine Liste zum Bestätigen. **Deaktivieren** sperrt die Anmeldung und beendet
Sitzungen, behält aber Inhalte und Gruppen.

Mit `[UserSettings] DeactivateAndMove=enabled` deaktiviert **Deaktivieren und verschieben** Konten und verschiebt alle
ihre Orte in eine verborgene Gruppe ohne Rollen, wobei vermerkt wird, wo sie waren; **Reaktivieren und
zurückverschieben** stellt sie wieder her ([Deaktivieren und verschieben](../../../../eng-us/features/6.0/user-hygiene.md#deactivate-and-move)).
Für ein Portal ist das der sauberste Weg, Konten stillzulegen, ohne sie zu löschen. Das Entfernen eines Benutzerobjekts
ist endgültig; für Benutzer gibt es keinen Papierkorb.

Leeren Sie auch **Benutzer > Nicht aktivierte Benutzer**: Registrierungen, die nie bestätigt wurden
([Rollen und Richtlinien, 7](../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)).

## Schritt 4: Bereiche

Öffnen Sie **Setup > Sektionen** (`/section/list`). Jede Sektionskarte zeigt ihre Objekte und die Rollen, die sie
verwenden, und kennzeichnet Sektionen, die Aufmerksamkeit brauchen
([Sektionen, 3](../../../anleitungen/sektionen.md#3-die-liste-der-sektionen)).

- [ ] Jede Partnersektion wird nur von den vorgesehenen Rollen verwendet.
- [ ] Keine Partnerinhalte liegen versehentlich in der Sektion Standard.
- [ ] Zustandsgruppen, die für Zielgruppen verwendet werden, listen nur die vorgesehenen Rollen auf (**Setup >
  Zustände**, „Rollen, die diese Gruppe verwenden“).

## Schritt 5: Stichproben

- Melden Sie sich als Testbenutzer zweier Partner an und öffnen Sie die Adresse des Ordners des anderen Partners; es
  muss „Zugriff verweigert“ erscheinen.
- Suchen Sie als jeder Testbenutzer nach einem Wort aus den Dokumenten des anderen; es darf nichts erscheinen.
- `./console exp:access:check` fragt die Berechtigungsprüfungen für einen anderen Benutzer auf der Kommandozeile ab
  ([Einschränkungen von Inhaltsrichtlinien](../../../../eng-us/features/6.0/content-limitation-handlers.md#checking-access-for-another-user)).

## Schritt 6: die Prüfung festhalten

Der Audit-Trail zeichnet Rollenzuordnungen, Kontosperren, Anmeldungen und Inhaltsänderungen in einem
manipulationssicheren Log auf, und `./console exp:audit verify` belegt, dass die Aufzeichnung unversehrt ist
([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)). Schreiben Sie zu jeder Prüfung eine kurze Notiz: Datum,
prüfende Person, was geändert wurde. Die Seite
[Compliance-Nachweise](../sicherheit/compliance-nachweise-aus-dem-audit-trail.md) zeigt, wie Sie den Audit-Trail als
Nachweis verwenden.

## Wenn eine Partnerschaft endet

1. Entfernen Sie die Rollenzuordnungen der Organisation (ihre Teilbaum-Zuordnung und die Gruppen der Stufen).
2. Deaktivieren und verschieben Sie ihre Konten, oder deaktivieren Sie sie.
3. Archivieren oder entfernen Sie ihren Ordner, gemäß dem Vertrag und Ihren Aufbewahrungsregeln
   ([Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md)).
4. Vermerken Sie die Änderung in der Aufzeichnung der Prüfung.

## Verwandte Seiten

- [Zugriffsprüfungen](../sicherheit/zugriffspruefungen.md)
- [Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
- [Kennzahlen aus Logs und Audit](../../fuehrung/kennzahlen-aus-logs-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
