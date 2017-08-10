import { NgModule , CUSTOM_ELEMENTS_SCHEMA }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule }   from '@angular/forms'; // <-- NgModel lives here
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { SelectModule , SelectComponent} from "ng2-select";
import { HttpModule ,JsonpModule} from '@angular/http';


import { AppComponent }  from './components/app.component';
import { CategoryComponent } from './components/category.component';
import { FeedsTableComponent } from './components/feed-table.component';
import { FeedsRowComponent } from './components/feed-row.component';
// import { AddFeedsComponent } from './components/add-feeds.component';
import { BarComponent } from './components/misysnews-bar.component';
import { ModalComponent } from './components/misysnews-modal.component';



import { LineThroughDirective } from './directives/line-through.directive';
import { LoadingProgressDirective } from "./directives/loadingbar.directive";
import { FeedValidStatus } from './directives/feed-valid.directive';
import { CollapseDirective} from "./directives/collapse.directive";
import { ActiveStatusDirective } from "./directives/active-status.directive";
import { FeedExpireDirective } from "./directives/feed-expire.directive"

import { FeedsHttpService } from './feeds.service';


@NgModule({
  imports: [
    BrowserModule,
    FormsModule, // <-- import the FormsModule before binding with [(ngModel)]
    HttpModule,
    JsonpModule,
    SelectModule,
    NgbModule.forRoot()
  ],
  declarations: [
    AppComponent,
    CategoryComponent,
    FeedsTableComponent,
    FeedsRowComponent,
    // AddFeedsComponent,
    BarComponent,
    LineThroughDirective,
    FeedValidStatus,
    ModalComponent,
    LoadingProgressDirective,
    CollapseDirective,
    ActiveStatusDirective,
    FeedExpireDirective
  ],
  bootstrap: [ AppComponent ],
  providers :[
    FeedsHttpService,

  ],
  schemas : [CUSTOM_ELEMENTS_SCHEMA] //for the ng-select component
})
export class AppModule { }
