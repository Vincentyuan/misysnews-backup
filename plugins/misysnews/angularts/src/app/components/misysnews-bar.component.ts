import { Component , OnInit, Input,Output ,EventEmitter} from '@angular/core';

import { Http } from "@angular/http";

import { FeedOriginal ,RequestType } from '../entity/original.entity';
import { Bar,FeedValue,FeedOutput,RootObject } from '../entity/output.entity';


@Component({
    selector: 'misysnews-bar',
    templateUrl: "./misysnews-bar.component.html"
})
export class BarComponent implements OnInit
{
  @Input()
  barKey:string;
  @Input()
  bars: Object;
  @Output('updateBar')
  updateBar : EventEmitter<Bar> = new EventEmitter<Bar>();

  currentBar : Bar ;
  barConfigObj : any = null;
  barMappingArray:Array<any> =[
    {
      barNameOriginal:"meteo",
      barClass:"fa-cloud",
      barNameEn:"Weather"
    },
    {
      barNameOriginal:"horoscope",
      barClass:"fa-calendar",
      barNameEn:"Horoscope"
    },
    {
      barNameOriginal:"menu",
      barClass:"fa-cutlery",
      barNameEn:"Menu"
    },{
      barNameOriginal:"anniversaries",
      barClass:"fa-birthday-cake",
      barNameEn:"Birthday"
    }

  ];
  constructor(){
  }

  ngOnInit(){
    this.currentBar = new Bar();
    this.currentBar.name = this.barKey;
    this.currentBar.visibility = this.bars[this.barKey];

    for(var i  = 0 ; i < this.barMappingArray.length ;i++){
      if(this.barKey == this.barMappingArray[i].barNameOriginal){
        this.barConfigObj = this.barMappingArray[i];
      }
    }
  }

  triggerBarStatusChange():void{
    // let bar = new Bar();
    // bar.name = this.barKey;
    // bar.visibility = !this.bars[this.barKey]
    this.currentBar.visibility = !this.currentBar.visibility;
    this.updateBar.emit(this.currentBar);
  }

}
