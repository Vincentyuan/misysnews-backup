<?php

require_once(__DIR__."/../../..".'/wp-config.php');
class misysnews_feeds_tool_function{

  static function getWholeFeeds(){
    // return get_option('misysnews_feeds_new');
    // return get_option('misysnews_feeds')['json_data'];

    if(get_option('misysnews_feeds_new')&&('misysnews_feeds_new')!=""){
      return get_option('misysnews_feeds_new');
    }else {
      return get_option('misysnews_feeds')['json_data'];
    }

  }
  //get all the customized data by post_type
  static function getCustomizedPostsDataFromPostType($post_type){
    $posts_ids = self::getCustomizedPostsIdFromPostType($post_type);
    $posts_data = self::getPostsObjectsFromPostIds($posts_ids);

    return $posts_data;
  }
  // return the published posts id with the specified post_type
  static function getCustomizedPostsIdFromPostType($post_type){
    $publishedPostId  = array();
    $parameter = self::generatePostsParameter($post_type);
    $posts_array = get_posts($parameter);

    for ($i=0; $i < count($posts_array) ; $i++) {
      array_push($publishedPostId,$posts_array[$i]->ID);
    }

    return $publishedPostId;
  }

  //return all the valid object
  static function getPostsObjectsFromPostIds($posts_ids){
    $publishedPosts = array();
    for ($i=0; $i < count($posts_ids); $i++) {
      $post_data = self::getPostsMetaDataByPostID($posts_ids[$i]);
      $post_data["title"] = get_the_title($posts_ids[$i]);
      //if there should be a specific image
      if($post_data['background_image'] != ""){
        $post_data['background_image'] = get_field("background_image",$posts_ids[$i]);
      }
      // check the category and construct the url

      $postCategoryNative = get_the_category($posts_ids[$i]);
      $post_data['category_id'] = $postCategoryNative[0]->term_id;

      if(  $post_data['category_id']){
        $category_name = get_the_category_by_id($post_data["category_id"]);
        $post_data['category'] = $category_name ? $category_name:"";
        //init the feed url according to the category
        $post_data['url'] = get_category_feed_link($post_data['category_id']);
      }else{
        $post_data['url'] = get_field('url',$posts_ids[$i]);
      }

      // if($post_data['category']!=""){
      //   $post_data['category_id'] = $post_data['category'];
      //   $category_name = get_the_category_by_id($post_data["category_id"]);
      //   $post_data['category'] = $category_name ? $category_name:"";
      //   //init the feed url according to the category
      //   $post_data['url'] = get_category_feed_link($post_data['category_id']);
      // }
      array_push($publishedPosts,$post_data);
    }
    return $publishedPosts;
  }

  //return an object for get post function
  static function generatePostsParameter($post_type){
    //get only published post
    return array(
      "post_type" =>$post_type,
      "post_status" => 'publish',
      'numberposts'	=> -1,
      'suppress_filters' => false
    );
  }
  static function getPostsMetaDataByPostID($id){
    $originData = get_post_meta($id,"",true);
    $formatData = self::formatPostsMetaData($originData);
    $formatData['postID'] = $id;
    return $formatData;
  }
  //filter the field startwith  '_'
  static function formatPostsMetaData($obj){
    $newObj;

    foreach ($obj as $key => $value) {
      if(substr($key,0,1)!= '_'){
        $newObj[$key] = $value[0];
      }
    }
    return $newObj;
  }

  static function increaseSerialNumber(){
    $oldSerialNumber = get_option("misysnews_runnable_serial_number");
    $newSerialNumber = (int)$oldSerialNumber + 1;
    update_option("misysnews_runnable_serial_number",$newSerialNumber);
  }

}

?>
