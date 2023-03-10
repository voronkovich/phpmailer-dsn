<?php

namespace Voronkovich\PHPMailerDSN;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Configure PHPMailer with DSN string.
 *
 * @see https://en.wikipedia.org/wiki/Data_source_name
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class DSNConfigurator
{
    const DEFAULT_SECURE_PORT = 587;

    /**
     * Configure PHPMailer instance with DSN string.
     *
     * @param PHPMailer $mailer PHPMailer instance
     * @param string    $dsn    DSN
     *
     * @return PHPMailer
     */
    public function configure(PHPMailer $mailer, $dsn)
    {
        $config = $this->parseDSN($dsn);

        $this->applyConfig($mailer, $config);

        return $mailer;
    }

    /**
     * Parse DSN string.
     *
     * @param string $dsn DSN
     *
     * @throws Exception If DSN is mailformed
     *
     * @return array Configruration
     */
    private function parseDSN($dsn)
    {
        $config = parse_url($dsn);

        if (false === $config || !isset($config['scheme']) || !isset($config['host'])) {
            throw new Exception(
                sprintf('Mailformed DSN: "%s".', $dsn)
            );
        }

        if (isset($config['query'])) {
            parse_str($config['query'], $config['query']);
        }

        return $config;
    }

    /**
     * Apply configuration to mailer.
     *
     * @param PHPMailer $mailer PHPMailer instance
     * @param array     $config Configuration
     *
     * @throws Exception If scheme is invalid
     */
    private function applyConfig(PHPMailer $mailer, $config)
    {
        switch ($config['scheme']) {
            case 'mail':
                $mailer->isMail();
                break;
            case 'sendmail':
                $mailer->isSendmail();
                break;
            case 'qmail':
                $mailer->isQmail();
                break;
            case 'smtp':
            case 'smtps':
                $mailer->isSMTP();
                $this->configureSMTP($mailer, $config);
                break;
            default:
                throw new Exception(
                    sprintf(
                        'Invalid scheme: "%s". Allowed values: "mail", "sendmail", "qmail", "smtp", "smtps".',
                        $config['scheme'],
                    )
                );
        }

        if (isset($config['query'])) {
            $this->configureOptions($mailer, $config['query']);
        }
    }

    /**
     * Configure SMTP.
     *
     * @param PHPMailer $mailer PHPMailer instance
     * @param array     $config Configuration
     */
    private function configureSMTP($mailer, $config)
    {
        $isSMTPS = 'smtps' === $config['scheme'];

        if ($isSMTPS) {
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mailer->Host = $config['host'];

        if (isset($config['port'])) {
            $mailer->Port = $config['port'];
        } elseif ($isSMTPS) {
            $mailer->Port = self::DEFAULT_SECURE_PORT;
        }

        $mailer->SMTPAuth = isset($config['user']) || isset($config['pass']);

        if (isset($config['user'])) {
            $mailer->Username = $config['user'];
        }

        if (isset($config['pass'])) {
            $mailer->Password = $config['pass'];
        }
    }

    /**
     * Configure options.
     *
     * @param PHPMailer $mailer  PHPMailer instance
     * @param array     $options Options
     *
     * @throws Exception If option is unknown
     */
    private function configureOptions(PHPMailer $mailer, $options)
    {
        $allowedOptions = get_object_vars($mailer);

        unset($allowedOptions['Mailer']);
        unset($allowedOptions['SMTPAuth']);
        unset($allowedOptions['Username']);
        unset($allowedOptions['Password']);
        unset($allowedOptions['Hostname']);
        unset($allowedOptions['Port']);
        unset($allowedOptions['ErrorInfo']);

        $allowedOptions = \array_keys($allowedOptions);

        foreach ($options as $key => $value) {
            if (!in_array($key, $allowedOptions)) {
                throw new Exception(
                    sprintf(
                        'Unknown option: "%s". Allowed values: "%s"',
                        $key,
                        implode('", "', $allowedOptions)
                    )
                );
            }

            switch ($key) {
                case 'AllowEmpty':
                case 'SMTPAutoTLS':
                case 'SMTPKeepAlive':
                case 'SingleTo':
                case 'UseSendmailOptions':
                case 'do_verp':
                case 'DKIM_copyHeaderFields':
                    $mailer->$key = (bool) $value;
                    break;
                case 'Priority':
                case 'SMTPDebug':
                case 'WordWrap':
                    $mailer->$key = (integer) $value;
                    break;
                default:
                    $mailer->$key = $value;
                    break;
            }
        }
    }
}
