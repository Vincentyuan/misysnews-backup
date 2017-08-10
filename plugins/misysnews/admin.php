<?php
require_once("databaseoop.php");
require_once("deviceoop.php");
class MisysNewsSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'init', array( $this, 'create_customized_post_type' ),0 );//create customized post type`
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action( 'admin_footer',array($this,'add_js_function'));
        add_action( 'admin_enqueue_scripts', array($this,'load_custom_wp_admin_style' ));
        $this->add_customized_ajax_request();
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {

        add_menu_page(
          'FinastraNews Admin',// 'Misysnews Admin',
          'Finastra News',// 'Misys News',
          'manage_options',
          'misysnews_top_admin' ,
          array( $this, 'create_home_page' ),
          'dashicons-admin-generic' //icon
        );


        add_submenu_page(
            'misysnews_top_admin',
            'sources',
            'Sources',
            'manage_options',
            'edit.php?post_type=misysnews_feeds',
            null
        );

        add_submenu_page(
            'misysnews_top_admin',
            'Topics',
            'Topics',
            'manage_options',
            'misysnews_angular',
            array( $this, 'create_sources_angular_field' )
        );

        add_submenu_page(
            'misysnews_top_admin',
            'Connections',
            'Devices',
            'manage_options',
            'misysnews_connection',
            array( $this, 'create_connection_page' )
        );
        //advanced page
        add_submenu_page(
          'misysnews_top_admin',
          "Misysnews feeds Advanced",
          "Advanced",
          "manage_options",
          "misysnews_old",
          array( $this, 'create_admin_page' )
        );



    }
    public function create_sources_angular_field(){

      wp_enqueue_script( 'misysnews_angularts_indline', plugins_url( 'misysnews/angularts/dist/inline.bundle.js' ), null, null, true );
      wp_enqueue_script( 'misysnews_angularts_polyfills', plugins_url( 'misysnews/angularts/dist/polyfills.bundle.js' ), null, null, true );
      wp_enqueue_script( 'misysnews_angularts_styles', plugins_url( 'misysnews/angularts/dist/styles.bundle.js' ), null, null, true );
      wp_enqueue_script( 'misysnews_angularts_vendor', plugins_url( 'misysnews/angularts/dist/vendor.bundle.js' ), null, null, true );
      wp_enqueue_script( 'misysnews_angularts_main', plugins_url( 'misysnews/angularts/dist/main.bundle.js' ), null, null, true );

      ?>
      <!-- tag that contains the plugin url  -->
      <h1 id="wpPluginPath" style="display:none;"><?php echo plugins_url(); ?></h1>

      <my-app></my-app>
      <?php

    }

    /**
    *function for create customized post type
    */
    public function create_customized_post_type(){
      add_action("manage_misysnews_feeds_posts_custom_column",array($this,"fullfil_customized_table"),10,2);
      $labels = array(
          'name'               => _x( 'Sources', 'post type general name' ),
          'singular_name'      => _x( 'Sources', 'post type singular name' ),
          'add_new'            => _x( 'Add New', 'book' ),
          'add_new_item'       => __( 'Add New Source' ),
          'edit_item'          => __( 'Edit Source' ),
          'new_item'           => __( 'New Source' ),
          'all_items'          => __( 'Sources' ),
          'view_item'          => __( 'View Source' ),
          'search_items'       => __( 'Search Source' ),
          'not_found'          => __( 'No Source found' ),
          'not_found_in_trash' => __( 'No Source found in the Trash' ),
        );
        $args = array(
          'labels'        => $labels,
          'description'   => 'Holds source for finastra news',
          'public'        => true,
          'publicly_queryable' => true,
          'menu_icon'   => 'dashicons-admin-generic',
          'hierarchical '  => true,
          'supports'      => array(   'custom-fields',  'title','misysnews_feeds', 'thumbnail' , 'excerpt', 'editor','comments', 'page-attributes'),
          'query_var'          => true,
		      'rewrite'            => array( 'slug' => 'ttlm_team' ),
          'has_archive'   => true,
          'show_in_menu'   => false,
          'can_export'          => true,
          'taxonomies'     => array( 'category' ),
        );
        register_post_type( 'misysnews_feeds', $args ); //register post type
    }
    // define the header of table here
    public function create_customized_head(){
      $columns = array(

    		'title' => __( 'Title' ),
    		'url' => __( 'URL' ),
    		//'discription' => __( 'discription' ),
    		'date' => __( 'Date' )
    	);
    	return $columns;
    }
    // fullfil the table content
    public function fullfil_customized_table($column, $post_id){
      //echo $post_id;
      global $post;
      switch ($column) {
        case 'url':
          /*get the post meta */
          $url = get_post_meta($post_id,'url',true);
          if( empty($url)){
            echo __('Unknow');
          }else {
            printf( __('%s'),$url);
          }
          break;

        default:
          # code...
          break;
      }
    }


    /**
     * Options page callback
     */
    public function create_home_page()
    {
        ?>
        <div class="wrap" id ="customized_home_page">
          <h1 style="margin-bottom:20px;">Finastra News</h1>
          <form id="post" >
            <div id ="poststuff">
              <div id ="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="background-color:rgb(255,255,255);padding-bottom:50px" align="center">
                  <!-- main block welcome image -->
                  <img src="http://misysnews/wp-content/uploads/2017/06/admin-hero-image_01.png" style="width:60%;height:auto" />
                </div>
                <div id="postbox-container-1" class="postbox-container ">
                  <!-- publish block  -->
                  <div id="side-sortables" class="meta-box-sortables ui-sortable">
                      <div id="submitdiv" class="postbox custom-box-class">
                        <button  onclick ="collepseClickEvent(this)" type="button" class="handlediv  custom-collepse-class" aria-expanded="true">
                          <span class="screen-reader-text"> Toggle panel :Publish </span>
                          <span class="toggle-indicator" aria-hidden="true"></span>
                        </button>
                        <h2 onclick ="collepseClickEvent(this)" class = "hndle ui-sortable-handle  custom-collepse-class"><span> Version Information : </span> </h2>
                        <div class="inside">
                          <div class="submitbox" id = "submitpost">
                            <div id ="minor-publishing">
                              <!-- containt should be here  -->
                              <div id ="misc-publishing-actions">
                                <!-- <div class="misc-pub-section">Hello </div> -->
                                <h2 class="handle ui-sortable-handle" style ="margin-left:5px;">
                                  <span> Version Number : </span>
                                  <span class="wpBlue"> <?php $version = get_option("misys_news_version");echo ($version ? $version:"Empty") ?> </span> </h2>
                                <h2 class="handle ui-sortable-handle" style ="margin-left:5px;">
                                  <span> Serial Number: </span>
                                  <span id ="serialnumber" class="wpBlue"><?php $serialNumber = get_option("misysnews_runnable_serial_number");echo ($serialNumber ? $serialNumber:"Empty") ?></span> </h2>
                                <div class="inside" style ="margin-left:20px;">

                                <div class="clear"></div>
                              </div>
                            </div>
                            <div id ="minor-publishing-actions">
                                <!-- operation button here  -->
                                <div >
                                  <span class="spinner"></span>
                                  <div class="clear"></div>
                                </div>
                                <div class="clear" style="margin-bottom:10px;"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
                <div id="postbox-container-2" class="postbox-container">
                  <!-- table should be here   -->
                </div>
              </div>
            </div>
          </form>
        </div>
        <?php



    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {

         $this->options = get_option( 'misysnews_feeds_new' );
        ?>
        <div class="wrap">
            <h2>Advanced</h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'misysnews_feeds_group' );
                do_settings_sections( 'misysnews-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
    /**
     * create connection page
     */
    public function create_connection_page()
    {
      $this->loadManagemntPanel();
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'misysnews_feeds_group', // Option group
            'misysnews_feeds_new', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );


        add_settings_section(
            'misysnews_feeds_section_id', // ID
            'Configuration the Topics( Only advanced user)', // Title
            array( $this, 'print_section_info' ), // Callback
            'misysnews-admin' // Page
        );

        add_settings_field(
            'json_data',
            'Configuration',
            array( $this, 'json_data_callback' ),
            'misysnews-admin',
            'misysnews_feeds_section_id'
        );



    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        // $new_input = array();
        //
        // if( isset( $input['json_data'] ) ) {
        //     $new_input['json_data'] = $input['json_data'];
        // }
        //
        // return $new_input;

        return $input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Modify the topics setting here:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function json_data_callback()
    {
        printf(
            '<textarea  id="json_data" name="misysnews_feeds_new" type="textarea" cols="150" rows="20" style="resize:none;">%s</textarea>',
            isset( $this->options) ? esc_attr( $this->options) : ''
        );
    }
    /**
    *load the field related to the version
    */
    public function loadManagemntPanel(){
      ?>
      <div class="wrap" >
          <h1>Devices</h1>

          <div>
            <br/>
            <Button class="button button-primary button-large" onclick="clearAllUnactiveDevice()">Clear All Unactive Device</Button>
            <h2 style="font-size:1.1em">Registered Device</h2>
            <?php
            self::listAllRegestedDevice();
             ?>
          </div>
          <br/>

          <div>
            <h2 style="font-size:1.1em">Anonymous Access</h2>
            <?php
              self::listUnrecognizedDevices();

            ?>
          </div>

      </div>
      <?php
    }
    /**
    *print all the registered devices
    */
    public function listAllRegestedDevice()
    {

        $deviceMgt = new misysnews_deviceManagement();
        $data = $deviceMgt->listRegisteredDevice();

        if(count($data).length!=0){
          // print out the result
          printf("<table   class ='wp-list-table widefat fixed striped posts' >");
          printf("<tr>
                    <th>Device</th>
                    <th>Last Request</th>
                    <th>IP</th>
                    <th>Status</th>
                    <th>Version</th>
                    <th>Serial Number</th>
                    <th>Operation</th>
                  </tr>
                  ");
          // loop to print all the element

          for ($i=0; $i < count($data) ; $i++) {
            printf("<tr>
                      <td style='font-weight:bold'>".$data[$i]->getName()."</td>
                      <td>".$data[$i]->getLatestRequest()." ago</td>
                      <td>".$data[$i]->getIP()."</td>
                      <td>
                        <svg xmlns='http://www.w3.org/2000/svg' style=' width: 30px; height: 10px;'>
                            <circle cx='5' cy='5' r='5' fill=".$data[$i]->getStatus()." />
                        </svg>
                      </td>
                      <td>".$data[$i]->getVersion()."</td>
                      <td>".$data[$i]->getSerialNumber()."</td>
                      <td style ='text-overflow:ellipsis; white-space:nowrap; overflow:hidden;'>
                        <label id ='".$data[$i]->getIP()."'  onclick='showModal(this.id)' class='trash cursorHand trashLink'><a>Remove</a></label>
                        <div id ='".$data[$i]->getIP()."Modal'
                        class='customizeModal'
                        style='visibility:hidden; '>
                          <div class='modal-dialog'>
                            <div class='modal-content'>
                              <div class='modal-header'>
                                Notice
                              </div>
                              <div class='modal-body'>
                                Do you really want to remove this device? <span style='color:#0085ba'>  ".$data[$i]->getName()." </span><br/>
                                After that this device will be displayed in Anonymous Devices
                              </div>
                              <div class='modal-footer'>
                                <button type='button' id ='".$data[$i]->getIP()."' class='button  button-primary button-large page-title-action'
                                 onclick='removeDevices(this.id)' >Yes</button>
                                <button type='button' id ='".$data[$i]->getIP()."'  class='button button-large page-title-action'
                                  onclick ='hideModal(this.id)' >No</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
            ");
          }
          printf('</table>');
        }else {
          echo ("
          <div id='minor-publishing' class='NoDeviceDiv'>
            <h4 class='NoDeviceMessage'>Please register some devices</h4>
          </div>
          ");
        }

    }
    public function listUnrecognizedDevices(){
      $deviceMgt = new misysnews_deviceManagement();
      $data = $deviceMgt->listUnrecognizedDevices();

      if(count($data) != 0){
        printf("<table  class ='wp-list-table widefat fixed striped posts' >");
        printf("<tr>
                  <th>Device</th>
                  <th>Last Request</th>
                  <th>IP</th>
                  <th>Status</th>
                  <th>Version</th>
                  <th>Serial Number</th>
                  <th>Operation</th>
                </tr>
                ");
        for ($i=0; $i < count($data) ; $i++) {
          printf("<tr>
                    <td><input  type='search' class='text form-control InputDeviceName' placeholder='  Device Name'
                      id='".$data[$i]->getName()."-NewName' ></input></td>
                    <td>".$data[$i]->getLatestRequest()." ago</td>
                    <td >".$data[$i]->getName()."</td>
                    <td>
                      <svg xmlns='http://www.w3.org/2000/svg' style=' width: 30px; height: 10px;'>
                          <circle cx='5' cy='5' r='5' fill=".$data[$i]->getStatus()." />
                      </svg>
                    </td>
                    <td>".$data[$i]->getVersion()."</td>
                    <td>".$data[$i]->getSerialNumber()."</td>

                    <td><label style='color:#0073aa'  id ='".$data[$i]->getIP()."' onclick='registerDevices(this.id)' class='cursorHand'><a>Register</a></label></td>
                  </tr>
          ");
        }
      }else {
        echo ("
        <div id='minor-publishing' class='NoDeviceDiv'>
          <h4 class='NoDeviceMessage'>No Anonymous Connection!</h4>
        </div>
        ");
      }


    }
    // centrally add all ajax request
  private function add_customized_ajax_request(){
    add_action('wp_ajax_register_device',array($this,'registerDevices'));
    add_action('wp_ajax_remove_device',array($this,'removeDevices'));
    add_action('wp_ajax_increase_serial_number',array($this,'increaseSerialNumber'));
    add_action('wp_ajax_clear_all_unactive_device',array($this,"clearAllUnactiveDevice"));
    add_action('wp_ajax_switch_feeds',array($this,"switchFeeds"));
    add_action('wp_ajax_active_new_feeds',array($this,"activeNewFeeds"));
    add_action('wp_ajax_active_feeds_test_mode',array($this,"startFeedsTestMode"));

  }
  //here is the js functions
  public function add_js_function(){
    ?>
  	<script type="text/javascript" >

      function showModal(id){
        document.getElementById(id+"Modal").style.visibility="visible";
      }
      function hideModal(id){
        document.getElementById(id+"Modal").style.visibility="hidden";
      }
      function registerDevices(id){
        var ip = id;
        var name = document.getElementById(id+"-NewName").value;
        if(name){
          var data = {
            'action':'register_device',
            "ip":ip,
            "name":name
          };
          jQuery.post(ajaxurl,data,function(response){
              location.reload();
          });
        }else{
          alert("Please input the name of new device");
        }
      }
      function removeDevices(id){
        var ip = id;
        var data={
          'action':"remove_device",
          'ip':ip
        };
        jQuery.post(ajaxurl,data,function(response){
            location.reload();
        });
      }
      function increaseSerialNumber(){
        var data = {
          'action':'increase_serial_number'
        };
        jQuery.post(ajaxurl,data,function(response){
            document.getElementById("serialnumber").innerText = response.newSerialNumber;
        });
      }
      function clearAllUnactiveDevice(){
        jQuery.post(ajaxurl,{'action':"clear_all_unactive_device"},function(response){
            location.reload();
        });
      }
      function switchFeeds(){
        jQuery.post(ajaxurl,{'action':"switch_feeds"},function(response){
            // location.reload();
            document.getElementById("isrunlatestfeeds").innerText = response.feedsMode;
        });
      }
      function activeNewFeeds(){
        document.getElementById("changeFeeds").disabled = false;
        document.getElementById("isrunlatestfeeds").style.color = "Black";
        jQuery.post(ajaxurl,{'action':"active_new_feeds"},function(response){
            document.getElementById("isrunlatestfeeds").innerText = response.feedsMode;
        });
      }
      function startFeedsTestMode(){
        document.getElementById("isrunlatestfeeds").innerText = "Running on Test Mode";
        document.getElementById("isrunlatestfeeds").style.color = "Red";
        document.getElementById("changeFeeds").disabled = true;
        jQuery.post(ajaxurl,{'action':"active_feeds_test_mode"},function(response){
            // document.getElementById("isrunlatestfeeds").innerText = response.feedsMode;
        });
      }

      function collepseClickEvent(element){
        if(!element.parentElement.classList.contains("closed")){
          element.parentElement.classList.add("closed");
        }else {
          element.parentElement.classList.remove("closed");
        }
      }

      jQuery(document).ready(function($){

        if(document.getElementById("json_data")){
          var ugly = document.getElementById('json_data').value;
          var obj = JSON.parse(ugly);
          var pretty = JSON.stringify(obj, undefined, 4);
          document.getElementById('json_data').value = pretty;
        }

      });

  	</script> <?php
  }
  //below are the ajax handle functions
  public function registerDevices(){
    $device = new misysnews_device();
    $device->setRegisterInformation($_POST['ip'],$_POST['name']);

    $db = new misysnews_dbconnection();
    $stmt = $db->getConnection()->prepare("insert into ".$db->getTableName()["deviceTable"]." (ipaddress,devicename,addtime) values(?,?,NOW())");
    $stmt->bind_param("ss",$device->getIP(),$device->getName());
    $stmt->execute();
  }
  public function removeDevices(){

    $db = new misysnews_dbconnection();
    $stmt = $db->getConnection()->prepare("delete from ".$db->getTableName()["deviceTable"]." where ipaddress=?");
    $stmt->bind_param("s",$_POST['ip']);
    $stmt->execute();
  }
  public function increaseSerialNumber(){
    // here increase serial number
    $oldSerialNumber = get_option("misysnews_runnable_serial_number");
    $newSerialNumber = (int)$oldSerialNumber + 1;
    update_option("misysnews_runnable_serial_number",$newSerialNumber);
    $response =  array('newSerialNumber' => $newSerialNumber );
    header('Content-Type: application/json');
    echo json_encode($response);
    wp_die();
  }
  public function clearAllUnactiveDevice(){
    //just clear the unregistered deviceoop
    $deviceMgt = new misysnews_deviceManagement();
    $deviceMgt->clearAllUnactiveDevice();
    wp_die;
  }
  public function switchFeeds(){
    update_option("misysnews_feeds_run_test",0);
    update_option("misysnews_run_latest_feed",!get_option("misysnews_run_latest_feed"));
    $mode = get_option("misysnews_run_latest_feed")?"Latest Feeds":"Last Runable Feeds";
    $response =  array('feedsMode' => $mode );
    header('Content-Type: application/json');
    echo json_encode($response);
    wp_die();
  }

  public function activeNewFeeds(){
    //reset the status of misysnews_feeds_is_run_test status to exit feeds test mode
    //copy the feeds from misysnews_feeds_test to misysnewus_feeds_new
    //update the serial number to enable the new feeds misysnews_feeds_new
    update_option("misysnews_feeds_run_test",0);
    update_option("misysnews_feeds_new",get_option("misysnews_feeds_test"));
    update_option("misysnews_run_latest_feed",1);
    // $this->increaseSerialNumber();
    // wp_die();
    $mode = get_option("misysnews_run_latest_feed")?"Latest Feeds":"Last Runable Feeds";
    $response =  array('feedsMode' => $mode );
    header('Content-Type: application/json');
    echo json_encode($response);
    wp_die();
  }
  public function startFeedsTestMode(){
    update_option("misysnews_feeds_run_test",1);
    wp_die();
  }

  public function load_custom_wp_admin_style(){
      // wp_enqueue_script( 'misysnews_admin_css', plugins_url('misysnews/app/styles/admin.css' ), null, null, true );
      wp_register_style( 'misysnews_bootstrap_css', plugins_url( 'misysnews/app/styles/bootstrap.min.css' ));
      wp_register_style( 'misysnews_admin_css', plugins_url( 'misysnews/app/styles/admin.css' ));
      wp_register_style( 'misysnews_modal_css', plugins_url( 'misysnews/app/styles/modal.css' ));
      wp_register_style( 'misysnews_search_css', plugins_url( 'misysnews/app/styles/semantic.min.css' ));
      wp_register_style( 'misysnews_ngselect_css', plugins_url( 'misysnews/app/styles/ng-select.css' ));
      wp_register_style( 'misysnews_angularmodule_css', plugins_url( 'misysnews/app/styles/angularmodule.css' ));
      wp_enqueue_style("misysnews_bootstrap_css");
      wp_enqueue_style("misysnews_admin_css");
      wp_enqueue_style("misysnews_modal_css");
      wp_enqueue_style("misysnews_search_css");
      wp_enqueue_style("misysnews_ngselect_css");
      wp_enqueue_style("misysnews_angularmodule_css");

  }
}


if( is_admin() )
    $misysnews_settings_page = new MisysNewsSettingsPage();
