import { Component , OnInit, Input,Output ,EventEmitter } from '@angular/core';
import { Injectable }    from '@angular/core';
import { Http } from "@angular/http";

import { FeedOriginal ,RequestType } from '../entity/original.entity';
import { Bar,FeedValue,FeedOutput,RootObject } from '../entity/output.entity';

import { FeedsHttpService } from '../feeds.service'


@Component({
    selector: '[feed]',
    templateUrl: "./feed-row.component.html"
})
export class FeedsRowComponent implements OnInit
{
  @Input() // the main feed object for this component.
  feed : FeedValue;

  @Input()//the custoimzed post data
  feedOriginal : FeedOriginal[] =[];

  @Output('clearEmptyFeed')
  clearEmptyFeed : EventEmitter<null> = new EventEmitter<null>();
  @Output('deleteFeed')
  deleteFeed :EventEmitter <FeedValue> = new EventEmitter< FeedValue>();

  feedexpire : boolean = false;

  visibleAnimate: boolean = false;

  @Input() // this is used to initial the component , because the
  sourceOptions:Array<string>;

  //the content array for the source options
  latestSourceOptions:Array<string> = [];
  // predefine layout arrays
  layoutList :Array<any> = [
    {id:1,layout:"others",name:"1xL 3xS Image",text:name},
    {id:2,layout:"no-hero",name:"4xS Image",text:name},
    {id:3,layout:"no-hero-no-desc-3",name:"3xL Image",text:name},
    {id:4,layout:"grid-with-no-images",name:"4xS no Image",text:name}
  ];
  //the containt array for the layout options
  newLayoutItems :Array<string>=[this.layoutList[0].name,
          this.layoutList[1].name,
          this.layoutList[2].name,
          this.layoutList[3].name];
  // the object for select layout. {id:"", text:""}
  selectedLayout:any = {};
  //the object to store the select source.
  selectedPost : FeedOriginal;
  //the page number for this feed.
  newPage:number = 1;
  //control for the trash modal
  showTrashSourceMessage : boolean = false;
  constructor(
    private feedsHttpService : FeedsHttpService ){
  }
  ngOnInit(){
    //by default the valid status is true, it will be checked when publish the topics
    this.feed.validStatus = true;
    if(this.feed.id==""||this.feed.id==undefined){
      //this is a new feed that just created. set the default layout
      this.selectedLayout = this.layoutList[0];
    }else{
      //this is the feed that exist
      this.checkValidTime();
    }

    // need to check
    this.initSourceLayout();
    this.latestSourceOptions = this.sourceOptions;

    if(this.feedOriginal.length==0){
      this.loadAllCustomizedFeed()
          .subscribe(
            customizedFeeds =>{this.feedOriginal = customizedFeeds;},
            error=>""
          );
    }
    if(this.latestSourceOptions.length==0){
      this.loadAllCustomizedFeed()
          .subscribe(
            customizedFeeds => this.bindingCustomizedFeeds(customizedFeeds),
            error =>""
          );
    }


  }
  // init the layout options
  private initSourceLayout():void {
    for(var i = 0 ; i<this.feedOriginal.length; i++){
      if(this.feed.postID == this.feedOriginal[i].postID){
        this.selectedPost = this.feedOriginal[i];
        break;
      }
    }
    this.setLayout();
  }
  // set the default layout or set the layout by the parameter
  private setLayout():void{
    if(this.feed.layout=="" || this.feed.layout == undefined){
      this.selectedLayout = this.layoutList[0];
    }else{
      for(var i = 0 ; i < this.layoutList.length;i++){
        if(this.layoutList[i].layout == this.feed.layout){
          this.selectedLayout = this.layoutList[i];
          break;
        }
      }
    }
  }

