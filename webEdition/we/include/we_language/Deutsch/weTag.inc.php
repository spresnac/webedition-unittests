<?php
/** Generated language file of webEdition CMS */
$l_weTag=array(
	'addDelNewsletterEmail'=>array(
		'description'=>'Dieses Tag erzeugt, schreibt oder entfernt eine Email aus der Newsletter-Empfänger-Liste. Die Empfänger-Listen werden als CSV-Datei gespeichert und können dann beim Versand im Newslettermodul verwendet werden.',
	),
	'addDelShopItem'=>array(
		'description'=>'Das we:addDelShopItem-Tag ermöglicht das Hinzufügen oder Wegnehmen eines Artikels aus dem Warenkorb.',
	),
	'addPercent'=>array(
		'description'=>'Das we:addPercent-Tag ermöglicht das Addieren eines gewissen Prozentsatzes, beispielsweise für die Mehrwertsteuer.',
	),
	'answers'=>array(
		'description'=>'Dieses Tag dient der Ausgabe der Antwortmöglichkeiten eines Votings.',
	),
	'author'=>array(
		'description'=>'Das we:author-Tag dient dazu, um den Autor der Seite anzuzeigen. Ist das Attribut `type` nicht gesetzt, wird der Benutzername angezeigt. Wenn type="name" ist, dann wird der Vor- und Nachname des Benutzers angezeigt. Ist `type="initials", dann werden die Initialen des Benutzers angezeigt. Ist kein Vor- und Nachname eingetragen, wird immer der Benutzername angezeigt.',
	),
	'a'=>array(
		'description'=>'Das we:a-Tag erzeugt ein HTML-Link-Tag, das auf ein webEdition-internes Dokument mit der unten angegebenen ID verweist. Der gesamte Inhalt zwischen Start- und Endtag wird verlinkt.',
	),
	'back'=>array(
		'description'=>'Das we:back-Tag erzeugt ein HTML-Link-Tag, das auf die vorherige we:listview-Seite verweist. Der gesamte Inhalt zwischen Start- und Endtag wird verlinkt.',
	),
	'bannerSelect'=>array(
		'description'=>'Mit diesem Tag wird ein DropDown-Menü (&lt;select&gt;) erzeugt, um Werbebanner auszuwählen. Wenn customer auf true gesetzt ist (bei installierter Kundenverwaltung), werden nur die Banner des eingeloggten Kunden angezeigt.',
	),
	'bannerSum'=>array(
		'description'=>'Das we:bannerSum-Tag gibt die Anzahl aller gezeigten oder geklickten Banner oder deren Klick-Rate aus. Das Tag funktioniert nur innerhalb einer listview mit type="banner"!',
	),
	'banner'=>array(
		'description'=>'Mit dem Tag wird der Werbebanner des Banner Moduls eingebunden.',
	),
	'block'=>array(
		'description'=>'Mit dem we:block-Tag kann man erweiterbare Blöcke/Listen erzeugen. Alles, was zwischen Start- und Endtag steht, wird im Bearbeitungsmodus durch einen Klick auf den Plus-Button angehängt, bzw. eingefügt. Dies können beliebiges HTML sowie fast alle we:tags sein.',
	),
	'calculate'=>array(
		'description'=>'Das we:calculate-Tag erlaubt alle möglichen mathematischen Operationen. (*, /, +, -,(), sqrt.....). Die Attribute to und nameto wirken nur bei print=true',
	),
	'captcha'=>array(
		'description'=>'Dieses Tag dient dazu, ein Bild mit einem Zufallscode zu generieren.',
	),
	'categorySelect'=>array(
		'description'=>'Mit diesem Tag wird ein DropDown-Menü (&lt;select&gt;) erzeugt, um Kategorien auszuwählen. Wenn man gleich nach dem Starttag das Endtag setzt, dann werden automatisch alle in webEdition definierten Kategorien angezeigt.',
	),
	'category'=>array(
		'description'=>'Das we:category Tag wird durch die Kategorie(n) ersetzt, die in der Ansicht "Eigenschaft" dem Dokument zugeordnet wurden. Wenn es mehrere Kategorien sind, werden sie durch Kommas getrennt. Wenn Sie ein anderes Trennzeichen verwenden möchten, können Sie dies mit dem Attribut "delimiter" zuweisen. Bsp.: delimiter = " " (Hier wird ein Leerzeichen verwendet um die Kategorien zu trennen.)',
	),
	'charset'=>array(
		'description'=>'Das Tag we:charset generiert eine Meta-Angabe, die bestimmt mit welchem Zeichensatz die fertige Seite angezeigt wird. Für deutsche Seiten wird normalerweise der Zeichensatz "ISO-8859-15" verwendet. Dieser Tag muss innerhalb der &lt;head&gt;&lt;/head&gt; Tags der HTML-Seite stehen.',
	),
	'checkForm'=>array(
		'description'=>'Das Tag we:checkForm führt eine Validierung eines Formulars per JavaScript durch. <br/>Die Kombination der Parameter `match` und `type` legen den `name`,bzw. die `id` des zu kontrollierenden Formulars fest.<br/>`mandatory` und `email` erwarten eine kommaseparierte Liste von Pflichtfeldern, bzw. Email-Adressen(Syntax-Check). In `password` können kommasepariert 2 Feldnamen und eine Mindestlänge eingegeben werden, die auf Gleichheit, bzw die Mindestlänge überprüft weden.<br/>Mit onError kann im Fehlerfall eine eigene JavaScript-Funktion aufgerufen werden, die als Parameter arrays mit fehlenden Pflichtfeldern und den invaliden Email-Adressen erhält, als dritter Parameter wird ein Flag übergeben, ob die Passworteingabe korrekt war. Andernfalls wird der Standardwert im Fehlerfall ausgegeben.',
	),
	'colorChooser'=>array(
		'description'=>'Das we:colorChooser Tag erzeugt ein Eingabefeld um Farbwerte auszuwählen',
	),
	'comment'=>array(
		'description'=>'Das comment Tag kann benutzt werden, um explizit Kommentare in der angegebenen Sprache zu generieren, oder einfach nur zur Sturkturierung des Template Kodes, wobei der Kommentar vorher gefiltert wird.',
	),
	'conditionAdd'=>array(
		'description'=>'Dieses Tag fügt der mit &lt;we:condition&gt; eingeleiteten Bedingung eine neue Regel/Vergleich hinzu.',
	),
	'conditionAnd'=>array(
		'description'=>'Dieses Tag verknüpft Regeln/Vergleiche mit anderen Regeln/Vergleichen innerhalb von &lt;we:condition&gt; mit einer UND Verknüpfung. Beide Regeln/Vergleiche müssen erfüllt sein, damit die Bedingung wahr (true) wird.',
	),
	'conditionOr'=>array(
		'description'=>'Dieses Tag verknüpft Regeln/Vergleiche mit anderen Regeln/Vergleichen innerhalb von &lt;we:condition&gt; mit einer OR Verknüpfung. Eine der beiden Regeln/Vergleiche muß erfüllt sein, damit die Bedingung wahr (true) wird.',
	),
	'condition'=>array(
		'description'=>'Mit diesem Tag kann man in Verbindung mit &lt;we:conditionAdd&gt; für das Attribut condition bei &lt;we:listviews type="object"&gt; eine Bedingung dynamisch erzeugen. Es ist zudem möglich &lt;we:condition&gt; ineinander zu verschachteln, wenn man z. B. ODER und UND Verknüpfungen miteinander mischen möchte.',
	),
	'content'=>array(
		'description'=>'&lt;we:content /&gt; wird nur innerhalb einer Hauptvorlage eingesetzt. Es markiert die Fläche, in die der Inhalt der Detailvorlage innerhalb der Hauptvorlage eingebunden wird.',
	),
	'controlElement'=>array(
		'description'=>'Mit dem Tag we:controlElement können Schaltflächen in der Bearbeiten-Ansicht eines Dokuments gezielt manipuliert werden. Buttons können ausgeblendet werden. Checkboxen können aktiviert, disabled und/oder versteckt werden.',
	),
	'cookie'=>array(
		'description'=>'Dieser Tag wird vom Voting Modul benötigt und setzt einen Cookie, der Mehrfachabstimmungen eines Besuchers verhindert. Der Tag muss ganz am Anfang der Vorlage stehen, es dürfen sich keine Zeichen (inkl. Leerzeichen und Zeilenumbruch) vor diesem Tag befinden.',
	),
	'createShop'=>array(
		'description'=>'Das we:createShop-Tag wird auf jeder Seite benötigt, die Shop-Daten enthalten soll.',
	),
	'css'=>array(
		'description'=>'Das we:css-Tag erzeugt ein HTML-Tag, das auf ein webEdition-internes CSS Stylesheet mit der unten angegebenen ID verweist. Dadurch können Sie Stylesheets in einer separaten Datei definieren.',
	),
	'customer'=>array(
		'description'=>'Mit Hilfe dieses Tags kann man einen Kunden auf einer webEdition darstellen. Die Kundenfelder werden wie bei einer Listview und beim &lt;we:object&gt; Tag mit dem Tag &lt;we:field&gt; dargestellt.<br/><br/>Durch Kombination der Attribute kann das Tag 3 verschiedene Funktionen erfüllen:<br/>Wenn name gesetzt ist, dann kann der Redakteur einen Kunden per Customer-Selector auswählen. Dieser Kunde wird dann im Dokument unter dem in nam angegeben Feld gespeichert.<br/>Wenn name nicht gesetzt ist, dafür aber id, wird der Kunde mit dieser ID angezeigt<br/>Wenn weder name noch id gesetzt ist, erwartet das Tag, dass die id des Kunden per Request Parameter übermittelt wird. Dies tut zB. die Customer-Listview wenn das Attribut hyperlink="true" im &lt;we:field&gt; Tag gesetzt ist. Der Name des Request Parameters lautet we_cid',
	),
	'dateSelect'=>array(
		'description'=>'Das we:dateSelect-Tag gibt ein Auswahlfeld für ein Datum zurück, welches im Zusammenhang mit dem Tag we:processDateSelect in eine Variable als Unix Timestamp eingelesen werden kann.',
	),
	'date'=>array(
		'description'=>'Das we:date-Tag zeigt, entsprechend dem Formatstring, das aktuelle Datum auf der Seite an. Wenn das Dokument statisch gespeichert wird, sollte der Typ auf &quot;js&quot; gesetzt werden, damit das Datum mit Javascript erzeugt wird.',
	),
	'deleteShop'=>array(
		'description'=>'Das we:deleteShop-Tag löscht den kompletten Warenkorb.',
	),
	'delete'=>array(
		'description'=>'Dieses Tag dient dazu, webEdition-Dokumente bzw. Objekte die über &lt;we:a edit="true" delete="true"&gt; aufgerufen wurden, zu löschen.<br/><br/>ACHTUNG: Dieses Tag sollte nur in Verbindung mit der Kundenverwaltung und den Attributen admin bzw. userid und/oder mit den Attributen doctype, pid und classid benutzt werden. Ansonsten ist es theoretisch möglich, dass Unbefugte mit Aufruf des entsprechenden URL webEdition-Dokumente bzw. Objekte löschen können.',
	),
	'description'=>array(
		'description'=>'Das we:description-Tag erzeugt ein description Meta-Tag. Falls das Beschreibungsfeld in der Ansicht "Eigenschaften" leer ist, wird der Inhalt zwischen Start- und Endtag als Standardbeschreibung eingetragen.',
	),
	'DID'=>array(
		'description'=>'Dieses Tag gibt die ID eines webEdition-Dokuments zurück.',
	),
	'docType'=>array(
		'description'=>'Dieses Tag gibt den Dokumenttyp eines webEdition-Dokuments zurück.',
	),
	'else'=>array(
		'description'=>'Dieses Tag leitet die Alternative ein, wenn die Bedingung eines if-Tags (z. B. &lt;we:ifEditmode&gt;, &lt;we:ifNotVar&gt;, &lt;we:ifNotEmpty&gt;, &lt;we:ifFieldNotEmpty&gt;, ) nicht zutrifft.',
	),
	'field'=>array(
		'description'=>'Das we:field-Tag wird benötigt, um den Inhalt eines Datenbankfeldes des zugehörigen Listview-Eintrages anzuzeigen. Das we:field-Tag darf nur innerhalb des we:repeat Start- und Endtags stehen.',
	),
	'flashmovie'=>array(
		'description'=>'Das we:flashmovie-Tag dient dazu, einen Flash Movie in den Inhalt des Dokumentes einzubauen. Im Bearbeitungsmodus eines Dokumentes, das diese Vorlage zugrunde liegen hat, ist ein Button "bearbeiten" sichtbar. Durch Anklicken dieses Buttons öffnet sich ein Dateimanager, in dem man einen Flash Movie, der zuvor in webEdition angelegt wurde, auswählen kann.',
	),
	'formfield'=>array(
		'description'=>'Dieses Tag dient zum Anlegen und Bearbeiten von Formularfeldern.',
	),
	'formmail'=>array(
		'description'=>'Bei eingeschalteter Einstellung Formmail über webEdition-Dokument aufrufen, erfolgt die Einbindung des Formmail-Scripts über ein webEdition-Dokument. Hierfür wird der neue (derzeit noch parameterlose) we-Tag formmail verwendet.<br/>Wird die Captcha-Prüfung eingesetzt, steht &lt;we:formmail/&gt; innerhalb des we-Tags ifCaptcha.',
	),
	'form'=>array(
		'description'=>'Das we:form Tag wird für Such- und Mailformulare eingesetzt. Es funktioniert wie das normale HTML-Form-Tag, jedoch werden zusätzliche Hidden-Fields vom Parser eingefügt.',
	),
	'hidden'=>array(
		'description'=>'Das we:hidden-Tag erzeugt ein hidden-input-Tag, mit den Inhalt der  gleichnamigen globalen PHP-Variablen. Dieses Tag wird normalerweise gebraucht, um eingehende Variablen weiterzuleiten.',
	),
	'hidePages'=>array(
		'description'=>'Das we:hidePages-Tag ermöglicht es, bestimmte Modi eines Dokuments zu deaktivieren. Dieses Tag kann dazu eingesetzt werden, um den Zugriff auf die Eigenschaftsseite eines Dokuments zu verhindern. Dadurch kann dieses Dokument bspw. nicht mehr geparkt werden.',
	),
	'href'=>array(
		'description'=>'Das we:href-Tag erzeugt eine Url, welche im Editmodus eingegeben werden kann.',
	),
	'icon'=>array(
		'description'=>'Das we:icon-Tag erzeugt ein HTML-Tag, das auf ein webEdition internes Icon mit der unten angegebenen ID verweist. Dadurch können Sie ein Icon einbinden, welches beim Bookmarken Ihrer Homepage im Internet Explorer, Mozilla, Safari und Opera angezeigt wird.<br/><br/>Bitte beachten Sie: Die Icon Datei sollte den Dateinamen "favicon.ico" haben und möglichst direkt im Document-Root liegen.',
	),
	'ifBack'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn es bei einer Listview auch eine vorherige Seite gibt. Gibt es keine vorherige Seite f?r die Listview, dann wird der umschlossene Inhalt nicht angezeigt.',
	),
	'ifbannerexists'=>array(
		'description'=>'Führt den eingeschlossenen Code nur aus, wenn das Banner-Modul nicht deaktiviert wurde (Einstellungsdialog).',
	),
	'ifCaptcha'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen bzw. auszuführen, wenn der vom User eingegebene Code gültig ist.',
	),
	'ifCat'=>array(
		'description'=>'Das we:ifCat-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn die in "categories" eingetragenen Kategorien dem Dokument zugewiesen wurden.',
	),
	'ifClient'=>array(
		'description'=>'Das we:ifClient-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn der Client (Browser) den vorgegebenen Anforderungen entspricht. Dieses Tag funktioniert nur bei dynamisch abgespeicherten Seiten!',
	),
	'ifConfirmFailed'=>array(
		'description'=>'Nutzt man DoubleOptIn bei der Anmeldung zum Newslettermodul, kann mit &lt;we:ifConfirmFailed&gt; überprüft werden, ob die gegebene E-Mail Adresse bestätigt werden konnte.',
	),
	'ifCurrentDate'=>array(
		'description'=>'Dieses Tag highlighted den aktuellen Tag innerhalb einer Calendar-listview.',
	),
	'ifcustomerexists'=>array(
		'description'=>'Führt den eingeschlossenen Code nur aus, wenn das Kundenverwaltungs-Modul nicht deaktiviert wurde (Einstellungsdialog).',
	),
	'ifDeleted'=>array(
		'description'=>'Dieses Tag dient dazu, um webEdition-Dokumente bzw. Objekte die über &lt;we:a edit="true" delete="true"&gt; aufgerufen wurden zu löschen.<br/><br/><strong>ACHTUNG: Dieses Tag sollte nur in Verbindung mit der Kundenverwaltung und den Attributen admin bzw. userid und/oder mit den Attributen doctype, pid und classid benutzt werden. Ansonsten ist es theoretisch möglich, dass Unbefugte mit Aufruf des entsprechenden URL webEdition-Dokumente bzw. Objekte löschen können.</strong>',
	),
	'ifDoctype'=>array(
		'description'=>'Das we:ifDoctype-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn der in "doctype" eingetragene Dokument-Typ dem Dokument zugewiesen wurde.',
	),
	'ifDoubleOptIn'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn es sich um den ersten Schritt eines Double Opt-In handelt.',
	),
	'ifEditmode'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur innerhalb des Edit-Modus anzuzeigen.',
	),
	'ifEmailExists'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn eine bestimmte E-Mail-Adresse bereits in der Newsletter-Adressliste vorhanden ist.',
	),
	'ifEmailInvalid'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn eine bestimmte E-Mail-Adresse syntaktisch falsch ist.',
	),
	'ifEmailNotExists'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn eine bestimmte E-Mail-Adresse noch nicht in der Newsletter-Adressliste vorhanden ist.',
	),
	'ifEmpty'=>array(
		'description'=>'Das we:ifEmpty-Tag bewirkt, dass alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn das Feld, das den in "match" eingetragenen Namen hat, leer ist. Im Attribut "type" muss der Typ des Feldes angegeben werden, wenn es sich um ein "img", "flashmovie" oder "href" Feld handelt.',
	),
	'ifEqual'=>array(
		'description'=>'Das we:ifEqual-Tag vergleicht den Inhalt der beiden Felder "name" und "eqname". Ist der Inhalt der Felder gleich, wird alles, was zwischen Start- und Endtag steht, dargestellt. Wird das Tag innerhalb von we:list, we:block oder we:linklist benutzt, kann nur ein Feld innerhalb dieser Tags mit einem Feld außerhalb verglichen werden. In diesem Fall müssen Sie im Attribut "name" den Namen des Feldes innerhalb der we:block, we:list oder we:linklist Tags benutzen. Im Attribut eqname muß dann der Name eines Feldes außerhalb der Tags benutzt werden. Ebenso kann sich das Tag innerhalb von dynamisch includierten webEdition-Seiten befinden. In diesem Fall wird in "name" ein Feld innerhalb der includierten Seite angegeben und in "eqname" der Name eines Feldes auf der Hauptseite. Wenn im Attribut "value" etwas eingetragen ist, wird "eqname" ignoriert und es wird mit dem in "value" eingetragenen Wert verglichen.',
	),
	'ifFemale'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb eines Newsletters nur dann anzuzeigen, wenn die weibliche Anrede angezeigt werden soll.',
	),
	'ifFieldEmpty'=>array(
		'description'=>'Das we:ifFieldEmpty-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn das Feld einer we:listview, das den in "match" eingetragenen Namen hat, leer ist. Im Attribut "type" muss der Typ des Feldes angegeben werden, wenn es sich um ein "img", "flashmovie" oder "href" Feld handelt.',
	),
	'ifFieldNotEmpty'=>array(
		'description'=>'Das we:ifFieldNotEmpty-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn das Feld einer we:listview, das den in "match" eingetragenen Namen hat,  nicht leer ist. Im Attribut "type" muss der Typ des Feldes angegeben werden, wenn es sich um ein "img", "flashmovie" oder "href" Feld handelt.',
	),
	'ifField'=>array(
		'description'=>'Das we:ifField-Tag wird benötigt, um den umschlossenen Inhalt nur dann anzuzeigen, wenn der Wert des Datenbankfeldes des zugehörigen Listview-Eintrages gleich dem Wert des Attributes "match" ist. Das we:ifField-Tag darf nur innerhalb des we:repeat Start- und Endtags stehen.',
	),
	'ifFound'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn bei einer &lt;we:listview&gt; Einträge gefunden werden.',
	),
	'ifHasChildren'=>array(
		'description'=>'Innerhalb des we:repeat Tags kann mit &lt;we:ifHasChildren&gt; abgefragt werden, ob der aktuelle Kategorie-Ordner Kategorien enthält bzw. die Kategorie Kinder hat.',
	),
	'ifHasCurrentEntry'=>array(
		'description'=>'Mit we:ifHasCurrentEntry kann innerhalb eines we:navigationEntry type="folder" Inhalt nur dann ausgegeben werden, wenn der auszugebende Navigationsordner den aktiven Eintrag besitzt.',
	),
	'ifHasEntries'=>array(
		'description'=>'Mit we:ifHasEntries kann innerhalb eines we:navigationEntry Inhalt nur ausgegeben werden, wenn der auszugebende Navigationseintrag Einträge besitzt',
	),
	'ifHasShopVariants'=>array(
		'description'=>'Mit &lt;we:ifHasShopVariants&gt; kann ein Inhalt bedingt nur dann angezeigt werden, wenn ein Dokument, Objekt auch Varianten enthält. Damit kann bspw. kontrolliert werden, ob eine &lt;we:listview type="shopVariant"&gt; überhaupt angezeigt werden soll. <b>Dieses Tag wirkt in Dokumenten und in Objekt-Vorlagen die per Arbeitsbereich zugewiesen wurden, nicht jedoch in we:listview bzw. we:object Tags!</b>',
	),
	'ifHtmlMail'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn es sich um Inhalt für einen Newsletter im HTML-Format handelt.',
	),
	'ifIsDomain'=>array(
		'description'=>'Das we:ifIsDomain-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn der Domainname des Servers den in "domain" eingetragenen Namen hat. Der Domainname muss dabei exakt identisch sein, inkl. eventuell führendem "www".<br/>Das Ergebnis ist nur auf der fertigen Webseite und in der Vorschau zu sehen, im Bearbeitungsmodus wird alles angezeigt.',
	),
	'ifIsNotDomain'=>array(
		'description'=>'Das we:ifIsDomain Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn der Domainname des Servers den in "domain" eingetragenen Namen nicht hat. Das Ergebnis ist nur auf der fertigen Webseite und in der Vorschau zu sehen, im Bearbeitungsmodus wird alles angezeigt.',
	),
	'ifLastCol'=>array(
		'description'=>'Werden die Tabellenfunktionen einer &lt;we:listview&gt; eingesetzt, kann mit &lt;we:ifLastCol&gt; die letzte Spalte einer Tabellenzeile erkannt werden.',
	),
	'ifLoginFailed'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn ein Login-Versuch gescheitert ist.',
	),
	'ifLogin'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn ein Login auf der Seite durchgeführt wurde und ermöglicht damit Initialisierungen.',
	),
	'ifLogout'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn ein Logout auf der Seite durchgeführt wurde und ermöglicht damit Aufräumarbeiten.',
	),
	'ifMailingListEmpty'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der Newsletterinteressent keinen Newsletter ausgewählt hat.',
	),
	'ifMale'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb eines Newsletters nur dann anzuzeigen, wenn die männliche Anrede angezeigt werden soll.',
	),
	'ifnewsletterexists'=>array(
		'description'=>'Führt den eingeschlossenen Code nur aus, wenn das Newsletter-Modul nicht deaktiviert wurde (Einstellungsdialog).',
	),
	'ifNewsletterSalutationEmpty'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb eines Newsletters nur dann anzuzeigen, wenn das in type definierte Anredefeld leer ist.',
	),
	'ifNewsletterSalutationNotEmpty'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb eines Newsletters nur dann anzuzeigen, wenn das in type definierte Anredefeld nicht leer ist.',
	),
	'ifNew'=>array(
		'description'=>'Alles was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das zu bearbeitende Dokument/Objekt neu ist. Mit dem Attribut type wird bestimmt, ob es sich um ein Dokument oder Objekt handelt.',
	),
	'ifNext'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn es bei einer Listview auch eine nächste Seite gibt. Gibt es keine nächste Seite, dann wird der umschlossene Inhalt nicht angezeigt.',
	),
	'ifNoJavaScript'=>array(
		'description'=>'Dieses Tag dient dazu, den Browser auf ein webEdition-Dokument umzuleiten, falls im Browser des Users JavaScript deaktiviert bzw. nicht verfügbar ist. Das Tag darf nur im Headerbereich der HTML-Seite (zwischen &lt;head&gt; und &lt;/head&gt;) verwendet werden.',
	),
	'ifNotCaptcha'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der vom User eingegebene Code ungültig ist.',
	),
	'ifNotCat'=>array(
		'description'=>'Das we:ifNotCat-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn die in "categories" eingetragenen Kategorien nicht dem Dokument zugewiesen wurden.',
	),
	'ifNotDeleted'=>array(
		'description'=>'Wenn ein Dokument/Objekt durch das Tag we:delete gelöscht wurde, wird alles zwischen Start- und Endtag angezeigt, wenn das Löschen nicht erfolgreich war.',
	),
	'ifNotDoctype'=>array(
		'description'=>'Umschlossenen Inhalt nur anzeigen, wenn das Dokument nicht zu einem der im Attribut "doctypes" angegebenen Dokumenttypen gehört.',
	),
	'ifNotEditmode'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur außerhalb des Edit-Modus anzuzeigen.',
	),
	'ifNotEmpty'=>array(
		'description'=>'Das we:ifNotEmpty-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn das Feld, das den in "match" eingetragenen Namen hat,  nicht leer ist. Im Attribut "type" muss der Typ des Feldes angegeben werden, wenn es sich um ein "img", "flashmovie" oder "href" Feld handelt.',
	),
	'ifNotEqual'=>array(
		'description'=>'Das we:ifNotEqual-Tag vergleicht den Inhalt der beiden Felder "name" und "eqname". Ist der Inhalt der Felder gleich, wird alles, was zwischen Start- und Endtag steht, nicht dargestellt. Wird das Tag innerhalb von we:list, we:block oder we:linklist benutzt, kann nur ein Feld innerhalb dieser Tags mit einem Feld außerhalb verglichen werden. In diesem Fall müssen Sie im Attribut "name" den Namen des Feldes innerhalb der we:block, we:list oder we:linklist Tags benutzen. Im Attribut eqname muß dann der Name eines Feldes außerhalb der Tags benutzt werden. Ebenso kann sich das Tag innerhalb von dynamisch includierten webEdition-Seiten befinden. In diesem Fall wird in "name" ein Feld innerhalb der includierten Seite angegeben und in "eqname" der Name eines Feldes auf der Hauptseite. Wenn im Attribut "value" etwas eingetragen ist, wird "eqname" ignoriert und es wird mit dem in "value" eingetragenen Wert verglichen.',
	),
	'ifNotField'=>array(
		'description'=>'Das we:ifNotField-Tag wird benötigt, um den umschlossenen Inhalt nur dann anzuzeigen, wenn der Wert des Datenbankfeldes des zugehörigen Listview-Eintrages nicht gleich dem Wert des Attributes "match" ist. Das we:ifNotField-Tag darf nur innerhalb des we:repeat Start- und Endtags stehen.',
	),
	'ifNotFound'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn bei einer &lt;we:listview&gt; keine Einträge gefunden werden.',
	),
	'ifNotHasChildren'=>array(
		'description'=>'Innerhalb des we:repeat Tags kann mit &lt;we:ifNotHasChildren&gt; abgefragt werden, ob der aktuelle Kategorie-Ordner Kategorien enthält bzw. die Kategorie Kinder hat.',
	),
	'ifNotHasCurrentEntry'=>array(
		'description'=>'Mit we:ifNotHasCurrentEntry kann innerhalb eines we:navigationEntry type="folder" Inhalt nur dann ausgegeben werden, wenn der auszugebende Navigationsordner NICHT den aktiven Eintrag besitzt.',
	),
	'ifNotHasEntries'=>array(
		'description'=>'Mit we:ifNotHasEntries kann innerhalb eines we:navigationEntry Inhalt nur ausgegeben werden, wenn der auszugebende Navigationseintrag KEINE Einträge besitzt',
	),
	'ifNotHasShopVariants'=>array(
		'description'=>'Mit &lt;we:ifNotHasShopVariants&gt; kann ein Inhalt bedingt nur dann angezeigt werden, wenn ein Dokument, Objekt KEINE Varianten enthält. Damit kann bspw. kontrolliert werden, ob eine &lt;we:listview type="shopVariant"&gt; überhaupt angezeigt werden soll bzw. was alternativ angezeigt werden soll.',
	),
	'ifNotHtmlMail'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn es sich nicht um Inhalt für einen Newsletter im HTML-Format handelt.',
	),
	'ifNotNewsletterSalutation'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb eines Newsletters nur dann anzuzeigen, wenn das in type definierte Anredefeld dem Wert in match nicht entspricht.',
	),
	'ifNotNew'=>array(
		'description'=>'Alles was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das zu bearbeitende Dokument/Objekt nicht neu ist. Mit dem Attribut type wird bestimmt, ob es sich um ein Dokument oder Objekt handelt.',
	),
	'ifNotObjectLanguage'=>array(
		'description'=>'Mit we:ifObjectLanguage kann auf die Spracheinstellung des Objectes getestet werden, dabei können mehrere Werte durch Komma separiert angegeben werden (oder-Verknüpfung). Die möglichen Werte ergeben sich aus dem Einstellungsdialog, Tab `Sprachen`.',
	),
	'ifNotObject'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der jeweilige Eintrag innerhalb von &lt;we:listview type="search"&gt; kein Objekt ist.',
	),
	'ifNotPageLanguage'=>array(
		'description'=>'Mit we:ifNotPageLanguage kann auf die Spracheinstellung des Dokumentes getestet werden, dabei können mehrere Werte durch Komma separiert angegeben werden (oder-Verknüpfung). Die möglichen Werte ergeben sich aus dem Einstellungsdialog, Tab `Sprachen`.',
	),
	'ifNotPosition'=>array(
		'description'=>'Mit dem Tag we:ifNotPosition kann man eine Aktion definieren, die an einer bestimmten Position eines Blocks, einer Listview, einer Linklist oder einer Listdir NICHT ausgeführt wird. Der Parameter "position" erlaubt eine vielseitige Eingabe der Position. So ist es möglich, die erste (first), letze (last), alle geraden (even), bzw ungeraden (odd), sowie einzelne Positionen (1,2,3, ...) abzuprüfen. Wird der type "block", "linklist" verwendet, muss zusätzlich der Name (reference) des entsprechenden Blocks/Linklist angegeben werden.',
	),
	'ifNotRegisteredUser'=>array(
		'description'=>'Prüft, ob ein User nicht registriert ist.',
	),
	'ifNotReturnPage'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nach dem Ändern/Erzeugen von einem webEdition-Dokument bzw. Objekt nur dann anzuzeigen, wenn der Wert des Attributs "return" von &lt;we:a edit="true"&gt; gleich "false" ist oder das Attribut nicht gesetzt wurde.',
	),
	'ifNotSearch'=>array(
		'description'=>'Durch das we:ifNotSearch-Tag wird der Inhalt zwischen dem Start- und Endtag nur dann angezeigt, wenn kein Suchbegriff mit we:search übermittelt wurde oder dieser leer ist. Ist das Attribut "set" auf true gesetzt, wird nur geprüft ob die Request-Variable von we:search nicht gesetzt ist.',
	),
	'ifNotSeeMode'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur ausserhalb des seeMode anzuzeigen.',
	),
	'ifNotSelf'=>array(
		'description'=>'Das we:ifNotSelf-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nicht angezeigt wird, wenn das Dokument eine der unten eingetragenen ID`s hat. Befindet sich das Tag nicht innerhalb von we:linklist oder we:listdir Tags, dann ist "id" ein erforderlicher Eintrag!',
	),
	'ifNotSendMail'=>array(
		'description'=>'Prüft, ob eine Seite gerade mit we:sendMail versendet wird und erlaubt Inhalte dabei aus- und einzublenden',
	),
	'ifNotShopField'=>array(
		'description'=>'Das Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn das shopField mit dem Namen "name" ungleich dem Wert ist, welcher in "match" eingetragen ist. Ist dies der Fall, wird alles zwischen Start- und Endtag angezeigt.',
	),
	'ifNotSidebar'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur auszugeben, wenn der Seitenaufruf ausserhalb der Sidebar stattfindet.',
	),
	'ifNotSubscribe'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der Eintrag in eine Newsletter-Adressliste nicht erfolgreich war und sollte in der Vorlage nach &lt;we:addDelNewsletterEmail&gt; stehen, aus der das webEdition-Dokument erzeugt wird, welches nach einem Eintrag in eine Newsletter-Adressliste aufgerufen wird.',
	),
	'ifNotTemplate'=>array(
		'description'=>'Zeigt dem umschlossenen Inhalt nur an, wenn das aktuelle Dokument nicht auf der angegebenen Vorlage beruht.<br/><br/>Weitere Informationen finden Sie in der Dokumentation des Tags we:ifTemplate.',
	),
	'ifNotTop'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn sich das Tag innerhalb einer includierten Datei befindet.',
	),
	'ifNotUnsubscribe'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der Austrag aus einer Newsletter-Adressliste nicht erfolgreich war und sollte in der Vorlage nach &lt;we:addDelNewsletterEmail&gt; stehen, aus der das webEdition-Dokument erzeugt wird, welches nach dem Austrag aus einer Newsletter-Adressliste aufgerufen wird.',
	),
	'ifNotVarSet'=>array(
		'description'=>'Mit diesem Tag kann man prüfen, ob eine Variable mit dem Namen name nicht gesetzt ist. Achtung: Es ist ein Unterschied ob eine Variable leer ist oder nicht gesetzt wurde!',
	),
	'ifNotVar'=>array(
		'description'=>'Das we:ifNotVar-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nicht angezeigt wird, wenn die Variable mit dem Namen "name" gleich dem Wert ist, welcher in "match" eingetragen ist. Im Attribut "type" kann angegeben werden, um welchen Typ von Variable es sich handelt.',
	),
	'ifNotVoteActive'=>array(
		'description'=>'Alles, was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das Voting abgelaufen ist.',
	),
	'ifNotVoteIsRequired'=>array(
		'description'=>'Alles, was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das Voting kein Pflichtfeld ist.',
	),
	'ifNotVote'=>array(
		'description'=>'Alles, was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das Voting nicht gespeichert ist. Das Attribut type spezifiziert die Art des Fehlers.',
	),
	'ifNotVotingField'=>array(
		'description'=>'&Uuml;berprüft ob ein VotingField keinen Wert entsprechend dem Attribut match hat, die Kombinationen von name und type Attributen entsprechen denen des we:votingFiled-Tags',
	),
	'ifNotVotingIsRequired'=>array(
		'description'=>'Gibt des umschlossenen Inhalt nur aus, wenn das Voting-Feld kein Pflichtfeld ist',
	),
	'ifNotWebEdition'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb von webEdition nicht anzuzeigen. Auf der fertig erzeugten Seite wird der umschlossene Inhalt angezeigt.',
	),
	'ifNotWorkspace'=>array(
		'description'=>'&Uuml;berprüft, ob sich ein Dokument NICHT in dem unter "path" angegeben Arbeitsbereich befindet.',
	),
	'ifNotWritten'=>array(
		'description'=>'Alle was zwischen Start- und Endtag steht, wird nur angezeigt, wenn es einen Fehler beim Schreiben eines Dokuments/Objekts mit dem Tag we:write gab. Bei einem Objekt muß type="object" sein.',
	),
	'ifObjectLanguage'=>array(
		'description'=>'Mit we:ifObjectLanguage kann auf die Spracheinstellung des Objectes getestet werden, dabei können mehrere Werte durch Komma separiert angegeben werden (oder-Verknüpfung). Die möglichen Werte ergeben sich aus dem Einstellungsdialog, Tab `Sprachen`.',
	),
	'ifObject'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der jeweilige Eintrag innerhalb von &lt;we:listview type="search"&gt; ein Objekt ist.',
	),
	'ifobjektexists'=>array(
		'description'=>'Führt den eingeschlossenen Code nur aus, wenn das Objekt/DB-Modul nicht deaktiviert wurde (Einstellungsdialog).',
	),
	'ifPageLanguage'=>array(
		'description'=>'Mit we:ifPageLanguage kann auf die Spracheinstellung des Dokumentes getestet werden, dabei können mehrere Werte durch Komma separiert angegeben werden (oder-Verknüpfung). Die möglichen Werte ergeben sich aus dem Einstellungsdialog, Tab `Sprachen`.',
	),
	'ifPosition'=>array(
		'description'=>'Mit dem Tag we:ifPosition ist es möglich die aktuelle Position eines Blocks, einer Listview, einer Linklist oder einer Listdir zu kontrollieren. Der Parameter "position" erlaubt eine vielseitige Eingabe der Position. So ist es möglich das Erste (first), Letze (last), alle geraden (even), bzw ungeraden (odd), sowie einzelne Positionen (1,2,3, ...) abzuprüfen. Wird der type "block", "linklist" verwendet, muss zusätzlich der Name (reference) des entsprechenden Blocks/Linklist angegeben werden.',
	),
	'ifRegisteredUserCanChange'=>array(
		'description'=>'Prüft, ob das aktuelle Dokument/Objekt von einem registrierten Kunden verändert werden kann. In einer listview wird das Dokument/Objekt des aktuellen listview-Eintrags benutzt.',
	),
	'ifRegisteredUser'=>array(
		'description'=>'Prüft, ob ein User registriert ist oder nicht.',
	),
	'ifReturnPage'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nach dem Ändern/Erzeugen eines webEdition-Dokuments bzw. Objekts nur dann anzuzeigen, wenn der Wert das Attributs "return" von &lt;we:a edit="document"&gt; bzw. &lt;we:a edit="object"&gt; gleich "true" ist.',
	),
	'ifSearch'=>array(
		'description'=>'Durch das we:ifSearch-Tag wird der Inhalt zwischen dem Start- und Endtag nur dann angezeigt, wenn ein Suchbegriff mit we:search  übermittelt wurde und dieser nicht leer ist. Ist das Attribut "set" auf true gesetzt, wird nur geprüft ob die Request-Variable von we:search gesetzt ist.',
	),
	'ifSeeMode'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur innerhalb des seeMode anzuzeigen.',
	),
	'ifSelf'=>array(
		'description'=>'Durch das we:ifSelf-Tag wird der Inhalt zwischen dem Start- und Endtag nur dann angezeigt, wenn es sich um ein Dokument handelt, welches übder das Attribut ID angegeben wird. Befindet sich das Tag nicht innerhalb von we:linklist oder we:listdir Tags, dann ist "id" ein erforderlicher Eintrag!',
	),
	'ifSendMail'=>array(
		'description'=>'Prüft, ob eine Seite gerade mit we:sendMail versendet wird und erlaubt Inhalte dabei aus- und einzublenden',
	),
	'ifShopEmpty'=>array(
		'description'=>'Alles, was sich zwischen dam Start- und Endtag befindet, wird angezeigt, wenn der Warenkorb leer ist.',
	),
	'ifshopexists'=>array(
		'description'=>'Führt den eingeschlossenen Code nur aus, wenn das Shop-Modul nicht deaktiviert wurde (Einstellungsdialog).',
	),
	'ifShopFieldEmpty'=>array(
		'description'=>'Prüft, ob ein shopField leer ist. Ist dies der Fall, wird alles zwischen Start- und Endtag angezeigt.',
	),
	'ifShopFieldNotEmpty'=>array(
		'description'=>'Prüft, ob ein shopField nicht leer ist. Ist dies der Fall, wird alles zwischen Start- und Endtag angezeigt.',
	),
	'ifShopField'=>array(
		'description'=>'Das Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn das shopField mit dem Namen "name" gleich dem Wert ist, welcher in "match" eingetragen ist. Ist dies der Fall, wird alles zwischen Start- und Endtag angezeigt.',
	),
	'ifShopNotEmpty'=>array(
		'description'=>'Alles, was sich zwischen dam Start- und Endtag befindet, wird angezeigt, wenn der Warenkorb nicht leer ist.',
	),
	'ifShopPayVat'=>array(
		'description'=>'Mit we:ifShopPayVat wird ein Inhalt nur bedingt angezeigt, wenn ein eingeloggter Kunde Mehrwertsteuern entrichten muss.',
	),
	'ifShopVat'=>array(
		'description'=>'Mit we:ifShopTag kann man den Mehrwertsteuersatz, des aktuellen Artikels des Dokuments, bzw. des Warenkorbs prüfen. Ist Id gesetzt, wird die des Mehrwertsteuersatzes des aktuellen (Dokument oder Warenkorb) Artikels mit der hier angegeben verglichen.',
	),
	'ifSidebar'=>array(
		'description'=>'Dieses Tag dient dazu, den umschliessenden Inhalt nur auszugeben, wenn der Seitenaufruf innerhalb der Sidebar stattfindet.',
	),
	'ifSubscribe'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der Eintrag in eine Newsletter-Adressliste erfolgreich war.',
	),
	'ifTdEmpty'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn eine Tabellenzelle in einer Listview leer ist (keine Inhalte vorhanden sind).',
	),
	'ifTdNotEmpty'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn eine Tabellenzelle in einer Listview nicht leer ist (also Inhalte vorhanden sind).',
	),
	'ifTemplate'=>array(
		'description'=>'Der umschlossene Inhalt wird angezeigt, wenn das aktuelle Dokument auf der angegebenen Vorlage beruht.',
	),
	'ifTop'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn sich das Tag nicht innerhalb einer includierten Datei befindet.',
	),
	'ifUnsubscribe'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur dann anzuzeigen, wenn der Austrag aus einer Newsletter-Adressliste erfolgreich war und sollte in der Vorlage nach &lt;we:addDelNewsletterEmail&gt; stehen.',
	),
	'ifUserInputEmpty'=>array(
		'description'=>'Alles was sich zwischen Start- und Endtag befindet, wird angezeigt, wenn ein UserInput-Feld, mit dem im Attribut match stehenden Namen, leer ist.',
	),
	'ifUserInputNotEmpty'=>array(
		'description'=>'Alles was sich zwischen Start- und Endtag befindet, wird angezeigt, wenn ein UserInput-Feld, mit dem im Attribut match stehenden Namen, nicht leer ist.',
	),
	'ifVarEmpty'=>array(
		'description'=>'Prüft, ob eine Variable leer ist. Ist dies der Fall, wird alles zwischen Start- und Endtag angezeigt.',
	),
	'ifVarNotEmpty'=>array(
		'description'=>'Prüft, ob eine Variable nicht leer ist. Ist dies der Fall, wird alles zwischen Start- und Endtag angezeigt.',
	),
	'ifVarSet'=>array(
		'description'=>'Mit diesem Tag kann man prüfen, ob eine Variable mit dem Namen name gesetzt ist. Achtung: Es ist ein Unterschied ob eine Variable leer ist oder nicht gesetzt wurde!',
	),
	'ifVar'=>array(
		'description'=>'Das we:ifVar-Tag bewirkt, daß alles, was zwischen dem Start- und Endtag steht, nur dann angezeigt wird, wenn die Variable mit dem Namen "name" gleich dem Wert ist, welcher in "match" eingetragen ist. Im Attribut "type" kann angegeben werden, um welchen Typ von Variable es sich handelt.',
	),
	'ifVoteActive'=>array(
		'description'=>'Alles, was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das Voting nicht abgelaufen ist.',
	),
	'ifVoteIsRequired'=>array(
		'description'=>'Alles, was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das Voting ein Pflichtfeld ist.',
	),
	'ifVote'=>array(
		'description'=>'Alles, was sich zwischen Start- und Endtag befindet, wird nur angezeigt, wenn das Voting erfolgreich gespeichert ist.',
	),
	'ifvotingexists'=>array(
		'description'=>'Führt den eingeschlossenen Code nur aus, wenn das Voting-Modul nicht deaktiviert wurde (Einstellungsdialog).',
	),
	'ifVotingFieldEmpty'=>array(
		'description'=>'&Uuml;berprüft ob ein VotingField leer ist, die Kombinationen von name und type Attributen entsprechen denen des we:votingFiled-Tags',
	),
	'ifVotingFieldNotEmpty'=>array(
		'description'=>'&Uuml;berprüft ob ein VotingField nicht leer ist, die Kombinationen von name und type Attributen entsprechen denen des we:votingFiled-Tags',
	),
	'ifVotingField'=>array(
		'description'=>'&Uuml;berprüft ob ein VotingField einen Wert entsprechend dem Attribut match hat, die Kombinationen von name und type Attributen entsprechen denen des we:votingFiled-Tags',
	),
	'ifVotingIsRequired'=>array(
		'description'=>'Gibt des umschlossenen Inhalt nur aus, wenn das Voting-Feld ein Pflichtfeld ist',
	),
	'ifWebEdition'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt nur innerhalb von webEdition anzuzeigen. Auf der fertig erzeugten Seite wird der umschlossene Inhalt nicht angezeigt.',
	),
	'ifWorkspace'=>array(
		'description'=>'&Uuml;berprüft, ob sich das Dokument in dem unter "path" bzw. "id" angegeben Arbeitsbereich befindet.',
	),
	'ifWritten'=>array(
		'description'=>'Alle was zwischen Start- und Endtag steht, wird nur angezeigt, wenn es keinen Fehler beim Schreiben eines Dokuments/Objekts mit dem Tag we:write gab. Bei einem Objekt muß type="object" sein.',
	),
	'img'=>array(
		'description'=>'Das we:img-Tag dient dazu, eine Grafik in den Inhalt eines Dokumentes einzubauen. Im Bearbeitungsmodus eines Dokumentes ist unter der Grafik ein Button "edit" sichtbar. Durch Anklicken des Buttons öffnet sich der Dateimanager, aus dem man eine Grafik auswählen oder neu anlegen kann. Wenn die Attribute "width", "height", "border", "hspace", "vspace", "alt" oder "align" gesetzt werden, dann werden diese Einstellungen für die Grafik verwendet, ansonsten gelten die Einstellungen, welche bei der Grafik gemacht wurden. Wenn das Attribut id gesetzt ist, dann wird die Grafik mit dieser ID benutzt, falls noch keine andere Grafik ausgewählt wurde. Das Attribut showimage ermöglicht es, das Bild im Bearbeiten-Modus nicht anzeigen zu lassen. Mit showinputs lassen sich die Eingabefelder für title und alt-text deaktivieren.',
	),
	'include'=>array(
		'description'=>'Mit diesem-Tag können Sie ein webEdition-Dokument oder eine HTML-Seite in die Vorlage einbinden. Dies ist besonders für Navigationen oder Teile, die auf jeder Vorlage gleich sind, zu empfehlen. Wenn Sie mit dem we:include-Tag arbeiten, brauchen Sie eine Änderung der Navigation nicht in allen Vorlagen ändern, sondern nur im einzubindenden Dokument. Danach brauchen Sie nur einen "rebuild" auszuführen, und alle Seiten werden automatisch geändert. Haben Sie nur dynamisch erzeugte Seiten, kann der "rebuild" entfallen. Anstelle des we:include Tags wird die Seite mit der unten angegebenen ID eingefügt. Mit dem Attribut "gethttp" können Sie bestimmen, ob die Seite per http geholt werden soll oder nicht. Das Attribut seeMode bestimmt, ob die Datei im seeMode als Include Datei bearbeitet werden kann, dies ist allerdings nur möglich wenn das Dokument per id included wird.',
	),
	'input'=>array(
		'description'=>'Das we:input-Tag bewirkt, daß im Bearbeitungsmodus des Dokumentes, das diese Vorlage zugrunde liegen hat, ein einzeiliges Eingabefeld erzeugt wird, wenn der Typ = "text" ausgewählt wird. Für die anderen Typen siehe Handbuch oder Hilfe.',
	),
	'js'=>array(
		'description'=>'Das we:js-Tag erzeugt ein HTML-Tag, das auf ein webEdition-internes Javascript-Dokument mit der unten angegebenen ID verweist. Dadurch können Sie Javascripts in einer separaten Datei definieren.',
	),
	'keywords'=>array(
		'description'=>'Das we:keywords-Tag erzeugt ein Schlüsselwort Meta-Tag. Alles zwischen Start- und Endtag wird als default-keywords eingetragen, falls das Schlüsselwortfeld in der Ansicht "Eigenschaft" leer ist. Ansonsten werden die Schlüsselworte aus der Ansicht "Eigenschaft" eingetragen.',
	),
	'linklist'=>array(
		'description'=>'Mit dem we:linklist-Tag kann man Linklisten generieren. Im Bearbeitungsmodus erscheint ein Plus-Button. Klickt man diesen Button, so wird der Liste ein neuer Link hinzugefügt. Innerhalb des Start- und Endtags wird mit Hilfe der Tags "we:link", "we:prelink" und "we:postlink", sowie normalem HTML, das Aussehen der Linkliste bestimmt. Alle eingefügten Links können mit einem Button "edit" verändert, oder mit einem Button "löschen" gelöscht werden.',
	),
	'linkToSeeMode'=>array(
		'description'=>'Dieser Tag erzeugt auf der Web-Seite einen Link, der das eben besuchte Dokument im seeMode von webEdition öffnet und somit einfach bearbeitet werden kann.',
	),
	'link'=>array(
		'description'=>'Das we:link-Tag erzeugt einen einzelnen Link, der durch einen Button "edit" verändert werden kann. Wird das Tag innerhalb von Linklisten verwendet, so darf das Attribut "name" nicht angegeben werden. Wird das Tag außerhalb von Linklisten verwendet, dann muß das Attribut "name" angegeben werden!Das Attribut only kann dazu eingesetzt werden nur ein einzelnes Attribut  (only="Attributname") oder den Inhalt (only="content") auszugeben.',
	),
	'listdir'=>array(
		'description'=>'Mit dem we:listdir-Tag kann man eine Liste erzeugen, welche alle Dateien im gleichen Verzeichnis anzeigt. Im Attribut "field" kann man angeben, welches Feld angezeigt wird. Ist das Feld leer oder gibt es das Feld nicht, wird der Dateiname angezeigt. Bei Verzeichnissen wird überprüft, ob es darin eine index Datei gibt und wenn ja, wird diese angezeigt. Im Attribut "dirfield" kann man angeben, welches Feld zur Anzeige benutzt werden soll. Ist das Feld leer oder gibt es das Feld nicht, wird der Eintrag von "field" bzw der Dateiname benutzt. Ist das Attribut "id" gesetzt, werden die Dateien vom Verzeichnis mit der angegebenen ID angezeigt.',
	),
	'listviewEnd'=>array(
		'description'=>'Dieses Tag gibt die Nummer des letzten Eintrags der aktuellen Seite einer &lt;we:listview&gt; aus.',
	),
	'listviewPageNr'=>array(
		'description'=>'Dieses Tag gibt die Nummer der aktuellen Seite einer &lt;we:listview&gt; aus.',
	),
	'listviewPages'=>array(
		'description'=>'Dieses Tag gibt die Anzahl der Seiten einer &lt;we:listview&gt; aus.',
	),
	'listviewRows'=>array(
		'description'=>'Dieses Tag gibt die Anzahl aller gefundenen Einträge einer &lt;we:listview&gt; aus.',
	),
	'listviewStart'=>array(
		'description'=>'Dises Tag gibt die Nummer des ersten Eintrags der aktuellen Seite einer &lt;we:listview&gt; aus.',
	),
	'listview'=>array(
		'description'=>'Das we:listview-Tag ist das Start- und Endtag von automatisch generierten Listen. (&Uuml;bersichtsseiten von News, usw.).',
	),
	'list'=>array(
		'description'=>'Mit dem we:list-Tag kann man erweiterbare Listen erzeugen. Alles, was zwischen Start- und Endtag steht, wird im Bearbeitungsmodus durch einen Klick auf den Plus-Button angehängt, bzw. eingefügt. Dies können beliebiges HTML sowie fast alle we:tags sein.',
	),
	'master'=>array(
		'description'=>'Wird in einer Detailvorlage verwendet und fügt den umschlossenen Inhalt beim we:content Tag im Mastertemplate ein. Die Verknüpfung zu we:content wird über das Attribut "name" hergestellt. (we:master name="head" =&gt; we:content name="head").<br/><br/>Inhalt der Detailvorlage außerhalb des we:master Tags wird weiterhin beim we:content ohne Name eingesetzt.',
	),
	'metadata'=>array(
		'description'=>'Das we:metadata-Tag wird benötigt, um die Metadatenfelder von eingebundenen Bildern, Flash-Movies und Quicktime-Movies darzustellen. Innerhalb des we:metadata Start- und Endtags kann man die Metadatenfelder mit we:field-Tags darstellen. Im Attribut "name" muss der Name des darzustellenden Elements angegeben werden.',
	),
	'navigationEntries'=>array(
		'description'=>'Dient innerhalb eines we:navigationEntry type="folder" als Platzhalter für alle Einträge eines Ordners der Navigation.',
	),
	'navigationEntry'=>array(
		'description'=>'Mit we:navigationEntry kann das Aussehen eines Eintrags innerhalb der Navigation beeinflusst werden. Mit den Attributen "type", "level", "current" und "position" kann man sich dabei gezielt einzelne Elemente verschiedenster Ebene rauspicken und ausgeben.',
	),
	'navigationField'=>array(
		'description'=>'Mit dem Tag &lt;we:navigationField&gt; kann innerhalb eines &lt;we:navigationEntry&gt; ein Wert des aktuellen Navigationseintrags ausgegeben werden.<br/> Wählen Sie dabei <b>entweder</b> einen Eintrag für das Attribut <i>name</i>, <b>oder</b> einen Eintrag für das Attribut <i>attributes</i>, <b>oder</b> einen Eintrag für das Attribut <i>complete</i>',
	),
	'navigationWrite'=>array(
		'description'=>'Wird benutzt um eine we:navigation mit gegebenem Name zu schreiben.',
	),
	'navigation'=>array(
		'description'=>'Mit we:navigation wird eine innerhalb des Navigationstools erstellte Navigation initialisiert.',
	),
	'newsletterConfirmLink'=>array(
		'defaultvalue'=>'Newsletter bestätigen',
		'description'=>'Dieser Tag dient dazu, einen Bestätigungs-Link für einen Double-Opt-In zu erstellen. Ein Newsletter-Interessent kann so bestätigen, dass er den Newsletter abonnieren möchte.',
	),
	'newsletterField'=>array(
		'description'=>'Ein Feld aus dem Empfängerdatensatz der Kundenverwaltung innerhalb eines Newsletters anzeigen.',
	),
	'newsletterSalutation'=>array(
		'description'=>'Mit diesem Tag kann man Anrede-Felder anzeigen.',
	),
	'newsletterUnsubscribeLink'=>array(
		'description'=>'Das we:newsletterUnsubscribeLink-Tag erzeugt ein HTML-Link-Tag zum Austragen aus der Newsletterliste. Dieses Tag kann nur in E-Mail Vorlagen benutzt werden!',
	),
	'next'=>array(
		'description'=>'Das we:next-Tag erzeugt ein HTML-Link-Tag, das auf die nächste we:listview-Seite verweist. Der gesamte Inhalt zwischen Start- und Endtag wird verlinkt.',
	),
	'noCache'=>array(
		'description'=>'Innerhalb dieses Tags kann PHP-Code stehen, welcher bei einer gecachten Vorlage (Ausnahme: Full-Cache) immer ausgeführt werden soll.',
	),
	'objectLanguage'=>array(
		'description'=>'Gibt die dem Objekt zugewiesene Sprache aus',
	),
	'object'=>array(
		'description'=>'Das we:object-Tag wird benötigt, um Objekte darzustellen. Innerhalb des we:object Start- und Endtags kann man Felder des Objekts mit we:field-Tags darstellen. Ist nur das Attribut "name" gesetzt, erscheint im Edit Mode ein Objekt-Selector, in dem der Redakteur aus allen vorhandenen Objekten aller Klassen auswählen kann. Schränkt man mit dem Attribut "classid" durch Angabe der ID einer Klasse die Auswahl ein, können jetzt nur noch Objekte dieser Klasse ausgewählt werden. Im Attribut "id" kann man ein festes Objekt der gesetzten classid bestimmen. Das Attribut "triggerid" wird gebraucht, um bei einer statisch erzeugten Objekt-Listview ein dynamisches Dokument anzugeben, welches bei Objekt-Links benötigt wird, um das entsprechende Objekt darzustellen.',
	),
	'orderitem'=>array(
		'description'=>'Mit Hilfe dieses Tags kann man einen einzelnen Artikel einer Bestellung auf einer webEdition darstellen. Die Felder des Artikels werden wie bei einer Listview und beim <we:object> Tag mit dem Tag <we:field> dargestellt.',
	),
	'order'=>array(
		'description'=>'Mit Hilfe dieses Tags kann man eine Bestellung auf einer webEdition darstellen. Die Felder der Bestellung werden wie bei einer Listview und beim <we:object> Tag mit dem Tag <we:field> dargestellt.',
	),
	'pageLanguage'=>array(
		'description'=>'Gibt die dem Dokument zugewiesene Sprache aus',
	),
	'pagelogger'=>array(
		'description'=>'Das we:pagelogger-Tag erzeugt je nach gewähltem Attribut "type" den von pageLogger erforderlichen Erfassungscode oder den Fileserver- bzw. Download-Code.',
	),
	'path'=>array(
		'description'=>'Das we:path-Tag stellt den Pfad des aktuellen Dokuments dar. Gibt es in einem der Unterverzeichnisse eine index-Datei, wird ein Link auf das Verzeichnis gesetzt. Im Attribut index kann man die verwendeten index-Dateien (mit Kommas getrennt) angeben. Ist dort nichts angegeben, werden "index.html", "index.htm", "index.php", "default.htm", "default.html und "default.php" als Voreinstellung benutzt. Im Attribut home kann man angeben, was ganz am Anfang des Pfades stehen soll. Ist nichts angegeben, wird automatisch "home" angezeigt. Das Attribut separator beschreibt das Trennzeichen zwischen den Verzeichnissen. Ist das Attribut leer, dann wird ein "/" als Trennzeichen verwendet. Im Attribut "field" kann man angeben, welches Feld angezeigt wird. Ist das Feld leer oder gibt es das Feld nicht, wird der Dateiname angezeigt. Im Attribut "dirfield" kann man angeben, welches Feld zur Anzeige bei Verzeichnissen benutzt werden soll. Ist das Feld leer oder gibt es das Feld nicht, wird der Eintrag von "field" bzw der Dateiname benutzt.',
	),
	'paypal'=>array(
		'description'=>'we:paypal stellt eine Schnittstelle zu paypal zur Verfügung. Damit können sie Ihre Verkäufe bequem über einen Payment-Provider abwickeln. Beachten Sie hierbei, dass sie im Backend des Shop Moduls weitere Parameter angeben müssen.',
	),
	'position'=>array(
		'description'=>'Das Tag we:position wird eingesetzt, um die aktuelle Position der duchlaufenen Listview, Block, Linklist oder einer Linklist auszugeben. Wird type "block" oder "linklist" verwendet, muss zusätzlich der Name (reference) des entsprechenden Blocks/Linklist angegeben werden. &Uuml;ber das Attribut "format" kann die Formatierung der Positionsangabe angegeben werden.',
	),
	'postlink'=>array(
		'description'=>'Das we:postlink-Tag wird benötigt, um Code einzugrenzen, welcher beim letzten Durchlauf der Linkliste nicht angezeigt werden soll.',
	),
	'prelink'=>array(
		'description'=>'Das we:prelink-Tag wird benötigt, um Code einzugrenzen, welcher beim ersten Durchlauf der Linkliste nicht angezeigt werden soll.',
	),
	'printVersion'=>array(
		'description'=>'Das we:printVersion-Tag erzeugt ein HTML-Link-Tag, das auf das gleiche Dokument, aber mit einer anderen Vorlage, verweist. Das Attribut tid bestimmt die id der Vorlage. Der gesamte Inhalt zwischen Start- und Endtag wird verlinkt.',
	),
	'processDateSelect'=>array(
		'description'=>'Das &lt;we:processDateSelect&gt;-Tag wandelt die 3 Werte, der Select Boxen des we:dateSelect-Tags in einen Unix Timestamp und schreibt diesen Wert in die globale Variable mit dem in "name" angegeben Namen.',
	),
	'quicktime'=>array(
		'description'=>'Das we:quicktime-Tag dient dazu, einen Quicktime Movie in den Inhalt des Dokumentes einzubauen. Im Bearbeitungsmodus eines Dokumentes, das diese Vorlage zugrunde liegen hat, ist ein Button "edit" sichtbar. Durch Anklicken dieses Buttons, öffnet sich ein Dateimanager, in dem man einen Quicktime Movie, der zuvor in webEdition angelegt wurde, auswählen kann. Für das Tag we:quicktime gibt es momentan leider keine xhtml-valide Ausgabe, die auf gängigen Browsern korrekt ausgeführt wird. Daher wird dem Attribut "xml" unabhängig von der hier gemachten Einstellung immer der Wert "false" zugeordnet.',
	),
	'registeredUser'=>array(
		'description'=>'Dieses Tag dient dazu, Daten eines bestimmten Kunden, der in der Kundenverwaltung eingetragen ist, anzuzeigen.',
	),
	'registerSwitch'=>array(
		'description'=>'Dieses Tag erzeugt im Edit-Mode einen Umschalter, mit dem man zwischen dem Status eines registrierten und eines unregistrierten Benutzers umschalten kann. Das ist sinnvoll bei der Verwendung der Tags &lt;we:ifRegisteredUser&gt; und &lt;we:ifNotRgisteredUser&gt;, um die verschiedenen Ansichten zu überprüfen und volle Sicherheit über das Layout zu haben.',
	),
	'repeatShopItem'=>array(
		'description'=>'Dieses Tag erstellt eine Liste aller Artikel im Warenkorb.',
	),
	'repeat'=>array(
		'description'=>'Dieses Tag dient dazu, den umschlossenen Inhalt innerhalb von &lt;we:listview&gt; pro gefundenem Eintrag zu wiederholen.',
	),
	'returnPage'=>array(
		'description'=>'Dieses Tag dient dazu, den URL der Ursprungsseite auzugeben, wenn der Wert das Attributs "return" von &lt;we:a edit="document"&gt; bzw. &lt;we:a edit="object"&gt; gleich "true" war.',
	),
	'saferpay'=>array(
		'description'=>'we:saferpay stellt eine Schnittstelle zu saferpay zur Verfügung. Damit können sie Ihre Verkäufe bequem über einen Payment-Provider abwickeln. Beachten Sie hierbei, dass sie im Backend des Shop Moduls weitere Parameter angeben müssen.',
	),
	'saveRegisteredUser'=>array(
		'description'=>'Dieses Tag dient zum Abspeichern von Kundendaten die über Sessionfields eingegeben wurden.',
	),
	'search'=>array(
		'description'=>'Das we:search-Tag erzeugt ein Eingabefeld oder ein Textfeld, das für Suchanfragen genutzt werden soll. Das Suchfeld hat intern den Namen "we_lv_search_0". Wenn die Suchform also gesendet wird, dann wird auf der empfangenden Webseite die PHP-Variable $_REQUEST["we_lv_search_0"] mit dem Inhalt des Eingabefeldes gefüllt sein.',
	),
	'select'=>array(
		'description'=>'Das we:select-Tag erzeugt im Bearbeitungsmodus eine Auswahlbox für die Eingabe. Wird bei Size eine 1 eingetragen (Size = "1"), so erscheint die Auswahlbox als Popup-Menü. Dieses Tag verhält sich genau wie ein HTML-Select-Tag. Innerhalb von Start- und Endtag werden die Einträge durch normale HTML-Options-Tags bestimmt.',
	),
	'sendMail'=>array(
		'description'=>'Das we:sendMail-Tag verschickt eine webEdition-Seite als E-Mail an die im Attribut "recipient" eingetragenen Adressen.',
	),
	'sessionField'=>array(
		'description'=>'Das we:sessionField-Tag erzeugt ein HTML Input, Select oder Textarea-Tag, welches für die Eingabe von Session-Feldern (Kundendaten oä.) verwendet wird.',
	),
	'sessionLogout'=>array(
		'description'=>'Das we:sessionLogout-Tag erzeugt ein HTML-Link-Tag, das auf ein webEdition-internes Dokument mit der unten angegebenen ID verweist. Wenn das angegebene Dokument ein we:sessionStart-Tag besitzt und dynamisch gespeichert wurde, dann wird die aktuelle Session gelöscht.',
	),
	'sessionStart'=>array(
		'description'=>'Dieses Tag dient dazu, eine Session zu beginnen oder eine bestehende fortzusetzen. Dieses Tag wird auf folgenden Vorlagen benötigt: bei Seiten, welche durch die Kundenverwaltung geschützt sind, bei Shopseiten und bei Frontend-Eingaben (Erzeugung von webEdition-Dokumenten und Objekten über das Frontend).<br/>Das Tag muss immer in der ersten Zeile der Vorlage stehen!',
	),
	'setVar'=>array(
		'description'=>'Mit we:setVar können verschiedene Arten von Variablen gesetzt werden.<br/><strong>Achtung:</strong> Ohne gesetztes Attribut <strong>striptags</strong> werden dabei HTML- und PHP-Code mit übertragen, dies ist ein potenzielles <strong>Sicherheitsrisiko!</strong>',
	),
	'shipping'=>array(
		'description'=>'we:shipping ermittelt die bei einem Einkauf fälligen Versandkosten. Dabei wird auf den Wert des Warenkorbs, das Herkunftsland des eingeloggten Kunden und die innerhalb des Shop Moduls eingepflegten Regeln für Porto- und Versandkosten zugegriffen, um die anfallenden Kosten zu ermitteln. Mit dem Parameter "sum" übergibt man den Namen einer mit we:sum errechneten Summe. Mit dem Parameter type kann gezielt der Netto- (net), bzw. Brutto- (gros) Betrag sowie der Mehrwertsteueranteil (vat) ermittelt werden.',
	),
	'shopField'=>array(
		'description'=>'Mit we:shopField können verschiedene Felder direkt bei Artikeln, bzw. im Warenkorb (Bestellung) abgespeichert werden. Diese Felder können vom Administrator mit verschiedenen Werten festgelegt werden, die der Endkunde dann einstellen kann. Neben Artikel-Varianten ist es damit möglich, eine Vielzahl von verschiedenen Artikelvariationen auf einfache Weise abzubilden.',
	),
	'shopVat'=>array(
		'description'=>'Mit we:shopVat ist es möglich einen Mehrwertsteuersatz für einen Artikel festzulegen. Mehrwertsteuersätze können direkt im Shop Modul gepflegt werden. Ist Id gesetzt, wird der Mehrwertsteuersatz mit der angegebenen Id ausgegeben.',
	),
	'showShopItemNumber'=>array(
		'description'=>'Das we:showShopItemNumber-Tag zeigt die im Warenkorb vorhandene Anzahl der Artikel eines Typs an.',
	),
	'sidebar'=>array(
		'defaultvalue'=>'Sidebar öffnen',
		'description'=>'Dieses Tag dient dazu einen Button im Bearbeitenmodus eines Dokumentes zum öffnen einer Webseite in der Sidebar zu öffnen einzubinden.',
	),
	'subscribe'=>array(
		'description'=>'Dieses Tag erzeugt ein Eingabefeld zum Eintragen in die Newsletter-Liste. Mit dem Attribut "type" kann bestimmt werden, um welche Art Feld es sich handelt.',
	),
	'sum'=>array(
		'description'=>'Das we:sum-Tag addiert alle Zahlen in einer Liste zusammen.',
	),
	'target'=>array(
		'description'=>'Dieses Tag dient dazu, innerhalb von &lt;we:linklist&gt; das Linkziel auszugeben.',
	),
	'textarea'=>array(
		'description'=>'Das we:textarea-Tag erzeugt ein mehrzeiliges Eingabefeld.',
	),
	'title'=>array(
		'description'=>'Das we:title-Tag erzeugt ein normales title-Tag. Alles, was zwischen dem Start- und Endtag steht, wird als default-Titel eingetragen, falls das Titelfeld in der Ansicht "Eigenschaft" leer ist. Ansonsten wird der Titel aus dieser Ansicht eingetragen.',
	),
	'tr'=>array(
		'description'=>'Das &lt;we:tr&gt; Tag entspricht dem HTML-tag &lt;tr&gt; und dient der Definition einer Tabellenzeile.<br/>Beim Einsatz in einer Listview erzwingt der Tag alle x Datensätze eine neue Tabellenzeile, wobei x die Anzahl der im Listview Parameter cols eingestellten Spalten ist.',
	),
	'unsubscribe'=>array(
		'description'=>'Dieses Tag erzeugt ein Eingabefeld zum Austragen aus der Newsletter-Liste. Dieser Tag muss innerhalb eines Formulars platziert werden.<br/>Auf der Folgeseite muss der Tag &lt;we:addDelNewsletterEmail/&gt; vorhanden sein, bei diesem legen Sie auch den Speicherort der CSV Empfängerlisten fest, aus denen der Empfänger ausgetragen werden soll.',
	),
	'url'=>array(
		'description'=>'Das we:url-Tag erzeugt eine webEdition-interne URL, die auf das Dokument mit der unten angegebenen ID verlinkt.',
	),
	'userInput'=>array(
		'description'=>'Das we:userInput-Tag erzeugt Eingabefelder um in Verbindung mit we:form type="document" bzw. type="object" Dokumente oder Objekte zu erzeugen.',
	),
	'useShopVariant'=>array(
		'description'=>'Das we:shopVariant-Tag übernimmt die Daten einer per Namen übergebenen Artikel-Variante. Existiert kein Artikel-Variante mit gegebenem Namen, wird der Original-Artikel verwendet.',
	),
	'var'=>array(
		'description'=>'Das we:var-Tag zeigt den Inhalt einer globalen Php-Variablen bzw. den Inhalt eines Dokumentfeldes mit dem unten eingegebenen Namen an.',
	),
	'votingField'=>array(
		'description'=>'Das we:votingField-Tag wird benötigt, um den Inhalt eines Votings anzuzeigen. Das Attribut name definiert den zu zeigenden Inhalt, das Attribut type die Art der Anzeige. Gültige name-type Kombinationen sind: question - text; result - count, percent, total; id - answer, select, radio, voting; answer - text, radio, checkbox (Mehrfachauswahl), select (Mehrfachauswahl), textinput und textarea (freies Textantwortfeld), image (hier sind alle we:img Attribute wie thumbnail usw. möglich), media (liefert über to und nameto den Pfad der Datei);',
	),
	'votingList'=>array(
		'description'=>'Mit diesem Tag können Sie automatisch Listen über Ihre Votings generieren.',
	),
	'votingSelect'=>array(
		'description'=>'Mit diesem Tag wird ein DropDown-Menü (&lt;select&gt;) erzeugt, mit dem es möglich ist, ein Voting auszuwählen.',
	),
	'votingSession'=>array(
		'description'=>'Generiert einen eindeutigen Identifier, der mit ins Voting-Log aufgenommen wird und so erlaubt, die Antworten zu verschienenen Fragen einer Befragung einander zuzuordnen',
	),
	'voting'=>array(
		'description'=>'Das we:voting-Tag wird benötigt, um Votings darzustellen.',
	),
	'writeShopData'=>array(
		'description'=>'Das we:writeShopData-Tag schreibt alle Daten des aktuellen Warenkorbs in die Datenbank.',
	),
	'writeVoting'=>array(
		'description'=>'Dieses Tag schreibt ein Voting in die Datenbank. Falls das Attribut "id" definiert ist, wird nur das Voting mit dieser id gespeichert.<br/><br/>Hinweis:  WICHTIG! Das Tag &lt;we:writeVoting/&gt; muss in der allerersten Zeile der Vorlage stehen, in der es verwendet wird. Andernfalls ist eine &Uuml;berprüfung des Abstimmungsintervalls per COOKIE nicht möglich!',
	),
	'write'=>array(
		'description'=>'Das we:write Tag schreibt ein zuvor mit &lt;we:form type="document/object"&gt; erzeugtes webEdition Dokument/Objekt.',
	),
	'xmlfeed'=>array(
		'description'=>'Das we:xmlfeed Tag lädt den XML-Inhalt von der eingegebenen URL.',
	),
	'xmlnode'=>array(
		'description'=>'Das we:xmlnode Tag erzeugt ein XML-Element aus einem vorgegebenen XML-Feed oder URL.',
));