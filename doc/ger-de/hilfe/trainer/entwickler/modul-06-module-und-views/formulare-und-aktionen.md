---
title: "Lektion 6.4: Formulare und Aktionen in einer View"
audience: trainer, developer
category: training
order: 64
summary: Die Teilnehmenden fügen ihrer View ein Formular hinzu, ordnen dessen Schaltfläche mit single_post_actions einer Aktion zu, lesen den abgesendeten Wert über die Aktionsparameter des Moduls, validieren ihn, leiten nach dem Absenden weiter und sehen, wie der Formular-Token das Formular schützt. 55 Minuten.
keywords: entwickler, formular, POST, single_post_actions, post_action_parameters, isCurrentAction, actionParameter, redirectTo, eZHTTPTool, ezformtoken, ezxform_token, CSRF, formular abgelaufen, validierung, weiterleitung, post redirect get, developers course, form, form expired, validation
type: help
---
# Lektion 6.4: Formulare und Aktionen in einer View

Views, die etwas ändern, empfangen ein Formular. Der Kernel macht aus einer gedrückten Schaltfläche eine benannte
**Aktion**, übergibt der View die abgesendeten Werte und schützt jedes Formular mit einem Formular-Token gegen
Cross-Site Request Forgery. In dieser Lektion fügen die Teilnehmenden der View `greet` ein kleines Formular hinzu:
Ein Besucher gibt einen Namen ein, die View validiert ihn und leitet zur Begrüßung weiter.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 6: Module und Views](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 6.3](richtlinien-fuer-ihre-views.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Schaltfläche mit `single_post_actions` einer Aktion und ihre Werte mit `post_action_parameters` zuordnen;
- die Aktion mit `$Module->isCurrentAction()` prüfen und Werte mit `$Module->actionParameter()` lesen;
- Eingaben in der View validieren und im Template einen Fehler anzeigen;
- nach erfolgreichem Absenden mit `$Module->redirectTo()` weiterleiten;
- erklären, was der Formular-Token (`ezformtoken`) tut und was die Seite „Formular abgelaufen“ bedeutet.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/content/module.php` (`single_post_actions` von `edit` und `copy`), die Methoden `isCurrentAction()`,
`actionParameter()` und `redirectTo()` in `lib/ezutils/classes/ezmodule.php` sowie
[„Dieses Formular ist abgelaufen“](../../../../../eng-us/features/6.0/form-expired-page.md).

1. **Aktionen** (4 Minuten). In `module.php` bedeutet `'single_post_actions' => array( 'GreetButton' => 'Greet' )`:
   Ein POST mit einem Feld `GreetButton` ist die Aktion `Greet`. `'post_action_parameters' => array( 'Greet' => array(
   'Name' => 'GreetName' ) )` stellt das abgesendete Feld `GreetName` als Aktionsparameter `Name` bereit.
2. **In der View** (4 Minuten). `if ( $Module->isCurrentAction( 'Greet' ) )`, dann
   `$Module->actionParameter( 'Name' )`. Validieren; bei Erfolg `return $Module->redirectTo( ... )`; bei einem
   Fehler eine Fehlermeldung für das Template setzen.
3. **Post, Redirect, Get** (2 Minuten). Eine Weiterleitung nach erfolgreichem Absenden bedeutet, dass ein Neuladen
   nicht erneut absendet.
4. **Der Formular-Token** (3 Minuten). `ezformtoken` fügt jedem HTML-Formular, das der Kernel ausgibt, ein
   verstecktes Feld `ezxform_token` hinzu und prüft es bei jedem POST. Ein POST ohne dieses Feld wird mit der Seite
   „Formular abgelaufen“ abgewiesen: Nichts wird gespeichert. JavaScript, das Daten absendet, muss den Token
   ebenfalls mitschicken.
5. **Der Eingabe niemals vertrauen** (2 Minuten). In PHP Leerzeichen abschneiden und Länge und Zeichen prüfen; im
   Template escapen.

### Häufige Fragen

**Füge ich das Token-Feld selbst hinzu?** Nein, der Ausgabefilter fügt es den Formularen im HTML hinzu. Ajax-Code
muss es mitschicken; das README von `ezformtoken` erklärt, wie.

**Warum `redirectTo` und nicht einfach das Ergebnis anzeigen?** Beim Neuladen einer Seite, die aus einem POST
stammt, fragt der Browser, ob erneut abgesendet werden soll; eine Weiterleitung macht daraus einen normalen
Seitenaufruf.

**Kann ich `$_POST` direkt verwenden?** Verwenden Sie `eZHTTPTool::instance()->postVariable()` oder die
Aktionsparameter; diese nutzt auch der übrige Kernel, und andere Entwickler erwarten sie.

### Stolperfallen

- **Ein Schaltflächenname, der nicht in `single_post_actions` steht.** Die View sieht keine Aktion; nichts passiert.
- **Mit einer Seite testen, die vor einem Neustart geöffnet wurde.** Eine alte Seite kann einen alten Token
  enthalten: Laden Sie das Formular zuerst neu.
- **Eine Weiterleitung auf eine aus der Eingabe gebaute Adresse.** Leiten Sie auf Ihre eigene View weiter, niemals
  auf eine Adresse aus dem Formular ([Sichere Weiterleitungen](../../../../../eng-us/features/6.0/safe-redirects.md)).

## Praktische Übung

Auf der Schulungsinstallation, in `extension/mysite_hello`. Als Administrator angemeldet oder mit der Richtlinie für
Anonymous aus Lektion 6.3.

1. Erweitern Sie die View in `modules/hello/module.php`:

   ```php
   $ViewList['greet'] = array(
       'functions' => array( 'greet' ),
       'script' => 'greet.php',
       'params' => array( 'Name' ),
       'unordered_params' => array( 'times' => 'Times' ),
       'single_post_actions' => array( 'GreetButton' => 'Greet' ),
       'post_action_parameters' => array( 'Greet' => array( 'Name' => 'GreetName' ) ) );
   ```

2. Fügen Sie die Aktionsverarbeitung am Anfang von `modules/hello/greet.php` ein, nach dem einleitenden Kommentar:

   ```php
   $error = false;
   if ( $Module->isCurrentAction( 'Greet' ) )
   {
       $posted = trim( (string)$Module->actionParameter( 'Name' ) );
       if ( $posted === '' || mb_strlen( $posted ) > 40 )
       {
           $error = 'Please type a name of 1 to 40 characters.';
       }
       else
       {
           return $Module->redirectTo( '/hello/greet/' . rawurlencode( $posted ) );
       }
   }
   ```

   und übergeben Sie sie an das Template: `$tpl->setVariable( 'error', $error );`.
3. Fügen Sie das Formular in `design/standard/templates/hello/greet.tpl` ein:

   ```
   {if $error}<p class="error">{$error|wash}</p>{/if}
   <form method="post" action={'/hello/greet'|ezurl}>
       <label for="greet-name">Name</label>
       <input id="greet-name" type="text" name="GreetName" value="" />
       <input type="submit" name="GreetButton" value="Greet" />
   </form>
   ```

4. Prüfen Sie die Dateien mit `php -l` und `eztemplatecheck.php`; leeren Sie die Caches `ini`, `global_ini` und den
   Template-Cache.
5. Öffnen Sie `/hello/greet`, geben Sie einen Namen ein und klicken Sie auf **Greet**.
6. Senden Sie das Formular leer ab; lesen Sie die Fehlermeldung.
7. Sehen Sie sich den Seitenquelltext des Formulars an und suchen Sie das versteckte Feld `ezxform_token`.
8. Löschen Sie in den Entwicklerwerkzeugen des Browsers das versteckte Token-Feld und senden Sie das Formular dann
   ab: Lesen Sie die Seite „Formular abgelaufen“.
9. Committen Sie die Änderungen.

## Erwartetes Ergebnis

- Schritt 5 leitet zu `/hello/greet/<name>` weiter und begrüßt den Namen.
- Schritt 6 zeigt „Please type a name of 1 to 40 characters.“ über dem Formular.
- Schritt 7: ein `<input type="hidden" name="ezxform_token" ...>` innerhalb des Formulars.
- Schritt 8: die Seite „Formular abgelaufen“; nichts wird verarbeitet.
- Der Trainer prüft das Formular einer Person in den Entwicklerwerkzeugen des Browsers.

## Quiz

1. Was bewirkt `'single_post_actions' => array( 'GreetButton' => 'Greet' )`?
2. Welche Methode teilt der View mit, dass die Aktion `Greet` abgesendet wurde?
   A. `actionParameter()` B. `isCurrentAction()` C. `redirectTo()` D. nur `currentAction()`
3. Warum wird nach erfolgreichem Absenden weitergeleitet?
4. Wogegen schützt der Formular-Token?
5. Wo muss die Fehlermeldung escapt werden?

<details>
<summary>Antworten</summary>

1. Ein POST mit dem Feld `GreetButton` wird zur Aktion `Greet`.
2. **B.** `$Module->isCurrentAction( 'Greet' )`.
3. Damit ein Neuladen das Formular nicht erneut absendet (Post, Redirect, Get).
4. Gegen Cross-Site Request Forgery: Eine andere Website bringt den Browser dazu, ein Formular an Ihre Website
   abzusenden.
5. Im Template, dort, wo sie ausgegeben wird: `{$error|wash}`.

</details>

## Weiterführende Literatur

- [„Dieses Formular ist abgelaufen“](../../../../../eng-us/features/6.0/form-expired-page.md): Abweisungen durch den
  Formular-Token.
- [Sichere Weiterleitungen](../../../../../eng-us/features/6.0/safe-redirects.md): Weiterleitungsziele aus Benutzereingaben.
- [Sicherheitsstandard](../../../../../eng-us/standards/security.md): die Sicherheitsregeln des Projekts für Code.

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
