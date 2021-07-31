# Rekursivlisting von Fluxel für JTL-Shop 5+

[Download](https://github.com/fluxel-app/fluxel_rekursivliste/releases/download/v1/fluxel_rekursivliste.zip)

[Blogeintrag und Infos](https://www.fluxel.de/2021/07/29/fluxel-open-source-jtl-shop-plugins/)

Kompatibilität: JTL-Shop 5 oder höher

Diese Plugin zieht Ihre Artikel in die Oberkategorien des JTL-Shops.

## JTL-Wawi / Vorbereitung

Für die Funktion des Plugins werden zwei Funktionsattribute oder eigene Felder benötigt, die exakt folgendermaßen heißen müssen:

- Rekursion einschließen
- Rekursion ausschließen

Empfohlen wird als Datentyp die Checkbox.

Hier eine Beispielkonfiguration:

![Eigene Felder in JTL-Wawi](Eigene_Felder_JTL-Wawi.jpeg)

## Rekursion einschließen

Wenn das Feld gesetzt ist, werden Artikel aus den Unterkategorien in der Kategorie angezeigt.

## Rekursion ausschließen

Ist das Feld gesetzt ist, werden Artikel dieser Kategorie und den Unterkategorien nicht in den oberen Kategorien angezeigt.

## Bespiel:

- Kategorie 1 (Rekursion einschließen)
  - Kategorie 1.1
    - Artikel 1
  - Kategorie 1.2
    - Artikel 2
    - Artikel 3
    - Artikel 4
    - Kategorie 1.2.1 (Rekursion ausschließen)
      - Artikel 5
      - Artikel 6
      - Kategorie 1.2.1.1
        - Artikel 7

In dem Beispiel werden in Kategorie 1 Artikel 1 - 4 angezeigt.

## Status und Sicherheit

Das Plugin baut eine Liste aus Kategorie-Schlüssel zusammen und modifiziert die Bedingungen für die Artikelauswahl. Die Kategorie-Schlüssel werden in den Objekt-Cache geschrieben und haben somit nach der ersten Auswahl keinen Einfluss auf deine Shop-Performance.
