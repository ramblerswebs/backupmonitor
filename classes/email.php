<?php

/**
 * Description of adminemail
 *
 * @author Chris Vaughan
 */
class email {

    Const UNKNOWN = 0;
    Const BACKUP = 1;
    Const WEBMONITOR = 2;
    Const JOOMLAUPDATE = 3;
    Const REMOVEBACKUP = 4;
    const REMOVEWEBMONITOR = 5;

    private $header;
    private $subject;
    private $from;
    private $to;
    private $emailfrom;
    private $date;
    private $domain;

    public function __construct($mbox, $value) {
        $this->header = imap_headerinfo($mbox, $value, 0, 1024);
        $this->subject = $this->header->subject;
        if ($this->subject === null) {
            $this->subject = "No subject";
        }
        $this->from = $this->header->from[0];
        $this->to = $this->header->to[0];
        $this->emailfrom = $this->from->mailbox . '@' . $this->from->host;
        $this->domain = $this->to->host;
        $this->date = $this->header->date;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getDomain() {
        return $this->domain;
    }

    public function getFromEmail() {
        return $this->emailfrom;
    }

    public function getType() {
        if (strpos($this->subject, 'Backup ') === 0) {
            return self::BACKUP;
        }
        if (strpos($this->subject, 'Update is available') === 0) {
            return self::JOOMLAUPDATE;
        }
        if (strpos($this->subject, 'updates are available') > 0) {
            return self::JOOMLAUPDATE;
        }
        if (strpos($this->subject, 'WebMonitor: ') === 0) {
            return self::WEBMONITOR;
        }
       if (strpos($this->subject, 'Remove WebMonitor: ') === 0) {
            return self::REMOVEWEBMONITOR;
        }
        if (strpos($this->subject, 'Remove backup ') === 0) {
            return self::REMOVEBACKUP;
        }

        return self::UNKNOWN;
    }

    public function getBackupName() {
        if ($this->getType() == self::BACKUP or $this->getType() == self::REMOVEBACKUP) {
            $text = $this->subject;
            $items = explode(" ", $text);
            foreach ($items as $item) {
                switch ($item) {
                    case "Remove":
                    case "backup":
                    case "Backup":
                    case "of":
                    case "":
                    case " ":
                        break;
                    default:
                        return $item;
                        break;
                }
            }
        } else {
            return "";
        }
    }

    public function getDomainName() {
        if ($this->getType() == self::WEBMONITOR or $this->getType() == self::REMOVEWEBMONITOR) {
            $text = $this->subject;
            $items = explode(" ", $text);
            foreach ($items as $item) {
                switch ($item) {
                    case "Remove":
                    case "WebMonitor:":
                    case "NEW":
                    case "INSTALL:":
                    case "":
                    case " ":
                        break;
                    default:
                        return $item;
                        break;
                }
            }
        } else {
            return "";
        }
    }

    public function getNoUpdates() {
        if ($this->getType() == self::JOOMLAUPDATE) {
            $text = $this->subject;
            $items = explode(" ", $text);
            $cno = $items[0];
            return intval($cno);
        }
        return 0;
    }

    public function getDate() {
        return $this->date;
    }

}