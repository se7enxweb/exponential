---
title: Reaktion auf KI-Vorfälle
audience: ai-agents, admin, businesses, project-manager
category: ai-agents
order: 86
summary: Was zu tun ist, wenn sich KI auf einer Exponential-Installation falsch verhält - ein Entwurf, der versteckten Anweisungen gefolgt ist, unerwartete Kosten, ein durchgesickerter Schlüssel, ein Agent, der das Falsche geschrieben hat - mit den Kontrollen, die Sie zuerst verwenden (Hauptschalter, Veto, Konto, Widerruf von Schlüsseln und Tokens), wie Sie im Audit-Trail und in den Logs herausfinden, was geschehen ist, und wie Sie sich erholen.
keywords: Reaktion auf Vorfälle, KI-Vorfall, ausschalten, Notschalter, Schlüssel widerrufen, Konto deaktivieren, Prompt-Injection-Vorfall, Kostensprung, durchgesickerter Schlüssel, Audit-Trail, Wiederherstellung, Papierkorb, Versionen, incident response, AI incident, switch off, kill switch, revoke key, disable account, cost spike, leaked key, recovery, trash, versions
type: help
---
# Reaktion auf KI-Vorfälle

Diese Seite richtet sich an Administratoren und Führungskräfte, die handeln müssen, wenn mit dem Assistenten oder einem Agenten
etwas schiefgeht. Legen Sie sie dort ab, wo man sie findet, bevor man sie braucht. Für Vorfälle im Allgemeinen siehe
[Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md).

## Inhalt

1. [Zuerst: stoppen](#zuerst-stoppen)
2. [Dann: herausfinden, was geschehen ist](#dann-herausfinden-was-geschehen-ist)
3. [Wiederherstellen](#wiederherstellen)
4. [Typische Vorfälle](#typische-vorfälle)
5. [Danach](#danach)

## Zuerst: stoppen

| Was betroffen ist | Stoppen Sie es mit |
|---|---|
| Der Assistent, überall | `[AISettings] Enabled=disabled` ([Der Hauptschalter und der Veto-Hook](../expai/hauptschalter-und-veto.md)) |
| Der Assistent, auf einem Siteaccess | `[AISettings] DisabledSiteAccesses[]` |
| Der Assistent, für einige Inhalte | Eine Veto-Regel für die Sektion, Klasse oder den Teilbaum |
| Ein Anbieter | Ihn aus `[ProviderSettings] Providers[]` entfernen, oder eine Veto-Regel auf `Providers[]` |
| Ein Agent | Sein Konto deaktivieren: Jeder Schlüssel und jedes Token eines deaktivierten Eigentümers hört sofort auf |
| Ein Schlüssel oder Token | Ihn widerrufen (Mein Konto > API-Zugang, Setup > oAuth-Verwaltung > API-Schlüssel oder `expsession::tokenRevoke`) |

Das Ausschalten des Assistenten hält eingereihte Anfragen an; sie laufen erst weiter, wenn er wieder eingeschaltet ist.

## Dann: herausfinden, was geschehen ist

| Frage | Wo |
|---|---|
| Welche KI-Anfragen liefen, mit welchem Modell, für wen? | Der Audit-Kanal `ai`: `./console exp:audit tail --channel=ai` und EI: Log |
| Welche Entwürfe hat der Assistent geschrieben oder veröffentlicht? | Ereignisse `content.expai_draft.*`, die Kennzeichnung an Versionen, Meine Entwürfe |
| Was hat ein Agentenkonto geändert? | Der Audit-Trail, gefiltert auf dieses Konto; Schreibzugriffe über expservices als `service.<domain>.<method>` |
| Wurde ein Schlüssel oder Token verwendet oder abgelehnt? | `access.apikey.*`, `access.expservices.token.*` |
| Wurden Einstellungen oder Rollen geändert? | `system.setting.write`, `access.role.*` |
| Sind Fehler aufgetreten? | `./console exp:logs --tail=var/log/error.log` oder Setup > Logdateien |

Prüfen Sie die Audit-Dateien, bevor Sie sich auf sie verlassen: `./console exp:audit verify --channel=ai`
([Der Audit-Trail für KI](audit-trail-fuer-ki.md)).

## Wiederherstellen

- **Entwürfe des Assistenten**: Verwerfen Sie sie; nichts wurde veröffentlicht, sofern es kein Mensch gesendet hat.
- **Veröffentlichte Inhalte**: Stellen Sie eine frühere Version des Objekts wieder her oder holen Sie es aus dem Papierkorb
  ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)).
- **Angehaltene eingereihte Jobs**: Setzen Sie sie unter EI: Budgets fort, sobald die Ursache behoben ist, oder lassen Sie sie
  ablaufen.
- **Schlüssel**: Erstellen Sie neue Schlüssel für die Agenten, die sie brauchen, mit den engsten Geltungsbereichen.

## Typische Vorfälle

| Vorfall | Wahrscheinliche Ursache | Reaktion |
|---|---|---|
| Ein Entwurf enthält Text, der wie eine Anweisung klingt, oder Änderungen, nach denen niemand gefragt hat | Das Modell ist in Inhalten verstecktem Text gefolgt | Den Entwurf verwerfen; den Quellinhalt finden; Web- und Dienstwerkzeuge für diesen Redakteur bis zur Prüfung ausgeschaltet lassen ([Nicht vertrauenswürdige Daten und Prompt-Injection](../agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)) |
| Die Kosten springen | Eine große Kette oder ein Massenlauf, eine Schleife, eine falsche Modellwahl | EI: Budgets und EI: Log zeigen die Anfragen; die Grenzen senken; ein Budget der Website setzen ([Kostenkontrolle](kostenkontrolle.md)) |
| Ein Schlüssel taucht in einem Chat, einem Log oder einem Repository auf | Falsch behandelte Zugangsdaten | Sofort widerrufen, einen neuen erstellen, das Audit auf seine Verwendung prüfen |
| Ein Agent hat falsche Inhalte angelegt oder geändert | Zuordnungsfehler oder irregeleitetes Modell | Das Konto deaktivieren, Versionen wiederherstellen, den korrigierten Ablauf auf einer Testinstallation proben |
| Ein Anbieter scheitert bei vielen Anfragen | Ausfall, widerrufener Schlüssel, kein Guthaben | Der Audit-Alarm für viele gescheiterte Anfragen; der Rückfall auf den Standard der Website; EI: Models prüfen |

## Danach

- Schreiben Sie auf, was geschehen ist, was betroffen war und was sich geändert hat.
- Aktualisieren Sie die Richtlinie zur KI-Nutzung und die Checklisten, wenn eine Regel gefehlt hat
  ([Eine Richtlinie zur KI-Nutzung](eine-richtlinie-zur-ki-nutzung.md)).
- Informieren Sie die betroffenen Redakteure und sagen Sie ihnen, worauf sie in Entwürfen achten sollen.

## Verwandte Seiten

- [Rollen und minimale Rechte](rollen-und-minimale-rechte.md)
- [Zugangsdaten für Agenten](../agentische-ablaeufe/zugangsdaten-fuer-agenten.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
