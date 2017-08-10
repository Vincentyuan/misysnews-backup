import { Component , OnInit, Input,Output ,EventEmitter} from '@angular/core';
// import { Injectable }    from '@angular/core';
import { Http } from "@angular/http";

import { FeedOriginal ,RequestType } from '../entity/original.entity';
import { Bar,FeedValue,FeedOutput,RootObject } from '../entity/output.entity';

import { Observable } from 'rxjs/Observable';


import { FeedsHttpService } from '../feeds.service'

import { SelectModule } from 'ng2-select';
@Component({
    selector: 'categories',
    templateUrl: "./category.component.html"
})
export class CategoryComponent// implements OnInit
{
  //contains some field to implement two way binding
  @Input()
  category:FeedOutput ;
  @Output('updateCategory')
  updatedCategory : EventEmitter<FeedOutput> = new EventEmitter<FeedOutput>();
  @Output('moveDown')
  moveDown :EventEmitter<string> = new EventEmitter<string>();
  @Output('moveUp')
  moveUp : EventEmitter<string> = new EventEmitter<string>();
  @Output('removeCategory')
  removeCategory : EventEmitter<string> = new EventEmitter<string>();

  sourceOptions:Array<string> = []; // the string array for the source options

  constructor(
    private http :Http,
    private feedsHttpService : FeedsHttpService){
      // this.loadAllCustomizedFeed();
  }
  //load all customized source
  private loadAllCustomizedFeed():void {
     this.feedsHttpService.getDataByRequest(RequestType.customized)
            .map(res=>res.json())
            // .catch(this.feedsHttpService.handleError)
            .subscribe(
              customizedFeeds => this.getSourceOptions(customizedFeeds),
              error =>""
            );
  }
  private getSourceOptions(customizedFeeds:FeedOriginal[]):void {
    for(var i = 0; i < customizedFeeds.length ; i++){
      this.sourceOptions.push(customizedFeeds[i].title);
    }
    this.excuteAddingSourceToCategory();
  }
  // get the latest source option everytime;
  addSourceToCategory():void{
    this.sourceOptions=[];
    this.loadAllCustomizedFeed();
    // if(this.sourceOptions==undefined||this.sourceOptions.length == 0){
      // this.excuteAddingSourceToCategory();
    // }else{
      // this.excuteAddingSourceToCategory();
    // }
  }
  excuteAddingSourceToCategory():void{
    let newFeed = new FeedValue();
    this.category.values.push(newFeed);
    this.category.displayBackend=true;
  }

  // updateFeeds(feedsValue: FeedValue[]):void{
  //   this.category.values= feedsValue;
  //   this.onChange();
  // }
  //two way binding with app.component
  private onChange(){
    this.updatedCategory.emit(this.category);
  }
  triggerMoveDown():void{
    this.moveDown.emit(this.category.name);
  }
  triggerMoveUp():void{
    this.moveUp.emit(this.category.name);
  }
  triggerRemoveCategory():void{
    this.removeCategory.emit(this.category.name);
  }
  changeVisibleFrontend():void{
    this.category.displayFrontend = !this.category.displayFrontend;
    this.onChange();
  }
  changeVisibleBackend():void {
    this.category.displayBackend=!this.category.displayBackend;
  }


}
