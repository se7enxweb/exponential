---
title: "Lektion 12.2: Einen Test schreiben"
audience: trainer, developer
category: training
order: 122
summary: Die Teilnehmenden schreiben für ihren Template-Operator einen PHPUnit-Test ohne Datenbank, führen ihn mit dem Bootstrap des Kernels aus, lassen ihn absichtlich fehlschlagen und entscheiden, was in einer Erweiterung einen Test verdient. 50 Minuten.
keywords: entwickler, PHPUnit, testfall, TestCase, assertSame, test ohne datenbank, tests/bootstrap.php, tests von erweiterungen, test zuerst, regressionstest, mysite_initials, developers course, test case, DB-less test, extension tests, test first, regression test
type: help
---
# Lektion 12.2: Einen Test schreiben

Ein Test ist der günstigste Weg zu wissen, dass eine Änderung nichts kaputt gemacht hat, und die beste Dokumentation
dessen, was Code tun soll. In dieser Lektion schreiben die Teilnehmenden einen kleinen PHPUnit-Test für den
Template-Operator aus Lektion 7.5, führen ihn mit dem Test-Bootstrap des Kernels aus und sehen, wie er einen Fehler
findet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 12: Tests und Qualität](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 12.1](die-test-suiten.md) und [Lektion 7.5](../modul-07-erweiterungen/ein-template-operator.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Testklasse schreiben, die `PHPUnit\Framework\TestCase` erweitert, mit Testmethoden und Assertions;
- Code ohne Datenbank testen, indem sie ihn direkt aufrufen;
- eine Testdatei mit `vendor/bin/phpunit --bootstrap tests/bootstrap.php <file>` ausführen;
- eine Fehlermeldung lesen und den Code oder den Test korrigieren;
- entscheiden, was einen Test verdient: Verhalten, auf das sich anderer Code verlässt, und jeder behobene Fehler.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Tests und Continuous Integration](../../../../../eng-us/standards/testing-and-ci.md) und ein Kernel-Test eines
Template-Operators, zum Beispiel in `tests/tests/kernel/common/`.

1. **Aufbau** (4 Minuten). Eine Klasse mit dem Namen `<Something>Test`, die `TestCase` erweitert; öffentliche
   Methoden, die mit `test` beginnen; jede bereitet Eingaben vor, ruft den Code auf und prüft das Ergebnis mit
   `assertSame()`, `assertTrue()`, `assertCount()` und weiteren.
2. **Ohne Datenbank** (3 Minuten). Der Operator ist reines PHP: Rufen Sie `modify()` mit einem Wert und Parametern
   auf und lesen Sie den Wert zurück. Kein Start des Kernels, keine Datenbank, Millisekunden pro Test.
3. **Wo Tests liegen** (3 Minuten). Die Tests des Kernels liegen in `tests/tests/`. Eine eigene Erweiterung führt ihre
   Tests in ihrem eigenen Repository, zum Beispiel `extension/mysite_hello/tests/`, und führt sie mit dem Bootstrap
   des Kernels aus.
4. **Erst rot, dann grün** (3 Minuten). Machen Sie den Code absichtlich kaputt und sehen Sie zu, wie der Test mit
   einer klaren Meldung fehlschlägt. Ein Test, der nie fehlgeschlagen ist, hat nichts bewiesen.
5. **Was testen** (2 Minuten). Verhalten, auf das sich anderer Code verlässt, Grenzfälle (leere Eingabe,
   Multibyte-Text) und jeden Fehler: Schreiben Sie den Test, der den Fehler zeigt, bevor Sie ihn beheben.

### Häufige Fragen

**Was ist mit Code, der die Datenbank braucht?** Das Toolkit des Kernels hat `ezpDatabaseTestCase` für Tests mit dem
Tag `@group database`, die mit `tests/runtests.php` gegen eine Wegwerf-Datenbank laufen.

**Soll ich Templates testen?** Templates werden mit `eztemplatecheck.php` geprüft; Logik gehört in PHP, wo sie sich
testen lässt.

**Wie viele Assertions pro Test?** Ein Verhalten pro Testmethode; mehrere Assertions sind in Ordnung, wenn sie ein
Verhalten beschreiben.

### Stolperfallen

- **Tests, die voneinander abhängen.** Jeder Test muss allein und in beliebiger Reihenfolge funktionieren.
- **Tests, die die Live-Website brauchen.** Verwenden Sie Fixtures und Platzhalterobjekte; schreiben Sie niemals
  Tests, die echte Inhalte ändern.
