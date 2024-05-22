<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.0.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once root . "/vendor/autoload.php";
$ModulosMikrowisp = new Mikrowisp();
$ModulosMikrowisp->ConfigGlobal($db);
class Mikrowisp
{
    public $mysql;
    public $moneda;
    public $urlportal;
    public $pushEnabled;
    public $validapago;
    public $smsSuspendido;
    public $smsalpagar;
    public $token;
    public $Zonahoraria;
    public $smart_url;
    public $smart_api;
    public $smart_corte_onu;
    public function ConfigGlobal($mysql)
    {
        $this->mysql = $mysql;
        $this->moneda = $this->getDDB("currency");
        $this->urlportal = $this->getDDB("url_portal");
        $this->pushEnabled = $this->getDDB("onesignalid");
        $this->validapago = $this->getDDB("valida_pago");
        $this->Zonahoraria = $this->getDDB("zona_horaria");
        $this->smsalpagar = $this->getDDB("sms_alpagar") ? true : false;
        $this->smsSuspendido = $this->getDDB("sms_suspendido") ? true : false;
        $this->smart_url = $this->getDDB("smart_url");
        $this->smart_api = $this->getDDB("smart_api");
        $this->smart_corte_onu = $this->getDDB("smart_corte_onu");
        if (isset($_SERVER)) {
            $_obfuscated_0D2C1813152A103031103819013C390B332F1513381901_ = ["/admin/index.php" => true, "/admin/ajax/router.php" => true, "/admin/ajax/usuarios.php" => true, "/admin/ajax/facturas.php" => true, "/admin/ajax/viewuser.php" => true, "/admin/ajax/addpago.php" => true, "/admin/ajax/addpagomasivo.php" => true, "/admin/ajax/transacciones.php" => true, "/admin/ajax/perfiles.php" => true, "/admin/ajax/ajustes.php" => true, "/admin/ajax/soporte.php" => true, "/admin/ajax/instalaciones.php" => true, "/admin/ajax/anuncios.php" => true, "/admin/ajax/hotspot.php" => true];
            if (isset($_obfuscated_0D2C1813152A103031103819013C390B332F1513381901_[$_SERVER["SCRIPT_NAME"]])) {
                try {
                    $this->tokenlocal();
                } catch (Exception $_obfuscated_0D2D3C5C040E1B3C1626192A29250718261D273D261322_) {
                    if (isset($_SESSION["idusername"])) {
                        unset($_COOKIE["mkwsp6_cliente"]);
                        session_unset();
                        session_destroy();
                        session_unset();
                        $_SESSION = [];
                    }
                    $this->validalicencia();
                    header("Location: login?error=0");
                    exit;
                }
            }
        }
    }
    public function getDDB($campo)
    {
        $db = MysqliDb::getInstance();
        $db->where("setting", $campo);
        $_obfuscated_0D09400D131226243C312619101012030D291F17051B01_ = $db->getOne("tblconfiguration");
        return $_obfuscated_0D09400D131226243C312619101012030D291F17051B01_["value"];
    }
    public function limiteuser()
    {
        $token = explode("::", $this->getDDB("tokenlic"));
        $_obfuscated_0D311A2931351F2506290E252A0F343E03301B34371C01_ = new ParagonIE\HiddenString\HiddenString($this->getDDB("passlic"));
        $_obfuscated_0D261A2F135C3E5C3306132A261A34051C3514082B0B01_ = ParagonIE\Halite\KeyFactory::deriveEncryptionKey($_obfuscated_0D311A2931351F2506290E252A0F343E03301B34371C01_, base64_decode($token[0]));
        $_obfuscated_0D102B2C2C121A2A333F2132220E143708102B08292801_ = ParagonIE\Halite\Symmetric\Crypto::decrypt($token[1], $_obfuscated_0D261A2F135C3E5C3306132A261A34051C3514082B0B01_);
        $data = unserialize(base64_decode($_obfuscated_0D102B2C2C121A2A333F2132220E143708102B08292801_->getString()));
        if (isset($data["limite"]) && 0 < $data["limite"]) {
            return $data["limite"];
        }
        return "0";
    }
    private function tokenlocal($r = true)
    {
        $token = explode("::", $this->getDDB("tokenlic"));
        if (!isset($token[1])) {
            if (isset($_SESSION["idusername"])) {
                unset($_COOKIE["mkwsp6_cliente"]);
                session_unset();
                session_destroy();
                session_unset();
                $_SESSION = [];
            }
            $this->validalicencia();
            header("Location: login?error=0");
            exit;
        }
        $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_ = new ParagonIE\HiddenString\HiddenString($this->getDDB("passlic"));
        $key = ParagonIE\Halite\KeyFactory::deriveEncryptionKey($_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_, base64_decode($token[0]));
        $_obfuscated_0D3C095C1A30301114071E07342B2606052B39291E0511_ = ParagonIE\Halite\Symmetric\Crypto::decrypt($token[1], $key);
        $data = unserialize(base64_decode($_obfuscated_0D3C095C1A30301114071E07342B2606052B39291E0511_->getString()));
        if (isset($data["limite"]) && isset($data["fecha"])) {
            if (!$r) {
                return $data;
            }
            if (intval($data["estado"]) == 1) {
                $this->validalicencia();
                if (isset($_SESSION["idusername"])) {
                    unset($_COOKIE["mkwsp6_cliente"]);
                    session_unset();
                    session_destroy();
                    session_unset();
                    $_SESSION = [];
                }
                header("Location: login?error=1");
                exit;
            }
            if (intval($data["estado"]) == 2) {
                $this->validalicencia();
                if (isset($_SESSION["idusername"])) {
                    unset($_COOKIE["mkwsp6_cliente"]);
                    session_unset();
                    session_destroy();
                    session_unset();
                    $_SESSION = [];
                }
                header("Location: login?error=2");
                exit;
            }
            if ($data["fecha"] < date("Y-m-d")) {
                if (isset($_SESSION["idusername"])) {
                    unset($_COOKIE["mkwsp6_cliente"]);
                    session_unset();
                    session_destroy();
                    session_unset();
                    $_SESSION = [];
                }
                header("Location: login?error=3");
                exit;
            }
            if (intval($data["estado"]) !== 0) {
                if (isset($_SESSION["idusername"])) {
                    unset($_COOKIE["mkwsp6_cliente"]);
                    session_unset();
                    session_destroy();
                    session_unset();
                    $_SESSION = [];
                }
                header("Location: login?error=3");
                exit;
            }
        } else {
            $this->validalicencia();
            header("Location: login?error=0");
            exit;
        }
    }
    public function bytesfunction($bytes)
    {
        if ($bytes == "0/0") {
            return "0/0";
        }
        $_obfuscated_0D011E1119373F33103C150A0F2F085B3008193F373032_ = explode("/", $bytes);
        if (!empty($_obfuscated_0D011E1119373F33103C150A0F2F085B3008193F373032_[1])) {
            $bytes = frombytesfunction($_obfuscated_0D011E1119373F33103C150A0F2F085B3008193F373032_[0]) * 1000;
            $_obfuscated_0D39043F372B5B19022E0F1B381E15031833115B260332_ = ["", "K", "M", "G"];
            $exp = floor(log($bytes, 1000)) | 0;
            $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = round($bytes / pow(1000, $exp), 2);
            if (fmod($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_, 1) !== 0) {
                $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = $bytes / 1000;
                $_obfuscated_0D1723142F5B063F2A1B1F1439091E2D175C26142A1C01_ = $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ . "K";
            } else {
                $_obfuscated_0D1723142F5B063F2A1B1F1439091E2D175C26142A1C01_ = round($bytes / pow(1000, $exp)) . $_obfuscated_0D39043F372B5B19022E0F1B381E15031833115B260332_[$exp];
            }
            $bytes = frombytesfunction($_obfuscated_0D011E1119373F33103C150A0F2F085B3008193F373032_[1]) * 1000;
            $_obfuscated_0D39043F372B5B19022E0F1B381E15031833115B260332_ = ["", "K", "M", "G"];
            $exp = floor(log($bytes, 1000)) | 0;
            $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = round($bytes / pow(1000, $exp), 2);
            if (fmod($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_, 1) !== 0) {
                $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = $bytes / 1000;
                return $_obfuscated_0D1723142F5B063F2A1B1F1439091E2D175C26142A1C01_ . "/" . $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ . "K";
            }
            return $_obfuscated_0D1723142F5B063F2A1B1F1439091E2D175C26142A1C01_ . "/" . round($bytes / pow(1000, $exp)) . $_obfuscated_0D39043F372B5B19022E0F1B381E15031833115B260332_[$exp];
        }
        $bytes = frombytesfunction($bytes) * 1000;
        $_obfuscated_0D39043F372B5B19022E0F1B381E15031833115B260332_ = ["", "K", "M", "G"];
        $exp = floor(log($bytes, 1000)) | 0;
        $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = round($bytes / pow(1000, $exp), 2);
        if (fmod($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_, 1) !== 0) {
            $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = $bytes / 1000;
            return $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ . "K";
        }
        return round($bytes / pow(1000, $exp)) . $_obfuscated_0D39043F372B5B19022E0F1B381E15031833115B260332_[$exp];
    }
    public function frombytesfunction($from)
    {
        $_obfuscated_0D2D0D12271F350B2931211D0C013F2830160E0C341311_ = substr($from, 0, -1);
        strtoupper(substr($from, -1));
        strtoupper(substr($from, -1));
        switch (strtoupper(substr($from, -1))) {
            case "M":
                return $_obfuscated_0D2D0D12271F350B2931211D0C013F2830160E0C341311_ * pow(1024, 1);
                break;
            case "G":
                return $_obfuscated_0D2D0D12271F350B2931211D0C013F2830160E0C341311_ * pow(1024, 2);
                break;
            case "T":
                return $_obfuscated_0D2D0D12271F350B2931211D0C013F2830160E0C341311_ * pow(1024, 3);
                break;
            case "P":
                return $_obfuscated_0D2D0D12271F350B2931211D0C013F2830160E0C341311_ * pow(1024, 4);
                break;
            default:
                return $_obfuscated_0D2D0D12271F350B2931211D0C013F2830160E0C341311_;
        }
    }
    private function urlsistema($full = false)
    {
        $url = parse_url($this->urlportal);
        if ($full) {
            return $url["scheme"] . "://" . $url["host"];
        }
        return $url["host"];
    }
    public function limitarservicio($idservicio)
    {
        $db = $this->mysql;
        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_ = [];
        $_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_ = [];
        if (empty($idservicio)) {
            return NULL;
        }
        $_obfuscated_0D0D1D232E17142129153909380E0D0E2B023609161801_ = explode(",", $idservicio);
        foreach ($_obfuscated_0D0D1D232E17142129153909380E0D0E2B023609161801_ as $_obfuscated_0D371222291B330D010E2A251D0614390402043B1E0A32_) {
            $db->where("id", $_obfuscated_0D371222291B330D010E2A251D0614390402043B1E0A32_);
            $username = $db->getOne("tblservicios");
            $db->where("id", $username["idperfil"]);
            $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_ = $db->getOne("perfiles");
            $db->where("id", $username["nodo"]);
            $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_ = $db->getOne("server");
            if (0 < $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] && 0 < $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"]) {
                if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "profile" && $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["seguridad"] == "1") {
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["ADD"][] = ["/ppp/profile/add", ["local-address" => $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"], "name" => "Mkws_bajada_velocidad_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"], "rate-limit" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"] . "K/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] . "K")]];
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/ppp/secret/add", ["profile" => "Mkws_bajada_velocidad_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"]], [".proplist" => ".id", "?name" => $username["pppuser"]]];
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $username["pppuser"]]];
                } else {
                    if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "profile" && $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["seguridad"] == "2") {
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/ip/hotspot/user/profile/add", ["name" => "Mkws_bajada_velocidad_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"], "rate-limit" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"] . "K/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] . "K"), "transparent-proxy" => "no"], [".proplist" => ".id", "?name" => "Mkws_bajada_velocidad_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"]]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/ip/hotspot/user/add", ["name" => $username["pppuser"], "profile" => "Mkws_bajada_velocidad_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"]], [".proplist" => ".id", "?name" => $username["pppuser"]]];
                    } else {
                        if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "profile" && 2 < $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["seguridad"]) {
                            $db->where("username", $username["pppuser"]);
                            $db->where("attribute", "Mikrotik-Rate-Limit");
                            $db->update("radreply", ["value" => $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"] . "K/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] . "K"]);
                            $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $username["pppuser"]]];
                        }
                    }
                }
                if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "queues") {
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/queue/simple/add", ["max-limit" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"] . "K/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] . "K"), "burst-limit" => "0/0", "burst-threshold" => "0/0", "burst-time" => "0/0", "limit-at" => "0/0"], [".proplist" => ".id", "?name" => "Mkws_queue_" . $username["id"]]];
                } else {
                    if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "pcq") {
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/ip/firewall/mangle/add", ["action" => "mark-packet", "chain" => "postrouting", "comment" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-in", "dst-address-list" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"], "passthrough" => "no", "new-packet-mark" => "BajadadeplanpMark_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-in"], [".proplist" => ".id", "?comment" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-in"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/ip/firewall/mangle/add", ["action" => "mark-packet", "chain" => "forward", "passthrough" => "no", "comment" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-out", "src-address-list" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"], "new-packet-mark" => "BajadadeplanpMark_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-out"], [".proplist" => ".id", "?comment" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-out"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["ADD"][] = ["/queue/tree/add", ["name" => "Mkws_DOWN", "parent" => "global"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["ADD"][] = ["/queue/tree/add", ["name" => "Mkws_UP", "parent" => "global"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/queue/type/add", ["kind" => "pcq", "name" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-down", "pcq-classifier" => "dst-address", "pcq-limit" => "50", "pcq-rate" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] . "K"), "pcq-total-limit" => "2000"], [".proplist" => ".id", "?name" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-down"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/queue/type/add", ["kind" => "pcq", "name" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-up", "pcq-classifier" => "src-address", "pcq-limit" => "50", "pcq-rate" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"] . "K"), "pcq-total-limit" => "2000"], [".proplist" => ".id", "?name" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-up"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/queue/tree/add", ["name" => "Bajada_de_plan_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-DOWN", "packet-mark" => "BajadadeplanpMark_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-in", "parent" => "Mkws_DOWN", "priority" => $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["prioridad"], "queue" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-down"], [".proplist" => ".id", "?name" => "Bajada_de_plan_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-DOWN"]];
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["EDIT"][] = ["/queue/tree/add", ["name" => "Bajada_de_plan_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-UP", "packet-mark" => "BajadadeplanpMark_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-out", "parent" => "Mkws_UP", "priority" => $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["prioridad"], "queue" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-up"], [".proplist" => ".id", "?name" => "Bajada_de_plan_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] . "-UP"]];
                        if (!empty($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["pcq"]) && $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["api"] == "0") {
                            $_obfuscated_0D342D1D0402310A5C302A2A170606042C5B30395C3911_ = $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["pcq"];
                        } else {
                            $_obfuscated_0D342D1D0402310A5C302A2A170606042C5B30395C3911_ = "Mkws_PCQ_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"];
                        }
                        if (2 < $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["seguridad"]) {
                            $db->where("username", $username["pppuser"]);
                            $db->where("attribute", "Mikrotik-Address-List");
                            $db->update("radreply", ["attribute" => "Mikrotik-Rate-Limit", "value" => $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_up"] . "K/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limit_down"] . "K"]);
                            $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $username["pppuser"]]];
                        } else {
                            $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["REMOVEALL"][] = ["/ip/firewall/address-list/remove", "List_pcq_" . $username["id"]];
                            $_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ = explode(",", $username["ip"]);
                            foreach ($_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ as $ip) {
                                $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["id"]]["ADD"][] = ["/ip/firewall/address-list/add", ["list" => "Bajada_de_plan" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"], "comment" => "List_pcq_" . $username["id"], "address" => $ip, "disabled" => "no"]];
                            }
                        }
                    }
                }
                $db->where("id", $username["id"]);
                $db->update("tblservicios", ["limitado" => "1"]);
                $log = "Servicio Reducido de velocidad ({{cliente}}) - Servicio ID: " . $username["id"];
                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $username["idcliente"], 2);
            }
        }
        return $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_;
    }
    public function crearLog($log, $operador = 0, $user = 0, $tipo = 0)
    {
        $db = MysqliDb::getInstance();
        if (isset($_SESSION["idusername"]) && empty($operador)) {
            $operador = $_SESSION["idusername"];
        }
        if (isset($_SESSION["idusername"]) && $operador == 0) {
            $operador = $_SESSION["idusername"];
        }
        if (empty($operador)) {
            $operador = 0;
        }
        $data = ["log" => $log, "operador" => $operador, "tipolog" => $tipo, "idcliente" => $user];
        $db->insert("logsistema", $data);
    }
    public function limitarS($down, $subida, $porcentaje, $op = "")
    {
        if ($porcentaje == 0) {
            return "0/0";
        }
        $down = frombytesfunction($down);
        $subida = frombytesfunction($subida);
        if (!empty($op)) {
            $down = round($down * $porcentaje / 100, 0);
            $subida = round($subida * $porcentaje / 100, 0);
            return $subida . "K/" . $down . "K";
        }
        $down = $down + round($down * $porcentaje / 100, 0);
        $subida = $subida + round($subida * $porcentaje / 100, 0);
        return $subida . "K/" . $down . "K";
    }
    public function quitarlimitarservicio($idservicio)
    {
        $db = $this->mysql;
        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_ = [];
        $_obfuscated_0D0D1D232E17142129153909380E0D0E2B023609161801_ = explode(",", $idservicio);
        foreach ($_obfuscated_0D0D1D232E17142129153909380E0D0E2B023609161801_ as $_obfuscated_0D371222291B330D010E2A251D0614390402043B1E0A32_) {
            $db->where("id", $_obfuscated_0D371222291B330D010E2A251D0614390402043B1E0A32_);
            $username = $db->getOne("tblservicios");
            if ($username["limitado"] != 0) {
                $db->where("id", $username["idperfil"]);
                $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_ = $db->getOne("perfiles");
                $db->where("id", $username["nodo"]);
                $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_ = $db->getOne("server");
                $_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_ = explode("/", $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["velocidad"]);
                list($_obfuscated_0D1723142F5B063F2A1B1F1439091E2D175C26142A1C01_, $down) = $_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_;
                $_obfuscated_0D062D0A220D253B3C24363C0D0A293D07075C07401D32_ = limitarS($_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_[1], $_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_[0], $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["limitat"], "limit");
                $_obfuscated_0D2D362E3E323D32141018042B2F0E1A3C2B3213042311_ = limitarS($_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_[1], $_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_[0], $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["burst_limit"], "");
                $_obfuscated_0D3201030915081A29190115111404332F0F040D0D0E01_ = limitarS($_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_[1], $_obfuscated_0D1C22321F340E3C35081C310A1C193E5B1C402F133C22_[0], $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["burst_threshold"], "t");
                if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "profile" && $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["seguridad"] == "1") {
                    if (empty($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["profile"]) && $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["api"] == "0") {
                        $_obfuscated_0D36143635282D1D5C24303E2B031D241B2507212E3622_ = "Mkws_profile_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"];
                    } else {
                        $_obfuscated_0D36143635282D1D5C24303E2B031D241B2507212E3622_ = $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["profile"];
                    }
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["EDIT"][] = ["/ppp/secret/add", ["profile" => $_obfuscated_0D36143635282D1D5C24303E2B031D241B2507212E3622_], [".proplist" => ".id", "?name" => $username["pppuser"]]];
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $username["pppuser"]]];
                }
                if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "profile" && $row["seguridad"] == "2") {
                    if (empty($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["profile"]) && $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["api"] == "0") {
                        $_obfuscated_0D36143635282D1D5C24303E2B031D241B2507212E3622_ = "Mkws_profile_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"];
                    } else {
                        $_obfuscated_0D36143635282D1D5C24303E2B031D241B2507212E3622_ = $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["profile"];
                    }
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["EDIT"][] = ["/ip/hotspot/user/add", ["name" => $username["pppuser"], "profile" => $_obfuscated_0D36143635282D1D5C24303E2B031D241B2507212E3622_], [".proplist" => ".id", "?name" => $username["pppuser"]]];
                } else {
                    if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "profile" && 2 < $row["seguridad"]) {
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $username["pppuser"]]];
                        $db->where("username", $username["pppuser"]);
                        $db->where("attribute", "Mikrotik-Rate-Limit");
                        $db->update("radreply", ["value" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["velocidad"]) . " " . bytesfunction($_obfuscated_0D2D362E3E323D32141018042B2F0E1A3C2B3213042311_) . " " . bytesfunction($_obfuscated_0D3201030915081A29190115111404332F0F040D0D0E01_) . " " . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["burst_time"] . "/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["burst_time"] . " " . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["prioridad"] . " " . bytesfunction($_obfuscated_0D062D0A220D253B3C24363C0D0A293D07075C07401D32_)]);
                    }
                }
                if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "queues") {
                    $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["EDIT"][] = ["/queue/simple/add", ["max-limit" => bytesfunction($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["velocidad"]), "burst-limit" => bytesfunction($_obfuscated_0D2D362E3E323D32141018042B2F0E1A3C2B3213042311_), "parent" => empty($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["parent"]) ? "none" : $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["parent"], "burst-threshold" => bytesfunction($_obfuscated_0D3201030915081A29190115111404332F0F040D0D0E01_), "burst-time" => $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["burst_time"] . "/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["burst_time"], "priority" => $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["prioridad"] . "/" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["prioridad"], "limit-at" => bytesfunction($_obfuscated_0D062D0A220D253B3C24363C0D0A293D07075C07401D32_)], [".proplist" => ".id", "?name" => "Mkws_queue_" . $username["id"]]];
                }
                if ($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["velocidad"] == "pcq") {
                    if (!empty($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["pcq"]) && $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["api"] == "1") {
                        $_obfuscated_0D342D1D0402310A5C302A2A170606042C5B30395C3911_ = $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["pcq"];
                    } else {
                        $_obfuscated_0D342D1D0402310A5C302A2A170606042C5B30395C3911_ = "Mkws_PCQ_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"];
                    }
                    if (2 < $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["seguridad"]) {
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $username["pppuser"]]];
                        $db->where("username", $username["pppuser"]);
                        $db->where("attribute", "Mikrotik-Rate-Limit");
                        $db->update("radreply", ["attribute" => "Mikrotik-Address-List", "value" => empty($_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["pcq"]) ? "Mkws_PCQ_" . $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["id"] : $_obfuscated_0D5B1D1831013032232E1D5C092C15094038370C225C22_["pcq"]]);
                    } else {
                        $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["REMOVEALL"][] = ["/ip/firewall/address-list/remove", "List_pcq_" . $username["id"]];
                        $_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ = explode(",", $username["ip"]);
                        foreach ($_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ as $ip) {
                            $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_[$username["nodo"]]["ADD"][] = ["/ip/firewall/address-list/add", ["list" => $_obfuscated_0D342D1D0402310A5C302A2A170606042C5B30395C3911_, "comment" => "List_pcq_" . $username["id"], "address" => $ip, "disabled" => "no"]];
                        }
                    }
                }
                $db->where("id", $username["id"]);
                $db->update("tblservicios", ["limitado" => "0"]);
                $log = "Servicio Restaurado a su plan contratado ({{cliente}}) - Servicio ID: " . $username["id"];
                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $username["idcliente"], 2);
            }
        }
        return $_obfuscated_0D5B310E092D0A5C071C1205133E0F400D0E0A24100701_;
    }
    public function idgetFacturas($n, $c = 6)
    {
        return str_pad($n, $c, "0", STR_PAD_LEFT);
    }
    public function getImpuesto($totales, $iva_igv, $ini_impuesto)
    {
        if ($iva_igv == "NO") {
            $_obfuscated_0D0F1C0D1502212D2F0C2F340E16133E07061D21061832_ = $ini_impuesto / 100;
            $_obfuscated_0D0F1C0D1502212D2F0C2F340E16133E07061D21061832_ = 1 + $_obfuscated_0D0F1C0D1502212D2F0C2F340E16133E07061D21061832_;
            $_obfuscated_0D400C3D060213031B10182B2E3C2E0D275B352A1B1901_ = $totales / $_obfuscated_0D0F1C0D1502212D2F0C2F340E16133E07061D21061832_;
            $array["subtotal"] = $_obfuscated_0D400C3D060213031B10182B2E3C2E0D275B352A1B1901_;
            $array["impuesto"] = $totales - $_obfuscated_0D400C3D060213031B10182B2E3C2E0D275B352A1B1901_;
            $array["total"] = $totales;
            return $array;
        }
        if ($iva_igv == "SI") {
            $array["subtotal"] = $totales;
            $array["impuesto"] = $totales * $ini_impuesto / 100;
            $array["total"] = $array["subtotal"] + $array["impuesto"];
            return $array;
        }
        if ($iva_igv == "NADA") {
            $_obfuscated_0D400C3D060213031B10182B2E3C2E0D275B352A1B1901_ = $totales;
            $this->getImpuesto = "0";
            $_obfuscated_0D0339145C12311E1C3422081E352B021B3E0E1D160B01_ = $totales;
            $array["subtotal"] = $_obfuscated_0D400C3D060213031B10182B2E3C2E0D275B352A1B1901_;
            $array["impuesto"] = $this->getImpuesto;
            $array["total"] = $_obfuscated_0D0339145C12311E1C3422081E352B021B3E0E1D160B01_;
            return $array;
        }
    }
    public function getHtml($cliente, $html)
    {
        if (empty($cliente)) {
            return $html;
        }
        $db = MysqliDb::getInstance();
        $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_ = $this->getDDB("currency");
        if (!function_exists("money_format")) {
            include_once root . "/admin/ajax/moneda.php";
        }
        $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_ = $db->rawQueryOne("SELECT *,(Select COALESCE(SUM(total),0) from facturas where idcliente=usuarios.id and estado='No pagado') as t,(Select COALESCE(SUM(monto),0) from saldos where iduser=usuarios.id and estado='no cobrado') as s FROM usuarios where id=?", [$cliente]);
        $db->where("cliente", $cliente);
        $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_ = $db->getOne("tblavisouser");
        $db->where("id", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["zona"]);
        $_obfuscated_0D182D38213F160E38183417092D2A2324320D241A5C01_ = $db->getOne("zonas");
        $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["ubicacion"] = $_obfuscated_0D182D38213F160E38183417092D2A2324320D241A5C01_["zona"];
        $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["id"] = $this->idgetFacturas($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["id"], 6);
        $db->where("tipo", "Siro");
        $_obfuscated_0D312B170D1D1C2913031D3F2518025C053305035C0F11_ = $db->getOne("pasarela");
        $html = str_replace("{siro}", $this->idgetFacturas($cliente, 9) . $_obfuscated_0D312B170D1D1C2913031D3F2518025C053305035C0F11_["pais"], $html);
        $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["deuda"] = $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["t"] + $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["s"];
        if (!empty($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["deuda"]) && 0 < $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["deuda"]) {
            $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["deuda"] = $this->parse($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["deuda"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_);
        } else {
            $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["deuda"] = $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_);
        }
        unset($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["t"]);
        unset($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["s"]);
        foreach ($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_ as $campo => $_obfuscated_0D1D1A053E18030A332B103D3D160721151E24240C0232_) {
            $html = str_replace("{" . $campo . "_cliente}", $_obfuscated_0D1D1A053E18030A332B103D3D160721151E24240C0232_, $html);
        }
        $html = str_replace("{config_diapago}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["diapago"], $html);
        $html = str_replace("{diapago_cliente}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["diapago"], $html);
        $html = str_replace("{url_logo}", $this->getDDB("url_logo"), $html);
        $html = str_replace("{url_portal}", $this->getDDB("url_portal"), $html);
        $html = str_replace("{direccion_cliente}", $_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_["direccion_principal"], $html);
        unset($db);
        unset($_obfuscated_0D123214241F0D5B22165C403D23142B02152C03140632_);
        return $html;
    }
    public function isApiMk()
    {
        if (isset($_SERVER["HTTP_USER_AGENT"]) && !empty($_SERVER["HTTP_USER_AGENT"])) {
            $_obfuscated_0D2E25084026025C2F2C0E1722020C2E1830272D1D2311_ = $_SERVER["HTTP_USER_AGENT"];
            if ($_obfuscated_0D2E25084026025C2F2C0E1722020C2E1830272D1D2311_ == "APP_MIKROWISP_IOS") {
                return true;
            }
            if (preg_match("/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\\/|Mini|Doris\\/|Skyfire\\/|iPhone|Fennec\\/|Maemo|Iris\\/|CLDC\\-|Mobi\\/)/uis", $_obfuscated_0D2E25084026025C2F2C0E1722020C2E1830272D1D2311_)) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function facturacionAction($id, $action = "ver", $html, $tiposaldo = "")
    {
        $db = MysqliDb::getInstance();
        require_once root . "/admin/ajax/mpdf/mpdf.php";
        $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_ = $this->getDDB("currency");
        date_default_timezone_set($this->getDDB("zona_horaria"));
        $_obfuscated_0D1B102D061B190F1D3F0622402C1D36355B1C31372F11_ = $db->get("tblconfiguration");
        foreach ($_obfuscated_0D1B102D061B190F1D3F0622402C1D36355B1C31372F11_ as $data) {
            $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_[$data["setting"]] = $data["value"];
        }
        if (is_array($id)) {
            $_obfuscated_0D1A3312210D285C2C0C3C121233223C2C14230B0D2532_ = [];
            for ($i = 1; $i <= count($id); $i++) {
                $_obfuscated_0D1A3312210D285C2C0C3C121233223C2C14230B0D2532_[] = "\"?\"";
            }
            $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_ = $db->rawQuery("Select * from operaciones where id IN(" . implode(",", $id) . ") order by id desc", [implode(",", $id)]);
            $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["idcliente"]);
            $cliente = $db->getOne("usuarios");
            $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["nfactura"]);
            $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_ = $db->getOne("facturas");
            $html = html_entity_decode($html);
            $html = preg_replace("/\\s+/", " ", $html);
            $html = str_replace("\r\n", "", $html);
            $html = str_replace("\r", "", $html);
            $html = str_replace("\n", "", $html);
            preg_match("/<title>(.+)<\\/title>/", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[1], "Recibo Nº " . $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["id"], 8), $html);
            $html = str_replace("{nrecibo}", $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["id"], 8), $html);
            $html = str_replace("{id_transaccion}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["transaccion"], $html);
            $html = str_replace("{fecha}", date("d/m/Y H:i:s A", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["fecha_pago"])), $html);
            $html = str_replace("{fecha_impresion}", date("d/m/Y h:i:s A"), $html);
            $html = str_replace("{nfactura}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["nfactura"], $html);
            $html = str_replace("{nlegal}", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["legal"], $html);
            $html = str_replace("{forma_pago}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["forma_pago"], $html);
            $html = str_replace("{vencimiento}", date("d/m/Y", strtotime($_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["vencimiento"])), $html);
            $html = str_replace("{corte}", date("d/m/Y", strtotime($_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["vencimiento"])), $html);
            $html = str_replace("{nota_pago}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["descripcion"], $html);
            $html = str_replace("{nombre_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["nombre_empresa"], $html);
            $html = str_replace("{ruc_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["ruc_empresa"], $html);
            $html = str_replace("{direccion_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["direccion_empresa"], $html);
            $html = str_replace("{telefono_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["telefono_empresa"], $html);
            foreach ($cliente as $key => $data) {
                if ($key == "id") {
                    $campo = "{" . $key . "_cliente}";
                    $html = str_replace($campo, $this->idgetFacturas($data, 6), $html);
                } else {
                    $campo = "{" . $key . "_cliente}";
                    $html = str_replace($campo, $data, $html);
                }
            }
            $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["operador"]);
            $operador = $db->getOne("login");
            $html = str_replace("{operador}", $operador["nombre"], $html);
            $html = str_replace("src=\"../images/", "src=\"" . root . "/admin/images/", $html);
            $html = str_replace("src=\"images/", "src=\"" . root . "/admin/images/", $html);
            $_obfuscated_0D255B1A040C112F043418120C17143539252E291C0432_ = $db->rawQuery("Select * from operaciones where id IN(" . implode(",", $id) . ") order by id asc", [implode(",", $id)]);
            $totales = 0;
            $_obfuscated_0D331D0E3D250803372324053534122907231616173401_ = 0;
            foreach ($_obfuscated_0D255B1A040C112F043418120C17143539252E291C0432_ as $_obfuscated_0D2E2F0234073E3F130122032B3002393C1D26282C0101_) {
                $db->where("idfactura", $_obfuscated_0D2E2F0234073E3F130122032B3002393C1D26282C0101_["nfactura"]);
                $_obfuscated_0D032136081A030B2A012B0231312A0F131633362C1732_ = $db->get("facturaitems");
                foreach ($_obfuscated_0D032136081A030B2A012B0231312A0F131633362C1732_ as $row) {
                    $_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_ = $this->getImpuesto($row["cantidad"] * $row["unidades"], $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["impuesto"], $row["impuesto"]);
                    $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ .= str_replace("\\n", "<br>", nl2br($row["descripcion"])) . "<br><br>";
                    $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ .= "\n    <tr>\n    <td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n    <td valign=\"top\">" . $this->parse($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n    </tr>\n    ";
                    $totales += $_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"];
                }
                $_obfuscated_0D331D0E3D250803372324053534122907231616173401_ += $_obfuscated_0D2E2F0234073E3F130122032B3002393C1D26282C0101_["cobrado"];
            }
            $html = str_replace("{itempos}", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
            $html = str_replace("<tr> <td>{items}</td> </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
            $html = str_replace("<tr><td>{items}</td></tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
            $html = str_replace("<tr>\n    <td>{items}</td>\n    </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
            $html = str_replace("{descuento}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{saldo}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{cobrado}", $this->parse($_obfuscated_0D331D0E3D250803372324053534122907231616173401_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{total}", $this->parse($totales, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_ = NumeroALetras::convertir($totales, $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_letra"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_unidad"]);
            $html = str_replace("{total_letras}", $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_, $html);
            $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_ = NumeroALetras::convertir($_obfuscated_0D331D0E3D250803372324053534122907231616173401_, $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_letra"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_unidad"]);
            $html = str_replace("{total_cobrado_letras}", $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_, $html);
            $id = $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_[0]["id"];
        } else {
            $db->where("id", $id);
            $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_ = $db->getOne("operaciones");
            $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
            $cliente = $db->getOne("usuarios");
            $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["nfactura"]);
            $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_ = $db->getOne("facturas");
            $html = html_entity_decode($html);
            $html = preg_replace("/\\s+/", " ", $html);
            $html = str_replace("\r\n", "", $html);
            $html = str_replace("\r", "", $html);
            $html = str_replace("\n", "", $html);
            preg_match("/<title>(.+)<\\/title>/", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[1], "Recibo Nº " . $this->idgetFacturas($id, 8), $html);
            $html = str_replace("{nrecibo}", $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"], 8), $html);
            $html = str_replace("{id_transaccion}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["transaccion"], $html);
            $html = str_replace("{fecha}", date("d/m/Y H:i:s A", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["fecha_pago"])), $html);
            $html = str_replace("{fecha_impresion}", date("d/m/Y h:i:s A"), $html);
            $html = str_replace("{nfactura}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["nfactura"], $html);
            $html = str_replace("{nlegal}", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["legal"], $html);
            $html = str_replace("{forma_pago}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["forma_pago"], $html);
            $html = str_replace("{nota_pago}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["descripcion"], $html);
            $html = str_replace("{vencimiento}", date("d/m/Y", strtotime($_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["vencimiento"])), $html);
            $html = str_replace("{corte}", date("d/m/Y", strtotime($_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["vencimiento"])), $html);
            $html = str_replace("{nombre_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["nombre_empresa"], $html);
            $html = str_replace("{ruc_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["ruc_empresa"], $html);
            $html = str_replace("{direccion_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["direccion_empresa"], $html);
            $html = str_replace("{telefono_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["telefono_empresa"], $html);
            foreach ($cliente as $key => $data) {
                if ($key == "id") {
                    $campo = "{" . $key . "_cliente}";
                    $html = str_replace($campo, $this->idgetFacturas($data, 6), $html);
                } else {
                    $campo = "{" . $key . "_cliente}";
                    $html = str_replace($campo, $data, $html);
                }
            }
            $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["operador"]);
            $operador = $db->getOne("login");
            $html = str_replace("{operador}", $operador["nombre"], $html);
            $html = str_replace("src=\"../images/", "src=\"" . root . "/admin/images/", $html);
            $html = str_replace("src=\"images/", "src=\"" . root . "/admin/images/", $html);
            if (empty($tiposaldo)) {
                $db->where("idfactura", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["nfactura"]);
                $_obfuscated_0D032136081A030B2A012B0231312A0F131633362C1732_ = $db->get("facturaitems");
                $totales = 0;
                foreach ($_obfuscated_0D032136081A030B2A012B0231312A0F131633362C1732_ as $row) {
                    $_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_ = $this->getImpuesto($row["cantidad"] * $row["unidades"], $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["impuesto"], $row["impuesto"]);
                    $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ .= str_replace("\\n", "<br>", nl2br($row["descripcion"])) . "<br><br>";
                    $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ .= "\n    <tr>\n    <td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n    <td valign=\"top\">" . $this->parse($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n    </tr>\n    ";
                    $totales += round($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], 2);
                }
            } else {
                $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ .= str_replace("\\n", "<br>", nl2br($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["descripcion"])) . "<br><br>";
                $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ .= "\n    <tr>\n    <td class=\"pad\">" . str_replace("\n", "<br>", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["descripcion"]) . "</td>\n    <td valign=\"top\">" . $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["cobrado"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n    </tr>\n    ";
                $totales = $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["cobrado"];
            }
            $html = str_replace("{itempos}", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
            $html = str_replace("<tr><td>{items}</td></tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
            $html = str_replace("<tr> <td>{items}</td> </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
            $html = str_replace("<tr>\n    <td>{items}</td>\n    </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
            $db->where("iduser", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["idcliente"]);
            $db->where("estado", "facturado");
            $db->where("iddestino", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["id"]);
            $_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ = $db->get("saldos");
            $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = 0;
            foreach ($_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ as $_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_) {
                if ($_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_["monto"] < 0) {
                    $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ - $_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_["monto"];
                }
            }
            $db->where("iduser", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["idcliente"]);
            $db->where("idorigen", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["id"]);
            $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_ = $db->getOne("saldos");
            if (0 < $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ && $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_["monto"] < 0) {
                $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ + $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_["monto"];
            }
            if (0 < $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_) {
                $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = "-" . $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_;
            }
            $totales = $totales + $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_;
            $html = str_replace("{saldo}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["saldo"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{cobrado}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["cobrado"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{total}", $this->parse($totales, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{descuento}", $this->parse($_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $_obfuscated_0D0325063E2603332A0E031B140D1F341B5B3D142D1D32_ = explode(";", $_obfuscated_0D1D02302E36351634212E050F085C291E312D3B280B11_["otros_impuestos"]);
            foreach ($_obfuscated_0D0325063E2603332A0E031B140D1F341B5B3D142D1D32_ as $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_) {
                $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_++;
                $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_ = [];
                if (0 < $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_) {
                    ${"conterimps" . $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_} = 1;
                    $html = str_replace("{otro_impuesto_" . $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_ . "}", $this->parse($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
                }
            }
            $html = str_replace("{otro_impuesto_1}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{otro_impuesto_2}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $html = str_replace("{otro_impuesto_3}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_ = NumeroALetras::convertir($totales, $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_letra"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_unidad"]);
            $html = str_replace("{total_letras}", $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_, $html);
            $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_ = NumeroALetras::convertir($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["cobrado"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_letra"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_unidad"]);
            $html = str_replace("{total_cobrado_letras}", $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_, $html);
        }
        $html = $this->getHtml($cliente["id"], $html);
        $db->where("cliente", $cliente["id"]);
        $cliente = $db->getOne("tblavisouser");
        if (0 < $cliente["mensaje_comprobante"]) {
            $db->where("id", $cliente["mensaje_comprobante"]);
            $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_ = $db->getOne("notificaciones_factura");
            $html = str_replace("<p>{mensaje_personalizado}</p>", "<div>" . $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_["mensaje"] . "</div>", $html);
            $html = str_replace("{mensaje_personalizado}", $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_["mensaje"], $html);
        } else {
            $html = str_replace("{mensaje_personalizado}", "", $html);
        }
        switch ($action) {
            case "crear":
                $db->where("tipoaviso", "recibo");
                $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
                if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", [(int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"], (int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["alto"]], "", "", 1, 1, 1, 1, 1, 1);
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] . $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["posicion"], "", "", 1, 1, 1, 1, 1, 1);
                }
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output(root . "/admin/ajax/factura/Recibo-" . $id . ".pdf", "F");
                break;
            case "pos-printer":
                $db->where("tipoaviso", "recibopos");
                $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
                if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
                    $_obfuscated_0D1C0E011D342C143B213D255C123B1F2D11212A341D01_ = $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"];
                } else {
                    $_obfuscated_0D1C0E011D342C143B213D255C123B1F2D11212A341D01_ = $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"];
                }
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", "", "", "", 1, 1, 1, 1, 1, 1);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Recibo Nº " . $this->idgetFacturas($id, 8));
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->page = 0;
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->state = 0;
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->pages);
                $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_ = "P";
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->_setPageSize([(int) $_obfuscated_0D1C0E011D342C143B213D255C123B1F2D11212A341D01_, $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->y + 50], $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->addPage("", "", 0, "", 1, 5, 3, 0, 0, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetJS("this.print();");
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html);
                $db->disconnect();
                if (isApiMk()) {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Recibo Nº " . $this->idgetFacturas($id, 8) . ".pdf", "D");
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output();
                }
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_);
                break;
            case "pos":
                $db->where("tipoaviso", "recibopos");
                $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
                if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
                    $_obfuscated_0D1C0E011D342C143B213D255C123B1F2D11212A341D01_ = $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"];
                } else {
                    $_obfuscated_0D1C0E011D342C143B213D255C123B1F2D11212A341D01_ = $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"];
                }
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", "", "", "", 1, 1, 1, 1, 1, 1);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Recibo Nº " . $this->idgetFacturas($id, 8));
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->page = 0;
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->state = 0;
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->pages);
                $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_ = "P";
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->_setPageSize([(int) $_obfuscated_0D1C0E011D342C143B213D255C123B1F2D11212A341D01_, $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->y + 50], $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->addPage("", "", 0, "", 1, 5, 3, 0, 0, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html);
                $db->disconnect();
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output();
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_);
                break;
            case "email":
                $db->where("tipoaviso", "recibo");
                $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
                if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", [(int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"], (int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["alto"]], "", "", 1, 1, 1, 1, 1, 1);
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] . $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["posicion"], "", "", 1, 1, 1, 1, 1, 1);
                }
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                return $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Recibo-" . $this->idgetFacturas($id, 8) . ".pdf", "S");
                break;
            case "ver":
                $db->where("tipoaviso", "recibo");
                $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
                if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", [(int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"], (int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["alto"]], "", "", 1, 1, 1, 1, 1, 1);
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] . $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["posicion"], "", "", 1, 1, 1, 1, 1, 1);
                }
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Recibo Nº " . $this->idgetFacturas($id, 8));
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output();
                break;
            case "ver-printer":
                $db->where("tipoaviso", "recibo");
                $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
                if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", [(int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"], (int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["alto"]], "", "", 1, 1, 1, 1, 1, 1);
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] . $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["posicion"], "", "", 1, 1, 1, 1, 1, 1);
                }
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Recibo Nº " . $this->idgetFacturas($id, 8));
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetJS("this.print();");
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                if (isApiMk()) {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Recibo Nº " . $this->idgetFacturas($id, 8) . ".pdf", "D");
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output();
                }
                break;
        }
    }
    public function getMes($mes)
    {
        $_obfuscated_0D1E192B371A2D2E070B172B1F042111085B5B2C3B3822_ = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
        return $_obfuscated_0D1E192B371A2D2E070B172B1F042111085B5B2C3B3822_[$mes];
    }
    public function facturacionFactura($id, $action = "ver", $html = "")
    {
        error_reporting(24567);
        ini_set("memory_limit", "2G");
        ini_set("max_execution_time", 0);
        set_time_limit(0);
        $db = MysqliDb::getInstance();
        require_once root . "/admin/ajax/mpdf/mpdf.php";
        $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_ = $this->getDDB("currency");
        date_default_timezone_set($this->getDDB("zona_horaria"));
        $db->where("tipoaviso", "factura");
        $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
        $_obfuscated_0D1B102D061B190F1D3F0622402C1D36355B1C31372F11_ = $db->get("tblconfiguration");
        foreach ($_obfuscated_0D1B102D061B190F1D3F0622402C1D36355B1C31372F11_ as $data) {
            $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_[$data["setting"]] = $data["value"];
        }
        $db->where("id", $id);
        $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_ = $db->getOne("facturas");
        $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
        $cliente = $db->getOne("usuarios");
        $db->where("cliente", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
        $_obfuscated_0D030838111E171A131D150333373D330A3039362F1311_ = $db->getOne("tblavisouser");
        $_obfuscated_0D1919030309270237260D1533103D03330E275C361322_ = ["", "1 (Bajo -  Bajo)", "2 (Bajo)", "3 (Medio Bajo)", "4 (Medio)", "5 (Medio Alto)", "6 (Alto)"];
        if (empty($html)) {
            $html = html_entity_decode($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["html"]);
        } else {
            $html = html_entity_decode($html);
        }
        $html = preg_replace("/\\s+/", " ", $html);
        $html = str_replace("\r\n", "", $html);
        $html = str_replace("\r", "", $html);
        $html = str_replace("\n", "", $html);
        preg_match("/<title>(.+)<\\/title>/", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
        $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[1], "Factura Nº " . $id, $html);
        $html = str_replace("{nfactura}", $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"], 8), $html);
        $html = str_replace("{nlegal}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["legal"], $html);
        $html = str_replace("{estrato_cliente}", $_obfuscated_0D1919030309270237260D1533103D03330E275C361322_[$_obfuscated_0D030838111E171A131D150333373D330A3039362F1311_["tipo_estrato"]], $html);
        $html = str_replace("{vencimiento}", date("d/m/Y", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["vencimiento"])), $html);
        $html = str_replace("{emitido}", date("d/m/Y", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["emitido"])), $html);
        $html = str_replace("{nombre_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["nombre_empresa"], $html);
        $html = str_replace("{ruc_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["ruc_empresa"], $html);
        $html = str_replace("{direccion_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["direccion_empresa"], $html);
        $html = str_replace("{telefono_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["telefono_empresa"], $html);
        if (!empty($cliente)) {
            foreach ($cliente as $key => $data) {
                if ($key == "id") {
                    $campo = "{" . $key . "_cliente}";
                    $html = str_replace($campo, $this->idgetFacturas($data, 6), $html);
                } else {
                    $campo = "{" . $key . "_cliente}";
                    $html = str_replace($campo, $data, $html);
                }
            }
        }
        $_obfuscated_0D36260F073B2A282813240215102716182C3B36350601_ = ["No pagado" => "nopagado", "pagado" => "pagado", "anulado" => "anulado"];
        $html = str_replace("{estado}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["estado"], $html);
        $html = str_replace("class=\"estadocss\"", "class=\"" . $_obfuscated_0D36260F073B2A282813240215102716182C3B36350601_[$_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["estado"]] . "\"", $html);
        $db->where("idfactura", $id);
        $_obfuscated_0D400508313517111824291A2901072D302C10051E2432_ = $db->get("facturaitems");
        $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ = "";
        $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ = "";
        $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_ = "";
        $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_ = 0;
        foreach ($_obfuscated_0D400508313517111824291A2901072D302C10051E2432_ as $row) {
            $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_ += $row["impuesto911"];
            $_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_ = $this->getImpuesto($row["cantidad"] * $row["unidades"], $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["impuesto"], $row["impuesto"]);
            $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ .= "\n<tr>\n<td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n<td valign=\"top\">" . $this->parse($row["cantidad"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n<td align=\"center\" valign=\"top\">" . $row["impuesto"] . "</td>\n<td align=\"center\" valign=\"top\">" . $row["unidades"] . "</td>\n<td valign=\"top\">" . $this->parse(round($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], 2), $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n</tr>\n";
            $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ .= "\n<tr>\n<td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n<td valign=\"top\">" . $this->parse(round($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], 2), $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n</tr>\n";
            $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_ .= "\n<tr>\n<td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n</tr>\n";
        }
        if ($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_ == 0) {
            preg_match("#<tr\\sclass=\"otrosimpuestos\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        $html = str_replace("<tr> <td>{items}</td> </tr>", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
        $html = str_replace("<tr><td>{items}</td></tr>", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
        $html = str_replace("<tr>\n<td>{items}</td>\n</tr>", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
        $html = str_replace("<tr> <td>{items2}</td> </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
        $html = str_replace("<tr><td>{items2}</td></tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
        $html = str_replace("<tr>\n<td>{items2}</td>\n</tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
        $html = str_replace("<tr> <td>{items3}</td> </tr>", $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_, $html);
        $html = str_replace("<tr><td>{items3}</td></tr>", $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_, $html);
        $html = str_replace("<tr>\n<td>{items3}</td>\n</tr>", $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_, $html);
        $db->where("iduser", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
        $db->where("estado", "facturado");
        $db->where("iddestino", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"]);
        $_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ = $db->get("saldos");
        $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = 0;
        foreach ($_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ as $_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_) {
            if ($_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_["monto"] < 0) {
                $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ - $_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_["monto"];
            }
        }
        $db->where("iduser", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
        $db->where("idorigen", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"]);
        $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_ = $db->getOne("saldos");
        if (0 < $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ && $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_["monto"] < 0) {
            $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ + $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_["monto"];
        }
        if (0 < $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_) {
            $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = "-" . $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_;
        }
        $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_ = 0;
        $_obfuscated_0D0325063E2603332A0E031B140D1F341B5B3D142D1D32_ = explode(";", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["otros_impuestos"]);
        foreach ($_obfuscated_0D0325063E2603332A0E031B140D1F341B5B3D142D1D32_ as $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_) {
            $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_++;
            $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_ = [];
            if (0 < $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_) {
                ${"conterimps" . $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_} = 1;
                $html = str_replace("{otro_impuesto_" . $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_ . "}", $this->parse($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
            }
        }
        if (empty($_obfuscated_0D383F0D11331C010C1A0A0E1E02331C1D31350C291C01_)) {
            preg_match("#<tr\\sclass=\"otrosimpuestos1\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        if (empty($_obfuscated_0D222F2B101A042A1C070F3B2F170D090B5C23222B0F11_)) {
            preg_match("#<tr\\sclass=\"otrosimpuestos2\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        if (empty($_obfuscated_0D2618061509320A13190F0F04291A21050F25111C1211_)) {
            preg_match("#<tr\\sclass=\"otrosimpuestos3\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        $html = str_replace("{subtotal}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["sub_total"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $html = str_replace("{impuesto}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["iva_igv"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $html = str_replace("{otro_impuesto}", $this->parse($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $html = str_replace("{saldo}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $html = str_replace("{total}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $html = str_replace("{percepcion}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["percepcion_afip"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $html = str_replace("{descuento}", $this->parse($_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $html);
        $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_ = NumeroALetras::convertir($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_letra"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_unidad"]);
        $html = str_replace("{total_letras}", $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_, $html);
        $html = str_replace("src=\"../images/", "src=\"" . root . "/admin/images/", $html);
        $html = str_replace("src=\"images/", "src=\"" . root . "/admin/images/", $html);
        if (!empty($_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["pdf_generado"])) {
            $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_ = preg_split("/(<body.*?>)/i", $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $html = $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_[0] . $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_[1] . "<div style=\"position: absolute;right: 0;bottom: 0;left: 0;padding: 5px;text-align: center;\"><small>PDF Generado " . date("d/m/Y h:i a") . "</small></div>" . $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_[2];
        }
        if (!empty($cliente["pasarela"]) && empty($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["oxxo_referencia"])) {
            $html = str_replace("{barcode}", $cliente["pasarela"], $html);
        } else {
            if (!empty($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["oxxo_referencia"])) {
                $html = str_replace("{barcode}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["oxxo_referencia"], $html);
            } else {
                if (!empty($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["barcode_cobro_digital"])) {
                    $html = str_replace("{barcode}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["barcode_cobro_digital"], $html);
                } else {
                    $html = str_replace("{barcode}", $this->idgetFacturas($id, 8), $html);
                }
            }
        }
        $html = str_replace("{barcode_oxxo}", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["oxxo_referencia"], $html);
        if (!empty($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["barcode_cobro_digital"])) {
            $html = str_replace("{barcode_cobro_digital}", "<img src=\"https://www.cobrodigital.com/wse/bccd/" . $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["barcode_cobro_digital"] . "HL.png\">", $html);
        } else {
            $html = str_replace("{barcode_cobro_digital}", "", $html);
        }
        $db->where("tipo", "Siro");
        $db->where("estado", "on");
        $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_ = $db->getOne("pasarela");
        if (0 < $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["siro"]) {
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"], 9) . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["pais"];
            $html = str_replace("{siro}", $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_, $html);
        } else {
            if (!empty($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["id"])) {
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"], 9) . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["pais"];
                $html = str_replace("{siro}", $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_, $html);
            } else {
                $html = str_replace("{siro}", "", $html);
            }
        }
        if (!empty($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["id"])) {
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = "0447";
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= "0";
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"], 8);
            if ($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["sandbox"] == "boton") {
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["vencimiento"] . " + " . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"] . " days"));
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"]), 7);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
            } else {
                if ($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["comision"] == "1") {
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["vencimiento"] . " + " . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"] . " days"));
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"]), 7);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                } else {
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["vencimiento"]));
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"]), 7);
                    if ($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["tipo"] == "2") {
                        $_obfuscated_0D15330703123F24271B30263626280508351821152F11_ = $this->getDDB("mora_cliente");
                        $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_ = $this->getDDB("reconexion_cliente");
                        $_obfuscated_0D390B250A5C132D3136233D360E0B362F09272D0F2F01_ = $this->getDDB("tipo_reconexion");
                        $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_ = explode("%", $_obfuscated_0D15330703123F24271B30263626280508351821152F11_);
                        if (0 < $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_[0]) {
                            $pos = strpos($_obfuscated_0D15330703123F24271B30263626280508351821152F11_, "%");
                            if ($pos === false) {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D15330703123F24271B30263626280508351821152F11_;
                            } else {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D361E3C2D0201360C291C02103D052B212D1033223211_["total"] * $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_[0] / 100;
                            }
                            $_obfuscated_0D3734333605122A2B025C1913113923212E3B01321522_ = $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_;
                            $db->where("cliente", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
                            $cliente = $db->getOne("tblavisouser");
                            $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_ = number_format($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"] + $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_, 2, ".", "");
                            if ($cliente["corteautomatico"] == "0") {
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(1, 2);
                            } else {
                                $cliente["corteautomatico"] = $cliente["corteautomatico"] - 1;
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($cliente["corteautomatico"], 2);
                            }
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_), 7);
                        } else {
                            if (0 < $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_ && $_obfuscated_0D390B250A5C132D3136233D360E0B362F09272D0F2F01_ == "1") {
                                $db->where("cliente", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
                                $cliente = $db->getOne("tblavisouser");
                                $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_ = explode("%", $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_);
                                $pos = strpos($_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_, "%");
                                if ($pos === false) {
                                    $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_;
                                } else {
                                    $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"] * $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_[0] / 100;
                                }
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ + $_obfuscated_0D3734333605122A2B025C1913113923212E3B01321522_;
                                $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_ = number_format($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"] + $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_, 2, ".", "");
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($cliente["corteautomatico"] + $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"], 2);
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_), 7);
                            } else {
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                            }
                        }
                        if (0 < $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_ && $_obfuscated_0D390B250A5C132D3136233D360E0B362F09272D0F2F01_ == "1") {
                            $db->where("cliente", $_obfuscated_0D361E3C2D0201360C291C02103D052B212D1033223211_["idcliente"]);
                            $cliente = $db->getOne("tblavisouser");
                            $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_ = explode("%", $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_);
                            $pos = strpos($_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_, "%");
                            if ($pos === false) {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_;
                            } else {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"] * $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_[0] / 100;
                            }
                            $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ + $_obfuscated_0D3734333605122A2B025C1913113923212E3B01321522_;
                            $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_ = number_format($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"] + $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_, 2, ".", "");
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($cliente["corteautomatico"] + $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"], 2);
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_), 7);
                        } else {
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                        }
                    } else {
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                    }
                }
            }
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["pais"];
            $_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_ = [1, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3];
            $string = str_split($_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_);
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = 0;
            foreach ($string as $k => $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_) {
                $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ += $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_ * $_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_[$k];
            }
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = explode(".", $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ / 2);
            $_obfuscated_0D101429312C1B3833352D301D2C332C1F161629101022_ = fmod($_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_[0], 10);
            array_push($_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_, 5);
            array_push($string, $_obfuscated_0D101429312C1B3833352D301D2C332C1F161629101022_);
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = 0;
            foreach ($string as $k => $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_) {
                $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ += $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_ * $_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_[$k];
            }
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = explode(".", $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ / 2);
            $_obfuscated_0D281F04082C215C2C111C400909030E2D2E3939362B22_ = fmod($_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_[0], 10);
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $_obfuscated_0D101429312C1B3833352D301D2C332C1F161629101022_;
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $_obfuscated_0D281F04082C215C2C111C400909030E2D2E3939362B22_;
            if ($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["vencimiento"] < date("Y-m-d")) {
                $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"]);
                $_obfuscated_0D3734132E252611091C1B0916360A061C2C162C2D2B32_ = $db->getOne("facturas");
                if (!empty($_obfuscated_0D3734132E252611091C1B0916360A061C2C162C2D2B32_["barcode_siro"])) {
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = $_obfuscated_0D3734132E252611091C1B0916360A061C2C162C2D2B32_["barcode_siro"];
                }
            } else {
                $db->where("id", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["id"]);
                $db->update("facturas", ["barcode_siro" => $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_]);
            }
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = "\n<div style=\"text-align: center;width: 400px;\">\n<barcode code=\"" . $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ . "\" height=\"1.7\" size=\"0.55\" type=\"I25\"></barcode><br>\n<div style=\"font-family: ocrb;font-size:9px;text-align: center\">" . $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ . "</div><br>\n<div>Abonar en: Rapipago, Pago Fácil, Cobro Expres y Provincia Pagos.</div>\n<div>Entidad Recaudadora: BANCO ROELA a través de <img width=\"55px\" src=\"" . root . "/admin/images/siro.png\"></div>\n</div>\n";
            $html = str_replace("{barcode_siro}", $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_, $html);
        } else {
            $html = str_replace("{barcode_siro}", "", $html);
        }
        if ($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["estado"] == "pagado") {
            $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = "<style type=\"text/css\">\n\t.trans{\nfont-size: 85%;\ntable-layout: fixed;\nborder-collapse: collapse;\nwidth:100%;\n\t}\n\t\n.trans td {\npadding:7px 4px;\nborder: 1px solid #d9e8ed;\n}\n\n.trans th {\n\tfont-weight:bold;\n\theight:30px;\n\tborder: 1px solid #d9e8ed;\n\tvertical-align:middle;\n}\n\t\n</style>\n\n<div style=\"display: inline-block;margin: 20px 10px;\">\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"trans\">\n<tr>\n <td colspan=\"4\" align=\"center\"><h3>Transacciones</h3></td>\n </tr>\n  <tbody>\n    <tr>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Fecha</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Forma pago</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Nº transacción</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Total</th>\n    </tr>\n    {itemop}\n    <tr>\n      <td colspan=\"3\" align=\"right\" bgcolor=\"#EEF2F3\"><b>Balance</b></td>\n      <td bgcolor=\"#EEF2F3\" align=\"center\">{balance}</td>\n    </tr>\n  </tbody>\n</table>\n</div>";
            $db->where("nfactura", $id);
            $_obfuscated_0D2D312C093F1D1F1904322E1D30021F041313120A3932_ = $db->get("operaciones");
            $_obfuscated_0D2D121A180803020F050C1C25323232043233273E4022_ = "";
            $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_ = 0;
            foreach ($_obfuscated_0D2D312C093F1D1F1904322E1D30021F041313120A3932_ as $op) {
                $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_ += $op["cobrado"];
                if ($op["forma_pago"] == "PagueloFacil") {
                    $op["forma_pago"] = "PagueloFacil - " . $op["descripcion"];
                }
                $_obfuscated_0D2D121A180803020F050C1C25323232043233273E4022_ .= "<tr>\n      <td align=\"center\">" . date("d/m/Y h:i:s A", strtotime($op["fecha_pago"])) . "</td>\n      <td align=\"center\">" . $op["forma_pago"] . "</td>\n      <td align=\"center\">" . $op["transaccion"] . "</td>\n      <td align=\"center\">" . $this->parse($op["cobrado"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n    </tr>";
            }
            $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = str_replace("{itemop}", $_obfuscated_0D2D121A180803020F050C1C25323232043233273E4022_, $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_);
            if (strpos($op["forma_pago"], "Payu") !== false) {
                $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = str_replace("{balance}", $this->parse(0, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_);
            } else {
                $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = str_replace("{balance}", $this->parse($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["total"] - $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_);
            }
            if (0 < $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_) {
                $html = str_replace("{operaciones}", $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_, $html);
            } else {
                $html = str_replace("{operaciones}", "", $html);
            }
        } else {
            $html = str_replace("{operaciones}", "", $html);
        }
        $_obfuscated_0D362D5C381F36072239392B3315091C0D130E33211501_ = "";
        $_obfuscated_0D261F2D31033724353218221F5C1C36293631151F3901_ = "";
        $_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_ = 0;
        $_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ = $db->rawQuery("Select * from facturas where estado=? and idcliente=? order by vencimiento desc ", ["No pagado", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]]);
        foreach ($_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ as $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_) {
            $_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_ += $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["total"];
            $action .= "<tr>\n      <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["id"], 8) . "</td>\n      <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["emitido"]) . "</td>\n\t  <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["vencimiento"]) . "</td>\n      <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["total"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n\t  <td align=\"center\">Pendiente de Pago</td>\n    </tr>";
            $_obfuscated_0D24103D082F110A1E3410330327212802390E14300222_ .= "<tr>\n      <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["id"], 8) . "</td>\n      <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["emitido"]) . "</td>\n\t  <td align=\"center\">" . $this->getMes(date("m", strtotime($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["vencimiento"]))) . "</td>\n      <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["total"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n\t  <td align=\"center\">Pendiente de Pago</td>\n    </tr>";
        }
        $_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ = $db->rawQuery("Select * from saldos where estado=? and iduser=? and monto > 0 order by fecha desc ", ["no cobrado", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]]);
        foreach ($_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ as $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_) {
            $_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_ += $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["monto"];
            $action .= "<tr>\n      <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n      <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["fecha"]) . "</td>\n\t   <td align=\"center\">---</td>\n      <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["monto"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n\t  <td align=\"center\">Saldo Anterior #" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n    </tr>";
            $_obfuscated_0D24103D082F110A1E3410330327212802390E14300222_ .= "<tr>\n      <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n      <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["fecha"]) . "</td>\n\t   <td align=\"center\">---</td>\n      <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["monto"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</td>\n\t  <td align=\"center\">Saldo Anterior #" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n    </tr>";
        }
        if (!empty($action)) {
            $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = "<style type=\"text/css\">\n\t.trans2{\nfont-size: 85%;\ntable-layout: fixed;\nborder-collapse: collapse;\nwidth:100%;\n\t}\n\t\n.trans2 td {\npadding:7px 4px;\nborder: 1px solid #d9e8ed;\n}\n\n.trans2 th {\n\tfont-weight:bold;\n\theight:30px;\n\tborder: 1px solid #d9e8ed;\n\tvertical-align:middle;\n}\n\t\n</style>\n\n<div style=\"display: inline-block;margin: 20px 10px;\">\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"trans2\">\n<tr>\n <td colspan=\"5\" align=\"center\"><h3>RESUMEN DE DEUDA</h3></td>\n </tr>\n  <tbody>\n    <tr>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Nº Comprobante</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Emitido</th>\n\t  <th bgcolor=\"#EEF2F3\" scope=\"col\">Vencimiento</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">TOTAL</th>\n\t  <th bgcolor=\"#EEF2F3\" scope=\"col\">Detalle</th>\n    </tr>\n " . $action . "\n <tr>\n      <td colspan=\"4\" align=\"right\" bgcolor=\"#EEF2F3\"><b>DEUDA TOTAL</b></td>\n      <td bgcolor=\"#EEF2F3\" align=\"center\"><b>" . $this->parse($_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</b></td>\n    </tr>\n  </tbody>\n</table>\n</div>";
            $_obfuscated_0D10331839212E1C0B0F0B285B2A5B3B21282201313201_ = "<style type=\"text/css\">\n\t.trans2{\nfont-size: 85%;\ntable-layout: fixed;\nborder-collapse: collapse;\nwidth:100%;\n\t}\n\t\n.trans2 td {\npadding:7px 4px;\nborder: 1px solid #d9e8ed;\n}\n\n.trans2 th {\n\tfont-weight:bold;\n\theight:30px;\n\tborder: 1px solid #d9e8ed;\n\tvertical-align:middle;\n}\n\t\n</style>\n\n<div style=\"display: inline-block;margin: 20px 10px;\">\n<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"trans2\">\n<tr>\n <td colspan=\"5\" align=\"center\"><h3>RESUMEN DE DEUDA</h3></td>\n </tr>\n  <tbody>\n    <tr>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Nº Comprobante</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">Emitido</th>\n\t  <th bgcolor=\"#EEF2F3\" scope=\"col\">Mes</th>\n      <th bgcolor=\"#EEF2F3\" scope=\"col\">TOTAL</th>\n\t  <th bgcolor=\"#EEF2F3\" scope=\"col\">Detalle</th>\n    </tr>\n " . $_obfuscated_0D24103D082F110A1E3410330327212802390E14300222_ . "\n <tr>\n      <td colspan=\"4\" align=\"right\" bgcolor=\"#EEF2F3\"><b>DEUDA TOTAL</b></td>\n      <td bgcolor=\"#EEF2F3\" align=\"center\"><b>" . $this->parse($_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_, $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_) . "</b></td>\n    </tr>\n  </tbody>\n</table>\n</div>";
            $html = str_replace("{resumen_deuda2}", $_obfuscated_0D10331839212E1C0B0F0B285B2A5B3B21282201313201_, $html);
            $html = str_replace("{resumen_deuda}", $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_, $html);
        } else {
            $html = str_replace("{resumen_deuda2}", "", $html);
            $html = str_replace("{resumen_deuda}", "", $html);
        }
        $html = $this->getHtml($_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"], $html);
        $db->where("cliente", $_obfuscated_0D03302F013E3D40331C042B2706121E05301114350922_["idcliente"]);
        $cliente = $db->getOne("tblavisouser");
        if (0 < $cliente["mensaje_comprobante"]) {
            $db->where("id", $cliente["mensaje_comprobante"]);
            $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_ = $db->getOne("notificaciones_factura");
            $html = str_replace("<p>{mensaje_personalizado}</p>", "<div>" . $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_["mensaje"] . "</div>", $html);
            $html = str_replace("{mensaje_personalizado}", $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_["mensaje"], $html);
        } else {
            $html = str_replace("{mensaje_personalizado}", "", $html);
        }
        if ($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0") {
            $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", [(int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"], (int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["alto"]], "", "", 1, 1, 1, 1, 1, 1);
        } else {
            $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new mPDF("utf-8", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] . $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["posicion"], "", "", 5, 5, 8, 5, 1, 1);
        }
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->cacheTables = true;
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->packTableData = true;
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
        switch ($action) {
            case "ver":
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
                if (isApiMk()) {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output();
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "I");
                }
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_);
                break;
            case "printer":
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetJS("this.print();");
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
                if (isApiMk()) {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "D");
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "I");
                }
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_);
                break;
            case "email":
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                return $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "S");
                break;
            case "crear":
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                unset($html);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output(root . "/admin/ajax/tmpfc/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "F");
                chown(root . "/admin/ajax/tmpfc/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "www-data");
                chgrp(root . "/admin/ajax/tmpfc/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "www-data");
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_);
                break;
            case "file":
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $db->disconnect();
                unset($html);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output(root . "/admin/ajax/factura/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "F");
                chown(root . "/admin/ajax/factura/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "www-data");
                chgrp(root . "/admin/ajax/factura/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "www-data");
                unset($_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_);
                break;
        }
    }
    public function getFechaPagado($valor)
    {
        $_obfuscated_0D1025155B1D18022536185B35120C5B111627232B0622_ = explode(" ", $valor);
        if (!empty($_obfuscated_0D1025155B1D18022536185B35120C5B111627232B0622_[1])) {
            $_obfuscated_0D10142E1A1828012D34152B1A0831192305381B292722_ = date(" H:i:s", strtotime($_obfuscated_0D1025155B1D18022536185B35120C5B111627232B0622_[1]));
        }
        return implode("/", array_reverse(explode("-", $_obfuscated_0D1025155B1D18022536185B35120C5B111627232B0622_[0]))) . $_obfuscated_0D10142E1A1828012D34152B1A0831192305381B292722_;
    }
    public function getFacturas($cliente, $id, $tipo)
    {
        if (empty($id)) {
            return false;
        }
        if (empty($cliente)) {
            return false;
        }
        date_default_timezone_set($this->getDDB("zona_horaria"));
        $id = $this->idgetFacturas($id, 8);
        $db = MysqliDb::getInstance();
        $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_ = $this->getDDB("currency");
        $db->where("id", $id);
        $_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_ = $db->getOne("operaciones");
        $db->where("id", $cliente);
        $user = $db->getOne("usuarios");
        $db->where("id", $_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["nfactura"]);
        $_obfuscated_0D361E3C2D0201360C291C02103D052B212D1033223211_ = $db->getOne("facturas");
        $db->where("tipoaviso", "confirmacion");
        $_obfuscated_0D0B5C234015342932215C0E09311D0E03363C3B1C1632_ = $db->getOne("formatos");
        if ($tipo == "recibo") {
            $db->where("tipoaviso", "recibo");
            $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
            $this->facturacionAction($id, "crear", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["html"]);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = html_entity_decode($_obfuscated_0D0B5C234015342932215C0E09311D0E03363C3B1C1632_["html"]);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nfactura}", $this->idgetFacturas($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["nfactura"], 8), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nrecibo}", $id, $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nafip}", $_obfuscated_0D2B140A0D131436380B33332A04081A3B2A2A3F390622_["invoice_num"], $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{total}", $this->parse($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["cobrado"] + $_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["saldo"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{url_logo}", $this->getDDB("url_logo"), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{transaccion}", $_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["transaccion"], $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{fecha_pagado}", $this->getFechaPagado($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["fecha_pago"]), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{total_pagado}", $this->parse($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["cobrado"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("http://{url_portal}", $this->getDDB("url_portal"), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{url_portal}", $this->getDDB("url_portal"), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nlegal}", $_obfuscated_0D361E3C2D0201360C291C02103D052B212D1033223211_["legal"], $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = $this->getHtml($cliente, $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $data = ["userid" => $user["id"], "cuerpo" => htmlentities($_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_), "asunto" => $_obfuscated_0D0B5C234015342932215C0E09311D0E03363C3B1C1632_["asuntofactura"], "adjunto" => "ajax/factura/Recibo-" . $id . ".pdf", "mailremitente" => $this->getDDB("correo_factura"), "maildestino" => $user["correo"]];
            $db->insert("mail", $data);
            unset($_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
        }
        if ($tipo == "factura") {
            $db->where("tipoaviso", "factura");
            $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
            $this->facturacionFactura($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["nfactura"], "file", $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["html"]);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = html_entity_decode($_obfuscated_0D0B5C234015342932215C0E09311D0E03363C3B1C1632_["html"]);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nfactura}", $this->idgetFacturas($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["nfactura"], 8), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nrecibo}", $id, $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nafip}", $_obfuscated_0D2B140A0D131436380B33332A04081A3B2A2A3F390622_["invoice_num"], $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{total}", $this->parse($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["cobrado"] + $_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["saldo"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{url_logo}", $this->getDDB("url_logo"), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{transaccion}", $_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["transaccion"], $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{fecha_pagado}", $this->getFechaPagado($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["fecha_pago"]), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{total_pagado}", $this->parse($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["cobrado"], $_obfuscated_0D362219141D06011F281418351910111A260B3E2A5B22_), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("http://{url_portal}", $this->getDDB("url_portal"), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{url_portal}", $this->getDDB("url_portal"), $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = str_replace("{nlegal}", $_obfuscated_0D361E3C2D0201360C291C02103D052B212D1033223211_["legal"], $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_ = $this->getHtml($cliente, $_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
            $data = ["userid" => $user["id"], "cuerpo" => htmlentities($_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_), "asunto" => $_obfuscated_0D0B5C234015342932215C0E09311D0E03363C3B1C1632_["asuntofactura"], "adjunto" => "ajax/factura/Doc-" . $this->idgetFacturas($_obfuscated_0D0E3332070D4004052D5B162A112F021B2C0C150B3901_["nfactura"], 8) . ".pdf", "mailremitente" => $this->getDDB("correo_factura"), "maildestino" => $user["correo"]];
            $db->insert("mail", $data);
            unset($_obfuscated_0D093F242A06360D173408395C0A2A0A0238330D3D2422_);
        }
    }
    public function Estadocliente($idcliente, $suspender = false, $logpersonalizado = "", $isautomatico = false)
    {
        $db = $this->mysql;
        $_obfuscated_0D0A5B5C5C050C105C1F043B023F182F1436312B213432_ = [];
        $_obfuscated_0D2D090205381E380E3D22322D060D382803190F0F2511_ = [];
        $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_ = [];
        $_obfuscated_0D21381C153B092B0E2906343F163415083C142A1E1E32_ = [];
        $_obfuscated_0D392A35372A1C331F2C1A2E02083E362D1928160A3911_ = [];
        $_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_ = [];
        $smsSuspendido = [];
        if (empty($idcliente)) {
            return ["estado" => "error", "salida" => "Ningún cliente a procesar"];
        }
        $_obfuscated_0D12212301292F051B12312C39342230125C5B090F2722_ = explode(",", $idcliente);
        if (1 < $_obfuscated_0D12212301292F051B12312C39342230125C5B090F2722_) {
            $_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_ = $db->rawQuery("SELECT tblservicios.ppp_routes,server.ip as ipserver,server.velocidad as velo,server.velocidad as velo,server.secret_radius,server.seguridad,tblservicios.id,tblservicios.limitado,tblservicios.ip,tblservicios.ipv6,tblservicios.nodo,tblservicios.idperfil,tblservicios.pppuser,usuarios.nombre,usuarios.id as iduser,usuarios.estado,tblservicios.onu_sn,tblservicios.smartolt FROM tblservicios,usuarios,server WHERE tblservicios.idcliente IN(" . $idcliente . ") and usuarios.id=tblservicios.idcliente and server.id=tblservicios.nodo");
            if (empty($_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_)) {
                $db->rawQuery("update usuarios SET estado=? where id IN(" . $idcliente . ")", [$suspender ? "SUSPENDIDO" : "ACTIVO"]);
                return ["estado" => "exito", "salida" => "Operación Exitosa"];
            }
        } else {
            $_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_ = $db->rawQuery("SELECT tblservicios.ppp_routes,server.ip as ipserver,server.velocidad as velo,server.secret_radius,server.seguridad,tblservicios.id,tblservicios.ip,tblservicios.ipv6,tblservicios.limitado,tblservicios.nodo,tblservicios.idperfil,tblservicios.pppuser,usuarios.nombre,usuarios.id as iduser,usuarios.estado,tblservicios.onu_sn,tblservicios.smartolt FROM tblservicios,usuarios,server WHERE tblservicios.idcliente=? and usuarios.id=tblservicios.idcliente and server.id=tblservicios.nodo", [$idcliente]);
            if (empty($_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_)) {
                $db->where("id", $idcliente);
                $db->update("usuarios", ["estado" => $suspender ? "SUSPENDIDO" : "ACTIVO"]);
                return ["estado" => "exito", "salida" => "Operación Exitosa"];
            }
        }
        foreach ($_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_ as $row) {
            if (!empty($row["ppp_routes"])) {
                $row["ip"] = $row["ppp_routes"];
            }
            $db->where("cliente", $row["iduser"]);
            $cliente = $db->getOne("tblavisouser");
            $cliente = (int) $cliente;
            if ($row["estado"] == "ACTIVO" && $row["limitado"] == 0 && !$suspender) {
                return ["estado" => "error", "salida" => "No se realizó ningún cambio de estado al cliente porque está activo"];
            }
            if (2 < $row["seguridad"]) {
                if ($row["estado"] == "SUSPENDIDO") {
                    $db->rawQueryOne("DELETE from radreply where username=? and attribute=\"Mikrotik-Address-List\" and value=\"morosos\"", [$row["pppuser"]]);
                    $_obfuscated_0D24111C223E171F221838400811331D1C3F11160A0611_ = $db->rawQueryOne("select radacctid,framedipaddress as ip from radacct where username=? and acctstoptime is null  order by radacctid DESC limit 1", [$row["pppuser"]]);
                    if (!empty($_obfuscated_0D24111C223E171F221838400811331D1C3F11160A0611_["radacctid"])) {
                        $sql = "SELECT framedipaddress as ip FROM radacct where username=? order by radacctid DESC limit 1";
                        $address = $db->rawQueryOne($sql, [$row["pppuser"]]);
                        $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Corte_Servicio_" . $row["id"]]];
                        $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?list" => "morosos", "?address" => $address["ip"]]];
                    }
                    $db->where("id", $row["iduser"]);
                    $db->update("usuarios", ["estado" => "ACTIVO"]);
                    $_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_[$row["nodo"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $row["pppuser"]]];
                    if (!empty($row["onu_sn"])) {
                        $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_ = new mikrowisp_pso();
                        $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->url = pso_url;
                        $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->user = pso_user;
                        $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_ = $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->getonu($row["onu_sn"]);
                        if ($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["estado"] == "exito") {
                            $_obfuscated_0D06022A0E1B4006141C34360A220619400A14143C3422_ = ["op" => "ont_modify_by_sn", "env" => "db", "data" => [["sn" => $row["onu_sn"], "frame" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["olt"], "activate_catv" => 1, "model_name" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["modelo"]]]];
                            $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->editonu($editonu);
                            unset($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_);
                        }
                        unset($_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_);
                    }
                    if (!empty($this->smart_url) && !empty($this->smart_api) && !empty($row["smartolt"])) {
                        if (0 < $this->smart_corte_onu) {
                            $_obfuscated_0D3C1F06050F070D0226070C032D323D2C2F3421122122_ = new smartolt();
                            $smartolt->urlsmart = $this->smart_url;
                            $smartolt->apismart = $this->smart_api;
                            $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->enable_catv($row["id"]);
                            unset($smartolt);
                            if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                $log = "Servicio CATV Activado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                            }
                            $smartolt = new smartolt();
                            $smartolt->urlsmart = $this->smart_url;
                            $smartolt->apismart = $this->smart_api;
                            $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->enable_onu($row["id"]);
                            unset($smartolt);
                            if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                $log = "ONU Activado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                            }
                        } else {
                            $smartolt = new smartolt();
                            $smartolt->urlsmart = $this->smart_url;
                            $smartolt->apismart = $this->smart_api;
                            $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->enable_catv($row["id"]);
                            unset($smartolt);
                            if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                $log = "Servicio CATV Activado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                            }
                        }
                    }
                    $log = "Servicio Activado (" . $row["nombre"] . ") - Servicio ID: " . $row["id"];
                    $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                } else {
                    if ($row["estado"] == "ACTIVO" && 0 < $row["limitado"] && !$suspender) {
                        $_obfuscated_0D392A35372A1C331F2C1A2E02083E362D1928160A3911_[] = $row["id"];
                    } else {
                        if ($row["estado"] == "ACTIVO" && $cliente->limit_velocidad == 0 && $suspender) {
                            $_obfuscated_0D3C090D1D33375B041D40273F3813150B1E135C190222_ = ["username" => $row["pppuser"], "attribute" => "Mikrotik-Address-List", "op" => "+=", "value" => "morosos"];
                            $db->insert("radreply", $_obfuscated_0D3C090D1D33375B041D40273F3813150B1E135C190222_);
                            $_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_[$row["nodo"]]["DELETE"][] = ["/ppp/active/add", [".proplist" => ".id", "?name" => $row["pppuser"]]];
                            $db->where("id", $row["iduser"]);
                            $db->update("usuarios", ["estado" => "SUSPENDIDO"]);
                            $db->where("cliente", $row["iduser"]);
                            $db->update("tblavisouser", ["fecha_suspendido" => date("Y-m-d H:i:s")]);
                            if ($this->smsSuspendido) {
                                $smsSuspendido[$row["iduser"]] = $row["iduser"];
                            }
                            if (!empty($cliente->mikrowisp_app_id) && !empty($this->pushEnabled)) {
                                $_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_ = [];
                                $_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_ = [];
                                $_obfuscated_0D370B2E25032A1E1307233C231421012B1E052D081622_ = explode("[-]", $cliente->mikrowisp_app_id);
                                foreach ($_obfuscated_0D370B2E25032A1E1307233C231421012B1E052D081622_ as $_obfuscated_0D013334242E0C312529270B332D091E331F081C061011_) {
                                    $_obfuscated_0D3B2F2A1A2A39041D251D333903381825281F0D215B32_ = 0;
                                    $_obfuscated_0D0F2B3509065B16213C13231A0A0405353F0A0A401301_ = str_replace("WEBPUSH", "", $_obfuscated_0D013334242E0C312529270B332D091E331F081C061011_, $_obfuscated_0D3B2F2A1A2A39041D251D333903381825281F0D215B32_);
                                    if (0 < $_obfuscated_0D3B2F2A1A2A39041D251D333903381825281F0D215B32_) {
                                        array_push($_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_, $_obfuscated_0D0F2B3509065B16213C13231A0A0405353F0A0A401301_);
                                    } else {
                                        array_push($_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_, $_obfuscated_0D0F2B3509065B16213C13231A0A0405353F0A0A401301_);
                                    }
                                }
                                $_obfuscated_0D132F0C2828172C090F1A182221062225333936403822_ = $this->getHtml($row["iduser"], "Estimado(a) cliente, su servicio fue suspendido porque tiene una deuda pendiente de {deuda_cliente}.");
                                $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_ = ["cliente" => $row["iduser"], "estado" => 2, "portal" => 1, "url" => $this->urlsistema(true) . "/avisos/?id=" . $this->EncryptFunction("encode", $row["id"]) . "&tipo=corte", "titulo" => "SERVICIO SUSPENDIDO", "mensaje" => $_obfuscated_0D132F0C2828172C090F1A182221062225333936403822_];
                                $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["text_url"] = "VER AVISO DE CORTE";
                                if (0 < count($_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_)) {
                                    $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["id_signal"] = serialize($_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_);
                                    $_obfuscated_0D0A5B5C5C050C105C1F043B023F182F1436312B213432_[$row["iduser"]] = $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_;
                                }
                                if (0 < count($_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_)) {
                                    $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["tipo"] = 1;
                                    $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["id_signal"] = serialize($_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_);
                                    $_obfuscated_0D2D090205381E380E3D22322D060D382803190F0F2511_[$row["iduser"]] = $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_;
                                }
                            }
                            if (!empty($row["onu_sn"])) {
                                $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_ = new mikrowisp_pso();
                                $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->url = pso_url;
                                $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->user = pso_user;
                                $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_ = $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->getonu($row["onu_sn"]);
                                if ($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["estado"] == "exito") {
                                    $editonu = ["op" => "ont_modify_by_sn", "env" => "db", "data" => [["sn" => $row["onu_sn"], "frame" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["olt"], "activate_catv" => 0, "model_name" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["modelo"]]]];
                                    $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->editonu($editonu);
                                    unset($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_);
                                }
                                unset($_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_);
                            }
                            if (!empty($this->smart_url) && !empty($this->smart_api) && !empty($row["smartolt"])) {
                                if (0 < $this->smart_corte_onu) {
                                    $smartolt = new smartolt();
                                    $smartolt->urlsmart = $this->smart_url;
                                    $smartolt->apismart = $this->smart_api;
                                    $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->disable_catv($row["id"]);
                                    unset($smartolt);
                                    if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                        $log = "Servicio CATV Desactivado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                        $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                                    }
                                    $smartolt = new smartolt();
                                    $smartolt->urlsmart = $this->smart_url;
                                    $smartolt->apismart = $this->smart_api;
                                    $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->disable_onu($row["id"]);
                                    unset($smartolt);
                                    if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                        $log = "ONU Desactivado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                        $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                                    }
                                } else {
                                    $smartolt = new smartolt();
                                    $smartolt->urlsmart = $this->smart_url;
                                    $smartolt->apismart = $this->smart_api;
                                    $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->disable_catv($row["id"]);
                                    unset($smartolt);
                                    if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                        $log = "Servicio CATV Desactivado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                        $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                                    }
                                }
                            }
                            $log = "Servicio Suspendido " . $logpersonalizado . " (" . $row["nombre"] . ") - Servicio ID: " . $row["id"];
                            $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                        } else {
                            if ($row["estado"] == "ACTIVO" && $row["limitado"] == 0 && $suspender && 0 < $cliente->limit_velocidad) {
                                $_obfuscated_0D21381C153B092B0E2906343F163415083C142A1E1E32_[] = $row["id"];
                            }
                        }
                    }
                }
            } else {
                if ($row["estado"] == "SUSPENDIDO") {
                    $_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ = explode(",", $row["ip"]);
                    foreach ($_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ as $ip) {
                        $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Corte_Servicio_" . $row["id"]]];
                        $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Corte_Servicio_" . $row["id"]]];
                    }
                    $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Corte_Servicio_" . $row["id"]]];
                    if (!empty($row["ipv6"])) {
                        $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ipv6/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Corte_Servicio_" . $row["id"]]];
                    }
                    $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Corte_Servicio_" . $row["id"]]];
                    $db->where("id", $row["iduser"]);
                    $db->update("usuarios", ["estado" => "ACTIVO"]);
                    if (!empty($row["onu_sn"])) {
                        $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_ = new mikrowisp_pso();
                        $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->url = pso_url;
                        $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->user = pso_user;
                        $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_ = $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->getonu($row["onu_sn"]);
                        if ($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["estado"] == "exito") {
                            $editonu = ["op" => "ont_modify_by_sn", "env" => "db", "data" => [["sn" => $row["onu_sn"], "frame" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["olt"], "activate_catv" => 1, "model_name" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["modelo"]]]];
                            $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->editonu($editonu);
                            unset($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_);
                        }
                        unset($_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_);
                    }
                    if (!empty($this->smart_url) && !empty($this->smart_api) && !empty($row["smartolt"])) {
                        if (0 < $this->smart_corte_onu) {
                            $smartolt = new smartolt();
                            $smartolt->urlsmart = $this->smart_url;
                            $smartolt->apismart = $this->smart_api;
                            $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->enable_catv($row["id"]);
                            unset($smartolt);
                            if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                $log = "Servicio CATV Activado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                            }
                            $smartolt = new smartolt();
                            $smartolt->urlsmart = $this->smart_url;
                            $smartolt->apismart = $this->smart_api;
                            $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->enable_onu($row["id"]);
                            unset($smartolt);
                            if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                $log = "ONU Activado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                            }
                        } else {
                            $smartolt = new smartolt();
                            $smartolt->urlsmart = $this->smart_url;
                            $smartolt->apismart = $this->smart_api;
                            $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->enable_catv($row["id"]);
                            unset($smartolt);
                            if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                $log = "Servicio CATV Activado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                            }
                        }
                    }
                    $log = "Servicio Activado (" . $row["nombre"] . ") - Servicio ID: " . $row["id"];
                    $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                } else {
                    if ($row["estado"] == "ACTIVO" && 0 < $row["limitado"] && !$suspender) {
                        $_obfuscated_0D392A35372A1C331F2C1A2E02083E362D1928160A3911_[] = $row["id"];
                    } else {
                        if ($row["estado"] == "ACTIVO" && $cliente->limit_velocidad == 0 && $suspender) {
                            $db->where("idservicio", $row["id"]);
                            $db->delete("webproxy");
                            $_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ = explode(",", $row["ip"]);
                            foreach ($_obfuscated_0D2D23012C2D1E1915312A0510192638315B1A1F402901_ as $ip) {
                                $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["ADD"][] = ["/ip/firewall/address-list/add", ["list" => "morosos", "comment" => "Corte_Servicio_" . $row["id"], "address" => trim($ip), "disabled" => "no"]];
                                $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["ADD"][] = ["/ip/proxy/access/add", ["action" => "deny", "comment" => "Corte_Servicio_" . $row["id"], "redirect-to" => $this->urlsistema() . "/avisos/?id=" . $this->EncryptFunction("encode", $row["id"]) . "&tipo=corte", "src-address" => trim($ip)]];
                            }
                            if (!empty($row["ipv6"])) {
                                $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["ADD"][] = ["/ipv6/firewall/address-list/add", ["list" => "morosos", "comment" => "Corte_Servicio_" . $row["id"], "address" => trim($row["ipv6"]), "disabled" => "no"]];
                            }
                            $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Aviso_Servicio_" . $row["id"]]];
                            $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Anuncio_Servicio_" . $row["id"]]];
                            $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Aviso_Servicio_" . $row["id"]]];
                            $_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Anuncio_Servicio_" . $row["id"]]];
                            $db->where("id", $row["iduser"]);
                            $db->update("usuarios", ["estado" => "SUSPENDIDO"]);
                            $db->where("cliente", $row["iduser"]);
                            $db->update("tblavisouser", ["fecha_suspendido" => date("Y-m-d H:i:s")]);
                            if ($this->smsSuspendido) {
                                $smsSuspendido[$row["iduser"]] = $row["iduser"];
                            }
                            if (!empty($cliente->mikrowisp_app_id) && !empty($this->pushEnabled)) {
                                $_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_ = [];
                                $_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_ = [];
                                $_obfuscated_0D370B2E25032A1E1307233C231421012B1E052D081622_ = explode("[-]", $cliente->mikrowisp_app_id);
                                foreach ($_obfuscated_0D370B2E25032A1E1307233C231421012B1E052D081622_ as $_obfuscated_0D013334242E0C312529270B332D091E331F081C061011_) {
                                    $_obfuscated_0D3B2F2A1A2A39041D251D333903381825281F0D215B32_ = 0;
                                    $_obfuscated_0D0F2B3509065B16213C13231A0A0405353F0A0A401301_ = str_replace("WEBPUSH", "", $_obfuscated_0D013334242E0C312529270B332D091E331F081C061011_, $_obfuscated_0D3B2F2A1A2A39041D251D333903381825281F0D215B32_);
                                    if (0 < $_obfuscated_0D3B2F2A1A2A39041D251D333903381825281F0D215B32_) {
                                        array_push($_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_, $_obfuscated_0D0F2B3509065B16213C13231A0A0405353F0A0A401301_);
                                    } else {
                                        array_push($_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_, $_obfuscated_0D0F2B3509065B16213C13231A0A0405353F0A0A401301_);
                                    }
                                }
                                $_obfuscated_0D132F0C2828172C090F1A182221062225333936403822_ = $this->getHtml($row["iduser"], "Estimado(a) cliente, su servicio fue suspendido porque tiene una deuda pendiente de {deuda_cliente}.");
                                $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_ = ["cliente" => $row["iduser"], "estado" => 2, "portal" => 1, "url" => $this->urlsistema(true) . "/avisos/?id=" . $this->EncryptFunction("encode", $row["id"]) . "&tipo=corte", "titulo" => "SERVICIO SUSPENDIDO", "mensaje" => $_obfuscated_0D132F0C2828172C090F1A182221062225333936403822_];
                                $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["text_url"] = "VER AVISO DE CORTE";
                                if (0 < count($_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_)) {
                                    $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["id_signal"] = serialize($_obfuscated_0D101B13162B1D3129223F1022362113350C34160C3C01_);
                                    $_obfuscated_0D0A5B5C5C050C105C1F043B023F182F1436312B213432_[$row["iduser"]] = $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_;
                                }
                                if (0 < count($_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_)) {
                                    $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["tipo"] = 1;
                                    $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_["id_signal"] = serialize($_obfuscated_0D0E1A401113300C1B052C1C2328190A07092D2C220722_);
                                    $_obfuscated_0D2D090205381E380E3D22322D060D382803190F0F2511_[$row["iduser"]] = $_obfuscated_0D1F1B0A5C352A1F3422111B0F1A06111F121D01380D01_;
                                }
                            }
                            if (!empty($row["onu_sn"])) {
                                $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_ = new mikrowisp_pso();
                                $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->url = pso_url;
                                $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->user = pso_user;
                                $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_ = $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->getonu($row["onu_sn"]);
                                if ($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["estado"] == "exito") {
                                    $editonu = ["op" => "ont_modify_by_sn", "env" => "db", "data" => [["sn" => $row["onu_sn"], "frame" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["olt"], "activate_catv" => 0, "model_name" => $_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_["modelo"]]]];
                                    $_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_->editonu($editonu);
                                    unset($_obfuscated_0D3F0F1418120E3B1C032C0913022E2A3201245B342E11_);
                                }
                                unset($_obfuscated_0D09132D3D172C013E0A092E021636010111051D290822_);
                            }
                            if (!empty($this->smart_url) && !empty($this->smart_api) && !empty($row["smartolt"])) {
                                if (0 < $this->smart_corte_onu) {
                                    $smartolt = new smartolt();
                                    $smartolt->urlsmart = $this->smart_url;
                                    $smartolt->apismart = $this->smart_api;
                                    $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->disable_catv($row["id"]);
                                    unset($smartolt);
                                    if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                        $log = "Servicio CATV Desactivado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                        $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                                    }
                                    $smartolt = new smartolt();
                                    $smartolt->urlsmart = $this->smart_url;
                                    $smartolt->apismart = $this->smart_api;
                                    $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->disable_onu($row["id"]);
                                    unset($smartolt);
                                    if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                        $log = "ONU Desactivado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                        $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                                    }
                                } else {
                                    $smartolt = new smartolt();
                                    $smartolt->urlsmart = $this->smart_url;
                                    $smartolt->apismart = $this->smart_api;
                                    $_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_ = $smartolt->disable_catv($row["id"]);
                                    unset($smartolt);
                                    if ($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_["status"]) {
                                        $log = "Servicio CATV Desactivado (" . $row["nombre"] . ") - External ID: " . $row["id"] . "<br>" . json_encode($_obfuscated_0D2B1A012F251C262D0E023F0A3C3F2633232D1D080932_);
                                        $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                                    }
                                }
                            }
                            $log = "Servicio Suspendido " . $logpersonalizado . " (" . $row["nombre"] . ") - Servicio ID: " . $row["id"];
                            $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $row["iduser"], 2);
                        } else {
                            if ($row["estado"] == "ACTIVO" && $row["limitado"] == 0 && $suspender && 0 < $cliente->limit_velocidad) {
                                $_obfuscated_0D21381C153B092B0E2906343F163415083C142A1E1E32_[] = $row["id"];
                            }
                        }
                    }
                }
            }
        }
        $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_ = new MKWS();
        if (!empty($_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_)) {
            foreach ($_obfuscated_0D04191633210B2A14341F123C03121E115C35303B0122_ as $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_ => $_obfuscated_0D1C0D3E3F1E0C15120F2D35062B372E08140A17125C01_) {
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->idmk = $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->datamk = $datamk[$_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_];
                $_obfuscated_0D0F0C335C0225291D0B5C161D031C5C2C3128231A5B01_ = $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->MIKROTIK();
            }
        }
        if (!empty($_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_)) {
            foreach ($_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_ as $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_ => $datamk) {
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->idmk = $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->datamk = $_obfuscated_0D361A025C33382A13175B0F13193C1507331A290A1911_[$_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_];
                $_obfuscated_0D0F0C335C0225291D0B5C161D031C5C2C3128231A5B01_ = $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->MIKROTIK();
            }
        }
        if (!empty($_obfuscated_0D21381C153B092B0E2906343F163415083C142A1E1E32_)) {
            $_obfuscated_0D181713221A400E1E05403C2D1F372801391F120E0A01_ = $this->limitarservicio(implode(",", $_obfuscated_0D21381C153B092B0E2906343F163415083C142A1E1E32_));
            foreach ($_obfuscated_0D181713221A400E1E05403C2D1F372801391F120E0A01_ as $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_ => $datamk) {
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->idmk = $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->limitar = true;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->datamk = $_obfuscated_0D181713221A400E1E05403C2D1F372801391F120E0A01_[$_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_];
                $_obfuscated_0D0F0C335C0225291D0B5C161D031C5C2C3128231A5B01_ = $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->MIKROTIK();
            }
        }
        if (!empty($_obfuscated_0D392A35372A1C331F2C1A2E02083E362D1928160A3911_)) {
            $_obfuscated_0D181713221A400E1E05403C2D1F372801391F120E0A01_ = $this->quitarlimitarservicio(implode(",", $_obfuscated_0D392A35372A1C331F2C1A2E02083E362D1928160A3911_));
            foreach ($_obfuscated_0D181713221A400E1E05403C2D1F372801391F120E0A01_ as $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_ => $datamk) {
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->idmk = $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->limitar = true;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->datamk = $_obfuscated_0D181713221A400E1E05403C2D1F372801391F120E0A01_[$_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_];
                $_obfuscated_0D0F0C335C0225291D0B5C161D031C5C2C3128231A5B01_ = $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->MIKROTIK();
            }
        }
        if (!empty($_obfuscated_0D0A5B5C5C050C105C1F043B023F182F1436312B213432_)) {
            foreach ($_obfuscated_0D0A5B5C5C050C105C1F043B023F182F1436312B213432_ as $_obfuscated_0D25352F1C06240B0B402117300F09011A2A371B1A3F11_) {
                if (!empty($_obfuscated_0D25352F1C06240B0B402117300F09011A2A371B1A3F11_)) {
                    $db->insert("push", $_obfuscated_0D25352F1C06240B0B402117300F09011A2A371B1A3F11_);
                }
            }
        }
        if (!empty($_obfuscated_0D2D090205381E380E3D22322D060D382803190F0F2511_)) {
            foreach ($_obfuscated_0D2D090205381E380E3D22322D060D382803190F0F2511_ as $_obfuscated_0D25352F1C06240B0B402117300F09011A2A371B1A3F11_) {
                if (!empty($_obfuscated_0D25352F1C06240B0B402117300F09011A2A371B1A3F11_)) {
                    $db->insert("push", $_obfuscated_0D25352F1C06240B0B402117300F09011A2A371B1A3F11_);
                }
            }
        }
        if (!$isautomatico && !empty($smsSuspendido)) {
            $db->where("tipoaviso", "smscorte");
            $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
            foreach ($smsSuspendido as $_obfuscated_0D3205250219153E053F0B18060E0B140E08111E363E22_) {
                $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_ = $db->rawQueryOne("Select usuarios.movil,usuarios.nombre,(select COALESCE(SUM(facturas.total),0) from facturas where facturas.idcliente=? and facturas.estado='No pagado' ) as total from usuarios where usuarios.id=?", [$_obfuscated_0D3205250219153E053F0B18060E0B140E08111E363E22_, $_obfuscated_0D3205250219153E053F0B18060E0B140E08111E363E22_]);
                if (!empty($_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["movil"])) {
                    $_obfuscated_0D213504112D2E1F0209080D0C182608090E2F1E223132_ = explode(",", $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["movil"]);
                    $_obfuscated_0D042236181310231528383901043F341F172C3D3D3532_ = str_replace("{total}", $this->parse($_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["total"], $this->moneda), $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["html"]);
                    $_obfuscated_0D042236181310231528383901043F341F172C3D3D3532_ = str_replace("{nombre_cliente}", $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["nombre"], $_obfuscated_0D042236181310231528383901043F341F172C3D3D3532_);
                    $_obfuscated_0D042236181310231528383901043F341F172C3D3D3532_ = $this->getHtml($_obfuscated_0D3205250219153E053F0B18060E0B140E08111E363E22_, $_obfuscated_0D042236181310231528383901043F341F172C3D3D3532_);
                    foreach ($_obfuscated_0D213504112D2E1F0209080D0C182608090E2F1E223132_ as $_obfuscated_0D272832402C243F401E1311115C0132080C33231E2332_) {
                        $_obfuscated_0D2E403819132232275B261A0D361C033D283033270F11_ = ["user" => $_obfuscated_0D3205250219153E053F0B18060E0B140E08111E363E22_, "ndestino" => $_obfuscated_0D272832402C243F401E1311115C0132080C33231E2332_, "enviado" => date("Y-m-d H:i:s"), "mensaje" => strip_tags(html_entity_decode($_obfuscated_0D042236181310231528383901043F341F172C3D3D3532_))];
                        $db->insert("smsmensajes", $_obfuscated_0D2E403819132232275B261A0D361C033D283033270F11_);
                    }
                    $log = "SMS de Corte generado (" . $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["nombre"] . ")";
                    $this->crearLog($log, isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0, $_obfuscated_0D3205250219153E053F0B18060E0B140E08111E363E22_);
                }
            }
        }
        return ["estado" => "exito", "salida" => "operación exitosa"];
    }
    public function nlegal($factura)
    {
        $db = $this->mysql;
        if (isset($factura->legal) && $factura->legal == 0) {
            $legal = $this->getDDB("nfacturalegal");
            $_obfuscated_0D0A052B10330D342D2B1E1D402F33241908061E1D0732_ = $legal + 1;
            $db->rawQueryOne("UPDATE tblconfiguration SET value='" . $_obfuscated_0D0A052B10330D342D2B1E1D402F33241908061E1D0732_ . "' Where setting='nfacturalegal'");
            return $legal;
        }
        if (isset($factura->legal) && 0 < $factura->legal) {
            return $factura->legal;
        }
        return 0;
    }
    public function addpago($data, $activaCliente = true, $Ispasarela = false, $isMultiple = false)
    {
        $db = $this->mysql;
        if (!isset($data["operador"])) {
            $data["operador"] = isset($_SESSION["idusername"]) ? $_SESSION["idusername"] : 0;
        }
        $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $this->getDDB("send_pagado");
        $_obfuscated_0D22020A2D2726340B3538241A0A142C160A1A27111622_ = $this->getDDB("send_recibo");
        $factura = $db->rawQueryOne("SELECT facturas.id,facturas.idcliente,facturas.tipo,facturas.legal,facturas.total,facturas.vencimiento FROM facturas where facturas.id=? and facturas.estado='No pagado' limit 1", [$data["nfactura"]]);
        $factura = json_decode(json_encode($factura));
        if (!isset($factura->id)) {
            return ["estado" => "error", "salida" => "La facctura no existe o ya esta pagada."];
        }
        $_obfuscated_0D373107020205180F0C01040225282E3D213625163401_ = ["PagueloFacil" => 0];
        if (!$Ispasarela && !empty($this->validapago) && trim($data["transaccion"]) && !isset($_obfuscated_0D373107020205180F0C01040225282E3D213625163401_[$data["forma_pago"]])) {
            $db->where("forma_pago", $data["forma_pago"]);
            $db->where("transaccion", trim($data["transaccion"]));
            $_obfuscated_0D0133172B3F3B3F3D233F360E312F010F3B05360B4011_ = $db->getOne("operaciones");
            if (isset($_obfuscated_0D0133172B3F3B3F3D233F360E312F010F3B05360B4011_["id"])) {
                $this->crearLog("Pago no registrado porqué ID transacción Ya esta registrado (" . $data["forma_pago"] . ":" . $data["transaccion"] . ")", $data["operador"], $factura->idcliente);
                return ["estado" => "error", "salida" => "El Nº de transacción ya esta registrado para otro pago: " . $data["transaccion"]];
            }
        }
        if (!isset($data["fecha"])) {
            $data["fecha"] = date("Y-m-d H:i:s");
        }
        if (!isset($data["meses"])) {
            $data["meses"] = 1;
        }
        if (!isset($data["comision"])) {
            $data["comision"] = 0;
        }
        $_obfuscated_0D153D0706242D2608332A191018271130273432311822_ = false;
        if (strpos($data["forma_pago"], "Payu -") !== false) {
            $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ = 0;
        } else {
            if ($data["forma_pago"] == "PayPal/Visa/Mastercard" && 0 < $data["comision"]) {
                $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ = 0;
            } else {
                if ($data["forma_pago"] == "Mercadopago" && 0 < $data["comision"]) {
                    $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ = 0;
                } else {
                    if ($data["forma_pago"] == "culqui") {
                        $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ = 0;
                    } else {
                        if ($data["forma_pago"] == "flow") {
                            $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ = 0;
                        } else {
                            $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ = round($factura->total - $data["cobrado"], 2);
                        }
                    }
                }
            }
        }
        $_obfuscated_0D114017270B34341E3212400E0A3619160F011E012232_ = ["nfactura" => $factura->id, "idcliente" => $factura->idcliente, "fecha_pago" => $data["fecha"], "saldo" => $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_, "operador" => $data["operador"], "transaccion" => $data["transaccion"], "forma_pago" => $data["forma_pago"], "cobrado" => $data["cobrado"], "meses" => $data["meses"], "comision" => $data["comision"], "descripcion" => isset($data["descripcion"]) ? $data["descripcion"] : "", "descripcion_pago" => isset($data["descripcion_pago"]) ? $data["descripcion_pago"] : ""];
        $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_ = $db->insert("operaciones", $_obfuscated_0D114017270B34341E3212400E0A3619160F011E012232_);
        if (!$_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_) {
            return ["estado" => "error", "salida" => "El pago no fué registrado por error de datos.", "error" => $db->getLastError()];
        }
        if ($this->getDDB("update_alpagar") && $factura->tipo == "2" && $factura->vencimiento < date("Y-m-d")) {
            $_obfuscated_0D04381D1F092B10051E330A082F040F1D0903273C1E01_ = date("j");
            if (28 < $_obfuscated_0D04381D1F092B10051E330A082F040F1D0903273C1E01_) {
                $_obfuscated_0D04381D1F092B10051E330A082F040F1D0903273C1E01_ = 1;
            }
            $db->where("cliente", $factura->idcliente);
            $db->update("tblavisouser", ["diapago" => $_obfuscated_0D04381D1F092B10051E330A082F040F1D0903273C1E01_]);
            $_obfuscated_0D2E3E2B021E221716112925353C282F5B385C5C025C22_ = "Dia de pago actualizado a (" . $_obfuscated_0D04381D1F092B10051E330A082F040F1D0903273C1E01_ . ") - Factura ID: " . $factura->id;
            $this->crearLog($_obfuscated_0D2E3E2B021E221716112925353C282F5B385C5C025C22_, $data["operador"], $factura->idcliente);
        }
        if (0 < $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ || $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_ < 0) {
            $_obfuscated_0D153D0706242D2608332A191018271130273432311822_ = true;
            $_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ = ["idorigen" => $factura->id, "iduser" => $factura->idcliente, "fecha" => date("Y-m-d H:i:s"), "monto" => $_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_];
            $db->insert("saldos", $_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_);
            $_obfuscated_0D16105B3D1C1A28212E5C133C100E3D5B121A16272332_ = "Saldo generado al registrar transacción (Total cobrado: " . $this->parse($data["cobrado"], $this->moneda) . ", Saldo: " . $this->parse($_obfuscated_0D1E0C365B31173B1733030E23190D1A013C222B282511_, $this->moneda) . ") - Factura ID: " . $factura->id;
            $this->crearLog($_obfuscated_0D16105B3D1C1A28212E5C133C100E3D5B121A16272332_, $data["operador"], $factura->idcliente);
        }
        $nlegal = $this->nlegal($factura);
        $_obfuscated_0D2E403819132232275B261A0D361C033D283033270F11_ = ["estado" => "pagado", "legal" => $nlegal, "cobrado" => $data["cobrado"], "forma" => $data["forma_pago"], "pago" => $data["fecha"]];
        $db->where("id", $factura->id);
        $db->update("facturas", $_obfuscated_0D2E403819132232275B261A0D361C033D283033270F11_);
        $db->where("cliente", $factura->idcliente);
        $cliente = $db->getOne("tblavisouser");
        $cliente = (int) $cliente;
        $_obfuscated_0D111A141B171F231D2840012A1A3B1E033D2731283B32_ = true;
        if ($factura->tipo == "2") {
            $db->where("idcliente", $factura->idcliente);
            $db->delete("credito");
        }
        $_obfuscated_0D1919231C230F0D0507070B152F0F1129233322033822_ = unserialize($cliente->invoice_data);
        $db->where("estado", "on");
        $_obfuscated_0D040B2B2B1B3D400E331F040D5B36212C223C0C0B0532_ = $db->getOne("gateway_invoice");
        $_obfuscated_0D040B2B2B1B3D400E331F040D5B36212C223C0C0B0532_ = (int) $_obfuscated_0D040B2B2B1B3D400E331F040D5B36212C223C0C0B0532_;
        if ($cliente->afip == "on" && $cliente->afip_automatico == 2 && !$_obfuscated_0D153D0706242D2608332A191018271130273432311822_ && $isMultiple) {
            $_obfuscated_0D1D13011C37115C321D0B1F181822080E040C1C0E0611_ = ["idcliente" => $factura->idcliente, "factura" => $factura->id, "mail" => 1, "idoperacion" => $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_];
            $db->insert("afiptmp", $_obfuscated_0D1D13011C37115C321D0B1F181822080E040C1C0E0611_);
            $_obfuscated_0D111A141B171F231D2840012A1A3B1E033D2731283B32_ = false;
        }
        if (0 < $cliente->invoice_electronic && $_obfuscated_0D1919231C230F0D0507070B152F0F1129233322033822_["auto"] == "2" && $this->getDDB("zona_horaria") == "America/Santiago" && !$_obfuscated_0D153D0706242D2608332A191018271130273432311822_) {
            $_obfuscated_0D33152C060A3D1C3E3D28131B5B13402939013F363622_ = ["idcliente" => $factura->idcliente, "factura" => $factura->id, "api" => "openfactura", "idoperacion" => $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_, "tipodoc" => $cliente->invoice_electronic, "mail" => 1];
            $db->insert("electronicatmp", $_obfuscated_0D33152C060A3D1C3E3D28131B5B13402939013F363622_);
            $_obfuscated_0D111A141B171F231D2840012A1A3B1E033D2731283B32_ = false;
        }
        if (isset($_obfuscated_0D040B2B2B1B3D400E331F040D5B36212C223C0C0B0532_->id) && 0 < $cliente->invoice_electronic && $_obfuscated_0D1919231C230F0D0507070B152F0F1129233322033822_["auto"] == "2" && !$_obfuscated_0D153D0706242D2608332A191018271130273432311822_) {
            $_obfuscated_0D33152C060A3D1C3E3D28131B5B13402939013F363622_ = ["idcliente" => $factura->idcliente, "factura" => $factura->id, "api" => $_obfuscated_0D040B2B2B1B3D400E331F040D5B36212C223C0C0B0532_->proveedor, "idoperacion" => $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_, "tipodoc" => $cliente->invoice_electronic, "mail" => 1];
            $db->insert("electronicatmp", $_obfuscated_0D33152C060A3D1C3E3D28131B5B13402939013F363622_);
            $_obfuscated_0D111A141B171F231D2840012A1A3B1E033D2731283B32_ = false;
        }
        if ($_obfuscated_0D111A141B171F231D2840012A1A3B1E033D2731283B32_) {
            if (!empty($_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_)) {
                $this->getFacturas($factura->idcliente, $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_, "factura");
            }
            if (!empty($_obfuscated_0D22020A2D2726340B3538241A0A142C160A1A27111622_)) {
                $this->getFacturas($factura->idcliente, $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_, "recibo");
            }
        }
        if ($this->smsalpagar) {
            $db->where("tipoaviso", "smsconfirmapago");
            $_obfuscated_0D5B2C0B3B14353C361D3105172B045C073312391B1111_ = $db->getOne("formatos");
            $html = $this->getHtml($factura->idcliente, html_entity_decode($_obfuscated_0D5B2C0B3B14353C361D3105172B045C073312391B1111_["html"]));
            $html = str_replace("{transaccion}", $data["transaccion"], $html);
            $html = str_replace("{total}", $data["cobrado"], $html);
            $html = str_replace("{nfactura}", $factura->id, $html);
            $html = str_replace("{fecha}", $data["fecha"], $html);
            $db->where("id", $factura->idcliente);
            $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_ = $db->getOne("usuarios");
            if (!empty($_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["movil"])) {
                $_obfuscated_0D33282904180F332834360B230D33270B142C2C0E2711_ = ["user" => $factura->idcliente, "enviado" => date("Y-m-d H:i:s"), "ndestino" => $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["movil"], "mensaje" => $html];
                $db->insert("smsmensajes", $_obfuscated_0D33282904180F332834360B230D33270B142C2C0E2711_);
                $this->crearLog("SMS Confirmaciónde pago Enviado a " . $_obfuscated_0D300B183F2109191D25245B262C09360A35323C013122_["movil"], $data["operador"], $factura->idcliente);
            }
        }
        $this->crearLog(str_replace("{idoperacion}", $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_, $data["log"]), $data["operador"], $factura->idcliente);
        $db->where("idcliente", $factura->idcliente);
        $db->where("limitado", 1);
        $_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_ = $db->getOne("tblservicios");
        $db->where("id", $factura->idcliente);
        $user = $db->getOne("usuarios");
        if ($factura->tipo == "2" && $user["estado"] == "SUSPENDIDO" && $activaCliente) {
            $this->Estadocliente($factura->idcliente, false);
        } else {
            if ($factura->tipo == "2" && $user["estado"] == "ACTIVO" && isset($_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_["id"]) && $activaCliente) {
                $_obfuscated_0D301715141423232A13091713090E26223918381E3722_ = $db->rawQueryOne("select count(id) as total from facturas where idcliente=? and estado=? and tipo=?", [$factura->idcliente, "No pagado", "2"]);
                if ($_obfuscated_0D301715141423232A13091713090E26223918381E3722_["total"] == 0) {
                    $this->Estadocliente($factura->idcliente);
                }
            }
        }
        if ($factura->tipo == "2" && $user["estado"] == "ACTIVO") {
            $this->Removeproxy($factura->idcliente);
        }
        return ["estado" => "exito", "salida" => "Pago registrado correctamente.", "id" => $_obfuscated_0D0314211F335C2E1F035C051E0C19265B342B0A383432_];
    }
    public function Removeproxy($idcliente)
    {
        $db = $this->mysql;
        $_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_ = $db->rawQuery("SELECT server.ip as ipserver,server.velocidad as velo,server.secret_radius,server.seguridad,tblservicios.id,tblservicios.ip,tblservicios.limitado,tblservicios.nodo,tblservicios.idperfil,tblservicios.pppuser,usuarios.nombre,usuarios.id as iduser,usuarios.estado FROM tblservicios,usuarios,server,webproxy WHERE tblservicios.idcliente=?  and webproxy.idservicio=tblservicios.id and usuarios.id=tblservicios.idcliente and server.id=tblservicios.nodo", [$idcliente]);
        $datamk = [];
        foreach ($_obfuscated_0D092B2A1B39183030362C1C23313F37322E26163E2322_ as $row) {
            $db->where("idservicio", $row["id"]);
            $db->delete("webproxy");
            $datamk[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Aviso_Servicio_" . $row["id"]]];
            $datamk[$row["nodo"]]["DELETE"][] = ["/ip/firewall/address-list/add", [".proplist" => ".id", "?comment" => "Anuncio_Servicio_" . $row["id"]]];
            $datamk[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Aviso_Servicio_" . $row["id"]]];
            $datamk[$row["nodo"]]["DELETE"][] = ["/ip/proxy/access/add", [".proplist" => ".id", "?comment" => "Anuncio_Servicio_" . $row["id"]]];
            if (2 < $row["seguridad"]) {
                $db->where("username", $row["pppuser"]);
                $db->where("attribute", "Mikrotik-Address-List");
                $db->where("value", "aviso");
                $db->delete("radreply");
            }
        }
        if (!empty($datamk)) {
            $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_ = new MKWS();
            foreach ($datamk as $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_ => $datamk) {
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->idmk = $_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_;
                $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->datamk = $datamk[$_obfuscated_0D1F352B230D361F052D4016400127271D3D3828110F01_];
                $_obfuscated_0D0F0C335C0225291D0B5C161D031C5C2C3128231A5B01_ = $_obfuscated_0D3C24140F1D1F0902282F371410283E06321903013611_->MIKROTIK();
            }
        }
    }
    private function token($data)
    {
        $db = $this->mysql;
        $_obfuscated_0D325C375C5B101E26185C190231393735235B3F393422_ = random_bytes(16);
        $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_ = new ParagonIE\HiddenString\HiddenString($data["lic"]);
        $_obfuscated_0D1F0B32071D11193C351A01315C39083112351A052311_ = new ParagonIE\HiddenString\HiddenString(base64_encode(serialize($data)));
        $key = ParagonIE\Halite\KeyFactory::deriveEncryptionKey($_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_, $_obfuscated_0D325C375C5B101E26185C190231393735235B3F393422_);
        $_obfuscated_0D12291216083240042A2115363E0C212A011A02123F32_ = ["a" => base64_encode($_obfuscated_0D325C375C5B101E26185C190231393735235B3F393422_), "b" => ParagonIE\Halite\Symmetric\Crypto::encrypt($_obfuscated_0D1F0B32071D11193C351A01315C39083112351A052311_, $key)];
        return $_obfuscated_0D12291216083240042A2115363E0C212A011A02123F32_;
    }
    public function validalicencia($newtoken = "")
    {
        $db = $this->mysql;
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"] = exec("ip route get 8.8.4.4 | head -1 | awk '{print \$7}'");
        if (!filter_var($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"], FILTER_VALIDATE_IP)) {
            $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"] = exec("ip route get 1 | awk '{print \$NF;exit}'");
        }
        if (!filter_var($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"], FILTER_VALIDATE_IP)) {
            $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"] = exec("hostname -I | cut -d' ' -f1");
        }
        if (empty($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"])) {
            $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["ip"] = "0.0.0.0";
        }
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["mac"] = exec("cat /sys/class/net/\$(ip route show default | awk '/default/ {print \$5}')/address");
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["hw"] = exec("cat /var/lib/dbus/machine-id");
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["lic"] = !empty($newtoken) ? $newtoken : $this->getDDB("passlic");
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["mail"] = $this->getDDB("userlic");
        $_obfuscated_0D033506133C310C0413053C29252A29121213322C0B32_ = $db->rawQueryOne("SELECT count(DISTINCT tblservicios.idcliente) as total from tblservicios LEFT JOIN usuarios ON tblservicios.idcliente = usuarios.id where usuarios.estado!='RETIRADO'");
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["v"] = $this->getDDB("version_sistema");
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["r"] = $this->getDDB("revision");
        $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["tu"] = $_obfuscated_0D033506133C310C0413053C29252A29121213322C0B32_["total"];
        if (!empty($newtoken)) {
            $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["renew"] = true;
        }
        $token = $this->token($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://mkws6.net/check.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["lic"]]);
        $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = curl_exec($ch);
        $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = json_decode($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_, true);
        $_obfuscated_0D09192C0C3E0C0E14160F3006192D3427182311333D11_ = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (isset($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["estado"]) && $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["estado"] == "error") {
            return $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_;
        }
        if (isset($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["a"]) && isset($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["a"])) {
            $_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_ = new ParagonIE\HiddenString\HiddenString($_obfuscated_0D1F0B2A5B3C3812023D243D2206252F35121A17363832_["lic"]);
            $key = ParagonIE\Halite\KeyFactory::deriveEncryptionKey($_obfuscated_0D062917013F2D243D3C375B3B0E2A313E0835053B2F01_, base64_decode($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["a"]));
            $_obfuscated_0D3C095C1A30301114071E07342B2606052B39291E0511_ = ParagonIE\Halite\Symmetric\Crypto::decrypt($_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["b"], $key);
            $data = unserialize(base64_decode($_obfuscated_0D3C095C1A30301114071E07342B2606052B39291E0511_->getString()));
            if (isset($data["licencia"]) && isset($data["limite"])) {
                if (!empty($newtoken)) {
                    return $data;
                }
                $data["valid"] = "exito";
                $db->where("setting", "tokenlic");
                $db->update("tblconfiguration", ["value" => $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["a"] . "::" . $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_["b"]]);
                $db->rawQueryOne("UPDATE tblconfiguration SET value=? Where setting='clientes_sistema'", [$data["limite"]]);
                $db->rawQueryOne("UPDATE tblconfiguration SET value='yes' Where setting='internet'");
                $db->rawQueryOne("UPDATE tblconfiguration SET value=? Where setting='userlic'", [$data["correo"]]);
                $_obfuscated_0D09031B2A305C07052B182B3C32353C3415262D303711_ = $this->getDDB("userlic");
                $_obfuscated_0D2D3B0937020421181A1428080A311631103F34171132_ = $this->getDDB("clientes_sistema");
                if (empty($_obfuscated_0D2D3B0937020421181A1428080A311631103F34171132_)) {
                    return ["estado" => "error", "data" => "Licencia incorrecta -  Límite"];
                }
                if ($_obfuscated_0D2D3B0937020421181A1428080A311631103F34171132_ < $_obfuscated_0D033506133C310C0413053C29252A29121213322C0B32_["total"]) {
                    $update = $db->update("tblavisouser", ["isaviable" => 0]);
                    $_obfuscated_0D2E07240D041622173F1A28042F07103D150B10303001_ = $db->rawQuery("SELECT tblservicios.idcliente,usuarios.id from tblservicios LEFT JOIN usuarios ON tblservicios.idcliente = usuarios.id where usuarios.estado!='RETIRADO' group by usuarios.id order by usuarios.id asc");
                    $_obfuscated_0D09242D2B0E0E332E0D0A1C050D172D2C21223D112422_ = 0;
                    foreach ($_obfuscated_0D2E07240D041622173F1A28042F07103D150B10303001_ as $_obfuscated_0D3B3C3617180C112D2A3035352D18130E330C37302711_) {
                        $_obfuscated_0D09242D2B0E0E332E0D0A1C050D172D2C21223D112422_++;
                        if ($_obfuscated_0D2D3B0937020421181A1428080A311631103F34171132_ < $_obfuscated_0D09242D2B0E0E332E0D0A1C050D172D2C21223D112422_) {
                            $db->where("cliente", $_obfuscated_0D3B3C3617180C112D2A3035352D18130E330C37302711_["id"]);
                            $update = $db->update("tblavisouser", ["isaviable" => 1]);
                        }
                    }
                } else {
                    if ($_obfuscated_0D033506133C310C0413053C29252A29121213322C0B32_["total"] <= $_obfuscated_0D2D3B0937020421181A1428080A311631103F34171132_) {
                        $update = $db->update("tblavisouser", ["isaviable" => 0]);
                    }
                }
                return $data;
            } else {
                return ["estado" => "error", "data" => "Licencia incorrecta - No data"];
            }
        } else {
            return ["estado" => "error", "data" => "Licencia incorrecta - general"];
        }
    }
    public function barraroela($idfactura)
    {
    }
    public function FacturaPDF($id, $action = 0)
    {
        $db = $this->mysql;
        $moneda = $this->moneda;
        error_reporting(0);
        ini_set("memory_limit", "4G");
        ini_set("max_execution_time", 0);
        set_time_limit(0);
        date_default_timezone_set($this->Zonahoraria);
        $db->where("tipoaviso", "factura");
        $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_ = $db->getOne("formatos");
        $db->where("tipo", "Siro");
        $db->where("estado", "on");
        $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_ = $db->getOne("pasarela");
        $_obfuscated_0D1B102D061B190F1D3F0622402C1D36355B1C31372F11_ = $db->get("tblconfiguration");
        foreach ($_obfuscated_0D1B102D061B190F1D3F0622402C1D36355B1C31372F11_ as $data) {
            $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_[$data["setting"]] = $data["value"];
        }
        $db->where("id", $id);
        $factura = $db->getOne("facturas");
        $db->where("cliente", $factura["idcliente"]);
        $_obfuscated_0D030838111E171A131D150333373D330A3039362F1311_ = $db->getOne("tblavisouser");
        $_obfuscated_0D1919030309270237260D1533103D03330E275C361322_ = ["", "1 (Bajo -  Bajo)", "2 (Bajo)", "3 (Medio Bajo)", "4 (Medio)", "5 (Medio Alto)", "6 (Alto)"];
        if (!isset($factura["id"])) {
            return false;
        }
        $db->where("id", $factura["idcliente"]);
        $cliente = $db->getOne("usuarios");
        $html = html_entity_decode($_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["html"]);
        $html = preg_replace("/\\s+/", " ", $html);
        $html = str_replace("\r\n", "", $html);
        $html = str_replace("\r", "", $html);
        $html = str_replace("\n", "", $html);
        preg_match("/<title>(.+)<\\/title>/", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
        $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[1], "Factura Nº " . $id, $html);
        $html = str_replace("{nfactura}", $this->idgetFacturas($factura["id"], 8), $html);
        $html = str_replace("{nlegal}", $factura["legal"], $html);
        $html = str_replace("{vencimiento}", date("d/m/Y", strtotime($factura["vencimiento"])), $html);
        $html = str_replace("{emitido}", date("d/m/Y", strtotime($factura["emitido"])), $html);
        $html = str_replace("{estrato_cliente}", $_obfuscated_0D1919030309270237260D1533103D03330E275C361322_[$_obfuscated_0D030838111E171A131D150333373D330A3039362F1311_["tipo_estrato"]], $html);
        $html = str_replace("{nombre_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["nombre_empresa"], $html);
        $html = str_replace("{ruc_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["ruc_empresa"], $html);
        $html = str_replace("{direccion_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["direccion_empresa"], $html);
        $html = str_replace("{telefono_empresa}", $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["telefono_empresa"], $html);
        $_obfuscated_0D36260F073B2A282813240215102716182C3B36350601_ = ["No pagado" => "nopagado", "pagado" => "pagado", "anulado" => "anulado"];
        $html = str_replace("{estado}", $factura["estado"], $html);
        $html = str_replace("class=\"estadocss\"", "class=\"" . $_obfuscated_0D36260F073B2A282813240215102716182C3B36350601_[$factura["estado"]] . "\"", $html);
        $db->where("idfactura", $id);
        $_obfuscated_0D400508313517111824291A2901072D302C10051E2432_ = $db->get("facturaitems");
        $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ = "";
        $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ = "";
        $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_ = "";
        $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_ = 0;
        foreach ($_obfuscated_0D400508313517111824291A2901072D302C10051E2432_ as $row) {
            $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_ += $row["impuesto911"];
            $_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_ = $this->getImpuesto($row["cantidad"] * $row["unidades"], $factura["impuesto"], $row["impuesto"]);
            $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_ .= "\n        <tr>\n        <td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n        <td valign=\"top\">" . $this->parse($row["cantidad"], $moneda) . "</td>\n        <td align=\"center\" valign=\"top\">" . $row["impuesto"] . "</td>\n        <td align=\"center\" valign=\"top\">" . $row["unidades"] . "</td>\n        <td valign=\"top\">" . $this->parse(round($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], 2), $moneda) . "</td>\n        </tr>\n        ";
            $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_ .= "\n        <tr>\n        <td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n        <td valign=\"top\">" . $this->parse(round($_obfuscated_0D25163C3C1D172A10310B1027333F0F23232B020F3311_["total"], 2), $moneda) . "</td>\n        </tr>\n        ";
            $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_ .= "\n        <tr>\n        <td class=\"pad\">" . str_replace("\n", "<br>", $row["descripcion"]) . "</td>\n        </tr>\n        ";
        }
        if ($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_ == 0) {
            preg_match("#<tr\\sclass=\"otrosimpuestos\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        $html = str_replace("<tr> <td>{items}</td> </tr>", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
        $html = str_replace("<tr><td>{items}</td></tr>", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
        $html = str_replace("<tr>\n        <td>{items}</td>\n        </tr>", $_obfuscated_0D32313516292A2D075B0F2E11262F252C2F171F0B4001_, $html);
        $html = str_replace("<tr> <td>{items2}</td> </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
        $html = str_replace("<tr><td>{items2}</td></tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
        $html = str_replace("<tr>\n        <td>{items2}</td>\n        </tr>", $_obfuscated_0D111D0E0B2D3D265B1C08051D303E361908362C112F01_, $html);
        $html = str_replace("<tr> <td>{items3}</td> </tr>", $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_, $html);
        $html = str_replace("<tr><td>{items3}</td></tr>", $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_, $html);
        $html = str_replace("<tr>\n        <td>{items3}</td>\n        </tr>", $_obfuscated_0D3C3D015B0711190B12271B1A05391536141B24380611_, $html);
        $db->where("iduser", $factura["idcliente"]);
        $db->where("estado", "facturado");
        $db->where("iddestino", $factura["id"]);
        $_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ = $db->get("saldos");
        $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = 0;
        foreach ($_obfuscated_0D132D231830150E26033C401B5B1B3F3217062E1E2B11_ as $_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_) {
            if ($_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_["monto"] < 0) {
                $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ - $_obfuscated_0D19084018160E163D28352E5B1F2523390D251E101911_["monto"];
            }
        }
        $db->where("iduser", $factura["idcliente"]);
        $db->where("idorigen", $factura["id"]);
        $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_ = $db->getOne("saldos");
        if (0 < $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ && $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_["monto"] < 0) {
            $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ + $_obfuscated_0D1E401C0902155B0B3F121F071B052415171F230E0522_["monto"];
        }
        if (0 < $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_) {
            $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_ = "-" . $_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_;
        }
        $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_ = 0;
        $_obfuscated_0D0325063E2603332A0E031B140D1F341B5B3D142D1D32_ = explode(";", $factura["otros_impuestos"]);
        foreach ($_obfuscated_0D0325063E2603332A0E031B140D1F341B5B3D142D1D32_ as $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_) {
            $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_++;
            $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_ = [];
            if (0 < $_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_) {
                ${"conterimps" . $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_} = 1;
                $html = str_replace("{otro_impuesto_" . $_obfuscated_0D331639365C3D10290B281C2C1E12303E1140130E1622_ . "}", $this->parse($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_, $moneda), $html);
            }
        }
        if (empty($_obfuscated_0D383F0D11331C010C1A0A0E1E02331C1D31350C291C01_)) {
            preg_match("#<tr\\sclass=\"otrosimpuestos1\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        if (empty($_obfuscated_0D222F2B101A042A1C070F3B2F170D090B5C23222B0F11_)) {
            preg_match("#<tr\\sclass=\"otrosimpuestos2\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        if (empty($_obfuscated_0D2618061509320A13190F0F04291A21050F25111C1211_)) {
            preg_match("#<tr\\sclass=\"otrosimpuestos3\">(.*?)</tr>#is", $html, $_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_);
            $html = str_replace($_obfuscated_0D283D172727301C1C26291A5C1E10400B0F1D152D2401_[0], "", $html);
        }
        $html = str_replace("{subtotal}", $this->parse($factura["sub_total"], $moneda), $html);
        $html = str_replace("{impuesto}", $this->parse($factura["iva_igv"], $moneda), $html);
        $html = str_replace("{otro_impuesto}", $this->parse($_obfuscated_0D142F0725303B403F1D0F2D2E1F07021A221923260C32_, $moneda), $html);
        $html = str_replace("{saldo}", $this->parse(0, $moneda), $html);
        $html = str_replace("{total}", $this->parse($factura["total"], $moneda), $html);
        $html = str_replace("{percepcion}", $this->parse($factura["percepcion_afip"], $moneda), $html);
        $html = str_replace("{descuento}", $this->parse($_obfuscated_0D101A01011F16110F223C110D01350C32061B273F2901_, $moneda), $html);
        $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_ = NumeroALetras::convertir($factura["total"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_letra"], $_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["moneda_unidad"]);
        $html = str_replace("{total_letras}", $_obfuscated_0D2C08372621112D045C3C0232060F07380F2824310611_, $html);
        $html = str_replace("src=\"../images/", "src=\"" . root . "/admin/images/", $html);
        $html = str_replace("src=\"images/", "src=\"" . root . "/admin/images/", $html);
        if (!empty($_obfuscated_0D1A3B283B3B393F1407172E02243C342329063F0F5C11_["pdf_generado"])) {
            $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_ = preg_split("/(<body.*?>)/i", $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $html = $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_[0] . $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_[1] . "<div style=\"position: absolute;right: 0;bottom: 0;left: 0;padding: 5px;text-align: center;\"><small>PDF Generado " . date("d/m/Y h:i a") . "</small></div>" . $_obfuscated_0D071D302F193D1D1E12363F4006115C27062D072B1F22_[2];
        }
        if (!empty($cliente["pasarela"]) && empty($factura["oxxo_referencia"])) {
            $html = str_replace("{barcode}", $cliente["pasarela"], $html);
        } else {
            if (!empty($factura["oxxo_referencia"])) {
                $html = str_replace("{barcode}", $factura["oxxo_referencia"], $html);
            } else {
                if (!empty($factura["barcode_cobro_digital"])) {
                    $html = str_replace("{barcode}", $factura["barcode_cobro_digital"], $html);
                } else {
                    $html = str_replace("{barcode}", $this->idgetFacturas($id, 8), $html);
                }
            }
        }
        $html = str_replace("{barcode_oxxo}", $factura["oxxo_referencia"], $html);
        if (!empty($factura["barcode_cobro_digital"])) {
            $html = str_replace("{barcode_cobro_digital}", "<img src=\"https://www.cobrodigital.com/wse/bccd/" . $factura["barcode_cobro_digital"] . "HL.png\">", $html);
        } else {
            $html = str_replace("{barcode_cobro_digital}", "", $html);
        }
        if (0 < $factura["siro"]) {
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = $this->idgetFacturas($factura["idcliente"], 9) . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["pais"];
            $html = str_replace("{siro}", $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_, $html);
        } else {
            if (!empty($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["id"])) {
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = $this->idgetFacturas($factura["idcliente"], 9) . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["pais"];
                $html = str_replace("{siro}", $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_, $html);
            } else {
                $html = str_replace("{siro}", "", $html);
            }
        }
        if (!empty($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["id"])) {
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = "0447";
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= "0";
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($factura["id"], 8);
            if ($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["sandbox"] == "boton") {
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($factura["vencimiento"] . " + " . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"] . " days"));
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $factura["total"]), 7);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
            } else {
                if ($_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["comision"] == "1") {
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($factura["vencimiento"] . " + " . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"] . " days"));
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $factura["total"]), 7);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                } else {
                    if ($factura["tipo"] == "2") {
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($factura["vencimiento"]) . " + " . $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"] . " days");
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $factura["total"]), 7);
                        $_obfuscated_0D15330703123F24271B30263626280508351821152F11_ = $this->getDDB("mora_cliente");
                        $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_ = $this->getDDB("reconexion_cliente");
                        $_obfuscated_0D390B250A5C132D3136233D360E0B362F09272D0F2F01_ = $this->getDDB("tipo_reconexion");
                        $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_ = explode("%", $_obfuscated_0D15330703123F24271B30263626280508351821152F11_);
                        if (0 < $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_[0]) {
                            $pos = strpos($_obfuscated_0D15330703123F24271B30263626280508351821152F11_, "%");
                            if ($pos === false) {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D15330703123F24271B30263626280508351821152F11_;
                            } else {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $factura["total"] * $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_[0] / 100;
                            }
                            $_obfuscated_0D3734333605122A2B025C1913113923212E3B01321522_ = $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_;
                            $db->where("cliente", $factura["idcliente"]);
                            $cliente = $db->getOne("tblavisouser");
                            $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_ = number_format($factura["total"] + $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_, 2, ".", "");
                            if ($cliente["corteautomatico"] == "0") {
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(1, 2);
                            } else {
                                $cliente["corteautomatico"] = $cliente["corteautomatico"] - 1;
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($cliente["corteautomatico"], 2);
                            }
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_), 7);
                        } else {
                            if (0 < $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_ && $_obfuscated_0D390B250A5C132D3136233D360E0B362F09272D0F2F01_ == "1") {
                                $db->where("cliente", $factura["idcliente"]);
                                $cliente = $db->getOne("tblavisouser");
                                $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_ = explode("%", $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_);
                                $pos = strpos($_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_, "%");
                                if ($pos === false) {
                                    $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_;
                                } else {
                                    $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $factura["total"] * $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_[0] / 100;
                                }
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ + $_obfuscated_0D3734333605122A2B025C1913113923212E3B01321522_;
                                $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_ = number_format($factura["total"] + $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_, 2, ".", "");
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($cliente["corteautomatico"] + $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"], 2);
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_), 7);
                            } else {
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                                $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                            }
                        }
                        if (0 < $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_ && $_obfuscated_0D390B250A5C132D3136233D360E0B362F09272D0F2F01_ == "1") {
                            $db->where("cliente", $_obfuscated_0D361E3C2D0201360C291C02103D052B212D1033223211_["idcliente"]);
                            $cliente = $db->getOne("tblavisouser");
                            $_obfuscated_0D010C0F2F32190F3C1F1327103F2F3E14251923073711_ = explode("%", $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_);
                            $pos = strpos($_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_, "%");
                            if ($pos === false) {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_;
                            } else {
                                $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $factura["total"] * $_obfuscated_0D2A181E1D321416213E5C0413353E132B173F375C3C22_[0] / 100;
                            }
                            $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ = $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_ + $_obfuscated_0D3734333605122A2B025C1913113923212E3B01321522_;
                            $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_ = number_format($factura["total"] + $_obfuscated_0D28072540040F1B210A071114051030173B0F06270732_, 2, ".", "");
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas($cliente["corteautomatico"] + $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["dias"], 2);
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $_obfuscated_0D5B25130C0206112F331B1C020E2606361628213B3832_), 7);
                        } else {
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                        }
                    } else {
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= date("ymd", strtotime($factura["vencimiento"]));
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(str_replace(".", "", $factura["total"]), 7);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 2);
                        $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $this->idgetFacturas(0, 7);
                    }
                }
            }
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $_obfuscated_0D09241B2201250D381D0C1E3C17130412130C2D0D1D22_["pais"];
            $_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_ = [1, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3, 5, 7, 9, 3];
            $string = str_split($_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_);
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = 0;
            foreach ($string as $k => $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_) {
                $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ += $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_ * $_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_[$k];
            }
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = explode(".", $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ / 2);
            $_obfuscated_0D101429312C1B3833352D301D2C332C1F161629101022_ = fmod($_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_[0], 10);
            array_push($_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_, 5);
            array_push($string, $_obfuscated_0D101429312C1B3833352D301D2C332C1F161629101022_);
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = 0;
            foreach ($string as $k => $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_) {
                $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ += $_obfuscated_0D2B27101137060A5C2322151F1F253205403826281411_ * $_obfuscated_0D3E380D131B163C0E010322021A021505220704243B22_[$k];
            }
            $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ = explode(".", $_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_ / 2);
            $_obfuscated_0D281F04082C215C2C111C400909030E2D2E3939362B22_ = fmod($_obfuscated_0D31183F211837361B15182F115B0E2B1B2717051D2B11_[0], 10);
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $_obfuscated_0D101429312C1B3833352D301D2C332C1F161629101022_;
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ .= $_obfuscated_0D281F04082C215C2C111C400909030E2D2E3939362B22_;
            if ($factura["vencimiento"] < date("Y-m-d")) {
                $db->where("id", $factura["id"]);
                $_obfuscated_0D3734132E252611091C1B0916360A061C2C162C2D2B32_ = $db->getOne("facturas");
                if (!empty($_obfuscated_0D3734132E252611091C1B0916360A061C2C162C2D2B32_["barcode_siro"])) {
                    $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = $_obfuscated_0D3734132E252611091C1B0916360A061C2C162C2D2B32_["barcode_siro"];
                }
            } else {
                $db->where("id", $factura["id"]);
                $db->update("facturas", ["barcode_siro" => $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_]);
            }
            $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ = "\n        <div style=\"text-align: center;width: 400px;\">\n       <barcode code=\"" . $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ . "\" height=\"1.7\" size=\"0.55\" type=\"I25\"></barcode><br>\n       <div style=\"font-family: ocrb;font-size:9px;text-align: center\">" . $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_ . "</div><br>\n       <div>Abonar en: Rapipago, Pago Fácil, Cobro Expres y Provincia Pagos.</div>\n       <div>Entidad Recaudadora: BANCO ROELA a través de <img width=\"55px\" src=\"" . root . "/admin/images/siro.png\"></div>\n        </div>\n        ";
            $html = str_replace("{barcode_siro}", $_obfuscated_0D12322A26083E22090D2D2B1C1A5C0803292B1F044032_, $html);
        } else {
            $html = str_replace("{barcode_siro}", "", $html);
        }
        if ($factura["estado"] == "pagado") {
            $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = "<style type=\"text/css\">\n                .trans{\n            font-size: 85%;\n            table-layout: fixed;\n            border-collapse: collapse;\n            width:100%;\n                }\n                \n            .trans td {\n            padding:7px 4px;\n            border: 1px solid #d9e8ed;\n            }\n\n            .trans th {\n                font-weight:bold;\n                height:30px;\n                border: 1px solid #d9e8ed;\n                vertical-align:middle;\n            }\n                \n            </style>\n\n            <div style=\"display: inline-block;margin: 20px 10px;\">\n            <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"trans\">\n            <tr>\n            <td colspan=\"4\" align=\"center\"><h3>Transacciones</h3></td>\n            </tr>\n            <tbody>\n                <tr>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Fecha</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Forma pago</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Nº transacción</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Total</th>\n                </tr>\n                {itemop}\n                <tr>\n                <td colspan=\"3\" align=\"right\" bgcolor=\"#EEF2F3\"><b>Balance</b></td>\n                <td bgcolor=\"#EEF2F3\" align=\"center\">{balance}</td>\n                </tr>\n            </tbody>\n            </table>\n            </div>";
            $db->where("nfactura", $id);
            $_obfuscated_0D2D312C093F1D1F1904322E1D30021F041313120A3932_ = $db->get("operaciones");
            $_obfuscated_0D2D121A180803020F050C1C25323232043233273E4022_ = "";
            $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_ = 0;
            foreach ($_obfuscated_0D2D312C093F1D1F1904322E1D30021F041313120A3932_ as $op) {
                $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_ += $op["cobrado"];
                if ($op["forma_pago"] == "PagueloFacil") {
                    $op["forma_pago"] = "PagueloFacil - " . $op["descripcion"];
                }
                $_obfuscated_0D2D121A180803020F050C1C25323232043233273E4022_ .= "<tr>\n                <td align=\"center\">" . date("d/m/Y h:i:s A", strtotime($op["fecha_pago"])) . "</td>\n                <td align=\"center\">" . $op["forma_pago"] . "</td>\n                <td align=\"center\">" . $op["transaccion"] . "</td>\n                <td align=\"center\">" . $this->parse($op["cobrado"], $moneda) . "</td>\n                </tr>";
            }
            $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = str_replace("{itemop}", $_obfuscated_0D2D121A180803020F050C1C25323232043233273E4022_, $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_);
            if (strpos($op["forma_pago"], "Payu") !== false) {
                $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = str_replace("{balance}", $this->parse(0, $moneda), $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_);
            } else {
                $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = str_replace("{balance}", $this->parse($factura["total"] - $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_, $moneda), $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_);
            }
            if (0 < $_obfuscated_0D1814212D302E3B393908133C04300D0D5C323B103C22_) {
                $html = str_replace("{operaciones}", $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_, $html);
            } else {
                $html = str_replace("{operaciones}", "", $html);
            }
        } else {
            $html = str_replace("{operaciones}", "", $html);
        }
        $_obfuscated_0D362D5C381F36072239392B3315091C0D130E33211501_ = "";
        $_obfuscated_0D261F2D31033724353218221F5C1C36293631151F3901_ = "";
        $_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_ = 0;
        $_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ = $db->rawQuery("Select * from facturas where estado=? and idcliente=? order by vencimiento desc ", ["No pagado", $factura["idcliente"]]);
        foreach ($_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ as $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_) {
            $_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_ += $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["total"];
            $action .= "<tr>\n                <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["id"], 8) . "</td>\n                <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["emitido"]) . "</td>\n                <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["vencimiento"]) . "</td>\n                <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["total"], $moneda) . "</td>\n                <td align=\"center\">Pendiente de Pago</td>\n                </tr>";
            $_obfuscated_0D24103D082F110A1E3410330327212802390E14300222_ .= "<tr>\n                <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["id"], 8) . "</td>\n                <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["emitido"]) . "</td>\n                <td align=\"center\">" . $this->getMes(date("m", strtotime($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["vencimiento"]))) . "</td>\n                <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["total"], $moneda) . "</td>\n                <td align=\"center\">Pendiente de Pago</td>\n                </tr>";
        }
        $_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ = $db->rawQuery("Select * from saldos where estado=? and iduser=? and monto > 0 order by fecha desc ", ["no cobrado", $factura["idcliente"]]);
        foreach ($_obfuscated_0D2232083409183801312D25042B253F373D252C361F22_ as $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_) {
            $_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_ += $_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["monto"];
            $action .= "<tr>\n            <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n            <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["fecha"]) . "</td>\n            <td align=\"center\">---</td>\n            <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["monto"], $moneda) . "</td>\n            <td align=\"center\">Saldo Anterior #" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n            </tr>";
            $_obfuscated_0D24103D082F110A1E3410330327212802390E14300222_ .= "<tr>\n            <td align=\"center\">" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n            <td align=\"center\">" . $this->getFechaPagado($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["fecha"]) . "</td>\n            <td align=\"center\">---</td>\n            <td align=\"center\">" . $this->parse($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["monto"], $moneda) . "</td>\n            <td align=\"center\">Saldo Anterior #" . $this->idgetFacturas($_obfuscated_0D233D280B271B10373C3C1029051A2E260C1C1B303D22_["idorigen"], 8) . "</td>\n            </tr>";
        }
        if (!empty($action)) {
            $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_ = "<style type=\"text/css\">\n                .trans2{\n            font-size: 85%;\n            table-layout: fixed;\n            border-collapse: collapse;\n            width:100%;\n                }\n                \n            .trans2 td {\n            padding:7px 4px;\n            border: 1px solid #d9e8ed;\n            }\n\n            .trans2 th {\n                font-weight:bold;\n                height:30px;\n                border: 1px solid #d9e8ed;\n                vertical-align:middle;\n            }\n                \n            </style>\n\n            <div style=\"display: inline-block;margin: 20px 10px;\">\n            <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"trans2\">\n            <tr>\n            <td colspan=\"5\" align=\"center\"><h3>RESUMEN DE DEUDA</h3></td>\n            </tr>\n            <tbody>\n                <tr>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Nº Comprobante</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Emitido</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Vencimiento</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">TOTAL</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Detalle</th>\n                </tr>\n            " . $action . "\n            <tr>\n                <td colspan=\"4\" align=\"right\" bgcolor=\"#EEF2F3\"><b>DEUDA TOTAL</b></td>\n                <td bgcolor=\"#EEF2F3\" align=\"center\"><b>" . $this->parse($_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_, $moneda) . "</b></td>\n                </tr>\n            </tbody>\n            </table>\n            </div>";
            $_obfuscated_0D10331839212E1C0B0F0B285B2A5B3B21282201313201_ = "<style type=\"text/css\">\n                .trans2{\n            font-size: 85%;\n            table-layout: fixed;\n            border-collapse: collapse;\n            width:100%;\n                }\n                \n            .trans2 td {\n            padding:7px 4px;\n            border: 1px solid #d9e8ed;\n            }\n\n            .trans2 th {\n                font-weight:bold;\n                height:30px;\n                border: 1px solid #d9e8ed;\n                vertical-align:middle;\n            }\n                \n            </style>\n\n            <div style=\"display: inline-block;margin: 20px 10px;\">\n            <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"trans2\">\n            <tr>\n            <td colspan=\"5\" align=\"center\"><h3>RESUMEN DE DEUDA</h3></td>\n            </tr>\n            <tbody>\n                <tr>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Nº Comprobante</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Emitido</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Mes</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">TOTAL</th>\n                <th bgcolor=\"#EEF2F3\" scope=\"col\">Detalle</th>\n                </tr>\n            " . $_obfuscated_0D24103D082F110A1E3410330327212802390E14300222_ . "\n            <tr>\n                <td colspan=\"4\" align=\"right\" bgcolor=\"#EEF2F3\"><b>DEUDA TOTAL</b></td>\n                <td bgcolor=\"#EEF2F3\" align=\"center\"><b>" . $this->parse($_obfuscated_0D1F1404403906340A2A1502195C3F1206353811362132_, $moneda) . "</b></td>\n                </tr>\n            </tbody>\n            </table>\n            </div>";
            $html = str_replace("{resumen_deuda2}", $_obfuscated_0D10331839212E1C0B0F0B285B2A5B3B21282201313201_, $html);
            $html = str_replace("{resumen_deuda}", $_obfuscated_0D5C2638290D223C1D1D121C402B321F32401B08182A22_, $html);
        } else {
            $html = str_replace("{resumen_deuda2}", "", $html);
            $html = str_replace("{resumen_deuda}", "", $html);
        }
        $html = $this->getHtml($factura["idcliente"], $html);
        $db->where("cliente", $factura["idcliente"]);
        $cliente = $db->getOne("tblavisouser");
        if (0 < $cliente["mensaje_comprobante"]) {
            $db->where("id", $cliente["mensaje_comprobante"]);
            $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_ = $db->getOne("notificaciones_factura");
            $html = str_replace("<p>{mensaje_personalizado}</p>", "<div>" . $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_["mensaje"] . "</div>", $html);
            $html = str_replace("{mensaje_personalizado}", $_obfuscated_0D31020829030621025B1A262A062E282B1F1803400D01_["mensaje"], $html);
        } else {
            $html = str_replace("{mensaje_personalizado}", "", $html);
        }
        if (!file_exists("/var/www/html/admin/tmpMPDF")) {
            mkdir("/var/www/html/admin/tmpMPDF", 511);
        }
        $_obfuscated_0D2202111A271028061626033E32212D34372B5C381701_ = ["format" => $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] == "0" ? [(int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["ancho"], (int) $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["alto"]] : $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["tamano"] . $_obfuscated_0D0232340F1B332419302230321F23152C1A3830090211_["posicion"], "mode" => "utf-8", "tempDir" => "/var/www/html/admin/tmpMPDF", "margin_left" => 5, "margin_right" => 5, "margin_top" => 8, "margin_bottom" => 5, "margin_header" => 1, "margin_footer" => 1];
        ob_clean();
        ob_get_clean();
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_ = new Mpdf\Mpdf($_obfuscated_0D2202111A271028061626033E32212D34372B5C381701_);
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->useSubstitutions = false;
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->simpleTables = true;
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->packTableData = true;
        $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
        switch ($action) {
            case 0:
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
                if ($this->isMobileDevice()) {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output();
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "I");
                }
                break;
            case 1:
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
                if ($this->isMobileDevice()) {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "D");
                } else {
                    $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "I");
                }
                break;
            case 2:
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->WriteHTML($html, 0);
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->SetTitle("Comprobante Nº " . $this->idgetFacturas($id, 8));
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output(root . "/admin/ajax/factura/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "F");
                chown(root . "/admin/ajax/factura/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "www-data");
                chgrp(root . "/admin/ajax/factura/Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "www-data");
                break;
            case 3:
                $_obfuscated_0D0D1C3D3B3C09210C293C210A3E40352D1C10055C3901_->Output("Doc-" . $this->idgetFacturas($id, 8) . ".pdf", "D");
                break;
        }
    }
    public function isMobileDevice()
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo\n            |fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\\.browser|up\\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    public function parse($str, $currency = "USD")
    {
        $currency = is_string($currency) ? new Currency($currency) : $currency;
        $str = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $str);
        $str = preg_replace("/^[^0-9]*/", "", $str);
        if (strlen($currency->getThousandSeparator())) {
            $_obfuscated_0D3B242B373C3D180F363C233C2E163630291814143722_ = $currency->getThousandSeparator();
            $str = str_replace($currency->getThousandSeparator(), ".", $str);
        }
        if (strlen($currency->getDecimalSeparator())) {
            $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ = preg_quote($currency->getDecimalSeparator());
            $str = preg_replace("/[^" . $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ . "\\d]/", ".", $str);
            $str = preg_replace("/" . $_obfuscated_0D1B082D023B3423182A151A0705240224311337220411_ . "/", ".", $str);
        } else {
            $str = preg_replace("/[^\\d]/", "", $str);
        }
        return new Money($str, $currency);
    }
    public function EncryptFunction($action, $string)
    {
        $_obfuscated_0D5B163316162F3F1D2D331C0C17092906151A08181A32_ = false;
        if (empty($string)) {
            return "";
        }
        $_obfuscated_0D5B36402C1D112E02163D33060B0A1930310610103611_ = "AES-256-CBC";
        $_obfuscated_0D301E36333B212923062F0117141930270A301B0B3101_ = "@mikrowisp5#123?";
        $_obfuscated_0D103E0D09401135220C23310E040C222C2D3D2A393101_ = "#123?#mikrowisp5@";
        $key = hash("sha256", $_obfuscated_0D301E36333B212923062F0117141930270A301B0B3101_);
        $_obfuscated_0D1D073E011A3B132C18041B2B31153E211A3E0D2E2222_ = substr(hash("sha256", $_obfuscated_0D103E0D09401135220C23310E040C222C2D3D2A393101_), 0, 16);
        if ($action == "encode") {
            $_obfuscated_0D5B163316162F3F1D2D331C0C17092906151A08181A32_ = openssl_encrypt($string, $_obfuscated_0D5B36402C1D112E02163D33060B0A1930310610103611_, $key, 0, $_obfuscated_0D1D073E011A3B132C18041B2B31153E211A3E0D2E2222_);
            $_obfuscated_0D5B163316162F3F1D2D331C0C17092906151A08181A32_ = base64_encode($_obfuscated_0D5B163316162F3F1D2D331C0C17092906151A08181A32_);
        } else {
            if ($action == "decode") {
                $_obfuscated_0D5B163316162F3F1D2D331C0C17092906151A08181A32_ = openssl_decrypt(base64_decode($string), $_obfuscated_0D5B36402C1D112E02163D33060B0A1930310610103611_, $key, 0, $_obfuscated_0D1D073E011A3B132C18041B2B31153E211A3E0D2E2222_);
            }
        }
        return $_obfuscated_0D5B163316162F3F1D2D331C0C17092906151A08181A32_;
    }
}

?>