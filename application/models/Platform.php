<?php
class Platform
{
    public static function getAll()
    {
       return DB::table('platform')->get();
    }
    
    public static function getByID($platform_id)
    {
        return DB::table('platform')->where('id','=',$platform_id)->first();
    }
}
?>