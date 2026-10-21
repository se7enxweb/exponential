---
title: "Lektion 10.3: Aktualisierungen mit ezupdate und git_manager"
audience: trainer, admin
category: training
order: 103
summary: Die Teilnehmenden verwenden die zwei Werkzeuge der Administration, mit denen sich eine Installation ohne Shell aktualisieren lässt, ezupdate (Setup > Aktualisierungen und Pakete) und git_manager (Setup > Git Manager), mit Prüfungen und Probeläufen, die nichts ändern, und entscheiden, welches Werkzeug zu welcher Installation passt. 45 Minuten.
keywords: website-administration, ezupdate, aktualisierungen und pakete, nach aktualisierungen suchen, vorschau (probelauf), aktualisierungen installieren, AllowUpdate, Composer, git_manager, Git Manager, jetzt abrufen, upstream, update/ezupdate, update/manage, site administration, Updates and packages, Check for updates, Preview (dry run), Install updates, Fetch now
type: help
---
# Lektion 10.3: Aktualisierungen mit ezupdate und git_manager

Nicht jeder Administrator hat eine Shell. Zwei Erweiterungen bringen das Aktualisieren in die Administration:
**ezupdate** führt Composer aus (nach Aktualisierungen suchen, Probelauf, abgesicherte Aktualisierung, Paketsuche), und
**git_manager** zeigt, wo ein Git-Checkout im Vergleich zu seinem Remote steht, und holt Änderungen oder wechselt
Zweige. Beide sind um Prüfungen herum gebaut, die zunächst nichts ändern. Diese Lektion vermittelt sie ausschließlich
mit diesen Prüfungen.

