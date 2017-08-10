import { Directive, ElementRef , Input } from '@angular/core';

@Directive(
  {
      selector:'[activeStatus]'
  }
)
// the directive for the bars
export class ActiveStatusDirective{
  @Input('activeStatus') activeStatus :boolean ;

  constructor(private el : ElementRef){
  }
  ngOnInit(){
    this.controlBarActiveStatus();
  }
  ngOnChanges(){
    this.controlBarActiveStatus();
  }
  private controlBarActiveStatus(){
    if(this.activeStatus){
      //if active then more black

        this.el.nativeElement.style.color = "#0085ba";
      }else{
        //if not active then more opacity
        this.el.nativeElement.style.opacity = "0.75";
        this.el.nativeElement.style.color = "black";
        // this.el.nativeElement.style.opacity = "0.3";
      }

  }

}
