<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Only process POST requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form fields and remove whitespace.
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Check that data was sent to the mailer.
    if (empty($name) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set a 400 (bad request) response code and exit.
        http_response_code(400);
        echo "Lütfen formu eksiksiz doldurun ve tekrar deneyin.";
        exit;
    }

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0; // Hata ayıklama modunu kapatın
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP sunucusu
        $mail->SMTPAuth = true;
        $mail->Username = 'samismtp26@gmail.com'; // SMTP kullanıcı adı
        $mail->Password = 'ydmacdfgigqgrcxe'; // SMTP şifresi veya uygulama şifresi
        $mail->CharSet = "utf8";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your-email@example.com', 'Samisonmez');
        $mail->addAddress('sonmezsami2@gmail.com'); // Alıcı e-posta adresi

        // Attachments
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            // Dosya yükleme hatalarını kontrol et
            if ($_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Dosya yükleme hatası: ' . $_FILES['attachment']['error']);
            }

            // Dosya yükleme geçici dizinini ve izinlerini kontrol et
            if (!is_uploaded_file($_FILES['attachment']['tmp_name'])) {
                throw new Exception('Geçici dosya bulunamadı.');
            }

            // Dosyayı e-posta ekine ekle
            $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $_POST["subject"];
        $mail->Body = "<strong>Ad:</strong>" . $_POST["name"] . "<br><br>" ."<strong>Mail Adresi: </strong>" . $_POST["email"] . "<br><br>" . "<strong>Ehliyet sınıfı: </strong> " . $_POST["subject"] . "<br><br>" . "<strong>Konu: </strong> " . $_POST["message"];

        // Gönderimi yap
        $mail->send();

        // Başarılı yanıt kodu
        http_response_code(200);
        echo "Teşekkürler! Mesajınız başarıyla gönderildi.";
    } catch (Exception $e) {
        // Hata durumunda hata kodu ile yanıt ver
        http_response_code(500);
        echo "Oops! Mesajınız gönderilirken bir hata oluştu: {$e->getMessage()}";
    }
} else {
    // POST isteği değilse 403 (forbidden) yanıt kodu ile yanıt ver
    http_response_code(403);
    echo "Bir sorun oluştu, lütfen tekrar deneyin.";
}
?>
