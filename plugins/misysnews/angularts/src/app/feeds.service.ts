import { Injectable } from '@angular/core';
import { Http , URLSearchParams} from '@angular/http';
import { Observable } from 'rxjs/Observable';
// import 'rxjs/add/operator/toPromise';
// import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';

@Injectable()
export class FeedsHttpService{
  private relativeRequestHandleUrl = "/misysnews/feeds_requests_handle.php";
  private relativeGetUrl = "/misysnews/getURL.php";
  private headers:any;
  private fullRequestHandleUrl : string = "";
  private pluginRootPath :string = "";

  private pathAdminSource = "edit.php?post_type=misysnews_feeds";
  constructor(
    private http : Http
  ){
    // set the http header and url;
    this.headers = new Headers({'Content-Type': 'application/json'});
    this.headers.append('Access-Control-Allow-Headers', 'Content-Type');
    this.headers.append('Access-Control-Allow-Methods', 'GET');
    this.headers.append('Access-Control-Allow-Origin', '*');
    this.getPluginRootURL();
  }
  // get the data for the whole object or the customize
  getDataByRequest(type:string):Observable<any>{
    let params: URLSearchParams = new URLSearchParams();
    params.set("requesttype", type);
    return this.http.get(this.fullRequestHandleUrl,{search:params});
  }
  // get the plugin url (not the same with the current url )
  getPluginRootURL(){
    //function call like jquery
    this.pluginRootPath = document.querySelector("#wpPluginPath").textContent;
    this.fullRequestHandleUrl = this.pluginRootPath+this.relativeRequestHandleUrl;
  }
  //load the rss file to check wether it is a valid source
  getDataFromFeedsUrl(url:string) :Observable<any>{
    let params : URLSearchParams = new URLSearchParams();
    params.set("url",url);
    return this.http.get(this.pluginRootPath+this.relativeGetUrl,{search: params});
  }
  //update topics into database
  saveFeeds(data:string) :Observable<any> {
    let jsonData: URLSearchParams = new URLSearchParams();
    jsonData.set("json_Data", data);
    return this.http.post(this.fullRequestHandleUrl,data);
  }
  handleError (error: Response | any) {

  }
  getSourcePath():string{
    return this.pathAdminSource;
  }
}
