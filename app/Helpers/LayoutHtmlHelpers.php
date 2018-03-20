<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Category;
use App\MegaMenu;
use Auth;
use Session;

class LayoutHtmlHelpers {



    public static function getHeaderMenu(){
       $menu= new MegaMenu;
       $menuJson=$menu->select('*')->where('block_name','Header menu')->where('is_default_block','1')->orderBy('id','DESC')->first();
       // echo "<pre>";
       // print_r(json_decode($menuJson));
       // dd($menuJson);
       /*foreach($menuJson as $data){
         $menuRes=$data->menu_json;
       }*/
      return $menuJson;
    }


    public static function getFooterMenu(){
       $menu= new MegaMenu;
       $menuJson=$menu->select('*')->where('block_name','footer')->orderBy('id','DESC')->first();
       /*foreach($menuJson as $data){
         $menuRes=$data->menu_json;
       }*/
      return $menuJson;
    }



    public static function getChildCategory($parent_id){
        $child=array();
        $results = Category::where([['parent_id', $parent_id], ['status', '1']])->limit(25)->get();
        foreach($results as $result){
          $child[]=$result;
        }
        return $child; 

    }


    public static function getCatgoriesMenuList($cat_id=null){
       if($cat_id>0){
         $results=Category::where([['parent_id', '0'], ['status', '1'],['id', $cat_id]])->limit(15)->get();
        }else{
          $results=Category::where([['parent_id', '0'], ['status', '1']])->limit(15)->get();
        }
                    
        $html='';
        foreach($results as $result){
          $childCat=self::getChildCategory($result->id);
          $catName = isset($result->CategoryDesc->category_name)?$result->CategoryDesc->category_name:'';
          $html.=' <li class="item level1 parent">
                      <a class="menu-link" href="en/category/'.$result->url.'"><span>'.$catName.'</span>';
          if(count($childCat)>0){
            $html.=' <i class="glyphicon glyphicon-menu-right"></i>';
          }
          $html.='</a>';
          if(count($childCat)>0){ 
          $html.='<ul class="level1 groupmenu-drop">';
                  foreach($childCat as $childresult){
                  $catNameChild = isset($childresult->CategoryDesc->category_name)?$childresult->CategoryDesc->category_name:'';
                   $childCat2=self::getChildCategory($childresult->id);
                   $html.='<li class="item level2 nav-2-1 first "><a class="menu-link" href="en/category/'.$childresult->url.'"><span>'.$catNameChild.'</span>';
                    if(!empty($childCat2)){
                        $html.='<i class="glyphicon glyphicon-menu-right"></i>';
                    }
                    $html.='</a>';
                      if(!empty($childCat2)){
                        $html.='<ul class="level1 groupmenu-drop">';
                        foreach($childCat2 as $childresult2){
                        $catNameChild2 = isset($childresult2->CategoryDesc->category_name)?$childresult2->CategoryDesc->category_name:'';
                         $html.='<li class="item level2 nav-2-1 first"><a class="menu-link" href="en/category/'.$childresult2->url.'"><span>'.$catNameChild2.'</span></a>';
                         $html.='</li>';
                        }
                        $html.='</ul>';
                      }
                   $html.='</li>';
                  }
          $html.='</ul>';

          }
          $html.=' </li>';
        }
        return $html;
    }



    public static function getTemplateChild($childCat,$id){
       $html='<ul class="level1 groupmenu-drop">';
                  foreach($childCat as $childresult){
                  $catNameChild = isset($childresult->CategoryDesc->category_name)?$childresult->CategoryDesc->category_name:'';
                   $html.='<li class="item level2 nav-2-1 first"><a class="menu-link" href="en/'.$childresult->url.'"><span>'.$catNameChild.'</span></a>';
                   $html.='</li>';
                  }
          $html.='</ul>';
      return $html;
    }



    public static function getCatgoriesMenu($cat_id=0) {
      
      $default_lang_code = '';

        //$default_lang_code = session('lang_code');
      // $catUrl = action('ProductsController@category');
       $html = '<div class="megaNavigation">';
       $html.= '<a href="" class="mobileMaxmenu"><span></span><span></span><span></span></a><ul>';
       $results = Category::where([['parent_id', '0'], ['status', '1']])->limit(5)->get();

       //categorydesc, category
       foreach($results as $result){
          //dd($result->CategoryDesc->category_name);
          $catName = isset($result->CategoryDesc->category_name)?$result->CategoryDesc->category_name:'';
           //dd($catName);
          $html.='<li><a href="'.action('ProductsController@category', $result->url).'">'.$catName.'<i class="fa fa-angle-down" aria-hidden="true"></i></a>';

          if(isset($result->category) && count($result->category)>0){
             $html.='<div class="bxMenu"><div class="insidebxmenu">
                       <div class="bxlinkmenu-left clearfix">';
             $html.='<div class="catlisting"><div class="listing-item"><ul>';
             foreach($result->category as $subcat){
                $subcatName = isset($subcat->CategoryDesc->category_name)?$subcat->CategoryDesc->category_name:'';
                $html.='<li><a href="'.action('ProductsController@category', $subcat->url).'">'.$subcatName.'</a></li>';

             }
             $html.= '</ul></div></div>';
             $html.= '</div></div>';             
          }
         $html.='</li>';
       }
       $html .='</ul></div>';
       return $html;
    } 


  public static function productsRender($angulerController = null){
     
     $html = '';

     $html .= '<div class="product-list">
            <div class="loader-wrap" ng-if="loadingMore == true">
                <div class="loader-img">
                    <img src="images/ajax-loader.gif" alt="">
                </div>
            </div>
            <div class="row new-item-list" data-ng-cloak>
                <ul ng-class="productsView==\'list\'? \'product-list\':\'\'">
                    <li class="col-sm-3 col-xs-6 product-list-item"  data-ng-repeat="item in product_Items track by $index">
                        <!--div class="item-version">
                            <span class="version-txt">New</span>
                        </div-->
                        <a class="product-image" href="<%item.url%>"><img ng-src="<%item.thumbnail_image%>" alt="<%item.name%>"></a>
                        <div class="product-info">';
                        if(Auth::id()) {
                            $html .= '<span class="pull-right icon-heart" 
                               ng-click="addIntoWishlist(item.id, $event, $index)" ng-if="item.wish == null"></span>

                               <span class="pull-right icon-heart active" 
                               ng-click="removeFromWishlist(item.id, $event, $index)" ng-if="item.wish != null"></span>';
                          }

                         $html .= '<h2 class="product-name"><%item.name%></h2>
                            <span class="sku"><%item.sku%></span>
                            <div class="price-wrap">
                                <span class="price"><%item.initial_price%>'.Session::get('default_currency_code').'</span>
                            </div>
                        </div>
                    </li>
                    
                </ul>  
            </div>
    </div>';
     
     $html .= '<div class="pagination">
        <div class="pages">
            <ol>
               <li class="prev">
                  
                  <a href="javascript:void(0)" ng-click="loadpagedata(page-1)" ng-if="page > 1" ></a>
               </li>
                <li ng-class="i == page ? \'current\' : \'\'" data-ng-repeat="i in totalpagesArray">

                 <a href="javascript:void(0)" ng-if="i == page"><%i%></a>

                 <a href="javascript:void(0)" ng-click="loadpagedata(i)" ng-if="i != page"><%i%></a>
                    


                </li>
                
                <li class="last">
                   <a href="javascript:void(0)" ng-click="loadData()" ng-if="totalpages >= page && totalpages != page" ></a>

                </li>
            </ol>
        </div>
    </div>';
         
     return $html;
  }  



}