> **Hinweis**
> Beide Werkzeuge können den Code der Installation umschreiben. Verwenden Sie im Kurs nur die Aktionen, die diese
> Lektion nennt und die nichts ändern, und nur auf Ihrer eigenen Schulungsinstallation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 10: Upgrades, Erweiterungen und Pakete](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter im Hosting |
| Voraussetzungen | [Lektion 10.1](ein-upgrade-planen-und-durchfuehren.md); ein Administratorkonto; für die vollständige Übung eine Schulungsinstallation mit aktivem `ezupdate` und, wenn sie ein Git-Checkout ist, aktivem `git_manager` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was ezupdate und git_manager jeweils tun und zu welcher Installation sie passen;
- mit ezupdate nach Aktualisierungen suchen und eine Aktualisierung in der Vorschau ansehen, ohne etwas zu ändern;
- erklären, warum Aktualisieren und Installieren standardmäßig ausgeschaltet sind und welche Richtlinien eine Rolle
  braucht;
- die Upstream-Karte von git_manager lesen: aktuell, im Rückstand, voraus oder divergiert;
- die Sicherheitsregeln nennen, denen beide Werkzeuge folgen (zuerst sichern, Probelauf, nie ein erzwungener Push).

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [ezupdate: Aktualisierungen und Pakete in der Administration](../../../../../eng-us/features/6.0/extensions/ezupdate.md),
[git_manager: Deploy und Sicherung aus der Administration](../../../../../eng-us/features/6.0/extensions/git_manager.md) und
[Automatische Aktualisierungen mit ezupdate für Hoster](../../../hosting/betrieb/automatische-updates-mit-ezupdate.md).

1. **Zwei Arten von Installationen** (3 Minuten). Eine mit Composer verwaltete Installation (`composer.json`,
   `composer.lock`, `vendor/`) wird über Composer aktualisiert: ezupdate. Eine Installation, die ein Git-Checkout ist,
   wird über Git aktualisiert: git_manager. Die Liste der Erweiterungen (**Setup > Extensions**) kennzeichnet
   Git-Checkouts.
2. **ezupdate** (6 Minuten). **Setup > Aktualisierungen und Pakete** (`/update/dashboard`). Die **Übersicht** zeigt,
   wo Composer liegt, ob Aktualisieren und Installieren erlaubt sind, **Nach Aktualisierungen suchen**
   (`composer outdated`, ändert nichts), **Vorschau (Probelauf)**, **Aktualisierungen installieren** nach einer
   Bestätigung der Sicherung und **Letzte Läufe**. Aktualisierungen laufen im Hintergrund, eine nach der anderen, und
   die Ausgabe von Composer lässt sich live verfolgen. Danach werden die Autoloads neu erzeugt und die Caches geleert.
3. **Standardmäßig aus** (2 Minuten). `ezupdate.ini [UpdateSettings] AllowUpdate` und `AllowInstall` stehen auf
   `disabled`, bis jemand sie einschaltet (die Seite hat dafür **Aktualisierungen einschalten**). Richtlinien:
   `update/ezupdate` zum Ansehen, Suchen und für Probeläufe; `update/manage`, um die Installation zu ändern.
4. **git_manager** (6 Minuten). **Setup > Git Manager** (`/git_manager/dashboard`). Die Upstream-Karte zeigt das
   Remote, den Zweig, den letzten Abruf und ein Abzeichen (**aktuell**, **im Rückstand**, **voraus**,
   **divergiert**) mit den fehlenden Commits. **Jetzt abrufen** führt `git fetch --prune` aus und sonst nichts. Ein
   Push wird nie erzwungen. Jeder Abruf wird im Audit aufgezeichnet.
5. **Die Regeln** (3 Minuten). Zuerst sichern (Modul 9), Probelauf vor dem echten Lauf, die Ausgabe lesen, prüfen wie
   in Lektion 10.1.

### Häufige Fragen

**Gehört ezupdate zu jeder Installation?** Es ist eine Erweiterung (`se7enxweb/ezupdate`), die in `ActiveExtensions`
aktiviert wird. Sehen Sie auf Ihrer Schulungsinstallation unter **Setup** nach: Der Eintrag
**Aktualisierungen und Pakete** ist nur da, wenn sie aktiv ist.

**Kann ezupdate neue Erweiterungen installieren?** Ja, über **Pakete finden** und `composer require`, wenn
`AllowInstall` eingeschaltet ist. Zuerst ein Probelauf.

**Was bedeutet „divergiert“ in git_manager?** Die Installation hat Commits, die dem Remote fehlen, und das Remote hat
Commits, die der Installation fehlen. Holen Sie die Änderungen nicht blind; finden Sie heraus, wer auf dem Server
Commits gemacht hat.

### Stolperfallen

- **Im Kurs die Aktualisierungen einschalten.** Lassen Sie `AllowUpdate` ausgeschaltet; der Probelauf vermittelt
  alles, worauf es ankommt.
- **Eine Schulungsinstallation, die keines von beidem ist.** Eine aus einem Archiv gebaute Installation ist weder ein
  Git-Checkout noch auf dieselbe Weise vollständig mit Composer verwaltet. Zeigen Sie die Werkzeuge stattdessen auf der
  Installation am Projektor.
- **Composer als root ausführen.** Von root in `vendor/` und `var/` angelegte Dateien können die Website für den
  Benutzer des Webservers kaputt machen.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, wenn sie die Erweiterungen hat; folgen Sie andernfalls auf der
Installation des Trainers am Projektor. Jeder der folgenden Schritte liest nur.

1. Öffnen Sie **Setup** und prüfen Sie, ob **Aktualisierungen und Pakete** und **Git Manager** in der linken Spalte
   stehen. Notieren Sie, welche Werkzeuge Ihre Installation hat.
2. Öffnen Sie **Setup > Extensions** und stellen Sie fest, ob die Erweiterungen der Installation als Git-Checkouts
   gekennzeichnet sind.
3. Öffnen Sie **Setup > Aktualisierungen und Pakete**. Lesen Sie **Zusammenfassung** und **Composer**: wo Composer
   liegt, welches PHP es ausführt, ob Aktualisierungen erlaubt sind.
4. Klicken Sie auf **Nach Aktualisierungen suchen**. Lesen Sie die Liste der veralteten Pakete, falls es welche gibt.
5. Klicken Sie auf **Vorschau (Probelauf)**. Verfolgen Sie die Ausgabe und lesen Sie die Spalte Art: Welche Pakete
   sind *Installierbar*, und welche sind *Durch composer.json blockiert*?
6. Öffnen Sie **Letzte Läufe** und suchen Sie Ihren Probelauf mit seinem Ergebnis.
7. Lesen Sie die Entsprechung für die Shell und führen Sie sie aus, wenn Sie eine Shell haben:

   ```bash
   php extension/ezupdate/bin/php/ezupdate.php status
   php extension/ezupdate/bin/php/ezupdate.php outdated
   ```

8. Ist die Installation ein Git-Checkout mit git_manager: Öffnen Sie **Setup > Git Manager** und lesen Sie die
   Upstream-Karte: Remote, Zweig, letzter Abruf und Abzeichen. Klicken Sie auf **Jetzt abrufen** und lesen Sie das
   Abzeichen erneut.
9. Notieren Sie für Ihre Schulungsinstallation, welches Werkzeug Sie für eine echte Aktualisierung verwenden würden und
   warum.

## Erwartetes Ergebnis

- Schritt 4 listet veraltete Pakete auf oder meldet, dass die Installation aktuell ist; nichts wurde geändert.
- Schritt 5 zeigt die Ausgabe des Probelaufs von Composer mit installierbaren und blockierten Paketen; nichts wurde
  geändert.
- Schritt 8 zeigt ein Abzeichen und nach **Jetzt abrufen** die aktualisierte Zeit des letzten Abrufs; kein Code wurde
  geändert.
- Der Trainer prüft die Antwort aus Schritt 9 bei zwei Teilnehmenden: mit Composer verwaltete Installation, ezupdate;
  Git-Checkout, git_manager; eine Shell bleibt für beide die Rückfallebene.

## Quiz

1. Welche Schaltfläche von ezupdate ändert nichts?
   A. **Aktualisierungen installieren** B. **Nach Aktualisierungen suchen** C. **Aktualisierungen einschalten**
   D. **Erneut ausführen** bei einem echten Lauf
2. Warum kann eine frische Installation nicht sofort aus der Administration aktualisiert werden?
3. Welche Richtlinie erlaubt einer Rolle, anzusehen, zu suchen und Probeläufe zu machen, aber nicht, die Installation
   zu ändern?
4. Was macht **Jetzt abrufen** in git_manager?
5. Das Upstream-Abzeichen zeigt **divergiert**. Was tun Sie, bevor Sie Änderungen holen?

<details>
<summary>Antworten</summary>

1. **B.** Sie führt `composer outdated` aus
   ([ezupdate, So läuft eine Aktualisierung ab](../../../../../eng-us/features/6.0/extensions/ezupdate.md#how-an-update-goes)).
2. `AllowUpdate` und `AllowInstall` sind ausgeschaltet, bis ein Administrator sie einschaltet.
3. `update/ezupdate`; um die Installation zu ändern, ist `update/manage` nötig.
4. `git fetch --prune` auf dem Remote und sonst nichts: kein Pull, Merge, Reset oder Checkout.
5. Vor allem anderen herausfinden, welche Commits nur auf der Installation existieren (wer auf dem Server Commits
   gemacht hat und warum).

</details>

## Weiterführende Literatur

- [ezupdate: Aktualisierungen und Pakete in der Administration](../../../../../eng-us/features/6.0/extensions/ezupdate.md).
- [git_manager: Deploy und Sicherung aus der Administration](../../../../../eng-us/features/6.0/extensions/git_manager.md).
- [Mit Git aktualisieren](../../../mit-git-aktualisieren.md): einen Git-Checkout in einer Shell aktualisieren.
- [Automatische Aktualisierungen mit ezupdate für Hoster](../../../hosting/betrieb/automatische-updates-mit-ezupdate.md).

## Verwandte Seiten

- [Modul 10: Upgrades, Erweiterungen und Pakete](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
