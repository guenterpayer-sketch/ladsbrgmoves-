-- Migration: Impressum & Datenschutz redaktionell pflegbar machen.
-- Bisher stand der komplette Fließtext fest im PHP-Code; jetzt liegt er als
-- ein HTML-Textblock pro Seite in content_blocks (Admin -> Texte), damit
-- kurzfristige rechtliche Anpassungen ohne Code-Deploy möglich sind.
-- {{contact_name}}/{{contact_address}}/{{contact_phone}}/{{contact_email}}
-- werden beim Anzeigen automatisch durch die zentralen Kontaktdaten ersetzt.
-- Über phpMyAdmin einmalig ausführen (--default-character-set=utf8mb4!).

INSERT INTO content_blocks (content_key, label, content_value) VALUES
('legal_impressum_body', 'Impressum: Seiteninhalt (HTML)', '<h2>Angaben gemäß § 5 TMG</h2>
<p>
  {{contact_name}}<br>
  Inhaber: Günter Payer<br>
  {{contact_address}}
</p>

<h2>Kontakt</h2>
<p>
  Telefon: {{contact_phone}}<br>
  E-Mail: {{contact_email}}
</p>

<h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
<p>
  Günter Payer<br>
  {{contact_address}}
</p>

<p style="margin-top:32px;font-size:0.8rem;color:var(--text-dimmer);">
  Hinweis: Dies ist ein nach § 5 TMG minimal notwendiges Standard-Impressum auf Basis der
  verfügbaren Kontaktdaten. Bitte prüfen, ob weitere Pflichtangaben ergänzt werden müssen
  (z.B. Umsatzsteuer-ID, Handelsregistereintrag), falls zutreffend.
</p>'),
('legal_datenschutz_body', 'Datenschutz: Seiteninhalt (HTML)', '<h2>Allgemeiner Datenschutzpassus</h2>
<p>Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren
personenbezogenen Daten passiert, wenn Sie diese Website besuchen. Personenbezogene
Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können.</p>

<h2>Hinweis zur verantwortlichen Stelle</h2>
<p>
  {{contact_name}}, Günter Payer<br>
  {{contact_address}}<br>
  Telefon: {{contact_phone}}<br>
  E-Mail: {{contact_email}}
</p>
<p>Verantwortliche Stelle ist die natürliche oder juristische Person, die allein oder
gemeinsam mit anderen über die Zwecke und Mittel der Verarbeitung von personenbezogenen
Daten entscheidet.</p>

<h2>Wie erfassen wir Ihre Daten?</h2>
<p>Ihre Daten werden zum einen dadurch erhoben, dass Sie uns diese mitteilen, z.B. über ein
Anmeldeformular für eine Schnupperstunde. Andere Daten werden automatisch beim Besuch der
Website erfasst (z.B. Internetbrowser, Betriebssystem, Uhrzeit des Seitenaufrufs).</p>

<h2>Wofür nutzen wir Ihre Daten?</h2>
<p>Ein Teil der Daten wird erhoben, um eine fehlerfreie Bereitstellung der Website zu
gewährleisten. Andere Daten können zur Analyse Ihres Nutzerverhaltens verwendet werden.</p>

<h2>Anmeldeformular für Schnupperstunden (NimbusCloud)</h2>
<p>Für die Buchung einer kostenlosen Schnupperstunde wird ein Anmeldeformular unseres
externen Anbieters NimbusCloud (tanzcenter-payer.nimbuscloud.at) eingebunden. Beim Öffnen
dieses Formulars werden Daten an NimbusCloud übertragen; es gilt zusätzlich die
Datenschutzerklärung von NimbusCloud.</p>

<h2>Welche Rechte haben Sie bezüglich Ihrer Daten?</h2>
<p>Sie haben jederzeit das Recht, unentgeltlich Auskunft über Herkunft, Empfänger und
Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten sowie deren Berichtigung,
Sperrung oder Löschung zu verlangen. Hierzu sowie zu weiteren Fragen können Sie sich
jederzeit an die oben genannte Adresse wenden. Außerdem steht Ihnen ein Beschwerderecht
bei der zuständigen Aufsichtsbehörde zu.</p>

<h2>SSL-/TLS-Verschlüsselung</h2>
<p>Diese Seite nutzt aus Sicherheitsgründen eine SSL- bzw. TLS-Verschlüsselung. Eine
verschlüsselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von
„http://" auf „https://" wechselt und an dem Schloss-Symbol in der Browserzeile.</p>

<h2>Cookies</h2>
<p>Diese Website verwendet ggf. Cookies, um das Angebot nutzerfreundlicher, effektiver und
sicherer zu machen. Die meisten der verwendeten Cookies sind sogenannte
„Session-Cookies", die nach Ende des Besuchs automatisch gelöscht werden.</p>

<p style="margin-top:32px;font-size:0.8rem;color:var(--text-dimmer);">
  Hinweis: Dieser Text basiert auf der bisherigen Datenschutzerklärung von
  hiphoplandsberg.de und wurde um den Hinweis zur NimbusCloud-Einbindung ergänzt. Bitte
  fachlich prüfen (lassen), bevor die Seite live geht.
</p>');
