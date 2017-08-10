import { Component , OnInit ,HostListener } from '@angular/core';
import { Http} from "@angular/http";
import { SelectModule } from 'ng2-select';
import { Observable } from 'rxjs/Observable';


import { FeedOriginal ,RequestType } from '../entity/original.entity';
import { Bar,FeedValue,FeedOutput,RootObject } from '../entity/output.entity';

import { FeedsHttpService } from '../feeds.service'

import { CategoryComponent } from './category.component'
import { BarComponent } from './misysnews-bar.component'
import { LoadingProgressDirective } from "../directives/loadingbar.directive"

@Component({
    selector: 'my-app',
    templateUrl: "./app.component.html"
})
export class AppComponent implements OnInit
{
  // notice message when user close this page
    @HostListener('window:beforeunload', ['$event'])
    handleBeforeUnload(event:any) {
      // this.loadTopics().
      if(this.isTopicsChanged()){
        event.returnValue = "you have unsaved data changes.";
      }

    }
    title = 'Topics';
    newCategory = "";
    //topics object the central data during configure the topics
    rootObject : RootObject = new RootObject();
    rootObjectStr: string ="";
    //the string array of the bar name for the sub component
    barArray: String [] = [];
    validFeedsNumber:number  = 0 ;
    operatedFeeds: number = 0;
    newSourceValid : boolean = false;
    checkFinished : boolean = false;
    checkValid : boolean = false;
    saveFinished : boolean = false;
    saveSuccessed : boolean = false;
    warningMessage:boolean =false;
    warningNoFeedsMessage:boolean = false;
    categoryNameTobeDeleted : string = "";
    newCategoryValidStatus :boolean = false;

    //constructor
    //1.load the previous topics object
    //2.init the service object
    constructor(
      private http :Http,
      private feedsHttpService : FeedsHttpService){
      this.loadTopics()
        .subscribe(
          wholeTopics =>this.bindingOldTopics(wholeTopics),
          error => this.initTopicsObj()
        );

    }
    ngOnInit(): void {
    }
    private loadTopics() {
        return this.loadPreviousTopics()
    }
    //get the previous topics
    private loadPreviousTopics() {
       return this.feedsHttpService.getDataByRequest(RequestType.whole)
              .map(res => res.json());
              // .catch(this.feedsHttpService.handleError)

    }
    private bindingOldTopics(prevousWholeTopics : RootObject):void{

      if(prevousWholeTopics.feeds.length>0&&prevousWholeTopics.bar){
        for(let i = 0 ; i< prevousWholeTopics.feeds.length;i++){
          if(!prevousWholeTopics.feeds[i].displayFrontend)
            prevousWholeTopics.feeds[i].displayFrontend = true;
        }
        this.rootObject = prevousWholeTopics;
        this.rootObjectStr = JSON.stringify(this.rootObject);
        this.barArray = Object.keys(this.rootObject.bar);
      }else{
        this.initTopicsObj();
      }

    }
    //when the error happens or the feed is not well formed then create one empty object
    private initTopicsObj():void{
      this.rootObject = new RootObject();
      this.rootObject.bar={'meteo':true,"horoscope":true,
                          "menu":true,"anniversaries":false};
      this.rootObject.feeds=[];
      this.barArray = Object.keys(this.rootObject.bar);
    }

    addNewTopic():void {
      this.newCategoryValidStatus=false;
      if(this.checkNewTopics()){
        this.createTopics();
      }
      this.newCategory = "";
    }
    //check new category is valid or not
    checkNewTopics(): boolean {
      this.newCategoryValidStatus=false;
      if(this.newCategory=="" ||this.newCategory.length==0){
        this.newCategory="";
        this.newCategoryValidStatus=false;
        return false;
      }else{
        for( let i = 0;i<this.rootObject.feeds.length;i++){
            if(this.rootObject.feeds[i].name == this.newCategory){
              this.newCategory="";
              this.newCategoryValidStatus=false;
              return false;
            }
        }
        this.newCategoryValidStatus = true;
        return true;
      }

    }
    // do the action :create a new topics
    createTopics():void{
      let category = new FeedOutput();
      category.displayBackend=true;
      category.name = this.newCategory;
      category.values = [];
      this.rootObject.feeds.push(category);
    }
    // save the topics obj into database and publish these topics
    updateTopicsInBackend(): void {
      //save data into database
      this.clearEmptyProperty();
      if(this.newSourceValid&&this.checkFinished&&this.checkValid){
        let strfeed = this.TopicsToString();
        this.feedsHttpService.saveFeeds(strfeed)
                    .subscribe(
                        data => {
                          this.saveFinished=true;
                          this.saveSuccessed = true;
                          this.rootObjectStr= JSON.stringify(this.rootObject)
                        },
                        error => {this.saveFinished=true; this.saveSuccessed = false;}
                      );
      }else{
        this.warningMessage=true;
      }
    }
    //refresh the original topics function (callback)
    //when the sub component change object value
    updateTopics(data:FeedOutput):void{
      for( let i = 0;i<this.rootObject.feeds.length;i++){
        if(this.rootObject.feeds[i].name == data.name){
          this.rootObject.feeds[i].values = data.values;
        }
      }
    }
    // the callback function for the bar component to update the bars.
    updateBar(bar:Bar):void{
      this.rootObject.bar[bar.name] = bar.visibility;
    }

