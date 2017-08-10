import { Directive, ElementRef, Input } from '@angular/core';

@Directive(
  {
      selector:'[feedExpire]'
  }
)
// directive for the expired feed
export class FeedExpireDirective{
  @Input('feedExpire') feedExpire :boolean ;

  constructor(private el : ElementRef){
  }
  ngOnInit(){
    this.controlFeedExpire();
  }
  ngOnChanges(){
    this.controlFeedExpire();
  }
  private controlFeedExpire(){
    if(!this.feedExpire){
      //normal
      this.el.nativeElement.style.color = "black";
    }else{
      // if expired
      this.el.nativeElement.style.color = "orange";
    }

  }

}
