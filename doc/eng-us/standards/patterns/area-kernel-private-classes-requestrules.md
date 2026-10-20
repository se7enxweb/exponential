---
title: Patterns in kernel/private/classes/requestrules
audience: developer
category: standards
order: 521
summary: Every class, view, fetch function, operator and plugin point of kernel/private/classes/requestrules with its design patterns.
keywords: kernel/private/classes/requestrules, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/private/classes/requestrules

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/private/classes/requestrules` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (44)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `ezpRequestActionAllow` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:16` | allow: the request runs as usual; no later rule is asked. |
| `ezpRequestActionForbidden` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:34` | forbidden: the "access denied" page, HTTP 403 -- the same page a missing policy gives. |
| `ezpRequestActionLog` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:120` | log: writes a line to var/log/requestrules.log and lets the next rule decide. |
| `ezpRequestActionLogin` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:47` | login: an anonymous visitor is sent to user/login and back to the page after signing in. |
| `ezpRequestActionNotFound` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:25` | notfound: the "not found" page, HTTP 404. |
| `ezpRequestActionRedirect` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:68` | redirect: HTTP redirect. |
| `ezpRequestActionRedirectToAlias` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:86` | redirect_to_alias: HTTP redirect to the URL alias of the node shown, with the view's user parameters ((offset)/10); the query string is kept by the k… |
| `ezpRequestActionRewrite` | class implements ezpRequestRuleAction | – | `kernel/private/classes/requestrules/ezprequestruleactions.php:106` | rewrite: the kernel runs another URI instead, without a redirect; the visitor keeps the address they typed. |
| `ezpRequestConditionClass` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:112` | class: the content class identifier of the node shown: Conditions[class]=article,blog_post |
| `ezpRequestConditionFact` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:200` | fact:: any fact, including the ones an extension defines: Conditions[fact:customer_tier]=gold |
| `ezpRequestConditionGroup` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:154` | group: a user group (content object id) the user belongs to: Conditions[group]=12,13 |
| `ezpRequestConditionHeader` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:190` | header:: a request header, shell patterns; "*" means "sent at all": Conditions[header:X-Requested-With]=XMLHttpRequest |
| `ezpRequestConditionHost` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:184` | host: the host name the visitor used, shell patterns: *.example.com |
| `ezpRequestConditionIp` | class implements ezpRequestRuleCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:213` | ip: the client address is one of these addresses or networks, IPv4 or IPv6, CIDR: Conditions[ip]=10.0.0.0/8,192.168.1.20,2001:db8::/32 |
| `ezpRequestConditionMethod` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:178` | method: the HTTP method: GET, POST, HEAD |
| `ezpRequestConditionModule` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:80` | module: the module that runs, after URL translation: content, user, shop |
| `ezpRequestConditionModuleView` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:83` | module_view: module/view, shell patterns: content/view, content/*, user/login |
| `ezpRequestConditionNode` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:106` | node: the node shown, by id: Conditions[node]=2,43 |
| `ezpRequestConditionParam` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:96` | param:: a view parameter by its name in module.php: Conditions[param:NodeID]=2,43 |
| `ezpRequestConditionPolicy` | class implements ezpRequestRuleCondition, ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:160` | policy: the user has access to any of these policy functions, module/function: Conditions[policy]=!content/view_system_url. |
| `ezpRequestConditionRequestedVia` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:71` | requested_via: how the visitor reached the page -- system, alias, wildcard or index. |
| `ezpRequestConditionResolvedUri` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:77` | resolved_uri: the system address the kernel runs, shell patterns: content/view/full/* |
| `ezpRequestConditionRole` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:151` | role: the name of a role assigned to the user, directly or through a group: Conditions[role]=Editor |
| `ezpRequestConditionScheme` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:181` | scheme: http or https |
| `ezpRequestConditionSection` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:115` | section: the section of the node shown, by id or identifier: Conditions[section]=standard,6 |
| `ezpRequestConditionSiteAccess` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:175` | siteaccess: the current siteaccess name: Conditions[siteaccess]=site,bold |
| `ezpRequestConditionState` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:127` | state: an object state of the node shown, group/state identifiers: Conditions[state]=ez_lock/locked |
| `ezpRequestConditionSubtree` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:109` | subtree: the node shown is one of these nodes or below them: Conditions[subtree]=864 |
| `ezpRequestConditionUri` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:74` | uri: the address as typed (without siteaccess, leading slash or query), shell patterns: content/view/*, Fit-Healthy/* |
| `ezpRequestConditionUser` | class implements ezpRequestRuleCondition, ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:130` | user: anonymous or logged_in |
| `ezpRequestConditionUserId` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:145` | user_id: the current user's content object id |
| `ezpRequestConditionUserLogin` | class extends ezpRequestFactCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:148` | user_login: the current user's login, shell patterns |
| `ezpRequestConditionViewMode` | class extends ezpRequestFactCondition implements ezpRequestRuleStableCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:93` | view_mode: the ViewMode parameter of content/view: full, line, embed |
| `ezpRequestContext` | class | – | `kernel/private/classes/requestrules/ezprequestcontext.php:26` | What is known about one request when the request rules decide on it: a set of named facts. |
| `ezpRequestFactCondition` | class implements ezpRequestRuleCondition | – | `kernel/private/classes/requestrules/ezprequestruleconditions.php:21` | Base for the conditions that compare one fact with the values: the fact matches when it equals any value (case-insensitive), or, when the fact is a l… |
| `ezpRequestFactProvider` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:90` | Adds facts to every request's context before the rules run. |
| `ezpRequestRule` | class | [Factory and static factory methods](factory.md) | `kernel/private/classes/requestrules/ezprequestrule.php:33` | One request rule: when every condition matches, the action decides. |
| `ezpRequestRuleAction` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:57` | An action: what happens when every condition of a rule matches. |
| `ezpRequestRuleCondition` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:23` | A condition: one test on the facts of a request. |
| `ezpRequestRuleEngine` | class | [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/requestrules/ezprequestruleengine.php:32` | The request rules: an ordered list of rules, each a set of conditions and an action, asked about every module view the kernel is about to run. |
| `ezpRequestRuleKernel` | class | [Utility (static helper)](utility.md) | `kernel/private/classes/requestrules/ezprequestrulekernel.php:18` | Where the request rules meet the kernel: the facts of a real request (context()) and the response for a decision (moduleResult()). |
| `ezpRequestRuleProvider` | interface | [Interface (contract)](interface.md), [Strategy / handler](strategy-handler.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:76` | Adds rules from PHP: an extension whose rules are computed (from a database table, from another INI file) rather than written in requestrules.ini. |
| `ezpRequestRuleResult` | class | [Factory and static factory methods](factory.md) | `kernel/private/classes/requestrules/ezprequestruleresult.php:24` | What a rule decided. |
| `ezpRequestRuleStableCondition` | interface | [Interface (contract)](interface.md) | `kernel/private/classes/requestrules/ezprequestruleinterfaces.php:46` | Marks a condition whose answer is the same for every request that the page caches would answer with one stored page: it reads only what their keys ho… |
