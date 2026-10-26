---
title: Patterns in kernel/classes/requestclassifier
audience: developer
category: standards
order: 512
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/requestclassifier with its design patterns.
keywords: kernel/classes/requestclassifier, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/requestclassifier

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/requestclassifier` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (12)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expRequestClassifier` | class | [Singleton / instance()](singleton.md) | `kernel/classes/requestclassifier/exprequestclassifier.php:29` | The request classifier: tells a probe, a traversal, an injection, a scanner or a burst of not-found answers from an ordinary request, and writes one… |
| `expRequestContext` | class | [Factory and static factory methods](factory.md) | `kernel/classes/requestclassifier/exprequestcontext.php:20` | What the request classifier looks at: one request, as it came in. |
| `expRequestPatternRuleSet` | abstract class implements expRequestRuleSetInterface | [Template Method / abstract base class](template-method.md) | `kernel/classes/requestclassifier/exprequestpatternruleset.php:29` | A rule set made of regular expressions: one per rule, all of them compiled into one combined expression, so a request costs one preg_match() per subj… |
| `expRequestRuleSetInjection` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesetinjection.php:21` | Category "injection": signatures of SQL, command, template (SSTI) and script (XSS) injection in the query or the path, decoded once (%3Cscript in the… |
| `expRequestRuleSetInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/requestclassifier/exprequestrulesetinterface.php:24` | A rule set of the request classifier (settings/requestclassifier.ini, RuleSets[] and [RuleSet-] Class=). |
| `expRequestRuleSetProbes` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesetprobes.php:25` | Category "probe": a request for something an Exponential site does not have and a scanner looks for: WordPress, hidden files (.env, .git), database t… |
| `expRequestRuleSetScanner` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesetscanner.php:18` | Category "scanner": the user agent of a known attack or discovery tool (sqlmap, nikto, nuclei ...). |
| `expRequestRuleSetThreshold` | class implements expRequestRuleSetInterface | – | `kernel/classes/requestclassifier/exprequestrulesetthreshold.php:22` | Category "threshold", rule "404-rate": more than Max404PerMinute not-found answers (404 and 410, see settings/requestclassifier.ini [ThresholdSetting… |
| `expRequestRuleSetTraversal` | class extends expRequestPatternRuleSet | – | `kernel/classes/requestclassifier/exprequestrulesettraversal.php:17` | Category "traversal": a path that tries to climb out of the web root (../, ..\), encoded (%2e%2e, any case) or double-encoded (%252e), and null bytes… |
| `expRequestThresholdCounter` | class | – | `kernel/classes/requestclassifier/exprequestthresholdcounter.php:25` | Counts events per key in fixed time windows (a minute by default), in small JSON files shared by every process of the installation: the web server's… |
| `expRequestVerdict` | class | – | `kernel/classes/requestclassifier/exprequestverdict.php:16` | What the request classifier found: the category (probe, traversal, injection, scanner, threshold, or one an extension's rule set adds), the rule, how… |
| `expRequestVerdictListenerInterface` | interface | [Interface (contract)](interface.md), [Observer / listener / event](observer.md) | `kernel/classes/requestclassifier/exprequestverdictlistenerinterface.php:18` | Told about every verdict of the request classifier after its security.log line is written (settings/requestclassifier.ini [ClassifierSettings] Listen… |
