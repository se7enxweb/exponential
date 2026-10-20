---
title: Eine alte Erweiterung bewerten, bevor Sie sie verwenden
audience: user, developer, designer, app-creator
category: extension-development
order: 170
summary: Eine Checkliste mit Befehlen, um eine Erweiterung zu beurteilen, die Sie nicht selbst geschrieben haben - Lizenz, PHP-8-Syntax, entfernte Funktionen, Zugriffsprüfungen, Escaping, SQL, Inline-Skripte und Datenbankänderungen.
keywords: bewerten, Prüfung, Audit, Review, alte Erweiterung, Checkliste, Sicherheit, Zugriffskontrolle, SQL-Injection, Inline-Skript, Qualität, evaluate, audit, review, old extension, checklist, php -l, php 8, security, access control, wash, sql injection, inline script, csp, quality
type: help
---
# Eine alte Erweiterung bewerten, bevor Sie sie verwenden

Teil 1, Kapitel 17. Eine Erweiterung läuft mit allen Rechten des Kernels: Sie kann jede Tabelle lesen, jede Datei, die
der Webserver lesen kann, und die Anfrage jedes Besuchers. Bevor Sie eine installieren, die Sie nicht selbst
geschrieben haben, oder sich entscheiden, eine zu übernehmen, nehmen Sie sich eine halbe Stunde für diese Checkliste.
Alle Befehle laufen auf einer **Entwicklungsinstallation**, mit der Erweiterung entpackt, aber noch nicht eingeschaltet.

## 1. Lizenz und Herkunft (5 Minuten)

- [ ] Es gibt eine Lizenzdatei, und sie erlaubt, was Sie vorhaben ([Lizenzen lesen, bevor Sie etwas wiederverwenden](lizenzen-vor-der-wiederverwendung-lesen.md)).
- [ ] `ezinfo.php` und `extension.xml` stimmen bei Name, Version und Lizenz überein.
- [ ] Sie wissen, woher sie stammt: ein Repository mit Historie, ein Packagist-Paket, eine Projektseite.

## 2. Lässt sie sich unter PHP 8 parsen? (2 Minuten)

```bash
find extension/<name> -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
```

Keine Ausgabe bedeutet, dass sich jede Datei parsen lässt. Jede ausgegebene Zeile ist eine Datei, die PHP 8 ablehnt;
typische Ursachen stehen in [PHP-8-Fehler, die alten Code stoppen](../teil-4-aktualisieren/php-8-fehler-die-alten-code-stoppen.md).

## 3. Entfernte und veraltete Funktionen (5 Minuten)

```bash
grep -rnE '\b(each|create_function|split|ereg|eregi|mysql_[a-z_]+|money_format|get_magic_quotes_gpc|utf8_decode|utf8_encode)\s*\(' extension/<name> --include=*.php
grep -rnE 'function\s+([A-Za-z_]+)\s*\(' extension/<name> --include=*.php | head -50
```

Die erste Suche findet Funktionen, die PHP 8 entfernt hat oder als veraltet markiert; für jede gibt es einen Ersatz in
[Entfernte PHP-Funktionen und ihr Ersatz](../teil-4-aktualisieren/entfernte-php-funktionen.md). Die zweite listet die
Methoden auf, damit Sie **Konstruktoren im alten Stil** erkennen: eine Methode mit demselben Namen wie ihre Klasse.
PHP 8 ruft eine solche Methode nicht mehr als Konstruktor auf, also wird das Objekt erzeugt, ohne eingerichtet zu
werden. Die Template-Operator-Erweiterung `str_replace`, im Kernel-Baum noch in ihrer ursprünglichen Form von 2005, ist
genau dieser Fall: Ihre Operatorklasse setzt ihre Operatorliste in `function MyStrReplaceOperator()`, die PHP 8 nie
aufruft, und die Template-Engine meldet „Object problem with operator 'ezstr_replace'“. Die Korrektur ist eine Zeile
und wird in [Konstruktoren im alten Stil und statische Aufrufe](../teil-4-aktualisieren/alte-konstruktoren-und-statische-aufrufe.md)
gezeigt.

