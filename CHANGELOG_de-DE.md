# 1.2.1
- Plugin-Name als Namespace für eigene Templates verwenden: es gibt Fehler auf 6.4.5.0 für nicht auffindbare Templates
- Zeige niemals eine Bewertung an, wenn die Bewertung im Shop deaktiviert ist
- Sammle alle möglichen propertyGroups jedes Produkts zum Vergleich: wir haben einige verschiedene Eigenschaften. Der Kunde sollte immer alle Eigenschaften der zu vergleichenden Produkte sehen

# 1.2.0
- [Issue-20] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/20) Fixes Cross-Sellings-Vergleich funktioniert nicht mit Produktvarianten
- [Issue-19] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/19) Neue Plugin-Konfiguration hinzugefügt, um Attribute auszublenden (Beschreibung/Preis/Bewertung/Hersteller oder den ganzen Abschnitt)

# 1.1.0
- Shopware 6.4 Kompatibilität
- [Issue-14] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/14) Behebt, dass der Cross-Sellings-Vergleich nicht mit Produktvarianten funktioniert
- Anzeige des Wertes der Eigenschaftsoption der Variante in der Vergleichsliste
- [Issue-15] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/15) Neue Plugin-Konfiguration hinzugefügt, um zwischen "show all/selected properties" umzuschalten
- Kleinere Probleme beheben

# 1.0.6
- [Issue-11] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/11) Korrigieren Sie die Import-SCSS-Syntax.
- Kleiner Refactor auf der `base.html.twig` des Plugins

# 1.0.5
- Kleine Korrektur für Handle-Click-Ereignisse.
- [Issue-9] (https://github.com/FriendsOfShopware/FroshProductCompare/issues/9) Fügen Sie die Plugin-Konfiguration `showIconOnly` hinzu, die nur die Schaltfläche AddToCompare mit nur einem Symbol (ohne Text) anzeigt.

# 1.0.4
- Use `HttpClient` instead of `StoreApiClient`.

# 1.0.3
- Fix Cross-Selling funktioniert nach der Installation des Plugins nicht.

# 1.0.2
- Shopware 6.3 Kompatibilität

# 1.0.1 - Freigabe im Shopware-Store als Plugin von Frosh
- Entfernen einiger überflüssiger LoC.
- Hinzufügen von robot noindex meta auf der Vergleichsseite
- Aktualisieren Sie den Namen des Plugins und fügen Sie einige Anforderungen des Shops hinzu.

# 1.0.0 - Erste Freigabe
- Hinzufügen der Schaltfläche "Zum Vergleich hinzufügen" in der Produktkarte und im Produktdetail.
- Schaltfläche "Float" unten links auf der Seite mit Zähler für "hinzugefügte Produkte" hinzufügen.
- Hinzufügen von "Zur Vergleichsliste hinzugefügte Produkte außerhalb des Bildschirms", wenn Sie auf die Schaltfläche "Float" klicken.
- Fügen Sie "Vergleichsseite" hinzu, um die Tabelle "Produkte vergleichen" anzuzeigen. Es können bis zu 4 Produkte in die Liste aufgenommen werden.
- Schaltfläche "Drucken" auf der Vergleichsseite hinzufügen, um die Vergleichstabelle zu drucken.
- Schaltfläche "Alle löschen" hinzufügen, um alle Produkte in der Liste zu löschen.
- Schalter "Vergleichbar" in "Verwaltung > Produktdetails > Registerkarte "Cross-Selling"" hinzufügen, um Cross-Selling-Produkte als Vergleichstabelle anzuzeigen.