  //load the customized source
  private loadAllCustomizedFeed() {
     return this.feedsHttpService.getDataByRequest(RequestType.customized)
            .map(res=>res.json());
            // .catch(this.feedsHttpService.handleError)

  }
  // reset the FeedOriginal object and reset the option array for the sourceOption
  private bindingCustomizedFeeds(customizedFeeds : FeedOriginal []){
    // this.feedOriginal = customizedFeeds;

    //reset the latestSourceOptions
    this.latestSourceOptions = [];
    for(var i = 0 ; i<customizedFeeds.length ; i++){
      if(this.latestSourceOptions.indexOf(customizedFeeds[i].title)<0)
        this.latestSourceOptions.push(customizedFeeds[i].title);
    }
  }
  //edit
  startEdit():void{
    if(this.latestSourceOptions.length == 0){
      //if the source options is not loaded
      this.loadAllCustomizedFeed()
          .subscribe(
          customizedFeeds => {
            this.bindingCustomizedFeeds(customizedFeeds);
            this.setLayout();
            this.feed.editable=!this.feed.editable;
          },
            error =>""
          );
    }else{
      this.feed.editable=!this.feed.editable;
    }
  }
  //cancel
  endEdit(selectedPost:FeedOriginal):void{
    this.feed.editable=!this.feed.editable;
    this.triggerClearEmptyFeeds();
  }
  //save
  updateCurrentFeeds(newPage:string, newLayout:string ):void{
    //set default source
    if(this.selectedPost == undefined){
      if(this.feed.feedURL == undefined){
        //create new
        this.selectedPost = this.feedOriginal[0];
        this.excuteUpdateAction(newPage);
      }else{
        // update with out choose
        this.feed.id = this.feed.title;
        this.feed.page = newPage;
        this.feed.layout = this.selectedLayout.layout;
        this.feed.editable=false;
        // this.checkValidTime();
      }
      // this.excuteUpdateAction(newPage);
    }else{
      this.excuteUpdateAction(newPage);
      // alert("Please select one source!");
    }
  }

  excuteUpdateAction(newPage:string):void {
    if(this.feed.title){
      if(this.newPage<=0||this.newPage >5){
        alert("The number of page should be greater than 0 and less than 5!");
      }else{
        this.feed.id = this.feed.title;
        this.feed.postID = this.selectedPost.postID;
        this.feed.feedName = this.selectedPost.title;
        this.feed.feedURL = this.selectedPost.url;
        this.feed.background = this.selectedPost.background_image;
        this.feed.page = newPage;
        this.feed.layout = this.selectedLayout.layout;
        this.feed.valid_time_from = this.selectedPost.valid_time_from;

        if(this.selectedPost.valid_time_to!=""){
          this.feed.valid_time_to = this.selectedPost.valid_time_to;
        }else{
          this.feed.valid_time_to = "";
        }
        this.feed.editable=false;
        this.checkValidTime();
      }
    }else{
      alert("please input the title for this source");
    }
  }

  //function to delete current source
  removeFeedFromCategory():void{
    this.deleteFeed.emit(this.feed);
  }
  //clear all empty feed from the root object
  triggerClearEmptyFeeds():void{
    this.clearEmptyFeed.emit();
  }
  //action function when user select one option
  public selectSource(value: any ):void {
    //the value object structure { id :"", text :""}
    for(var i = 0 ; i < this.feedOriginal.length ; i++){
      if(value.id == (this.feedOriginal[i].title)){
        this.selectedPost = this.feedOriginal[i];
      }
    }
  }
  //user remove the select source
  public onRemoveSelectSource():void{
    this.selectedPost = null;
  }
  // action function when user select layout,
  // public onSelectLayout():void {
  //   for(var i = 0 ; i < this.layoutList.length ; i++){
  //     if( this.layoutList[i].id == this.selectedLayout.id){
  //       //transform the select option into another object
  //       this.selectLayout = this.layoutList[i].layout;
  //     }
  //   }
  // }

  // action function when user select layout,
  public selectLayout(value:any):void{
    for(var i = 0 ; i < this.layoutList.length ; i++){
      if(value.text == (this.layoutList[i].name)){
        //transform the select option into another object
        this.selectedLayout = this.layoutList[i];
      }
    }
  }
  public onRemoveSelectLayout():void{
    this.selectedLayout = null;
  }

  // popup the notice message when use want to delete the current source
  public onRemoveSourceFromTopic():void {
     this.showTrashSourceMessage = true;
    setTimeout(() =>this.visibleAnimate = true, 100);
    // this.showCreateNewTopicMessage=true;
  }
  public closeTrashSourceModal():void {
    this.visibleAnimate = false;
    setTimeout(() => this.showTrashSourceMessage = false, 300);
  }
  //click other place
  public onContainerClicked(event: MouseEvent): void {
    if ((<HTMLElement>event.target).classList.contains('customizeModal')) {
      this.closeTrashSourceModal();
    }
  }

  // check the valid time
  private checkValidTime():void{
    var validTime:any = this.feed.valid_time_to?this.feed.valid_time_to:"";
    if(validTime==""||(validTime!="")&&(validTime -this.getDateYYYYMMDD()) >= 0){
      this.feedexpire = false;
    }else{
      this.feedexpire = true;
    }
  }
  //tools function to get the valid date type
  private getDateYYYYMMDD():any{
    var obj = new Date();
    var mm = obj.getMonth() + 1; // getMonth() is zero-based
      var dd = obj.getDate();

      return [obj.getFullYear(),
              (mm>9 ? '' : '0') + mm,
              (dd>9 ? '' : '0') + dd
            ].join('');
  }
}
