<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;
    
    protected  $table = 'languages';
    
    protected $dates = ['deleted_at'];    

    public static function getLangugeDetails() {
    	return self::select('id', 'languageName', 'languageCode', 'languageFlag')->where('status', '1')->get();
    } 

    public static function getDefaultLanguge() {
    	return self::select('id', 'languageName', 'languageCode', 'languageFlag')->where(['status'=>'1', 'isDefault'=>'1'])->first();
    }       
}
