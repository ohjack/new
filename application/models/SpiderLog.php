<?php

class SpiderLog {

    public static function getLastSpider( $type, $mark ) {
        $lasttime = DB::table('spider_log')->where('mark', '=', $mark)
                                           ->where('type', '=', $type)
                                           ->first(['id', 'lasttime']);

        return $lasttime;
    
    }

    public static function updateLastSpider( $id ) {
        $prevtime = DB::table('spider_log')->where('id', '=', $id)
                                           ->only('prevtime');

        $data = [
            'lasttime' => $prevtime,
            'prevtime' => date('Y-m-d H:i:s')
            ];

        DB::table('spider_log')->where('id', '=', $id)
                               ->update($data);
    
    }

    public static function insertLastSpider( $type, $mark ) {

        $data = [
            'type' => $type,
            'mark' => $mark,
            'prevtime' => date('Y-m-d H:i:s'),
            'lasttime' => date('Y-m-d H:i:s')
            ];

        DB::table('spider_log')->insert($data);
    
    }
}
?>
