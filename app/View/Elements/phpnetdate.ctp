<table class="doctable table">
<caption><strong>Die folgenden Zeichen werden im Parameter <code class="parameter">Format</code>
 erkannt</strong></caption>

 <thead>
  <tr>
   <th><code class="parameter">Format</code>-Zeichen</th>
   <th>Beschreibung</th>
   <th>Beispiel für Rückgabewerte</th>
  </tr>

 </thead>

 <tbody class="tbody">
  <tr>
   <td style="text-align: center;"><em class="emphasis">Tag</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>d</em></td>
   <td>Tag des Monats, 2-stellig mit führender Null</td>
   <td><em>01</em> bis <em>31</em></td>
  </tr>

  <tr>
   <td><em>D</em></td>
   <td>Wochentag, gekürzt auf drei Buchstaben</td>
   <td><em>Mon</em> bis <em>Sun</em></td>
  </tr>

  <tr>
   <td><em>j</em></td>
   <td>Tag des Monats ohne führende Nullen</td>
   <td><em>1</em> bis <em>31</em></td>
  </tr>

  <tr>
   <td><em>l</em> (kleines 'L')</td>
   <td>Ausgeschriebener Wochentags</td>
   <td><em>Sunday</em> bis <em>Saturday</em></td>
  </tr>

  <tr>
   <td><em>N</em></td>
   <td>Numerische Repräsentation des Wochentages gemäß ISO-8601 (in PHP 5.1.0 hinzugefügt)</td>
   <td><em>1</em> (für Montag) bis <em>7</em> (für Sonntag)</td>
  </tr>

  <tr>
   <td><em>S</em></td>
   <td>Anhang der englischen Aufzählung für einen Monatstag, zwei Zeichen</td>
   <td>
    <em>st</em>, <em>nd</em>, <em>rd</em> oder
   <em>th</em>. Zur Verwendung mit <em>j</em> empfohlen.
   </td>
  </tr>

  <tr>
   <td><em>w</em></td>
   <td>Numerischer Tag einer Woche</td>
   <td><em>0</em> (für Sonntag) bis <em>6</em> (für Samstag)</td>
  </tr>

  <tr>
   <td><em>z</em></td>
   <td>Der Tag des Jahres (von 0 beginnend)</td>
   <td><em>0</em> bis <em>365</em></td>
  </tr>

  <tr>
   <td style="text-align: center;"><em class="emphasis">Woche</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>W</em></td>
   <td>ISO-8601 Wochennummer des Jahres, die Woche beginnt am Montag (hinzugefügt in PHP 4.1.0)</td>
   <td>Beispiel: <em>42</em> (die 42. Woche im Jahr)</td>
  </tr>

  <tr>
   <td style="text-align: center;"><em class="emphasis">Monat</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>F</em></td>
   <td>Monat als ganzes Wort, wie January oder March</td>
   <td><em>January</em> bis <em>December</em></td>
  </tr>

  <tr>
   <td><em>m</em></td>
   <td>Monat als Zahl, mit führenden Nullen</td>
   <td><em>01</em> bis <em>12</em></td>
  </tr>

  <tr>
   <td><em>M</em></td>
   <td>Monatsname mit drei Buchstaben</td>
   <td><em>Jan</em> bis <em>Dec</em></td>
  </tr>

  <tr>
   <td><em>n</em></td>
   <td>Monatszahl, ohne führende Nullen</td>
   <td><em>1</em> bis <em>12</em></td>
  </tr>

  <tr>
   <td><em>t</em></td>
   <td>Anzahl der Tage des angegebenen Monats</td>
   <td><em>28</em> bis <em>31</em></td>
  </tr>

  <tr>
   <td style="text-align: center;"><em class="emphasis">Jahr</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>L</em></td>
   <td>Schaltjahr oder nicht</td>
   <td><em>1</em> für ein Schaltjahr, ansonsten <em>0</em></td>
  </tr>

  <tr>
   <td><em>o</em></td>
   <td>Jahreszahl gemäß ISO-8601. Dies ergibt den gleichen Wert
    wie <em>Y</em>, außer wenn die ISO-Kalenderwoche
    (<em>W</em>) zum vorhergehenden oder nächsten Jahr
    gehört, wobei dann jenes Jahr verwendet wird (in PHP 5.1.0 
    hinzugefügt).</td>
   <td>Beispiele: <em>1999</em> oder <em>2003</em></td>
  </tr>

  <tr>
   <td><em>Y</em></td>
   <td>Vierstellige Jahreszahl</td>
   <td>Beispiele: <em>1999</em> oder <em>2003</em></td>
  </tr>

  <tr>
   <td><em>y</em></td>
   <td>Jahreszahl, zweistellig</td>
   <td>Beispiele: <em>99</em> oder <em>03</em></td>
  </tr>

  <tr>
   <td style="text-align: center;"><em class="emphasis">Uhrzeit</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>a</em></td>
   <td>Kleingeschrieben: Ante meridiem (Vormittag) und 
    Post meridiem (Nachmittag)</td>
   <td><em>am</em> oder <em>pm</em></td>
  </tr>

  <tr>
   <td><em>A</em></td>
   <td>Großgeschrieben: Ante meridiem (Vormittag) und 
    Post meridiem (Nachmittag)</td>
   <td><em>AM</em> oder <em>PM</em></td>
  </tr>

  <tr>
   <td><em>B</em></td>
   <td>Swatch-Internet-Zeit</td>
   <td><em>000</em> bis <em>999</em></td>
  </tr>

  <tr>
   <td><em>g</em></td>
   <td>Stunde im 12-Stunden-Format, ohne führende Nullen</td>
   <td><em>1</em> bis <em>12</em></td>
  </tr>

  <tr>
   <td><em>G</em></td>
   <td>Stunde im 24-Stunden-Format, ohne führende Nullen</td>
   <td><em>0</em> bis <em>23</em></td>
  </tr>

  <tr>
   <td><em>h</em></td>
   <td>Stunde im 12-Stunden-Format, mit führenden Nullen</td>
   <td><em>01</em> bis <em>12</em></td>
  </tr>

  <tr>
   <td><em>H</em></td>
   <td>Stunde im 24-Stunden-Format, mit führenden Nullen</td>
   <td><em>00</em> bis <em>23</em></td>
  </tr>

  <tr>
   <td><em>i</em></td>
   <td>Minuten, mit führenden Nullen</td>
   <td><em>00</em> bis <em>59</em></td>
  </tr>

  <tr>
   <td><em>s</em></td>
   <td>Sekunden, mit führenden Nullen</td>
   <td><em>00</em> bis <em>59</em></td>
  </tr>

  <tr>
   <td><em>u</em></td>
   <td>
    Mikrosekunden
   </td>
   <td>Beispiel: <em>654321</em></td>
  </tr>
  <tr>
   <td style="text-align: center;"><em class="emphasis">Zeitzone</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>e</em></td>
   <td>Zeitzonen-Bezeichner (hinzugefügt in PHP 5.1.0)</td>
   <td>Beispiele: <em>UTC</em>, <em>GMT</em>, <em>Atlantic/Azores</em></td>
  </tr>

  <tr>
   <td><em>I</em> (großes 'i')</td>
   <td>Fällt ein Datum in die Sommerzeit</td>
   <td><em>1</em> bei Sommerzeit, ansonsten <em>0</em>.</td>
  </tr>

  <tr>
   <td><em>O</em></td>
   <td>Zeitunterschied zur Greenwich time (GMT) in Stunden</td>
   <td>Beispiel: <em>+0200</em></td>
  </tr>

  <tr>
   <td><em>P</em></td>
   <td>Zeitunterschied zur Greenwich time (GMT) in Stunden mit Doppelpunkt 
    zwischen Stunden und Minuten (hinzugefügt in PHP 5.1.3)</td>
   <td>Beispiel: <em>+02:00</em></td>
  </tr>

  <tr>
   <td><em>T</em></td>
   <td>Abkürzung der Zeitzone</td>
   <td>Beispiele: <em>EST</em>, <em>MDT</em> ...</td>
  </tr>

  <tr>
   <td><em>Z</em></td>
   <td>Offset der Zeitzone in Sekunden. Der Offset für Zeitzonen westlich von
    UTC ist immer negativ und für Zeitzonen östlich von UTC immer 
    positiv.</td>
   <td><em>-43200</em> bis <em>50400</em></td>
  </tr>

  <tr>
   <td style="text-align: center;"><em class="emphasis">Vollständige(s) Datum/Uhrzeit</em></td>
   <td>---</td>
   <td>---</td>
  </tr>

  <tr>
   <td><em>c</em></td>
   <td>ISO 8601 Datum (hinzugefügt in PHP 5)</td>
   <td>2004-02-12T15:19:21+00:00</td>
  </tr>

  <tr>
   <td><em>r</em></td>
   <td>Gemäß <a target="_blank" class="link external" href="http://www.faqs.org/rfcs/rfc2822">»&nbsp;RFC 2822</a> formatiertes Datum</td>
   <td>Beispiel: <em>Thu, 21 Dec 2000 16:01:07 +0200</em></td>
  </tr>

  <tr>
   <td><em>U</em></td>
   <td>Sekunden seit Beginn der UNIX-Epoche (January 1 1970 00:00:00 GMT)</td>
   <td></td>
  </tr>

 </tbody>

</table>