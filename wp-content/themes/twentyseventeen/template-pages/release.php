<?php
/* Template Name: Idealhit Release Report Page */
/**
 * Created by PhpStorm.
 * User: ywang
 * Date: 2018/5/27
 * Time: 18:47
 */
/**
 * 判断，如果没有登陆，那么直接跳转到 Login 页面
 */
/*
if(!is_user_logged_in()){
    //重定向浏览器
    header("Location: /do-login.php");
    //确保重定向后，后续代码不会被执行
    exit;
}
*/
?>
<!DOCTYPE html>
<html>
<head>
    <title>Release Report - GoodOne Rechnungsplattform</title>
    <style>
        html, body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px; }
        body { padding-bottom: 100px; }
        .release-page-ul { -webkit-margin-before: 0 !important; -webkit-margin-after: 0 !important; }
        .release-page-ul li { margin-top: 5px; }
        .release-page-row { padding: 5px 0; }
        .release-page-title-row { padding: 15px 0; }
    </style>
</head>
<body>

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.6.0&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;18.06.2019&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">
        <div>1. Ersatzteil kann im GoodOne bestellt werden.</div>
        <div>
            <ul class="release-page-ul">
                <li>Die Gründe zum Ersatzteil kann bearbeitet werden.</li>
                <li>In einer Bestellung kann mehr als ein Grund ausgewählt werden.</li>
                <li>Die Bestellung zum Ersatzteil wird automatisch an Afterbuy gesendet werden.</li>
            </ul>
        </div>
    </div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.5.0&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;10.05.2019&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. Vorbereitung auf Ersatzteil-Bestellung-Funktion ist angefangen.</div>
    <div class="release-page-row">
        <div>2. Funktion zur Produktstandardisierung:</div>
        <div>
            <ul class="release-page-ul">
                <li>Mapping Daten zwischen Produkte und Elements(Pakete) kann aus E2 System geladen werden.</li>
                <li>Mapping Daten zwischen Elements(Pakete) und Ersatzteile kann in GoodOne Backoffice erstellt und bearbeitet werden.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">3. Die Gründe von Ersatzteil kann in GoodOne Backoffice erstellt und bearbeitet werden.</div>
    <div class="release-page-row">
        <div>4. Funktion für Lagerarbeit:</div>
        <div>
            <ul class="release-page-ul">
                <li>Mapping Daten zwischen Regal und Elements(Pakete) kann in GoodOne Backoffice erstellt und bearbeitet werden.</li>
                <li>Lagerbestand Real kann in Goodone angezeigt werden.</li>
            </ul>
        </div>
    </div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.4.2&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;27.02.2019&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. Die Bestellung kann in der Bestellungsliste Seite nach Rechnungsnummer gesucht werden.</div>
    <div class="release-page-row">2. Die Rechnungsnummer wird auf der Bestellung Seite angezeigt.</div>
    <div class="release-page-row">3. Die Bestell-Nr. von Afterbuy wird auf der Bestellung Seite angezeigt.</div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.4.1&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;14.11.2018&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Systemfunktionen&raquo;</b></div>
    <div class="release-page-row">
        <div>Lieferscheine-Automatisch-Generieren-Funktion im Sogood Lager wurde angepasst:</div>
        <div>
            <ul class="release-page-ul">
                <li><b>Bei Abholung Bestellung:</b> Der Lieferschein wird nicht automatisch generiert werden.</li>
                <li><b>Bei Zusendung Bestellung:</b> Wenn der Lieferschein im GoodOne manuell ausgedruckt wird, wird es nicht mehr im Lager automatisch generiert werden.</li>
                <li><b>Bei Zusendung Bestellung:</b> Wenn der Lieferschein im GoodOne niemals ausgedruckt wird, wird es im Lager automatisch generiert werden.</li>
            </ul>
        </div>
    </div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.4&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;09.11.2018&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. In der Bestellung-Seite wird der Lagerbestand jeder Position angezeigt.</div>
    <div class="release-page-row">2. Unsatzstatistik nach Mitarbeitern und Zeitraum mit Bar Chart.</div>
    <div class="release-page-row">
        <div>3. [Memo] und [Rechnungsvermerk] Felder der Bestellung wurden verbessert:</div>
        <div style="font-weight: bold; color: #FF0000; padding: 5px 0;"><img src="/wp-includes/images/smilies/icon_exclaim.gif"> Die Änderungen von [Memo] und [Rechnungsvermerk] werden momentan noch nicht an Afterbuy gesendet. Ying ist dran und wird auf jeden Fall eine Lösung finden.</div>
        <div>
            <ul class="release-page-ul">
                <li>Maximale Länge wurde von 200 auf 800 Zeichen geändert.</li>
                <li>[Memo] Text wird auf Lieferschein angezeigt.</li>
                <li>[Rechnungsvermerk] Text wird auf Rechnung angezeigt.</li>
                <li>Showroom Kollegen dürfen [Memo] Text in Bestellungen von Showroom bearbeiten. ( Regel für andere GoodOne-Benutzergruppe gleich. )</li>
                <li>Showroom Kollegen dürfen [Rechnungsvermerk] Text in Bestellungen von Showroom bearbeiten. ( Regel für andere GoodOne-Benutzergruppe gleich. )</li>
                <li>Buchhalterin darf [Memo] Text in jeder Bestellung bearbeiten.</li>
                <li>Buchhalterin darf [Buchhaltungsmemo] Text in jeder Bestellung bearbeiten.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">
        <div>4. Operation History Funktion ( Erster Teil geschafft )</div>
        <div>
            <ul class="release-page-ul">
                <li>Wichtige [Operation History] werden gespeichert.</li>
                <li>Die [Operation History: Wer, Wann und Was] werden in der Bestellung Seite angezeigt.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">
        <div>5. Drei Artikel für Lieferkosten wurden erstellt.</div>
        <div>
            <ul class="release-page-ul">
                <li>EAN: 7777777777771 | Preis brutto: 12,90 EUR</li>
                <li>EAN: 7777777777772 | Preis brutto: 29,90 EUR</li>
                <li>EAN: 7777777777773 | Preis brutto: 59,90 EUR</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row"><b>&laquo;Systemfunktionen&raquo;</b></div>
    <div class="release-page-row"  style="font-weight: bold; color: #FF0000;"><img src="/wp-includes/images/smilies/icon_exclaim.gif"> Für die folgende 3 Funktionen muss IT-Abteilung noch überlegen und diskutieren. Momentan gibt es Problem mit Afterbuy API.</div>
    <div class="release-page-row" style="text-decoration:line-through;">1. [Memo] Text wird sich nach dem Bearbeiten mit Memo Text im Afterbuy synchronisiert werden, wenn die Bestellung schon an Afterbuy zugeschickt wurde.</div>
    <div class="release-page-row" style="text-decoration:line-through;">2. [Rechnungsvermerk] Text wird sich nach dem Bearbeiten mit Rechnungsvermerk Text im Afterbuy synchronisiert werden, wenn die Bestellung schon an Afterbuy zugeschickt wurde.</div>
    <div class="release-page-row" style="text-decoration:line-through;">3. Die Änderung von [Memo] und [Rechnungsvermerk] in Afterbuy werden ins GoodOne System synchronisiert werden.</div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.3&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;12.10.2018&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. Neue Benutzergruppe "Spedition" wurde hinzugefügt.</div>
    <div class="release-page-row">2. Lieferscheine automatisch exportieren Funktion fertig.</div>
    <div class="release-page-row">3. Datenanalyse Chart: Umsatz-Linien</div>
    <div class="release-page-row">4. Datenanalyse Chart: Umsatz Normal Distribution</div>
    <div class="release-page-row">5. Datenanalyse Chart: Lagerbestand Historie</div>
    <div class="release-page-row">6. Datenexport Function für [Online Real Fulfillment der allen Produkte]</div>
    <div class="release-page-row">7. Felder Buchhaltungsmemo kann für Bestellung geschrieben werden.</div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.2&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;06.08.2018&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. Nur Kollegen und Kolleginnen bei Showroom dürfen maximal 35% Rabatt geben.</div>
    <div class="release-page-row">2. Der Buchhalter darf den Status der Bestellung bearbeiten, nachdem die Bestellung bezahlt wird.</div>
    <div class="release-page-row">3. Die Auftragsbestätigung kann ausgedruckt werden. Um eine Barcode in der Auftragsbestätigung ist für die Abholung verfuegbar.</div>
    <div class="release-page-row">4. Die Bestellung kann storniert werden, wenn es noch nicht an Afterbuy gesendet wird.</div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.1&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;25.06.2018&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. Bei der Bestellung kann der Lagerbestand nicht geändert werden.</div>
    <div class="release-page-row">2. Für Versandart kann die neue Option [Abholung ohne Rabatt] hinzugefügt werden.</div>
    <div class="release-page-row">3. Angebot kann erstellt werden.</div>
    <div class="release-page-row">4. Angebot kann in Bestellung umgewandelt werden.</div>
    <div class="release-page-row">5. Für Angebot und Bestellung können die Dateien hochgeladen werden. z.B.: PDF, JPG, PNG.</div>
    <div class="release-page-row">
        <div>6. Im GoodOne Rechnungsplattform dürfen folgende Unterlagen exportiert und ausgedruckt werden:</div>
        <div>
            <ul class="release-page-ul">
                <li>Angebot</li>
                <li>Auftragsbestätigung</li>
                <li>Rechnung</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">7. Der Mitarbeiter darf eine Notiz für Groß-Handler schreiben. Und die Notiz wird in aller exportierten Unterlagen angezeigt werden.</div>
    <div class="release-page-row">8. Kunden könne mit Paypal mittels QR-Code bezahlen.</div>
    <div class="release-page-row">9. Der Mitarbeiter, wer die Bestellung erstellt hat, darf die Datei im Anhang entfernen.</div>
    <div class="release-page-row"><b>&laquo;Systemfunktionen&raquo;</b></div>
    <div class="release-page-row">1. HTTPS wurde eingesetzt.</div>

    <hr style="margin: 35px 0 25px 0;">

    <div class="release-page-title-row"><b>GoodOne&nbsp;Release&nbsp;Report</b>&nbsp;&nbsp;&nbsp;&nbsp;Version&nbsp;1.0&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;Erstellt&nbsp;am&nbsp;28.05.2018&nbsp;)</div>
    <div class="release-page-row"><b>&laquo;Benutzerfunktionen&raquo;</b></div>
    <div class="release-page-row">1. Alle Produkte von Sogood können in der Seite "Produkt->Produktliste" nach Katagorien durchsucht werden.</div>
    <div class="release-page-row">
        <div>2. Alle Produkte von Sogood können beim Erstellen der Bestellung nach Suchbegriffe durchsucht werden.</div>
        <div>
            <ul class="release-page-ul">
                <li>Die Suchfunktion funktioniert wie Google.</li>
                <li>Suchbegriffe durch Leerzeichen getrennt.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">
        <div>3. Die Bestellung kann in 4 Schritten erstellt werden:</div>
        <div>
            <ul class="release-page-ul">
                <li>Produkt suchen und im Warenkorb hinzufügen.</li>
                <li>Infomation von Käufern eingeben.</li>
                <li>Rabatt, Zahlungsmethode, Versandart wählen.</li>
                <li>Notiz zur Bestellung schreiben.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">4. Die vorhandene Käuferinfo kann gesucht und geladen werden.</div>
    <div class="release-page-row">5. Für neuen Kunde sollte die Käuferinfo manuell eingegeben werden.</div>
    <div class="release-page-row">
        <div>6. Bei der Berechnung des Verkaufspreises sollten anpassende Rabatte auch berücksichtigt werden.</div>
        <div>
            <ul class="release-page-ul">
                <li>Wenn die Zwischensumme weniger als 1000 &euro; ist, liegt Rabatt zwischen 1% bis 5%.</li>
                <li>Wenn die Zwischensumme mehr als 1000 &euro; ist, liegt Rabatt zwischen 1% bis 10%.</li>
                <li>Wenn die Zwischensumme mehr als 2000 &euro; ist, liegt Rabatt zwischen 1% bis 15%.</li>
                <li>Wenn die Zwischensumme mehr als 5000 &euro; ist, liegt Rabatt zwischen 1% bis 20%.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">7. Beim Versandart "Abholung" erhält der Käufer automatisch einen Abholung-Rabatt in Höhe von 5%.</div>
    <div class="release-page-row">8. Nachdem der Artikel in den Warenkorb hinzugefügt wird, kann die Anzahl im Warenkorb nachbearbeitet werden. Anzahl "0" bedeutet "Artikel entfert".</div>
    <div class="release-page-row">9. Wenn die Anzahl des Artikels mehr als die verfügbare Menge ist, darf der Artikel trotzdem in den Warenkorb hinzugefügt werden.</div>
    <div class="release-page-row">10. Statistische Daten für Produkte z.B.: Lagerbestand Historie u.s.w. werden in der Seite "Produkt->Übersicht" angezeigt.</div>
    <div class="release-page-row">
        <div>11. Bei jeder Bestellung werden folgende Daten angezeigt:</div>
        <div>
            <ul class="release-page-ul">
                <li>Bestellungsdatum</li>
                <li>Positionen, Bestellungsmenge, Gesamtbetrag</li>
                <li>Rechnungsadresse</li>
                <li>Versandadresse</li>
                <li>Bestellungstatus </li>
                <li>Bestell-Nr. in Afterbuy (Beim klicken öffnet sich die Bestellung in Afterbuy)</li>
                <li>Der Benutzer, wer die Bestellung erstellt hat.</li>
                <li>Der Benutzer, wer die Bestellung aktualisiert hat.</li>
                <li>Rabatt und Abholung Rabatt</li>
                <li>Zahlungsinfo</li>
                <li>Lieferungsinfo</li>
                <li>Voraussichtliches Lieferdatum</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">12. Für jede Bestellung kann die Auftragsbestätigung ausgedruckt werden.</div>
    <div class="release-page-row">
        <div>13. Bei folgenden 2 Situationen wird die Bestellung in der Warteliste gehalten.</div>
        <div>
            <ul class="release-page-ul">
                <li>Bei der Zahlungsmethode "Überweisung" sollte der Käufer warten bis das Geld da ist.</li>
                <li>Wenn die Anzahl des Artikels mehr als die verfügbare Menge ist, sollte der Käufer warten bis der Artikel auf Lager ist.</li>
            </ul>
        </div>
    </div>
    <div class="release-page-row">14. In der Seite "Dashboard" kann die Produktdaten manuell aktualisiert werden.</div>
    <div class="release-page-row">15. In der Seite "Mein Konto" kann das Password des Kontos geändert werden.</div>
    <div class="release-page-row"><b>&laquo;Systemfunktionen&raquo;</b></div>
    <div class="release-page-row">1. GoodOne System bietet für jede Kollegin und jeden Kollege die eigene Zugangsdaten an.</div>
    <div class="release-page-row">2. Der Benutzer, der die Bestellung erstellt und naher aktualisiert hat, wird automatisch in den System gespeichert.</div>
    <div class="release-page-row">3. Die Produktdaten (z.B. Lagerbestand, Preis, Notsent u.s.w.) werden alle 60 Minuten aktualisiert.</div>
    <div class="release-page-row">4. Bei erster Anmeldung wird der Benutzer informiert, das automatisch generierte Passwort aus Sicherheitsgründen zu ändern.</div>
    <div class="release-page-row">
        <div>5. Im GoodOne System wurde folgende Benutzergruppe definiert:</div>
        <div>
            <ul class="release-page-ul">
                <li>Benutzergruppe: Admin</li>
                <li>Benutzergruppe: IT</li>
                <li>Benutzergruppe: Showroom</li>
                <li>Benutzergruppe: Sales</li>
                <li>Benutzergruppe: Marketing</li>
                <li>Benutzergruppe: Einkauf</li>
                <li>Benutzergruppe: Support</li>
                <li>Benutzergruppe: Accounting</li>
            </ul>
            &nbsp;&nbsp;&nbsp;&nbsp;Die Benutzern aus verschiedener Benutzergruppe dürfen unterschiedliche Seiten und Funktionen benutzen.
        </div>
    </div>
    <div class="release-page-row">6. Das Format vom Kundenname(Kd.-Nr.) in Afterbuy lautet 96994_sogood-de-[Benutzername]@[Benutzergruppe].goodone</div>
</body>
</html>