# Statisches Veröffentlichen: Fehlerbehebung

Diese Anleitung ist für Administratoren, deren Build, Veröffentlichung oder GitHub-Pages-Site nicht tut, was sie
erwarten. Sie listet die Symptome nach Abschnitt, mit Ursache und Lösung, und die Befehle, die zeigen, was geschehen
ist.

[Anleitungen](README.md) · Verwandt: [Wenn eine Veröffentlichung fehlschlägt](../hilfe/statisches-veroeffentlichen/wenn-eine-veroeffentlichung-fehlschlaegt.md),
[Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md)

## Was Sie am Ende haben

Die Ursache eines fehlgeschlagenen Builds, Pushs oder einer Seite, gefunden aus Bericht und Status und ohne Raten
behoben.

## Zuerst nachsehen

```bash
./console exp:static:targets                       # jedes Ziel, seine Einstellungen geprüft
./console exp:static:status github-public          # Repository, Zugangsdaten lesbar oder nicht, die letzten Läufe
./console exp:static:diff github-public --verify --check-links --mount=/my-site/
./console exp:static:github status github-public   # GitHub Pages, die Domain, HTTPS, der letzte Pages-Build
```

In der Administration hat jeder Job seine Seite (`/staticpublish/log/<run-id>`) mit dem Bericht, und der Verlauf
listet jede Veröffentlichung mit ihrem Push-Status.

## Einstellungen

| Symptom | Ursache und Lösung |
|---|---|
| `Static publishing is switched off` | `[StaticPublishSettings] Enabled=enabled` setzen |
| `TargetList[] names no target` | Die Id in `TargetList[]` aufnehmen; der Block `[Target_<id>]` allein wird nicht gebaut |
| Der Siteaccess wird abgelehnt | Es ist ein Administrations- oder Bearbeitungs-Siteaccess; nennen Sie einen Front-Siteaccess |
| `Remote` wird abgelehnt | Es enthält ein Passwort oder Token; das Geheimnis gehört nach Setup > Geheimnisse und wird in `Credential` benannt |

## Der Build

| Symptom | Ursache und Lösung |
|---|---|
| `No page was exported` | Der Server kann die Site unter `SiteURL` nicht abrufen; `CrawlURL` auf eine erreichbare Adresse setzen |
| `refused: the response set the session cookie` | Die Seite startet eine Session für anonyme Besucher (ein Formular, ein Warenkorb, eine Registrierung); mit `ExcludePaths[]` auslassen oder ändern |
| `refused: debug output` | Debug-Ausgaben sind für die Adresse des Builds eingeschaltet; dort ausschalten |
| Seiten `skipped` | Sie existieren nur mit einer Query, oder ihr Pfad kann nicht auf jedem System ein Dateiname sein |
| Dateien `could not be collected` | Die Site gibt sie anonymen Besuchern nicht heraus; ihre Links führen zur Live-Site (`files_failed` in `--format=json`) |
| `another process works on the target` | Ein Build, eine Veröffentlichung, ein Zurücknehmen oder ein Push dieses Ziels läuft; warten |
| Ein Build ist sehr groß | `Assets=referenced` kopiert nur, was Seiten nennen; Sektionen auslassen, die nicht öffentlich sein müssen |

## Die Veröffentlichung

| Symptom | Ursache und Lösung |
|---|---|
| Push-Status `pending`, Anmeldung fehlgeschlagen | Das Geheimnis fehlt, ist abgelaufen oder hat keinen Schreibzugriff; erneuern, dann `exp:static:push` |
| `pending` mit einem Größengrund | Der Build ist größer als `MaxSiteBytes` oder hat eine Datei über `MaxFileBytes`; verkleinern |
| `pending`, `rejected (fetch first)` | Der Branch hat Commits, die nicht von hier veröffentlicht wurden; das statische Veröffentlichen erzwingt nie. Committen Sie nicht von Hand in den Veröffentlichungs-Branch ([Repositorys und Zugangsdaten](statisches-veroeffentlichen-repositorys-und-zugangsdaten.md)) |
| `the secret ... is not set` | Unter Setup > Geheimnisse hinzufügen oder `token:env:VARIABLE` verwenden |
| Der Ordner oder `.git` wird abgelehnt | Er gehört einem anderen Systembenutzer; die Befehle als Eigentümer der Dateien der Site ausführen |
| Ein Token wird für die Workflow-Datei abgelehnt | `Delivery=actions` pusht `.github/workflows/`; das Token braucht die Berechtigung Workflows |

## Auf GitHub Pages

| Symptom | Ursache und Lösung |
|---|---|
| 404 auf `github.io` | Pages ist nicht auf den Branch gestellt (oder auf GitHub Actions bei `Delivery=actions`), oder der Pages-Build läuft noch |
| CSS und Bilder fehlen unter `/<repository>/` | `Links=base-path` mit falschem `BasePath`; `Links=relative` verwenden oder korrigieren |
| CSS fehlt unter einer eigenen Domain | `BasePath` ist nicht leer; leeren, bauen und veröffentlichen |
| Die eigene Domain geht nach einem Push verloren | `CustomDomain` ist nicht gesetzt, also wird kein `CNAME` geschrieben |
| Noch kein HTTPS | GitHub stellt das Zertifikat aus, nachdem es den DNS sieht; warten, dann erneut `exp:static:github setup` |
| Ein privates Repository liefert nichts aus | GitHub Pages liefert private Repositorys nur in kostenpflichtigen Tarifen aus |

## Nächster Schritt

[Statisches Veröffentlichen: eigene Domains und DNS](statisches-veroeffentlichen-eigene-domains-und-dns.md).