    private TopicsToString():string{
      return JSON.stringify(this.rootObject);
    }
    //the order of content display. down the topics
    moveDown(name:string):void{
      for(let i = 0;i<this.rootObject.feeds.length-1;i++){
        if(this.rootObject.feeds[i].name == name){
          this.FeedArraySwap(i,i+1);
          break;
        }
      }
    }
    //the order of content display. up the topics
    moveUp(name: string):void{
      for(let i = 0;i<this.rootObject.feeds.length;i++){
        if(this.rootObject.feeds[i].name == name && i > 0){
          this.FeedArraySwap(i,i-1);
          break;
        }
      }
    }
    //tools function for moveup() and moveDown()
    private FeedArraySwap(index1:number,index2:number):void{
      let tmp = this.rootObject.feeds[index1];
      this.rootObject.feeds[index1]=this.rootObject.feeds[index2];
      this.rootObject.feeds[index2]=tmp;
    }

    // function to check wethter the source in the topics object is valid or not
    checkNewSources():void{
      // try to access all the result in the object if we can get all the number of the result then this means these sources is valid.
      this.validFeedsNumber = 0;
      this.operatedFeeds = 0;
      if(this.getNumberOfAllFeeds()!=0){
        //check each source
        for(let i = 0 ; i< this.rootObject.feeds.length ; i++){
          for(let j = 0 ; j < this.rootObject.feeds[i].values.length;j++){
                  this.feedsHttpService.getDataFromFeedsUrl(this.rootObject.feeds[i].values[j].feedURL)
                    .subscribe(
                      data => this.sourcesLoadSuccess(data.text(),this.rootObject.feeds[i].values[j]),
                      error => {this.checkFinished = true;}
                    );
          }
        }
      }else{
        //no source in this topics object
        this.warningNoFeedsMessage=true;
        this.checkFinished=true;
      }
    }
    // should check whether the source is valid
    private sourcesLoadSuccess(rssContent:string,correspondFeeds : FeedValue):void{
      // check the structure of this content is rss structure or not

      this.operatedFeeds++;
      // typical rss content contains the rss and channel tag
      if(rssContent.indexOf("rss") > 0 && rssContent.indexOf("channel") > 0){
        this.validFeedsNumber++;
        correspondFeeds.validStatus = true;
        // check if all source have been checked
        if(this.validFeedsNumber == this.getNumberOfAllFeeds()){
          this.newSourceValid = true;
          this.checkFinished = true;
          this.checkValid = true;
          //check success update in the backend
          this.updateTopicsInBackend();
          return ; //finished
        }
      }else{
        //structure is not rss
        correspondFeeds.validStatus = false;
      }
      //if there are some not valid source, set the parameter for the directives
      if(this.operatedFeeds == this.getNumberOfAllFeeds() && this.operatedFeeds !=this.validFeedsNumber){
        for(let i = 0 ; i< this.rootObject.feeds.length ; i++){
          if(this.containsErrorItem(this.rootObject.feeds[i])){
            this.rootObject.feeds[i].displayBackend = true;
          }
        }
        this.checkFinished=true;
        this.checkValid=false;
      }
    }
    //tools function
    public getNumberOfAllFeeds():number{
      let numberOfFeeds = 0;
      for(let i = 0 ; i< this.rootObject.feeds.length ; i++){
        for(let j = 0 ; j < this.rootObject.feeds[i].values.length;j++){
          numberOfFeeds++;
        }
      }
      return numberOfFeeds;
    }
    //tools function to check wether exist not valid sources
    private containsErrorItem(catageryObj: FeedOutput):boolean{
      for(let i = 0 ; i < catageryObj.values.length ; i++){
        if(catageryObj.values[i].validStatus == false){
          return true;
        }
      }
      return false;
    }
    // the ratio of the source checking
    getFeedsLoadedStatus():number {
      return this.operatedFeeds/this.getNumberOfAllFeeds();
    }

    initPublishModal():void {
      this.checkFinished=false;
      this.checkValid = false;
      this.saveFinished=false;
      this.saveSuccessed =false;
      this.warningNoFeedsMessage = false;
      this.warningMessage =false;
      this.checkNewSources();
    }

    initDeleteModal(categoryName: string) :void {
      this.categoryNameTobeDeleted = categoryName;
    }
    //function called on delete modal
    deleteTopicsWithChecking():void{
      this.removeTopic(this.categoryNameTobeDeleted);
      this.categoryNameTobeDeleted = "";
    }
    // delete the topic  from the object
    removeTopic(categoryName:string):void{
      for( let i = 0;i<this.rootObject.feeds.length;i++){
        if(this.rootObject.feeds[i].name == categoryName){
          this.rootObject.feeds.splice(i,1);
        }
      }
    }
    // clear all empty source in the topics object
    // called during the user cancel or delete some source from anyone topic
    private clearEmptyProperty():void{
      for(let i = 0 ; i< this.rootObject.feeds.length ; i++){
        for(let j = 0 ; j < this.rootObject.feeds[i].values.length;j++){
          // numberOfFeeds++;
          var objectKeys = Object.keys(this.rootObject.feeds[i].values[j]);
          for(var n = 0; n<objectKeys.length;n++){
            if(this.rootObject.feeds[i].values[j][objectKeys[n]]==""||this.rootObject.feeds[i].values[j][objectKeys[n]]==null){
              delete this.rootObject.feeds[i].values[j][objectKeys[n]];
            }
          }
        }
      }
    }
    //create a new window to create sources
    public createNewWindowForSource():void {
      var newWindow = window.open(this.feedsHttpService.getSourcePath());
    }
    //function check the rootObject is changed or not
    private isTopicsChanged():boolean{
      if(JSON.stringify(this.rootObject) == this.rootObjectStr)
        return false;
      else
        return true;
    }

}