- **Die Implementierung testen statt des Verhaltens.** Prüfen Sie die Ausgabe, nicht private Details, damit ein
  Refactoring den Test nicht kaputt macht.

## Praktische Übung

Auf der Entwicklungsinstallation, aus ihrem Wurzelverzeichnis.

1. Legen Sie `extension/mysite_hello/tests/MysiteInitialsTest.php` an:

   ```php
   <?php
   /**
    * Tests of the template operator mysite_initials.
    */
   use PHPUnit\Framework\TestCase;

   require_once __DIR__ . '/../autoloads/mysitehellooperators.php';

   class MysiteInitialsTest extends TestCase
   {
       private function initials( $text, $separator = '' )
       {
           $operator = new mysiteHelloOperators();
           $value = $text;
           $operator->modify( null, 'mysite_initials', array(), '', '', $value, array( 'separator' => $separator ) );
           return $value;
       }

       public function testTwoWords()
       {
           $this->assertSame( 'AL', $this->initials( 'Ada Lovelace' ) );
       }

       public function testSeparatorAndLowerCase()
       {
           $this->assertSame( 'G.B.H', $this->initials( 'grace brewster hopper', '.' ) );
       }

       public function testEmptyText()
       {
           $this->assertSame( '', $this->initials( '   ' ) );
       }

       public function testMultibyte()
       {
           $this->assertSame( 'ÉÖ', $this->initials( 'élan öl' ) );
       }
   }
   ```

2. Führen Sie ihn aus: `vendor/bin/phpunit --bootstrap tests/bootstrap.php extension/mysite_hello/tests/MysiteInitialsTest.php`.
3. Machen Sie den Operator absichtlich kaputt: Ersetzen Sie in `autoloads/mysitehellooperators.php`
   `mb_strtoupper( mb_substr(` durch `strtoupper( substr(` (behalten Sie die Klammern bei) und führen Sie den Test
   erneut aus.
4. Lesen Sie, welcher Test fehlschlägt und warum; stellen Sie den Code wieder her und führen Sie den Test erneut aus.
5. Fügen Sie einen eigenen Test für zwei Leerzeichen zwischen Wörtern hinzu (`'Ada  Lovelace'`). Entscheiden Sie, was
   das Ergebnis sein soll, und führen Sie ihn dann aus.
6. Committen Sie den Test.

## Erwartetes Ergebnis

- Schritt 2: `OK (4 tests, 4 assertions)`.
- Schritt 3: `testMultibyte` schlägt fehl: Byte-Funktionen zerschneiden die Multibyte-Buchstaben, und die Meldung
  zeigt erwarteten und tatsächlichen Wert.
- Schritt 4: Alle Tests laufen wieder durch.
- Schritt 5: Ein fünfter Test läuft mit `AL` durch (die Trennung an `\s+` verarbeitet mehrere Leerzeichen).
- Der Trainer fragt alle Teilnehmenden, wovor ihr neuer Test schützt.

## Quiz

1. Welche Klasse erweitert ein PHPUnit-Test?
2. Welche Assertion vergleicht Typ und Wert?
   A. `assertEquals()` B. `assertSame()` C. `assertTrue()` D. `assertCount()`
3. Warum ist der Operator-Test schnell?
4. Wann sollten Sie einen Test für einen Fehler schreiben?
5. Wo führt eine eigene Erweiterung ihre Tests?

<details>
<summary>Antworten</summary>

1. `PHPUnit\Framework\TestCase` (oder eine Toolkit-Klasse wie `ezpDatabaseTestCase` für Datenbanktests).
2. **B.** `assertSame()`.
3. Er ruft reines PHP auf, ohne den Kernel oder eine Datenbank zu starten.
4. Bevor Sie ihn beheben: Der Test zeigt den Fehler und belegt dann die Korrektur.
5. In ihrem eigenen Repository, zum Beispiel `extension/mysite_hello/tests/`.

</details>

## Weiterführende Literatur

- [Tests und Continuous Integration](../../../../../eng-us/standards/testing-and-ci.md).
- [Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md).
- [Programmierrichtlinien](../../../../../eng-us/standards/coding-standards.md).

## Verwandte Seiten

- [Modul 12: Tests und Qualität](README.md)
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
