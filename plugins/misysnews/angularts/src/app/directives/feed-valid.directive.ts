import { Directive, ElementRef, Input } from '@angular/core';

@Directive(
  {
      selector:'[FeedValidStatus]'
  }
)
// directive for check the status of feed , wether it is valid or not
export class FeedValidStatus{
  @Input('FeedValidStatus') validStatus :boolean ;

  constructor(private el : ElementRef){
  }
  ngOnInit(){
    this.controlValidStatus();
  }
  ngOnChanges(){
    this.controlValidStatus();
  }
  private controlValidStatus(){
    if(this.validStatus ){
        this.el.nativeElement.style.color = 'black';
      }else{
        this.el.nativeElement.style.color = 'red';
      }

  }

}
