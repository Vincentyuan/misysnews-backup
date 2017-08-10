import { Directive, ElementRef, Input } from '@angular/core';

@Directive(
  {
      selector:'[displayTitleNormal]'
  }
)
// directive for when the topic is not displayed in the frontend
export class LineThroughDirective{
  @Input('displayTitleNormal') displayTitleNormal :boolean ;

  constructor(private el : ElementRef){
  }
  ngOnInit(){
    this.controlLineThrough();
  }
  ngOnChanges(){
    this.controlLineThrough();
  }
  private controlLineThrough(){
    if(!this.displayTitleNormal){
        this.el.nativeElement.style.textDecoration = 'line-through';
        this.el.nativeElement.style.color = "hsla(204,5%,35%,0.35)";
      }else{
        this.el.nativeElement.style.textDecoration = '';
        this.el.nativeElement.style.color = "#23282d";
      }

  }

}
