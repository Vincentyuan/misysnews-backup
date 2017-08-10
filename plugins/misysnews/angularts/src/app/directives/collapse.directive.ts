import { Directive, ElementRef , Input } from '@angular/core';

@Directive(
  {
      selector:'[displayBackend]'
  }
)
//directive to implement the topics collapse
export class CollapseDirective{
  @Input('displayBackend') displayBackend :boolean ;

  constructor(private el : ElementRef){
  }
  ngOnInit(){
    this.controlCollapse();
  }
  ngOnChanges(){
    this.controlCollapse();
  }
  private controlCollapse(){
    if(this.displayBackend){
        this.el.nativeElement.parentElement.classList.remove("closed");

      }else{
        this.el.nativeElement.parentElement.classList.add("closed");
      }

  }

}
