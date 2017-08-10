import { Directive, ElementRef, Input } from '@angular/core';

@Directive(
  {
      selector:'[loadingProgress]'
  }
)
//directive used when we check the status of the source
export class LoadingProgressDirective{
  @Input('loadingProgress') loadingProgress :number ;

  constructor(private el : ElementRef){
  }
  ngOnInit(){
    this.controlLoadingBar();
  }
  ngOnChanges(){
    this.controlLoadingBar();
  }
  private controlLoadingBar(){
    this.el.nativeElement.style.width = this.loadingProgress *100 +"%";
  }
}
