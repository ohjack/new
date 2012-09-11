<?php
/**
 * 平台抓取日志
 *
 * 为下次抓取提供参照物
 *
 * @author: weelion <weelion@qq.com>
 * @copyright: Copyright (c) 2012 EMIO Tech All Rights Reserved.
 * @version: $Id:SpiderLog.php  2012年09月10日 星期一 16时42分25秒Z $
 */



class SpiderLog {

    /**
     * 获取上上次抓取时间
     *
     * 每次抓取都从上上次的时间到当前时间段进行抓取
     * 为了防止漏抓,每段时间的订单都会抓取两次
     * spider表中lasttime就是上上次抓取时间,prevtime是上次抓取时间
     * 
     * @param: $type string  抓取类型
     * @param: $mark hash    标识 由API的配置md5生成
     *
     * return $lasttime  最后抓取时间
     */
    public static function getLastSpider( $type, $mark ) {

        $lasttime = DB::table('spider_log')->where('mark', '=', $mark)
                                           ->where('type', '=', $type)
                                           ->first(['id', 'lasttime']);

        return $lasttime;
    
    }

    /**
     * 更新抓取时间
     *
     * 将上次抓取时间更新成上上次更新时间放在lasttime字段
     * 将当前时间储存在上次抓取时间prevtime字段
     *
     * @param: $id integer 目标记录id
     *
     * return void
     */
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

    /**
     * 新增抓取记录
     *
     * 记录抓取时间以便以后从这个时间段进行抓取
     *
     * @param: $type string  抓取类型
     * @param: $mark hash    标识 由API的配置md5生成
     *
     * return void
     */
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
