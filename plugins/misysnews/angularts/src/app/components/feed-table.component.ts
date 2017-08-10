import { Component , OnInit, Input,Output ,EventEmitter} from '@angular/core';
import { Http } from "@angular/http";
import { FeedOriginal ,RequestType } from '../entity/original.entity';
import { Bar,FeedValue,FeedOutput,RootObject } from '../entity/output.entity';
import { SelectModule } from 'ng2-select';

@Component({
    selector: 'misysnews-feeds-table',
    templateUrl: "./feed-table.component.html"
})
export class FeedsTableComponent
{
    // mediam class between the category component and the feed-row component
    @Input()
    feeds : FeedValue[] ;
    @Output('updateFeeds')
    updatedFeeds : EventEmitter<FeedValue[]> = new EventEmitter<FeedValue[]>();
    isFeedEditable:boolean=false;
    @Input()
    sourceOptions : Array<string>;
    constructor(){
    }

    private onChange(){
      this.updatedFeeds.emit(this.feeds);
    }

    clearEmptyFeed():void{
      for(var i = 0; i < this.feeds.length;i++){
        if(this.feeds[i].id){

        }else{
          this.feeds.splice(i,1);
          i--;
        }
      }
    }
    removeFeed(feed:FeedValue):void{
      for(var i = 0; i < this.feeds.length;i++){
        // if(this.feeds[i].id==feed.id&&this.feeds[i].category == feed.category &&this.feeds[i].feedName == feed.feedName){
        if(this.feeds[i].postID == feed.postID &&this.feeds[i].title == feed.title){
          this.feeds.splice(i,1);
          i--;
        }
      }
      this.clearEmptyFeed();
    }

}
