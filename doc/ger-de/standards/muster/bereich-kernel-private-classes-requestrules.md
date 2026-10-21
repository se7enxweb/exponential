---
title: Muster in kernel/private/classes/requestrules
audience: developer
category: standards
order: 521
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/private/classes/requestrules mit den Entwurfsmustern.
keywords: kernel/private/classes/requestrules, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/private/classes/requestrules

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/private/classes/requestrules` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (44)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `ezpRequestActionAllow` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:16` | allow: Die Anfrage läuft wie gewohnt; keine weitere Regel wird gefragt. |
| `ezpRequestActionForbidden` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:34` | forbidden: die Seite "Zugriff verweigert", HTTP 403 -- dieselbe Seite, die eine fehlende Richtlinie liefert. |
| `ezpRequestActionLog` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:120` | log: schreibt eine Zeile nach var/log/requestrules.log und lässt die nächste Regel entscheiden. |
| `ezpRequestActionLogin` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:47` | login: Ein anonymer Besucher wird zu user/login und nach der Anmeldung zurück zur Seite geschickt. |
| `ezpRequestActionNotFound` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:25` | notfound: die Seite "Nicht gefunden", HTTP 404. |
| `ezpRequestActionRedirect` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:68` | redirect: HTTP-Weiterleitung. |
| `ezpRequestActionRedirectToAlias` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:86` | redirect_to_alias: HTTP-Weiterleitung zum URL-Alias des angezeigten Knotens, mit den Benutzerparametern der Ansicht ((offset)/10); die Abfragezeichenkette behält der K… |
| `ezpRequestActionRewrite` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:106` | rewrite: Der Kernel führt stattdessen eine andere URI aus, ohne Weiterleitung; der Besucher behält die eingegebene Adresse. |
| `ezpRequestConditionClass` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:112` | class: der Inhaltsklassen-Bezeichner des angezeigten Knotens: Conditions[class]=article,blog_post |
| `ezpRequestConditionFact` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:200` | fact:: jeder Fakt, auch die, die eine Extension definiert: Conditions[fact:customer_tier]=gold |
| `ezpRequestConditionGroup` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:154` | group: eine Benutzergruppe (Inhaltsobjekt-ID), zu der der Benutzer gehört: Conditions[group]=12,13 |
| `ezpRequestConditionHeader` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:190` | header:: ein Anfrage-Header, Shell-Muster; "*" bedeutet "überhaupt gesendet": Conditions[header:X-Requested-With]=XMLHttpRequest |
| `ezpRequestConditionHost` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:184` | host: der Hostname, den der Besucher verwendet hat, Shell-Muster: *.example.com |
| `ezpRequestConditionIp` | class implements ezpRequestRuleCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:213` | ip: die Client-Adresse ist eine dieser Adressen oder Netze, IPv4 oder IPv6, CIDR: Conditions[ip]=10.0.0.0/8,192.168.1.20,2001:db8::/32 |
| `ezpRequestConditionMethod` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:178` | method: die HTTP-Methode: GET, POST, HEAD |
| `ezpRequestConditionModule` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:80` | module: das Modul, das nach der URL-Übersetzung läuft: content, user, shop |
| `ezpRequestConditionModuleView` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:83` | module_view: Modul/Ansicht, Shell-Muster: content/view, content/*, user/login |
| `ezpRequestConditionNode` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:106` | node: der angezeigte Knoten, nach ID: Conditions[node]=2,43 |
| `ezpRequestConditionParam` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:96` | param:: ein Ansichtsparameter mit seinem Namen in module.php: Conditions[param:NodeID]=2,43 |
| `ezpRequestConditionPolicy` | class implements ezpRequestRuleCondition, ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:160` | policy: Der Benutzer hat Zugriff auf eine dieser Richtlinienfunktionen, Modul/Funktion: Conditions[policy]=!content/view_system_url. |
| `ezpRequestConditionRequestedVia` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:71` | requested_via: wie der Besucher die Seite erreicht hat -- system, alias, wildcard oder index. |
| `ezpRequestConditionResolvedUri` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:77` | resolved_uri: die Systemadresse, die der Kernel ausführt, Shell-Muster: content/view/full/* |
| `ezpRequestConditionRole` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:151` | role: der Name einer dem Benutzer zugewiesenen Rolle, direkt oder über eine Gruppe: Conditions[role]=Editor |
| `ezpRequestConditionScheme` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:181` | scheme: http oder https |
| `ezpRequestConditionSection` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:115` | section: die Sektion des angezeigten Knotens, nach ID oder Bezeichner: Conditions[section]=standard,6 |
| `ezpRequestConditionSiteAccess` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:175` | siteaccess: der Name des aktuellen Siteaccess: Conditions[siteaccess]=site,bold |
| `ezpRequestConditionState` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:127` | state: ein Objektzustand des angezeigten Knotens, Bezeichner Gruppe/Zustand: Conditions[state]=ez_lock/locked |
| `ezpRequestConditionSubtree` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:109` | subtree: Der angezeigte Knoten ist einer dieser Knoten oder liegt darunter: Conditions[subtree]=864 |
| `ezpRequestConditionUri` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:74` | uri: die Adresse wie eingegeben (ohne Siteaccess, führenden Schrägstrich oder Abfrage), Shell-Muster: content/view/*, Fit-Healthy/* |
| `ezpRequestConditionUser` | class implements ezpRequestRuleCondition, ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:130` | user: anonymous oder logged_in |
| `ezpRequestConditionUserId` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:145` | user_id: die Inhaltsobjekt-ID des aktuellen Benutzers |
| `ezpRequestConditionUserLogin` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:148` | user_login: der Login des aktuellen Benutzers, Shell-Muster |
| `ezpRequestConditionViewMode` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:93` | view_mode: der Parameter ViewMode von content/view: full, line, embed |
| `ezpRequestContext` | class | – | `kernel/private/classes/requestrules/ezprequestcontext.php:26` | Was über eine Anfrage bekannt ist, wenn die Anfrageregeln über sie entscheiden: eine Menge benannter Fakten. |
| `ezpRequestFactCondition` | class implements ezpRequestRuleCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:21` | Basis für die Bedingungen, die einen Fakt mit den Werten vergleichen: Der Fakt passt, wenn er einem der Werte gleicht (ohne Beachtung der Groß-/Kleinschreibung), oder, wenn der Fakt eine L… |
| `ezpRequestFactProvider` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:90` | Fügt dem Kontext jeder Anfrage Fakten hinzu, bevor die Regeln laufen. |
| `ezpRequestRule` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/requestrules/ezprequestrule.php:33` | Eine Anfrageregel: Wenn jede Bedingung zutrifft, entscheidet die Aktion. |
| `ezpRequestRuleAction` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:57` | Eine Aktion: was geschieht, wenn alle Bedingungen einer Regel zutreffen. |
| `ezpRequestRuleCondition` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:23` | Eine Bedingung: ein Test auf die Fakten einer Anfrage. |
| `ezpRequestRuleEngine` | class | [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/requestrules/ezprequestruleengine.php:32` | Die Anfrageregeln: eine geordnete Liste von Regeln, jede eine Menge von Bedingungen und eine Aktion, befragt zu jeder Modulansicht, die der Kernel gleich ausführt. |
| `ezpRequestRuleKernel` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/private/classes/requestrules/ezprequestrulekernel.php:18` | Wo die Anfrageregeln auf den Kernel treffen: die Fakten einer echten Anfrage (context()) und die Antwort für eine Entscheidung (moduleResult()). |
| `ezpRequestRuleProvider` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:76` | Fügt Regeln aus PHP hinzu: eine Extension, deren Regeln berechnet werden (aus einer Datenbanktabelle, aus einer anderen INI-Datei), statt in requestrules.ini geschrieben zu sein. |
| `ezpRequestRuleResult` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/private/classes/requestrules/ezprequestruleresult.php:24` | Was eine Regel entschieden hat. |
| `ezpRequestRuleStableCondition` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:46` | Kennzeichnet eine Bedingung, deren Antwort für jede Anfrage gleich ist, die die Seiten-Caches mit einer gespeicherten Seite beantworten würden: sie liest nur, was ihre Schlüssel enth… |
