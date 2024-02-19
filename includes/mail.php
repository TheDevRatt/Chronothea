<?php
require_once "Mail.php";  //this includes the pear SMTP mail library
$from = "Password System Reset <noreply@loki.trentu.ca>";
$to = "Matthew Makary <email@sample.com>";  //put user's email here
$subject = "This is the subject";
$body = "This is the message body";
$host = "smtp.trentu.ca";
$headers = array(
  'From' => $from,
  'To' => $to,
  'Subject' => $subject
);
$smtp = Mail::factory(
  'smtp',
  array('host' => $host)
);

$mail = $smtp->send($to, $headers, $body);
if (PEAR::isError($mail)) {
  echo ("<p>" . $mail->getMessage() . "</p>");
} else {
  echo ("<p>Message successfully sent!</p>");
}
