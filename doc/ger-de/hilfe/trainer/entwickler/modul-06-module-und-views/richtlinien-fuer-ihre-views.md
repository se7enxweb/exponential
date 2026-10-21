---
title: "Lektion 6.3: Richtlinien für Ihre Views"
audience: trainer, developer
category: training
order: 63
summary: Die Teilnehmenden deklarieren eine Richtlinienfunktion in ihrem Modul, binden die View greet daran, weisen sie unter Benutzer > Rollen und Richtlinien der Rolle Anonymous zu und prüfen das Ergebnis mit has_access_to; exp:accesscheck erklärt den Zugriff auf Inhalte. 50 Minuten.
keywords: entwickler, richtlinie, richtlinienfunktion, FunctionList, functions, rolle, rolle anonymous, neue richtlinie, zugriff auf eine funktion gewähren, hasAccessTo, exp:accesscheck, has_access_to, berechtigungen, einschränkungen, developers course, policy, policy function, role, permissions, limitations
type: help
---
# Lektion 6.3: Richtlinien für Ihre Views

Eine View ist nur so sicher wie ihre Berechtigungsprüfung. Der Kernel übernimmt die Prüfung für Sie, wenn eine View
eine **Richtlinienfunktion** nennt: Benutzer brauchen dann eine Rolle mit einer Richtlinie für diese Funktion. In
dieser Lektion deklarieren die Teilnehmenden die Funktion `greet`, binden die View daran, weisen sie auf ihrer
Schulungsinstallation der Rolle Anonymous zu und prüfen auf der Kommandozeile, wer die View öffnen darf.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 6: Module und Views](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren, die Rollen aufbauen |
| Voraussetzungen | [Lektion 6.2](eine-eigene-view.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Richtlinienfunktionen in `$FunctionList` einer `module.php` deklarieren;
- eine View mit `'functions' => array( '<function>' )` an eine Funktion binden;
- erklären, was bei einer View ohne `functions` geschieht;
- einer Rolle unter **Benutzer** > **Rollen und Richtlinien** eine Modulfunktion zuweisen;
- den Zugriff auf eine Modulfunktion mit `has_access_to` und den Zugriff eines Benutzers auf Inhalte mit
  `exp:accesscheck` prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md), `kernel/section/module.php` (sein
`$FunctionList` mit Einschränkungen) und `eZUser::hasAccessToView()` in `kernel/classes/datatypes/ezuser/ezuser.php`.

1. **Funktionen** (4 Minuten). `$FunctionList['greet'] = array();` deklariert eine Richtlinienfunktion ohne
   Einschränkungen. `$FunctionList['assign']` des Moduls section zeigt Einschränkungen (Class, Section, Owner), die
   eine Rolle setzen kann.
2. **Eine View binden** (3 Minuten). `'functions' => array( 'greet' )` in der View. Mehrere Funktionen werden mit
   „und“ verknüpft; ein String wie `'read or edit'` ist ein Ausdruck.
3. **Kein `functions`** (2 Minuten). Die View braucht dann eine Richtlinie für das ganze Modul (alle Funktionen),
   die nur weit gefasste Rollen wie die eines Administrators haben. Das war die Situation in Lektion 6.2.
4. **Zuweisen** (4 Minuten). **Benutzer** > **Rollen und Richtlinien**, eine Rolle öffnen, **Bearbeiten**,
   **Neue Richtlinie**, das Modul wählen, dann **Zugriff auf eine Funktion gewähren**, die Funktion,
   **Vollen Zugriff gewähren** und **Speichern**.
5. **Prüfen** (2 Minuten). `exp:accesscheck` erklärt den Zugriff eines Benutzers auf Inhalte (`content/read`,
   `edit` ...) für einen Knoten oder ein Objekt, zum Beispiel
   `php bin/php/console exp:accesscheck --user=anonymous --node=2`; für eine Modulfunktion verwenden Templates
   `fetch( 'user', 'has_access_to', ... )` (Lektion 5.3), um Links auszublenden, niemals zum Schutz.

### Häufige Fragen

**Muss ich nach dem Ändern einer Rolle Caches leeren?** Rollenänderungen übernimmt der Kernel selbst; angemeldete
Benutzer müssen sich wegen zwischengespeicherter Berechtigungsdaten eventuell neu anmelden, und für Seiten im
View-Cache muss eventuell der Inhalts-Cache geleert werden.

**Kann ich meiner eigenen Funktion Einschränkungen hinzufügen?** Ja: Geben Sie der Funktion ein Array von
Einschränkungsdefinitionen, wie es das Modul section tut. Der Rolleneditor bietet sie dann an.

**Was ist der Unterschied zwischen der Modulrichtlinie und einer Funktionsrichtlinie?** Eine Modulrichtlinie gewährt
alle Funktionen des Moduls, heutige und künftige; eine Funktionsrichtlinie gewährt eine einzige.

### Stolperfallen

- **Das ganze Modul an Anonymous vergeben.** Im Rolleneditor einfach und gefährlich: Weisen Sie die eine Funktion zu.
- **Nur im Template prüfen.** Ein ausgeblendeter Link ist keine geschützte View. Die Prüfung ist das `functions` der
  View.
- **Gemeinsame Installation.** Eine Änderung an der Rolle Anonymous betrifft alle Teilnehmenden. Alle arbeiten auf
  ihrer eigenen Installation, oder der Trainer nimmt die Rollenänderung einmal am Projektor vor.

## Praktische Übung

Auf der Schulungsinstallation, in `extension/mysite_hello`, und in der Administration als Administrator.

1. Ändern Sie `modules/hello/module.php`:

   ```php
   <?php
   $Module = array( 'name' => 'hello' );
   $ViewList = array();
   $ViewList['greet'] = array(
       'functions' => array( 'greet' ),
       'script' => 'greet.php',
       'params' => array( 'Name' ),
       'unordered_params' => array( 'times' => 'Times' ) );
   $FunctionList = array();
   $FunctionList['greet'] = array();
   ```

2. Prüfen Sie die Datei mit `php -l` und leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini` und
   `--clear-id=global_ini`.
3. Öffnen Sie abgemeldet `/hello/greet/Ada`: Der Zugriff wird verweigert.
4. Öffnen Sie in der Administration **Benutzer** > **Rollen und Richtlinien** und die Rolle **Anonymous**. Lesen
   Sie ihre Richtlinien.
5. Klicken Sie auf **Bearbeiten** und dann auf **Neue Richtlinie**. Wählen Sie das Modul `hello`, dann
   **Zugriff auf eine Funktion gewähren**, wählen Sie `greet` und dann **Vollen Zugriff gewähren**.
6. Klicken Sie bei der Rolle auf **Speichern**.
7. Öffnen Sie abgemeldet erneut `/hello/greet/Ada`.
8. Vergleichen Sie mit dem Zugriff auf Inhalte, den die Zugriffsprüfung erklärt:
   `php bin/php/console exp:accesscheck --user=anonymous --node=2`.
9. Prüfen Sie die Funktion im Playground:
   `{fetch( 'user', 'has_access_to', hash( 'module', 'hello', 'function', 'greet' ) )|choose( 'no', 'yes' )}`.
10. Entfernen Sie die Richtlinie wieder aus der Rolle Anonymous, wenn Ihr Trainer Sie darum bittet, und committen
    Sie `module.php`.

## Erwartetes Ergebnis

- Schritt 3: die Seite „Zugriff verweigert“ der Website (oder das Anmeldeformular).
- Schritt 5: Der Rolleneditor führt `hello` unter den Modulen und `greet` als seine Funktion auf.
- Schritt 7: Die Begrüßung wird Besuchern angezeigt.
- Schritt 9 gibt nach Schritt 6 `yes` aus (der Playground läuft als anonymer Benutzer).
- Der Trainer prüft die Rolle Anonymous einer Person: eine Richtlinie `hello/greet`, nicht `hello/*`.

## Quiz

1. Welches Array der `module.php` deklariert Richtlinienfunktionen?
2. Eine View hat keinen Schlüssel `functions`. Welche Richtlinie braucht ein Benutzer?
   A. Keine B. Eine Richtlinie für das ganze Modul C. `content/read` D. `user/login`
3. Mit welcher Schaltfläche im Rolleneditor beginnt das Hinzufügen einer Richtlinie?
4. Warum genügt es nicht, einen Link mit `has_access_to` auszublenden?
5. Warum sollte Anonymous `hello/greet` statt des ganzen Moduls `hello` erhalten?

<details>
<summary>Antworten</summary>

1. `$FunctionList`.
2. **B.** Eine Richtlinie für das ganze Modul (`eZUser::hasAccessToView()`).
3. **Neue Richtlinie**.
4. Die View lässt sich weiterhin über ihre Adresse öffnen; nur die Prüfung über `functions` der View schützt sie.
5. Eine Modulrichtlinie gewährt alle Funktionen des Moduls, auch solche, die später hinzukommen.

</details>

## Weiterführende Literatur

- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): Rollen, Richtlinien, Einschränkungen und
  Zuweisungen.
- [Reihenfolge von Rollen und Richtlinien](../../../../../eng-us/features/6.0/role-policy-order.md) und
  [Berechtigungsprüfungen bei vielen Zuweisungen](../../../../../eng-us/features/6.0/permission-checks-for-many-assignments.md).
- [Handler für Inhaltseinschränkungen](../../../../../eng-us/features/6.0/content-limitation-handlers.md): Einschränkungen, in
  PHP geschrieben.

## Verwandte Seiten

- [Modul 6: Module und Views](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