## 4. Lässt sich jede Klasse laden? (2 Minuten, nachdem Sie sie in der Entwicklung eingeschaltet haben)

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/checkclasses.php --quiet-ok
```

`checkclasses.php` lädt jede Klasse der Installation in einem Kindprozess und nennt diejenigen, die PHP ablehnt. Fügen
Sie `--allow-root-user` hinzu, wenn Sie es als root ausführen.

## 5. Zugriffskontrolle (5 Minuten)

Öffnen Sie jede `modules/<module>/module.php`:

```bash
grep -n "ViewList\|'functions'\|FunctionList" extension/<name>/modules/*/module.php
```

- [ ] Jede View, die Daten anzeigt oder ändert, nennt einen Eintrag `'functions'`.
- [ ] `$FunctionList` deklariert diese Funktionen, damit sie über Rollen vergeben werden können.
- [ ] Views, die Daten ändern, handeln nur bei POST und prüfen das Formular-Token (siehe
      [ezformtoken und CSRF-Schutz](../teil-8-referenz/ezformtoken-und-csrf.md)).

Eine View ohne `'functions'` kann jeder öffnen, der die Website erreicht.

## 6. Escaping in Templates (5 Minuten)

```bash
grep -rnE '\{\$[a-zA-Z_.]+\}' extension/<name>/design | grep -v '|wash' | head -30
```

Variablen, die ohne `|wash` ausgegeben werden, sind ein Risiko für Cross-Site-Scripting, wenn ihr Wert von einem
Besucher stammen kann (ein View-Parameter, ein Formularfeld, ein Suchwort). Nicht jeder Treffer ist ein Fehler (eine
Zahl aus dem Kernel ist sicher), aber jeder verdient einen Blick. Siehe
[Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md).

## 7. SQL (5 Minuten)

```bash
grep -rnE '(query|arrayQuery)\s*\(' extension/<name> --include=*.php | head -30
```

Für jede Abfrage, die aus Variablen zusammengesetzt wird:

- [ ] Zeichenketten laufen durch `$db->escapeString()`,
- [ ] Zahlen werden mit `(int)` umgewandelt,
- [ ] oder besser: Der Code verwendet `eZPersistentObject` statt handgeschriebenem SQL.

## 8. Inline-Skripte und -Styles (2 Minuten)

```bash
grep -rnE '<script[^>]*>[^<]|on(click|change|submit|load)=|javascript:|style="' extension/<name>/design | head -30
```

Die Administration erzwingt eine Content Security Policy: Inline-Skripte, Handler-Attribute `on...=` und
`javascript:`-Links laufen dort nicht. Jeder Treffer ist Arbeit für [Teil 4](../teil-4-aktualisieren/README.md), bevor die
Administrationsseiten der Erweiterung funktionieren.

## 9. Was sie im System ändert (5 Minuten)

- [ ] `ls extension/<name>/settings/` und lesen Sie jede Datei: Welche Einstellungen ändert sie? Überschreibt sie eine
      Kernel-Klasse (Autoloads im Stil von `ezpkerneloverride`), ersetzt sie ein Modul oder fügt sie Event-Listener
      hinzu?
- [ ] `share/`, `sql/`, `update/`: Legt sie Tabellen an? Gibt es Upgrade-Skripte dafür?
- [ ] `cronjobs/`: Führt sie etwas nach Zeitplan aus?
- [ ] Sendet sie Mails, ruft sie externe Dienste auf, schreibt sie Dateien außerhalb von `var/`?

## 10. Entscheiden

| Ergebnis | Entscheidung |
|---|---|
| Lizenz in Ordnung, lässt sich parsen, Zugriffsprüfungen vorhanden, Escaping in Ordnung | Verwenden Sie sie; notieren Sie, was Sie geprüft haben |
| Lizenz in Ordnung, kleine Probleme mit PHP 8 oder CSP | Bringen Sie sie mit Teil 4 auf den neuesten Stand und bieten Sie die Korrekturen dann ihrem Autor an |
| Keine Zugriffsprüfungen, ungeschützte Ausgabe, SQL aus Anfragewerten | Verwenden Sie sie auf keiner Live-Website, bis das behoben ist |
| Keine Lizenz | Kopieren Sie sie nicht; übernehmen Sie nur die Idee |

Schreiben Sie Ihre Ergebnisse auf. Wenn Sie die Erweiterung übernehmen oder korrigieren, werden sie zum ersten Issue in
ihrem Tracker.

## Verwandte Seiten

- Zurück: [Verlinken oder kopieren: Quellen fair zitieren](verlinken-oder-kopieren.md)
- Weiter: [Schritt für Schritt von einer Erweiterung lernen](schritt-fuer-schritt-von-einer-erweiterung-lernen.md)
- [Die Upgrade-Checkliste](../teil-4-aktualisieren/die-upgrade-checkliste.md)
- [Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
