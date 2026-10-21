# Änderungen an der Suchindizierung von Beziehungen

Mit dem Release 2018.06 hat Exponential die Art geändert, wie Beziehungen indiziert werden. Bisher wurden die Metadaten
der Beziehungen rekursiv geladen, das heißt, auch Beziehungen von Beziehungen usw. wurden einbezogen. Dieses Verhalten
wurde so geändert, dass nur Objekte bis zu einer Ebene Tiefe einbezogen werden, sodass nur direkte Beziehungen
berücksichtigt werden.

Die Änderung wurde vorgenommen, um verschiedene Rekursionsprobleme bei zirkulären Beziehungen zu vermeiden. Außerdem
ermöglicht das neue Verhalten bessere (relevantere) Suchergebnisse.

Betroffen sind die Kernel-Datentypen eZObjectRelationType und eZObjectRelationListType.

Haben Sie einen eigenen Datentyp, der Beziehungen zu anderen Objekten speichert, können Sie ihn ähnlich verhalten
lassen. Fügen Sie Ihrer Datentyp-Klasse einfach Folgendes hinzu:
```php
    public function isRelationType()
    {
        return true;
    }
```
