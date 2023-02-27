<?php

namespace Voronkovich\PHPMailerDSN\Tests;

use Voronkovich\PHPMailerDSN\DSNConfigurator;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPUnit\Framework\TestCase;

/**
 * Test configuring with DSN.
 *
 * @covers \Voronkovich\PHPMailerDSN\DSNConfigurator
 */
final class DSNConfiguratorTest extends TestCase
{
    /**
     * Test throwing exception if DSN is invalid.
     */
    public function testInvalidDSN()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Mailformed DSN: "localhost".');

        $configurator->configure($mailer, 'localhost');
    }

    /**
     * Test throwing exception if DSN scheme is invalid.
     */
    public function testInvalidScheme()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid scheme: "ftp".');

        $configurator->configure($mailer, 'ftp://localhost');
    }

    /**
     * Test cofiguring mail.
     */
    public function testConfigureMail()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'mail://localhost');

        self::assertEquals($mailer->Mailer, 'mail');
    }

    /**
     * Test cofiguring sendmail.
     */
    public function testConfigureSendmail()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'sendmail://localhost');

        self::assertEquals($mailer->Mailer, 'sendmail');
    }

    /**
     * Test cofiguring qmail.
     */
    public function testConfigureQmail()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'qmail://localhost');

        self::assertEquals($mailer->Mailer, 'qmail');
    }

    /**
     * Test cofiguring SMTP without authentication.
     */
    public function testConfigureSmtpWithoutAuthentication()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'smtp://localhost');

        self::assertEquals($mailer->Mailer, 'smtp');
        self::assertEquals($mailer->Host, 'localhost');
        self::assertFalse($mailer->SMTPAuth);
    }

    /**
     * Test cofiguring SMTP with authentication.
     */
    public function testConfigureSmtpWithAuthentication()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'smtp://user:pass@remotehost');

        self::assertEquals($mailer->Mailer, 'smtp');
        self::assertEquals($mailer->Host, 'remotehost');

        self::assertTrue($mailer->SMTPAuth);
        self::assertEquals($mailer->Username, 'user');
        self::assertEquals($mailer->Password, 'pass');
    }

    /**
     * Test cofiguring SMTP without port.
     */
    public function testConfigureSmtpWithoutPort()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'smtp://localhost');

        self::assertEquals($mailer->Mailer, 'smtp');
        self::assertEquals($mailer->Host, 'localhost');
        self::assertEquals($mailer->Port, SMTP::DEFAULT_PORT);
    }

    /**
     * Test cofiguring SMTP with port.
     */
    public function testConfigureSmtpWitPort()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'smtp://localhost:2525');

        self::assertEquals($mailer->Mailer, 'smtp');
        self::assertEquals($mailer->Host, 'localhost');
        self::assertEquals($mailer->Port, 2525);
    }

    /**
     * Test cofiguring SMTPs without port.
     */
    public function testConfigureSmtpsWithoutPort()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'smtps://user:pass@remotehost');

        self::assertEquals($mailer->Mailer, 'smtp');
        self::assertEquals($mailer->SMTPSecure, PHPMailer::ENCRYPTION_STARTTLS);

        self::assertEquals($mailer->Host, 'remotehost');
        self::assertEquals($mailer->Port, DSNConfigurator::DEFAULT_SECURE_PORT);

        self::assertTrue($mailer->SMTPAuth);
        self::assertEquals($mailer->Username, 'user');
        self::assertEquals($mailer->Password, 'pass');
    }

    /**
     * Test cofiguring SMTPs with port.
     */
    public function testConfigureWithUnknownOption()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown option: "UnknownOption".');

        $configurator->configure($mailer, 'mail://locahost?UnknownOption=Value');
    }

    /**
     * Test cofiguring options with query sting.
     */
    public function testConfigureWithOptions()
    {
        $mailer = new PHPMailer(true);
        $configurator = new DSNConfigurator();

        $configurator->configure($mailer, 'sendmail://localhost?Sendmail=/usr/local/bin/sendmail&AllowEmpty=1&WordWrap=78');

        self::assertEquals($mailer->Mailer, 'sendmail');
        self::assertEquals($mailer->Sendmail, '/usr/local/bin/sendmail');
        self::assertEquals($mailer->AllowEmpty, true);
        self::assertEquals($mailer->WordWrap, 78);
    }
}
