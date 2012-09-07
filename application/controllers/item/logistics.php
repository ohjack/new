<?php

class Item_Logistics_Controller extends Base_Controller {

    public $restful = true;

    public function post_index() {

        $action = Input::get('action');

        if($action == 'allOrder') {
            Logistics::allHandle();
        } else if($action == 'allOther') {
            Logistics::allOther();
        } else if($action == 'listOrder') {
            Logistics::listToOther( Input::get('ids') );
        }

        return Response::json('ok');
    }

    public function get_index() {

        $system = Input::get('system');

        $systems = [
            'coolsystem',
            'birdsystem',
            'other'
            ];

        if(in_array($system, $systems)) {
            Logistics::getCSV($system);
        }else {
            return View::make('item.logistics.download');
        }
    }

    public function put_index() {
    
        $maps = [
        ['original_sku' => '32-YRX3-CYU9', 'target_sku' => '60-BZDS', 'logistics' => 'coolsystem'],
        ['original_sku' => 'UX-4LQI-IGJN', 'target_sku' => '60-YTMMJ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'O1-I13F-DSY6', 'target_sku' => '60-BZDS', 'logistics' => 'coolsystem'],
        ['original_sku' => 'FR-12QL-MMAF', 'target_sku' => '60-SBLZD', 'logistics' => 'coolsystem'],
        ['original_sku' => 'IP-AX82-YWDU', 'target_sku' => '60-SBLLT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'EH-P7SL-JNHG', 'target_sku' => '60-HSZ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'I4-2HYB-P9JJ', 'target_sku' => '60-YFFS', 'logistics' => 'coolsystem'],
        ['original_sku' => '5I-IF5X-Z8WQ', 'target_sku' => '60-YTMQEX', 'logistics' => 'coolsystem'],
        ['original_sku' => '1S-83TN-A3AR', 'target_sku' => '60-SZSN', 'logistics' => 'coolsystem'],
        ['original_sku' => 'O4-RH9S-XFO8', 'target_sku' => '60-ZZKB', 'logistics' => 'coolsystem'],
        ['original_sku' => 'T2-87JK-5W2B', 'target_sku' => '60-HHT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'IP-VXHX-YWEP', 'target_sku' => '60-YTMHM', 'logistics' => 'coolsystem'],
        ['original_sku' => 'EH-USGI-JOQL', 'target_sku' => '60-YTMC4', 'logistics' => 'coolsystem'],
        ['original_sku' => '83-80XN-5381', 'target_sku' => '60-BZDH', 'logistics' => 'coolsystem'],
        ['original_sku' => 'CP-GJ8T-QQBA', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => '86-C61N-OQ2D', 'target_sku' => '60-YFFS', 'logistics' => 'coolsystem'],
        ['original_sku' => 'NG-976M-45C8', 'target_sku' => '60-SBLDH', 'logistics' => 'coolsystem'],
        ['original_sku' => '4N-7YMP-2RG4', 'target_sku' => '60-BMMLT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'IX-QH1U-249E', 'target_sku' => '60-SZFC', 'logistics' => 'coolsystem'],
        ['original_sku' => 'GG-3RGS-FVU7', 'target_sku' => '60-HTMTZ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'SD-STVD-CLOS', 'target_sku' => '60-SZSXI9100', 'logistics' => 'coolsystem'],
        ['original_sku' => 'O1-Z7PB-DSYQ', 'target_sku' => '60-SZQST', 'logistics' => 'coolsystem'],
        ['original_sku' => 'RK-F1I9-ZQO8', 'target_sku' => '60-HSZBTW', 'logistics' => 'coolsystem'],
        ['original_sku' => 'CI-LTDU-NHXI', 'target_sku' => '60-HSZ', 'logistics' => 'coolsystem'],
        ['original_sku' => '9K-SCS9-B8B0', 'target_sku' => '60-SZXM', 'logistics' => 'coolsystem'],
        ['original_sku' => 'T9-KV1H-93S3', 'target_sku' => '60-SZYDAR', 'logistics' => 'coolsystem'],
        ['original_sku' => 'QZ-DZAF-Q329', 'target_sku' => '60-SZQD', 'logistics' => 'coolsystem'],
        ['original_sku' => 'P0-EKT4-TWCN', 'target_sku' => '60-YTMHM', 'logistics' => 'coolsystem'],
        ['original_sku' => 'CM-TF1B-74BH', 'target_sku' => '60-SZC3', 'logistics' => 'coolsystem'],
        ['original_sku' => 'CI-MOZM-NIFV', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'LR-ZJJZ-USEX', 'target_sku' => '60-HTMMJ', 'logistics' => 'coolsystem'],
        ['original_sku' => '7Z-UP0Z-LI5O', 'target_sku' => '60-BZDS', 'logistics' => 'coolsystem'],
        ['original_sku' => 'HX-MYP4-LZ9I', 'target_sku' => '60-SZTW', 'logistics' => 'coolsystem'],
        ['original_sku' => 'E6-1BZ7-WVB2', 'target_sku' => '60-SZMG', 'logistics' => 'coolsystem'],
        ['original_sku' => 'Q3-2U0L-TKWJ', 'target_sku' => '60-HSZBTW', 'logistics' => 'coolsystem'],
        ['original_sku' => 'KV-HRZP-YAM7', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => '0Z-8DWV-X7SM', 'target_sku' => '60-HHT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'GN-EAO4-J3QO', 'target_sku' => '60-YTMML', 'logistics' => 'coolsystem'],
        ['original_sku' => 'LD-FGJG-OBY4', 'target_sku' => '60-HSZ', 'logistics' => 'coolsystem'],
        ['original_sku' => '7L-QDBQ-F2BT', 'target_sku' => '60-ZZYYLM', 'logistics' => 'coolsystem'],
        ['original_sku' => 'IP-CJNB-YWEP', 'target_sku' => '60-SZTW', 'logistics' => 'coolsystem'],
        ['original_sku' => 'NN-T67K-7D7D', 'target_sku' => '60-BZMJ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'O8-8EPE-GZ7E', 'target_sku' => '60-BZPT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'HJ-8VLR-FL51', 'target_sku' => '60-BMMMJ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'K7-NGKS-50PI', 'target_sku' => '60-BJHTW', 'logistics' => 'coolsystem'],
        ['original_sku' => '4J-HZO0-J5IT', 'target_sku' => '60-SZQN', 'logistics' => 'coolsystem'],
        ['original_sku' => '6B-GJT9-C2QX', 'target_sku' => '60-SZTT', 'logistics' => 'coolsystem'],
        ['original_sku' => '3C-GAYN-ZUA6', 'target_sku' => '60-SZMJ', 'logistics' => 'coolsystem'],
        ['original_sku' => '8O-O680-EQEK', 'target_sku' => '60-SZC3', 'logistics' => 'coolsystem'],
        ['original_sku' => 'UT-WVG8-YUKY', 'target_sku' => '60-BJHTW', 'logistics' => 'coolsystem'],
        ['original_sku' => '3G-LRHJ-JGG1', 'target_sku' => '60-C91CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => '8Z-B2JX-1JBE', 'target_sku' => '60-SBLXM', 'logistics' => 'coolsystem'],
        ['original_sku' => '22-5P7L-WWQA', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'QW-XCBJ-6H4G', 'target_sku' => '60-SZDS', 'logistics' => 'coolsystem'],
        ['original_sku' => '07-EGIL-KBL1', 'target_sku' => '60-SZPT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'NN-WF2C-7CHN', 'target_sku' => '60-BZPT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'J0-D0ZM-LQ6M', 'target_sku' => '60-BZDS', 'logistics' => 'coolsystem'],
        ['original_sku' => '04-G3AW-0QDB', 'target_sku' => '60-SBLC4', 'logistics' => 'coolsystem'],
        ['original_sku' => 'QL-T245-JNAP', 'target_sku' => '60-YTMBSLN', 'logistics' => 'coolsystem'],
        ['original_sku' => '5B-2M8L-W10B', 'target_sku' => '60-SBLC3', 'logistics' => 'coolsystem'],
        ['original_sku' => 'J7-WAKJ-OXCD', 'target_sku' => '60-HTMBTW', 'logistics' => 'coolsystem'],
        ['original_sku' => '83-131Q-5389', 'target_sku' => '60-HTMC5', 'logistics' => 'coolsystem'],
        ['original_sku' => 'UC-M2IS-8SVX', 'target_sku' => '60-iPadZJ', 'logistics' => 'coolsystem'],
        ['original_sku' => '3U-D07A-PV9O', 'target_sku' => '60-HSZBTW', 'logistics' => 'coolsystem'],
        ['original_sku' => 'RH-UBL6-G4X6', 'target_sku' => '60-SBLHJ', 'logistics' => 'coolsystem'],
        ['original_sku' => '1S-RRUW-A2X4', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'SN-5FA3-ZF96', 'target_sku' => '60-BMM', 'logistics' => 'coolsystem'],
        ['original_sku' => 'NQ-55EA-QZ53', 'target_sku' => '60-BZJG', 'logistics' => 'coolsystem'],
        ['original_sku' => 'BT-DK7J-U8TQ', 'target_sku' => '60-SBLTY', 'logistics' => 'coolsystem'],
        ['original_sku' => 'LY-UTC9-XZUD', 'target_sku' => '60-HHT', 'logistics' => 'coolsystem'],
        ['original_sku' => '2O-3MUI-6L3N', 'target_sku' => '60-C91CQ', 'logistics' => 'coolsystem'],
        ['original_sku' => '5F-NM2B-FM82', 'target_sku' => '60-HSZ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'JI-CPE7-BR2C', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'S2-URDB-PRMU', 'target_sku' => '60-YTMTT', 'logistics' => 'coolsystem'],
        ['original_sku' => 'S2-W88C-PSF9', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => '07-9070-KCB8', 'target_sku' => '60-SZJG', 'logistics' => 'coolsystem'],
        ['original_sku' => 'OM-4DTK-NGKZ', 'target_sku' => '60-SZM9', 'logistics' => 'coolsystem'],
        ['original_sku' => 'ES-N4TQ-6IWJ', 'target_sku' => '60-HSZIPAD', 'logistics' => 'coolsystem'],
        ['original_sku' => 'RS-ERXS-2YKR', 'target_sku' => '60-YTMTT', 'logistics' => 'coolsystem'],
        ['original_sku' => '5B-CJ57-W18N', 'target_sku' => '60-C91CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => '83-29S9-53AR', 'target_sku' => '60-FC6CDQ', 'logistics' => 'coolsystem'],
        ['original_sku' => 'FY-P7CM-PU5M', 'target_sku' => '60-HSZBTW', 'logistics' => 'coolsystem'],
        ['original_sku' => 'M2-RVW0-HM8C', 'target_sku' => '60-SZHT', 'logistics' => 'coolsystem'],
            
            ];

    }

}
?>
