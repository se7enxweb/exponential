---
title: Muster in kernel/classes/requestclassifier
audience: developer
category: standards
order: 512
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/requestclassifier mit den Entwurfsmustern.
keywords: kernel/classes/requestclassifier, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/requestclassifier

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/requestclassifier` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (12)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expRequestClassifier` | class | [Singleton / instance()](singleton.md) | `kernel/classes/requestclassifier/exprequestclassifier.php:29` | Der Anfrageklassifizierer: unterscheidet eine Sondierung, eine Traversierung, eine Injektion, einen Scanner oder eine Häufung von Not-found-Antworten von einer gewöhnlichen Anfrage und schreibt eine… |
| `expRequestContext` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/requestclassifier/exprequestcontext.php:20` | Was der Anfrageklassifizierer betrachtet: eine Anfrage, so wie sie eingegangen ist. |
| `expRequestPatternRuleSet` | abstract class implements expRequestRuleSetInterface | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/classes/requestclassifier/exprequestpatternruleset.php:29` | Ein Regelsatz aus regulären Ausdrücken: einer pro Regel, alle zu einem kombinierten Ausdruck kompiliert, sodass eine Anfrage einen preg_match() pro Subj… |
| `expRequestRuleSetInjection` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesetinjection.php:21` | Kategorie "injection": Signaturen von SQL-, Befehls-, Template- (SSTI) und Skript-Injection (XSS) in der Abfrage oder im Pfad, einmal dekodiert (%3Cscript in de… |
| `expRequestRuleSetInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/requestclassifier/exprequestrulesetinterface.php:24` | Ein Regelsatz des Anfrageklassifizierers (settings/requestclassifier.ini, RuleSets[] und [RuleSet-] Class=). |
| `expRequestRuleSetProbes` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesetprobes.php:25` | Kategorie "probe": eine Anfrage nach etwas, das eine Exponential-Website nicht hat und nach dem ein Scanner sucht: WordPress, versteckte Dateien (.env, .git), Datenbank-t… |
| `expRequestRuleSetScanner` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesetscanner.php:18` | Kategorie "scanner": der User Agent eines bekannten Angriffs- oder Erkundungswerkzeugs (sqlmap, nikto, nuclei ...). |
| `expRequestRuleSetThreshold` | class implements expRequestRuleSetInterface | – | `kernel/classes/requestclassifier/exprequestrulesetthreshold.php:22` | Kategorie "threshold", Regel "404-rate": mehr als Max404PerMinute Antworten "nicht gefunden" (404 und 410, siehe settings/requestclassifier.ini [ThresholdSetting… |
| `expRequestRuleSetTraversal` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesettraversal.php:17` | Kategorie "traversal": ein Pfad, der versucht, aus dem Web-Root herauszuklettern (../, ..\), kodiert (%2e%2e, beliebige Schreibweise) oder doppelt kodiert (%252e), und Null-Bytes… |
| `expRequestThresholdCounter` | class | – | `kernel/classes/requestclassifier/exprequestthresholdcounter.php:25` | Zählt Ereignisse pro Schlüssel in festen Zeitfenstern (standardmäßig eine Minute), in kleinen JSON-Dateien, die sich alle Prozesse der Installation teilen: die des Webservers… |
| `expRequestVerdict` | class | – | `kernel/classes/requestclassifier/exprequestverdict.php:16` | Was der Anfrage-Klassifizierer gefunden hat: die Kategorie (probe, traversal, injection, scanner, threshold oder eine, die der Regelsatz einer Extension hinzufügt), die Regel, wie… |
| `expRequestVerdictListenerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Beobachter / Listener / Ereignis](beobachter.md) | `kernel/classes/requestclassifier/exprequestverdictlistenerinterface.php:18` | Wird über jedes Urteil des Anfrageklassifizierers informiert, nachdem dessen security.log-Zeile geschrieben wurde (settings/requestclassifier.ini [ClassifierSettings] Listen… |
